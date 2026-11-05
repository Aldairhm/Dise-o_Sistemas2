<?php

namespace App\Services;

use App\Models\{ComisionVendedor, NotificationDelivery, NotificationEvent, SoporteTicket, User};
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function backupFailed(): void
    {
        $this->publish('backup-failed:'.now()->format('Y-m-d-H'), 'backup_fallido', 'administracion', 'critica',
            'Fallo confirmado de respaldo', 'No se pudo completar un respaldo. Revisa los registros autorizados del servidor.',
            'backup', null, $this->roles('super_admin'));
    }

    public function publish(string $key, string $type, string $category, string $priority, string $title, string $message, string $resource, ?int $id, iterable $recipients, array $context = []): void
    {
        DB::transaction(function () use ($key, $type, $category, $priority, $title, $message, $resource, $id, $recipients, $context) {
            $event = NotificationEvent::firstOrCreate(['event_key' => $key], [
                'type' => $type, 'category' => $category, 'priority' => $priority,
                'title' => $title, 'message' => $message, 'resource_type' => $resource,
                'resource_id' => $id, 'context' => $context, 'created_at' => now(),
            ]);
            foreach (collect($recipients)->filter()->unique() as $userId) {
                NotificationDelivery::firstOrCreate(['event_id' => $event->id, 'user_id' => $userId]);
            }
        });
    }

    public function roles(string $role): array
    {
        return User::where('rol', $role)->where('estado', 1)->pluck('id')->all();
    }

    public function notifyPayableCommissions(?int $bonusId = null): void
    {
        $admins = $this->roles('admin');
        if (!$admins) {
            return;
        }

        $pending = ComisionVendedor::pendientes()->where('monto', '>', 0)->whereNull('aviso_liquidacion_at');
        if ($bonusId !== null) {
            $pending->whereKey($bonusId)->whereNull('id_salida');
        }
        $sellers = (clone $pending)->select('id_vendedor')->distinct()->lazyById(100, 'id_vendedor');
        foreach ($sellers as $seller) {
            DB::transaction(function () use ($seller, $pending, $admins) {
                $sellerId = $seller->id_vendedor;
                $commissions = (clone $pending)->deVendedor($sellerId)->with('salida')->orderBy('id')->lockForUpdate()->get();
                ComisionVendedor::cargarVentas($commissions);
                $payable = $commissions->reject(fn ($commission) => $commission->es_liquidacion_bloqueada);
                if ($payable->isEmpty()) {
                    return;
                }

                $ids = $payable->modelKeys();
                $key = 'liquidacion:'.$sellerId.':'.hash('sha256', implode(',', $ids));
                $bonuses = $payable->whereNull('id_salida')->count();
                $commissionCount = $payable->count() - $bonuses;
                $name = User::find($sellerId)?->nombre_real ?? "Vendedor #{$sellerId}";
                $amount = '$'.number_format((float) $payable->sum('monto'), 2);
                if ($bonuses === 1 && $commissionCount === 0) {
                    $bonus = $payable->first();
                    $title = 'Bono pendiente de pago';
                    $message = "{$name} tiene un bono de {$amount} pendiente de pago.";
                    if ($bonus->concepto) {
                        $message .= " Concepto: {$bonus->concepto}.";
                    }
                } else {
                    $title = 'Pagos pendientes de liquidación';
                    $message = "{$name} tiene {$commissionCount} comisión(es) y {$bonuses} bono(s) que ya se pueden liquidar. Subtotal de estos registros: {$amount}.";
                }
                $this->publish($key, 'liquidacion_pendiente', 'comisiones', 'importante', $title, $message,
                    'liquidacion', $sellerId, $admins);
                ComisionVendedor::whereKey($ids)->update(['aviso_liquidacion_at' => now()]);
            });
        }
    }

    public function canSeeTicket(User $user, SoporteTicket $ticket): bool
    {
        return $user->isSuperAdmin() || (int) $ticket->id_usuario === (int) $user->id
            || ($user->rol === 'admin' && $ticket->nivel_escalamiento === 'admin');
    }

    public function ticketRecipients(SoporteTicket $ticket, bool $internal = false): array
    {
        $staff = User::where('estado', 1)->where(function ($query) use ($ticket) {
            $query->where('rol', $ticket->nivel_escalamiento === 'super_admin' ? 'super_admin' : 'admin');
        })->get()->filter(fn ($user) => $this->canSeeTicket($user, $ticket));
        if ($ticket->id_asignado) {
            $staff = $staff->filter(fn ($user) => $user->isSuperAdmin() || (int) $user->id === (int) $ticket->id_asignado);
        }
        $ids = $staff->pluck('id')->all();
        $owner = User::find($ticket->id_usuario);
        if ($owner && (!$internal || $owner->isAdmin())) {
            $ids[] = $owner->id;
        }
        return array_unique($ids);
    }
}
