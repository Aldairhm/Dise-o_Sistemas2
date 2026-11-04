<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SoporteTicket extends Model
{
    use HasFactory;

    protected $table = 'soporte_tickets';

    protected $fillable = [
        'codigo_ticket',
        'titulo',
        'id_usuario',
        'nombre_usuario',
        'rol_usuario',
        'id_asignado',
        'modulo',
        'categoria',
        'tipo_consulta',
        'prioridad',
        'duda',
        'evidencia_url',
        'estado',
        'nivel_escalamiento',
        'escalado_por',
        'justificacion_escalamiento',
        'id_ticket_padre',
        'resolucion',
        'resuelto_por',
        'fecha_resolucion',
        'fecha_cerrado',
    ];

    protected $casts = [
        'fecha_resolucion' => 'datetime',
        'fecha_cerrado'    => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    // Transiciones válidas de estado según la jerarquía
    public const ESTADOS = [
        'Pendiente',
        'Asignada',
        'En proceso',
        'Esperando respuesta',
        'Escalada',
        'Resuelta',
        'Cerrada',
        'Reabierta',
    ];

    public const PRIORIDADES = [
        'Baja',
        'Media',
        'Alta',
        'Urgente',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function asignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_asignado');
    }

    public function escalador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalado_por');
    }

    public function resolvedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resuelto_por');
    }

    public function ticketPadre(): BelongsTo
    {
        return $this->belongsTo(SoporteTicket::class, 'id_ticket_padre');
    }

    public function subTickets(): HasMany
    {
        return $this->hasMany(SoporteTicket::class, 'id_ticket_padre');
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(SoporteTicketMensaje::class, 'id_ticket')->orderBy('id', 'asc');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(SoporteTicketHistorial::class, 'id_ticket')->orderBy('id', 'desc');
    }

    /**
     * Valida si la transición de estado solicitada está permitida
     */
    public function puedeTransicionarA(string $nuevoEstado, User $usuario): bool
    {
        if ($this->estado === $nuevoEstado) {
            return true;
        }

        // SUPER_ADMIN tiene facultad de supervisión y puede reajustar cualquier estado
        if ($usuario->isSuperAdmin()) {
            return true;
        }

        // Si es vendedor: solo puede reabrir si estaba resuelta o cerrada, o cerrar voluntariamente
        if ($usuario->isVendedor()) {
            if ($this->id_usuario !== $usuario->id) {
                return false;
            }
            if ($nuevoEstado === 'Reabierta' && in_array($this->estado, ['Resuelta', 'Cerrada'])) {
                return true;
            }
            if ($nuevoEstado === 'Cerrada' && in_array($this->estado, ['Resuelta', 'En proceso'])) {
                return true;
            }
            return false;
        }

        // Si es admin: puede gestionar el flujo normal
        $transicionesValidas = [
            'Pendiente'           => ['Asignada', 'En proceso', 'Esperando respuesta', 'Escalada', 'Resuelta', 'Cerrada'],
            'Asignada'            => ['En proceso', 'Esperando respuesta', 'Escalada', 'Resuelta', 'Cerrada'],
            'En proceso'          => ['Esperando respuesta', 'Escalada', 'Resuelta', 'Cerrada'],
            'Esperando respuesta' => ['En proceso', 'Resuelta', 'Cerrada'],
            'Escalada'            => ['En proceso', 'Resuelta'], // Admin puede retomar si super admin responde
            'Resuelta'            => ['Cerrada', 'Reabierta'],
            'Cerrada'             => ['Reabierta'],
            'Reabierta'           => ['En proceso', 'Esperando respuesta', 'Escalada', 'Resuelta'],
        ];

        return in_array($nuevoEstado, $transicionesValidas[$this->estado] ?? []);
    }
}
