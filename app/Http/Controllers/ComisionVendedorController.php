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
        $user  = Auth::user();
        $isAdmin = $user->rol === 'admin';

        $query = ComisionVendedor::with(['vendedor', 'salida', 'liquidadoPor'])
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
    // STORE — Registro manual de comisión (solo admin)
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_vendedor' => ['required', 'integer', 'exists:usuario,id'],
            'id_salida'   => ['nullable', 'integer', 'exists:salida,id'],
            'monto'       => ['required', 'numeric', 'min:0.01'],
            'porcentaje'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notas'       => ['nullable', 'string', 'max:500'],
        ], [
            'id_vendedor.required' => 'Debes seleccionar un vendedor.',
            'id_vendedor.exists'   => 'El vendedor seleccionado no existe.',
            'monto.required'       => 'El monto de comisión es obligatorio.',
            'monto.min'            => 'El monto debe ser mayor a 0.',
        ]);

        // Anti-duplicado si viene con id_salida
        if (!empty($validated['id_salida'])) {
            if (ComisionVendedor::where('id_salida', $validated['id_salida'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una comisión registrada para esa salida.',
                ], 422);
            }
        }

        $comision = ComisionVendedor::create([
            'id_vendedor'    => $validated['id_vendedor'],
            'id_salida'      => $validated['id_salida'] ?? null,
            'monto'          => $validated['monto'],
            'porcentaje'     => $validated['porcentaje'] ?? 0,
            'notas'          => $validated['notas'] ?? null,
            'estado'         => 'Pendiente',
            'fecha_registro' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comisión registrada exitosamente.',
            ]);
        }

        return redirect()->route('comisiones.index')->with('success', 'Comisión registrada exitosamente.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE — Ajuste manual de monto/notas antes de pagar (solo admin)
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
            'monto'      => ['required', 'numeric', 'min:0.01'],
            'porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notas'      => ['nullable', 'string', 'max:500'],
        ]);

        $comision->update([
            'monto'      => $validated['monto'],
            'porcentaje' => $validated['porcentaje'] ?? $comision->porcentaje,
            'notas'      => $validated['notas'] ?? $comision->notas,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comisión actualizada correctamente.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LIQUIDAR — Marca en lote como Pagadas (solo admin)
    // ─────────────────────────────────────────────────────────────────────────

    public function liquidar(Request $request)
    {
        $validated = $request->validate([
            'id_vendedor' => ['required', 'integer', 'exists:usuario,id'],
            'hasta_fecha' => ['nullable', 'date'],
            'notas'       => ['nullable', 'string', 'max:500'],
        ], [
            'id_vendedor.required' => 'Debes seleccionar un vendedor.',
        ]);

        $cantidad = $this->comisionService->liquidarVendedor(
            $validated['id_vendedor'],
            $validated['hasta_fecha'] ?? null,
            $validated['notas']       ?? null
        );

        if ($cantidad === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No hay comisiones pendientes para liquidar con los filtros seleccionados.',
            ], 422);
        }

        return response()->json([
            'success'  => true,
            'message'  => "Se liquidaron {$cantidad} comisión(es) exitosamente.",
            'cantidad' => $cantidad,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CANCELAR — Cancela una comisión Pendiente (solo admin)
    // ─────────────────────────────────────────────────────────────────────────

    public function cancelar(Request $request, ComisionVendedor $comision)
    {
        if ($comision->estado !== 'Pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden cancelar comisiones en estado Pendiente.',
            ], 422);
        }

        $comision->update([
            'estado' => 'Cancelada',
            'notas'  => ($comision->notas ? $comision->notas . ' | ' : '') . 'Cancelada manualmente por ' . Auth::user()->nombre_real . '.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comisión cancelada correctamente.',
        ]);
    }
}
