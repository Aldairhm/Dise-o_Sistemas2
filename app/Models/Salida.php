<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'nombre_cliente',
        'departamento',
        'municipio',
        'direccion',
        'telefono',
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
        'comprobante_paquete',
        'comprobante_devolucion',
        'fecha_cancelacion',
        'created_at',
    ];

    protected $casts = [
        'cantidad'             => 'integer',
        'fecha_salida'         => 'date',
        'fecha_entrega'        => 'date',
        'precio_envio'         => 'decimal:2',
        'costo_extra'          => 'decimal:2',
        'precio_unitario'      => 'decimal:2',
        'subtotal'             => 'decimal:2',
        'descuento'            => 'decimal:2',
        'total'                => 'decimal:2',
        'costo_total_aplicado' => 'decimal:2',
        'comision_aplicada'    => 'decimal:2',
        'fecha_cancelacion'    => 'datetime',
        'created_at'           => 'datetime',
    ];

    public function variante(): BelongsTo
    {
        return $this->belongsTo(Variante::class, 'id_variante');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function comision(): HasOne
    {
        return $this->hasOne(ComisionVendedor::class, 'id_salida');
    }

    public function comisiones(): HasMany
    {
        return $this->hasMany(ComisionVendedor::class, 'id_salida');
    }

    public function getVentaIdAttribute(): ?int
    {
        if (preg_match('/Venta\s*#(\d+)/i', $this->observaciones ?? '', $m)) {
            return (int) $m[1];
        }
        return null;
    }

    public function getVentaAttribute(): ?Venta
    {
        if ($this->relationLoaded('ventaModel')) {
            return $this->getRelation('ventaModel');
        }
        $ventaId = $this->venta_id;
        if ($ventaId) {
            $venta = Venta::find($ventaId);
            $this->setRelation('ventaModel', $venta);
            return $venta;
        }
        return null;
    }
}
