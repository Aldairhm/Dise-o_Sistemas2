<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Devolucion;
use App\Models\DevolucionDetalle;
use App\Models\Variante;
use App\Models\MovimientoBodega;
use App\Models\DetalleVenta;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class DevolucionController extends Controller
{
    /**
     * Muestra el historial de devoluciones.
     */
    public function index()
    {
        $devoluciones = Devolucion::query()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('devoluciones.index', compact('devoluciones'));
    }

    public function devolucionesProveedor()
    {
        $devoluciones = Devolucion::where('origen_tipo', 'compra')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('compras.devoluciones', compact('devoluciones'));
    }

    public function devolucionesCliente()
    {
        $devoluciones = Devolucion::where('origen_tipo', 'venta')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('ventas.devoluciones', compact('devoluciones'));
    }

    /**
     * Procesa la devolución de una venta.
     */
    public function storeVenta(Request $request)
    {
        $validated = $request->validate([
            'venta_id' => ['required', 'integer', 'exists:venta,id'],
            'motivo' => ['required', 'string', 'max:255'],
            'tipo_resolucion' => ['required', 'in:reembolso_tienda,reembolso_cuarentena,cambio_tienda,cambio_cuarentena'],
            'comprobante' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'productos' => ['required', 'array', 'min:1'],
            'productos.*.id_variante' => ['required', 'integer', 'distinct', 'exists:variante,id'],
            'productos.*.cantidad' => ['required', 'integer', 'min:1'],
            'productos.*.seleccionado' => ['nullable', 'boolean'],
        ]);

            $productos = collect($validated['productos'])
                ->filter(fn (array $producto) => filter_var($producto['seleccionado'] ?? false, FILTER_VALIDATE_BOOLEAN))
                ->values();

            if ($productos->isEmpty()) {
                throw ValidationException::withMessages(['productos' => 'Selecciona al menos un producto para devolver.']);
            }

            return DB::transaction(function () use ($request, $validated, $productos) {
                $venta = Venta::with('detalles')
                    ->lockForUpdate()
                    ->findOrFail($validated['venta_id']);

                if ($venta->estado !== 'Entregada') {
                    throw ValidationException::withMessages(['venta_id' => 'Solo se pueden devolver ventas entregadas.']);
                }

                $fechaReferencia = $venta->fecha_entrega ?? $venta->fecha;
                $diasGarantia = $venta->dias_garantia ?? 4;
                $limiteGarantia = Carbon::parse($fechaReferencia)->startOfDay()->addDays($diasGarantia)->endOfDay();
                if (now()->greaterThan($limiteGarantia)) {
                    throw ValidationException::withMessages(['venta_id' => "La garantía de devolución ({$diasGarantia} días) ha expirado."]);
                }

                $detallesVenta = $venta->detalles->keyBy('id_variante');
                $variantesIds = $productos->pluck('id_variante');
                $variantes = Variante::whereIn('id', $variantesIds)->lockForUpdate()->get()->keyBy('id');
                $usuarioId = (int) $request->user()->id;
                $montoReembolsado = 0.00;

                $rutaComprobante = null;
                if ($request->hasFile('comprobante')) {
                    $archivo = $request->file('comprobante');
                    $nombreArchivo = 'DEV-VNT-' . $venta->id . '_' . now()->format('Y-m-d_H-i-s') . '.' . $archivo->extension();
                    $rutaComprobante = $archivo->storeAs('comprobantes_devolucion', $nombreArchivo, 'public');
                }

                foreach ($productos as $producto) {
                    $detalleVenta = $detallesVenta->get((int) $producto['id_variante']);
                    $variante = $variantes->get((int) $producto['id_variante']);
                    $cantidad = (int) $producto['cantidad'];

                    if (!$detalleVenta || !$variante) {
                        throw ValidationException::withMessages(['productos' => 'Todos los productos deben pertenecer a la venta seleccionada.']);
                    }

                    $cantidadDevuelta = (int) DevolucionDetalle::where('id_variante', $variante->id)
                        ->whereHas('devolucion', fn ($query) => $query
                            ->where('origen_tipo', 'venta')
                            ->where('origen_id', $venta->id))
                        ->sum('cantidad');
                    $disponible = (int) $detalleVenta->cantidad - $cantidadDevuelta;

                    if ($cantidad > $disponible) {
                        throw ValidationException::withMessages(['productos' => "La variante {$variante->nombre_variante} solo permite devolver {$disponible} unidad(es)."]);
                    }

                    if (str_contains($validated['tipo_resolucion'], 'reembolso')) {
                        $montoReembolsado += $cantidad * (float) $detalleVenta->precio_unitario;
                    }
                }

                $devolucion = Devolucion::create([
                    'origen_tipo' => 'venta',
                    'origen_id' => $venta->id,
                    'tipo_resolucion' => $validated['tipo_resolucion'],
                    'monto_reembolsado' => $montoReembolsado,
                    'motivo' => $validated['motivo'],
                    'comprobante' => $rutaComprobante,
                ]);

                foreach ($productos as $producto) {
                    $variante = $variantes->get((int) $producto['id_variante']);
                    $detalleVenta = $detallesVenta->get($variante->id);
                    $cantidad = (int) $producto['cantidad'];
                    $stockAnterior = (int) $variante->stock;
                    $reservaAnterior = (int) $variante->reserva;
                    $cuarentenaAnterior = (int) $variante->stock_cuarentena;

                    DevolucionDetalle::create([
                        'devolucion_id' => $devolucion->id,
                        'id_variante' => $variante->id,
                        'cantidad' => $cantidad,
                    ]);

                    if ($validated['tipo_resolucion'] === 'reembolso_tienda') {
                        $variante->increment('stock', $cantidad);
                        $this->registrarMovimiento($devolucion, $variante, $usuarioId, 'Entrada', $cantidad,
                            $reservaAnterior, $reservaAnterior, $stockAnterior, $stockAnterior + $cantidad,
                            $cuarentenaAnterior, $cuarentenaAnterior, 'Retorno a stock por reembolso de Venta #' . $venta->id);
                    } elseif ($validated['tipo_resolucion'] === 'reembolso_cuarentena') {
                        $variante->increment('stock_cuarentena', $cantidad);
                        $this->registrarMovimiento($devolucion, $variante, $usuarioId, 'Entrada', $cantidad,
                            $reservaAnterior, $reservaAnterior, $stockAnterior, $stockAnterior,
                            $cuarentenaAnterior, $cuarentenaAnterior + $cantidad, 'Ingreso a cuarentena por reembolso de Venta #' . $venta->id);
                    } elseif ($validated['tipo_resolucion'] === 'cambio_tienda') {
                        $this->registrarMovimiento($devolucion, $variante, $usuarioId, 'Entrada', $cantidad,
                            $reservaAnterior, $reservaAnterior, $stockAnterior, $stockAnterior + $cantidad,
                            $cuarentenaAnterior, $cuarentenaAnterior, 'Retorno por cambio físico de Venta #' . $venta->id);
                        $this->registrarMovimiento($devolucion, $variante, $usuarioId, 'Salida', $cantidad,
                            $reservaAnterior, $reservaAnterior, $stockAnterior + $cantidad, $stockAnterior,
                            $cuarentenaAnterior, $cuarentenaAnterior, 'Reposición por cambio físico de Venta #' . $venta->id);
                    } else {
                        if ($stockAnterior < $cantidad) {
                            DB::rollBack();
                            return back()->with('error', 'Acción denegada: No hay suficiente stock en tienda para dar el producto de reemplazo. Stock actual: ' . $variante->stock);
                        }

                        $variante->update([
                            'stock' => $stockAnterior - $cantidad,
                            'stock_cuarentena' => $cuarentenaAnterior + $cantidad,
                        ]);
                        $this->registrarMovimiento($devolucion, $variante, $usuarioId, 'Entrada', $cantidad,
                            $reservaAnterior, $reservaAnterior, $stockAnterior, $stockAnterior,
                            $cuarentenaAnterior, $cuarentenaAnterior + $cantidad, 'Ingreso de producto defectuoso a cuarentena de Venta #' . $venta->id);
                        $this->registrarMovimiento($devolucion, $variante, $usuarioId, 'Salida', $cantidad,
                            $reservaAnterior, $reservaAnterior, $stockAnterior, $stockAnterior - $cantidad,
                            $cuarentenaAnterior + $cantidad, $cuarentenaAnterior + $cantidad, 'Salida de producto nuevo por cambio de Venta #' . $venta->id);
                    }
                }

                $nuevoEstado = str_contains($validated['tipo_resolucion'], 'cambio') ? 'Cambio' : 'Devolución';
                $venta->update([
                    'estado' => $nuevoEstado,
                    'observaciones' => trim(($venta->observaciones ? $venta->observaciones . PHP_EOL : '') . $nuevoEstado . ': ' . $validated['motivo']),
                ]);

                // Unificar con el flujo de comisiones según la resolución tomada
                app(\App\Services\ComisionService::class)->procesarResolucionDevolucion(
                    $devolucion,
                    $venta,
                    $validated['tipo_resolucion'],
                    $productos->toArray()
                );

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => "{$nuevoEstado} procesado correctamente. Impacto financiero: $" . number_format($montoReembolsado, 2),
                    ]);
                }

                return back()->with('success', "{$nuevoEstado} procesado correctamente. El reembolso registrado es de $" . number_format($montoReembolsado, 2));
            });
    }

    /**
     * Registra una devolución de mercancía a proveedor desde una compra.
     */
    public function storeCompra(Request $request)
    {
        $validated = $request->validate([
            'compra_id' => ['required', 'integer', 'exists:compra,id'],
            'motivo' => ['required', 'string', 'max:1000'],
            'comprobante' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'productos' => ['required', 'array', 'min:1'],
            'productos.*.id_compra_detalle' => ['required', 'integer', 'distinct', 'exists:compra_detalle,id'],
            'productos.*.ubicaciones.stock' => ['required', 'integer', 'min:0'],
            'productos.*.ubicaciones.reserva' => ['required', 'integer', 'min:0'],
            'productos.*.ubicaciones.cuarentena' => ['required', 'integer', 'min:0'],
        ]);

        $productos = collect($validated['productos'])
            ->map(function (array $producto) {
                $ubicaciones = $producto['ubicaciones'];
                return [
                    'id_compra_detalle' => (int) $producto['id_compra_detalle'],
                    'ubicaciones' => [
                        'stock' => (int) $ubicaciones['stock'],
                        'reserva' => (int) $ubicaciones['reserva'],
                        'cuarentena' => (int) $ubicaciones['cuarentena'],
                    ],
                ];
            })
            ->filter(fn (array $producto) => array_sum($producto['ubicaciones']) > 0)
            ->values();

        if ($productos->isEmpty()) {
            throw ValidationException::withMessages(['productos' => 'Selecciona al menos un producto para devolver.']);
        }

        return DB::transaction(function () use ($request, $validated, $productos) {
            $compra = Compra::with('detalles')
                ->lockForUpdate()
                ->findOrFail($validated['compra_id']);
            $detalleIds = $productos->pluck('id_compra_detalle');
            $detalles = CompraDetalle::with('variante')
                ->where('id_compra', $compra->id)
                ->whereIn('id', $detalleIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($productos as $producto) {
                $detalle = $detalles->get((int) $producto['id_compra_detalle']);
                if (!$detalle) {
                    throw ValidationException::withMessages(['productos' => 'Todos los productos deben pertenecer a la compra seleccionada.']);
                }

                $cantidad = array_sum($producto['ubicaciones']);
                $cantidadAnterior = (int) DevolucionDetalle::where('id_variante', $detalle->id_variante)
                    ->whereHas('devolucion', fn ($query) => $query->where('origen_tipo', 'compra')->where('origen_id', $compra->id))
                    ->sum('cantidad');
                $disponible = (int) $detalle->cantidad - $cantidadAnterior;

                if ($cantidad > $disponible) {
                    throw ValidationException::withMessages([
                        'productos' => "La variante {$detalle->variante?->nombre_variante} solo permite devolver {$disponible} unidad(es) de esta compra.",
                    ]);
                }

                $variante = $detalle->variante;
                if ($producto['ubicaciones']['stock'] > (int) $variante->stock
                    || $producto['ubicaciones']['reserva'] > (int) $variante->reserva
                    || $producto['ubicaciones']['cuarentena'] > (int) $variante->stock_cuarentena) {
                    throw ValidationException::withMessages(['productos' => "La distribución supera el inventario actual de {$variante->nombre_variante}."]);
                }

            }

            $rutaComprobante = null;
            if ($request->hasFile('comprobante')) {
                $archivo = $request->file('comprobante');
                $nombre = 'DEV-CMP-' . $compra->id . '_' . now()->format('Y-m-d_H-i-s') . '.' . $archivo->extension();
                $rutaComprobante = $archivo->storeAs('comprobantes_devolucion', $nombre, 'public');
            }

            $devolucion = Devolucion::create([
                'origen_tipo' => 'compra',
                'origen_id' => $compra->id,
                'tipo_resolucion' => 'pendiente',
                'estado' => 'pendiente',
                'detalles_json' => $productos->all(),
                'monto_reembolsado' => 0,
                'motivo' => $validated['motivo'],
                'comprobante' => $rutaComprobante,
            ]);

            foreach ($productos as $producto) {
                $detalle = $detalles->get((int) $producto['id_compra_detalle']);
                $cantidad = array_sum($producto['ubicaciones']);

                DevolucionDetalle::create([
                    'devolucion_id' => $devolucion->id,
                    'id_variante' => $detalle->id_variante,
                    'cantidad' => $cantidad,
                ]);
            }

            return back()->with('success', 'Ticket de devolución creado y pendiente de resolución.');
        });
    }

    public function resolverCompra(Request $request, int $id)
    {
        $validated = $request->validate([
            'resolucion_final' => ['required', 'in:cambio_fisico,reembolso,merma'],
        ]);

        return DB::transaction(function () use ($request, $validated, $id) {
            $devolucion = Devolucion::with('detalles')->lockForUpdate()->findOrFail($id);
            if ($devolucion->origen_tipo !== 'compra' || $devolucion->estado !== 'pendiente') {
                throw ValidationException::withMessages(['estado' => 'El ticket no está pendiente o no pertenece a una compra.']);
            }

            $compra = Compra::with('detalles')->lockForUpdate()->findOrFail($devolucion->origen_id);
            $usuarioId = (int) $request->user()->id;
            $monto = 0.0;

            $detallesJson = is_array($devolucion->detalles_json) ? $devolucion->detalles_json : json_decode($devolucion->detalles_json ?? '[]', true);
            if (!is_array($detallesJson) || $detallesJson === []) {
                throw ValidationException::withMessages(['productos' => 'El ticket no tiene un desglose de ubicaciones válido.']);
            }

            foreach ($detallesJson as $producto) {
                $detalleCompra = CompraDetalle::where('id_compra', $compra->id)
                    ->where('id', (int) $producto['id_compra_detalle'])
                    ->lockForUpdate()
                    ->firstOrFail();
                $variante = Variante::lockForUpdate()->findOrFail($detalleCompra->id_variante);
                $ubicaciones = $producto['ubicaciones'] ?? [];

                foreach (['stock', 'reserva', 'cuarentena'] as $ubicacion) {
                    $cantidad = (int) ($ubicaciones[$ubicacion] ?? 0);
                    if ($cantidad < 1) {
                        continue;
                    }

                    $reservaAnterior = (int) $variante->reserva;
                    $stockAnterior = (int) $variante->stock;
                    $cuarentenaAnterior = (int) $variante->stock_cuarentena;
                    $disponible = match ($ubicacion) {
                        'stock' => $stockAnterior,
                        'reserva' => $reservaAnterior,
                        default => $cuarentenaAnterior,
                    };
                    if ($cantidad > $disponible) {
                        throw ValidationException::withMessages(['productos' => "No hay suficiente existencia en {$ubicacion} para resolver el ticket."]);
                    }

                    if ($validated['resolucion_final'] === 'reembolso') {
                        $monto += $cantidad * (float) $detalleCompra->precio_unitario;
                    }

                    $reservaNueva = $reservaAnterior - ($ubicacion === 'reserva' ? $cantidad : 0);
                    $stockNuevo = $stockAnterior - ($ubicacion === 'stock' ? $cantidad : 0);
                    $cuarentenaNueva = $cuarentenaAnterior - ($ubicacion === 'cuarentena' ? $cantidad : 0);
                    $variante->update(['reserva' => $reservaNueva, 'stock' => $stockNuevo, 'stock_cuarentena' => $cuarentenaNueva]);

                    $motivoSalida = match ($validated['resolucion_final']) {
                        'cambio_fisico' => 'Retorno a proveedor - Salió de ' . ucfirst($ubicacion) . '. Cambio físico. Compra #' . $compra->id,
                        'reembolso' => 'Retorno a proveedor - Salió de ' . ucfirst($ubicacion) . '. Reembolso. Compra #' . $compra->id,
                        default => 'Retorno a proveedor - Salió de ' . ucfirst($ubicacion) . '. Merma/pérdida. Compra #' . $compra->id,
                    };
                    $this->registrarMovimiento($devolucion, $variante, $usuarioId, 'salida', $cantidad,
                        $reservaAnterior, $reservaNueva, $stockAnterior, $stockNuevo,
                        $cuarentenaAnterior, $cuarentenaNueva, $motivoSalida);

                    if ($validated['resolucion_final'] === 'cambio_fisico') {
                        $stockAntesEntrada = $variante->stock;
                        $variante->increment('stock', $cantidad);
                        $this->registrarMovimiento($devolucion, $variante, $usuarioId, 'entrada', $cantidad,
                            $reservaNueva, $reservaNueva, $stockAntesEntrada, $stockAntesEntrada + $cantidad,
                            $cuarentenaNueva, $cuarentenaNueva, 'Entrada de producto nuevo por cambio físico. Compra #' . $compra->id);
                    }
                }
            }

            $devolucion->update([
                'tipo_resolucion' => $validated['resolucion_final'],
                'monto_reembolsado' => $monto,
                'estado' => 'resuelto',
            ]);

            $totalComprado = (int) $compra->detalles->sum('cantidad');
            $totalDevuelto = (int) DevolucionDetalle::whereHas('devolucion', fn ($query) => $query
                ->where('origen_tipo', 'compra')
                ->where('origen_id', $compra->id)
                ->whereIn('estado', ['pendiente', 'resuelto']))
                ->sum('cantidad');
            $compra->update(['estado' => $totalDevuelto >= $totalComprado ? 'Devolución' : 'Devolución Parcial']);

            return back()->with('success', 'Ticket resuelto correctamente.');
        });
    }

    private function registrarMovimiento(Devolucion $devolucion, Variante $variante, int $usuarioId, string $tipo, int $cantidad, int $reservaAnterior, int $reservaNueva, int $stockAnterior, int $stockNuevo, int $cuarentenaAnterior, int $cuarentenaNueva, string $observacion): void
        {
            MovimientoBodega::create([
                'id_variante' => $variante->id,
                'devolucion_id' => $devolucion->id,
                'id_usuario' => $usuarioId,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'reserva_anterior' => $reservaAnterior,
                'reserva_nueva' => $reservaNueva,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'stock_cuarentena_anterior' => $cuarentenaAnterior,
                'stock_cuarentena_nuevo' => $cuarentenaNueva,
                'observacion' => $observacion,
            ]);
        }
}
