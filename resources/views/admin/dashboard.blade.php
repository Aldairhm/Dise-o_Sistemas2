<x-app title="Dashboard Administrativo | AXStore">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <div class="max-w-[1440px] mx-auto space-y-6" x-data="adminDashboard()">

        <!-- 1. ENCABEZADO PRINCIPAL -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <i class="fas fa-chart-pie text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-black uppercase tracking-[0.18em] text-blue-600">Panel Administrativo</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-700">Corporativo</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Dashboard Administrativo & Reportes</h1>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN RÁPIDA -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('admin.reportes.movimientos') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-sm transition-all hover:border-blue-300">
                    <i class="fas fa-file-invoice-dollar text-emerald-600"></i>
                    <span>Ver Reporte de Movimientos</span>
                </a>

                <a href="{{ route('ventas.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-plus"></i>
                    <span>Nueva Venta</span>
                </a>
            </div>
        </div>

        <!-- 2. SUB-NAV / PESTAÑAS ADMINISTRATIVAS -->
        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones de administración">
            <a href="{{ route('admin.dashboard') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard General</span>
            </a>
            <a href="{{ route('admin.reportes.movimientos') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-slate-400"></i>
                <span>Informe de Movimientos</span>
            </a>
            <a href="{{ route('comisiones.porVendedor') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-hand-holding-dollar text-slate-400"></i>
                <span>Comisiones de Vendedores</span>
            </a>
            <a href="{{ route('compras.movimientos') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-warehouse text-slate-400"></i>
                <span>Kardex de Bodega</span>
            </a>
            <a href="{{ route('ventas.index') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-chart-line text-slate-400"></i>
                <span>Historial de Ventas</span>
            </a>
        </nav>

        <!-- 3. BARRA DE FILTROS GLOBALES -->
        <section class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
            <form @submit.prevent="aplicarFiltros()" class="space-y-4" id="dashboard-filter-form">
                <!-- PRESETS DE FECHA RÁPIDA -->
                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <i class="fas fa-filter text-slate-400 mr-1"></i> Periodo:
                        </span>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" @click="cambiarPreset('hoy')"
                                :class="filtros.preset === 'hoy' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                Hoy
                            </button>
                            <button type="button" @click="cambiarPreset('ultimos_7_dias')"
                                :class="filtros.preset === 'ultimos_7_dias' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                Últimos 7 días
                            </button>
                            <button type="button" @click="cambiarPreset('este_mes')"
                                :class="filtros.preset === 'este_mes' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                Este mes
                            </button>
                            <button type="button" @click="cambiarPreset('mes_anterior')"
                                :class="filtros.preset === 'mes_anterior' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                Mes anterior
                            </button>
                            <button type="button" @click="cambiarPreset('todo')"
                                :class="filtros.preset === 'todo' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                Todo el historial
                            </button>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500 font-medium">
                        Rango activo: <span class="font-bold text-slate-800" x-text="formatearFechaVisual(filtros.desde)">{{ \Carbon\Carbon::parse($filtros['desde'])->format('d/m/Y') }}</span> al <span class="font-bold text-slate-800" x-text="formatearFechaVisual(filtros.hasta)">{{ \Carbon\Carbon::parse($filtros['hasta'])->format('d/m/Y') }}</span>
                    </div>
                </div>

                <!-- CONTROLES ESPECÍFICOS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
                    <!-- FECHA DESDE -->
                    <div class="lg:col-span-3">
                        <label for="desde" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            <i class="fas fa-calendar-day text-slate-400 mr-1"></i> Fecha Desde
                        </label>
                        <input
                            type="date"
                            name="desde"
                            id="desde"
                            x-model="filtros.desde"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <!-- FECHA HASTA -->
                    <div class="lg:col-span-3">
                        <label for="hasta" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            <i class="fas fa-calendar-check text-slate-400 mr-1"></i> Fecha Hasta
                        </label>
                        <input
                            type="date"
                            name="hasta"
                            id="hasta"
                            x-model="filtros.hasta"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <!-- VENDEDOR -->
                    <div class="lg:col-span-3">
                        <label for="vendedor_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            <i class="fas fa-user-tie text-slate-400 mr-1"></i> Vendedor
                        </label>
                        <select
                            name="vendedor_id"
                            id="vendedor_id"
                            x-model="filtros.vendedor_id"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                            <option value="">Todos los vendedores</option>
                            @foreach ($vendedores as $v)
                                <option value="{{ $v->id }}">
                                    {{ $v->nombre_real }} ({{ ucfirst($v->rol) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- BOTONES APLICAR Y LIMPIAR -->
                    <div class="lg:col-span-3 flex items-center gap-2">
                        <button
                            type="submit"
                            class="flex-1 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-3 text-xs transition-all shadow-md shadow-blue-600/15 flex items-center justify-center gap-1.5 cursor-pointer">
                            <i :class="cargando ? 'fas fa-spinner fa-spin' : 'fas fa-filter'"></i>
                            <span x-text="cargando ? 'Filtrando...' : 'Filtrar'"></span>
                        </button>
                        <button
                            type="button"
                            @click="restablecerFiltros()"
                            title="Restablecer filtros"
                            class="inline-flex items-center justify-center px-3 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-800 text-xs font-bold transition-all cursor-pointer">
                            <i class="fas fa-rotate-left"></i>
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <!-- 4. BLOQUE ESTRATÉGICO: 4 KPIS ADMINISTRATIVOS (CON SKELETON SHIMMER) -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- KPI 1: INGRESOS TOTALES -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md hover:border-blue-300 group">
                <div class="absolute top-0 left-0 h-1 w-full bg-blue-600"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-wider text-slate-500">Ingresos por Ventas</p>
                        <template x-if="cargando">
                            <div class="mt-2 space-y-1">
                                <div class="h-8 w-32 rounded-md ax-skeleton-shimmer"></div>
                            </div>
                        </template>
                        <div x-show="!cargando">
                            <h3 class="mt-2 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight tabular-nums">
                                $<span x-text="formatearMoneda(kpis.ingresos.total)">{{ number_format($kpis['ingresos']['total'], 2) }}</span>
                            </h3>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <i class="fas fa-dollar-sign text-base"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <template x-if="cargando">
                        <div class="w-full flex justify-between">
                            <div class="h-4 w-20 rounded ax-skeleton-shimmer"></div>
                            <div class="h-4 w-20 rounded ax-skeleton-shimmer"></div>
                        </div>
                    </template>
                    <div x-show="!cargando" class="w-full flex items-center justify-between">
                        <span class="flex items-center gap-1.5 font-medium">
                            <i class="fas fa-receipt text-slate-400"></i>
                            <b class="text-slate-700" x-text="kpis.ingresos.ventas_count">{{ $kpis['ingresos']['ventas_count'] }}</b> ventas
                        </span>
                        <span class="tabular-nums font-semibold text-slate-700" title="Ticket Promedio">
                            Prom: $<span x-text="formatearMoneda(kpis.ingresos.ticket_promedio)">{{ number_format($kpis['ingresos']['ticket_promedio'], 2) }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- KPI 2: COSTOS DE INVENTARIO -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md hover:border-rose-300 group">
                <div class="absolute top-0 left-0 h-1 w-full bg-rose-500"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-wider text-slate-500">Costos de Inventario</p>
                        <template x-if="cargando">
                            <div class="mt-2 space-y-1">
                                <div class="h-8 w-32 rounded-md ax-skeleton-shimmer"></div>
                            </div>
                        </template>
                        <div x-show="!cargando">
                            <h3 class="mt-2 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight tabular-nums">
                                $<span x-text="formatearMoneda(kpis.costos.cogs_periodo)">{{ number_format($kpis['costos']['cogs_periodo'], 2) }}</span>
                            </h3>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <i class="fas fa-boxes-stacked text-base"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <template x-if="cargando">
                        <div class="w-full flex justify-between">
                            <div class="h-4 w-20 rounded ax-skeleton-shimmer"></div>
                            <div class="h-4 w-20 rounded ax-skeleton-shimmer"></div>
                        </div>
                    </template>
                    <div x-show="!cargando" class="w-full flex items-center justify-between">
                        <span class="flex items-center gap-1.5 font-medium">
                            <i class="fas fa-cubes text-slate-400"></i>
                            <b class="text-slate-700" x-text="kpis.costos.unidades_vendidas">{{ $kpis['costos']['unidades_vendidas'] }}</b> uds. vendidas
                        </span>
                        <span class="tabular-nums font-semibold text-slate-700" title="Valoración Total de Stock en Almacén y Tienda">
                            Stock: $<span x-text="formatearMoneda(kpis.costos.valoracion_inventario)">{{ number_format($kpis['costos']['valoracion_inventario'], 2) }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- KPI 3: GANANCIAS NETAS -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md hover:border-emerald-300 group">
                <div class="absolute top-0 left-0 h-1 w-full bg-emerald-500"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-wider text-slate-500">Ganancias Netas</p>
                        <template x-if="cargando">
                            <div class="mt-2 space-y-1">
                                <div class="h-8 w-32 rounded-md ax-skeleton-shimmer"></div>
                            </div>
                        </template>
                        <div x-show="!cargando">
                            <h3 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight tabular-nums"
                                :class="kpis.ganancias.neta >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                                $<span x-text="formatearMoneda(kpis.ganancias.neta)">{{ number_format($kpis['ganancias']['neta'], 2) }}</span>
                            </h3>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <i class="fas fa-arrow-trend-up text-base"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <template x-if="cargando">
                        <div class="w-full flex justify-between">
                            <div class="h-4 w-20 rounded ax-skeleton-shimmer"></div>
                            <div class="h-4 w-20 rounded ax-skeleton-shimmer"></div>
                        </div>
                    </template>
                    <div x-show="!cargando" class="w-full flex items-center justify-between">
                        <span class="inline-flex items-center gap-1 font-bold"
                            :class="kpis.ganancias.margen_porcentaje >= 0 ? 'text-emerald-700' : 'text-rose-700'">
                            <i class="fas fa-percent text-[10px]"></i>
                            Margen: <span x-text="kpis.ganancias.margen_porcentaje">{{ $kpis['ganancias']['margen_porcentaje'] }}</span>%
                        </span>
                        <span class="text-slate-500 text-[11px]" title="Comisiones de ventas ya deducidas">
                            Comis: -$<span x-text="formatearMoneda(kpis.ganancias.comisiones_periodo)">{{ number_format($kpis['ganancias']['comisiones_periodo'], 2) }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- KPI 4: COMISIONES POR PAGAR -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md hover:border-indigo-300 group">
                <div class="absolute top-0 left-0 h-1 w-full bg-indigo-600"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-wider text-slate-500">Comisiones por Pagar</p>
                        <template x-if="cargando">
                            <div class="mt-2 space-y-1">
                                <div class="h-8 w-32 rounded-md ax-skeleton-shimmer"></div>
                            </div>
                        </template>
                        <div x-show="!cargando">
                            <h3 class="mt-2 text-2xl sm:text-3xl font-black text-indigo-700 tracking-tight tabular-nums">
                                $<span x-text="formatearMoneda(kpis.comisiones_por_pagar.total_pendiente)">{{ number_format($kpis['comisiones_por_pagar']['total_pendiente'], 2) }}</span>
                            </h3>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <i class="fas fa-hand-holding-dollar text-base"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <template x-if="cargando">
                        <div class="w-full flex justify-between">
                            <div class="h-4 w-20 rounded ax-skeleton-shimmer"></div>
                            <div class="h-4 w-20 rounded ax-skeleton-shimmer"></div>
                        </div>
                    </template>
                    <div x-show="!cargando" class="w-full flex items-center justify-between">
                        <span class="flex items-center gap-1.5 font-medium">
                            <i class="fas fa-hourglass-half text-amber-500"></i>
                            <b class="text-slate-700" x-text="kpis.comisiones_por_pagar.conteo_pendientes">{{ $kpis['comisiones_por_pagar']['conteo_pendientes'] }}</b> pendientes
                        </span>
                        <a href="{{ route('comisiones.porVendedor') }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition-colors inline-flex items-center gap-1">
                            Liquidar <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. SECCIÓN DE GRÁFICOS VISUALES -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- GRÁFICO 1: COMPARATIVA FINANCIERA (INGRESOS VS COSTOS VS GANANCIAS) -->
            <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Comparativa Financiera</h2>
                        <p class="text-xs text-slate-500">Ingresos Brutos vs. Costo de Ventas (COGS) vs. Ganancias Netas</p>
                    </div>
                    <!-- Badges de leyenda -->
                    <div class="flex items-center gap-3 text-xs font-bold">
                        <span class="inline-flex items-center gap-1.5 text-blue-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Ingresos
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-rose-500">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Costos
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-emerald-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Ganancias
                        </span>
                    </div>
                </div>

                <div class="relative h-[290px] sm:h-[320px] w-full">
                    <!-- Skeleton shimmer durante recargas/filtros de la gráfica de líneas -->
                    <div x-show="cargando" x-cloak class="absolute inset-0 z-10 bg-white">
                        <x-skeleton-chart type="line" height="h-full" />
                    </div>
                    <canvas id="chartComparativaFinanciera" :class="{'opacity-0': cargando}"></canvas>
                </div>
            </div>

            <!-- GRÁFICO 2: MOVIMIENTOS DE INVENTARIO Y COMISIONES -->
            <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Flujo de Stock & Comisiones</h2>
                        <p class="text-xs text-slate-500">Entradas a almacén vs. Salidas despachadas</p>
                    </div>
                    <div class="flex items-center gap-3 text-xs font-bold">
                        <span class="inline-flex items-center gap-1 text-emerald-600">
                            <span class="w-2.5 h-2.5 rounded bg-emerald-500"></span> Entradas
                        </span>
                        <span class="inline-flex items-center gap-1 text-indigo-600">
                            <span class="w-2.5 h-2.5 rounded bg-indigo-600"></span> Salidas
                        </span>
                    </div>
                </div>

                <div class="relative h-[290px] sm:h-[320px] w-full">
                    <!-- Skeleton shimmer durante recargas/filtros de la gráfica de barras -->
                    <div x-show="cargando" x-cloak class="absolute inset-0 z-10 bg-white">
                        <x-skeleton-chart type="bar" height="h-full" />
                    </div>
                    <canvas id="chartMovimientosComisiones" :class="{'opacity-0': cargando}"></canvas>
                </div>
            </div>
        </section>

        <!-- 6. RESUMEN DE VALORACIÓN DE INVENTARIO & ACCESO AL INFORME FORMAL -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- TARJETA DESGLOSE DE STOCK -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-warehouse text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Valoración de Existencias</h3>
                            <p class="text-[11px] text-slate-400">Stock físico total disponible</p>
                        </div>
                    </div>
                    <span class="text-xs font-black px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700">
                        {{ $kpis['costos']['stock_unidades_total'] }} uds.
                    </span>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div>
                            <p class="text-xs font-bold text-slate-700">Tienda Física (Punto de Venta)</p>
                            <p class="text-[10px] text-slate-400">Stock listo para despacho inmediato</p>
                        </div>
                        <p class="text-sm font-black text-slate-900 tabular-nums">
                            ${{ number_format($kpis['costos']['valoracion_tienda'], 2) }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div>
                            <p class="text-xs font-bold text-slate-700">Bodega Central (Reserva)</p>
                            <p class="text-[10px] text-slate-400">Stock almacenado para reposición</p>
                        </div>
                        <p class="text-sm font-black text-slate-900 tabular-nums">
                            ${{ number_format($kpis['costos']['valoracion_bodega'], 2) }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50/60 border border-blue-100 text-blue-900">
                        <div>
                            <p class="text-xs font-extrabold">Valor Total en Libros</p>
                            <p class="text-[10px] text-blue-600">Costo promedio ponderado</p>
                        </div>
                        <p class="text-base font-black text-blue-700 tabular-nums">
                            ${{ number_format($kpis['costos']['valoracion_inventario'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- TARJETA BANNER INFORME EJECUTIVO (ESTILO CORPORATIVO AXSTORE) -->
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-bold">
                        <i class="fas fa-file-invoice text-blue-600"></i>
                        <span>Módulo de Auditoría y Control</span>
                    </div>

                    <h3 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900">
                        Informes Detallados y Reportes de Movimientos
                    </h3>

                    <p class="text-xs sm:text-sm text-slate-600 max-w-xl leading-relaxed">
                        Accede a la trazabilidad exhaustiva de cada entrada por compra, salida por venta, transferencia interna o ajuste de comisión con soporte para exportación limpia e impresión formal con membrete institucional.
                    </p>
                </div>

                <div class="pt-5 mt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4 text-xs text-slate-500 font-medium">
                        <span><i class="fas fa-check-circle text-emerald-600 mr-1.5"></i>Sin cuadrículas saturadas</span>
                        <span><i class="fas fa-print text-blue-600 mr-1.5"></i>Optimizado para @media print</span>
                    </div>

                    <a href="{{ route('admin.reportes.movimientos') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-lg shadow-blue-600/25 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                        <span>Abrir Informe Ejecutivo</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </section>

    </div>

    <!-- SCRIPT DE INICIALIZACIÓN DE GRÁFICOS CHART.JS Y COMPONENTE REACTIVO -->
    <script>
        function adminDashboard() {
            return {
                cargando: false,
                filtros: {
                    preset: '{{ $filtros['preset'] ?? 'este_mes' }}',
                    desde: '{{ $filtros['desde'] }}',
                    hasta: '{{ $filtros['hasta'] }}',
                    vendedor_id: '{{ $filtros['vendedor_id'] ?? '' }}',
                },
                kpis: @js($kpis),

                formatearMoneda(valor) {
                    if (valor === null || valor === undefined) return '0.00';
                    return Number(valor).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                formatearFechaVisual(fechaIso) {
                    if (!fechaIso) return '';
                    const partes = fechaIso.split('-');
                    if (partes.length === 3) {
                        return `${partes[2]}/${partes[1]}/${partes[0]}`;
                    }
                    return fechaIso;
                },

                cambiarPreset(nuevoPreset) {
                    this.filtros.preset = nuevoPreset;
                    this.filtros.desde = '';
                    this.filtros.hasta = '';
                    this.aplicarFiltros();
                },

                restablecerFiltros() {
                    this.filtros.preset = 'este_mes';
                    this.filtros.desde = '';
                    this.filtros.hasta = '';
                    this.filtros.vendedor_id = '';
                    this.aplicarFiltros();
                },

                aplicarFiltros() {
                    if (this.cargando) return;
                    this.cargando = true;

                    const params = new URLSearchParams();
                    if (this.filtros.preset) params.append('preset', this.filtros.preset);
                    if (this.filtros.desde) params.append('desde', this.filtros.desde);
                    if (this.filtros.hasta) params.append('hasta', this.filtros.hasta);
                    if (this.filtros.vendedor_id) params.append('vendedor_id', this.filtros.vendedor_id);

                    const urlDestino = `{{ route('admin.dashboard') }}?${params.toString()}`;

                    fetch(urlDestino, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Error al consultar métricas del dashboard');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // 1. Actualizar KPIs reactivamente
                            if (data.kpis) {
                                this.kpis = data.kpis;
                            }

                            // 2. Si el backend calculó nuevas fechas de inicio y fin para el preset
                            if (data.rango) {
                                this.filtros.desde = data.rango.desde;
                                this.filtros.hasta = data.rango.hasta;
                            }

                            // 3. Actualizar Gráficos Chart.js
                            if (data.graficoComparativa || data.graficoMovimientos) {
                                actualizarGraficosDashboard(data.graficoComparativa, data.graficoMovimientos);
                            }

                            // 4. Actualizar barra de direcciones
                            window.history.pushState({}, '', urlDestino);
                        }
                    })
                    .catch(err => {
                        console.error('Error al actualizar el dashboard:', err);
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
                }
            };
        }

        // Función para actualizar datasets sin destruir los canvas de Chart.js
        function actualizarGraficosDashboard(compData, movData) {
            if (window.chartDashboardCompInstance && compData) {
                window.chartDashboardCompInstance.data.labels = compData.labels || [];
                if (window.chartDashboardCompInstance.data.datasets[0]) {
                    window.chartDashboardCompInstance.data.datasets[0].data = compData.ingresos || [];
                }
                if (window.chartDashboardCompInstance.data.datasets[1]) {
                    window.chartDashboardCompInstance.data.datasets[1].data = compData.costos || [];
                }
                if (window.chartDashboardCompInstance.data.datasets[2]) {
                    window.chartDashboardCompInstance.data.datasets[2].data = compData.ganancias || [];
                }
                window.chartDashboardCompInstance.update();
            }

            if (window.chartDashboardMovInstance && movData) {
                window.chartDashboardMovInstance.data.labels = movData.labels || [];
                if (window.chartDashboardMovInstance.data.datasets[0]) {
                    window.chartDashboardMovInstance.data.datasets[0].data = movData.entradas || [];
                }
                if (window.chartDashboardMovInstance.data.datasets[1]) {
                    window.chartDashboardMovInstance.data.datasets[1].data = movData.salidas || [];
                }
                window.chartDashboardMovInstance.update();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Datos iniciales inyectados desde el backend
            const compData = @js($graficoComparativa);
            const movData = @js($graficoMovimientos);

            // 1. Gráfico Comparativo Financiero
            const ctxComp = document.getElementById('chartComparativaFinanciera');
            if (ctxComp) {
                if (window.chartDashboardCompInstance) {
                    window.chartDashboardCompInstance.destroy();
                }
                window.chartDashboardCompInstance = new Chart(ctxComp, {
                    type: 'line',
                    data: {
                        labels: compData.labels,
                        datasets: [
                            {
                                label: 'Ingresos ($)',
                                data: compData.ingresos,
                                borderColor: '#2563eb', // Blue 600
                                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                                fill: true,
                                tension: 0.3,
                                borderWidth: 2.5,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                            },
                            {
                                label: 'Costos COGS ($)',
                                data: compData.costos,
                                borderColor: '#f43f5e', // Rose 500
                                backgroundColor: 'transparent',
                                borderDash: [4, 4],
                                tension: 0.3,
                                borderWidth: 2,
                                pointRadius: 2,
                                pointHoverRadius: 4,
                            },
                            {
                                label: 'Ganancia Neta ($)',
                                data: compData.ganancias,
                                borderColor: '#10b981', // Emerald 500
                                backgroundColor: 'rgba(16, 185, 129, 0.08)',
                                fill: true,
                                tension: 0.3,
                                borderWidth: 2.5,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                                bodyFont: { family: 'Inter', size: 12 },
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        if (context.parsed.y !== null) {
                                            label += '$' + Number(context.parsed.y).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Inter', size: 10 }, color: '#64748b', maxTicksLimit: 12 }
                            },
                            y: {
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    font: { family: 'Inter', size: 10 },
                                    color: '#64748b',
                                    callback: function(value) { return '$' + value; }
                                }
                            }
                        }
                    }
                });
            }

            // 2. Gráfico de Movimientos y Comisiones
            const ctxMov = document.getElementById('chartMovimientosComisiones');
            if (ctxMov) {
                if (window.chartDashboardMovInstance) {
                    window.chartDashboardMovInstance.destroy();
                }
                window.chartDashboardMovInstance = new Chart(ctxMov, {
                    type: 'bar',
                    data: {
                        labels: movData.labels,
                        datasets: [
                            {
                                label: 'Entradas (uds)',
                                data: movData.entradas,
                                backgroundColor: '#10b981', // Emerald 500
                                borderRadius: 4,
                                barPercentage: 0.6,
                            },
                            {
                                label: 'Salidas (uds)',
                                data: movData.salidas,
                                backgroundColor: '#6366f1', // Indigo 500
                                borderRadius: 4,
                                barPercentage: 0.6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                                bodyFont: { family: 'Inter', size: 12 },
                                padding: 10,
                                cornerRadius: 8,
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Inter', size: 10 }, color: '#64748b', maxTicksLimit: 10 }
                            },
                            y: {
                                grid: { color: '#f1f5f9' },
                                ticks: { font: { family: 'Inter', size: 10 }, color: '#64748b', precision: 0 }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app>
