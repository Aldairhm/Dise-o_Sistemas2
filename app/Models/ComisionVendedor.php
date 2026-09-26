<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComisionVendedor extends Model
{
    use HasFactory;

    protected $table = 'comision_vendedor';

    public $timestamps = false;

    protected $fillable = [
        'id_vendedor',
        'id_salida',
        'monto',
        'estado',
        'fecha_registro',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_registro' => 'datetime',
    ];

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'id_vendedor');
    }

    public function salida()
    {
        return $this->belongsTo(Salida::class, 'id_salida');
    }
}
