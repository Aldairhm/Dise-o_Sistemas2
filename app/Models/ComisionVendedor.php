<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ComisionVendedor extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'comision_vendedor';

    protected $fillable = [
        'id_vendedor',
        'id_salida',
        'concepto',
        'monto',
        'porcentaje',
        'estado',
        'metodo_pago',
        'referencia_pago',
        'comprobante_pago',
        'notas',
        'liquidado_por',
        'fecha_liquidacion',
        'fecha_registro',
    ];

    protected $casts = [
        'monto'             => 'decimal:2',
        'porcentaje'        => 'decimal:2',
        'fecha_registro'    => 'datetime',
        'fecha_liquidacion' => 'datetime',
    ];

    protected $appends = [
        'comprobante_url',
        'es_liquidacion_bloqueada',
        'motivo_bloqueo_liquidacion',
    ];

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_vendedor');
    }

    public function salida(): BelongsTo
    {
        return $this->belongsTo(Salida::class, 'id_salida');
    }

    public function liquidadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'liquidado_por');
    }

    public function getComprobanteUrlAttribute(): ?string
    {
        if (!$this->comprobante_pago) {
            return null;
        }
        return asset('storage/' . $this->comprobante_pago);
    }

    public function getVentaAttribute(): ?Venta
    {
        return $this->salida?->venta;
    }

    /**
     * Determina si la comisión no puede ser liquidada aún debido a que:
     * 1) Está asociada a una venta cuya garantía de devolución está activa (puede_devolver == true).
     * 2) O la venta asociada aún no ha sido entregada (estado != 'Entregada').
     */
    public function getEsLiquidacionBloqueadaAttribute(): bool
    {
        // Solo aplica para comisiones en estado Pendiente
        if ($this->estado !== 'Pendiente') {
            return false;
        }

        // Bonos directos (sin salida) o deducciones/ajustes negativos no se bloquean por garantía de venta
        if (!$this->id_salida || (float) $this->monto <= 0) {
            return false;
        }

        $salida = $this->salida;
        if (!$salida) {
            return false;
        }

        $venta = $salida->venta;
        if (!$venta) {
            return false;
        }

        // Si la venta no está entregada ni completada en cambio
        if (!in_array($venta->estado, ['Entregada', 'Cambio'])) {
            return true;
        }

        // Si la venta está en estado 'Cambio' por envío y aún no tiene fecha de entrega del reemplazo
        if ($venta->estado === 'Cambio' && in_array($venta->tipo_venta, ['Envio', 'Envío']) && !$venta->fecha_entrega) {
            return true;
        }

        // Si la venta está entregada o con cambio entregado, pero aún dentro del plazo de garantía de devolución
        return (bool) $venta->puede_devolver;
    }

    /**
     * Retorna el motivo legible por el cual la comisión está bloqueada para liquidar
     */
    public function getMotivoBloqueoLiquidacionAttribute(): ?string
    {
        if (!$this->es_liquidacion_bloqueada) {
            return null;
        }

        $venta = $this->salida?->venta;
        if (!$venta) {
            return null;
        }

        if (!in_array($venta->estado, ['Entregada', 'Cambio'])) {
            return "Venta en estado \"{$venta->estado}\" (aún no entregada)";
        }

        if ($venta->estado === 'Cambio' && in_array($venta->tipo_venta, ['Envio', 'Envío']) && !$venta->fecha_entrega) {
            return "Reenvío por cambio en camino (aún no entregado)";
        }

        $dias = $venta->dias_restantes_devolucion;
        $fechaLimite = $venta->fecha_limite_devolucion ? $venta->fecha_limite_devolucion->format('d/m/Y') : '';
        if ($dias > 1) {
            return "Garantía activa ({$dias} días restantes, hasta {$fechaLimite})";
        } elseif ($dias === 1) {
            return "Garantía activa (1 día restante, hasta {$fechaLimite})";
        } else {
            return "Garantía activa (vence hoy a las 23:59)";
        }
    }

    /**
     * Eager-loader eficiente para asociar ventas a una colección de comisiones
     */
    public static function cargarVentas($comisiones)
    {
        if (empty($comisiones) || (method_exists($comisiones, 'isEmpty') && $comisiones->isEmpty())) {
            return $comisiones;
        }

        $salidas = collect($comisiones)->pluck('salida')->filter();
        $ventaIds = [];
        foreach ($salidas as $salida) {
            if ($salida->venta_id) {
                $ventaIds[$salida->id] = $salida->venta_id;
            }
        }

        if (!empty($ventaIds)) {
            $ventas = Venta::whereIn('id', array_unique(array_values($ventaIds)))->get()->keyBy('id');
            foreach ($salidas as $salida) {
                $vId = $ventaIds[$salida->id] ?? null;
                $salida->setRelation('ventaModel', $vId ? $ventas->get($vId) : null);
            }
        }

        return $comisiones;
    }

    public function scopePendientes($query) { return $query->where('estado', 'Pendiente'); }
    public function scopePagadas($query)    { return $query->where('estado', 'Pagada'); }
    public function scopeCanceladas($query) { return $query->where('estado', 'Cancelada'); }
    public function scopeDeVendedor($query, int $id) { return $query->where('id_vendedor', $id); }
}
