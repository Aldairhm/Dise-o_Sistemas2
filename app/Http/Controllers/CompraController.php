<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\MovimientoBodega;
use App\Models\Proveedor;
use App\Models\Variante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function create()
    {
        $proveedores = Proveedor::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'correo']);

        $variantes = Variante::with('producto:id,nombre')
            ->where('estado', 1)
            ->orderBy('id')
            ->get(['id', 'id_producto', 'sku', 'nombre_variante', 'precio_venta'])
            ->map(fn (Variante $variante) => [
                'id' => $variante->id,
                'producto' => $variante->producto?->nombre ?? 'Producto sin nombre',
                'variante' => $variante->nombre_variante,
                'sku' => $variante->sku,
                'unidad' => 'unidad',
                'precio_venta' => $variante->precio_venta,
            ])
            ->values();

        return view('compras.create', compact('proveedores', 'variantes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_proveedor' => ['required', 'exists:proveedors,id'],
            'fecha_compra' => ['required', 'date'],
            'referencia' => ['nullable', 'string', 'max:100'],
            'observaciones' => ['nullable', 'string'],
            'lineas' => ['required', 'array', 'min:1'],
            'lineas.*.id_variante' => ['required', 'integer', 'distinct', 'exists:variante,id'],
            'lineas.*.cantidad' => ['required', 'integer', 'min:1'],
            'lineas.*.costo' => ['required', 'numeric', 'min:0'],
        ]);

        $compra = DB::transaction(function () use ($validated, $request) {
            $detalles = collect($validated['lineas']);
            $variantes = Variante::whereIn('id', $detalles->pluck('id_variante'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = $detalles->sum(
                fn (array $linea) => (float) $linea['cantidad'] * (float) $linea['costo']
            );

            $compra = Compra::create([
                'id_proveedor' => $validated['id_proveedor'],
                'id_usuario' => $request->user()?->id,
                'fecha_compra' => $validated['fecha_compra'],
                'referencia' => $validated['referencia'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null,
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'estado' => 'recibida',
            ]);

            foreach ($detalles as $linea) {
                $variante = $variantes->get($linea['id_variante']);
                $cantidad = (int) $linea['cantidad'];
                $costo = (float) $linea['costo'];
                $reservaAnterior = (int) $variante->reserva;
                $reservaNueva = $reservaAnterior + $cantidad;
                $stockActual = (int) $variante->stock;

                CompraDetalle::create([
                    'id_compra' => $compra->id,
                    'id_variante' => $variante->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $costo,
                    'subtotal' => $cantidad * $costo,
                ]);

                $variante->update(['reserva' => $reservaNueva]);

                MovimientoBodega::create([
                    'id_variante' => $variante->id,
                    'id_compra' => $compra->id,
                    'id_usuario' => $request->user()?->id,
                    'tipo' => 'compra_recibida',
                    'cantidad' => $cantidad,
                    'reserva_anterior' => $reservaAnterior,
                    'reserva_nueva' => $reservaNueva,
                    'stock_anterior' => $stockActual,
                    'stock_nuevo' => $stockActual,
                    'observacion' => 'Entrada a bodega por compra recibida.',
                ]);
            }

            return $compra;
        });

        return response()->json([
            'success' => true,
            'message' => 'Compra registrada y recibida en bodega.',
            'compra' => $compra->load('detalles'),
        ], 201);
    }
}