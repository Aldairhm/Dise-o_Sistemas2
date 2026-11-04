<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;
    protected $table = 'usuario';

    protected $fillable = [
        'nombre_real',
        'telefono',
        'username',
        'password',
        'rol',
        'estado',
        'token',
        'porcentaje_comision',
        'mfa_enabled',
        'mfa_secret',
        'mfa_backup_codes',
    ];

    protected $hidden = [
        'password',
        'token',
        'mfa_secret',
        'mfa_backup_codes',
    ];

    protected function casts(): array
    {
        return [
            'password'            => 'hashed',
            'porcentaje_comision' => 'decimal:2',
            'mfa_enabled'         => 'boolean',
        ];
    }

    // ── Helpers de Roles ───────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->rol === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->rol, ['admin', 'super_admin']);
    }

    public function isVendedor(): bool
    {
        return $this->rol === 'vendedor';
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_usuario');
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class, 'id_usuario');
    }

    public function comisiones()
    {
        return $this->hasMany(ComisionVendedor::class, 'id_vendedor');
    }

    public function ticketsCreados()
    {
        return $this->hasMany(SoporteTicket::class, 'id_usuario');
    }

    public function ticketsAsignados()
    {
        return $this->hasMany(SoporteTicket::class, 'id_asignado');
    }

    public function auditoriasIniciadas()
    {
        return $this->hasMany(AuditoriaSesion::class, 'id_super_admin');
    }

    public function auditoriasRecibidas()
    {
        return $this->hasMany(AuditoriaSesion::class, 'id_usuario_auditado');
    }
}
