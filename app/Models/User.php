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
    ];

    protected $hidden = [
        'password',
        'token',
    ];

    protected function casts(): array
    {
        return [
            'password'            => 'hashed',
            'porcentaje_comision' => 'decimal:2',
        ];
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function comisiones()
    {
        return $this->hasMany(ComisionVendedor::class, 'id_vendedor');
    }
}

