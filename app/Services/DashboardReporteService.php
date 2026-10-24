<?php

namespace App\Services;

use App\Models\ComisionVendedor;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Devolucion;
use App\Models\MovimientoBodega;
use App\Models\Producto;
use App\Models\Salida;
use App\Models\User;
use App\Models\Variante;
use App\Models\Venta;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardReporteService
{
    /**
     * Resuelve el rango de fechas a partir de filtros o defaults inteligentes.
     */
    public function resolverRangoFechas(array $filtros = []): array
    {
        $preset = $filtros['preset'] ?? null;
        $hoy = now();

        if ($preset) {
            switch ($preset) {
                case 'hoy':
                    return [
                        'desde' => $hoy->toDateString(),
                        'hasta' => $hoy->toDateString(),
                        'preset' => 'hoy',
                    ];
                case 'ultimos_7_dias':
                    return [
                        'desde' => $hoy->copy()->subDays(6)->toDateString(),
                        'hasta' => $hoy->toDateString(),
                        'preset' => 'ultimos_7_dias',
                    ];
                case 'este_mes':
                    return [
                        'desde' => $hoy->copy()->startOfMonth()->toDateString(),
                        'hasta' => $hoy->toDateString(),
                        'preset' => 'este_mes',
                    ];
                case 'mes_anterior':
                    $mesPasado = $hoy->copy()->subMonth();
                    return [
                        'desde' => $mesPasado->copy()->startOfMonth()->toDateString(),
                        'hasta' => $mesPasado->copy()->endOfMonth()->toDateString(),
                        'preset' => 'mes_anterior',
                    ];
                case 'todo':
                    $minFecha = Venta::min('fecha');
                    return [
                        'desde' => $minFecha ? Carbon::parse($minFecha)->toDateString() : $hoy->copy()->subDays(60)->toDateString(),
                        'hasta' => $hoy->toDateString(),
                        'preset' => 'todo',
                    ];
            }
        }

        // Si se especificaron fechas manuales
        if (!empty($filtros['desde']) || !empty($filtros['hasta'])) {
            $hasta = !empty($filtros['hasta']) ? $filtros['hasta'] : $hoy->toDateString();
            $desde = !empty($filtros['desde']) ? $filtros['desde'] : Carbon::parse($hasta)->subDays(30)->toDateString();
            return [
                'desde' => $desde,
                'hasta' => $hasta,
                'preset' => 'personalizado',
            ];
        }

        // Default inteligente: si este mes tiene ventas, usamos el inicio del mes; si no, últimos 45 días
        $hayVentasEsteMes = Venta::whereIn('estado', ['Entregada', 'Entregado'])
            ->whereDate('fecha', '>=', $hoy->copy()->startOfMonth()->toDateString())
            ->exists();

        $desde = $hayVentasEsteMes
            ? $hoy->copy()->startOfMonth()->toDateString()
            : $hoy->copy()->subDays(45)->toDateString();

        return [
            'desde' => $desde,
            'hasta' => $hoy->toDateString(),
            'preset' => $hayVentasEsteMes ? 'este_mes' : 'ultimos_45_dias',
        ];
    }

    /**
     * Calcula los 4 KPIs estratégicos administrativos sin problemas N+1.
     */
    public function getKpis(array $filtros): array
    {
        $desde = $filtros['desde'];
        $hasta = $filtros['hasta'];
        $vendedorId = $filtros['vendedor_id'] ?? null;
        $productoId = $filtros['producto_id'] ?? null;

        // ── 1. KPI INGRESOS ───────────────────────────────────────────
        $ventasQuery = Venta::query()
            ->whereIn('estado', ['Entregada', 'Entregado', 'Devolución', 'Cambio'])
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->when($vendedorId, fn($q) => $q->where('id_usuario', $vendedorId))
            ->when($productoId, function ($q) use ($productoId) {
                $q->whereHas('detalles.variante', fn($sub) => $sub->where('id_producto', $productoId));
            });

        $totalIngresos = (float) (clone $ventasQuery)->sum('total');
        $totalVentasCount = (int) (clone $ventasQuery)->count();
        $totalEnviosMonto = (float) (clone $ventasQuery)->sum('precio_envio');
        $ticketPromedio = $totalVentasCount > 0 ? round($totalIngresos / $totalVentasCount, 2) : 0.0;

        // ── 2. KPI COSTOS DE INVENTARIO ──────────────────────────────
        // 2a. Costo de Ventas (COGS) en el periodo analizado
        $salidasQuery = Salida::query()
            ->whereIn('estado', ['Entregada', 'Entregado', 'Devolución', 'Cambio'])
            ->whereDate('fecha_salida', '>=', $desde)
            ->whereDate('fecha_salida', '<=', $hasta)
            ->when($vendedorId, fn($q) => $q->where('id_usuario', $vendedorId))
            ->when($productoId, function ($q) use ($productoId) {
                $q->whereHas('variante', fn($sub) => $sub->where('id_producto', $productoId));
            });

        $costoMercanciaVendida = (float) (clone $salidasQuery)->sum('costo_total_aplicado');
        $unidadesVendidas = (int) (clone $salidasQuery)->sum('cantidad');

        // 2b. Valoración Total de Inventario Activo (Stock Físico + Reserva de Almacén)
        $variantesActivas = Variante::where('estado', 1)
            ->when($productoId, fn($q) => $q->where('id_producto', $productoId));

        $valoracionTienda = (float) (clone $variantesActivas)->selectRaw('COALESCE(SUM(stock * costo_promedio), 0) as val')->value('val');
        $valoracionBodega = (float) (clone $variantesActivas)->selectRaw('COALESCE(SUM(reserva * costo_promedio), 0) as val')->value('val');
        $valoracionTotalStock = round($valoracionTienda + $valoracionBodega, 2);
        $totalUnidadesStock = (int) (clone $variantesActivas)->selectRaw('COALESCE(SUM(stock + reserva), 0) as total')->value('total');

        // ── 3. KPI GANANCIAS NETAS ───────────────────────────────────
        // Comisiones generadas en el periodo por las ventas concretadas
        $comisionesGeneradasPeriodo = (float) (clone $salidasQuery)->sum('comision_aplicada');
        $gananciaNeta = round($totalIngresos - $costoMercanciaVendida - $comisionesGeneradasPeriodo, 2);
        $margenNetoPorcentaje = $totalIngresos > 0 ? round(($gananciaNeta / $totalIngresos) * 100, 1) : 0.0;

        // ── 4. KPI COMISIONES POR PAGAR ──────────────────────────────
        // Total acumulado pendiente de liquidación según la regla estricta del módulo comisiones
        $comisionesBaseQuery = ComisionVendedor::query()
            ->when($vendedorId, fn($q) => $q->where('id_vendedor', $vendedorId));

        $comisionesPendientes = max(0, (float) (clone $comisionesBaseQuery)
            ->where('estado', 'Pendiente')
            ->where('monto', '>=', 0)
            ->sum('monto')
            + (clone $comisionesBaseQuery)
            ->whereIn('estado', ['Cancelada', 'Pendiente'])
            ->where('monto', '<', 0)
            ->sum('monto')
        );

        $conteoComisionesPendientes = (clone $comisionesBaseQuery)
            ->where('estado', 'Pendiente')
            ->where('monto', '>=', 0)
            ->count();

        $comisionesPagadasHistoricas = (float) (clone $comisionesBaseQuery)
            ->where('estado', 'Pagada')
            ->sum('monto');

        return [
            'ingresos' => [
                'total' => $totalIngresos,
                'ventas_count' => $totalVentasCount,
                'ticket_promedio' => $ticketPromedio,
                'total_envios' => $totalEnviosMonto,
            ],
            'costos' => [
                'cogs_periodo' => $costoMercanciaVendida,
                'unidades_vendidas' => $unidadesVendidas,
                'valoracion_inventario' => $valoracionTotalStock,
                'valoracion_tienda' => $valoracionTienda,
                'valoracion_bodega' => $valoracionBodega,
                'stock_unidades_total' => $totalUnidadesStock,
            ],
            'ganancias' => [
                'neta' => $gananciaNeta,
                'margen_porcentaje' => $margenNetoPorcentaje,
                'comisiones_periodo' => $comisionesGeneradasPeriodo,
            ],
            'comisiones_por_pagar' => [
                'total_pendiente' => $comisionesPendientes,
                'conteo_pendientes' => $conteoComisionesPendientes,
                'total_pagadas' => $comisionesPagadasHistoricas,
            ],
        ];
    }

    /**
     * Alimenta el gráfico principal comparativo: Ingresos vs Costos vs Ganancias Netas.
     */
    public function getGraficoComparativaFinanciera(array $filtros): array
    {
        $desde = $filtros['desde'];
        $hasta = $filtros['hasta'];
        $vendedorId = $filtros['vendedor_id'] ?? null;
        $productoId = $filtros['producto_id'] ?? null;

        // 1. Ingresos diarios agrupados
        $ingresosPorDia = Venta::query()
            ->whereIn('estado', ['Entregada', 'Entregado', 'Devolución', 'Cambio'])
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->when($vendedorId, fn($q) => $q->where('id_usuario', $vendedorId))
            ->when($productoId, function ($q) use ($productoId) {
                $q->whereHas('detalles.variante', fn($sub) => $sub->where('id_producto', $productoId));
            })
            ->selectRaw('DATE(fecha) as dia, SUM(total) as monto')
            ->groupBy(DB::raw('DATE(fecha)'))
            ->pluck('monto', 'dia')
            ->all();

        // 2. Costos y comisiones diarias agrupadas desde salidas
        $salidasPorDia = Salida::query()
            ->whereIn('estado', ['Entregada', 'Entregado', 'Devolución', 'Cambio'])
            ->whereDate('fecha_salida', '>=', $desde)
            ->whereDate('fecha_salida', '<=', $hasta)
            ->when($vendedorId, fn($q) => $q->where('id_usuario', $vendedorId))
            ->when($productoId, function ($q) use ($productoId) {
                $q->whereHas('variante', fn($sub) => $sub->where('id_producto', $productoId));
            })
            ->selectRaw('DATE(fecha_salida) as dia, SUM(costo_total_aplicado) as costo, SUM(comision_aplicada) as comision')
            ->groupBy(DB::raw('DATE(fecha_salida)'))
            ->get()
            ->keyBy('dia');

        // 3. Construir serie cronológica continua completa
        $fechaInicio = Carbon::parse($desde);
        $fechaFin = Carbon::parse($hasta);
        $diasDiferencia = $fechaInicio->diffInDays($fechaFin);

        $labels = [];
        $serieIngresos = [];
        $serieCostos = [];
        $serieGanancias = [];

        // Si el periodo es de hasta 90 días, mostramos por día; si es mayor, por día o mes
        for ($i = 0; $i <= $diasDiferencia; $i++) {
            $currentDate = $fechaInicio->copy()->addDays($i);
            $diaStr = $currentDate->toDateString();
            $labelStr = $currentDate->format('d/m');

            $ingreso = (float) ($ingresosPorDia[$diaStr] ?? 0);
            $salidaData = $salidasPorDia->get($diaStr);
            $costo = (float) ($salidaData?->costo ?? 0);
            $comision = (float) ($salidaData?->comision ?? 0);
            $ganancia = round($ingreso - $costo - $comision, 2);

            $labels[] = $labelStr;
            $serieIngresos[] = round($ingreso, 2);
            $serieCostos[] = round($costo, 2);
            $serieGanancias[] = round($ganancia, 2);
        }

        return [
            'labels' => $labels,
            'ingresos' => $serieIngresos,
            'costos' => $serieCostos,
            'ganancias' => $serieGanancias,
        ];
    }

    /**
     * Alimenta el gráfico secundario de comportamiento de movimientos de inventario y comisiones.
     */
    public function getGraficoMovimientosYComisiones(array $filtros): array
    {
        $desde = $filtros['desde'];
        $hasta = $filtros['hasta'];
        $vendedorId = $filtros['vendedor_id'] ?? null;

        // Entradas físicas a bodega (compras o transferencias de entrada)
        $entradasPorDia = MovimientoBodega::query()
            ->whereIn('tipo', ['compra_recibida', 'Entrada', 'entrada'])
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->selectRaw('DATE(created_at) as dia, SUM(cantidad) as unidades')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('unidades', 'dia')
            ->all();

        // Salidas de inventario despachadas
        $salidasPorDia = Salida::query()
            ->whereIn('estado', ['Entregada', 'Entregado', 'Devolución', 'Cambio'])
            ->whereDate('fecha_salida', '>=', $desde)
            ->whereDate('fecha_salida', '<=', $hasta)
            ->when($vendedorId, fn($q) => $q->where('id_usuario', $vendedorId))
            ->selectRaw('DATE(fecha_salida) as dia, SUM(cantidad) as unidades, SUM(comision_aplicada) as comisiones')
            ->groupBy(DB::raw('DATE(fecha_salida)'))
            ->get()
            ->keyBy('dia');

        $fechaInicio = Carbon::parse($desde);
        $fechaFin = Carbon::parse($hasta);
        $diasDiferencia = $fechaInicio->diffInDays($fechaFin);

        $labels = [];
        $serieEntradas = [];
        $serieSalidas = [];
        $serieComisiones = [];

        for ($i = 0; $i <= $diasDiferencia; $i++) {
            $currentDate = $fechaInicio->copy()->addDays($i);
            $diaStr = $currentDate->toDateString();
            $labelStr = $currentDate->format('d/m');

            $entradas = (int) ($entradasPorDia[$diaStr] ?? 0);
            $salidaData = $salidasPorDia->get($diaStr);
            $salidas = (int) ($salidaData?->unidades ?? 0);
            $comisiones = (float) ($salidaData?->comisiones ?? 0);

            $labels[] = $labelStr;
            $serieEntradas[] = $entradas;
            $serieSalidas[] = $salidas;
            $serieComisiones[] = round($comisiones, 2);
        }

        return [
            'labels' => $labels,
            'entradas' => $serieEntradas,
            'salidas' => $serieSalidas,
            'comisiones' => $serieComisiones,
        ];
    }

    /**
     * Construye el informe de movimientos con soporte para vista y exportación / impresión limpia.
     * Consolida de forma unificada:
     * - Ventas / Salidas de stock
     * - Compras recibidas / Entradas a bodega
     * - Transferencias a tienda
     * - Devoluciones
     * - Comisiones liquidadas / generadas
     */
    public function getReporteMovimientos(array $filtros, int $perPage = 25): array
    {
        $desde = $filtros['desde'];
        $hasta = $filtros['hasta'];
        $tipoMovimiento = $filtros['tipo'] ?? 'todos';
        $vendedorId = $filtros['vendedor_id'] ?? null;
        $productoId = $filtros['producto_id'] ?? null;
        $busqueda = trim($filtros['q'] ?? '');

        // 1. Colección Unificada de Movimientos
        $movimientos = collect();

        // ── A. SALIDAS POR VENTA ───────────────────────────────────────────
        if (in_array($tipoMovimiento, ['todos', 'ventas', 'salidas'])) {
            $querySalidas = Salida::with(['variante.producto', 'variante.imagenes', 'usuario'])
                ->whereDate('fecha_salida', '>=', $desde)
                ->whereDate('fecha_salida', '<=', $hasta)
                ->when($vendedorId, fn($q) => $q->where('id_usuario', $vendedorId))
                ->when($productoId, function ($q) use ($productoId) {
                    $q->whereHas('variante', fn($sub) => $sub->where('id_producto', $productoId));
                });

            if (!empty($busqueda)) {
                $querySalidas->where(function ($q) use ($busqueda) {
                    $q->where('nombre_cliente', 'like', "%{$busqueda}%")
                        ->orWhere('observaciones', 'like', "%{$busqueda}%")
                        ->orWhere('telefono', 'like', "%{$busqueda}%")
                        ->orWhereHas('variante', function ($vq) use ($busqueda) {
                            $vq->where('sku', 'like', "%{$busqueda}%")
                                ->orWhere('nombre_variante', 'like', "%{$busqueda}%")
                                ->orWhereHas('producto', fn($pq) => $pq->where('nombre', 'like', "%{$busqueda}%"));
                        });
                });
            }

            foreach ($querySalidas->get() as $salida) {
                $ventaFolio = $salida->venta_id ? 'VNT-' . str_pad($salida->venta_id, 5, '0', STR_PAD_LEFT) : 'SAL-' . str_pad($salida->id, 5, '0', STR_PAD_LEFT);
                $margenLinea = (float) $salida->total - (float) $salida->costo_total_aplicado - (float) $salida->comision_aplicada;

                $movimientos->push([
                    'id' => 'salida_' . $salida->id,
                    'fecha' => $salida->fecha_salida ? Carbon::parse($salida->fecha_salida)->format('Y-m-d') . ' ' . ($salida->hora_salida ?? '00:00:00') : $salida->created_at->format('Y-m-d H:i:s'),
                    'fecha_formateada' => $salida->fecha_salida ? Carbon::parse($salida->fecha_salida)->format('d/m/Y') : $salida->created_at->format('d/m/Y'),
                    'hora_formateada' => $salida->hora_salida ? substr($salida->hora_salida, 0, 5) : $salida->created_at->format('H:i'),
                    'categoria' => 'Venta / Salida',
                    'tipo_badge' => 'rose',
                    'folio' => $ventaFolio,
                    'descripcion' => ($salida->variante?->producto?->nombre ?? 'Producto') . ' - ' . ($salida->variante?->nombre_variante ?? 'Variante'),
                    'sku' => $salida->variante?->sku ?? 'Sin SKU',
                    'cantidad' => (int) $salida->cantidad,
                    'signo_stock' => '-',
                    'costo_unitario' => (float) ($salida->variante?->costo_promedio ?? 0),
                    'costo_total' => (float) $salida->costo_total_aplicado,
                    'ingreso_total' => (float) $salida->total,
                    'comision' => (float) $salida->comision_aplicada,
                    'ganancia_neta' => round($margenLinea, 2),
                    'usuario_responsable' => $salida->usuario?->nombre_real ?? 'Sistema',
                    'estado' => $salida->estado,
                    'origen_destino' => $salida->nombre_cliente ? "Cliente: {$salida->nombre_cliente}" : ($salida->direccion ?: 'Venta Mostrador'),
                ]);
            }
        }

        // ── B. ENTRADAS / COMPRAS Y MOVIMIENTOS DE BODEGA ─────────────────
        if (in_array($tipoMovimiento, ['todos', 'compras', 'entradas', 'transferencias', 'devoluciones'])) {
            $queryBodega = MovimientoBodega::with(['variante.producto', 'compra.proveedor', 'devolucion'])
                ->whereDate('created_at', '>=', $desde)
                ->whereDate('created_at', '<=', $hasta)
                ->when($productoId, function ($q) use ($productoId) {
                    $q->whereHas('variante', fn($sub) => $sub->where('id_producto', $productoId));
                });

            if ($tipoMovimiento === 'compras') {
                $queryBodega->where('tipo', 'compra_recibida');
            } elseif ($tipoMovimiento === 'transferencias') {
                $queryBodega->where('tipo', 'transferencia_tienda');
            } elseif ($tipoMovimiento === 'devoluciones') {
                $queryBodega->where(function ($sub) {
                    $sub->whereNotNull('devolucion_id')
                        ->orWhereIn('tipo', ['devolucion_cliente', 'devolucion_proveedor']);
                });
            }

            if (!empty($busqueda)) {
                $queryBodega->where(function ($q) use ($busqueda) {
                    $q->where('observacion', 'like', "%{$busqueda}%")
                        ->orWhereHas('variante', function ($vq) use ($busqueda) {
                            $vq->where('sku', 'like', "%{$busqueda}%")
                                ->orWhere('nombre_variante', 'like', "%{$busqueda}%")
                                ->orWhereHas('producto', fn($pq) => $pq->where('nombre', 'like', "%{$busqueda}%"));
                        })
                        ->orWhereHas('compra.proveedor', fn($pq) => $pq->where('nombre', 'like', "%{$busqueda}%"));
                });
            }

            foreach ($queryBodega->get() as $mov) {
                $costoUnitario = (float) ($mov->variante?->costo_promedio ?? 0);
                $costoTotal = round($costoUnitario * (int) $mov->cantidad, 2);

                $categoriaLabel = match ($mov->tipo) {
                    'compra_recibida' => 'Compra / Entrada',
                    'transferencia_tienda' => 'Transferencia a Tienda',
                    'devolucion_cliente' => 'Devolución Cliente',
                    'devolucion_proveedor' => 'Devolución Proveedor',
                    default => ucfirst(str_replace('_', ' ', $mov->tipo)),
                };

                $badgeColor = match ($mov->tipo) {
                    'compra_recibida' => 'emerald',
                    'transferencia_tienda' => 'amber',
                    'devolucion_cliente', 'devolucion_proveedor' => 'purple',
                    default => 'slate',
                };

                $folio = $mov->devolucion_id
                    ? 'DEV-' . str_pad($mov->devolucion_id, 5, '0', STR_PAD_LEFT)
                    : ($mov->id_compra ? 'CMP-' . str_pad($mov->id_compra, 5, '0', STR_PAD_LEFT) : 'MOV-' . str_pad($mov->id, 5, '0', STR_PAD_LEFT));

                $signo = in_array($mov->tipo, ['compra_recibida', 'devolucion_cliente', 'Entrada', 'entrada']) ? '+' : '↔';

                $movimientos->push([
                    'id' => 'bodega_' . $mov->id,
                    'fecha' => $mov->created_at->format('Y-m-d H:i:s'),
                    'fecha_formateada' => $mov->created_at->format('d/m/Y'),
                    'hora_formateada' => $mov->created_at->format('H:i'),
                    'categoria' => $categoriaLabel,
                    'tipo_badge' => $badgeColor,
                    'folio' => $folio,
                    'descripcion' => ($mov->variante?->producto?->nombre ?? 'Producto') . ' - ' . ($mov->variante?->nombre_variante ?? 'Variante'),
                    'sku' => $mov->variante?->sku ?? 'Sin SKU',
                    'cantidad' => (int) $mov->cantidad,
                    'signo_stock' => $signo,
                    'costo_unitario' => $costoUnitario,
                    'costo_total' => $costoTotal,
                    'ingreso_total' => 0.00,
                    'comision' => 0.00,
                    'ganancia_neta' => 0.00,
                    'usuario_responsable' => 'Admin Almacén',
                    'estado' => 'Completado',
                    'origen_destino' => $mov->compra?->proveedor?->nombre ? "Proveedor: {$mov->compra->proveedor->nombre}" : ($mov->observacion ?: 'Inventario Interno'),
                ]);
            }
        }

        // ── C. COMISIONES DE VENDEDORES (si se filtra por comisiones) ─────
        if (in_array($tipoMovimiento, ['todos', 'comisiones'])) {
            $queryComisiones = ComisionVendedor::with(['vendedor', 'salida.variante.producto'])
                ->whereDate('fecha_registro', '>=', $desde)
                ->whereDate('fecha_registro', '<=', $hasta)
                ->when($vendedorId, fn($q) => $q->where('id_vendedor', $vendedorId));

            if (!empty($busqueda)) {
                $queryComisiones->where(function ($q) use ($busqueda) {
                    $q->where('concepto', 'like', "%{$busqueda}%")
                        ->orWhere('referencia_pago', 'like', "%{$busqueda}%")
                        ->orWhereHas('vendedor', fn($vq) => $vq->where('nombre_real', 'like', "%{$busqueda}%"));
                });
            }

            foreach ($queryComisiones->get() as $com) {
                // Solo agregar comisiones directas si no están ya representadas o para el reporte específico de comisiones
                if ($tipoMovimiento === 'comisiones') {
                    $movimientos->push([
                        'id' => 'comision_' . $com->id,
                        'fecha' => $com->fecha_registro ? $com->fecha_registro->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                        'fecha_formateada' => $com->fecha_registro ? $com->fecha_registro->format('d/m/Y') : now()->format('d/m/Y'),
                        'hora_formateada' => $com->fecha_registro ? $com->fecha_registro->format('H:i') : '--:--',
                        'categoria' => 'Comisión Vendedor',
                        'tipo_badge' => 'indigo',
                        'folio' => 'COM-' . str_pad($com->id, 5, '0', STR_PAD_LEFT),
                        'descripcion' => $com->concepto ?: 'Comisión de Venta',
                        'sku' => $com->salida?->variante?->sku ?? 'N/A',
                        'cantidad' => 1,
                        'signo_stock' => '•',
                        'costo_unitario' => 0.00,
                        'costo_total' => 0.00,
                        'ingreso_total' => 0.00,
                        'comision' => (float) $com->monto,
                        'ganancia_neta' => -((float) $com->monto),
                        'usuario_responsable' => $com->vendedor?->nombre_real ?? 'Vendedor',
                        'estado' => $com->estado,
                        'origen_destino' => $com->metodo_pago ? "Pago: {$com->metodo_pago}" : "Estado: {$com->estado}",
                    ]);
                }
            }
        }

        // Ordenar cronológicamente descendente
        $movimientosOrdenados = $movimientos->sortByDesc('fecha')->values();

        // Totales consolidados para la fila final de Totales / Resumen
        $totales = [
            'cantidad_movimientos' => $movimientosOrdenados->count(),
            'total_unidades' => (int) $movimientosOrdenados->sum('cantidad'),
            'total_costos' => (float) $movimientosOrdenados->sum('costo_total'),
            'total_ingresos' => (float) $movimientosOrdenados->sum('ingreso_total'),
            'total_comisiones' => (float) $movimientosOrdenados->sum('comision'),
            'total_ganancias' => (float) $movimientosOrdenados->sum('ganancia_neta'),
        ];

        // Paginación manual para colección de movimientos combinados
        $page = request()->get('page', 1);
        $paginados = new LengthAwarePaginator(
            $movimientosOrdenados->forPage($page, $perPage),
            $movimientosOrdenados->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return [
            'paginados' => $paginados,
            'todos_movimientos' => $movimientosOrdenados, // Para impresión o exportación completa
            'totales' => $totales,
        ];
    }
}
