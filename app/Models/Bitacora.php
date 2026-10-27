<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    public $timestamps = false;          // Solo usamos created_at manual
    protected $table   = 'bitacora';

    protected $fillable = [
        'id_usuario',
        'username',
        'nombre_usuario',
        'rol_usuario',
        'accion',
        'modulo',
        'descripcion',
        'datos_anteriores',
        'datos_nuevos',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos'     => 'array',
        'created_at'       => 'datetime',
    ];

    // ── Relación con usuario ────────────────────────────────────────────────
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // ── Scope: filtrar por módulo ──────────────────────────────────────────
    public function scopeModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    // ── Scope: filtrar por acción ──────────────────────────────────────────
    public function scopeAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    // ── Color badge según acción ───────────────────────────────────────────
    public function getBadgeColorAttribute(): string
    {
        return match ($this->accion) {
            'LOGIN'         => 'success',
            'LOGOUT'        => 'secondary',
            'CREAR'         => 'primary',
            'EDITAR'        => 'warning',
            'ELIMINAR'      => 'danger',
            'CAMBIO_ESTADO' => 'info',
            'CANCELAR'      => 'danger',
            'APROBAR'       => 'success',
            'TRANSFERIR'    => 'info',
            'LIQUIDAR'      => 'success',
            'RESTAURAR'     => 'warning',
            'EXPORTAR'      => 'secondary',
            'REORDENAR'     => 'secondary',
            'DEVOLUCION'    => 'warning',
            default         => 'secondary',
        };
    }

    // ── Ícono según acción ─────────────────────────────────────────────────
    public function getIconAttribute(): string
    {
        return match ($this->accion) {
            'LOGIN'         => 'fa-right-to-bracket',
            'LOGOUT'        => 'fa-right-from-bracket',
            'CREAR'         => 'fa-plus',
            'EDITAR'        => 'fa-pen',
            'ELIMINAR'      => 'fa-trash',
            'CAMBIO_ESTADO' => 'fa-toggle-on',
            'CANCELAR'      => 'fa-ban',
            'APROBAR'       => 'fa-check',
            'TRANSFERIR'    => 'fa-truck',
            'LIQUIDAR'      => 'fa-money-bill-wave',
            'RESTAURAR'     => 'fa-rotate-left',
            'EXPORTAR'      => 'fa-file-export',
            'REORDENAR'     => 'fa-sort',
            'DEVOLUCION'    => 'fa-arrow-rotate-left',
            default         => 'fa-circle-info',
        };
    }
}
