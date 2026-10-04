<x-app title="Dashboard Histórico de Ventas | AXStore">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <a href="{{ route('ventas.pedidos') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-boxes-packing text-slate-400"></i>
                <span>Control de Envíos y Estados</span>
            </a>
            <a href="{{ route('ventas.devoluciones') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-clock-rotate-left text-slate-400"></i>
                <span>Historial de Devoluciones</span>
            </a>
        </nav>

        <!-- 1. SECCIÓN SUPERIOR: FILTROS GLOBALES -->
        <section class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
            <form method="GET" action="{{ route('ventas.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-3.5 items-end">

                <!-- BÚSQUEDA RÁPIDA -->
                <div class="sm:col-span-2 md:col-span-3 lg:col-span-3">
                    <label for="q" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-magnifying-glass text-slate-400 mr-1"></i> Buscar por Folio / Cliente / Teléfono
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            name="q"
                            id="q"
                            value="{{ $busqueda ?? '' }}"
                            placeholder="Ej: 00004 o 7123-4567..."
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 pl-9 pr-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>
                </div>

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
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
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
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                </div>

                <!-- SELECT VENDEDOR -->
                <div class="lg:col-span-2">
                    <label for="vendedor_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-user-tie text-slate-400 mr-1"></i> Vendedor
                    </label>
                    <select
                        name="vendedor_id"
                        id="vendedor_id"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <option value="">Todos</option>
                        @foreach($vendedores as $v)
                        <option value="{{ $v->id }}" {{ (string)$vendedorId === (string)$v->id ? 'selected' : '' }}>
                            {{ $v->nombre_real ?: $v->username }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- SELECT PRODUCTO ESPECÍFICO (MÉTRICAS DETALLADAS) -->
                <div class="lg:col-span-2">
                    <label for="producto_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-box text-slate-400 mr-1"></i> Producto
                    </label>
                    <select
                        name="producto_id"
                        id="producto_id"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <option value="">Todos</option>
                        @foreach($productos as $p)
                        <option value="{{ $p->id }}" {{ (string)$productoId === (string)$p->id ? 'selected' : '' }}>
                            {{ $p->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- BOTONES DE FILTRADO -->
                <div class="lg:col-span-1 flex items-center gap-1.5">
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-2.5 text-xs transition-all shadow-md shadow-blue-600/15 flex items-center justify-center gap-1 cursor-pointer"
                        title="Aplicar filtros">
                        <i class="fas fa-filter text-[11px]"></i>
                        <span class="hidden sm:inline lg:hidden xl:inline">Filtrar</span>
                    </button>
                    @if(request()->hasAny(['desde', 'hasta', 'vendedor_id', 'producto_id', 'q']))
                    <a
                        href="{{ route('ventas.index') }}"
                        class="rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold py-2 px-2.5 text-xs transition-colors flex items-center justify-center shrink-0"
                        title="Limpiar filtros">
                        <i class="fas fa-rotate-left text-[11px]"></i>
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
                        <h2 class="text-base font-black text-slate-900">Historial de Transacciones (Entregadas)</h2>
                        <p class="text-xs text-slate-500">Listado cronológico de ventas entregadas y liquidadas en el periodo seleccionado</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('ventas.pedidos') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1 rounded-lg transition-colors flex items-center gap-1.5">
                        <i class="fas fa-boxes-packing text-[11px]"></i>
                        <span>Gestionar Pedidos en Proceso</span>
                    </a>
                    <span class="text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 px-3 py-1 rounded-lg">
                        {{ $ventas->total() }} entregadas
                    </span>
                </div>
            </div>

            <!-- TABLA -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[1050px]">
                    <thead class="bg-slate-50/80 text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5 w-24 whitespace-nowrap">Folio</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Fecha y Hora</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Vendedor</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Tipo y Destino</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Método de Pago</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Estado Actual</th>
                            <th class="px-4 py-3.5 text-right whitespace-nowrap w-24">Total</th>
                            <th class="px-5 py-3.5 text-right whitespace-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($ventas as $venta)
                        @php
                        $estado = $venta->estado ?? 'Pendiente';
                        $tipo = $venta->tipo_venta ?? 'Tienda';
                        $bloqueado = $venta->estado_bloqueado;
                        $permitidos = $venta->estados_permitidos;
                        $puedeDevolver = $venta->puede_devolver;
                        $diasRestantes = $venta->dias_restantes_devolucion;
                        $diasGarantia = $venta->dias_garantia;
                        $textoGarantia = $venta->texto_garantia_devolucion;
                        $fechaLimite = $venta->fecha_limite_devolucion;
                        $limiteFechaFormatted = $fechaLimite ? $fechaLimite->format('d/m/Y') : '';

                        $badgeClasses = match($estado) {
                        'Pendiente' => 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100/80',
                        'Confirmada' => 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100/80',
                        'En ruta' => 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100/80',
                        'Entregada' => 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100/80',
                        'Cancelada' => 'bg-rose-50 text-rose-700 border-rose-200',
                        'Devolución' => 'bg-purple-50 text-purple-700 border-purple-200',
                        'Cambio' => 'bg-teal-50 text-teal-700 border-teal-200',
                        default => 'bg-slate-50 text-slate-700 border-slate-200'
                        };

                        $badgeIcon = match($estado) {
                        'Pendiente' => 'fa-clock',
                        'Confirmada' => 'fa-circle-check',
                        'En ruta' => 'fa-truck-fast',
                        'Entregada' => 'fa-box-open',
                        'Cancelada' => 'fa-ban',
                        'Devolución' => 'fa-rotate-left',
                        'Cambio' => 'fa-arrow-right-arrow-left',
                        default => 'fa-info-circle'
                        };

                        $garantiaBadgeText = $diasRestantes > 1
                        ? "Garantía: {$diasRestantes} días"
                        : ($diasRestantes === 1 ? "Garantía: 1 día" : "Garantía: Hoy último día");
                        @endphp

                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- FOLIO -->
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[11px] font-mono">
                                    #VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            <!-- FECHA Y HORA -->
                            <td class="px-4 py-3.5 text-slate-600">
                                <p class="font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}
                                </p>
                                <span class="text-[10px] text-slate-400">
                                    {{ \Carbon\Carbon::parse($venta->fecha)->format('h:i A') }}
                                </span>
                            </td>

                            <!-- VENDEDOR -->
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

                            <!-- TIPO Y DESTINO -->
                            <td class="px-4 py-3.5">
                                <div class="space-y-0.5">
                                    @if($tipo === 'Envio')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <i class="fas fa-truck text-[9px]"></i> Entrega a Domicilio
                                    </span>
                                    @if($venta->direccion_entrega)
                                    <p class="text-[11px] text-slate-700 font-medium truncate max-w-[200px]" title="{{ $venta->direccion_entrega }}">
                                        {{ $venta->direccion_entrega }}
                                    </p>
                                    @endif
                                    @if($venta->telefono_entrega)
                                    <p class="text-[10px] text-slate-400 flex items-center gap-1 font-mono">
                                        <i class="fas fa-phone text-[8px]"></i> {{ $venta->telefono_entrega }}
                                    </p>
                                    @endif
                                    @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-store text-[9px]"></i> Venta en Tienda
                                    </span>
                                    @endif
                                </div>
                            </td>

                            <!-- MÉTODO DE PAGO -->
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $venta->metodo_pago === 'Efectivo' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($venta->metodo_pago === 'Transferencia Bancaria' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-blue-50 text-blue-700 border-blue-200') }}">
                                    <i class="fas {{ $venta->metodo_pago === 'Efectivo' ? 'fa-money-bill-wave' : ($venta->metodo_pago === 'Transferencia Bancaria' ? 'fa-building-columns' : 'fa-credit-card') }} text-[10px]"></i>
                                    <span>{{ $venta->metodo_pago }}</span>
                                </span>
                            </td>

                            <!-- ESTADO ACTUAL INTERACTIVO -->
                            <td class="px-4 py-3.5 text-center">
                                <div class="inline-flex flex-col items-center">
                                    @if(!$bloqueado && count($permitidos) > 0)
                                    <button
                                        type="button"
                                        @click="abrirModalCambiarEstado({{ $venta->id }}, '{{ $estado }}', '{{ $tipo }}', {{ $puedeDevolver ? 'true' : 'false' }}, {{ json_encode($permitidos) }}, {{ $diasRestantes }}, '{{ addslashes($textoGarantia) }}', '{{ $limiteFechaFormatted }}')"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border transition-all duration-200 shadow-2xs hover:shadow-xs hover:scale-105 cursor-pointer {{ $badgeClasses }}"
                                        title="Haz clic para cambiar el estado de la venta">
                                        <i class="fas {{ $badgeIcon }} text-[10px]"></i>
                                        <span>{{ $estado }}</span>
                                        <i class="fas fa-chevron-down text-[8px] opacity-60 ml-0.5"></i>
                                    </button>
                                    @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border shadow-2xs {{ $badgeClasses }}"
                                        title="{{ $estado === 'Entregada' ? 'Garantía de devolución expirada (' . $diasGarantia . ' días). Estado definitivo.' : 'Estado definitivo bloqueado.' }}">
                                        <i class="fas {{ $badgeIcon }} text-[10px]"></i>
                                        <span>{{ $estado }}</span>
                                        <i class="fas fa-lock text-[8px] opacity-40 ml-0.5"></i>
                                    </span>
                                    @endif

                                    @if($estado === 'Entregada' && $puedeDevolver)
                                    <span class="text-[9px] font-bold text-amber-600 mt-1 flex items-center gap-0.5" title="Garantía de devolución activa hasta el {{ $limiteFechaFormatted }} a las 23:59 ({{ $diasGarantia }} días en {{ $tipo === 'Envio' ? 'envío' : 'tienda' }})">
                                        <i class="fas fa-shield-halved text-[8px]"></i>
                                        <span>{{ $garantiaBadgeText }}</span>
                                    </span>
                                    @elseif($estado === 'Entregada' && !$puedeDevolver)
                                    <span class="text-[9px] font-medium text-slate-400 mt-0.5" title="Garantía de devolución expirada (plazo de {{ $diasGarantia }} días)">
                                        Garantía expirada
                                    </span>
                                    @elseif($tipo === 'Envio')
                                    <span class="text-[9px] font-medium text-slate-400 mt-0.5">Envío</span>
                                    @elseif($tipo === 'Tienda')
                                    <span class="text-[9px] font-medium text-slate-400 mt-0.5">Tienda</span>
                                    @endif
                                </div>
                            </td>

                            <!-- TOTAL -->
                            <td class="px-4 py-3.5 text-right font-black text-slate-900 text-sm whitespace-nowrap">
                                ${{ number_format($venta->total, 2) }}
                            </td>

                            <!-- ACCIONES -->
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5 flex-nowrap">
                                    <!-- COMPROBANTE DE PAGO (TRANSFERENCIA) -->
                                    @if($venta->comprobante_pago)
                                    <button
                                        type="button"
                                        @click="verImagenAmpliada('{{ asset('storage/' . $venta->comprobante_pago) }}', 'VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}_{{ \Carbon\Carbon::parse($venta->fecha)->format('Y-m-d') }}_comprobante_pago', 'Comprobante de Pago por Transferencia')"
                                        class="rounded-lg border border-purple-200 bg-purple-50 hover:bg-purple-600 hover:text-white text-purple-700 px-2 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                        title="Ver Comprobante de Pago por Transferencia">
                                        <i class="fas fa-file-invoice-dollar text-[10px]"></i>
                                        <span>Comprobante</span>
                                    </button>
                                    @endif

                                    <!-- COMPROBANTE DE PAQUETE (EN RUTA) -->
                                    @if($venta->comprobante_paquete)
                                    <button
                                        type="button"
                                        @click="verImagenAmpliada('{{ asset('storage/' . $venta->comprobante_paquete) }}', 'VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}_{{ \Carbon\Carbon::parse($venta->fecha)->format('Y-m-d') }}_paquete', 'Comprobante de Paquetería')"
                                        class="rounded-lg border border-indigo-200 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 px-2 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                        title="Ver Comprobante de Paquetería">
                                        <i class="fas fa-truck-ramp-box text-[10px]"></i>
                                        <span>Paquete</span>
                                    </button>
                                    @endif

                                    <!-- COMPROBANTE DE DEVOLUCIÓN -->
                                    @if($venta->comprobante_devolucion)
                                    <button
                                        type="button"
                                        @click="verImagenAmpliada('{{ asset('storage/' . $venta->comprobante_devolucion) }}', 'VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}_{{ \Carbon\Carbon::parse($venta->fecha)->format('Y-m-d') }}_devolucion', 'Comprobante de Devolución')"
                                        class="rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-700 px-2 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                        title="Ver Comprobante de Devolución">
                                        <i class="fas fa-box-archive text-[10px]"></i>
                                        <span>Devolución</span>
                                    </button>
                                    @endif

                                    <!-- BOTÓN VER DETALLE -->
                                    <button
                                        type="button"
                                        @click="abrirDetalle({{ $venta->id }})"
                                        class="rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 px-2 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                        title="Ver detalle completo de la venta">
                                        <i class="fas fa-eye text-slate-400 text-[10px]"></i>
                                        <span>Detalle</span>
                                    </button>

                                    <!-- BOTÓN IMPRIMIR COMPROBANTE -->
                                    <button
                                        type="button"
                                        @click="abrirModalImpresion({{ $venta->id }}, '{{ $venta->metodo_pago }}', {{ (float) $venta->total }})"
                                        class="rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 px-2 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                        title="Imprimir ticket o factura comercial/tributaria">
                                        <i class="fas fa-print text-[10px]"></i>
                                        <span>Imprimir</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center text-slate-400">
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
            aria-modal="true">
            <!-- Backdrop oscuro con blur idéntico al sistema de modales -->
            <div
                x-show="modalDetalleAbierto"
                x-transition.opacity
                class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                @click="if (!modalImprimirAbierto && !comprobanteZoomUrl && !openDevolucionModal) cerrarDetalle()"></div>

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
                <div
                    x-show="modalDetalleAbierto"
                    x-transition
                    @click.away="if (!modalImprimirAbierto && !comprobanteZoomUrl && !openDevolucionModal) cerrarDetalle()"
                    class="relative z-10 w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-gray-100 flex flex-col my-8 max-h-[90vh]">
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
                                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full ml-1"
                                        :class="{
                                              'bg-amber-100 text-amber-800': ventaSeleccionada?.estado === 'Pendiente',
                                              'bg-blue-100 text-blue-800': ventaSeleccionada?.estado === 'Confirmada',
                                              'bg-indigo-100 text-indigo-800': ventaSeleccionada?.estado === 'En ruta',
                                              'bg-emerald-100 text-emerald-800': ventaSeleccionada?.estado === 'Entregada',
                                              'bg-rose-100 text-rose-800': ventaSeleccionada?.estado === 'Cancelada',
                                              'bg-purple-100 text-purple-800': ventaSeleccionada?.estado === 'Devolución',
                                              'bg-teal-100 text-teal-800': ventaSeleccionada?.estado === 'Cambio'
                                          }"
                                        x-text="ventaSeleccionada?.estado || 'Entregada'">
                                    </span>
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
                            title="Cerrar ventana">
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
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                        <h4 class="text-[11px] font-black uppercase tracking-wider text-slate-500">
                                            INFORMACIÓN DE LA VENTA
                                        </h4>
                                        <div class="flex items-center gap-2">
                                            <template x-if="ventaSeleccionada.estado === 'Entregada' && ventaSeleccionada.puede_devolver">
                                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1"
                                                    :title="'Garantía válida hasta ' + (ventaSeleccionada.fecha_limite_devolucion ? new Date(ventaSeleccionada.fecha_limite_devolucion).toLocaleDateString() : '')">
                                                    <i class="fas fa-shield-halved text-[9px]"></i>
                                                    <span x-text="'Garantía: ' + (ventaSeleccionada.texto_garantia_devolucion || (ventaSeleccionada.dias_restantes_devolucion + ' días'))"></span>
                                                </span>
                                            </template>
                                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full"
                                                :class="ventaSeleccionada.tipo_venta === 'Envio' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'"
                                                x-text="ventaSeleccionada.tipo_venta === 'Envio' ? 'Modalidad: Entrega a Domicilio' : 'Modalidad: Venta en Mostrador (Tienda)'">
                                            </span>
                                        </div>
                                    </div>
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

                                <!-- DATOS DE ENTREGA A DOMICILIO (SI APLICA) -->
                                <template x-if="ventaSeleccionada.direccion_entrega && ventaSeleccionada.direccion_entrega !== 'Venta en mostrador / POS'">
                                    <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-4 shadow-2xs flex items-start gap-3.5">
                                        <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-600/20">
                                            <i class="fas fa-truck-fast text-sm"></i>
                                        </div>
                                        <div class="min-w-0 flex-1 text-xs">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <h4 class="font-black text-slate-900 uppercase tracking-wider text-[11px]">Entrega a Domicilio</h4>
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-700 bg-blue-100 border border-blue-200 px-2 py-0.5 rounded-full">
                                                    Despachado
                                                </span>
                                            </div>
                                            <p class="text-slate-800 font-bold" x-text="ventaSeleccionada.direccion_entrega"></p>
                                            <p class="text-[11px] text-slate-500 mt-0.5" x-show="ventaSeleccionada.fecha_salida">
                                                Creación registrada: <span class="font-semibold text-slate-700" x-text="ventaSeleccionada.fecha_salida + (ventaSeleccionada.hora_salida ? ' • ' + ventaSeleccionada.hora_salida : '')"></span>
                                            </p>
                                            <template x-if="Number(ventaSeleccionada.precio_envio || 0) > 0">
                                                <p class="text-[11px] text-blue-700 mt-0.5 font-bold">
                                                    Costo de envío: <span x-text="moneda(ventaSeleccionada.precio_envio)"></span>
                                                </p>
                                            </template>
                                            <template x-if="ventaSeleccionada.telefono">
                                                <p class="text-[11px] text-slate-600 mt-1 flex items-center gap-1.5 font-medium">
                                                    <i class="fas fa-phone text-blue-600 text-[10px]"></i>
                                                    <span>Teléfono:</span>
                                                    <strong class="text-slate-800 font-mono" x-text="ventaSeleccionada.telefono"></strong>
                                                </p>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- COMPROBANTE DE TRANSFERENCIA (SI APLICA) -->
                                <template x-if="ventaSeleccionada.comprobante_url">
                                    <div class="bg-gradient-to-r from-blue-50/70 via-indigo-50/50 to-slate-50 border border-blue-200/80 rounded-2xl p-4 sm:p-5 shadow-2xs">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-600/30">
                                                    <i class="fas fa-file-invoice-dollar text-sm"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                                        <span>Comprobante de Transferencia</span>
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded-full lowercase first-letter:uppercase">
                                                            <i class="fas fa-circle-check text-[9px]"></i> Verificado
                                                        </span>
                                                    </h4>
                                                    <p class="text-[11px] text-slate-500">Documento digital adjuntado durante la venta</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <a
                                                    :href="ventaSeleccionada.comprobante_url"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-blue-700 bg-white border border-blue-200 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-2xs">
                                                    <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                                                    <span>Abrir pestaña</span>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- VISTA PREVIA INTERACTIVA -->
                                        <div class="flex items-start gap-4 p-3 bg-white rounded-xl border border-blue-100">
                                            <button
                                                type="button"
                                                @click="verImagenAmpliada(ventaSeleccionada.comprobante_url, nombreComprobanteVenta('comprobante_pago'), 'Comprobante de Pago')"
                                                class="group relative overflow-hidden rounded-lg border border-slate-200 shadow-2xs shrink-0 cursor-pointer block"
                                                title="Haz clic para ampliar la imagen">
                                                <img
                                                    :src="ventaSeleccionada.comprobante_url"
                                                    alt="Comprobante de pago"
                                                    class="w-24 h-24 object-cover group-hover:scale-105 transition-transform duration-200">
                                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1">
                                                    <i class="fas fa-magnifying-glass-plus"></i>
                                                    <span>Ampliar</span>
                                                </div>
                                            </button>
                                            <div class="space-y-1.5 text-xs text-slate-600 flex-1">
                                                <p class="font-bold text-slate-800">Comprobante de Pago Registrado</p>
                                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                                    Puedes hacer clic en la miniatura para visualizar el comprobante en tamaño completo o abrirlo en una nueva pestaña para descargar o verificar los datos del depósito bancario.
                                                </p>
                                                <button
                                                    type="button"
                                                    @click="verImagenAmpliada(ventaSeleccionada.comprobante_url, nombreComprobanteVenta('comprobante_pago'), 'Comprobante de Pago')"
                                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 hover:text-blue-800 transition-colors cursor-pointer">
                                                    <i class="fas fa-expand text-[10px]"></i> Ver imagen ampliada
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- MOTIVO / OBSERVACIONES (SI APLICA) -->
                                <template x-if="ventaSeleccionada.observaciones">
                                    <div class="rounded-2xl p-4 sm:p-5 border shadow-2xs flex items-start gap-3.5"
                                        :class="ventaSeleccionada.estado === 'Cancelada' ? 'bg-rose-50/70 border-rose-200/80 text-rose-950' : (ventaSeleccionada.estado === 'Devolución' ? 'bg-purple-50/70 border-purple-200/80 text-purple-950' : (ventaSeleccionada.estado === 'Cambio' ? 'bg-teal-50/70 border-teal-200/80 text-teal-950' : 'bg-slate-50/90 border-slate-200 text-slate-800'))">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 shadow-sm"
                                            :class="ventaSeleccionada.estado === 'Cancelada' ? 'bg-rose-600 text-white' : (ventaSeleccionada.estado === 'Devolución' ? 'bg-purple-600 text-white' : (ventaSeleccionada.estado === 'Cambio' ? 'bg-teal-600 text-white' : 'bg-slate-700 text-white'))">
                                            <i class="fas" :class="ventaSeleccionada.estado === 'Cancelada' ? 'fa-ban' : (ventaSeleccionada.estado === 'Devolución' ? 'fa-rotate-left' : (ventaSeleccionada.estado === 'Cambio' ? 'fa-arrows-rotate' : 'fa-comment-dots'))"></i>
                                        </div>
                                        <div class="min-w-0 flex-1 text-xs">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 class="font-black uppercase tracking-wider text-[11px]"
                                                    x-text="ventaSeleccionada.estado === 'Cancelada' ? 'Motivo de Cancelación' : (ventaSeleccionada.estado === 'Devolución' ? 'Motivo de Devolución' : (ventaSeleccionada.estado === 'Cambio' ? 'Motivo de Cambio' : 'Observaciones Registradas'))">
                                                </h4>
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full"
                                                    :class="ventaSeleccionada.estado === 'Cancelada' ? 'bg-rose-100 text-rose-800' : (ventaSeleccionada.estado === 'Devolución' ? 'bg-purple-100 text-purple-800' : (ventaSeleccionada.estado === 'Cambio' ? 'bg-teal-100 text-teal-800' : 'bg-slate-200 text-slate-700'))"
                                                    x-text="ventaSeleccionada.estado">
                                                </span>
                                            </div>
                                            <p class="font-medium whitespace-pre-line text-slate-700 leading-relaxed bg-white/70 p-3 rounded-xl border border-black/5" x-text="ventaSeleccionada.observaciones"></p>
                                            <template x-if="ventaSeleccionada.fecha_cancelacion">
                                                <p class="text-[10px] text-slate-500 mt-1.5">
                                                    Fecha de anulación: <span class="font-semibold" x-text="formatearFecha(ventaSeleccionada.fecha_cancelacion)"></span>
                                                </p>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- COMPROBANTE DE PAQUETE EN RUTA (SI APLICA) -->
                                <template x-if="ventaSeleccionada.comprobante_paquete_url">
                                    <div class="bg-gradient-to-r from-indigo-50/70 via-blue-50/50 to-slate-50 border border-indigo-200/80 rounded-2xl p-4 sm:p-5 shadow-2xs">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-indigo-600/30">
                                                    <i class="fas fa-box-open text-sm"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                                        <span>Evidencia de Paquete en Paquetería</span>
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-700 bg-indigo-100 border border-indigo-200 px-2 py-0.5 rounded-full">
                                                            <i class="fas fa-truck text-[9px]"></i> En ruta
                                                        </span>
                                                    </h4>
                                                    <p class="text-[11px] text-slate-500">Fotografía tomada al entregar el paquete a la mensajería</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <a
                                                    :href="ventaSeleccionada.comprobante_paquete_url"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-indigo-700 bg-white border border-indigo-200 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-2xs">
                                                    <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                                                    <span>Abrir pestaña</span>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-4 p-3 bg-white rounded-xl border border-indigo-100">
                                            <button
                                                type="button"
                                                @click="verImagenAmpliada(ventaSeleccionada.comprobante_paquete_url, nombreComprobanteVenta('paquete'), 'Comprobante de Paquetería')"
                                                class="group relative overflow-hidden rounded-lg border border-slate-200 shadow-2xs shrink-0 cursor-pointer block"
                                                title="Haz clic para ampliar la imagen">
                                                <img
                                                    :src="ventaSeleccionada.comprobante_paquete_url"
                                                    alt="Paquete en paquetería"
                                                    class="w-24 h-24 object-cover group-hover:scale-105 transition-transform duration-200">
                                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1">
                                                    <i class="fas fa-magnifying-glass-plus"></i>
                                                    <span>Ampliar</span>
                                                </div>
                                            </button>
                                            <div class="space-y-1.5 text-xs text-slate-600 flex-1">
                                                <p class="font-bold text-slate-800">Comprobante de despacho a paquetería</p>
                                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                                    Evidencia del paquete rotulado y debidamente recibido por el servicio de encomiendas/delivery.
                                                </p>
                                                <button
                                                    type="button"
                                                    @click="verImagenAmpliada(ventaSeleccionada.comprobante_paquete_url, nombreComprobanteVenta('paquete'), 'Comprobante de Paquetería')"
                                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition-colors cursor-pointer">
                                                    <i class="fas fa-expand text-[10px]"></i> Ver imagen ampliada
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- COMPROBANTE DE DEVOLUCIÓN (SI APLICA) -->
                                <template x-if="ventaSeleccionada.comprobante_devolucion_url">
                                    <div class="bg-gradient-to-r from-purple-50/70 via-indigo-50/50 to-slate-50 border border-purple-200/80 rounded-2xl p-4 sm:p-5 shadow-2xs">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-purple-600/30">
                                                    <i class="fas fa-rotate-left text-sm"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                                        <span>Evidencia de Paquete Devuelto</span>
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-purple-700 bg-purple-100 border border-purple-200 px-2 py-0.5 rounded-full">
                                                            <i class="fas fa-arrow-rotate-left text-[9px]"></i> Devolución
                                                        </span>
                                                    </h4>
                                                    <p class="text-[11px] text-slate-500">Fotografía del paquete o producto retornado al inventario</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <a
                                                    :href="ventaSeleccionada.comprobante_devolucion_url"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-purple-700 bg-white border border-purple-200 hover:bg-purple-600 hover:text-white hover:border-purple-600 transition-all shadow-2xs">
                                                    <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                                                    <span>Abrir pestaña</span>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-4 p-3 bg-white rounded-xl border border-purple-100">
                                            <button
                                                type="button"
                                                @click="verImagenAmpliada(ventaSeleccionada.comprobante_devolucion_url, nombreComprobanteVenta('devolucion'), 'Comprobante de Devolución')"
                                                class="group relative overflow-hidden rounded-lg border border-slate-200 shadow-2xs shrink-0 cursor-pointer block"
                                                title="Haz clic para ampliar la imagen">
                                                <img
                                                    :src="ventaSeleccionada.comprobante_devolucion_url"
                                                    alt="Paquete devuelto"
                                                    class="w-24 h-24 object-cover group-hover:scale-105 transition-transform duration-200">
                                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1">
                                                    <i class="fas fa-magnifying-glass-plus"></i>
                                                    <span>Ampliar</span>
                                                </div>
                                            </button>
                                            <div class="space-y-1.5 text-xs text-slate-600 flex-1">
                                                <p class="font-bold text-slate-800">Fotografía del producto recibido en devolución</p>
                                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                                    Inspección visual del paquete o artículo devuelto por el cliente para retorno a inventario.
                                                </p>
                                                <button
                                                    type="button"
                                                    @click="verImagenAmpliada(ventaSeleccionada.comprobante_devolucion_url, nombreComprobanteVenta('devolucion'), 'Comprobante de Devolución')"
                                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-purple-600 hover:text-purple-800 transition-colors cursor-pointer">
                                                    <i class="fas fa-expand text-[10px]"></i> Ver imagen ampliada
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

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
                            class="px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                            Cerrar
                        </button>

                        <div class="flex items-center gap-3">
                            <template x-if="ventaSeleccionada && ventaSeleccionada.estado === 'Entregada' && ventaSeleccionada.puede_devolver">
                                <button
                                    type="button"
                                    @click="openDevolucionModal = true"
                                    class="rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold px-3.5 py-2 text-xs transition-all shadow-md shadow-red-600/20 flex items-center gap-2 cursor-pointer">
                                    <i class="fas fa-rotate-left"></i>
                                    <span>Registrar Devolución</span>
                                </button>
                            </template>

                            <button
                                type="button"
                                x-show="ventaSeleccionada"
                                @click="abrirModalImpresionDesdeDetalle()"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-slate-900 hover:bg-slate-800 shadow-slate-900/20">
                                <i class="fas fa-print text-xs"></i>
                                <span>Imprimir Comprobante (Ticket / Factura)</span>
                            </button>
                        </div>
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
            class="fixed inset-0 z-[70] overflow-y-auto"
            style="display: none;"
            role="dialog"
            aria-modal="true">
            <!-- Backdrop oscuro con blur idéntico al sistema -->
            <div
                x-show="modalImprimirAbierto"
                x-transition.opacity
                class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"
                @click.stop="cerrarModalImpresion()"></div>

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
                <div
                    x-show="modalImprimirAbierto"
                    x-transition
                    @click.stop
                    @click.away="cerrarModalImpresion()"
                    class="relative z-10 w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-gray-100 flex flex-col my-8 max-h-[90vh]">
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
                            @click.stop="cerrarModalImpresion()"
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center text-white transition-colors cursor-pointer"
                            title="Cerrar ventana">
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
                                    class="border rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between shadow-2xs">
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
                                    class="border rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between shadow-2xs">
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
                                    class="border rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between shadow-2xs">
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
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                        <span>DUI o NIT:</span>
                                        <span class="text-[10px] text-slate-400 font-normal">Opcional</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            x-model="clienteDuiNitComercial"
                                            @input="onInputDuiNitComercial($event)"
                                            placeholder="00000000-0 o 0000-000000-000-0"
                                            maxlength="17"
                                            :class="{
                                                'border-slate-300 focus:border-blue-600 focus:ring-blue-600/20': estadoDocComercial.estado === 'vacio',
                                                'border-amber-400 focus:border-amber-500 focus:ring-amber-500/20 bg-amber-50/10': estadoDocComercial.estado === 'incompleto',
                                                'border-emerald-500 focus:border-emerald-600 focus:ring-emerald-500/20 bg-emerald-50/20 text-emerald-950 font-bold': estadoDocComercial.estado === 'valido',
                                                'border-rose-500 focus:border-rose-600 focus:ring-rose-500/20 bg-rose-50/20 text-rose-950': estadoDocComercial.estado === 'invalido'
                                            }"
                                            class="w-full rounded-xl border px-3.5 py-2.5 pr-9 text-xs text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:outline-none transition-all bg-white font-mono">
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center pointer-events-none">
                                            <template x-if="estadoDocComercial.estado === 'valido'">
                                                <i class="fas fa-circle-check text-emerald-500 text-sm"></i>
                                            </template>
                                            <template x-if="estadoDocComercial.estado === 'invalido'">
                                                <i class="fas fa-circle-xmark text-rose-500 text-sm"></i>
                                            </template>
                                            <template x-if="estadoDocComercial.estado === 'incompleto'">
                                                <i class="fas fa-circle-exclamation text-amber-500 text-sm"></i>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="min-h-[18px] text-[11px] pt-1">
                                        <template x-if="estadoDocComercial.estado === 'vacio'">
                                            <p class="text-slate-400 flex items-center gap-1.5">
                                                <i class="fas fa-circle-info text-[10px]"></i>
                                                <span>DUI (9 dígitos: 00000000-0) o NIT (14 dígitos)</span>
                                            </p>
                                        </template>
                                        <template x-if="estadoDocComercial.estado === 'incompleto'">
                                            <p class="text-amber-600 font-medium flex items-center gap-1.5">
                                                <i class="fas fa-circle-exclamation text-[10px]"></i>
                                                <span x-text="estadoDocComercial.mensaje"></span>
                                            </p>
                                        </template>
                                        <template x-if="estadoDocComercial.estado === 'valido'">
                                            <p class="text-emerald-600 font-bold flex items-center gap-1.5">
                                                <i class="fas fa-check text-[10px]"></i>
                                                <span x-text="estadoDocComercial.mensaje"></span>
                                            </p>
                                        </template>
                                        <template x-if="estadoDocComercial.estado === 'invalido'">
                                            <p class="text-rose-600 font-bold flex items-center gap-1.5">
                                                <i class="fas fa-xmark text-[10px]"></i>
                                                <span x-text="estadoDocComercial.mensaje"></span>
                                            </p>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Dirección del Cliente:</label>
                                <input
                                    type="text"
                                    x-model="clienteDireccionComercial"
                                    placeholder="San Salvador, El Salvador"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all bg-white">
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
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                        <span>N° de Registro de Contribuyente (NRC) <span class="text-rose-500 font-bold">*</span>:</span>
                                    </label>
                                    <input
                                        type="text"
                                        x-model="clienteNrcCCF"
                                        @input="onInputNrcCCF($event)"
                                        placeholder="Ej. 123456-7"
                                        maxlength="10"
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 font-mono placeholder:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                        <span>NIT / DUI del Contribuyente <span class="text-rose-500 font-bold">*</span>:</span>
                                        <span class="text-[10px] text-purple-600 font-semibold">Obligatorio</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            x-model="clienteNitCCF"
                                            @input="onInputNitCCF($event)"
                                            placeholder="0614-010190-101-1 o 00000000-0"
                                            maxlength="17"
                                            :class="{
                                                'border-purple-200 focus:border-purple-600 focus:ring-purple-600/20': estadoDocCCF.estado === 'vacio',
                                                'border-amber-400 focus:border-amber-500 focus:ring-amber-500/20 bg-amber-50/10': estadoDocCCF.estado === 'incompleto',
                                                'border-emerald-500 focus:border-emerald-600 focus:ring-emerald-500/20 bg-emerald-50/20 text-emerald-950 font-bold': estadoDocCCF.estado === 'valido',
                                                'border-rose-500 focus:border-rose-600 focus:ring-rose-500/20 bg-rose-50/20 text-rose-950': estadoDocCCF.estado === 'invalido'
                                            }"
                                            class="w-full rounded-xl border bg-white px-3.5 py-2.5 pr-9 text-xs font-mono placeholder:text-slate-400 focus:ring-2 focus:outline-none transition-all">
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center pointer-events-none">
                                            <template x-if="estadoDocCCF.estado === 'valido'">
                                                <i class="fas fa-circle-check text-emerald-500 text-sm"></i>
                                            </template>
                                            <template x-if="estadoDocCCF.estado === 'invalido'">
                                                <i class="fas fa-circle-xmark text-rose-500 text-sm"></i>
                                            </template>
                                            <template x-if="estadoDocCCF.estado === 'incompleto'">
                                                <i class="fas fa-circle-exclamation text-amber-500 text-sm"></i>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="min-h-[18px] text-[11px] pt-1">
                                        <template x-if="estadoDocCCF.estado === 'vacio'">
                                            <p class="text-slate-400 flex items-center gap-1.5">
                                                <i class="fas fa-circle-info text-[10px]"></i>
                                                <span>NIT institucional (14 dígitos) o DUI (9 dígitos)</span>
                                            </p>
                                        </template>
                                        <template x-if="estadoDocCCF.estado === 'incompleto'">
                                            <p class="text-amber-600 font-medium flex items-center gap-1.5">
                                                <i class="fas fa-circle-exclamation text-[10px]"></i>
                                                <span x-text="estadoDocCCF.mensaje"></span>
                                            </p>
                                        </template>
                                        <template x-if="estadoDocCCF.estado === 'valido'">
                                            <p class="text-emerald-600 font-bold flex items-center gap-1.5">
                                                <i class="fas fa-check text-[10px]"></i>
                                                <span x-text="estadoDocCCF.mensaje"></span>
                                            </p>
                                        </template>
                                        <template x-if="estadoDocCCF.estado === 'invalido'">
                                            <p class="text-rose-600 font-bold flex items-center gap-1.5">
                                                <i class="fas fa-xmark text-[10px]"></i>
                                                <span x-text="estadoDocCCF.mensaje"></span>
                                            </p>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Giro o Actividad Económica <span class="text-rose-500 font-bold">*</span>:
                                    </label>
                                    <input
                                        type="text"
                                        x-model="clienteGiroCCF"
                                        placeholder="Ej. Servicios de Transporte / Taller"
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Dirección Comercial:</label>
                                    <input
                                        type="text"
                                        x-model="clienteDireccionCCF"
                                        placeholder="Colonia Escalón, San Salvador"
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Departamento:</label>
                                    <select
                                        x-model="clienteDepartamentoCCF"
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all">
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
                                        class="rounded border-purple-300 text-purple-600 focus:ring-purple-500">
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
                            @click.stop="cerrarModalImpresion()"
                            class="px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                            Cancelar
                        </button>

                        <button
                            type="button"
                            @click="procederImpresion()"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-slate-900 hover:bg-slate-800 shadow-slate-900/20">
                            <i class="fas fa-print text-xs"></i>
                            <span>Generar e Imprimir Documento</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 3: LIGHTBOX / ZOOM DEL COMPROBANTE DE TRANSFERENCIA                 -->
        <!-- ========================================================================= -->
        <div
            x-show="comprobanteZoomUrl"
            x-cloak
            class="fixed inset-0 z-[60] overflow-y-auto"
            style="display: none;"
            role="dialog"
            aria-modal="true">
            <div
                x-show="comprobanteZoomUrl"
                x-transition.opacity
                class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
                @click="cerrarImagenAmpliada()"></div>

            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div
                    x-show="comprobanteZoomUrl"
                    x-transition
                    class="relative z-10 max-w-3xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200"
                    @click.away="cerrarImagenAmpliada()">
                    <div class="bg-blue-600 px-5 py-3.5 flex items-center justify-between text-white shadow-md">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-file-invoice text-white text-base"></i>
                            <span class="text-xs font-black uppercase tracking-wider text-white" x-text="comprobanteZoomTitulo || 'Evidencia de Venta'"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="descargarImagenActual()"
                                class="text-xs text-white hover:bg-white/20 bg-white/10 flex items-center gap-1.5 font-bold transition-all px-3 py-1.5 rounded-xl cursor-pointer"
                                title="Descargar imagen con folio y fecha de venta">
                                <i class="fas fa-download"></i>
                                <span>Descargar</span>
                            </button>
                            <button type="button" @click="cerrarImagenAmpliada()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 bg-slate-900/5 flex items-center justify-center max-h-[75vh] overflow-auto">
                        <img :src="comprobanteZoomUrl" alt="Comprobante Ampliado" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-sm border border-slate-200">
                    </div>
                    <div class="px-5 py-3 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-500 font-medium">Inspección de comprobante de pago</span>
                        <button type="button" @click="cerrarImagenAmpliada()" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 4: CAMBIAR ESTADO DE LA VENTA (MÁQUINA DE ESTADOS Y VALIDACIONES)  -->
        <!-- ========================================================================= -->
        <div
            x-show="modalCambiarEstadoAbierto"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
            role="dialog"
            aria-modal="true">
            <div
                x-show="modalCambiarEstadoAbierto"
                x-transition.opacity
                class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                @click="cerrarModalCambiarEstado()"></div>

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
                <div
                    x-show="modalCambiarEstadoAbierto"
                    x-transition
                    @click.away="cerrarModalCambiarEstado()"
                    class="relative z-10 w-full max-w-xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-gray-100 flex flex-col my-8 max-h-[92vh]">
                    <!-- CABECERA AZUL -->
                    <div class="bg-blue-600 px-6 py-4.5 flex items-center justify-between text-white shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white text-sm shadow-xs">
                                <i class="fas fa-arrows-spin"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black tracking-tight text-white flex items-center gap-2">
                                    <span>Actualizar Estado</span>
                                    <span class="font-mono text-blue-200 text-sm" x-text="estadoModalData.folio"></span>
                                </h3>
                                <p class="text-xs text-blue-100 mt-0.5">
                                    Transición de estado y captura de evidencias operativas
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="cerrarModalCambiarEstado()"
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center text-white transition-colors cursor-pointer"
                            title="Cerrar ventana">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>

                    <!-- CUERPO DEL MODAL -->
                    <div id="modal-cambiar-estado-body" class="p-6 overflow-y-auto space-y-5 flex-1 custom-scrollbar text-xs">

                        <!-- BANNER GENERAL DE ERROR INLINE -->
                        <template x-if="errorEstadoModal">
                            <div class="p-3.5 bg-rose-50 border border-rose-300 rounded-xl text-xs text-rose-800 flex items-start justify-between gap-2 shadow-xs transition-all">
                                <div class="flex items-start gap-2.5">
                                    <i class="fas fa-circle-exclamation text-rose-600 mt-0.5 text-base shrink-0"></i>
                                    <div>
                                        <p class="font-bold text-rose-950 leading-snug">Atención</p>
                                        <p class="text-[11px] text-rose-800 mt-0.5" x-text="errorEstadoModal"></p>
                                    </div>
                                </div>
                                <button type="button" @click="errorEstadoModal = ''" class="text-rose-400 hover:text-rose-700 p-1 cursor-pointer">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </template>

                        <!-- ESTADO ACTUAL Y DETALLES -->
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Estado Actual</span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-sm font-black text-slate-800" x-text="estadoModalData.estadoActual"></span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                        :class="estadoModalData.tipoVenta === 'Envio' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'"
                                        x-text="estadoModalData.tipoVenta === 'Envio' ? 'Entrega a Domicilio' : 'Venta en Tienda'"></span>
                                </div>
                            </div>
                            <template x-if="estadoModalData.puedeDevolver">
                                <div class="text-right">
                                    <span class="text-[10px] uppercase font-bold text-amber-600 block flex items-center justify-end gap-1">
                                        <i class="fas fa-shield-halved text-[9px]"></i> Garantía Cambio / Devolución
                                    </span>
                                    <span class="text-xs font-black text-amber-700" x-text="estadoModalData.textoGarantia || (estadoModalData.diasRestantes + ' días restantes')"></span>
                                </div>
                            </template>
                        </div>

                        <!-- SELECCIÓN DEL NUEVO ESTADO -->
                        <div id="seccion-seleccion-nuevo-estado">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                    Selecciona el nuevo estado <span class="text-rose-500">*</span>
                                </label>
                                <span x-show="errorNuevoEstado" class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                    Selección requerida
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-1 rounded-xl transition-all"
                                :class="errorNuevoEstado ? 'border border-rose-400 bg-rose-50/30' : ''">
                                <template x-for="st in estadoModalData.estadosPermitidos" :key="st">
                                    <button
                                        type="button"
                                        @click="seleccionarNuevoEstado(st)"
                                        class="p-3.5 rounded-xl border text-left transition-all duration-200 flex items-start gap-3 cursor-pointer group relative overflow-hidden"
                                        :class="nuevoEstadoSeleccionado === st ? (st === 'Cambio' ? 'border-teal-500 bg-teal-50/70 shadow-sm ring-2 ring-teal-500/25' : (st === 'Devolución' ? 'border-purple-500 bg-purple-50/70 shadow-sm ring-2 ring-purple-500/25' : 'border-blue-600 bg-blue-50/70 shadow-sm ring-2 ring-blue-500/20')) : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50/60'">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 text-sm font-bold mt-0.5 shadow-2xs transition-transform group-hover:scale-105"
                                            :class="{
                                                 'bg-blue-100 text-blue-700': st === 'Confirmada',
                                                 'bg-indigo-100 text-indigo-700': st === 'En ruta',
                                                 'bg-emerald-100 text-emerald-700': st === 'Entregada',
                                                 'bg-rose-100 text-rose-700': st === 'Cancelada',
                                                 'bg-purple-100 text-purple-700': st === 'Devolución',
                                                 'bg-teal-100 text-teal-700': st === 'Cambio'
                                             }">
                                            <i class="fas" :class="{
                                                'fa-circle-check': st === 'Confirmada',
                                                'fa-truck-fast': st === 'En ruta',
                                                'fa-box-open': st === 'Entregada',
                                                'fa-ban': st === 'Cancelada',
                                                'fa-rotate-left': st === 'Devolución',
                                                'fa-arrow-right-arrow-left': st === 'Cambio'
                                            }"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <h4 class="font-bold text-slate-900 leading-tight flex items-center gap-1.5"
                                                    :class="{
                                                        'text-teal-950 font-black': st === 'Cambio' && nuevoEstadoSeleccionado === st,
                                                        'text-purple-950 font-black': st === 'Devolución' && nuevoEstadoSeleccionado === st
                                                    }">
                                                    <span x-text="st"></span>
                                                </h4>
                                                <!-- ADORNO / BADGE DE CAMBIO -->
                                                <template x-if="st === 'Cambio'">
                                                    <span class="inline-flex items-center gap-1 text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded-full bg-teal-100 text-teal-800 border border-teal-200">
                                                        <i class="fas fa-arrows-rotate text-[8px] animate-spin-slow"></i> Por Garantía
                                                    </span>
                                                </template>
                                                <!-- BADGE DE DEVOLUCIÓN -->
                                                <template x-if="st === 'Devolución'">
                                                    <span class="inline-flex items-center gap-1 text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                                                        <i class="fas fa-box text-[8px]"></i> Retorno Paquete
                                                    </span>
                                                </template>
                                            </div>
                                            <p class="text-[10px] text-slate-500 mt-1 leading-snug" x-text="obtenerDescripcionEstado(st)"></p>
                                        </div>
                                    </button>
                                </template>
                            </div>
                            <span x-show="errorNuevoEstado" class="text-[11px] font-bold text-rose-600 flex items-center gap-1 mt-1 pl-1">
                                <i class="fas fa-triangle-exclamation text-[10px]"></i> Debes elegir el nuevo estado al que transicionará la venta.
                            </span>
                        </div>

                        <!-- SECCIÓN DINÁMICA: EVIDENCIA PAQUETERÍA (EN RUTA) -->
                        <div
                            id="seccion-foto-paquete"
                            x-show="nuevoEstadoSeleccionado === 'En ruta'"
                            class="space-y-3 p-4 rounded-xl border transition-all"
                            :class="errorFotoPaquete ? 'bg-rose-50/80 border-rose-400 ring-2 ring-rose-500/20' : 'bg-indigo-50/60 border-indigo-200'"
                            x-transition>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-camera text-sm" :class="errorFotoPaquete ? 'text-rose-600' : 'text-indigo-600'"></i>
                                    <h4 class="font-bold text-xs uppercase tracking-wide" :class="errorFotoPaquete ? 'text-rose-950 font-black' : 'text-indigo-950'">
                                        Fotografía del Paquete entregado a Paquetería <span class="text-rose-500">*</span>
                                    </h4>
                                </div>
                                <span x-show="errorFotoPaquete" class="text-[10px] font-bold px-2 py-0.5 rounded-full text-rose-700 bg-rose-100 border border-rose-300 animate-pulse">
                                    ¡Fotografía requerida!
                                </span>
                            </div>

                            <!-- ALERTA INLINE VISIBLE CUANDO FALTA LA FOTO -->
                            <template x-if="errorFotoPaquete">
                                <div class="p-3 bg-rose-100/90 border border-rose-300 rounded-xl text-rose-900 text-xs font-semibold flex items-start gap-2.5 shadow-2xs">
                                    <i class="fas fa-circle-exclamation text-rose-600 mt-0.5 text-base shrink-0"></i>
                                    <div class="flex-1">
                                        <p class="font-bold leading-snug">¡Falta la fotografía del paquete!</p>
                                        <p class="text-[11px] text-rose-700 mt-0.5 leading-relaxed" x-text="errorFotoPaqueteMensaje || 'Para cambiar al estado &quot;En ruta&quot; es obligatorio adjuntar la fotografía del paquete entregado a la paquetería.'"></p>
                                    </div>
                                </div>
                            </template>

                            <p class="text-[11px]" :class="errorFotoPaquete ? 'text-rose-700 font-medium' : 'text-slate-600'">
                                Toma o adjunta la fotografía del paquete con la viñeta/guía tras entregarlo a la empresa de mensajería.
                            </p>

                            <template x-if="!previewPaquete">
                                <div>
                                    <label class="border-2 border-dashed bg-white rounded-xl p-5 flex flex-col items-center justify-center cursor-pointer transition-colors group shadow-2xs"
                                        :class="errorFotoPaquete ? 'border-rose-400 hover:border-rose-600 hover:bg-rose-50/40 ring-1 ring-rose-300' : 'border-indigo-300 hover:border-indigo-500'">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform"
                                            :class="errorFotoPaquete ? 'bg-rose-100 text-rose-600' : 'bg-indigo-50 text-indigo-600'">
                                            <i class="fas" :class="errorFotoPaquete ? 'fa-triangle-exclamation' : 'fa-cloud-arrow-up'"></i>
                                        </div>
                                        <span class="font-bold text-xs" :class="errorFotoPaquete ? 'text-rose-800' : 'text-indigo-700'">Seleccionar fotografía del paquete</span>
                                        <span class="text-[10px] mt-1" :class="errorFotoPaquete ? 'text-rose-500' : 'text-slate-400'">Formatos soportados: JPG, PNG, WEBP (Máx 5MB)</span>
                                        <input type="file" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" @change="onArchivoPaqueteSeleccionado($event)">
                                    </label>
                                    <template x-if="errorFotoPaquete">
                                        <span class="text-[11px] font-bold text-rose-600 flex items-center gap-1 mt-1.5 pl-1">
                                            <i class="fas fa-arrow-up text-[10px]"></i> Haz clic arriba para seleccionar la imagen del paquete.
                                        </span>
                                    </template>
                                </div>
                            </template>

                            <template x-if="previewPaquete">
                                <div class="flex items-center gap-3 p-2 bg-white rounded-xl border border-indigo-200">
                                    <img :src="previewPaquete" alt="Preview Paquete" class="w-16 h-16 rounded-lg object-cover border border-slate-200 shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-slate-800 truncate" x-text="nombreArchivoPaquete"></p>
                                        <span class="text-[10px] text-emerald-600 font-bold flex items-center gap-1 mt-0.5">
                                            <i class="fas fa-check-circle"></i> Imagen lista para adjuntar
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removerArchivoPaquete()"
                                        class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                        title="Eliminar imagen">
                                        <i class="fas fa-trash-can text-sm"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- SECCIÓN DINÁMICA: DEVOLUCIÓN O CAMBIO (EVIDENCIA Y MOTIVO) -->
                        <div
                            id="seccion-foto-devolucion"
                            x-show="nuevoEstadoSeleccionado === 'Devolución' || nuevoEstadoSeleccionado === 'Cambio'"
                            class="space-y-3 p-4 rounded-xl border transition-all"
                            :class="errorFotoDevolucion ? 'bg-rose-50/80 border-rose-400 ring-2 ring-rose-500/20' : (nuevoEstadoSeleccionado === 'Cambio' ? 'bg-teal-50/60 border-teal-200' : 'bg-purple-50/60 border-purple-200')"
                            x-transition>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas text-sm" :class="errorFotoDevolucion ? 'fa-triangle-exclamation text-rose-600' : (nuevoEstadoSeleccionado === 'Cambio' ? 'fa-arrows-rotate text-teal-600' : 'fa-rotate-left text-purple-600')"></i>
                                    <h4 class="font-bold text-xs uppercase tracking-wide" :class="errorFotoDevolucion ? 'text-rose-950 font-black' : (nuevoEstadoSeleccionado === 'Cambio' ? 'text-teal-950 font-black' : 'text-purple-950 font-black')">
                                        <span x-text="nuevoEstadoSeleccionado === 'Cambio' ? 'Evidencia y Justificación de Cambio' : 'Evidencia y Justificación de Devolución'"></span> <span class="text-rose-500">*</span>
                                    </h4>
                                </div>
                                <span x-show="errorFotoDevolucion" class="text-[10px] font-bold px-2 py-0.5 rounded-full text-rose-700 bg-rose-100 border border-rose-300 animate-pulse">
                                    ¡Fotografía requerida!
                                </span>
                            </div>

                            <!-- ALERTA INLINE VISIBLE CUANDO FALTA LA FOTO DE DEVOLUCIÓN O CAMBIO -->
                            <template x-if="errorFotoDevolucion">
                                <div class="p-3 bg-rose-100/90 border border-rose-300 rounded-xl text-rose-900 text-xs font-semibold flex items-start gap-2.5 shadow-2xs">
                                    <i class="fas fa-circle-exclamation text-rose-600 mt-0.5 text-base shrink-0"></i>
                                    <div class="flex-1">
                                        <p class="font-bold leading-snug" x-text="nuevoEstadoSeleccionado === 'Cambio' ? '¡Falta la fotografía del paquete para cambio!' : '¡Falta la fotografía de devolución!'"></p>
                                        <p class="text-[11px] text-rose-700 mt-0.5 leading-relaxed" x-text="errorFotoDevolucionMensaje || 'Debes adjuntar la fotografía del paquete correspondiente.'"></p>
                                    </div>
                                </div>
                            </template>

                            <p class="text-[11px]" :class="errorFotoDevolucion ? 'text-rose-700 font-medium' : 'text-slate-600'"
                                x-text="nuevoEstadoSeleccionado === 'Cambio' ? 'Adjunta la fotografía del estado del paquete/producto para proceder con el cambio en tienda.' : 'Adjunta la fotografía del estado del paquete/producto devuelto.'">
                            </p>

                            <template x-if="!previewDevolucion">
                                <div>
                                    <label class="border-2 border-dashed bg-white rounded-xl p-5 flex flex-col items-center justify-center cursor-pointer transition-colors group shadow-2xs"
                                        :class="errorFotoDevolucion ? 'border-rose-400 hover:border-rose-600 hover:bg-rose-50/40 ring-1 ring-rose-300' : (nuevoEstadoSeleccionado === 'Cambio' ? 'border-teal-300 hover:border-teal-500' : 'border-purple-300 hover:border-purple-500')">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform"
                                            :class="errorFotoDevolucion ? 'bg-rose-100 text-rose-600' : (nuevoEstadoSeleccionado === 'Cambio' ? 'bg-teal-50 text-teal-600' : 'bg-purple-50 text-purple-600')">
                                            <i class="fas" :class="errorFotoDevolucion ? 'fa-triangle-exclamation' : 'fa-camera'"></i>
                                        </div>
                                        <span class="font-bold text-xs" :class="errorFotoDevolucion ? 'text-rose-800' : (nuevoEstadoSeleccionado === 'Cambio' ? 'text-teal-700' : 'text-purple-700')"
                                            x-text="nuevoEstadoSeleccionado === 'Cambio' ? 'Subir fotografía del paquete para cambio' : 'Subir fotografía del paquete devuelto'"></span>
                                        <span class="text-[10px] mt-1" :class="errorFotoDevolucion ? 'text-rose-500' : 'text-slate-400'">Formatos soportados: JPG, PNG, WEBP (Máx 5MB)</span>
                                        <input type="file" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" @change="onArchivoDevolucionSeleccionado($event)">
                                    </label>
                                    <template x-if="errorFotoDevolucion">
                                        <span class="text-[11px] font-bold text-rose-600 flex items-center gap-1 mt-1.5 pl-1">
                                            <i class="fas fa-arrow-up text-[10px]"></i> Haz clic arriba para seleccionar la imagen requerida.
                                        </span>
                                    </template>
                                </div>
                            </template>

                            <template x-if="previewDevolucion">
                                <div class="flex items-center gap-3 p-2 bg-white rounded-xl border"
                                    :class="nuevoEstadoSeleccionado === 'Cambio' ? 'border-teal-200' : 'border-purple-200'">
                                    <img :src="previewDevolucion" alt="Preview Devolución / Cambio" class="w-16 h-16 rounded-lg object-cover border border-slate-200 shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-slate-800 truncate" x-text="nombreArchivoDevolucion"></p>
                                        <span class="text-[10px] text-emerald-600 font-bold flex items-center gap-1 mt-0.5">
                                            <i class="fas fa-check-circle"></i> Fotografía lista
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removerArchivoDevolucion()"
                                        class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                        title="Eliminar imagen">
                                        <i class="fas fa-trash-can text-sm"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- CAMPO DE OBSERVACIONES / MOTIVO -->
                        <div id="seccion-observaciones-estado">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                    Observaciones / Motivo del cambio
                                    <span x-show="nuevoEstadoSeleccionado === 'Cancelada' || nuevoEstadoSeleccionado === 'Devolución' || nuevoEstadoSeleccionado === 'Cambio'" class="text-rose-500">* (Obligatorio)</span>
                                </label>
                                <span x-show="errorObservacionesEstado" class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                    Requerido
                                </span>
                            </div>
                            <textarea
                                id="input-observaciones-estado"
                                x-model="observacionesEstado"
                                @input="errorObservacionesEstado = false"
                                rows="3"
                                placeholder="Ingresa notas operativas, motivo de cancelación o justificación de devolución..."
                                :class="errorObservacionesEstado ? 'border-rose-500 bg-rose-50/40 text-rose-900 focus:border-rose-500 focus:ring-rose-500/20 ring-1 ring-rose-400' : 'border-slate-300 text-slate-700 focus:border-blue-500 focus:ring-blue-500/10'"
                                class="w-full rounded-xl border p-3 text-xs font-semibold focus:outline-none focus:ring-4 transition-all resize-none"></textarea>
                            <span x-show="errorObservacionesEstado" class="text-[11px] font-bold text-rose-600 flex items-center gap-1 mt-1 pl-1">
                                <i class="fas fa-triangle-exclamation text-[10px]"></i>
                                <span x-text="errorObservacionesEstadoMensaje || 'Por favor ingresa el motivo u observaciones para continuar.'"></span>
                            </span>
                        </div>

                        <!-- ADVERTENCIA DE SEGURIDAD / IMPACTO -->
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-2.5 text-amber-900">
                            <i class="fas fa-circle-exclamation text-amber-600 text-sm mt-0.5 shrink-0"></i>
                            <div class="text-[11px]">
                                <span class="font-bold block">Acción con impacto en inventario y comisiones:</span>
                                <span class="text-amber-800">
                                    Si cancelas la venta o procesas devolución, el stock de las variantes se devolverá automáticamente a la bodega y se anularán las comisiones asociadas.
                                </span>
                            </div>
                        </div>

                    </div>

                    <!-- PIE DEL MODAL CON BOTONES -->
                    <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl">
                        <button
                            type="button"
                            @click="cerrarModalCambiarEstado()"
                            class="px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                            Cancelar
                        </button>

                        <button
                            type="button"
                            @click="guardarCambioEstado()"
                            :disabled="procesandoEstado"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none shadow-blue-600/20">
                            <i class="fas" :class="procesandoEstado ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                            <span x-text="procesandoEstado ? 'Actualizando...' : 'Confirmar Cambio'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    <!-- MODAL DE DEVOLUCIÓN (TRADUCIDO A ALPINE.JS) -->
        <div 
            x-show="openDevolucionModal" 
            x-cloak
            class="fixed inset-0 z-[80] overflow-y-auto"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <div 
                x-show="openDevolucionModal"
                x-transition.opacity
                class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
                @click="openDevolucionModal = false"
            ></div>

            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div 
                    x-show="openDevolucionModal"
                    x-transition
                    class="relative z-10 w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden text-left border border-slate-200"
                    @click.away="openDevolucionModal = false"
                >
                    <div class="bg-red-600 px-6 py-4 flex items-center justify-between text-white shadow-md">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-rotate-left text-white text-base"></i>
                            <span class="text-sm font-black uppercase tracking-wider text-white">Registrar Devolución</span>
                        </div>
                        <button type="button" @click="openDevolucionModal = false" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                    
                    <form action="/devoluciones/venta" method="POST" enctype="multipart/form-data" novalidate class="p-6 space-y-6" @click.stop onsubmit="event.preventDefault(); if (!this.checkValidity()) { let errorMsg = 'Por favor, completa todos los campos requeridos.'; this.querySelectorAll('input[type=number]').forEach(i => { if(i.validity.rangeOverflow) errorMsg = 'No puedes devolver más unidades de las que vendiste.'; }); Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 5000, timerProgressBar: true, icon: 'error', title: 'Datos inválidos', text: errorMsg }); return; } Swal.fire({ title: '¿Estás seguro?', text: 'Verifica que las cantidades sean correctas antes de procesar.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Sí, procesar', cancelButtonText: 'Cancelar' }).then((result) => { if (result.isConfirmed) { this.submit(); } });">
                        @csrf
                        <input type="hidden" name="venta_id" :value="ventaSeleccionada?.id">

                        <div>
                            <h3 class="text-sm font-bold text-slate-900 mb-3 uppercase tracking-wider">Productos a Devolver</h3>
                            <div class="overflow-x-auto border border-slate-200 rounded-xl max-h-60 overflow-y-auto custom-scrollbar">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold sticky top-0">
                                        <tr>
                                            <th class="px-4 py-3 w-10 text-center">Sel.</th>
                                            <th class="px-4 py-3">Producto / Variante</th>
                                            <th class="px-4 py-3 text-center w-24">Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <template x-for="(det, index) in ventaSeleccionada?.detalles" :key="det.id">
                                            <tr class="hover:bg-slate-50/60 transition-colors">
                                                <td class="px-4 py-3 text-center">
                                                    <input type="hidden" :name="'productos['+index+'][id_variante]'" :value="det.id_variante">
                                                    <input type="checkbox" :name="'productos['+index+'][seleccionado]'" value="1" class="rounded text-red-600 focus:ring-red-500 bg-slate-100 border-slate-300 w-4 h-4 cursor-pointer">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <p class="font-bold text-slate-900 text-xs" x-text="det.variante?.producto?.nombre || 'Producto'"></p>
                                                    <p class="text-[11px] text-slate-500" x-text="det.variante?.nombre_variante || 'Variante'"></p>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" :name="'productos['+index+'][cantidad]'" value="1" min="1" :max="det.cantidad" class="w-full text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring focus:ring-red-200 py-1.5 px-2 text-center">
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-4 bg-slate-50 p-4 rounded-xl border border-slate-200" x-data="{ tipo_resolucion: '' }">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Detalles de la Resolución</h3>
                            
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700">Resolución al Cliente <span class="text-red-500">*</span></label>
                                <select name="tipo_resolucion" x-model="tipo_resolucion" required class="w-full text-xs bg-white border border-slate-300 rounded-lg shadow-sm text-slate-700 px-3 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors appearance-none cursor-pointer">
                                    <option value="" disabled selected>¿Qué acción se tomará?</option>
                                    <option value="reembolso_tienda">Reembolso de dinero (Producto intacto / Paquete no recibido)</option>
                                    <option value="reembolso_cuarentena">Reembolso de dinero (Producto dañado de fábrica)</option>
                                    <option value="cambio_tienda">Cambio físico (Talla/Color equivocado)</option>
                                    <option value="cambio_cuarentena">Cambio físico (Viene defectuoso)</option>
                                </select>

                                <!-- INFO BOX DINÁMICO -->
                                <template x-if="tipo_resolucion">
                                    <div x-transition.opacity.duration.300ms class="mt-2.5 bg-blue-50 border border-blue-200 text-blue-800 p-3 rounded-lg text-xs flex items-start gap-2.5 shadow-sm">
                                        <template x-if="tipo_resolucion.includes('reembolso')">
                                            <i class="fas fa-money-bill-wave mt-0.5 shrink-0 text-blue-600"></i>
                                        </template>
                                        <template x-if="tipo_resolucion.includes('cambio')">
                                            <i class="fas fa-boxes-stacked mt-0.5 shrink-0 text-blue-600"></i>
                                        </template>
                                        <div class="leading-relaxed font-medium">
                                            <template x-if="tipo_resolucion.includes('reembolso')">
                                                <p><strong class="font-bold text-blue-900">Impacto:</strong> Se retornará el artículo a bodega y se registrará un EGRESO de dinero en caja.</p>
                                            </template>
                                            <template x-if="tipo_resolucion.includes('cambio')">
                                                <p><strong class="font-bold text-blue-900">Impacto:</strong> Entra el artículo devuelto y sale automáticamente uno nuevo del inventario. NO hay movimiento de efectivo.</p>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-700">Motivo de la Devolución <span class="text-red-500">*</span></label>
                                <textarea name="motivo" required rows="3" class="w-full text-xs bg-white border border-slate-300 rounded-lg shadow-sm text-slate-700 px-3 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors placeholder-slate-400 resize-none" placeholder="Escriba el motivo detallado por el cual el cliente devuelve la mercancía..."></textarea>
                            </div>

                            <div class="space-y-1 pt-1">
                                <label class="text-xs font-bold text-slate-700">Comprobante (Opcional)</label>
                                <div class="relative">
                                    <input type="file" name="comprobante" accept="image/*" class="w-full text-xs text-slate-500 bg-white border border-slate-300 rounded-lg shadow-sm px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors file:mr-4 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                </div>
                                <p class="text-[10px] text-slate-500 mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-camera text-slate-400"></i> Sube una fotografía de evidencia si el artículo llegó defectuoso.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                            <button type="button" @click="openDevolucionModal = false" class="px-5 py-2 bg-white border border-slate-300 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 transition-colors cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-colors shadow-md shadow-red-600/20 cursor-pointer flex items-center gap-2">
                                <i class="fas fa-check"></i>
                                Procesar Devolución
                            </button>
                        </div>
                    </form>
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
                comprobanteZoomUrl: null,
                comprobanteZoomNombre: '',
                comprobanteZoomTitulo: '',
                openDevolucionModal: false,

                // --- MODAL DE CAMBIO DE ESTADO (MÁQUINA DE ESTADOS) ---
                modalCambiarEstadoAbierto: false,
                estadoModalData: {
                    id: null,
                    folio: '',
                    estadoActual: '',
                    tipoVenta: '',
                    puedeDevolver: false,
                    estadosPermitidos: [],
                    diasRestantes: 0,
                    textoGarantia: '',
                    fechaLimite: ''
                },
                nuevoEstadoSeleccionado: null,
                observacionesEstado: '',
                archivoPaquete: null,
                previewPaquete: null,
                nombreArchivoPaquete: '',
                archivoDevolucion: null,
                previewDevolucion: null,
                nombreArchivoDevolucion: '',
                procesandoEstado: false,
                errorEstadoModal: '',
                errorNuevoEstado: false,
                errorFotoPaquete: false,
                errorFotoPaqueteMensaje: '',
                errorFotoDevolucion: false,
                errorFotoDevolucionMensaje: '',
                errorObservacionesEstado: false,
                errorObservacionesEstadoMensaje: '',

                abrirModalCambiarEstado(id, estadoActual, tipoVenta, puedeDevolver, estadosPermitidos, diasRestantes, textoGarantia, fechaLimite) {
                    this.estadoModalData = {
                        id: id,
                        folio: '#VNT-' + String(id).padStart(5, '0'),
                        estadoActual: estadoActual,
                        tipoVenta: tipoVenta,
                        puedeDevolver: puedeDevolver,
                        estadosPermitidos: Array.isArray(estadosPermitidos) ? estadosPermitidos : [],
                        diasRestantes: diasRestantes || 0,
                        textoGarantia: textoGarantia || '',
                        fechaLimite: fechaLimite || ''
                    };

                    this.nuevoEstadoSeleccionado = this.estadoModalData.estadosPermitidos.length > 0 ?
                        this.estadoModalData.estadosPermitidos[0] :
                        null;
                    this.observacionesEstado = '';
                    this.errorEstadoModal = '';
                    this.errorNuevoEstado = false;
                    this.errorFotoPaquete = false;
                    this.errorFotoPaqueteMensaje = '';
                    this.errorFotoDevolucion = false;
                    this.errorFotoDevolucionMensaje = '';
                    this.errorObservacionesEstado = false;
                    this.errorObservacionesEstadoMensaje = '';
                    this.removerArchivoPaquete();
                    this.removerArchivoDevolucion();
                    this.procesandoEstado = false;
                    this.modalCambiarEstadoAbierto = true;
                },

                cerrarModalCambiarEstado() {
                    this.modalCambiarEstadoAbierto = false;
                    this.removerArchivoPaquete();
                    this.removerArchivoDevolucion();
                    this.observacionesEstado = '';
                    this.nuevoEstadoSeleccionado = null;
                    this.errorEstadoModal = '';
                    this.errorNuevoEstado = false;
                    this.errorFotoPaquete = false;
                    this.errorFotoPaqueteMensaje = '';
                    this.errorFotoDevolucion = false;
                    this.errorFotoDevolucionMensaje = '';
                    this.errorObservacionesEstado = false;
                    this.errorObservacionesEstadoMensaje = '';
                },

                seleccionarNuevoEstado(st) {
                    this.nuevoEstadoSeleccionado = st;
                    this.errorNuevoEstado = false;
                    this.errorEstadoModal = '';
                    this.errorFotoPaquete = false;
                    this.errorFotoPaqueteMensaje = '';
                    this.errorFotoDevolucion = false;
                    this.errorFotoDevolucionMensaje = '';
                    this.errorObservacionesEstado = false;
                    this.errorObservacionesEstadoMensaje = '';
                },

                obtenerDescripcionEstado(st) {
                    switch (st) {
                        case 'Confirmada':
                            return 'Venta verificada y lista para empaque y despacho.';
                        case 'En ruta':
                            return 'Entregado a paquetería / encomienda. Requiere foto del paquete.';
                        case 'Entregada':
                            return 'Entregado al cliente. Inicia garantía de devolución / cambio.';
                        case 'Cancelada':
                            return 'Cancela la venta. Reintegra stock a bodega y anula comisiones.';
                        case 'Devolución':
                            return 'Paquete no recibido por el cliente en ruta. Requiere motivo y foto del paquete.';
                        case 'Cambio':
                            return 'Retorno por garantía. Requiere justificación y foto del paquete.';
                        default:
                            return '';
                    }
                },

                onArchivoPaqueteSeleccionado(e) {
                    const file = e.target.files ? e.target.files[0] : null;
                    if (!file) return;

                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        this.errorFotoPaquete = true;
                        this.errorFotoPaqueteMensaje = 'El archivo debe ser una imagen válida (JPG, PNG o WEBP).';
                        e.target.value = '';
                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        this.errorFotoPaquete = true;
                        this.errorFotoPaqueteMensaje = 'La fotografía del paquete no puede exceder los 5MB.';
                        e.target.value = '';
                        return;
                    }

                    this.archivoPaquete = file;
                    this.nombreArchivoPaquete = file.name;
                    this.errorFotoPaquete = false;
                    this.errorFotoPaqueteMensaje = '';
                    const reader = new FileReader();
                    reader.onload = (evt) => {
                        this.previewPaquete = evt.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                removerArchivoPaquete() {
                    this.archivoPaquete = null;
                    this.previewPaquete = null;
                    this.nombreArchivoPaquete = '';
                    this.errorFotoPaquete = false;
                    this.errorFotoPaqueteMensaje = '';
                },

                onArchivoDevolucionSeleccionado(e) {
                    const file = e.target.files ? e.target.files[0] : null;
                    if (!file) return;

                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        this.errorFotoDevolucion = true;
                        this.errorFotoDevolucionMensaje = 'El archivo debe ser una imagen válida (JPG, PNG o WEBP).';
                        e.target.value = '';
                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        this.errorFotoDevolucion = true;
                        this.errorFotoDevolucionMensaje = 'La fotografía de devolución no puede exceder los 5MB.';
                        e.target.value = '';
                        return;
                    }

                    this.archivoDevolucion = file;
                    this.nombreArchivoDevolucion = file.name;
                    this.errorFotoDevolucion = false;
                    this.errorFotoDevolucionMensaje = '';
                    const reader = new FileReader();
                    reader.onload = (evt) => {
                        this.previewDevolucion = evt.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                removerArchivoDevolucion() {
                    this.archivoDevolucion = null;
                    this.previewDevolucion = null;
                    this.nombreArchivoDevolucion = '';
                    this.errorFotoDevolucion = false;
                    this.errorFotoDevolucionMensaje = '';
                },

                async guardarCambioEstado() {
                    if (this.procesandoEstado) return;

                    this.errorEstadoModal = '';
                    this.errorNuevoEstado = false;
                    this.errorFotoPaquete = false;
                    this.errorFotoDevolucion = false;
                    this.errorObservacionesEstado = false;

                    if (!this.nuevoEstadoSeleccionado) {
                        this.errorNuevoEstado = true;
                        this.$nextTick(() => {
                            document.getElementById('seccion-seleccion-nuevo-estado')?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        });
                        return;
                    }

                    // Validar requisitos por estado
                    if (this.nuevoEstadoSeleccionado === 'En ruta' && !this.archivoPaquete) {
                        this.errorFotoPaquete = true;
                        this.errorFotoPaqueteMensaje = 'Para cambiar al estado "En ruta" es obligatorio adjuntar la fotografía del paquete entregado a la paquetería.';
                        this.$nextTick(() => {
                            document.getElementById('seccion-foto-paquete')?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        });
                        return;
                    }

                    if (this.nuevoEstadoSeleccionado === 'Cancelada' && !this.observacionesEstado.trim()) {
                        this.errorObservacionesEstado = true;
                        this.errorObservacionesEstadoMensaje = 'Por favor ingresa en el campo de observaciones el motivo de la cancelación de la venta.';
                        this.$nextTick(() => {
                            const el = document.getElementById('input-observaciones-estado');
                            el?.focus();
                            el?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        });
                        return;
                    }

                    if (this.nuevoEstadoSeleccionado === 'Devolución' || this.nuevoEstadoSeleccionado === 'Cambio') {
                        const labelEstado = this.nuevoEstadoSeleccionado === 'Cambio' ? 'cambio' : 'devolución';
                        if (!this.observacionesEstado.trim()) {
                            this.errorObservacionesEstado = true;
                            this.errorObservacionesEstadoMensaje = `Por favor ingresa la justificación o motivo por el cual se procesa el ${labelEstado}.`;
                            this.$nextTick(() => {
                                const el = document.getElementById('input-observaciones-estado');
                                el?.focus();
                                el?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                            });
                            return;
                        }

                        if (!this.archivoDevolucion) {
                            this.errorFotoDevolucion = true;
                            this.errorFotoDevolucionMensaje = `Para procesar un ${labelEstado} debes adjuntar la fotografía del paquete.`;
                            this.$nextTick(() => {
                                document.getElementById('seccion-foto-devolucion')?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                            });
                            return;
                        }
                    }

                    this.procesandoEstado = true;
                    const token = document.querySelector('meta[name="csrf-token"]')?.content;

                    const formData = new FormData();
                    formData.append('_method', 'PATCH');
                    if (token) formData.append('_token', token);
                    formData.append('estado', this.nuevoEstadoSeleccionado);
                    formData.append('observaciones', this.observacionesEstado.trim());

                    if (this.nuevoEstadoSeleccionado === 'En ruta' && this.archivoPaquete) {
                        formData.append('comprobante_paquete', this.archivoPaquete);
                    }

                    if ((this.nuevoEstadoSeleccionado === 'Devolución' || this.nuevoEstadoSeleccionado === 'Cambio') && this.archivoDevolucion) {
                        formData.append('comprobante_devolucion', this.archivoDevolucion);
                    }

                    try {
                        const response = await fetch(`/ventas/${this.estadoModalData.id}/estado`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            this.errorEstadoModal = data.message || 'No se pudo actualizar el estado de la venta.';
                            this.$nextTick(() => {
                                document.getElementById('modal-cambiar-estado-body')?.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                });
                            });
                            return;
                        }

                        this.cerrarModalCambiarEstado();

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Estado Actualizado!',
                                text: data.message,
                                timer: 1600,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            window.location.reload();
                        }

                    } catch (err) {
                        console.error('Error al actualizar estado:', err);
                        this.errorEstadoModal = err.message || 'Ocurrió un error inesperado al actualizar el estado.';
                        this.$nextTick(() => {
                            document.getElementById('modal-cambiar-estado-body')?.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        });
                    } finally {
                        this.procesandoEstado = false;
                    }
                },

                verImagenAmpliada(url, nombre = '', titulo = '') {
                    this.comprobanteZoomUrl = url;
                    this.comprobanteZoomNombre = nombre || 'VNT_comprobante';
                    this.comprobanteZoomTitulo = titulo || 'Evidencia de Venta';
                },

                cerrarImagenAmpliada() {
                    this.comprobanteZoomUrl = null;
                    this.comprobanteZoomNombre = '';
                    this.comprobanteZoomTitulo = '';
                },

                descargarImagenActual() {
                    if (!this.comprobanteZoomUrl) return;
                    const url = this.comprobanteZoomUrl;
                    const extMatch = url.match(/\.([a-zA-Z0-9]+)(?:\?|#|$)/);
                    const ext = extMatch ? extMatch[1] : 'jpg';
                    const filename = (this.comprobanteZoomNombre || 'VNT_comprobante') + '.' + ext;

                    fetch(url)
                        .then(res => res.blob())
                        .then(blob => {
                            const blobUrl = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = blobUrl;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(blobUrl);
                            document.body.removeChild(a);
                        })
                        .catch(() => {
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = filename;
                            a.target = '_blank';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        });
                },

                nombreComprobanteVenta(tipo) {
                    if (!this.ventaSeleccionada) return 'VNT_' + tipo;
                    const folio = 'VNT-' + String(this.ventaSeleccionada.id).padStart(5, '0');
                    const fecha = this.ventaSeleccionada.fecha ? this.ventaSeleccionada.fecha.slice(0, 10) : 'fecha';
                    return `${folio}_${fecha}_${tipo}`;
                },

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

                impresionDesdeDetalle: false,

                cerrarDetalle() {
                    this.modalDetalleAbierto = false;
                    this.ventaSeleccionada = null;
                    this.impresionDesdeDetalle = false;
                },

                abrirModalImpresion(id, metodoPago, total) {
                    this.ventaImprimirId = id;
                    this.metodoPagoImprimir = metodoPago;
                    this.totalImprimir = total;
                    this.tipoComprobante = 'ticket';
                    this.impresionDesdeDetalle = false;
                    this.modalImprimirAbierto = true;
                },

                abrirModalImpresionDesdeDetalle() {
                    if (!this.ventaSeleccionada) return;
                    this.abrirModalImpresion(
                        this.ventaSeleccionada.id,
                        this.ventaSeleccionada.metodo_pago,
                        this.ventaSeleccionada.total
                    );
                    this.impresionDesdeDetalle = true;
                },

                cerrarModalImpresion() {
                    this.modalImprimirAbierto = false;
                    if (this.impresionDesdeDetalle) {
                        this.modalDetalleAbierto = true;
                        setTimeout(() => {
                            this.modalDetalleAbierto = true;
                        }, 30);
                    }
                },

                // --- VALIDACIÓN Y FORMATO LEGAL DE DOCUMENTOS (EL SALVADOR) ---

                formatearDuiNit(valor) {
                    if (!valor) return '';
                    const digits = String(valor).replace(/\D/g, '').slice(0, 14);
                    if (!digits) return '';

                    // Si tiene hasta 9 dígitos: formato DUI ########-#
                    if (digits.length <= 8) {
                        return digits;
                    }
                    if (digits.length === 9) {
                        return `${digits.slice(0, 8)}-${digits.slice(8)}`;
                    }

                    // Si tiene más de 9 dígitos: formato NIT ####-######-###-# (14 dígitos)
                    const p1 = digits.slice(0, 4);
                    const p2 = digits.slice(4, 10);
                    const p3 = digits.slice(10, 13);
                    const p4 = digits.slice(13, 14);

                    let res = p1;
                    if (p2) res += '-' + p2;
                    if (p3) res += '-' + p3;
                    if (p4) res += '-' + p4;
                    return res;
                },

                formatearNrc(valor) {
                    if (!valor) return '';
                    const digits = String(valor).replace(/\D/g, '').slice(0, 8);
                    if (digits.length <= 1) return digits;
                    return `${digits.slice(0, -1)}-${digits.slice(-1)}`;
                },

                validarDuiSV(digits) {
                    if (!digits || digits.length !== 9) return false;
                    if (!/[1-9]/.test(digits)) return false; // Un DUI de sólo ceros no existe
                    let sum = 0;
                    for (let i = 0; i < 8; i++) {
                        sum += Number(digits[i]) * (9 - i);
                    }
                    const expected = (10 - (sum % 10)) % 10;
                    return Number(digits[8]) === expected;
                },

                validarNitSV(digits) {
                    if (!digits || digits.length !== 14) return false;
                    if (!/[1-9]/.test(digits)) return false;

                    let sum = 0;
                    let check = 0;
                    const correlativo = Number(digits.slice(10, 13));

                    if (correlativo <= 100) {
                        for (let i = 1; i <= 13; i++) {
                            sum += Number(digits[i - 1]) * (15 - i);
                        }
                        check = sum % 11;
                        if (check === 10) check = 0;
                    } else {
                        for (let i = 1; i <= 13; i++) {
                            const factor = 3 + 6 * Math.floor(Math.abs((i + 4) / 6)) - i;
                            sum += Number(digits[i - 1]) * factor;
                        }
                        check = sum % 11;
                        check = (check > 1) ? (11 - check) : 0;
                    }
                    return Number(digits[13]) === check;
                },

                validarDocumentoSV(valor) {
                    const raw = String(valor || '').trim();
                    const digits = raw.replace(/\D/g, '');

                    if (!digits) {
                        return {
                            estado: 'vacio',
                            valido: false,
                            mensaje: '',
                            tipo: null
                        };
                    }

                    if (digits.length < 9) {
                        return {
                            estado: 'incompleto',
                            valido: false,
                            mensaje: `DUI en progreso (${digits.length}/9 dígitos. Formato: 00000000-0)`,
                            tipo: 'dui'
                        };
                    }

                    if (digits.length === 9) {
                        const esValido = this.validarDuiSV(digits);
                        return {
                            estado: esValido ? 'valido' : 'invalido',
                            valido: esValido,
                            tipo: 'dui',
                            mensaje: esValido ?
                                '✓ DUI válido (Persona Natural / NIT homologado)' : '✗ Dígito verificador de DUI inválido según normativa de El Salvador'
                        };
                    }

                    if (digits.length < 14) {
                        return {
                            estado: 'incompleto',
                            valido: false,
                            mensaje: `NIT en progreso (${digits.length}/14 dígitos. Formato: 0000-000000-000-0)`,
                            tipo: 'nit'
                        };
                    }

                    if (digits.length === 14) {
                        const esValido = this.validarNitSV(digits);
                        return {
                            estado: esValido ? 'valido' : 'invalido',
                            valido: esValido,
                            tipo: 'nit',
                            mensaje: esValido ?
                                '✓ NIT válido de 14 dígitos (Persona Jurídica)' : '✗ Dígito verificador de NIT inválido según normativa de El Salvador'
                        };
                    }

                    return {
                        estado: 'invalido',
                        valido: false,
                        tipo: 'desconocido',
                        mensaje: '✗ Excede los 14 dígitos permitidos para documentos en El Salvador'
                    };
                },

                get estadoDocComercial() {
                    return this.validarDocumentoSV(this.clienteDuiNitComercial);
                },

                get estadoDocCCF() {
                    return this.validarDocumentoSV(this.clienteNitCCF);
                },

                onInputDuiNitComercial(e) {
                    const f = this.formatearDuiNit(e.target.value);
                    this.clienteDuiNitComercial = f;
                    e.target.value = f;
                },

                onInputNitCCF(e) {
                    const f = this.formatearDuiNit(e.target.value);
                    this.clienteNitCCF = f;
                    e.target.value = f;
                },

                onInputNrcCCF(e) {
                    const f = this.formatearNrc(e.target.value);
                    this.clienteNrcCCF = f;
                    e.target.value = f;
                },

                procederImpresion() {
                    if (!this.ventaImprimirId) return;

                    const params = new URLSearchParams();
                    params.set('tipo', this.tipoComprobante);

                    if (this.tipoComprobante === 'factura_comercial') {
                        const docComercial = this.clienteDuiNitComercial.trim();
                        if (docComercial) {
                            const analisis = this.validarDocumentoSV(docComercial);
                            if (!analisis.valido) {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'DUI o NIT no válido',
                                        html: `<div class="text-left text-xs text-slate-600 space-y-2">
                                            <p>El documento ingresado (<b>${docComercial}</b>) no cumple con el algoritmo legal de El Salvador:</p>
                                            <p class="p-2.5 bg-rose-50 border border-rose-200 text-rose-700 font-bold rounded-xl text-center">${analisis.mensaje}</p>
                                            <p class="text-[11px] text-slate-500">Para Consumidor Final este campo es opcional. Puedes corregirlo o dejarlo vacío para continuar.</p>
                                        </div>`,
                                        confirmButtonColor: '#2563eb',
                                        confirmButtonText: 'Revisar Documento'
                                    });
                                } else {
                                    alert('DUI o NIT no válido: ' + analisis.mensaje);
                                }
                                return;
                            }
                            params.set('cliente_documento', docComercial);
                        }

                        if (this.clienteNombreComercial.trim()) {
                            params.set('cliente_nombre', this.clienteNombreComercial.trim());
                        }
                        if (this.clienteDireccionComercial.trim()) {
                            params.set('cliente_direccion', this.clienteDireccionComercial.trim());
                        }
                    } else if (this.tipoComprobante === 'credito_fiscal') {
                        if (!this.clienteRazonSocialCCF.trim()) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Razón Social Requerida',
                                    text: 'Según las leyes tributarias de El Salvador (Arts. 107 y 108 Código Tributario), la Razón Social es obligatoria para emitir Comprobante de Crédito Fiscal.',
                                    confirmButtonColor: '#7c3aed'
                                });
                            } else {
                                alert('Según las leyes tributarias de El Salvador, la Razón Social es obligatoria para emitir Crédito Fiscal.');
                            }
                            return;
                        }
                        if (!this.clienteNrcCCF.trim()) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'NRC Obligatorio',
                                    text: 'El Número de Registro de Contribuyente (NRC) es obligatorio para emitir Crédito Fiscal en El Salvador.',
                                    confirmButtonColor: '#7c3aed'
                                });
                            } else {
                                alert('El Número de Registro de Contribuyente (NRC) es obligatorio para emitir Crédito Fiscal.');
                            }
                            return;
                        }
                        if (!this.clienteNitCCF.trim()) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'NIT o DUI Obligatorio',
                                    text: 'El NIT o DUI del contribuyente es obligatorio para Crédito Fiscal en El Salvador.',
                                    confirmButtonColor: '#7c3aed'
                                });
                            } else {
                                alert('El NIT o DUI del contribuyente es obligatorio para Crédito Fiscal.');
                            }
                            return;
                        }

                        const analisisCCF = this.validarDocumentoSV(this.clienteNitCCF);
                        if (!analisisCCF.valido) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'NIT/DUI no válido',
                                    html: `<div class="text-left text-xs text-slate-600 space-y-2">
                                        <p>El NIT o DUI ingresado para Crédito Fiscal (<b>${this.clienteNitCCF}</b>) no cumple con la normativa legal de El Salvador:</p>
                                        <p class="p-2.5 bg-rose-50 border border-rose-200 text-rose-700 font-bold rounded-xl text-center">${analisisCCF.mensaje}</p>
                                        <p class="text-[11px] text-slate-500">Debe ingresar un NIT de 14 dígitos o un DUI homologado de 9 dígitos con dígito verificador válido.</p>
                                    </div>`,
                                    confirmButtonColor: '#7c3aed',
                                    confirmButtonText: 'Corregir'
                                });
                            } else {
                                alert('El NIT o DUI del contribuyente no es válido: ' + analisisCCF.mensaje);
                            }
                            return;
                        }

                        if (!this.clienteGiroCCF.trim()) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Giro Comercial Requerido',
                                    text: 'El Giro o Actividad Económica es obligatorio por ley en El Salvador para Crédito Fiscal.',
                                    confirmButtonColor: '#7c3aed'
                                });
                            } else {
                                alert('El Giro o Actividad Económica es obligatorio por ley en El Salvador para Crédito Fiscal.');
                            }
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
                    return f.toLocaleDateString('es-SV', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    }) + ' ' + f.toLocaleTimeString('es-SV', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
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