<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'venta';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'fecha',
        'total',
        'metodo_pago',
        'comprobante_pago',
        'telefono',
        'precio_envio',
        'estado',
        'tipo_venta',
        'observaciones',
        'comprobante_paquete',
        'comprobante_devolucion',
        'fecha_entrega',
        'fecha_cancelacion',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'total' => 'decimal:2',
        'precio_envio' => 'decimal:2',
        'fecha_entrega' => 'datetime',
        'fecha_cancelacion' => 'datetime',
    ];

    protected $appends = [
        'comprobante_url',
        'comprobante_paquete_url',
        'comprobante_devolucion_url',
        'dias_garantia',
        'fecha_limite_devolucion',
        'puede_devolver',
        'dias_restantes_devolucion',
        'texto_garantia_devolucion',
        'horas_restantes_devolucion',
        'estado_bloqueado',
        'estados_permitidos',
    ];

    public function getComprobanteUrlAttribute(): ?string
    {
        return $this->comprobante_pago ? asset('storage/' . $this->comprobante_pago) : null;
    }

    public function getComprobantePaqueteUrlAttribute(): ?string
    {
        return $this->comprobante_paquete ? asset('storage/' . $this->comprobante_paquete) : null;
    }

    public function getComprobanteDevolucionUrlAttribute(): ?string
    {
        return $this->comprobante_devolucion ? asset('storage/' . $this->comprobante_devolucion) : null;
    }

    /**
     * Días totales de garantía según modalidad:
     * - Tienda: 4 días (ej. compra el 20 -> hasta el 24 a las 23:59:59)
     * - Envío: 6 días (ej. entregado el 20 -> hasta el 26 a las 23:59:59)
     */
    public function getDiasGarantiaAttribute(): int
    {
        return ($this->tipo_venta === 'Envio') ? 6 : 4;
    }

    /**
     * Fecha límite exacta para solicitar devolución (hasta el fin del N-ésimo día)
     */
    public function getFechaLimiteDevolucionAttribute(): ?\Carbon\Carbon
    {
        $fechaRef = $this->fecha_entrega ?? $this->fecha;
        if (!$fechaRef) {
            return null;
        }

        $dias = $this->dias_garantia;
        return \Carbon\Carbon::parse($fechaRef)->startOfDay()->addDays($dias)->endOfDay();
    }

    /**
     * Verifica si una venta en estado Entregada aún está dentro del período de garantía en días
     */
    public function getPuedeDevolverAttribute(): bool
    {
        if ($this->estado !== 'Entregada') {
            return false;
        }

        $fechaLimite = $this->fecha_limite_devolucion;
        if (!$fechaLimite) {
            return false;
        }

        return now()->lte($fechaLimite);
    }

    /**
     * Retorna los días restantes de garantía de devolución
     */
    public function getDiasRestantesDevolucionAttribute(): int
    {
        if (!$this->puede_devolver) {
            return 0;
        }

        $fechaLimite = $this->fecha_limite_devolucion;
        if (!$fechaLimite) {
            return 0;
        }

        $dias = (int) now()->startOfDay()->diffInDays($fechaLimite->copy()->startOfDay(), false);
        return max(0, $dias);
    }

    /**
     * Retorna una etiqueta amigable con el estado de la garantía
     */
    public function getTextoGarantiaDevolucionAttribute(): string
    {
        if (!$this->puede_devolver) {
            return 'Garantía expirada';
        }

        $dias = $this->dias_restantes_devolucion;
        $fechaLimite = $this->fecha_limite_devolucion;
        $limiteStr = $fechaLimite ? $fechaLimite->format('d/m/Y') : '';

        if ($dias > 1) {
            return "{$dias} días (hasta {$limiteStr})";
        } elseif ($dias === 1) {
            return "1 día (hasta {$limiteStr})";
        } else {
            return "Último día (vence hoy a las 23:59)";
        }
    }

    /**
     * Horas restantes hasta la expiración del plazo
     */
    public function getHorasRestantesDevolucionAttribute(): int
    {
        if (!$this->puede_devolver) {
            return 0;
        }

        $fechaLimite = $this->fecha_limite_devolucion;
        if (!$fechaLimite) {
            return 0;
        }

        return max(0, (int) now()->diffInHours($fechaLimite, false));
    }

    /**
     * Indica si el estado ya no puede ser alterado bajo ninguna circunstancia
     */
    public function getEstadoBloqueadoAttribute(): bool
    {
        if (in_array($this->estado, ['Cancelada', 'Devolución'])) {
            return true;
        }

        if ($this->estado === 'Entregada' && !$this->puede_devolver) {
            return true;
        }

        return false;
    }

    /**
     * Retorna los posibles estados a los que puede transicionar según el flujo
     */
    public function getEstadosPermitidosAttribute(): array
    {
        if ($this->estado_bloqueado) {
            return [];
        }

        // Si está Entregada y dentro de las 72 horas, SOLO puede pasar a Devolución
        if ($this->estado === 'Entregada') {
            return $this->puede_devolver ? ['Devolución'] : [];
        }

        // Flujo para envíos:
        if ($this->tipo_venta === 'Envio') {
            return match ($this->estado) {
                'Pendiente' => ['Confirmada', 'Cancelada'],
                'Confirmada' => ['En ruta', 'Cancelada'],
                'En ruta' => ['Entregada', 'Cancelada', 'Devolución'],
                default => [],
            };
        }

        // Si es venta en tienda:
        return ['Entregada', 'Cancelada'];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta');
    }
}
