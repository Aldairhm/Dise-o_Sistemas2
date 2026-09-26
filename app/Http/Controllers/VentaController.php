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
            'metodo_pago' => ['required', 'string', 'max:50'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'lineas' => ['required', 'array', 'min:1'],
            'lineas.*.id_variante' => ['required', 'integer', 'exists:variante,id'],
            'lineas.*.cantidad' => ['required', 'integer', 'min:1'],
            'lineas.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'lineas.*.costo_extra' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            // 3. Transacción de base de datos atómica
            $venta = DB::transaction(function () use ($validated, $userId) {
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

                // Cálculo financiero de la venta incorporando posibles costos extras por producto
                $subtotalGeneral = $lineas->sum(
                    fn ($l) => ((float) $l['cantidad'] * (float) $l['precio_unitario']) + max(0, (float) ($l['costo_extra'] ?? 0))
                );
                $descuentoGeneral = min($subtotalGeneral, max(0, (float) ($validated['descuento'] ?? 0)));
                $totalPagar = max(0, $subtotalGeneral - $descuentoGeneral);

                // A. Crear registro principal en la tabla `venta`
                $venta = Venta::create([
                    'id_usuario' => $userId,
                    'fecha' => now(),
                    'total' => $totalPagar,
                    'metodo_pago' => $validated['metodo_pago'],
                ]);

                // B. Iterar sobre el carrito e insertar en `detalleventa` y `salida`
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

                    // 2. Generar el registro de salida asociado para control de inventario con costo_extra
                    $salida = Salida::create([
                        'id_variante' => $variante->id,
                        'id_usuario' => $userId,
                        'cantidad' => $cantidad,
                        'fecha_salida' => now()->toDateString(),
                        'hora_salida' => now()->toTimeString(),
                        'fecha_entrega' => now()->toDateString(),
                        'direccion' => 'Venta en mostrador / POS',
                        'precio_envio' => 0.00,
                        'costo_extra' => $costoExtra,
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotalBase,
                        'descuento' => 0.00,
                        'total' => $subtotalLinea,
                        'costo_total_aplicado' => $costoTotal,
                        'comision_aplicada' => $comisionTotal,
                        'observaciones' => "Venta #{$venta->id}" . ($costoExtra > 0 ? " (Costo extra: $" . number_format($costoExtra, 2) . ")" : ""),
                        'estado' => 'Entregado',
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
        $venta->descuento_aplicado = max(0, $subtotalGeneral - (float) $venta->total);

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
