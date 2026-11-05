<?php

namespace App\Observers;

use App\Models\{AuditoriaSesion, ComisionVendedor, Devolucion, SoporteTicket, SoporteTicketMensaje, User, Venta};
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class NotificationObserver
{
    public function __construct(private NotificationService $notifications) {}

    public function created(Model $model): void { $this->dispatch($model, true); }
    public function updated(Model $model): void { $this->dispatch($model, false); }

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

        if ($model instanceof Venta && ($created || $model->wasChanged('estado'))) {
            $old = $created ? null : $model->getRawOriginal('estado');
            $ids = [$model->id_usuario];
            if ($created || in_array($model->estado, ['Cancelada', 'Devolución', 'Cambio'], true)) {
                $ids = array_merge($ids, $service->roles('admin'));
            }
            $emit($created ? 'venta_creada' : 'venta_estado', 'ventas', in_array($model->estado, ['Cancelada', 'Devolución']) ? 'importante' : 'informativa',
                "Venta #{$model->id}: {$model->estado}",
                $created ? "Se registró la venta #{$model->id} de {$model->usuario?->nombre_real} por $".number_format($model->total, 2)." en estado {$model->estado}." : "La venta #{$model->id} cambió de {$old} a {$model->estado}.",
                'venta', $model->id, $ids, ['previous_state' => $old, 'state' => $model->estado]);
        } elseif ($model instanceof ComisionVendedor && ($created || $model->wasChanged(['estado', 'monto', 'porcentaje']))) {
            $paid = $model->estado === 'Pagada' && ($created || $model->wasChanged('estado'));
            $emit($paid ? 'comision_pagada' : ($created ? 'comision_generada' : 'comision_actualizada'), 'comisiones', 'importante',
                $paid ? 'Comisión pagada' : "Comisión #{$model->id}: {$model->estado}",
                "Comisión #{$model->id} por $".number_format($model->monto, 2).". Estado confirmado: {$model->estado}.",
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
                $ids = array_filter($service->ticketRecipients($ticket, $model->tipo_mensaje !== 'publico'), fn ($id) => (int) $id !== (int) $model->id_usuario);
                $emit('ticket_respuesta', 'ayuda', 'importante', "Nueva respuesta #{$ticket->codigo_ticket}",
                    'Hay una nueva respuesta en la conversación de soporte.', 'ticket', $ticket->id, $ids,
                    ['internal' => $model->tipo_mensaje !== 'publico']);
            }
        } elseif ($model instanceof AuditoriaSesion && ($created || $model->wasChanged('estado'))) {
            $emit('auditoria_'.$model->estado, 'auditorias', 'importante', 'Auditoría administrativa de cuenta',
                $created ? 'Se ha iniciado una revisión administrativa autorizada de tu cuenta. La actividad está registrada bajo los controles de seguridad del sistema.' : "La revisión administrativa de tu cuenta está {$model->estado}.",
                'cuenta', $model->id_usuario_auditado, [$model->id_usuario_auditado], [], true);
            if (in_array($model->estado, ['expirada', 'revocada'], true)) {
                $emit('auditoria_intervencion', 'auditorias', 'alta', "Auditoría #{$model->id}: {$model->estado}",
                    'Consulta el historial autorizado de auditorías.', 'auditoria', $model->id, [$model->id_super_admin]);
            }
        } elseif ($model instanceof User && !$created && $model->wasChanged(['rol', 'estado', 'password', 'username', 'porcentaje_comision', 'mfa_enabled'])) {
            $emit('cuenta_actualizada', 'seguridad', 'alta', 'Cambios en tu cuenta',
                'Se actualizaron datos de acceso, permisos o configuración de tu cuenta. Si no reconoces el cambio, contacta a administración.',
                'cuenta', $model->id, [$model->id], [], true);
            if ($model->wasChanged(['rol', 'estado', 'mfa_enabled'])) {
                $emit('permisos_actualizados', 'seguridad', 'alta', 'Configuración de acceso modificada',
                    "Se modificaron permisos o el estado de la cuenta #{$model->id}.", 'usuarios', $model->id, $service->roles('super_admin'));
            }
        } elseif ($model instanceof Devolucion && $model->origen_tipo === 'compra' && ($created || $model->wasChanged('estado'))) {
            $emit('devolucion_proveedor', 'administracion', 'importante', "Devolución a proveedor #{$model->id}",
                "La devolución a proveedor #{$model->id} está en estado {$model->estado}.", 'devolucion', $model->id, $service->roles('admin'));
        }
    }
}
