<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compra';

    protected $fillable = [
        'id_proveedor',
        'id_usuario',
        'fecha_compra',
        'referencia',
        'observaciones',
        'subtotal',
        'total',
        'estado',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function detalles()
    {
        return $this->hasMany(CompraDetalle::class, 'id_compra');
    }
}