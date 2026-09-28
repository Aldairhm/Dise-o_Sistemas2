<?php

namespace App\Services;

use App\Models\ComisionVendedor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComisionService
{
    /**
     * Resuelve el % de comisión a aplicar para una salida dada.
     *
     * Cascada: comision de variante → porcentaje_comision del vendedor → global config
     *
     * @param  \App\Models\User       $vendedor
     * @param  mixed                  $variante   Objeto con propiedad "comision" (puede ser null)
     * @return float
     */
    public function resolverPorcentaje(User $vendedor, $variante = null): float
    {
        $fuente = config('comisiones.fuente', 'cascada');

        if ($fuente === 'global') {
            return (float) config('comisiones.porcentaje_global', 5.0);
        }

        if ($fuente === 'vendedor') {
            return (float) ($vendedor->porcentaje_comision ?? config('comisiones.porcentaje_global', 5.0));
        }

        // cascada (default)
        if ($variante) {
            if ((float) ($variante->comision ?? 0) > 0) {
                return (float) $variante->comision;
            }
            if (!empty($variante->producto) && (float) ($variante->producto->comision ?? 0) > 0) {
                return (float) $variante->producto->comision;
            }
        }

        if (!is_null($vendedor->porcentaje_comision) && (float) $vendedor->porcentaje_comision > 0) {
            return (float) $vendedor->porcentaje_comision;
        }

        return (float) config('comisiones.porcentaje_global', 5.0);
    }

    /**
     * Calcula el monto de comisión.
     *
     * @param  float $totalVenta
     * @param  float $porcentaje
     * @return float
     */
    public function calcularMonto(float $totalVenta, float $porcentaje): float
    {
        return round($totalVenta * ($porcentaje / 100), 2);
    }

    /**
     * Crea un registro de comisión para una salida.
     * Si ya existe una comisión para esa salida, no crea duplicado.
     *
     * @param  int   $idSalida
     * @param  int   $idVendedor
     * @param  float $totalVenta
     * @param  float $porcentaje
     * @return \App\Models\ComisionVendedor|null
     */
    public function crearParaSalida(int $idSalida, int $idVendedor, float $totalVenta, float $porcentaje): ?ComisionVendedor
    {
        // Anti-duplicado
        if (ComisionVendedor::where('id_salida', $idSalida)->exists()) {
            return null;
        }

        $monto = $this->calcularMonto($totalVenta, $porcentaje);

        return ComisionVendedor::create([
            'id_salida'      => $idSalida,
            'id_vendedor'    => $idVendedor,
            'monto'          => $monto,
            'porcentaje'     => $porcentaje,
            'estado'         => 'Pendiente',
            'fecha_registro' => now(),
        ]);
    }

    /**
     * Cancela la comisión asociada a una salida (ej: devolución/anulación).
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
     * Liquidar en lote: marca como Pagadas todas las comisiones Pendiente
     * de un vendedor, opcionalmente hasta una fecha límite.
     *
     * @param  int         $idVendedor
     * @param  string|null $hastaFecha   formato Y-m-d
     * @param  string|null $notas
     * @return int  Cantidad de comisiones liquidadas
     */
    public function liquidarVendedor(int $idVendedor, ?string $hastaFecha = null, ?string $notas = null): int
    {
        $query = ComisionVendedor::where('id_vendedor', $idVendedor)
            ->where('estado', 'Pendiente');

        if ($hastaFecha) {
            $query->whereDate('fecha_registro', '<=', $hastaFecha);
        }

        $comisiones = $query->get();

        if ($comisiones->isEmpty()) {
            return 0;
        }

        $adminId  = Auth::id();
        $ahora    = now();
        $notasFin = $notas ?? 'Liquidación en lote.';

        foreach ($comisiones as $comision) {
            $comision->update([
                'estado'             => 'Pagada',
                'liquidado_por'      => $adminId,
                'fecha_liquidacion'  => $ahora,
                'notas'              => $notasFin,
            ]);
        }

        return $comisiones->count();
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
