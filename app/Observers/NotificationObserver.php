<?php

namespace App\Observers;

use App\Models\{AuditoriaSesion, ComisionVendedor, MovimientoBodega, SoporteTicket, SoporteTicketMensaje, User, Venta};
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class NotificationObserver
{
    public function __construct(private NotificationService $notifications) {}

    public function created(Model $model): void
    {
        if ($model instanceof MovimientoBodega) {
            $this->notifyKardex($model);
            return;
        }

        $this->dispatch($model, true);
        if ($model instanceof ComisionVendedor && !$model->id_salida && $model->estado === 'Pendiente' && (float) $model->monto > 0) {
            $this->notifications->notifyPayableCommissions($model->id);
        }
    }
    public function updated(Model $model): void
    {
        if (!$model instanceof MovimientoBodega) {
            $this->dispatch($model, false);
        }
    }

    private function dispatch(Model $model, bool $created): void
    {
        $service = $this->notifications;
        $key = $model->getTable().':'.$model->id.':'.$model->notification_event_key;
        $actor = session()->get('auditoria_actor_real_id', Auth::id());
        $emit = function ($type, $category, $priority, $title, $message, $resource, $id, $ids, $context = [], $includeActor = false) use ($service, $key, $actor) {
            if (!$includeActor) {
                $ids = array_filter($ids, fn ($id) => (int) $id !== (int) $actor);
            }
            if ($ids) {
                $service->publish($key.':'.$type, $type, $category, $priority, $title, $message, $resource, $id, $ids, $context);
            }
        };

        if ($model instanceof Venta) {
            $shipping = in_array($model->tipo_venta, ['Envio', 'Envío'], true);
            if (!$created && (!$shipping || !$model->wasChanged('estado'))) {
                return;
            }

            $seller = $model->usuario()->first();
            $ids = $seller?->isVendedor() ? [$seller->id] : [];
            $notifyAdmins = $shipping ? $model->estado === 'Entregada' : $created;
            if ($notifyAdmins) {
                $ids = array_merge($ids, $service->roles('admin'));
            }
            $old = $created ? null : $model->getRawOriginal('estado');
            $channel = $shipping ? 'por envío' : 'en punto de venta';
            $title = $shipping ? "Venta #{$model->id}: {$model->estado}" : "Venta #{$model->id} realizada";
            $sellerName = $seller?->nombre_real ?? "Usuario #{$model->id_usuario}";
            $emit($created ? 'venta_creada' : 'venta_estado', 'ventas', in_array($model->estado, ['Cancelada', 'Devolución']) ? 'importante' : 'informativa',
                $title,
                $created ? "Se registró la venta {$channel} #{$model->id} de {$sellerName} por $".number_format($model->total, 2)."." : "La venta por envío #{$model->id} cambió de {$old} a {$model->estado}.",
                'venta', $model->id, $ids, ['previous_state' => $old, 'state' => $model->estado], true);
        } elseif ($model instanceof ComisionVendedor && ($created || $model->wasChanged(['estado', 'monto', 'porcentaje', 'concepto']))) {
            if (!$model->vendedor()->where('rol', 'vendedor')->exists()) {
                return;
            }

            [$kind, $label] = match (true) {
                (float) $model->monto < 0 => ['ajuste', 'Ajuste'],
                !$model->id_salida => ['bono', 'Bono'],
                default => ['comision', 'Comisión'],
            };
            $paid = $model->estado === 'Pagada' && ($created || $model->wasChanged('estado'));
            [$action, $title] = match (true) {
                $paid => $kind === 'comision' ? ['pagada', 'Comisión pagada'] : ['liquidado', "{$label} liquidado"],
                $created => $kind === 'comision' ? ['generada', 'Comisión generada'] : ['registrado', "{$label} registrado"],
                default => $kind === 'comision' ? ['actualizada', 'Comisión actualizada'] : ['actualizado', "{$label} actualizado"],
            };
            $amount = '$'.number_format((float) $model->monto, 2);
            $message = "{$label} #{$model->id} por {$amount}. Estado: {$model->estado}.";
            $concept = $model->concepto ?: ($model->venta ? "Venta #{$model->venta->id}" : null);
            if ($concept) {
                $message .= " {$concept}.";
            }
            if (!$created && $model->wasChanged('monto')) {
                $message .= ' Monto anterior: $'.number_format((float) $model->getRawOriginal('monto'), 2).'.';
            }
            if ($reason = $model->motivo_bloqueo_liquidacion) {
                $message .= " Liquidación pendiente: {$reason}.";
            }
            $emit("{$kind}_{$action}", 'comisiones', 'importante', $title, $message,
                'comision', $model->id, [$model->id_vendedor], ['state' => $model->estado], true);
        } elseif ($model instanceof SoporteTicket && ($created || $model->wasChanged(['estado', 'prioridad', 'id_asignado', 'nivel_escalamiento']))) {
            $priority = in_array($model->prioridad, ['Alta', 'Urgente']) ? 'alta' : 'importante';
            $emit($created ? 'ticket_creado' : 'ticket_actualizado', 'ayuda', $priority,
                ($created ? 'Nueva solicitud' : 'Solicitud actualizada')." #{$model->codigo_ticket}",
                "Solicitud #{$model->codigo_ticket}. Estado: {$model->estado}. Prioridad: {$model->prioridad}.",
                'ticket', $model->id, $service->ticketRecipients($model), [], $created);
        } elseif ($model instanceof SoporteTicketMensaje && $created) {
            $ticket = $model->ticket;
            // The opening message is already represented by ticket_creado.
            if ($ticket && !((int) $model->id_usuario === (int) $ticket->id_usuario && $ticket->mensajes()->count() === 1)) {
                $internal = $model->tipo_mensaje !== 'publico';
                $ids = array_filter($service->ticketRecipients($ticket, $internal), fn ($id) => (int) $id !== (int) $model->id_usuario);
                $title = $internal ? 'Nueva respuesta' : 'Solicitud contestada';
                $emit('ticket_respuesta', 'ayuda', 'importante', "{$title} #{$ticket->codigo_ticket}",
                    'Hay una nueva respuesta en la conversación de soporte.', 'ticket', $ticket->id, $ids,
                    ['internal' => $internal]);
            }
        } elseif ($model instanceof AuditoriaSesion && ($created || $model->wasChanged('estado'))) {
            $emit('auditoria_'.$model->estado, 'auditorias', 'importante', 'Auditoría administrativa de cuenta',
                $created ? 'Se ha iniciado una revisión administrativa autorizada de tu cuenta. La actividad está registrada bajo los controles de seguridad del sistema.' : "La revisión administrativa de tu cuenta está {$model->estado}.",
                'cuenta', $model->id_usuario_auditado, [$model->id_usuario_auditado], [], true);
            if (in_array($model->estado, ['expirada', 'revocada'], true)) {
                $emit('auditoria_intervencion', 'auditorias', 'alta', "Auditoría #{$model->id}: {$model->estado}",
                    'Consulta el historial autorizado de auditorías.', 'auditoria', $model->id, [$model->id_super_admin]);
            }
        } elseif ($model instanceof User && !$created) {
            $profileFields = [
                'nombre_real' => 'nombre', 'telefono' => 'teléfono', 'username' => 'correo electrónico',
                'password' => 'contraseña', 'rol' => 'rol', 'estado' => 'estado de la cuenta',
                'porcentaje_comision' => 'porcentaje de comisión',
            ];
            $fields = array_values(array_intersect_key($profileFields, $model->getChanges()));
            if ($model->wasChanged(['mfa_enabled', 'mfa_secret', 'mfa_backup_codes'])) {
                $fields[] = 'verificación en dos pasos';
            }
            if ($fields) {
                $emit('cuenta_actualizada', 'seguridad', 'alta', 'Cambios en tu perfil',
                    'Se actualizaron los siguientes datos de tu cuenta: '.implode(', ', $fields).'. Si no reconoces el cambio, contacta a administración.',
                    'cuenta', $model->id, [$model->id], [], true);
            }
            if ($model->wasChanged(['rol', 'estado', 'mfa_enabled'])) {
                $emit('permisos_actualizados', 'seguridad', 'alta', 'Configuración de acceso modificada',
                    "Se modificaron permisos o el estado de la cuenta #{$model->id}.", 'usuarios', $model->id, $service->roles('super_admin'));
            }
        }
    }

    private function notifyKardex(MovimientoBodega $movement): void
    {
        $recipients = $this->notifications->roles('admin');
        if (!$recipients) {
            return;
        }

        $type = match ($movement->tipo) {
            'compra_recibida' => 'Compra recibida',
            'transferencia_tienda' => 'Transferencia de bodega a tienda',
            'devolucion_cliente' => 'Devolución de cliente',
            'devolucion_proveedor' => 'Devolución a proveedor',
            default => ucfirst(str_replace('_', ' ', $movement->tipo)),
        };
        $variant = $movement->variante;
        $product = $variant?->producto?->nombre ?? "Variante #{$movement->id_variante}";
        $changes = [];
        foreach ([
            ['Bodega', $movement->reserva_anterior, $movement->reserva_nueva],
            ['Tienda', $movement->stock_anterior, $movement->stock_nuevo],
            ['Cuarentena', $movement->stock_cuarentena_anterior, $movement->stock_cuarentena_nuevo],
        ] as [$location, $before, $after]) {
            if ($before !== null && $after !== null && (int) $before !== (int) $after) {
                $changes[] = "{$location} {$before} → {$after}";
            }
        }
        $item = $variant?->sku ? "{$product} ({$variant->sku})" : $product;
        $message = "{$item}: {$movement->cantidad} unidad(es).";
        if ($changes) {
            $message .= ' '.implode('. ', $changes).'.';
        }

        $this->notifications->publish("kardex:{$movement->id}", 'kardex_movimiento', 'inventario', 'informativa',
            $type, $message, 'movimiento_bodega', $movement->id, $recipients);
    }
}
