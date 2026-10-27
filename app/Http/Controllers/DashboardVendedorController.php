<?php

namespace App\Http\Controllers;

use App\Models\ComisionVendedor;
use App\Models\Salida;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardVendedorController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $periodo = $request->input('periodo', 'mes'); // hoy | semana | mes | todo

        // ── 1. Rango de fechas según periodo ────────────────────────────────────
        [$desde, $hasta, $desdePrev, $hastaPrev] = $this->resolverRango($periodo);

        // ── 2. Obtener ventas del periodo actual ─────────────────────────────────
        $ventasPeriodo = Venta::where('id_usuario', $user->id)
            ->with(['detalles.variante.producto'])
            ->when($desde, fn($q) => $q->where('fecha', '>=', $desde))
            ->when($hasta, fn($q) => $q->where('fecha', '<=', $hasta))
            ->orderByDesc('fecha')
            ->get();

        // ── 3. Precargar salidas y comisiones del vendedor para mapeo exacto ───
        $salidasUsuario = Salida::where('id_usuario', $user->id)->get();
        $salidasMap = [];
        foreach ($salidasUsuario as $s) {
            if ($s->venta_id) {
                $salidasMap[$s->venta_id][] = $s;
            }
        }

        $comisionesUsuario = ComisionVendedor::where('id_vendedor', $user->id)->get();
        $comisionesMap = [];
        $bonosColeccion = collect();

        foreach ($comisionesUsuario as $c) {
            if (is_null($c->id_salida) && (float) $c->monto > 0) {
                // Bono directo
                $bonosColeccion->push($c);
            } else {
                // Comisión vinculada a salida / venta
                $vId = null;
                if ($c->id_salida) {
                    $salidaEncontrada = $salidasUsuario->firstWhere('id', $c->id_salida);
                    $vId = $salidaEncontrada?->venta_id;
                }
                if (!$vId && preg_match('/Venta\s*#(\d+)|VNT-(\d+)/i', $c->concepto ?? '', $m)) {
                    $vId = (int) ($m[1] ?: $m[2]);
                }
                if ($vId) {
                    $comisionesMap[$vId][] = $c;
                }
            }
        }

        // ── 4. Transformar cada venta ligando su comisión y cálculo derivado ──
        $ventasTransformadas = $ventasPeriodo->map(function ($venta) use ($comisionesMap, $salidasMap) {
            return $this->formatearVentaConComision(
                $venta,
                $comisionesMap[$venta->id] ?? [],
                $salidasMap[$venta->id] ?? []
            );
        });

        // ── 5. Transformar Bonos ────────────────────────────────────────────────
        $bonosTransformados = $bonosColeccion->map(function ($bono) {
            $estadoKey = $bono->estado === 'Pagada' ? 'pagado' : 'pendiente';
            return [
                'id'          => $bono->id,
                'motivo'      => $bono->concepto ?: 'Bono directo',
                'monto'       => (float) $bono->monto,
                'estado'      => $estadoKey,
                'estadoLabel' => $bono->estado,
                'fecha'       => $bono->fecha_registro ? $bono->fecha_registro->format('d/m/Y') : '—',
                'fecha_raw'   => $bono->fecha_registro ? $bono->fecha_registro->format('Y-m-d') : null,
            ];
        });

        // ── 6. Resumen financiero derivado de las ventas y bonos ──────────────
        $resumen = $this->calcularResumenDerivado($ventasTransformadas, $bonosTransformados);

        // ── 7. Resumen de ventas (franja compacta) ─────────────────────────────
        $ventasStats = [
            'total_monto'      => (float) $ventasTransformadas->sum('monto'),
            'total_ordenes'    => $ventasTransformadas->count(),
            'entregadas'       => $ventasTransformadas->where('estadoVenta', 'entregada')->count(),
            'monto_entregadas' => (float) $ventasTransformadas->where('estadoVenta', 'entregada')->sum('monto'),
            'en_proceso'       => $ventasTransformadas->whereIn('estadoVenta', ['pendiente', 'confirmada', 'en_ruta'])->count(),
            'pendientes'       => $ventasTransformadas->where('estadoVenta', 'pendiente')->count(),
            'confirmadas'      => $ventasTransformadas->where('estadoVenta', 'confirmada')->count(),
            'en_ruta'          => $ventasTransformadas->where('estadoVenta', 'en_ruta')->count(),
            'canceladas'       => $ventasTransformadas->whereIn('estadoVenta', ['cancelada', 'devolucion'])->count(),
            'devoluciones'     => $ventasTransformadas->where('estadoVenta', 'devolucion')->count(),
        ];

        // ── 8. Variación vs periodo anterior ────────────────────────────────────
        $ventasPrevQuery = Venta::where('id_usuario', $user->id)
            ->when($desdePrev, fn($q) => $q->where('fecha', '>=', $desdePrev))
            ->when($hastaPrev, fn($q) => $q->where('fecha', '<=', $hastaPrev));

        $montoPrev = (float) $ventasPrevQuery->sum('total');
        $variacion = $montoPrev > 0
            ? round((($ventasStats['total_monto'] - $montoPrev) / $montoPrev) * 100, 1)
            : ($ventasStats['total_monto'] > 0 ? 100 : 0);

        // ── 9. Meta mensual ─────────────────────────────────────────────────────
        $metaMensual = 500.00;
        $progresoMeta = $metaMensual > 0
            ? min(100, round(($resumen['total_ganado'] / $metaMensual) * 100, 1))
            : 0;

        // ── 10. Datos para gráfica de comisiones por día (últimos 14 días) ─────
        $chartDias = $this->generarGraficaComisiones($user->id, $comisionesMap, $salidasMap);

        // ── 11. Respuesta AJAX o Vista ─────────────────────────────────────────
        if ($request->ajax()) {
            return response()->json([
                'ventas'        => $ventasTransformadas,
                'bonos'         => $bonosTransformados,
                'resumen'       => $resumen,
                'ventasStats'   => $ventasStats,
                'variacion'     => $variacion,
                'metaMensual'   => $metaMensual,
                'progresoMeta'  => $progresoMeta,
                'periodoActual' => $periodo,
            ]);
        }

        return view('vendedor.dashboard', [
            'ventas'        => $ventasTransformadas,
            'bonos'         => $bonosTransformados,
            'resumen'       => $resumen,
            'ventasStats'   => $ventasStats,
            'variacion'     => $variacion,
            'metaMensual'   => $metaMensual,
            'progresoMeta'  => $progresoMeta,
            'chartDias'     => $chartDias,
            'periodoActual' => $periodo,
        ]);
    }

    /**
     * Da formato a una Venta y le asocia su comisión calculada o registrada.
     */
    private function formatearVentaConComision(Venta $venta, array $comisionesRegistradas, array $salidas): array
    {
        $estadoVentaNorm = strtolower(trim($venta->estado ?? 'pendiente'));
        $estadoVentaKey = match ($estadoVentaNorm) {
            'entregada', 'entregado' => 'entregada',
            'confirmada'             => 'confirmada',
            'en ruta', 'en_ruta'     => 'en_ruta',
            'cancelada'              => 'cancelada',
            'devolución', 'devolucion' => 'devolucion',
            'cambio'                 => 'cambio',
            default                  => 'pendiente',
        };

        $montoComision = 0.0;
        $comisionEstado = 'proyectada';
        $detallesCalculo = [];

        if (!empty($comisionesRegistradas)) {
            $pagadas = 0.0;
            $pendientes = 0.0;
            $canceladas = 0.0;
            $deducciones = 0.0;

            foreach ($comisionesRegistradas as $c) {
                $m = (float) $c->monto;
                if ($m < 0) {
                    $deducciones += $m;
                    $detallesCalculo[] = ($c->concepto ?: 'Deducción') . " (-$" . number_format(abs($m), 2) . ")";
                } elseif ($c->estado === 'Pagada') {
                    $pagadas += $m;
                    $detallesCalculo[] = ($c->concepto ?: 'Comisión pagada') . " ($" . number_format($m, 2) . ")";
                } elseif ($c->estado === 'Pendiente') {
                    $pendientes += $m;
                    $detallesCalculo[] = ($c->concepto ?: 'Comisión por cobrar') . " ($" . number_format($m, 2) . ")";
                } elseif ($c->estado === 'Cancelada') {
                    $canceladas += $m;
                    $detallesCalculo[] = ($c->concepto ?: 'Comisión cancelada') . " ($" . number_format($m, 2) . ")";
                }
            }

            if (in_array($estadoVentaKey, ['cancelada', 'devolucion'])) {
                $comisionEstado = 'anulada';
                $montoComision = $canceladas > 0 ? $canceladas : max(0.0, abs($deducciones));
            } elseif ($pendientes > 0) {
                $comisionEstado = 'pendiente_cobro';
                $montoComision = max(0.0, round($pendientes + $deducciones, 2));
            } elseif ($pagadas > 0) {
                $comisionEstado = 'pagada';
                $montoComision = max(0.0, round($pagadas + $deducciones, 2));
            } else {
                $comisionEstado = 'anulada';
                $montoComision = $canceladas;
            }
        } else {
            // Cálculo derivado a partir de los detalles de la venta
            $baseTotal = 0.0;
            if ($venta->detalles && $venta->detalles->isNotEmpty()) {
                foreach ($venta->detalles as $det) {
                    $prodComision = (float) ($det->variante?->producto?->comision ?? 0);
                    $varComision = (float) ($det->variante?->comision ?? 0);
                    $cUnit = $prodComision > 0 ? $prodComision : $varComision;
                    $sub = $cUnit * (int) $det->cantidad;
                    $baseTotal += $sub;
                    $prodNom = $det->variante?->producto?->nombre ?? 'Producto';
                    $detallesCalculo[] = "{$det->cantidad}x {$prodNom} ($" . number_format($cUnit, 2) . " c/u = $" . number_format($sub, 2) . ")";
                }
            }

            // Considerar costo extra si existe en salidas
            $costoExtraTotal = 0.0;
            foreach ($salidas as $s) {
                $costoExtraTotal += (float) ($s->costo_extra ?? 0);
            }
            if ($costoExtraTotal > 0) {
                $baseTotal += $costoExtraTotal;
                $detallesCalculo[] = "Costo extra: +$" . number_format($costoExtraTotal, 2);
            }

            $montoComision = round($baseTotal, 2);

            if (in_array($estadoVentaKey, ['cancelada', 'devolucion'])) {
                $comisionEstado = 'anulada';
            } elseif ($estadoVentaKey === 'entregada') {
                $comisionEstado = 'pendiente_cobro';
            } else {
                $comisionEstado = 'proyectada';
            }
        }

        $montoVenta = (float) $venta->total;
        $porcentaje = ($montoVenta > 0 && $montoComision > 0)
            ? round(($montoComision / $montoVenta) * 100, 1)
            : 0.0;

        $formula = "$" . number_format($montoVenta, 2) . " × " . $porcentaje . "% = $" . number_format($montoComision, 2);

        return [
            'id'               => $venta->id,
            'folio'            => 'VNT-' . str_pad($venta->id, 5, '0', STR_PAD_LEFT),
            'fecha'            => $venta->fecha ? $venta->fecha->format('Y-m-d H:i:s') : null,
            'fecha_formateada' => $venta->fecha ? $venta->fecha->format('d/m/Y H:i') : '—',
            'cliente'          => $venta->nombre_cliente ?: 'Cliente General',
            'monto'            => $montoVenta,
            'estadoVenta'      => $estadoVentaKey,
            'estadoVentaLabel' => $venta->estado ?: 'Pendiente',
            'comision'         => [
                'monto'      => $montoComision,
                'porcentaje' => $porcentaje,
                'estado'     => $comisionEstado, // proyectada | pendiente_cobro | pagada | anulada
                'formula'    => $formula,
                'detalles'   => $detallesCalculo,
            ],
        ];
    }

    /**
     * Calcula el resumen derivado a partir de las ventas y bonos.
     * Reglas estrictas:
     * - Total ganado = comisiones (pendiente_cobro + pagada) + bonos (pendiente + pagado)
     * - Pendiente de cobro = comisiones pendiente_cobro + bonos pendientes
     * - Pagada (Cobrado) = comisiones pagadas + bonos pagados
     * - Por ganar (proyectada) = comisiones de ventas en proceso (proyectadas)
     * - Anulada = comisiones de ventas canceladas o devueltas
     */
    private function calcularResumenDerivado($ventas, $bonos): array
    {
        $comisionesPagadas = 0.0;
        $comisionesPendientes = 0.0;
        $comisionesProyectadas = 0.0;
        $comisionesAnuladas = 0.0;

        foreach ($ventas as $v) {
            $c = $v['comision'];
            $m = (float) $c['monto'];
            match ($c['estado']) {
                'pagada'          => $comisionesPagadas += $m,
                'pendiente_cobro' => $comisionesPendientes += $m,
                'proyectada'      => $comisionesProyectadas += $m,
                'anulada'         => $comisionesAnuladas += $m,
                default           => null,
            };
        }

        $bonosPagados = 0.0;
        $bonosPendientes = 0.0;
        foreach ($bonos as $b) {
            $m = (float) $b['monto'];
            if ($b['estado'] === 'pagado') {
                $bonosPagados += $m;
            } else {
                $bonosPendientes += $m;
            }
        }

        $cobrado = round($comisionesPagadas + $bonosPagados, 2);
        $pendienteCobro = round($comisionesPendientes + $bonosPendientes, 2);
        $totalGanado = round($cobrado + $pendienteCobro, 2);
        $proyectada = round($comisionesProyectadas, 2);
        $anulada = round($comisionesAnuladas, 2);

        return [
            'total_ganado'          => $totalGanado,
            'cobrado'               => $cobrado,
            'pendiente_cobro'       => $pendienteCobro,
            'proyectada'            => $proyectada,
            'anulada'               => $anulada,
            'comisiones_pagadas'    => round($comisionesPagadas, 2),
            'comisiones_pendientes' => round($comisionesPendientes, 2),
            'bonos_pagados'         => round($bonosPagados, 2),
            'bonos_pendientes'      => round($bonosPendientes, 2),
            'bonos_total'           => round($bonosPagados + $bonosPendientes, 2),
        ];
    }

    /**
     * Genera datos de comisiones diarias de los últimos 14 días para la gráfica.
     */
    private function generarGraficaComisiones(int $userId, array $comisionesMap, array $salidasMap): array
    {
        $ventasUltimos14 = Venta::where('id_usuario', $userId)
            ->with(['detalles.variante.producto'])
            ->where('fecha', '>=', now()->subDays(13)->startOfDay())
            ->get();

        $diasMap = [];
        foreach ($ventasUltimos14 as $v) {
            $dia = $v->fecha ? $v->fecha->format('Y-m-d') : null;
            if (!$dia) continue;

            $trans = $this->formatearVentaConComision(
                $v,
                $comisionesMap[$v->id] ?? [],
                $salidasMap[$v->id] ?? []
            );

            if (!isset($diasMap[$dia])) {
                $diasMap[$dia] = [
                    'pagada'          => 0.0,
                    'pendiente_cobro' => 0.0,
                    'proyectada'      => 0.0,
                    'total_comision'  => 0.0,
                    'total_venta'     => 0.0,
                    'cantidad'        => 0,
                ];
            }

            $c = $trans['comision'];
            $m = (float) $c['monto'];
            if ($c['estado'] === 'pagada') {
                $diasMap[$dia]['pagada'] += $m;
                $diasMap[$dia]['total_comision'] += $m;
            } elseif ($c['estado'] === 'pendiente_cobro') {
                $diasMap[$dia]['pendiente_cobro'] += $m;
                $diasMap[$dia]['total_comision'] += $m;
            } elseif ($c['estado'] === 'proyectada') {
                $diasMap[$dia]['proyectada'] += $m;
                $diasMap[$dia]['total_comision'] += $m;
            }

            $diasMap[$dia]['total_venta'] += (float) $v->total;
            $diasMap[$dia]['cantidad']++;
        }

        $chartDias = [];
        for ($i = 13; $i >= 0; $i--) {
            $diaStr = now()->subDays($i)->format('Y-m-d');
            $data = $diasMap[$diaStr] ?? [
                'pagada'          => 0.0,
                'pendiente_cobro' => 0.0,
                'proyectada'      => 0.0,
                'total_comision'  => 0.0,
                'total_venta'     => 0.0,
                'cantidad'        => 0,
            ];

            $chartDias[] = [
                'dia'             => $diaStr,
                'label'           => now()->subDays($i)->format('d/m'),
                'esHoy'           => $diaStr === now()->format('Y-m-d'),
                'total_comision'  => round($data['total_comision'], 2),
                'pagada'          => round($data['pagada'], 2),
                'pendiente_cobro' => round($data['pendiente_cobro'], 2),
                'proyectada'      => round($data['proyectada'], 2),
                'total_venta'     => round($data['total_venta'], 2),
                'cantidad'        => $data['cantidad'],
            ];
        }

        return $chartDias;
    }

    /**
     * Resuelve los rangos de fecha actual y previa según el periodo.
     */
    private function resolverRango(string $periodo): array
    {
        return match ($periodo) {
            'hoy' => [
                now()->startOfDay(),
                now()->endOfDay(),
                now()->subDay()->startOfDay(),
                now()->subDay()->endOfDay(),
            ],
            'semana' => [
                now()->startOfWeek(),
                now()->endOfWeek(),
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek(),
            ],
            'mes' => [
                now()->startOfMonth(),
                now()->endOfMonth(),
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ],
            default => [ // 'todo'
                null,
                null,
                null,
                null,
            ],
        };
    }
}
