<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Salida extends Model
{
    public $timestamps = false;
    protected $table = 'salida';

    protected $fillable = [
        'id_variante','id_usuario','cantidad','fecha_salida','hora_salida',
        'fecha_entrega','direccion','precio_envio','costo_extra','precio_unitario',
        'subtotal','descuento','total','costo_total_aplicado','comision_aplicada',
        'observaciones','estado','fecha_cancelacion','created_at',
    ];

    protected $casts = [
        'fecha_salida'      => 'date',
        'fecha_entrega'     => 'date',
        'fecha_cancelacion' => 'datetime',
        'created_at'        => 'datetime',
        'total'             => 'decimal:2',
        'comision_aplicada' => 'decimal:2',
    ];

    public function variante(): BelongsTo
    {
        return $this->belongsTo(Variante::class, 'id_variante');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function comision(): HasOne
    {
        return $this->hasOne(ComisionVendedor::class, 'id_salida');
    }
}