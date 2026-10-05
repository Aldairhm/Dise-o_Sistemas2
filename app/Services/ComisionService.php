<?php

namespace App\Services;

use App\Mail\ComisionPagadaMail;
use App\Models\ComisionVendedor;
use App\Models\Devolucion;
use App\Models\Salida;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ComisionService
{
    /**
     * Resuelve el monto de comisión unitario ($) para una variante/producto.
     * Toma el valor directo asignado a la variante o al producto.
     *
     * @param  mixed $variante
     * @return float
     */
    public function resolverComisionUnitaria($variante = null): float
    {
        if (!$variante) {
            return 0.0;
        }

        // 1. Comisión directa en la variante si existe y es > 0
        if ((float) ($variante->comision ?? 0) > 0) {
            return (float) $variante->comision;
        }

        // 2. Comisión directa en el producto padre
        if (!empty($variante->producto) && (float) ($variante->producto->comision ?? 0) > 0) {
            return (float) $variante->producto->comision;
        }

        return 0.0;
    }

    /**
     * Calcula el monto total de comisión según la cantidad de unidades vendidas.
     *
     * @param  float $comisionUnitaria  Valor de comisión por unidad en dólares ($)
     * @param  int   $cantidad          Unidades vendidas
     * @return float
     */
    public function calcularMonto(float $comisionUnitaria, int $cantidad): float
    {
        return round($comisionUnitaria * max(1, $cantidad), 2);
    }

    /**
     * Obtiene las comisiones pendientes detalladas de un vendedor.
     *
     * @param  int $idVendedor
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendientesVendedor(int $idVendedor, ?string $fechaDesde = null, ?string $fechaHasta = null)
    {
        $pendientes = ComisionVendedor::with(['salida.variante.producto'])
            ->where('id_vendedor', $idVendedor)
            ->where('estado', 'Pendiente')
            ->when($fechaDesde && $fechaHasta, function ($query) use ($fechaDesde, $fechaHasta) {
                $query->whereBetween('fecha_registro', [
                    Carbon::parse($fechaDesde)->startOfDay(),
                    Carbon::parse($fechaHasta)->endOfDay(),
                ]);
            })
            ->get();

        $ajustesNegativos = ComisionVendedor::with(['salida.variante.producto'])
            ->where('id_vendedor', $idVendedor)
            ->whereIn('estado', ['Cancelada', 'Pendiente'])
            ->where('monto', '<', 0)
            ->when($fechaDesde && $fechaHasta, function ($query) use ($fechaDesde, $fechaHasta) {
                $query->whereBetween('fecha_registro', [
                    Carbon::parse($fechaDesde)->startOfDay(),
                    Carbon::parse($fechaHasta)->endOfDay(),
                ]);
            })
            ->get();

        return $pendientes->merge($ajustesNegativos)
            ->unique('id')
            ->sortBy('fecha_registro')
            ->values();
    }

    /**
     * Liquidar comisiones de un vendedor con registro de método de pago.
     *
     * @param  int         $idVendedor
     * @param  array       $comisionesIds     Array de IDs específicos a liquidar (vacío = todas las pendientes)
     * @param  string      $metodoPago        Efectivo, Transferencia Bancaria, Cheque, etc.
     * @param  string|null $referenciaPago    N° de transferencia, recibo o cheque
     * @param  string|null $comprobantePath   Ruta del archivo adjunto
     * @param  string|null $notas            Comentarios adicionales
     * @return int Cantidad de comisiones liquidadas
     */
    public function liquidarComisiones(
        int $idVendedor,
        array $comisionesIds = [],
        string $metodoPago = 'Efectivo',
        ?string $referenciaPago = null,
        ?string $comprobantePath = null,
        ?string $notas = null,
        ?string $fechaDesde = null,
        ?string $fechaHasta = null
    ): int {
        $notasFin = $notas ?? 'Liquidación procesada.';
        $comisiones = DB::transaction(function () use (
            $idVendedor,
            $comisionesIds,
            $metodoPago,
            $referenciaPago,
            $comprobantePath,
            $notasFin,
            $fechaDesde,
            $fechaHasta
        ) {
            $pendientesQuery = ComisionVendedor::where('id_vendedor', $idVendedor)
                ->where('estado', 'Pendiente')
                ->where('monto', '>=', 0)
                ->when($fechaDesde && $fechaHasta, function ($query) use ($fechaDesde, $fechaHasta) {
                    $query->whereBetween('fecha_registro', [
                        Carbon::parse($fechaDesde)->startOfDay(),
                        Carbon::parse($fechaHasta)->endOfDay(),
                    ]);
                });

            if (!empty($comisionesIds)) {
                $pendientesQuery->whereIn('id', $comisionesIds);
            }

            $pendientes = $pendientesQuery->lockForUpdate()->get();
            $ajustesNegativos = ComisionVendedor::where('id_vendedor', $idVendedor)
                ->whereIn('estado', ['Cancelada', 'Pendiente'])
                ->where('monto', '<', 0)
                ->when($fechaDesde && $fechaHasta, function ($query) use ($fechaDesde, $fechaHasta) {
                    $query->whereBetween('fecha_registro', [
                        Carbon::parse($fechaDesde)->startOfDay(),
                        Carbon::parse($fechaHasta)->endOfDay(),
                    ]);
                })
                ->lockForUpdate()
                ->get();
            $comisiones = $pendientes->merge($ajustesNegativos)->unique('id')->values();

            if ($comisiones->isEmpty() || (float) $comisiones->sum('monto') <= 0) {
                return collect();
            }

            $adminId = Auth::id();
            $ahora = now();

            foreach ($comisiones as $comision) {
                $notasComision = (float) $comision->monto < 0
                    ? trim(($comision->notas ? $comision->notas . ' | ' : '') . 'Ajuste negativo aplicado en liquidación.')
                    : $notasFin;

                $comision->update([
                    'estado'             => 'Pagada',
                    'metodo_pago'        => $metodoPago,
                    'referencia_pago'    => $referenciaPago,
                    'comprobante_pago'   => $comprobantePath ?? $comision->comprobante_pago,
                    'liquidado_por'      => $adminId,
                    'fecha_liquidacion'  => $ahora,
                    'notas'              => $notasComision,
                ]);
            }

            return $comisiones;
        });

        if ($comisiones->isEmpty()) {
            return 0;
        }

        // Cargar relación de salida para detalle en el correo
        $comisiones->load('salida');

        // Enviar correo de notificación al vendedor con el resumen de pago
        try {
            $vendedor = User::find($idVendedor);
            if ($vendedor && filter_var($vendedor->username, FILTER_VALIDATE_EMAIL)) {
                Mail::to($vendedor->username)->send(
                    new ComisionPagadaMail($vendedor, $comisiones, $metodoPago, $referenciaPago, $notas, $comprobantePath)
                );
            }
        } catch (\Throwable $e) {
            Log::error("Error al enviar correo de liquidación a vendedor ID {$idVendedor}: " . $e->getMessage());
        }

        return $comisiones->count();
    }

    /**
     * Cancela la comisión asociada a una salida (ej: devolución o anulación de venta).
     *
     * @param  int $idSalida
     * @return bool
     */
    public function cancelarPorSalida(int $idSalida): bool
    {
        $comision = ComisionVendedor::where('id_salida', $idSalida)
            ->where('estado', 'Pendiente')
            ->first();

        if (!$comision) {
            return false;
        }

        $comision->update([
            'estado' => 'Cancelada',
            'notas'  => ($comision->notas ? $comision->notas . ' | ' : '') . 'Cancelada por anulación de salida.',
        ]);

        return true;
    }

    /**
     * Resumen de comisiones agrupado por estado para un vendedor.
     *
     * @param  int $idVendedor
     * @return array{pendiente: float, pagada: float, cancelada: float, total: float}
     */
    public function resumenVendedor(int $idVendedor): array
    {
        $rows = ComisionVendedor::where('id_vendedor', $idVendedor)
            ->select('estado', DB::raw('SUM(monto) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return [
            'pendiente' => (float) ($rows['Pendiente'] ?? 0),
            'pagada'    => (float) ($rows['Pagada']    ?? 0),
            'cancelada' => (float) ($rows['Cancelada'] ?? 0),
            'total'     => (float) $rows->sum(),
        ];
    }

    /**
     * Procesa el impacto financiero y de comisiones según la resolución tomada en la devolución:
     * - reembolso_tienda: Reembolso de dinero (Producto intacto / Paquete no recibido).
     *                     Anula o reduce comisiones de los artículos devueltos y, si es envío, deduce el 50% del costo de envío.
     * - reembolso_cuarentena: Reembolso de dinero (Producto dañado de fábrica).
     *                         Anula o reduce comisiones de los artículos devueltos (sin deducción de envío al vendedor).
     * - cambio_tienda: Cambio físico (Talla/Color equivocado).
     *                  Mantiene la comisión del vendedor e inserta nota de auditoría.
     * - cambio_cuarentena: Cambio físico (Viene defectuoso).
     *                      Mantiene la comisión del vendedor e inserta nota de auditoría.
     *
     * @param  \App\Models\Devolucion  $devolucion
     * @param  \App\Models\Venta       $venta
     * @param  string                  $tipoResolucion
     * @param  array                   $productosDevueltos Array con ['id_variante' => int, 'cantidad' => int]
     * @return void
     */
    public function procesarResolucionDevolucion(Devolucion $devolucion, Venta $venta, string $tipoResolucion, array $productosDevueltos): void
    {
        $folioVenta = 'VNT-' . str_pad($venta->id, 5, '0', STR_PAD_LEFT);
        $folioDev = 'DEV-' . str_pad($devolucion->id, 5, '0', STR_PAD_LEFT);
        $esReembolso = str_contains($tipoResolucion, 'reembolso');
        $esCambio = str_contains($tipoResolucion, 'cambio');

        $labelResolucion = match ($tipoResolucion) {
            'reembolso_tienda' => 'Reembolso (Producto intacto / Paquete no recibido)',
            'reembolso_cuarentena' => 'Reembolso (Producto dañado de fábrica)',
            'cambio_tienda' => 'Cambio físico (Talla/Color equivocado)',
            'cambio_cuarentena' => 'Cambio físico (Viene defectuoso)',
            default => str_replace('_', ' ', $tipoResolucion),
        };

        // Salidas asociadas a la venta
        $salidas = Salida::where('observaciones', 'like', "Venta #{$venta->id}%")->get()->keyBy('id_variante');

        foreach ($productosDevueltos as $item) {
            $idVariante = (int) $item['id_variante'];
            $cantDevuelta = (int) $item['cantidad'];

            $salida = $salidas->get($idVariante);
            if (!$salida) {
                continue;
            }

            $comision = ComisionVendedor::where('id_salida', $salida->id)->first();
            if (!$comision) {
                continue;
            }

            $cantOriginal = (int) $salida->cantidad;
            $montoOriginal = (float) $comision->monto;

            if ($esReembolso) {
                if ($cantDevuelta >= $cantOriginal) {
                    // Devolución total de la línea
                    $montoAnteriorStr = '$' . number_format($montoOriginal, 2);
                    $notaAjuste = "Ajuste POS por devolución: saldo anterior {$montoAnteriorStr} -> $0.00. Cancelada por Devolución ({$labelResolucion}) [{$folioDev}].";
                    $comision->update([
                        'monto' => 0.00,
                        'estado' => 'Cancelada',
                        'notas' => trim(($comision->notas ? $comision->notas . ' | ' : '') . $notaAjuste),
                    ]);
                } else {
                    // Devolución parcial de la línea: calcular comisión unitaria efectiva (incluye base + extra)
                    $comisionUnitaria = $cantOriginal > 0
                        ? round($montoOriginal / $cantOriginal, 2)
                        : (float) ($comision->porcentaje ?: 0);
                    $deduccion = round($comisionUnitaria * $cantDevuelta, 2);
                    $nuevoMonto = max(0.00, $montoOriginal - $deduccion);

                    $notaAjuste = "Descuento por devolución parcial ({$cantDevuelta}/{$cantOriginal} unds) ({$labelResolucion}): saldo anterior $"
                        . number_format($montoOriginal, 2) . " -> $" . number_format($nuevoMonto, 2) . " [{$folioDev}].";

                    $comision->update([
                        'monto' => $nuevoMonto,
                        'notas' => trim(($comision->notas ? $comision->notas . ' | ' : '') . $notaAjuste),
                    ]);
                }
            } elseif ($esCambio) {
                // En cambios físicos NO hay reembolso de dinero; el cliente retiene la compra por un artículo de reemplazo.
                // La comisión del vendedor se mantiene íntegra con nota de auditoría.
                $notaCambio = "Cambio físico registrado ({$cantDevuelta} unds) ({$labelResolucion}) [{$folioDev}]. Comisión se mantiene.";
                $comision->update([
                    'notas' => trim(($comision->notas ? $comision->notas . ' | ' : '') . $notaCambio),
                ]);
            }
        }

        // ── Deducción del 50% de envío si es 'reembolso_tienda' (Paquete no recibido en envíos) ──
        if ($tipoResolucion === 'reembolso_tienda' && in_array($venta->tipo_venta, ['Envio', 'Envío']) && (float) $venta->precio_envio > 0) {
            $deduccionExiste = ComisionVendedor::where('id_vendedor', $venta->id_usuario)
                ->where('concepto', 'like', "%Deducción envío%{$folioVenta}%")
                ->where('monto', '<', 0)
                ->exists();

            if (!$deduccionExiste) {
                $costoEnvio = (float) $venta->precio_envio;
                $montoDeduccion = -round($costoEnvio * 0.50, 2);
                $primeraSalidaId = $salidas->first()?->id;

                ComisionVendedor::create([
                    'id_vendedor' => $venta->id_usuario,
                    'id_salida' => $primeraSalidaId,
                    'concepto' => "Deducción envío - Devolución {$folioVenta}",
                    'monto' => $montoDeduccion,
                    'porcentaje' => 0.00,
                    'estado' => 'Cancelada',
                    'notas' => "Deducción por devolución de envío: -$"
                        . number_format(abs($montoDeduccion), 2)
                        . " (50 % del costo de envío: $"
                        . number_format($costoEnvio, 2) . ") | Venta {$folioVenta} ({$folioDev} - Paquete no recibido)",
                    'fecha_registro' => now(),
                ]);
            }
        }
    }
}
