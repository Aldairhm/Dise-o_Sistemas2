<?php

namespace App\Http\Controllers;

use App\Models\ComisionVendedor;
use App\Models\User;
use App\Services\ComisionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComisionVendedorController extends Controller
{
    public function __construct(protected ComisionService $comisionService) {}

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — Lista con filtros (Admin ve todo; Vendedor solo lo suyo)
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user    = Auth::user();
        $isAdmin = $user->rol === 'admin';

        $query = ComisionVendedor::with(['vendedor', 'salida.variante.producto', 'liquidadoPor'])
            ->orderBy('fecha_registro', 'desc');

        // Vendedor solo ve las suyas
        if (!$isAdmin) {
            $query->where('id_vendedor', $user->id);
        }

        // Filtros para admin
        if ($isAdmin && $request->filled('vendedor_id')) {
            $query->where('id_vendedor', $request->vendedor_id);
        }

        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('metodo_pago') && $request->metodo_pago !== 'todos') {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_registro', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_registro', '<=', $request->fecha_hasta);
        }

        $comisiones = $query->paginate(15)->withQueryString();

        // KPIs
        $baseStats = ComisionVendedor::query();
        if (!$isAdmin) {
            $baseStats->where('id_vendedor', $user->id);
        }

        $stats = [
            'pendiente' => max(0, (float) (clone $baseStats)->where('estado', 'Pendiente')->where('monto', '>=', 0)->sum('monto')
                + (clone $baseStats)->whereIn('estado', ['Cancelada', 'Pendiente'])->where('monto', '<', 0)->sum('monto')),
            'pagada'    => (clone $baseStats)->where('estado', 'Pagada')->sum('monto'),
            'cancelada' => (clone $baseStats)->where('estado', 'Cancelada')->sum('monto'),
            'total'     => (clone $baseStats)->sum('monto'),
        ];

        $vendedores = $isAdmin ? User::orderBy('nombre_real')->get() : collect();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'table'   => view('comisiones.partials.table', compact('comisiones', 'isAdmin'))->render(),
                'stats'   => $stats,
            ]);
        }

        return view('comisiones.index', compact('comisiones', 'stats', 'vendedores', 'isAdmin'));
    }

    public function ajustes(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->rol === 'admin';

        $query = ComisionVendedor::with(['vendedor', 'salida.variante.producto'])
            ->where(function ($query) {
                $query->where('estado', 'Cancelada')
                    ->orWhere('monto', '<', 0)
                    ->orWhere('notas', 'like', '%Descuento descontado:%')
                    ->orWhere('concepto', 'like', 'Deducción envío%');
            })
            ->orderByDesc('fecha_registro');

        if (!$isAdmin) {
            $query->where('id_vendedor', $user->id);
        } elseif ($request->filled('vendedor_id')) {
            $query->where('id_vendedor', $request->input('vendedor_id'));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_registro', '>=', $request->input('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_registro', '<=', $request->input('fecha_hasta'));
        }

        $ajustes = $query->paginate(20)->withQueryString();
        $vendedores = $isAdmin ? User::orderBy('nombre_real')->get() : collect();

        return view('comisiones.ajustes', compact('ajustes', 'vendedores', 'isAdmin'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POR VENDEDOR — Historial completo, suma general y desglose por vendedor
    // ─────────────────────────────────────────────────────────────────────────

    public function porVendedor(Request $request)
    {
        $user    = Auth::user();
        $isAdmin = $user->rol === 'admin';

        $periodo  = $request->input('periodo', 'mes'); // 'mes' o 'historico'
        $mes      = (int) $request->input('mes', now()->month);
        $anio     = (int) $request->input('anio', now()->year);
        $desde    = $request->input('fecha_desde');
        $hasta    = $request->input('fecha_hasta');
        $busqueda = trim($request->input('q', ''));

        if ($periodo === 'mes') {
            if ($mes < 1 || $mes > 12) {
                $mes = (int) now()->month;
            }
            if ($anio < 2020 || $anio > 2050) {
                $anio = (int) now()->year;
            }
        }

        $vendedoresQuery = User::query()->orderBy('nombre_real');

        if (!$isAdmin) {
            $vendedoresQuery->where('id', $user->id);
        } else {
            if (!empty($busqueda)) {
                $vendedoresQuery->where(function ($q) use ($busqueda) {
                    $q->where('nombre_real', 'like', "%{$busqueda}%")
                      ->orWhere('username', 'like', "%{$busqueda}%");
                });
            }
        }

        $usuarios = $vendedoresQuery->get();

        $reporte = $usuarios->map(function ($vendedor) use ($periodo, $mes, $anio, $desde, $hasta) {
            $comisionesQuery = ComisionVendedor::with(['salida.variante.producto', 'liquidadoPor'])
                ->where('id_vendedor', $vendedor->id)
                ->orderBy('fecha_registro', 'desc');

            if ($periodo === 'mes') {
                $comisionesQuery->whereYear('fecha_registro', $anio)
                                ->whereMonth('fecha_registro', $mes);
            } else {
                if ($desde) {
                    $comisionesQuery->whereDate('fecha_registro', '>=', $desde);
                }
                if ($hasta) {
                    $comisionesQuery->whereDate('fecha_registro', '<=', $hasta);
                }
            }

            $comisiones = $comisionesQuery->get();

            $pendiente = max(0, (float) $comisiones->where('estado', 'Pendiente')->where('monto', '>=', 0)->sum('monto')
                + $comisiones->whereIn('estado', ['Cancelada', 'Pendiente'])->where('monto', '<', 0)->sum('monto'));
            $pagada    = (float) $comisiones->where('estado', 'Pagada')->sum('monto');
            $cancelada = (float) $comisiones->where('estado', 'Cancelada')->sum('monto');
            $total     = (float) $comisiones->sum('monto');

            // Saldo total pendiente de todos los tiempos para este vendedor
            $saldoPendienteTotal = max(0, (float) ComisionVendedor::where('id_vendedor', $vendedor->id)
                ->where('estado', 'Pendiente')
                ->where('monto', '>=', 0)
                ->sum('monto') + ComisionVendedor::where('id_vendedor', $vendedor->id)
                ->whereIn('estado', ['Cancelada', 'Pendiente'])
                ->where('monto', '<', 0)
                ->sum('monto'));

            return [
                'vendedor'              => $vendedor,
                'comisiones'            => $comisiones,
                'total_comisiones'      => $total,
                'total_pendiente'       => $pendiente,
                'total_pagada'          => $pagada,
                'total_cancelada'       => $cancelada,
                'total_ventas'          => $comisiones->count(),
                'total_pendientes'      => $comisiones->where('estado', 'Pendiente')->count(),
                'saldo_pendiente_total' => $saldoPendienteTotal,
            ];
        });

        // Totales globales para KPIs
        $kpis = [
            'total'      => (float) $reporte->sum('total_comisiones'),
            'pendiente'  => (float) $reporte->sum('total_pendiente'),
            'pagada'     => (float) $reporte->sum('total_pagada'),
            'cancelada'  => (float) $reporte->sum('total_cancelada'),
            'vendedores' => $reporte->where('total_comisiones', '>', 0)->count(),
        ];

        $nombresMeses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $mesNombre   = $nombresMeses[$mes] ?? 'Mes Actual';
        $esMesActual = ($periodo === 'mes' && $mes === (int) now()->month && $anio === (int) now()->year);

        $fechaReferencia = \Illuminate\Support\Carbon::createFromDate($anio, $mes, 1);
        $mesAnterior  = $fechaReferencia->copy()->subMonth();
        $mesSiguiente = $fechaReferencia->copy()->addMonth();

        $vendedores = $isAdmin ? User::orderBy('nombre_real')->get() : collect();

        return view('comisiones.por_vendedor', compact(
            'reporte', 'kpis', 'vendedores', 'isAdmin', 'desde', 'hasta', 'busqueda',
            'periodo', 'mes', 'anio', 'mesNombre', 'esMesActual', 'nombresMeses', 'mesAnterior', 'mesSiguiente'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POR SEMANA — Cortes semanales (Lunes a Domingo), acumulados y desglose
    // ─────────────────────────────────────────────────────────────────────────

    public function porSemana(Request $request)
    {
        $user    = Auth::user();
        $isAdmin = $user->rol === 'admin';

        $anio       = (int) ($request->input('anio', now()->year));
        $vendedorId = $request->input('vendedor_id');

        $query = ComisionVendedor::with(['vendedor', 'salida.variante.producto', 'liquidadoPor'])
            ->whereYear('fecha_registro', $anio)
            ->orderBy('fecha_registro', 'desc');

        if (!$isAdmin) {
            $query->where('id_vendedor', $user->id);
        } elseif ($vendedorId) {
            $query->where('id_vendedor', $vendedorId);
        }

        $comisiones = $query->get();

        // Agrupar por semana (ISO 8601 lunes a domingo)
        $semanasAgrupadas = $comisiones->groupBy(function ($c) {
            return $c->fecha_registro ? $c->fecha_registro->format('o-W') : now()->format('o-W');
        });

        // Aseguramos que la semana actual siempre esté presente en el reporte
        $semanaActualKey = now()->format('o-W');
        if (!$semanasAgrupadas->has($semanaActualKey) && $anio === (int) now()->year) {
            $semanasAgrupadas->put($semanaActualKey, collect());
        }

        $reporteSemanas = $semanasAgrupadas->map(function ($items, $key) use ($semanaActualKey) {
            $parts = explode('-', $key);
            $year = (int) ($parts[0] ?? now()->year);
            $week = (int) ($parts[1] ?? now()->weekOfYear);

            $inicioSemana = \Illuminate\Support\Carbon::now()->setISODate($year, $week)->startOfWeek();
            $finSemana    = \Illuminate\Support\Carbon::now()->setISODate($year, $week)->endOfWeek();

            $pendiente = max(0, (float) $items->where('estado', 'Pendiente')->where('monto', '>=', 0)->sum('monto')
                + $items->whereIn('estado', ['Cancelada', 'Pendiente'])->where('monto', '<', 0)->sum('monto'));
            $pagada    = (float) $items->where('estado', 'Pagada')->sum('monto');
            $cancelada = (float) $items->where('estado', 'Cancelada')->sum('monto');
            $semanaCerrada = now()->greaterThan($finSemana);

            // Resumen de vendedores en esta semana
            $vendedoresSemana = $items->groupBy('id_vendedor')->map(function ($vItems) use ($semanaCerrada) {
                $vendedor = $vItems->first()->vendedor ?? null;
                $totalVendedor = (float) $vItems->sum('monto');
                if ($semanaCerrada && $totalVendedor < 0) {
                    $totalVendedor = 0.0;
                }

                return [
                    'vendedor'  => $vendedor,
                    'total'     => $totalVendedor,
                    'pendiente' => max(0, (float) $vItems->where('estado', 'Pendiente')->where('monto', '>=', 0)->sum('monto')
                        + $vItems->whereIn('estado', ['Cancelada', 'Pendiente'])->where('monto', '<', 0)->sum('monto')),
                    'pagada'    => (float) $vItems->where('estado', 'Pagada')->sum('monto'),
                    'cantidad'  => $vItems->count(),
                ];
            });
            $total = (float) $vendedoresSemana->sum('total');

            return [
                'key'              => $key,
                'semana_numero'    => $week,
                'year'             => $year,
                'inicio_semana'    => $inicioSemana,
                'fin_semana'       => $finSemana,
                'es_semana_actual' => ($key === $semanaActualKey),
                'total_comisiones' => $total,
                'total_pendiente'  => $pendiente,
                'total_pagada'     => $pagada,
                'total_cancelada'  => $cancelada,
                'total_ventas'     => $items->count(),
                'comisiones'       => $items,
                'vendedores'       => $vendedoresSemana,
            ];
        })->sortByDesc('key')->values();

        $kpis = [
            'total'     => (float) $reporteSemanas->sum('total_comisiones'),
            'pendiente' => (float) $reporteSemanas->sum('total_pendiente'),
            'pagada'    => (float) $comisiones->where('estado', 'Pagada')->sum('monto'),
            'semanas'   => $reporteSemanas->where('total_comisiones', '>', 0)->count(),
        ];

        $vendedores = $isAdmin ? User::orderBy('nombre_real')->get() : collect();

        return view('comisiones.por_semana', compact('reporteSemanas', 'kpis', 'vendedores', 'isAdmin', 'anio', 'vendedorId'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PENDIENTES VENDEDOR — Obtiene comisiones pendientes para liquidación (admin)
    // ─────────────────────────────────────────────────────────────────────────

    public function pendientesVendedor(int $idVendedor)
    {
        $vendedor = User::findOrFail($idVendedor);
        $pendientes = $this->comisionService->getPendientesVendedor($idVendedor);

        $data = $pendientes->map(function ($c) {
            return [
                'id'             => $c->id,
                'concepto'       => $c->concepto ?? ($c->salida ? "Venta #{$c->salida->id} ({$c->salida->cantidad} uds)" : "Comisión #{$c->id}"),
                'monto'          => (float) $c->monto,
                'notas'          => $c->notas,
                'es_ajuste_negativo' => (float) $c->monto < 0,
                'fecha_registro' => $c->fecha_registro ? $c->fecha_registro->format('d/m/Y H:i') : 'N/A',
                'id_salida'      => $c->id_salida,
            ];
        });

        return response()->json([
            'success'    => true,
            'vendedor'   => $vendedor->nombre_real,
            'total'      => max(0, (float) $pendientes->sum('monto')),
            'cantidad'   => $pendientes->count(),
            'comisiones' => $data,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STORE — Registro manual de comisión / bono (solo admin)
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_vendedor' => ['required', 'integer', 'exists:usuario,id'],
            'concepto'    => ['required', 'string', 'max:150'],
            'monto'       => ['required', 'numeric', 'min:0.01'],
            'notas'       => ['nullable', 'string', 'max:500'],
        ], [
            'id_vendedor.required' => 'Debes seleccionar un vendedor.',
            'id_vendedor.exists'   => 'El vendedor seleccionado no existe.',
            'concepto.required'    => 'Debes ingresar el concepto o motivo.',
            'monto.required'       => 'El monto de comisión es obligatorio.',
            'monto.min'            => 'El monto debe ser mayor a 0.',
        ]);

        ComisionVendedor::create([
            'id_vendedor'    => $validated['id_vendedor'],
            'id_salida'      => null,
            'concepto'       => $validated['concepto'],
            'monto'          => $validated['monto'],
            'porcentaje'     => 0.00,
            'notas'          => $validated['notas'] ?? null,
            'estado'         => 'Pendiente',
            'fecha_registro' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bono registrado exitosamente.',
            ]);
        }

        return redirect()->route('comisiones.porVendedor')->with('success', 'Bono registrado exitosamente.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE — Ajuste manual de monto/concepto/notas antes de pagar (solo admin)
    // ─────────────────────────────────────────────────────────────────────────

    public function update(Request $request, ComisionVendedor $comision)
    {
        // Solo se puede editar si está Pendiente
        if ($comision->estado !== 'Pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden editar comisiones en estado Pendiente.',
            ], 422);
        }

        $validated = $request->validate([
            'concepto' => ['nullable', 'string', 'max:150'],
            'monto'    => ['required', 'numeric', 'min:0.01'],
            'notas'    => ['nullable', 'string', 'max:500'],
        ]);

        $comision->update([
            'concepto' => $validated['concepto'] ?? $comision->concepto,
            'monto'    => $validated['monto'],
            'notas'    => $validated['notas'] ?? $comision->notas,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comisión actualizada correctamente.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LIQUIDAR — Paga y registra el método de pago (solo admin)
    // ─────────────────────────────────────────────────────────────────────────

    public function liquidar(Request $request)
    {
        $isTransferencia = $request->input('metodo_pago') === 'Transferencia Bancaria';

        $validated = $request->validate([
            'id_vendedor'      => ['required', 'integer', 'exists:usuario,id'],
            'metodo_pago'      => ['required', 'string', 'in:Efectivo,Transferencia Bancaria'],
            'referencia_pago'  => ['nullable', 'string', 'max:100'],
            'comisiones_ids'   => ['nullable', 'array'],
            'comisiones_ids.*' => ['integer', 'exists:comision_vendedor,id'],
            'comprobante_pago' => [
                $isTransferencia ? 'required' : 'nullable',
                'file',
                'mimes:jpeg,png,jpg,webp,pdf',
                'max:5120'
            ],
            'notas'            => ['nullable', 'string', 'max:500'],
        ], [
            'id_vendedor.required'      => 'Debes seleccionar un vendedor.',
            'metodo_pago.required'      => 'Debes seleccionar el método de pago.',
            'metodo_pago.in'            => 'El método de pago debe ser Efectivo o Transferencia Bancaria.',
            'comprobante_pago.required' => 'El comprobante o recibo es obligatorio para pagos por Transferencia Bancaria.',
            'comprobante_pago.mimes'    => 'El comprobante debe ser una imagen (JPG, PNG, WEBP) o un documento PDF.',
            'comprobante_pago.max'      => 'El comprobante no debe superar los 5MB.',
        ]);

        $comprobantePath = null;
        if ($isTransferencia && $request->hasFile('comprobante_pago')) {
            $archivo = $request->file('comprobante_pago');
            $nombreArchivo = 'liquidacion_vendedor_' . $validated['id_vendedor'] . '_' . now()->format('Ymd_His') . '.' . $archivo->getClientOriginalExtension();
            $comprobantePath = $archivo->storeAs('comprobantes_liquidaciones', $nombreArchivo, 'public');
        }

        $cantidad = $this->comisionService->liquidarComisiones(
            (int) $validated['id_vendedor'],
            $validated['comisiones_ids'] ?? [],
            $validated['metodo_pago'],
            $validated['referencia_pago'] ?? null,
            $comprobantePath,
            $validated['notas'] ?? null
        );

        if ($cantidad === 0) {
            return response()->json([
                'success' => false,
                'message' => 'El saldo neto es $0.00 o negativo; no se realizará ningún pago.',
            ], 422);
        }

        return response()->json([
            'success'  => true,
            'message'  => "Se liquidaron exitosamente {$cantidad} comisiones mediante {$validated['metodo_pago']}.",
            'cantidad' => $cantidad,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CANCELAR — Anula una comisión pendiente (solo admin)
    // ─────────────────────────────────────────────────────────────────────────

    public function cancelar(Request $request, ComisionVendedor $comision)
    {
        if ($comision->estado !== 'Pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden cancelar comisiones en estado Pendiente.',
            ], 422);
        }

        $motivo = $request->input('motivo', 'Cancelada por el administrador.');

        $comision->update([
            'estado' => 'Cancelada',
            'notas'  => ($comision->notas ? $comision->notas . ' | ' : '') . $motivo,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comisión cancelada correctamente.',
        ]);
    }
}
