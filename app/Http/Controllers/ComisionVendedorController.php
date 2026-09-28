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
            'pendiente' => (clone $baseStats)->where('estado', 'Pendiente')->sum('monto'),
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

    // ─────────────────────────────────────────────────────────────────────────
    // POR VENDEDOR — Historial completo, suma general y desglose por vendedor
    // ─────────────────────────────────────────────────────────────────────────

    public function porVendedor(Request $request)
    {
        $user    = Auth::user();
        $isAdmin = $user->rol === 'admin';

        $desde    = $request->input('fecha_desde');
        $hasta    = $request->input('fecha_hasta');
        $busqueda = trim($request->input('q', ''));

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

        $reporte = $usuarios->map(function ($vendedor) use ($desde, $hasta) {
            $comisionesQuery = ComisionVendedor::with(['salida.variante.producto', 'liquidadoPor'])
                ->where('id_vendedor', $vendedor->id)
                ->orderBy('fecha_registro', 'desc');

            if ($desde) {
                $comisionesQuery->whereDate('fecha_registro', '>=', $desde);
            }
            if ($hasta) {
                $comisionesQuery->whereDate('fecha_registro', '<=', $hasta);
            }

            $comisiones = $comisionesQuery->get();

            $pendiente = (float) $comisiones->where('estado', 'Pendiente')->sum('monto');
            $pagada    = (float) $comisiones->where('estado', 'Pagada')->sum('monto');
            $cancelada = (float) $comisiones->where('estado', 'Cancelada')->sum('monto');
            $total     = (float) $comisiones->sum('monto');

            return [
                'vendedor'         => $vendedor,
                'comisiones'       => $comisiones,
                'total_comisiones' => $total,
                'total_pendiente'  => $pendiente,
                'total_pagada'     => $pagada,
                'total_cancelada'  => $cancelada,
                'total_ventas'     => $comisiones->count(),
                'total_pendientes' => $comisiones->where('estado', 'Pendiente')->count(),
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

        $vendedores = $isAdmin ? User::orderBy('nombre_real')->get() : collect();

        return view('comisiones.por_vendedor', compact('reporte', 'kpis', 'vendedores', 'isAdmin', 'desde', 'hasta', 'busqueda'));
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
                'fecha_registro' => $c->fecha_registro ? $c->fecha_registro->format('d/m/Y H:i') : 'N/A',
                'id_salida'      => $c->id_salida,
            ];
        });

        return response()->json([
            'success'    => true,
            'vendedor'   => $vendedor->nombre_real,
            'total'      => (float) $pendientes->sum('monto'),
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
            'concepto'       => $validated['concepto'],
            'monto'          => $validated['monto'],
            'porcentaje'     => null,
            'notas'          => $validated['notas'] ?? null,
            'estado'         => 'Pendiente',
            'fecha_registro' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comisión/Bono registrado exitosamente.',
            ]);
        }

        return redirect()->route('comisiones.index')->with('success', 'Comisión/Bono registrado exitosamente.');
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
        $validated = $request->validate([
            'id_vendedor'      => ['required', 'integer', 'exists:usuario,id'],
            'metodo_pago'      => ['required', 'string', 'in:Efectivo,Transferencia Bancaria,Cheque,Billetera Digital,Otro'],
            'referencia_pago'  => ['nullable', 'string', 'max:100'],
            'comisiones_ids'   => ['nullable', 'array'],
            'comisiones_ids.*' => ['integer', 'exists:comision_vendedor,id'],
            'comprobante_pago' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:5120'],
            'notas'            => ['nullable', 'string', 'max:500'],
        ], [
            'id_vendedor.required' => 'Debes seleccionar un vendedor.',
            'metodo_pago.required' => 'Debes seleccionar el método de pago.',
        ]);

        $comprobantePath = null;
        if ($request->hasFile('comprobante_pago')) {
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
                'message' => 'No se encontraron comisiones pendientes para liquidar con los criterios seleccionados.',
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
