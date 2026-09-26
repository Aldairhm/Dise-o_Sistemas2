<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;

    protected $table = 'salida';

    public $timestamps = false;

    protected $fillable = [
        'id_variante',
        'id_usuario',
        'cantidad',
        'fecha_salida',
        'hora_salida',
        'fecha_entrega',
        'direccion',
        'precio_envio',
        'costo_extra',
        'precio_unitario',
        'subtotal',
        'descuento',
        'total',
        'costo_total_aplicado',
        'comision_aplicada',
        'observaciones',
        'estado',
        'fecha_cancelacion',
        'created_at',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'fecha_salida' => 'date',
        'fecha_entrega' => 'date',
        'precio_envio' => 'decimal:2',
        'costo_extra' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'costo_total_aplicado' => 'decimal:2',
        'comision_aplicada' => 'decimal:2',
        'fecha_cancelacion' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function variante()
    {
        return $this->belongsTo(Variante::class, 'id_variante');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function comisiones()
    {
        return $this->hasMany(ComisionVendedor::class, 'id_salida');
    }
}
