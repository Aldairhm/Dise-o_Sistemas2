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
    public function historial()
    {
        $compras = Compra::with('proveedor')
            ->with(['detalles.variante.producto', 'detalles.variante.imagenes', 'movimientos.variante.producto'])
            ->withCount('detalles')
            ->latest('fecha_compra')
            ->latest('id')
            ->paginate(15);

        return view('compras.historial', compact('compras'));
    }

    public function movimientos()
    {
        $variantes = Variante::with(['producto:id,nombre', 'imagenes'])
            ->where('estado', 1)
            ->orderBy('id')
            ->limit(10)
            ->get(['id', 'id_producto', 'sku', 'nombre_variante', 'stock', 'reserva']);

        $movimientos = MovimientoBodega::with(['variante.producto', 'variante.imagenes', 'compra.proveedor'])
            ->where('tipo', 'transferencia_tienda')
            ->latest()
            ->paginate(20);

        return view('compras.movimientos', compact('movimientos', 'variantes'));
    }

    public function create()
    {
        $proveedores = Proveedor::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'correo']);

        $variantes = Variante::with(['producto:id,nombre', 'imagenes'])
            ->where('estado', 1)
            ->orderBy('id')
            ->limit(10)
            ->get(['id', 'id_producto', 'sku', 'nombre_variante', 'precio_venta'])
            ->map(fn (Variante $variante) => [
                'id' => $variante->id,
                'producto' => $variante->producto?->nombre ?? 'Producto sin nombre',
                'variante' => $variante->nombre_variante,
                'sku' => $variante->sku,
                'imagen' => ($imagen = $variante->imagenes->firstWhere('es_principal', 1) ?? $variante->imagenes->first())
                    ? asset('storage/' . $imagen->ruta_imagen)
                    : null,
                'unidad' => 'unidad',
                'precio_venta' => $variante->precio_venta,
            ])
            ->values();

        return view('compras.create', compact('proveedores', 'variantes'));
    }

    public function buscarVariantes(Request $request)
    {
        $busqueda = $request->query('q', '');

        $query = Variante::with(['producto:id,nombre', 'imagenes'])
            ->where('estado', 1);

        if (!empty($busqueda)) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('sku', 'like', "%{$busqueda}%")
                  ->orWhere('nombre_variante', 'like', "%{$busqueda}%")
                  ->orWhereHas('producto', function ($qProd) use ($busqueda) {
                      $qProd->where('nombre', 'like', "%{$busqueda}%");
                  });
            });
        }

        $paginator = $query->orderBy('id')
            ->paginate(10, ['id', 'id_producto', 'sku', 'nombre_variante', 'precio_venta', 'stock', 'reserva']);

        $paginator->getCollection()->transform(fn (Variante $variante) => [
            'id' => $variante->id,
            'producto' => $variante->producto?->nombre ?? 'Producto sin nombre',
            'variante' => $variante->nombre_variante,
            'sku' => $variante->sku,
            'imagen' => ($imagen = $variante->imagenes->firstWhere('es_principal', 1) ?? $variante->imagenes->first())
                ? asset('storage/' . $imagen->ruta_imagen)
                : null,
            'unidad' => 'unidad',
            'precio_venta' => $variante->precio_venta,
            'stock' => $variante->stock,
            'reserva' => $variante->reserva,
        ]);

        return response()->json($paginator);
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
            $variantes = Variante::with('producto')
                ->whereIn('id', $detalles->pluck('id_variante'))
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

                // Cálculo del nuevo costo promedio ponderado
                $unidadesActuales = $stockActual + $reservaAnterior;
                $costoPromedioActual = (float) $variante->costo_promedio;
                
                $nuevoCostoPromedio = (($unidadesActuales * $costoPromedioActual) + ($cantidad * $costo)) / ($unidadesActuales + $cantidad);
                
                // Cálculo del nuevo precio de venta (costo promedio + ganancia + comisión del producto)
                $porcentajeGanancia = (float) $variante->porcentaje_ganancia;
                $comision = (float) ($variante->producto->comision ?? 0);
                $nuevoPrecioVenta = ($nuevoCostoPromedio * (1 + ($porcentajeGanancia / 100))) + $comision;

                CompraDetalle::create([
                    'id_compra' => $compra->id,
                    'id_variante' => $variante->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $costo,
                    'subtotal' => $cantidad * $costo,
                ]);

                $variante->update([
                    'reserva' => $reservaNueva,
                    'costo_promedio' => $nuevoCostoPromedio,
                    'precio_venta' => $nuevoPrecioVenta
                ]);

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