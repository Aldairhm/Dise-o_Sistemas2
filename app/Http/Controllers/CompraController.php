<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\MovimientoBodega;
use App\Models\Proveedor;
use App\Models\Variante;
use App\Services\ActivityLogger;
use App\Support\CloudStorage;
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

    public function show(string|int $id)
    {
        if (!is_numeric($id)) {
            abort(404, 'Compra no encontrada');
        }

        $compra = Compra::with([
            'proveedor',
            'detalles.variante.producto',
            'detalles.variante.imagenes',
            'movimientos.variante.producto',
        ])->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'compra' => [
                    'id' => $compra->id,
                    'fecha_compra' => $compra->fecha_compra?->format('d/m/Y'),
                    'proveedor_nombre' => $compra->proveedor?->nombre ?? 'Sin proveedor',
                    'referencia' => $compra->referencia ?: 'Sin referencia',
                    'estado' => $compra->estado,
                    'total' => (float) $compra->total,
                    'observaciones' => $compra->observaciones ?: 'Sin observaciones',
                    'detalles' => $compra->detalles->map(function ($det) {
                        return [
                            'id' => $det->id,
                            'producto' => $det->variante?->producto?->nombre ?? 'Producto',
                            'variante' => $det->variante?->nombre_variante ?? '',
                            'sku' => $det->variante?->sku ?: 'Sin SKU',
                            'imagen_url' => $det->variante?->imagen_url,
                            'cantidad' => $det->cantidad,
                            'precio_unitario' => (float) $det->precio_unitario,
                            'subtotal' => (float) $det->subtotal,
                        ];
                    }),
                    'movimientos' => $compra->movimientos->map(function ($mov) {
                        return [
                            'id' => $mov->id,
                            'tipo' => $mov->tipo === 'compra_recibida' ? 'Entrada a bodega' : 'Transferencia a tienda',
                            'cantidad' => $mov->cantidad,
                            'fecha' => $mov->created_at?->format('d/m/Y H:i'),
                        ];
                    }),
                ],
            ]);
        }

        return redirect()->route('compras.historial');
    }

    public function movimientos()
    {
        $variantes = Variante::with(['producto:id,nombre', 'imagenes'])
            ->where('estado', 1)
            ->orderBy('id')
            ->limit(10)
            ->get(['id', 'id_producto', 'sku', 'nombre_variante', 'stock', 'reserva']);

        $filtroMovimiento = request()->query('tipo', 'todos');
        $movimientoId = request()->integer('movimiento');
        $tiposPermitidos = ['todos', 'transferencias', 'devoluciones', 'compras', 'entradas', 'salidas'];
        if (!in_array($filtroMovimiento, $tiposPermitidos, true)) {
            $filtroMovimiento = 'todos';
        }

        $movimientosQuery = MovimientoBodega::with([
            'variante.producto',
            'variante.imagenes',
            'compra.proveedor',
            'devolucion',
        ]);
        $movimientosQuery->when($movimientoId > 0, fn ($query) => $query->whereKey($movimientoId));

        $movimientosQuery->when($filtroMovimiento === 'transferencias', fn ($query) =>
            $query->where('tipo', 'transferencia_tienda')
        );
        $movimientosQuery->when($filtroMovimiento === 'compras', fn ($query) =>
            $query->where('tipo', 'compra_recibida')
        );
        $movimientosQuery->when($filtroMovimiento === 'devoluciones', fn ($query) =>
            $query->where(function ($subquery) {
                $subquery->whereNotNull('devolucion_id')
                    ->orWhereIn('tipo', ['devolucion_cliente', 'devolucion_proveedor'])
                    ->orWhere('observacion', 'like', '%devolución%')
                    ->orWhere('observacion', 'like', '%devolucion%');
            })
        );
        $movimientosQuery->when($filtroMovimiento === 'entradas', fn ($query) =>
            $query->whereIn('tipo', ['Entrada', 'entrada', 'compra_recibida', 'devolucion_cliente', 'devolucion_proveedor'])
        );
        $movimientosQuery->when($filtroMovimiento === 'salidas', fn ($query) =>
            $query->whereIn('tipo', ['Salida', 'salida', 'transferencia_tienda', 'devolucion_proveedor'])
        );

        $movimientos = $movimientosQuery
            ->latest()
            ->paginate(20)
            ->withQueryString();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('compras.tabla-movimientos', compact('movimientos'))->render(),
            ]);
        }

        return view('compras.movimientos', compact('movimientos', 'variantes', 'filtroMovimiento', 'movimientoId'));

    }

    public function detalleMovimiento(string|int $id)
    {
        if (!is_numeric($id)) {
            abort(404, 'Movimiento no encontrado');
        }

        $movimiento = MovimientoBodega::with([
            'variante.producto',
            'variante.imagenes',
            'compra',
            'devolucion'
        ])->findOrFail($id);

        $etiquetaTipo = match ($movimiento->tipo) {
            'transferencia_tienda' => 'Transferencia a tienda',
            'compra_recibida' => 'Compra recibida',
            'devolucion_cliente' => 'Devolución cliente',
            'devolucion_proveedor' => 'Devolución proveedor',
            'Entrada', 'entrada' => 'Entrada',
            'Salida', 'salida' => 'Salida',
            default => $movimiento->tipo,
        };

        $origen = $movimiento->devolucion 
            ? 'DEV-' . str_pad($movimiento->devolucion_id, 5, '0', STR_PAD_LEFT) 
            : ($movimiento->compra ? 'Compra #' . $movimiento->id_compra : 'Inventario');

        return response()->json([
            'success' => true,
            'movimiento' => [
                'id' => $movimiento->id,
                'fecha' => $movimiento->created_at?->format('d/m/Y H:i'),
                'tipo' => $etiquetaTipo,
                'cantidad' => $movimiento->cantidad,
                'producto' => $movimiento->variante?->producto?->nombre ?? 'Producto',
                'variante' => $movimiento->variante?->nombre_variante ?? 'Variante',
                'sku' => $movimiento->variante?->sku ?: 'Sin SKU',
                'imagen_url' => $movimiento->variante?->imagen_url,
                'origen' => $origen,
                'reserva_anterior' => $movimiento->reserva_anterior,
                'reserva_nueva' => $movimiento->reserva_nueva,
                'stock_anterior' => $movimiento->stock_anterior,
                'stock_nuevo' => $movimiento->stock_nuevo,
                'stock_cuarentena_anterior' => $movimiento->stock_cuarentena_anterior ?? 0,
                'stock_cuarentena_nuevo' => $movimiento->stock_cuarentena_nuevo ?? 0,
                'observacion' => $movimiento->observacion ?: 'Sin observación',
            ]
        ]);
    }

    public function create()
    {
        $proveedores = Proveedor::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'correo']);

        $variantes = $this->catalogoVariantes('', 1);

        return view('compras.create', compact('proveedores', 'variantes'));
    }

    public function buscarVariantes(Request $request)
    {
        return response()->json($this->catalogoVariantes($request->query('q', '')));
    }

    private function catalogoVariantes($busqueda, ?int $pagina = null)
    {
        $query = Variante::with(['producto:id,nombre,imagen_principal', 'imagenes'])
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
            ->paginate(10, ['id', 'id_producto', 'sku', 'nombre_variante', 'precio_venta', 'stock', 'reserva'], 'page', $pagina);

        $paginator->getCollection()->transform(fn (Variante $variante) => [
            'id' => $variante->id,
            'producto' => $variante->producto?->nombre ?? 'Producto sin nombre',
            'variante' => $variante->nombre_variante,
            'sku' => $variante->sku,
            'imagen' => ($imagen = $variante->imagenes->firstWhere('es_principal', 1) ?? $variante->imagenes->first())
                ? CloudStorage::url($imagen->ruta_imagen)
                : null,
            'unidad' => 'unidad',
            'precio_venta' => $variante->precio_venta,
            'stock' => $variante->stock,
            'reserva' => $variante->reserva,
        ]);

        return $paginator;
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

        // ── Bitácora ──
        ActivityLogger::log(
            'CREAR', 'Compras',
            "Se registró la compra #{$compra->id} con total de $" . number_format($compra->total, 2) . " (" . count($validated['lineas']) . " ítems)."
        );

        return response()->json([
            'success' => true,
            'message' => 'Compra registrada y recibida en bodega.',
            'compra' => $compra->load('detalles'),
        ], 201);
    }
}
