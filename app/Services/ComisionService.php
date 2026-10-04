<?php

namespace App\Services;

use App\Mail\ComisionPagadaMail;
use App\Models\ComisionVendedor;
use App\Models\User;
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
    public function getPendientesVendedor(int $idVendedor)
    {
        $pendientes = ComisionVendedor::with(['salida.variante.producto'])
            ->where('id_vendedor', $idVendedor)
            ->where('estado', 'Pendiente')
            ->get();

        $ajustesNegativos = ComisionVendedor::with(['salida.variante.producto'])
            ->where('id_vendedor', $idVendedor)
            ->whereIn('estado', ['Cancelada', 'Pendiente'])
            ->where('monto', '<', 0)
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
        ?string $notas = null
    ): int {
        $notasFin = $notas ?? 'Liquidación procesada.';
        $comisiones = DB::transaction(function () use (
            $idVendedor,
            $comisionesIds,
            $metodoPago,
            $referenciaPago,
            $comprobantePath,
            $notasFin
        ) {
            $pendientesQuery = ComisionVendedor::where('id_vendedor', $idVendedor)
                ->where('estado', 'Pendiente')
                ->where('monto', '>=', 0);

            if (!empty($comisionesIds)) {
                $pendientesQuery->whereIn('id', $comisionesIds);
            }

            $pendientes = $pendientesQuery->lockForUpdate()->get();
            $ajustesNegativos = ComisionVendedor::where('id_vendedor', $idVendedor)
                ->whereIn('estado', ['Cancelada', 'Pendiente'])
                ->where('monto', '<', 0)
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
}
