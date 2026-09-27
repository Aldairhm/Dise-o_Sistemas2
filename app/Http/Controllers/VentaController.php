<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Salida;
use App\Models\Variante;
use App\Models\ComisionVendedor;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    /**
     * Muestra el Dashboard Histórico de Ventas y analítica para administradores.
     */
    public function index(Request $request)
    {
        // 1. Filtros recibidos
        $desde = $request->filled('desde')
            ? $request->input('desde')
            : now()->startOfMonth()->toDateString();

        $hasta = $request->filled('hasta')
            ? $request->input('hasta')
            : now()->toDateString();

        $vendedorId = $request->input('vendedor_id');
        $productoId = $request->input('producto_id');

        // 2. Consulta base de ventas con filtros aplicados
        $query = Venta::with(['usuario', 'detalles.variante.producto'])
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta);

        if ($vendedorId) {
            $query->where('id_usuario', $vendedorId);
        }

        if ($productoId) {
            $query->whereHas('detalles.variante', function ($q) use ($productoId) {
                $q->where('id_producto', $productoId);
            });
        }

        // 3. Métricas Generales en el periodo
        $totalVentasCount = (clone $query)->count();
        $totalVentasMonto = (float) ((clone $query)->sum('total') ?? 0);

        // Ranking de productos (más y menos vendido)
        $rankingProductos = DetalleVenta::query()
            ->join('venta', 'detalleventa.id_venta', '=', 'venta.id')
            ->join('variante', 'detalleventa.id_variante', '=', 'variante.id')
            ->join('producto', 'variante.id_producto', '=', 'producto.id')
            ->whereDate('venta.fecha', '>=', $desde)
            ->whereDate('venta.fecha', '<=', $hasta)
            ->when($vendedorId, fn($q) => $q->where('venta.id_usuario', $vendedorId))
            ->select(
                'producto.id',
                'producto.nombre',
                DB::raw('SUM(detalleventa.cantidad) as total_unidades'),
                DB::raw('SUM(detalleventa.subtotal) as total_monto')
            )
            ->groupBy('producto.id', 'producto.nombre')
            ->orderByDesc('total_unidades')
            ->get();

        $productoMasVendido = $rankingProductos->first();
        $productoMenosVendido = $rankingProductos->count() > 1 ? $rankingProductos->last() : null;

        // Agrupación de ventas por día/fecha
        $ventasPorDia = (clone $query)
            ->select(
                DB::raw('DATE(fecha) as dia'),
                DB::raw('COUNT(*) as total_transacciones'),
                DB::raw('SUM(total) as monto_total')
            )
            ->groupBy(DB::raw('DATE(fecha)'))
            ->orderBy('dia', 'asc')
            ->get();

        // 4. Métricas por Producto Específico (si se seleccionó uno)
        $productoSeleccionado = $productoId ? Producto::find($productoId) : null;
        $ventasProductoPorDia = collect();
        $tendenciaProducto = null;

        if ($productoSeleccionado) {
            $ventasProductoPorDia = DetalleVenta::query()
                ->join('venta', 'detalleventa.id_venta', '=', 'venta.id')
                ->join('variante', 'detalleventa.id_variante', '=', 'variante.id')
                ->where('variante.id_producto', $productoId)
                ->whereDate('venta.fecha', '>=', $desde)
                ->whereDate('venta.fecha', '<=', $hasta)
                ->when($vendedorId, fn($q) => $q->where('venta.id_usuario', $vendedorId))
                ->select(
                    DB::raw('DATE(venta.fecha) as dia'),
                    DB::raw('SUM(detalleventa.cantidad) as unidades'),
                    DB::raw('SUM(detalleventa.subtotal) as subtotal')
                )
                ->groupBy(DB::raw('DATE(venta.fecha)'))
                ->orderBy('dia', 'asc')
                ->get();

            $unidadesActual = (int) $ventasProductoPorDia->sum('unidades');
            $montoActual = (float) $ventasProductoPorDia->sum('subtotal');

            // Periodo anterior equivalente
            $diasPeriodo = Carbon::parse($desde)->diffInDays(Carbon::parse($hasta)) + 1;
            $desdeAnterior = Carbon::parse($desde)->subDays($diasPeriodo)->toDateString();
            $hastaAnterior = Carbon::parse($desde)->subDay()->toDateString();

            $unidadesAnterior = (int) DetalleVenta::query()
                ->join('venta', 'detalleventa.id_venta', '=', 'venta.id')
                ->join('variante', 'detalleventa.id_variante', '=', 'variante.id')
                ->where('variante.id_producto', $productoId)
                ->whereDate('venta.fecha', '>=', $desdeAnterior)
                ->whereDate('venta.fecha', '<=', $hastaAnterior)
                ->when($vendedorId, fn($q) => $q->where('venta.id_usuario', $vendedorId))
                ->sum('detalleventa.cantidad');

            if ($unidadesAnterior > 0) {
                $porcentaje = (($unidadesActual - $unidadesAnterior) / $unidadesAnterior) * 100;
            } else {
                $porcentaje = $unidadesActual > 0 ? 100 : 0;
            }

            $tendenciaProducto = [
                'producto' => $productoSeleccionado,
                'unidades_actual' => $unidadesActual,
                'monto_actual' => $montoActual,
                'unidades_anterior' => $unidadesAnterior,
                'porcentaje' => round($porcentaje, 1),
                'es_positivo' => $porcentaje >= 0,
                'dias_periodo' => $diasPeriodo,
                'desde_anterior' => $desdeAnterior,
                'hasta_anterior' => $hastaAnterior,
            ];
        }

        // 5. Tabla paginada de ventas filtradas
        $ventas = (clone $query)
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        // 6. Catálogos para filtros
        $vendedores = User::where('estado', 1)->orderBy('nombre_real')->get(['id', 'nombre_real', 'username', 'rol']);
        $productos = Producto::where('estado', 1)->orderBy('nombre')->get(['id', 'nombre']);

        return view('ventas.index', compact(
            'ventas',
            'desde',
            'hasta',
            'vendedorId',
            'productoId',
            'totalVentasCount',
            'totalVentasMonto',
            'productoMasVendido',
            'productoMenosVendido',
            'ventasPorDia',
            'productoSeleccionado',
            'ventasProductoPorDia',
            'tendenciaProducto',
            'vendedores',
            'productos'
        ));
    }

    /**
     * Muestra el formulario para crear una nueva venta con el catálogo de variantes.
     */
    public function create()
    {
        $variantes = Variante::with(['producto:id,nombre', 'imagenes'])
            ->where('estado', 1)
            ->orderBy('id', 'desc')
            ->get(['id', 'id_producto', 'sku', 'nombre_variante', 'precio_venta', 'stock', 'imagen'])
            ->map(fn (Variante $variante) => [
                'id' => $variante->id,
                'producto' => $variante->producto?->nombre ?? 'Producto sin nombre',
                'variante' => $variante->nombre_variante,
                'sku' => $variante->sku ?? 'Sin SKU',
                'precio_venta' => (float) $variante->precio_venta,
                'stock' => (int) $variante->stock,
                'imagen' => ($img = $variante->imagenes->firstWhere('es_principal', 1) ?? $variante->imagenes->first())
                    ? asset('storage/' . $img->ruta_imagen)
                    : ($variante->imagen ? asset('storage/' . $variante->imagen) : null),
            ]);

        return view('ventas.create', compact('variantes'));
    }

    /**
     * Almacena una nueva venta en la base de datos de manera atómica
     * con bloqueo anti-duplicidad de 10 segundos y generación de salidas.
     */
    public function store(Request $request)
    {
        // 1. Bloqueo anti-duplicidad de 10 segundos por usuario / IP
        $userId = Auth::id() ?? $request->ip();
        $lockKey = 'venta_lock_usuario_' . $userId;

        // Cache::add() almacena el valor únicamente si no existe previamente
        $acquired = Cache::add($lockKey, true, 10);

        if (!$acquired) {
            return response()->json([
                'success' => false,
                'message' => 'Ya se está procesando una venta. Por favor espera 10 segundos antes de intentar nuevamente.',
            ], 429);
        }

        // 2. Validación exhaustiva de los datos del carrito
        $validated = $request->validate([
            'metodo_pago' => ['required', 'string', 'in:Efectivo,Transferencia Bancaria'],
            'tipo_venta' => ['nullable', 'string', 'in:Tienda,Envio,Envío'],
            'comprobante_pago' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'precio_envio' => ['nullable', 'numeric', 'min:0'],
            'direccion_entrega' => ['nullable', 'string', 'max:255'],
            'punto_referencia' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'regex:/^[267]\d{3}-\d{4}$/'],
            'fecha_salida' => ['nullable', 'string'],
            'hora_salida' => ['nullable', 'string'],
            'lineas' => ['required', 'array', 'min:1'],
            'lineas.*.id_variante' => ['required', 'integer', 'exists:variante,id'],
            'lineas.*.cantidad' => ['required', 'integer', 'min:1'],
            'lineas.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'lineas.*.costo_extra' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($validated['metodo_pago'] === 'Transferencia Bancaria' && !$request->hasFile('comprobante_pago')) {
            return response()->json([
                'success' => false,
                'message' => 'El comprobante de pago es obligatorio cuando el método de pago es Transferencia Bancaria.',
            ], 422);
        }

        try {
            // 3. Transacción de base de datos atómica
            $venta = DB::transaction(function () use ($validated, $userId, $request) {
                $lineas = collect($validated['lineas']);

                // Bloqueo pesimista de variantes para garantizar coherencia de stock ante concurrencia
                $variantes = Variante::whereIn('id', $lineas->pluck('id_variante'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // Validación de stock disponible en el servidor
                foreach ($lineas as $linea) {
                    $variante = $variantes->get($linea['id_variante']);

                    if (!$variante) {
                        throw new \Exception("La variante #{$linea['id_variante']} no fue encontrada.");
                    }

                    if ($variante->stock < $linea['cantidad']) {
                        throw new \Exception(
                            "Stock insuficiente para '{$variante->nombre_variante}'. " .
                            "Disponible: {$variante->stock}, solicitado: {$linea['cantidad']}."
                        );
                    }
                }

                // Cálculo financiero de la venta incorporando posibles costos extras por producto y costo de envío
                $subtotalGeneral = $lineas->sum(
                    fn ($l) => ((float) $l['cantidad'] * (float) $l['precio_unitario']) + max(0, (float) ($l['costo_extra'] ?? 0))
                );
                $costoEnvio = max(0, (float) ($validated['precio_envio'] ?? 0));
                $descuentoGeneral = min($subtotalGeneral + $costoEnvio, max(0, (float) ($validated['descuento'] ?? 0)));
                $totalPagar = max(0, $subtotalGeneral + $costoEnvio - $descuentoGeneral);

                $fechaVenta = now();
                $telefonoEntrega = !empty($validated['telefono']) ? trim($validated['telefono']) : null;

                // Determinar tipo de venta y estado inicial según regla de negocio
                $tipoVenta = $validated['tipo_venta'] ?? null;
                if (!$tipoVenta) {
                    if (!empty($validated['direccion_entrega']) && $validated['direccion_entrega'] !== 'Venta en mostrador / POS') {
                        $tipoVenta = 'Envio';
                    } elseif ($costoEnvio > 0) {
                        $tipoVenta = 'Envio';
                    } else {
                        $tipoVenta = 'Tienda';
                    }
                }
                if ($tipoVenta === 'Envío') {
                    $tipoVenta = 'Envio';
                }

                // Tienda -> Inmediatamente Entregada; Envio -> Pendiente
                $estadoInicial = ($tipoVenta === 'Tienda') ? 'Entregada' : 'Pendiente';
                $fechaEntregaInicial = ($tipoVenta === 'Tienda') ? $fechaVenta : null;

                // A. Crear registro principal en la tabla `venta`
                $venta = Venta::create([
                    'id_usuario' => $userId,
                    'fecha' => $fechaVenta,
                    'total' => $totalPagar,
                    'metodo_pago' => $validated['metodo_pago'],
                    'comprobante_pago' => null,
                    'telefono' => $telefonoEntrega,
                    'precio_envio' => $costoEnvio,
                    'tipo_venta' => $tipoVenta,
                    'estado' => $estadoInicial,
                    'fecha_entrega' => $fechaEntregaInicial,
                ]);

                // B. Guardar el archivo del comprobante con el nombre del día y folio de la venta
                if ($request->hasFile('comprobante_pago')) {
                    $archivo = $request->file('comprobante_pago');
                    $diaVenta = $fechaVenta->format('Y-m-d');
                    $folioVenta = 'VNT-' . str_pad($venta->id, 5, '0', STR_PAD_LEFT);
                    $extension = strtolower($archivo->getClientOriginalExtension() ?: $archivo->extension() ?: 'jpg');
                    $nombreArchivo = "{$diaVenta}_{$folioVenta}.{$extension}";

                    $rutaComprobante = $archivo->storeAs('comprobantes_pago', $nombreArchivo, 'public');
                    $venta->comprobante_pago = $rutaComprobante;
                    $venta->save();
                }

                $direccionFinal = !empty($validated['direccion_entrega'])
                    ? trim($validated['direccion_entrega'])
                    : 'Venta en mostrador / POS';

                if (!empty($validated['punto_referencia']) && $direccionFinal !== 'Venta en mostrador / POS') {
                    $direccionFinal .= ' (Ref: ' . trim($validated['punto_referencia']) . ')';
                }

                $fechaSalidaFinal = !empty($validated['fecha_salida']) ? $validated['fecha_salida'] : now()->toDateString();
                $horaSalidaFinal = !empty($validated['hora_salida']) ? $validated['hora_salida'] : now()->toTimeString();

                // C. Iterar sobre el carrito e insertar en `detalleventa` y `salida`
                foreach ($lineas as $linea) {
                    $variante = $variantes->get($linea['id_variante']);
                    $cantidad = (int) $linea['cantidad'];
                    $precioUnitario = (float) $linea['precio_unitario'];
                    $costoExtra = max(0, (float) ($linea['costo_extra'] ?? 0));
                    $subtotalBase = $cantidad * $precioUnitario;
                    $subtotalLinea = $subtotalBase + $costoExtra;

                    // 1. Guardar en detalleventa
                    DetalleVenta::create([
                        'id_venta' => $venta->id,
                        'id_variante' => $variante->id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotalLinea,
                    ]);

                    // Costos y comisiones asociados
                    $costoUnitario = (float) ($variante->costo_promedio ?? 0);
                    $costoTotal = $costoUnitario * $cantidad;
                    $comisionUnitaria = (float) ($variante->comision ?? 0);
                    $comisionTotal = $comisionUnitaria * $cantidad;

                    // 2. Generar el registro de salida asociado para control de inventario con costo_extra, precio_envio y datos de entrega
                    $salida = Salida::create([
                        'id_variante' => $variante->id,
                        'id_usuario' => $userId,
                        'cantidad' => $cantidad,
                        'fecha_salida' => $fechaSalidaFinal,
                        'hora_salida' => $horaSalidaFinal,
                        'fecha_entrega' => $fechaEntregaInicial ? $fechaSalidaFinal : null,
                        'direccion' => $direccionFinal,
                        'telefono' => $telefonoEntrega,
                        'precio_envio' => $costoEnvio,
                        'costo_extra' => $costoExtra,
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotalBase,
                        'descuento' => 0.00,
                        'total' => $subtotalLinea,
                        'costo_total_aplicado' => $costoTotal,
                        'comision_aplicada' => $comisionTotal,
                        'observaciones' => "Venta #{$venta->id}" . ($costoExtra > 0 ? " (Costo extra: $" . number_format($costoExtra, 2) . ")" : ""),
                        'estado' => $estadoInicial,
                        'fecha_cancelacion' => null,
                        'created_at' => now(),
                    ]);

                    // 3. Descontar el stock físico de la variante
                    $variante->decrement('stock', $cantidad);

                    // 4. Si la variante tiene comisión asignada, registrar en `comision_vendedor`
                    if ($comisionTotal > 0) {
                        ComisionVendedor::create([
                            'id_vendedor' => $userId,
                            'id_salida' => $salida->id,
                            'monto' => $comisionTotal,
                            'estado' => 'Pendiente',
                            'fecha_registro' => now(),
                        ]);
                    }
                }

                return $venta;
            });

            // Respuesta limpia según el tipo de petición
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Venta procesada con éxito!',
                    'id_venta' => $venta->id,
                    'total' => number_format((float) $venta->total, 2, '.', ''),
                ], 201);
            }

            return redirect()->route('ventas.create')->with('success', 'Venta registrada con éxito.');

        } catch (\Throwable $e) {
            // Liberar el bloqueo si falló para permitir reintentos
            Cache::forget($lockKey);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Error inesperado al procesar la venta.',
                ], 422);
            }

            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Actualiza el estado de una venta según la máquina de estados y reglas de negocio.
     */
    public function actualizarEstado(Request $request, string|int $id)
    {
        $venta = Venta::with(['detalles.variante', 'usuario'])->findOrFail($id);

        $validated = $request->validate([
            'estado' => ['required', 'string', 'in:Pendiente,Confirmada,En ruta,Entregada,Cancelada,Devolución'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'comprobante_paquete' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'comprobante_devolucion' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $nuevoEstado = $validated['estado'];

        // 1. Verificar si la venta está bloqueada
        if ($venta->estado_bloqueado) {
            return response()->json([
                'success' => false,
                'message' => 'Esta venta se encuentra en estado definitivo (' . $venta->estado . ') y ya no puede ser modificada.',
            ], 422);
        }

        // 2. Verificar regla de garantía en días para Devolución
        if ($venta->estado === 'Entregada') {
            if ($nuevoEstado !== 'Devolución') {
                return response()->json([
                    'success' => false,
                    'message' => 'Una venta ya entregada únicamente puede cambiar a "Devolución" dentro del plazo de garantía.',
                ], 422);
            }

            if (!$venta->puede_devolver) {
                $dias = $venta->dias_garantia;
                $limiteStr = $venta->fecha_limite_devolucion ? $venta->fecha_limite_devolucion->format('d/m/Y') : '';
                return response()->json([
                    'success' => false,
                    'message' => "El plazo de garantía de devolución ({$dias} días, vencido el {$limiteStr}) ha expirado.",
                ], 422);
            }
        }

        // 3. Verificar si el estado solicitado está en los estados permitidos
        $estadosPermitidos = $venta->estados_permitidos;
        if (!in_array($nuevoEstado, $estadosPermitidos)) {
            return response()->json([
                'success' => false,
                'message' => "Transición no permitida de '{$venta->estado}' hacia '{$nuevoEstado}'.",
            ], 422);
        }

        // 4. Validaciones específicas requeridas por estado
        if ($nuevoEstado === 'En ruta') {
            if (!$request->hasFile('comprobante_paquete')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Para cambiar al estado "En ruta" es obligatorio adjuntar la fotografía/comprobante del paquete entregado a paquetería.',
                ], 422);
            }
        }

        if ($nuevoEstado === 'Cancelada') {
            if (empty(trim($validated['observaciones'] ?? ''))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Para cancelar la venta es obligatorio ingresar el motivo en el campo observaciones.',
                ], 422);
            }
        }

        if ($nuevoEstado === 'Devolución') {
            if (empty(trim($validated['observaciones'] ?? ''))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Para procesar una devolución es obligatorio ingresar la justificación/motivo en el campo observaciones.',
                ], 422);
            }

            if (!$request->hasFile('comprobante_devolucion')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Para procesar una devolución es obligatorio adjuntar la fotografía del paquete devuelto.',
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $diaVenta = now()->format('Y-m-d');
            $folioVenta = 'VNT-' . str_pad($venta->id, 5, '0', STR_PAD_LEFT);

            // A. Guardar imagen comprobante paquete si va a En ruta
            if ($nuevoEstado === 'En ruta' && $request->hasFile('comprobante_paquete')) {
                $archivo = $request->file('comprobante_paquete');
                $extension = strtolower($archivo->getClientOriginalExtension() ?: $archivo->extension() ?: 'jpg');
                $nombreArchivo = "{$diaVenta}_{$folioVenta}_paquete.{$extension}";
                $rutaPaquete = $archivo->storeAs('comprobantes_paquete', $nombreArchivo, 'public');
                $venta->comprobante_paquete = $rutaPaquete;
            }

            // B. Guardar imagen comprobante devolución si va a Devolución
            if ($nuevoEstado === 'Devolución' && $request->hasFile('comprobante_devolucion')) {
                $archivo = $request->file('comprobante_devolucion');
                $extension = strtolower($archivo->getClientOriginalExtension() ?: $archivo->extension() ?: 'jpg');
                $nombreArchivo = "{$diaVenta}_{$folioVenta}_devolucion.{$extension}";
                $rutaDevolucion = $archivo->storeAs('comprobantes_devolucion', $nombreArchivo, 'public');
                $venta->comprobante_devolucion = $rutaDevolucion;
            }

            // C. Observaciones
            if (!empty($validated['observaciones'])) {
                $venta->observaciones = trim($validated['observaciones']);
            }

            // D. Fechas de estado
            if ($nuevoEstado === 'Entregada' && !$venta->fecha_entrega) {
                $venta->fecha_entrega = now();
            }

            if ($nuevoEstado === 'Cancelada') {
                $venta->fecha_cancelacion = now();
            }

            $venta->estado = $nuevoEstado;
            $venta->save();

            // E. Si es Cancelada o Devolución, restaurar stock de inventario y anular comisiones
            if (in_array($nuevoEstado, ['Cancelada', 'Devolución'])) {
                foreach ($venta->detalles as $detalle) {
                    if ($detalle->variante) {
                        $detalle->variante->increment('stock', (int) $detalle->cantidad);
                    }
                }

                // Anular comisiones de vendedores asociadas a las salidas de esta venta
                $salidasIds = Salida::where('observaciones', 'like', "Venta #{$venta->id}%")->pluck('id');
                if ($salidasIds->isNotEmpty()) {
                    ComisionVendedor::whereIn('id_salida', $salidasIds)
                        ->update(['estado' => 'Cancelada']);
                }
            }

            // F. Actualizar salidas asociadas para reflejar el estado actual
            $salidasUpdates = [
                'estado' => $nuevoEstado,
            ];

            if ($nuevoEstado === 'Entregada') {
                $salidasUpdates['fecha_entrega'] = now()->toDateString();
            } elseif ($nuevoEstado === 'Cancelada') {
                $salidasUpdates['fecha_cancelacion'] = now()->toDateString();
            }

            if ($venta->comprobante_paquete) {
                $salidasUpdates['comprobante_paquete'] = $venta->comprobante_paquete;
            }
            if ($venta->comprobante_devolucion) {
                $salidasUpdates['comprobante_devolucion'] = $venta->comprobante_devolucion;
            }
            if (!empty($validated['observaciones'])) {
                $salidasUpdates['observaciones'] = "Venta #{$venta->id} - {$nuevoEstado}: " . trim($validated['observaciones']);
            }

            Salida::where('observaciones', 'like', "Venta #{$venta->id}%")->update($salidasUpdates);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Estado de la venta #{$venta->id} actualizado exitosamente a \"{$nuevoEstado}\".",
                'venta' => $venta->fresh(['usuario', 'detalles.variante.producto']),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Muestra la información detallada de una venta específica (soporta JSON y vista).
     */
    public function show(string|int $id)
    {
        $venta = Venta::with(['usuario', 'detalles.variante.producto'])
            ->findOrFail($id);

        // Asociar costos extras guardados en la tabla salida
        $salidas = Salida::where('observaciones', 'like', "Venta #{$venta->id}%")
            ->get()
            ->keyBy('id_variante');

        $totalCostoExtra = 0;
        foreach ($venta->detalles as $detalle) {
            $salida = $salidas->get($detalle->id_variante);
            $costoExtra = $salida
                ? (float) $salida->costo_extra
                : max(0, (float) $detalle->subtotal - ((int) $detalle->cantidad * (float) $detalle->precio_unitario));

            $detalle->costo_extra = $costoExtra;
            $detalle->subtotal_base = (int) $detalle->cantidad * (float) $detalle->precio_unitario;
            $totalCostoExtra += $costoExtra;
        }

        $venta->total_costo_extra = $totalCostoExtra;
        $subtotalGeneral = $venta->detalles->sum('subtotal');

        $primeraSalida = $salidas->first();
        $venta->direccion_entrega = $primeraSalida?->direccion;
        $venta->fecha_salida = $primeraSalida?->fecha_salida;
        $venta->hora_salida = $primeraSalida?->hora_salida;
        $venta->telefono = $venta->telefono ?? $primeraSalida?->telefono ?? $venta->usuario?->telefono;
        $venta->precio_envio = (float) ($venta->precio_envio ?? $primeraSalida?->precio_envio ?? 0);

        $venta->descuento_aplicado = max(0, ($subtotalGeneral + $venta->precio_envio) - (float) $venta->total);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'venta' => $venta,
            ]);
        }

        return view('ventas.show', compact('venta'));
    }

    /**
     * Prepara e imprime el comprobante de venta (Ticket, Factura Comercial o Crédito Fiscal de El Salvador).
     */
    public function imprimir(Request $request, string|int $id)
    {
        $venta = Venta::with(['usuario', 'detalles.variante.producto'])
            ->findOrFail($id);

        $tipo = $request->query('tipo', 'ticket');
        if (!in_array($tipo, ['ticket', 'factura_comercial', 'credito_fiscal'])) {
            $tipo = 'ticket';
        }

        // Recuperar costos extras asociados a esta venta
        $salidas = Salida::where('observaciones', 'like', "Venta #{$venta->id}%")
            ->get()
            ->keyBy('id_variante');

        $totalCostoExtra = 0;
        foreach ($venta->detalles as $detalle) {
            $salida = $salidas->get($detalle->id_variante);
            $costoExtra = $salida
                ? (float) $salida->costo_extra
                : max(0, (float) $detalle->subtotal - ((int) $detalle->cantidad * (float) $detalle->precio_unitario));

            $detalle->costo_extra = $costoExtra;
            $detalle->subtotal_base = (int) $detalle->cantidad * (float) $detalle->precio_unitario;
            $totalCostoExtra += $costoExtra;
        }

        $venta->total_costo_extra = $totalCostoExtra;
        $subtotalGeneral = $venta->detalles->sum('subtotal');
        $venta->descuento_aplicado = max(0, $subtotalGeneral - (float) $venta->total);

        // Datos del receptor / cliente según parámetros recibidos
        $cliente = [
            'nombre' => $request->query('cliente_nombre') ?: ($request->query('cliente_razon_social') ?: 'Consumidor Final'),
            'documento' => $request->query('cliente_documento') ?: ($request->query('cliente_nit') ?: 'No especificado'),
            'nrc' => $request->query('cliente_nrc') ?: '',
            'giro' => $request->query('cliente_giro') ?: 'Comercio General',
            'direccion' => $request->query('cliente_direccion') ?: 'San Salvador, El Salvador',
            'departamento' => $request->query('cliente_departamento') ?: 'San Salvador',
            'retencion_1' => (bool) $request->query('retencion_1', false),
        ];

        // Desglose impositivo según la normativa del Ministerio de Hacienda de El Salvador
        $calculos = $this->calcularTributosElSalvador($venta, $tipo, $cliente['retencion_1']);

        return view('ventas.comprobante', compact('venta', 'tipo', 'cliente', 'calculos'));
    }

    /**
     * Aplica la normativa tributaria de El Salvador (Arts. 107, 108, 114 Código Tributario y tasa IVA 13%).
     */
    private function calcularTributosElSalvador(Venta $venta, string $tipo, bool $aplicaRetencion1): array
    {
        $totalOriginal = (float) $venta->total;
        $descuento = (float) ($venta->descuento_aplicado ?? 0);
        $lineas = [];

        if ($tipo === 'credito_fiscal') {
            // EN EL SALVADOR: En el Comprobante de Crédito Fiscal (CCF),
            // los precios unitarios y subtotales se expresan NETOS (sin IVA).
            $sumNetoGravado = 0;

            foreach ($venta->detalles as $det) {
                $subtotalConIva = (float) $det->subtotal;
                // Base neta imponible dividiendo entre 1.13 (incluye costo extra directamente en el bien)
                $subtotalNeto = round($subtotalConIva / 1.13, 2);
                $precioUnitarioNeto = $det->cantidad > 0 ? round($subtotalNeto / $det->cantidad, 4) : 0;
                $sumNetoGravado += $subtotalNeto;

                $lineas[] = [
                    'cantidad' => $det->cantidad,
                    'producto' => $det->variante?->producto?->nombre ?? 'Producto',
                    'variante' => $det->variante?->nombre_variante ?? '',
                    'sku' => $det->variante?->sku ?? '',
                    'costo_extra' => (float) ($det->costo_extra ?? 0),
                    'precio_unitario_con_iva' => $det->cantidad > 0 ? round($subtotalConIva / $det->cantidad, 2) : (float) $det->precio_unitario,
                    'precio_unitario_neto' => $precioUnitarioNeto,
                    'subtotal_neto' => $subtotalNeto,
                    'subtotal_con_iva' => $subtotalConIva,
                ];
            }

            // Descuento neto si aplica
            $descuentoNeto = $descuento > 0 ? round($descuento / 1.13, 2) : 0;
            $sumNetoGravado = max(0, $sumNetoGravado - $descuentoNeto);

            // 13% de IVA (Débito Fiscal)
            $iva13 = round($sumNetoGravado * 0.13, 2);
            $subtotalConIva = $sumNetoGravado + $iva13;

            // Retención del 1% si el cliente es Gran Contribuyente (Agente de Retención)
            $retencion1 = 0.00;
            if ($aplicaRetencion1 && $sumNetoGravado >= 100.00) {
                $retencion1 = round($sumNetoGravado * 0.01, 2);
            }

            $totalPagar = max(0, $subtotalConIva - $retencion1);

            return [
                'es_credito_fiscal' => true,
                'lineas' => $lineas,
                'ventas_no_sujetas' => 0.00,
                'ventas_exentas' => 0.00,
                'ventas_gravadas_netas' => $sumNetoGravado,
                'descuento_neto' => $descuentoNeto,
                'iva_13' => $iva13,
                'subtotal' => $subtotalConIva,
                'retencion_1' => $retencion1,
                'total_pagar' => $totalPagar,
                'monto_letras' => $this->numeroALetras($totalPagar),
            ];
        } else {
            // EN EL SALVADOR: En Ticket POS y Factura Comercial (Consumidor Final),
            // los precios de venta al consumidor ya llevan el 13% de IVA incluido (Art. 114 C.T.).
            // El costo extra se integra directamente en el precio del producto entregado al cliente.
            foreach ($venta->detalles as $det) {
                $subtotal = (float) $det->subtotal;
                $precioUnitarioEfectivo = $det->cantidad > 0 ? round($subtotal / $det->cantidad, 2) : (float) $det->precio_unitario;

                $lineas[] = [
                    'cantidad' => $det->cantidad,
                    'producto' => $det->variante?->producto?->nombre ?? 'Producto',
                    'variante' => $det->variante?->nombre_variante ?? '',
                    'sku' => $det->variante?->sku ?? '',
                    'costo_extra' => (float) ($det->costo_extra ?? 0),
                    'precio_unitario' => $precioUnitarioEfectivo,
                    'subtotal' => $subtotal,
                ];
            }

            return [
                'es_credito_fiscal' => false,
                'lineas' => $lineas,
                'subtotal_productos' => $venta->detalles->sum('subtotal_base'),
                'total_costos_extras' => (float) ($venta->total_costo_extra ?? 0),
                'precio_envio' => (float) ($venta->precio_envio ?? 0),
                'subtotal_general' => $venta->detalles->sum('subtotal'),
                'descuento' => $descuento,
                'ventas_no_sujetas' => 0.00,
                'ventas_exentas' => 0.00,
                'ventas_gravadas' => $totalOriginal,
                'total_pagar' => $totalOriginal,
                'monto_letras' => $this->numeroALetras($totalOriginal),
            ];
        }
    }

    /**
     * Convierte un monto monetario a su representación en letras en español (formato USD El Salvador).
     */
    public function numeroALetras(float $numero): string
    {
        $centavos = round(($numero - floor($numero)) * 100);
        $entero = (int) floor($numero);

        if ($entero == 0) {
            $letras = 'CERO';
        } else {
            $letras = $this->convertirEnteroALetras($entero);
        }

        $centavosStr = str_pad((string) $centavos, 2, '0', STR_PAD_LEFT);
        return trim($letras) . " DÓLARES CON {$centavosStr}/100 USD";
    }

    private function convertirEnteroALetras(int $numero): string
    {
        $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
        $decenas1 = ['DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
        $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $veintis = ['', 'VEINTIÚN', 'VEINTIDÓS', 'VEINTITRÉS', 'VEINTICUATRO', 'VEINTICINCO', 'VEINTISÉIS', 'VEINTISIETE', 'VEINTIOCHO', 'VEINTINUEVE'];
        $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

        if ($numero == 100) return 'CIEN';
        if ($numero < 10) return $unidades[$numero];
        if ($numero < 20) return $decenas1[$numero - 10];
        if ($numero < 30) {
            if ($numero == 20) return 'VEINTE';
            return $veintis[$numero - 20];
        }
        if ($numero < 100) {
            $d = (int) ($numero / 10);
            $u = $numero % 10;
            return $decenas[$d] . ($u > 0 ? ' Y ' . $unidades[$u] : '');
        }
        if ($numero < 1000) {
            $c = (int) ($numero / 100);
            $resto = $numero % 100;
            return $centenas[$c] . ($resto > 0 ? ' ' . $this->convertirEnteroALetras($resto) : '');
        }
        if ($numero < 1000000) {
            $miles = (int) ($numero / 1000);
            $resto = $numero % 1000;
            $milesStr = ($miles == 1) ? 'MIL' : $this->convertirEnteroALetras($miles) . ' MIL';
            return $milesStr . ($resto > 0 ? ' ' . $this->convertirEnteroALetras($resto) : '');
        }
        if ($numero < 1000000000) {
            $millones = (int) ($numero / 1000000);
            $resto = $numero % 1000000;
            $millonesStr = ($millones == 1) ? 'UN MILLÓN' : $this->convertirEnteroALetras($millones) . ' MILLONES';
            return $millonesStr . ($resto > 0 ? ' ' . $this->convertirEnteroALetras($resto) : '');
        }

        return (string) $numero;
    }
}
