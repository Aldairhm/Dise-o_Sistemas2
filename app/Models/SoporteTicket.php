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

    // Estados esenciales y claros del ciclo de vida del ticket
    public const ESTADOS = [
        'Pendiente',
        'En proceso',
        'Escalada',
        'Resuelta',
        'Cancelada',
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

        // Regla estricta: si el ticket ya está en estado Resuelta o Cancelada, su estado es DEFINITIVO y no se permite cambiar
        if (in_array($this->estado, ['Resuelta', 'Cancelada'], true) && !$usuario->isSuperAdmin()) {
            return false;
        }

        // SUPER_ADMIN tiene facultad de supervisión global
        if ($usuario->isSuperAdmin()) {
            return true;
        }

        // Si el usuario es el creador de la duda (incluso si es Admin, cuando la duda fue enviada al Super Admin):
        // El creador NO puede auto-resolverse ni gestionar el estado técnico de su propia consulta,
        // únicamente puede Cancelarla si la duda aún está abierta ('Pendiente' o 'En proceso').
        if ($this->id_usuario === $usuario->id) {
            if ($nuevoEstado === 'Cancelada' && in_array($this->estado, ['Pendiente', 'En proceso'])) {
                return true;
            }
            return false;
        }

        // Si es vendedor atendiendo ticket ajeno (no permitido):
        if ($usuario->isVendedor()) {
            return false;
        }

        // Si es admin gestionando tickets de terceros (ej. dudas enviadas por vendedores):
        $transicionesValidas = [
            'Pendiente'  => ['En proceso', 'Escalada', 'Resuelta', 'Cancelada'],
            'En proceso' => ['Escalada', 'Resuelta', 'Cancelada'],
            'Escalada'   => ['En proceso', 'Resuelta', 'Cancelada'],
            // Compatibilidad transitoria con registros antiguos que no eran Resuelta/Cancelada
            'Asignada'            => ['En proceso', 'Resuelta', 'Cancelada'],
            'Esperando respuesta' => ['En proceso', 'Resuelta', 'Cancelada'],
            'Reabierta'           => ['En proceso', 'Resuelta', 'Cancelada'],
        ];

        return in_array($nuevoEstado, $transicionesValidas[$this->estado] ?? []);
    }
}
