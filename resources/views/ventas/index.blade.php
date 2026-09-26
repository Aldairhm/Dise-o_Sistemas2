<x-app title="Dashboard Histórico de Ventas | AXStore">
    <div class="max-w-[1440px] mx-auto space-y-6" x-data="gestorVentasIndex()">

        <!-- HEADER DEL DASHBOARD -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600 mb-0.5">Analítica Corporativa</p>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Dashboard de Ventas</h1>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN RÁPIDA -->
            <div class="flex items-center gap-3">
                <a href="{{ route('ventas.create') }}" 
                   class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-lg shadow-blue-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-xs">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span>NUEVA VENTA</span>
                </a>
            </div>
        </div>

        <!-- SUB-NAV / PESTAÑAS -->
        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones de ventas">
            <a href="{{ route('ventas.index') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard Histórico</span>
            </a>
            <a href="{{ route('ventas.create') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-cash-register text-slate-400"></i>
                <span>Terminal de Ventas (Carrito)</span>
            </a>
        </nav>

        <!-- 1. SECCIÓN SUPERIOR: FILTROS GLOBALES -->
        <section class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <form method="GET" action="{{ route('ventas.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                
                <!-- FECHA DESDE -->
                <div class="lg:col-span-2">
                    <label for="desde" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-calendar-day text-slate-400 mr-1"></i> Desde
                    </label>
                    <input 
                        type="date" 
                        name="desde" 
                        id="desde" 
                        value="{{ $desde }}" 
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all"
                    >
                </div>

                <!-- FECHA HASTA -->
                <div class="lg:col-span-2">
                    <label for="hasta" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-calendar-check text-slate-400 mr-1"></i> Hasta
                    </label>
                    <input 
                        type="date" 
                        name="hasta" 
                        id="hasta" 
                        value="{{ $hasta }}" 
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all"
                    >
                </div>

                <!-- SELECT VENDEDOR -->
                <div class="lg:col-span-3">
                    <label for="vendedor_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-user-tie text-slate-400 mr-1"></i> Vendedor
                    </label>
                    <select 
                        name="vendedor_id" 
                        id="vendedor_id" 
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all"
                    >
                        <option value="">Todos los vendedores</option>
                        @foreach($vendedores as $v)
                            <option value="{{ $v->id }}" {{ (string)$vendedorId === (string)$v->id ? 'selected' : '' }}>
                                {{ $v->nombre_real ?: $v->username }} ({{ ucfirst($v->rol) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- SELECT PRODUCTO ESPECÍFICO (MÉTRICAS DETALLADAS) -->
                <div class="lg:col-span-3">
                    <label for="producto_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-box text-slate-400 mr-1"></i> Producto Específico
                    </label>
                    <select 
                        name="producto_id" 
                        id="producto_id" 
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all"
                    >
                        <option value="">Todos los productos</option>
                        @foreach($productos as $p)
                            <option value="{{ $p->id }}" {{ (string)$productoId === (string)$p->id ? 'selected' : '' }}>
                                {{ $p->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- BOTONES DE FILTRADO -->
                <div class="lg:col-span-2 flex items-center gap-2">
                    <button 
                        type="submit" 
                        class="flex-1 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-3 text-xs transition-all shadow-md shadow-blue-600/15 flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                        <i class="fas fa-filter"></i>
                        <span>Filtrar</span>
                    </button>
                    @if(request()->hasAny(['desde', 'hasta', 'vendedor_id', 'producto_id']))
                        <a 
                            href="{{ route('ventas.index') }}" 
                            class="rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold py-2 px-3 text-xs transition-colors flex items-center justify-center"
                            title="Limpiar filtros"
                        >
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>

            </form>
        </section>

        <!-- 2. SECCIÓN MEDIA: KPIS Y TARJETAS DE MÉTRICAS -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- KPI 1: TOTAL DE VENTAS -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Ventas</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-900 leading-none">
                        ${{ number_format($totalVentasMonto, 2) }}
                    </h3>
                    <p class="text-xs font-bold text-slate-400 mt-2 flex items-center gap-1.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span>
                        <span>{{ number_format($totalVentasCount) }} transacciones</span>
                    </p>
                </div>
            </div>

            <!-- KPI 2: PRODUCTO MÁS VENDIDO -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Más Vendido</span>
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
                <div>
                    @if($productoMasVendido)
                        <h3 class="text-base font-black text-slate-900 truncate" title="{{ $productoMasVendido->nombre }}">
                            {{ $productoMasVendido->nombre }}
                        </h3>
                        <p class="text-xs font-bold text-amber-600 mt-1">
                            {{ number_format($productoMasVendido->total_unidades) }} unidades vendidas
                        </p>
                    @else
                        <h3 class="text-sm font-bold text-slate-400">Sin datos en el periodo</h3>
                        <p class="text-xs text-slate-400 mt-1">0 unidades</p>
                    @endif
                </div>
            </div>

            <!-- KPI 3: PRODUCTO MENOS VENDIDO -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Menos Vendido</span>
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-arrow-down-short-wide"></i>
                    </div>
                </div>
                <div>
                    @if($productoMenosVendido)
                        <h3 class="text-base font-black text-slate-900 truncate" title="{{ $productoMenosVendido->nombre }}">
                            {{ $productoMenosVendido->nombre }}
                        </h3>
                        <p class="text-xs font-bold text-rose-500 mt-1">
                            {{ number_format($productoMenosVendido->total_unidades) }} unidades vendidas
                        </p>
                    @else
                        <h3 class="text-sm font-bold text-slate-400">Sin variación</h3>
                        <p class="text-xs text-slate-400 mt-1">Registros insuficientes</p>
                    @endif
                </div>
            </div>

            <!-- KPI 4: TENDENCIA O TICKET PROMEDIO -->
            @if($tendenciaProducto)
                <!-- KPI ESPECÍFICO DE PRODUCTO CON TENDENCIA (%) -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between hover:border-blue-300 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tendencia Producto</span>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-black {{ $tendenciaProducto['es_positivo'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                <i class="fas {{ $tendenciaProducto['es_positivo'] ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                                <span>{{ $tendenciaProducto['porcentaje'] >= 0 ? '+' : '' }}{{ $tendenciaProducto['porcentaje'] }}%</span>
                            </span>
                            <div class="w-9 h-9 rounded-xl {{ $tendenciaProducto['es_positivo'] ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center text-sm font-bold">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900 truncate" title="{{ $tendenciaProducto['producto']->nombre }}">
                            {{ $tendenciaProducto['producto']->nombre }}
                        </h3>
                        <div class="flex items-center justify-between mt-1 text-xs">
                            <span class="font-bold text-slate-700">{{ $tendenciaProducto['unidades_actual'] }} uds (${{ number_format($tendenciaProducto['monto_actual'], 2) }})</span>
                            <span class="text-[11px] font-semibold text-slate-400">vs {{ $tendenciaProducto['unidades_anterior'] }} previas</span>
                        </div>
                    </div>
                </div>
            @else
                <!-- TICKET PROMEDIO GENERAL -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Ticket Promedio</span>
                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold">
                            <i class="fas fa-calculator"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 leading-none">
                            ${{ number_format($totalVentasCount > 0 ? $totalVentasMonto / $totalVentasCount : 0, 2) }}
                        </h3>
                        <p class="text-xs font-bold text-slate-400 mt-2">
                            Promedio por cliente
                        </p>
                    </div>
                </div>
            @endif

        </section>

        <!-- AGRUPACIÓN DIARIA / GRÁFICO RESUMEN -->
        @if($ventasPorDia->count() > 0)
        <section class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-sm font-black text-slate-900">
                        {{ $productoSeleccionado ? 'Comportamiento Diario: ' . $productoSeleccionado->nombre : 'Comportamiento de Ventas por Fecha' }}
                    </h3>
                    <p class="text-xs text-slate-500">Desglose de transacciones y montos acumulados por cada día.</p>
                </div>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1 rounded-lg self-start sm:self-auto">
                    {{ $ventasPorDia->count() }} días con actividad
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">
                @foreach($productoSeleccionado && $ventasProductoPorDia->count() > 0 ? $ventasProductoPorDia : $ventasPorDia as $item)
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3 hover:border-blue-300 hover:bg-white transition-all text-center">
                        <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">
                            {{ \Carbon\Carbon::parse($item->dia)->format('d M Y') }}
                        </span>
                        <p class="text-sm font-black text-slate-900 leading-tight">
                            ${{ number_format($item->monto_total ?? $item->total_monto ?? $item->subtotal ?? 0, 2) }}
                        </p>
                        <span class="text-[11px] font-bold text-blue-600 block mt-0.5">
                            {{ $item->total_transacciones ?? $item->unidades ?? $item->total_unidades ?? 0 }} {{ isset($item->total_transacciones) ? 'ventas' : 'uds' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- 3. SECCIÓN INFERIOR: TABLA DE HISTORIAL DE VENTAS -->
        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 bg-slate-50/60">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                        <i class="fas fa-list-check"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-900">Historial de Transacciones</h2>
                        <p class="text-xs text-slate-500">Listado cronológico de ventas registradas en el periodo seleccionado</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 px-3 py-1 rounded-lg">
                    {{ $ventas->total() }} registros totales
                </span>
            </div>

            <!-- TABLA -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5 w-24">Folio</th>
                            <th class="px-4 py-3.5">Fecha y Hora</th>
                            <th class="px-4 py-3.5">Vendedor</th>
                            <th class="px-4 py-3.5">Método de Pago</th>
                            <th class="px-4 py-3.5 text-center">Ítems</th>
                            <th class="px-5 py-3.5 text-right w-28">Total</th>
                            <th class="px-5 py-3.5 text-right w-44">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($ventas as $venta)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5 font-bold text-slate-900">
                                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[11px] font-mono">
                                        #VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-slate-600">
                                    <p class="font-bold text-slate-800">
                                        {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}
                                    </p>
                                    <span class="text-[10px] text-slate-400">
                                        {{ \Carbon\Carbon::parse($venta->fecha)->format('h:i A') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 font-bold text-[10px] flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($venta->usuario?->nombre_real ?? $venta->usuario?->username ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-slate-800 truncate max-w-[150px]" title="{{ $venta->usuario?->nombre_real ?? $venta->usuario?->username }}">
                                            {{ $venta->usuario?->nombre_real ?? $venta->usuario?->username ?? 'Usuario no asignado' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $venta->metodo_pago === 'Efectivo' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($venta->metodo_pago === 'Transferencia Bancaria' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-blue-50 text-blue-700 border-blue-200') }}">
                                        <i class="fas {{ $venta->metodo_pago === 'Efectivo' ? 'fa-money-bill-wave' : ($venta->metodo_pago === 'Transferencia Bancaria' ? 'fa-building-columns' : 'fa-credit-card') }} text-[10px]"></i>
                                        <span>{{ $venta->metodo_pago }}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-block bg-slate-100 text-slate-700 font-bold px-2 py-0.5 rounded text-[11px]" title="{{ $venta->detalles->count() }} variantes distintas">
                                        {{ $venta->detalles->sum('cantidad') }} uds
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right font-black text-slate-900 text-sm">
                                    ${{ number_format($venta->total, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- BOTÓN VER DETALLE -->
                                        <button 
                                            type="button" 
                                            @click="abrirDetalle({{ $venta->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer"
                                            title="Ver detalle completo de la venta"
                                        >
                                            <i class="fas fa-eye text-slate-500"></i>
                                            <span>Detalle</span>
                                        </button>

                                        <!-- BOTÓN IMPRIMIR COMPROBANTE -->
                                        <button 
                                            type="button" 
                                            @click="abrirModalImpresion({{ $venta->id }}, '{{ $venta->metodo_pago }}', {{ (float) $venta->total }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-600 hover:text-white border border-emerald-200 hover:border-emerald-600 transition-all shadow-sm cursor-pointer"
                                            title="Imprimir ticket o factura comercial/tributaria"
                                        >
                                            <i class="fas fa-print"></i>
                                            <span>Imprimir</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center text-slate-400">
                                    <div class="w-14 h-14 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-3 border border-slate-100">
                                        <i class="fas fa-folder-open text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-600">No se encontraron ventas registradas</p>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Prueba ajustando los filtros de fecha o vendedor, o registra tu primera venta en el sistema.
                                    </p>
                                    <div class="mt-4">
                                        <a href="{{ route('ventas.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 text-xs font-bold transition-all shadow-sm">
                                            <i class="fas fa-plus"></i> Registrar Nueva Venta
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINACIÓN -->
            @if($ventas->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $ventas->links() }}
                </div>
            @endif
        </section>

        <!-- ========================================================================= -->
        <!-- MODAL 1: DETALLE COMPLETO DE LA VENTA                                     -->
        <!-- ========================================================================= -->
        <div 
            x-show="modalDetalleAbierto" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <!-- Backdrop oscuro con blur idéntico al sistema de modales -->
            <div 
                x-show="modalDetalleAbierto"
                x-transition.opacity
                class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                @click="cerrarDetalle()"
            ></div>

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
                <div 
                    x-show="modalDetalleAbierto"
                    x-transition
                    @click.away="cerrarDetalle()"
                    class="relative z-10 w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-gray-100 flex flex-col my-8 max-h-[90vh]"
                >
                    <!-- CABECERA AZUL UNIFICADA -->
                    <div class="bg-blue-600 px-6 py-4.5 flex items-center justify-between text-white shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white text-sm shadow-xs">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black tracking-tight text-white flex items-center gap-2">
                                    <span>Detalle de Venta</span>
                                    <span class="font-mono text-blue-200 text-sm" x-text="ventaSeleccionada ? '#VNT-' + String(ventaSeleccionada.id).padStart(5, '0') : ''"></span>
                                    <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full ml-1">Completada</span>
                                </h3>
                                <p class="text-xs text-blue-100 mt-0.5" x-show="ventaSeleccionada">
                                    Emitida el <span x-text="formatearFecha(ventaSeleccionada ? ventaSeleccionada.fecha : null)"></span> • Atendido por <span class="font-bold text-white" x-text="ventaSeleccionada?.usuario?.nombre_real || ventaSeleccionada?.usuario?.username || 'Josue'"></span>
                                </p>
                            </div>
                        </div>

                        <!-- Botón de Cerrar unificado -->
                        <button 
                            type="button" 
                            @click="cerrarDetalle()" 
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center text-white transition-colors cursor-pointer"
                            title="Cerrar ventana"
                        >
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>

                    <!-- CUERPO DEL MODAL (SPINNER O DATOS) -->
                    <div class="p-6 sm:p-8 overflow-y-auto space-y-6 flex-1 custom-scrollbar">
                        
                        <!-- ESTADO CARGANDO -->
                        <div x-show="cargandoDetalle" class="py-16 text-center text-slate-400 space-y-3">
                            <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
                            <p class="text-xs font-bold text-slate-600">Cargando desglose de la venta...</p>
                        </div>

                        <!-- CONTENIDO DE LA VENTA -->
                        <template x-if="!cargandoDetalle && ventaSeleccionada">
                            <div class="space-y-6">
                                
                                <!-- TARJETAS RESUMEN DE LA TRANSACCIÓN -->
                                <div>
                                    <h4 class="text-[11px] font-black uppercase tracking-wider text-slate-500 mb-3">
                                        INFORMACIÓN DE LA VENTA
                                    </h4>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                        <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5 shadow-2xs">
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Método de Pago</span>
                                            <span class="text-xs font-black text-slate-800" x-text="ventaSeleccionada.metodo_pago"></span>
                                        </div>
                                        <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5 shadow-2xs">
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Total Artículos</span>
                                            <span class="text-xs font-black text-slate-800" x-text="totalUnidadesVenta(ventaSeleccionada) + ' uds'"></span>
                                        </div>
                                        <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5 shadow-2xs">
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Costos Extras</span>
                                            <span class="text-xs font-black" :class="Number(ventaSeleccionada.total_costo_extra || 0) > 0 ? 'text-amber-700' : 'text-slate-500'" x-text="'+' + moneda(ventaSeleccionada.total_costo_extra || 0)"></span>
                                        </div>
                                        <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-xl p-3.5 shadow-2xs">
                                            <span class="text-[10px] uppercase font-bold text-emerald-600 block mb-1">Total Cobrado</span>
                                            <span class="text-base font-black text-emerald-700" x-text="moneda(ventaSeleccionada.total)"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- TABLA DE ARTÍCULOS VENDIDOS -->
                                <div>
                                    <h4 class="text-[11px] font-black uppercase tracking-wider text-slate-500 mb-3">
                                        ARTÍCULOS Y VARIANTES
                                    </h4>
                                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
                                        <table class="w-full text-left text-xs">
                                            <thead class="bg-slate-50 text-[10px] uppercase font-bold tracking-wider text-slate-500 border-b border-slate-200">
                                                <tr>
                                                    <th class="px-4 py-3">Producto</th>
                                                    <th class="px-2 py-3 text-center w-16">Cant.</th>
                                                    <th class="px-3 py-3 text-right w-24">P. Unit.</th>
                                                    <th class="px-3 py-3 text-right w-24">Costo Extra</th>
                                                    <th class="px-4 py-3 text-right w-28">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100 bg-white">
                                                <template x-for="item in ventaSeleccionada.detalles" :key="item.id">
                                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                                        <td class="px-4 py-3">
                                                            <p class="font-bold text-slate-900" x-text="item.variante?.producto?.nombre || 'Producto'"></p>
                                                            <p class="text-[11px] text-slate-500 font-medium" x-text="item.variante?.nombre_variante || 'Variante'"></p>
                                                            <span class="text-[9px] font-mono text-slate-400" x-text="'SKU: ' + (item.variante?.sku || 'Sin SKU')"></span>
                                                        </td>
                                                        <td class="px-2 py-3 text-center font-bold text-slate-800" x-text="item.cantidad"></td>
                                                        <td class="px-3 py-3 text-right font-medium text-slate-700" x-text="moneda(item.precio_unitario)"></td>
                                                        <td class="px-3 py-3 text-right">
                                                            <template x-if="Number(item.costo_extra || 0) > 0">
                                                                <span class="inline-block text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md" x-text="'+' + moneda(item.costo_extra)"></span>
                                                            </template>
                                                            <template x-if="!Number(item.costo_extra || 0)">
                                                                <span class="text-slate-400">$0.00</span>
                                                            </template>
                                                        </td>
                                                        <td class="px-4 py-3 text-right font-black text-slate-900" x-text="moneda(item.subtotal)"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- DESGLOSE ECONÓMICO FINAL -->
                                <div class="bg-slate-50/80 rounded-xl p-4.5 border border-slate-200 text-xs space-y-2 max-w-sm ml-auto shadow-2xs">
                                    <div class="flex justify-between text-slate-600">
                                        <span class="font-medium">Subtotal de productos:</span>
                                        <span class="font-bold text-slate-800" x-text="moneda(calcularSubtotalBase(ventaSeleccionada))"></span>
                                    </div>
                                    <template x-if="Number(ventaSeleccionada.total_costo_extra || 0) > 0">
                                        <div class="flex justify-between text-amber-700 font-bold">
                                            <span>Costos extras aplicados:</span>
                                            <span x-text="'+' + moneda(ventaSeleccionada.total_costo_extra)"></span>
                                        </div>
                                    </template>
                                    <template x-if="Number(ventaSeleccionada.descuento_aplicado || 0) > 0">
                                        <div class="flex justify-between text-emerald-600 font-bold">
                                            <span>Descuento aplicado:</span>
                                            <span x-text="'-' + moneda(ventaSeleccionada.descuento_aplicado)"></span>
                                        </div>
                                    </template>
                                    <div class="flex justify-between items-baseline pt-2.5 border-t border-slate-200 text-slate-900 font-black">
                                        <span class="text-xs uppercase tracking-wider text-slate-700">Total a Pagar:</span>
                                        <span class="text-xl text-emerald-600 font-black" x-text="moneda(ventaSeleccionada.total)"></span>
                                    </div>
                                </div>

                            </div>
                        </template>
                    </div>

                    <!-- PIE DEL MODAL CON ACCIONES (Estilo unificado) -->
                    <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl">
                        <button 
                            type="button" 
                            @click="cerrarDetalle()"
                            class="px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer"
                        >
                            Cerrar
                        </button>

                        <button 
                            type="button" 
                            x-show="ventaSeleccionada"
                            @click="abrirModalImpresionDesdeDetalle()"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-slate-900 hover:bg-slate-800 shadow-slate-900/20"
                        >
                            <i class="fas fa-print text-xs"></i>
                            <span>Imprimir Comprobante (Ticket / Factura)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 2: SELECTOR DE COMPROBANTE E IMPRESIÓN (LEYES DE EL SALVADOR)      -->
        <!-- ========================================================================= -->
        <div 
            x-show="modalImprimirAbierto" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <!-- Backdrop oscuro con blur idéntico al sistema -->
            <div 
                x-show="modalImprimirAbierto"
                x-transition.opacity
                class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                @click="cerrarModalImpresion()"
            ></div>

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
                <div 
                    x-show="modalImprimirAbierto"
                    x-transition
                    @click.away="cerrarModalImpresion()"
                    class="relative z-10 w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-gray-100 flex flex-col my-8 max-h-[90vh]"
                >
                    <!-- CABECERA AZUL UNIFICADA -->
                    <div class="bg-blue-600 px-6 py-4.5 flex items-center justify-between text-white shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white text-sm shadow-xs">
                                <i class="fas fa-print"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black tracking-tight text-white flex items-center gap-2">
                                    <span>Generar Comprobante de Venta</span>
                                    <span class="font-mono text-blue-200 text-sm" x-text="ventaImprimirId ? '#VNT-' + String(ventaImprimirId).padStart(5, '0') : ''"></span>
                                </h3>
                                <p class="text-xs text-blue-100 mt-0.5">
                                    Emisión de documento legal bajo Normativa Tributaria de El Salvador
                                </p>
                            </div>
                        </div>

                        <button 
                            type="button" 
                            @click="cerrarModalImpresion()"
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center text-white transition-colors cursor-pointer"
                            title="Cerrar ventana"
                        >
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>

                    <!-- CUERPO CON PREGUNTAS Y OPCIONES TRIBUTARIAS -->
                    <div class="p-6 sm:p-8 overflow-y-auto space-y-6 flex-1 text-xs custom-scrollbar">
                        
                        <div>
                            <h4 class="text-[11px] font-black uppercase tracking-wider text-slate-500 mb-3">
                                1. SELECCIONA EL TIPO DE DOCUMENTO A EMITIR
                            </h4>
                            
                            <!-- TARJETAS SELECTORAS -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                
                                <!-- OPCIÓN 1: TICKET POS -->
                                <div 
                                    @click="tipoComprobante = 'ticket'"
                                    :class="tipoComprobante === 'ticket' ? 'border-blue-600 bg-blue-50/50 ring-2 ring-blue-600/20' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50'"
                                    class="border rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between shadow-2xs"
                                >
                                    <div>
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center mb-2.5 font-bold">
                                            <i class="fas fa-receipt text-xs"></i>
                                        </div>
                                        <h5 class="font-black text-slate-900 text-xs">Ticket de Caja</h5>
                                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">
                                            Tirilla térmica 80mm/58mm. Entrega rápida a consumidor final.
                                        </p>
                                    </div>
                                    <span class="text-[10px] font-bold text-blue-700 mt-3 block">IVA 13% incluido</span>
                                </div>

                                <!-- OPCIÓN 2: FACTURA COMERCIAL -->
                                <div 
                                    @click="tipoComprobante = 'factura_comercial'"
                                    :class="tipoComprobante === 'factura_comercial' ? 'border-blue-600 bg-blue-50/50 ring-2 ring-blue-600/20' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50'"
                                    class="border rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between shadow-2xs"
                                >
                                    <div>
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center mb-2.5 font-bold">
                                            <i class="fas fa-file-invoice text-xs"></i>
                                        </div>
                                        <h5 class="font-black text-slate-900 text-xs">Factura Comercial</h5>
                                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">
                                            Consumidor Final (DTE-01). Formato membretado formal.
                                        </p>
                                    </div>
                                    <span class="text-[10px] font-bold text-blue-700 mt-3 block">Precios con IVA incluido</span>
                                </div>

                                <!-- OPCIÓN 3: FACTURA TRIBUTARIA (CRÉDITO FISCAL) -->
                                <div 
                                    @click="tipoComprobante = 'credito_fiscal'"
                                    :class="tipoComprobante === 'credito_fiscal' ? 'border-purple-600 bg-purple-50/50 ring-2 ring-purple-600/20' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50'"
                                    class="border rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between shadow-2xs"
                                >
                                    <div>
                                        <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center mb-2.5 font-bold">
                                            <i class="fas fa-building-columns text-xs"></i>
                                        </div>
                                        <h5 class="font-black text-slate-900 text-xs">Factura Tributaria</h5>
                                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">
                                            Crédito Fiscal (CCF). Exclusivo para empresas contribuyentes.
                                        </p>
                                    </div>
                                    <span class="text-[10px] font-bold text-purple-700 mt-3 block">Desglose neto + 13% IVA</span>
                                </div>

                            </div>
                        </div>

                        <!-- 2. FORMULARIO DINÁMICO SEGÚN TIPO SELECCIONADO -->
                        
                        <!-- CASO TICKET -->
                        <div x-show="tipoComprobante === 'ticket'" class="p-4 bg-slate-50/80 rounded-xl border border-slate-200">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-circle-info text-blue-500 mt-0.5"></i>
                                <div>
                                    <p class="font-bold text-slate-800">Impresión en formato tirilla térmica</p>
                                    <p class="text-slate-500 text-[11px] mt-0.5">
                                        No requiere ingresar datos fiscales del cliente. Se emitirá a nombre de "Consumidor Final" cumpliendo con los requisitos de tiquete de punto de venta en El Salvador.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- CASO FACTURA COMERCIAL -->
                        <div x-show="tipoComprobante === 'factura_comercial'" class="space-y-4 p-4.5 bg-slate-50/80 rounded-xl border border-slate-200">
                            <h4 class="text-[11px] font-black uppercase tracking-wider text-slate-500">
                                DATOS DEL CLIENTE (OPCIONALES PARA CONSUMIDOR FINAL)
                            </h4>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nombre o Razón del Cliente:</label>
                                    <input 
                                        type="text" 
                                        x-model="clienteNombreComercial"
                                        placeholder="Ej. Juan Pérez (o Consumidor Final)"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all bg-white"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">DUI o NIT:</label>
                                    <input 
                                        type="text" 
                                        x-model="clienteDuiNitComercial"
                                        placeholder="00000000-0"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all bg-white font-mono"
                                    >
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Dirección del Cliente:</label>
                                <input 
                                    type="text" 
                                    x-model="clienteDireccionComercial"
                                    placeholder="San Salvador, El Salvador"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all bg-white"
                                >
                            </div>
                        </div>

                        <!-- CASO FACTURA TRIBUTARIA (CRÉDITO FISCAL - LEYES DE EL SALVADOR) -->
                        <div x-show="tipoComprobante === 'credito_fiscal'" class="space-y-4 p-4.5 bg-purple-50/40 rounded-xl border border-purple-200">
                            <div class="flex items-start gap-2 bg-purple-100/70 p-3 rounded-xl text-purple-950 text-[11px]">
                                <i class="fas fa-scale-balanced text-purple-700 text-sm mt-0.5 flex-shrink-0"></i>
                                <div>
                                    <span class="font-bold">Código Tributario de El Salvador (Arts. 107 y 108):</span>
                                    <p class="text-[10px] text-purple-900 mt-0.5 leading-tight">
                                        El Comprobante de Crédito Fiscal requiere obligatoriamente el NRC, NIT, Razón Social y Giro del contribuyente. Los precios de venta se desglosarán netos calculando el 13% de IVA explícitamente.
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Razón Social / Nombre Contribuyente <span class="text-rose-500 font-bold">*</span>:
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="clienteRazonSocialCCF"
                                        placeholder="Ej. Distribuidora Salvadoreña S.A. de C.V."
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all"
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        N° de Registro de Contribuyente (NRC) <span class="text-rose-500 font-bold">*</span>:
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="clienteNrcCCF"
                                        placeholder="Ej. 123456-7"
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 font-mono placeholder:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all"
                                    >
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        NIT / DUI del Contribuyente <span class="text-rose-500 font-bold">*</span>:
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="clienteNitCCF"
                                        placeholder="0614-010190-101-1"
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 font-mono placeholder:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all"
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Giro o Actividad Económica <span class="text-rose-500 font-bold">*</span>:
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="clienteGiroCCF"
                                        placeholder="Ej. Servicios de Transporte / Taller"
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all"
                                    >
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Dirección Comercial:</label>
                                    <input 
                                        type="text" 
                                        x-model="clienteDireccionCCF"
                                        placeholder="Colonia Escalón, San Salvador"
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all"
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Departamento:</label>
                                    <select 
                                        x-model="clienteDepartamentoCCF"
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all"
                                    >
                                        <option value="San Salvador">San Salvador</option>
                                        <option value="La Libertad">La Libertad</option>
                                        <option value="Santa Ana">Santa Ana</option>
                                        <option value="San Miguel">San Miguel</option>
                                        <option value="Sonsonate">Sonsonate</option>
                                        <option value="Ahuachapán">Ahuachapán</option>
                                        <option value="Usulután">Usulután</option>
                                        <option value="La Paz">La Paz</option>
                                        <option value="Chalatenango">Chalatenango</option>
                                        <option value="Cuscatlán">Cuscatlán</option>
                                        <option value="Morazán">Morazán</option>
                                        <option value="San Vicente">San Vicente</option>
                                        <option value="Cabañas">Cabañas</option>
                                        <option value="La Unión">La Unión</option>
                                    </select>
                                </div>
                            </div>

                            <!-- CHECKBOX RETENCIÓN 1% GRAN CONTRIBUYENTE -->
                            <div class="pt-1">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        x-model="aplicaRetencion1"
                                        class="rounded border-purple-300 text-purple-600 focus:ring-purple-500"
                                    >
                                    <span class="text-[11px] font-bold text-slate-700">
                                        Aplicar Retención del 1% de IVA (Cliente es Gran Contribuyente / Agente de Retención)
                                    </span>
                                </label>
                            </div>
                        </div>

                    </div>

                    <!-- ACCIONES INFERIORES (Estilo unificado) -->
                    <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl">
                        <button 
                            type="button" 
                            @click="cerrarModalImpresion()"
                            class="px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer"
                        >
                            Cancelar
                        </button>

                        <button 
                            type="button" 
                            @click="procederImpresion()"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-slate-900 hover:bg-slate-800 shadow-slate-900/20"
                        >
                            <i class="fas fa-print text-xs"></i>
                            <span>Generar e Imprimir Documento</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- SCRIPT DE CONTROL REACTIVO ALPINE.JS PARA EL DASHBOARD DE VENTAS -->
    <script>
        function gestorVentasIndex() {
            return {
                modalDetalleAbierto: false,
                cargandoDetalle: false,
                ventaSeleccionada: null,

                modalImprimirAbierto: false,
                ventaImprimirId: null,
                metodoPagoImprimir: '',
                totalImprimir: 0,
                tipoComprobante: 'ticket',

                // Formulario Factura Comercial
                clienteNombreComercial: '',
                clienteDuiNitComercial: '',
                clienteDireccionComercial: '',

                // Formulario Factura Tributaria (CCF)
                clienteRazonSocialCCF: '',
                clienteNrcCCF: '',
                clienteNitCCF: '',
                clienteGiroCCF: '',
                clienteDireccionCCF: '',
                clienteDepartamentoCCF: 'San Salvador',
                aplicaRetencion1: false,

                async abrirDetalle(id) {
                    this.modalDetalleAbierto = true;
                    this.cargandoDetalle = true;
                    this.ventaSeleccionada = null;

                    try {
                        const response = await fetch(`/ventas/${id}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await response.json();
                        if (data.success && data.venta) {
                            this.ventaSeleccionada = data.venta;
                        } else {
                            alert('No se pudo obtener la información de la venta.');
                            this.modalDetalleAbierto = false;
                        }
                    } catch (e) {
                        console.error('Error al cargar detalle:', e);
                        alert('Ocurrió un error al cargar la información de la venta.');
                        this.modalDetalleAbierto = false;
                    } finally {
                        this.cargandoDetalle = false;
                    }
                },

                cerrarDetalle() {
                    this.modalDetalleAbierto = false;
                    this.ventaSeleccionada = null;
                },

                abrirModalImpresion(id, metodoPago, total) {
                    this.ventaImprimirId = id;
                    this.metodoPagoImprimir = metodoPago;
                    this.totalImprimir = total;
                    this.tipoComprobante = 'ticket';
                    this.modalImprimirAbierto = true;
                },

                abrirModalImpresionDesdeDetalle() {
                    if (!this.ventaSeleccionada) return;
                    this.abrirModalImpresion(
                        this.ventaSeleccionada.id,
                        this.ventaSeleccionada.metodo_pago,
                        this.ventaSeleccionada.total
                    );
                },

                cerrarModalImpresion() {
                    this.modalImprimirAbierto = false;
                },

                procederImpresion() {
                    if (!this.ventaImprimirId) return;

                    const params = new URLSearchParams();
                    params.set('tipo', this.tipoComprobante);

                    if (this.tipoComprobante === 'factura_comercial') {
                        if (this.clienteNombreComercial.trim()) params.set('cliente_nombre', this.clienteNombreComercial.trim());
                        if (this.clienteDuiNitComercial.trim()) params.set('cliente_documento', this.clienteDuiNitComercial.trim());
                        if (this.clienteDireccionComercial.trim()) params.set('cliente_direccion', this.clienteDireccionComercial.trim());
                    } else if (this.tipoComprobante === 'credito_fiscal') {
                        if (!this.clienteRazonSocialCCF.trim()) {
                            alert('Según las leyes tributarias de El Salvador, la Razón Social es obligatoria para emitir Crédito Fiscal.');
                            return;
                        }
                        if (!this.clienteNrcCCF.trim()) {
                            alert('Según las leyes tributarias de El Salvador, el Número de Registro de Contribuyente (NRC) es obligatorio para emitir Crédito Fiscal.');
                            return;
                        }
                        if (!this.clienteNitCCF.trim()) {
                            alert('El NIT o DUI del contribuyente es obligatorio para Crédito Fiscal.');
                            return;
                        }
                        if (!this.clienteGiroCCF.trim()) {
                            alert('El Giro o Actividad Económica es obligatorio por ley en El Salvador para Crédito Fiscal.');
                            return;
                        }

                        params.set('cliente_razon_social', this.clienteRazonSocialCCF.trim());
                        params.set('cliente_nrc', this.clienteNrcCCF.trim());
                        params.set('cliente_nit', this.clienteNitCCF.trim());
                        params.set('cliente_giro', this.clienteGiroCCF.trim());
                        if (this.clienteDireccionCCF.trim()) params.set('cliente_direccion', this.clienteDireccionCCF.trim());
                        params.set('cliente_departamento', this.clienteDepartamentoCCF);
                        if (this.aplicaRetencion1) params.set('retencion_1', '1');
                    }

                    const url = `/ventas/${this.ventaImprimirId}/imprimir?${params.toString()}`;
                    window.open(url, '_blank', 'width=950,height=850,scrollbars=yes');
                    this.cerrarModalImpresion();
                },

                moneda(valor) {
                    return new Intl.NumberFormat('es-SV', {
                        style: 'currency',
                        currency: 'USD',
                        minimumFractionDigits: 2
                    }).format(Number(valor || 0));
                },

                formatearFecha(fechaStr) {
                    if (!fechaStr) return '';
                    const f = new Date(fechaStr);
                    if (isNaN(f.getTime())) return fechaStr;
                    return f.toLocaleDateString('es-SV', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ' ' + f.toLocaleTimeString('es-SV', { hour: '2-digit', minute: '2-digit' });
                },

                totalUnidadesVenta(venta) {
                    if (!venta || !venta.detalles) return 0;
                    return venta.detalles.reduce((acc, d) => acc + Number(d.cantidad || 0), 0);
                },

                calcularSubtotalBase(venta) {
                    if (!venta || !venta.detalles) return 0;
                    return venta.detalles.reduce((acc, d) => acc + (Number(d.cantidad || 0) * Number(d.precio_unitario || 0)), 0);
                }
            };
        }
    </script>
</x-app>
