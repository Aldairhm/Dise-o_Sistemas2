<?php

namespace App\Services;

use App\Models\{NotificationDelivery, NotificationEvent, SoporteTicket, User};
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
