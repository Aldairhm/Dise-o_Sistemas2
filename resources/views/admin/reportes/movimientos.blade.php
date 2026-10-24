<x-app title="Informe Oficial de Movimientos y Flujo Financiero | AXStore">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <div class="max-w-[1440px] mx-auto space-y-6" id="reporte-container">

        <!-- 1. BARRA DE ACCIÓN Y NAVEGACIÓN SUPERIOR (SOLO PANTALLA) -->
        <div class="no-print flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard General
                </a>
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center shadow-lg shadow-blue-600/25">
                        <i class="fas fa-file-invoice-dollar text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-black uppercase tracking-[0.18em] text-blue-600">Módulo de Auditoría</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800">Oficial AXStore</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Informe de Movimientos & Flujo</h1>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN PARA EXPORTAR / IMPRIMIR -->
            <div class="flex flex-wrap items-center gap-2.5">
                @if (request()->query('modo') === 'imprimir')
                    <a href="{{ route('admin.reportes.movimientos', array_merge(request()->except('modo', 'page'), ['modo' => 'normal'])) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-sm transition-all">
                        <i class="fas fa-table-list text-slate-500"></i>
                        <span>Vista Paginada</span>
                    </a>
                @else
                    <a href="{{ route('admin.reportes.movimientos', array_merge(request()->except('page'), ['modo' => 'imprimir'])) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-sm transition-all"
                        title="Carga todos los registros continuos para impresión">
                        <i class="fas fa-list-check text-blue-600"></i>
                        <span>Cargar Todo</span>
                    </a>
                @endif

                <!-- BOTÓN EXPORTAR EXCEL XLSX ESTILIZADO CON GRÁFICOS -->
                <a href="{{ route('admin.reportes.movimientos.csv', request()->except('page')) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                    title="Descargar libro de Microsoft Excel (.xlsx) con estilos corporativos, tarjetas de KPIs y gráficos nativos">
                    <i class="fas fa-file-excel text-base"></i>
                    <span>Exportar Excel (.xlsx)</span>
                </a>

                <button
                    type="button"
                    onclick="prepararEImprimir()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-lg shadow-blue-600/30 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <i class="fas fa-print"></i>
                    <span>Imprimir / PDF</span>
                </button>
            </div>
        </div>

        <!-- 2. SUB-NAV ADMINISTRATIVO (SOLO PANTALLA) -->
        <nav class="no-print flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones de administración">
            <a href="{{ route('admin.dashboard') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-chart-pie text-slate-400"></i>
                <span>Dashboard General</span>
            </a>
            <a href="{{ route('admin.reportes.movimientos') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Informe de Movimientos</span>
            </a>
            <a href="{{ route('comisiones.porVendedor') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-hand-holding-dollar text-slate-400"></i>
                <span>Comisiones</span>
            </a>
            <a href="{{ route('compras.movimientos') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-warehouse text-slate-400"></i>
                <span>Kardex Físico</span>
            </a>
        </nav>

        <!-- 3. BARRA DE FILTROS INTERACTIVA (SOLO PANTALLA) -->
        <section class="no-print bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
            <form method="GET" action="{{ route('admin.reportes.movimientos') }}" class="space-y-4">
                <input type="hidden" name="modo" value="{{ $filtros['modo'] ?? 'normal' }}">

                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <i class="fas fa-clock text-slate-400 mr-1"></i> Periodo:
                        </span>
                        <div class="flex flex-wrap gap-1.5">
                            @php $pActual = $filtros['preset'] ?? 'este_mes'; @endphp
                            <a href="{{ route('admin.reportes.movimientos', array_merge(request()->except('page'), ['preset' => 'hoy', 'desde' => '', 'hasta' => ''])) }}"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all {{ $pActual === 'hoy' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                                Hoy
                            </a>
                            <a href="{{ route('admin.reportes.movimientos', array_merge(request()->except('page'), ['preset' => 'ultimos_7_dias', 'desde' => '', 'hasta' => ''])) }}"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all {{ $pActual === 'ultimos_7_dias' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                                7 Días
                            </a>
                            <a href="{{ route('admin.reportes.movimientos', array_merge(request()->except('page'), ['preset' => 'este_mes', 'desde' => '', 'hasta' => ''])) }}"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all {{ $pActual === 'este_mes' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                                Este mes
                            </a>
                            <a href="{{ route('admin.reportes.movimientos', array_merge(request()->except('page'), ['preset' => 'mes_anterior', 'desde' => '', 'hasta' => ''])) }}"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all {{ $pActual === 'mes_anterior' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                                Mes anterior
                            </a>
                            <a href="{{ route('admin.reportes.movimientos', array_merge(request()->except('page'), ['preset' => 'todo', 'desde' => '', 'hasta' => ''])) }}"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all {{ $pActual === 'todo' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                                Todo el historial
                            </a>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500 font-medium">
                        Periodo activo: <b class="text-slate-800">{{ \Carbon\Carbon::parse($filtros['desde'])->format('d/m/Y') }}</b> al <b class="text-slate-800">{{ \Carbon\Carbon::parse($filtros['hasta'])->format('d/m/Y') }}</b>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
                    <div class="lg:col-span-3">
                        <label for="tipo" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            <i class="fas fa-tags text-slate-400 mr-1"></i> Tipo de Movimiento
                        </label>
                        <select
                            name="tipo"
                            id="tipo"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                            <option value="todos" {{ ($filtros['tipo'] ?? '') === 'todos' ? 'selected' : '' }}>Todos los movimientos</option>
                            <option value="ventas" {{ ($filtros['tipo'] ?? '') === 'ventas' ? 'selected' : '' }}>Solo Ventas / Salidas</option>
                            <option value="compras" {{ ($filtros['tipo'] ?? '') === 'compras' ? 'selected' : '' }}>Solo Compras / Entradas</option>
                            <option value="transferencias" {{ ($filtros['tipo'] ?? '') === 'transferencias' ? 'selected' : '' }}>Transferencias a Tienda</option>
                            <option value="devoluciones" {{ ($filtros['tipo'] ?? '') === 'devoluciones' ? 'selected' : '' }}>Devoluciones</option>
                            <option value="comisiones" {{ ($filtros['tipo'] ?? '') === 'comisiones' ? 'selected' : '' }}>Comisiones de Vendedores</option>
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label for="desde" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">Desde</label>
                        <input
                            type="date"
                            name="desde"
                            id="desde"
                            value="{{ $filtros['desde'] }}"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <div class="lg:col-span-2">
                        <label for="hasta" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">Hasta</label>
                        <input
                            type="date"
                            name="hasta"
                            id="hasta"
                            value="{{ $filtros['hasta'] }}"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <div class="lg:col-span-3">
                        <label for="q" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">Buscar Producto / Folio / Cliente</label>
                        <input
                            type="text"
                            name="q"
                            id="q"
                            value="{{ $filtros['q'] ?? '' }}"
                            placeholder="Ej: Cámara, VNT-00036..."
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <div class="lg:col-span-2 flex items-center gap-2">
                        <button
                            type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all active:scale-95 cursor-pointer">
                            <i class="fas fa-magnifying-glass"></i>
                            <span>Filtrar</span>
                        </button>
                        <a
                            href="{{ route('admin.reportes.movimientos') }}"
                            title="Restablecer"
                            class="inline-flex items-center justify-center px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold transition-all">
                            <i class="fas fa-rotate-right"></i>
                        </a>
                    </div>
                </div>
            </form>
        </section>

        <!-- ════════════════════════════════════════════════════════════════════════════ -->
        <!-- 4. DOCUMENTO FORMAL DE INFORME EJECUTIVO (DISEÑO CORPORATIVO AXSTORE)        -->
        <!-- ════════════════════════════════════════════════════════════════════════════ -->
        <article class="report-paper bg-white border border-slate-200 rounded-3xl shadow-sm p-6 sm:p-10 print:p-0 print:border-none print:shadow-none print:m-0 space-y-6 text-slate-800">

            <!-- BARRA SUPERIOR DECORATIVA DE MARCA (AZUL CORPORATIVO) -->
            <div class="h-2 w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-500 rounded-t-full print:h-2 print:rounded-none"></div>

            <!-- A. ENCABEZADO CORPORATIVO CON LOGO Y MEMBRETE -->
            <header class="report-header flex flex-col sm:flex-row items-start justify-between gap-6 pb-6 border-b-2 border-slate-200">
                <!-- Lado Izquierdo: Logotipo AXStore y datos fiscales/comerciales -->
                <div class="flex items-center gap-4">
                    <div class="logo-box p-2.5 bg-slate-50 border border-slate-200 rounded-2xl shadow-sm flex items-center justify-center shrink-0 print:bg-white print:border-slate-300">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="AXStore Logo" class="h-14 sm:h-16 w-auto object-contain">
                    </div>
                    <div>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black tracking-tight text-slate-900 leading-none">AX<span class="text-blue-600">STORE</span></span>
                            <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">Oficial</span>
                        </div>
                        <p class="text-xs font-bold text-slate-700 tracking-wide mt-1">AXSTORE COMERCIO & LOGÍSTICA DIGITAL</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">San Salvador, El Salvador · Tu tienda online</p>
                        <p class="text-[10px] text-slate-400 font-mono">PBX: (503) 2200-0000 · soporte@axstore.com</p>
                    </div>
                </div>

                <!-- Lado Derecho: Tarjeta ejecutiva del informe con folio y metadatos -->
                <div class="w-full sm:w-auto bg-slate-50 rounded-2xl p-4 border border-slate-200/90 text-left sm:text-right shrink-0 print:bg-slate-50 print:border-slate-300 space-y-1.5 min-w-[270px]">
                    <div class="flex items-center justify-between sm:justify-end gap-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Tipo de Documento:</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-600 text-white uppercase tracking-wider">Auditoría Interna</span>
                    </div>

                    <h2 class="text-sm sm:text-base font-black text-slate-900 uppercase tracking-tight">
                        Informe de Movimientos & Flujo
                    </h2>

                    <div class="pt-2 border-t border-slate-200 text-[11px] text-slate-600 space-y-1">
                        <div class="flex justify-between gap-3">
                            <span class="text-slate-400">Folio:</span>
                            <span class="font-mono font-black text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200 text-[11px]">{{ $folioReporte }}</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="text-slate-400">Emisión:</span>
                            <span class="font-bold text-slate-800">{{ $fechaGeneracion }}</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="text-slate-400">Auditor:</span>
                            <span class="font-bold text-slate-800">{{ $usuarioEmisor }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- B. RESUMEN DE PARÁMETROS Y ALCANCE DE CONSULTA -->
            <section class="bg-gradient-to-r from-slate-50 to-slate-100/60 rounded-2xl p-4 border border-slate-200 text-xs text-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-4 print:bg-slate-50 print:border-slate-300">
                <div class="border-l-2 border-blue-600 pl-3">
                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Período Auditado</span>
                    <span class="font-black text-slate-900 text-[12px]">{{ \Carbon\Carbon::parse($filtros['desde'])->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($filtros['hasta'])->format('d/m/Y') }}</span>
                </div>
                <div class="border-l-2 border-emerald-500 pl-3">
                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Tipo de Movimiento</span>
                    <span class="font-bold text-slate-800 capitalize">{{ str_replace('_', ' ', $filtros['tipo'] ?? 'Todos') }}</span>
                </div>
                <div class="border-l-2 border-indigo-500 pl-3">
                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Vendedor / Usuario</span>
                    <span class="font-bold text-slate-800 truncate block">
                        @if (!empty($filtros['vendedor_id']))
                            {{ $vendedores->firstWhere('id', $filtros['vendedor_id'])?->nombre_real ?? 'Asignado' }}
                        @else
                            Todos los vendedores
                        @endif
                    </span>
                </div>
                <div class="border-l-2 border-amber-500 pl-3">
                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Registros Procesados</span>
                    <span class="font-black text-slate-900 text-[12px]">{{ $totales['cantidad_movimientos'] }} operaciones</span>
                </div>
            </section>

            <!-- C. BLOQUE EJECUTIVO DE LOS 4 KPIS FINANCIEROS -->
            <section class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 print:gap-2.5">
                <!-- KPI 1: Ingresos -->
                <div class="relative overflow-hidden rounded-2xl border-2 border-blue-100 bg-gradient-to-b from-blue-50/40 to-white p-4 print:border-blue-300 print:bg-white shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-blue-700">1. Ingresos Totales</span>
                        <div class="w-6 h-6 rounded-md bg-blue-100 text-blue-600 flex items-center justify-center text-[10px]">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xl sm:text-2xl font-black text-slate-900 tabular-nums">
                        ${{ number_format($kpis['ingresos']['total'], 2) }}
                    </p>
                    <p class="mt-1 text-[11px] text-slate-500 font-medium">
                        <b class="text-slate-800">{{ $kpis['ingresos']['ventas_count'] }}</b> ventas concretadas
                    </p>
                </div>

                <!-- KPI 2: Costos COGS -->
                <div class="relative overflow-hidden rounded-2xl border-2 border-rose-100 bg-gradient-to-b from-rose-50/40 to-white p-4 print:border-rose-300 print:bg-white shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-rose-700">2. Costo de Ventas</span>
                        <div class="w-6 h-6 rounded-md bg-rose-100 text-rose-600 flex items-center justify-center text-[10px]">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xl sm:text-2xl font-black text-rose-600 tabular-nums">
                        ${{ number_format($kpis['costos']['cogs_periodo'], 2) }}
                    </p>
                    <p class="mt-1 text-[11px] text-slate-500 font-medium">
                        <b class="text-slate-800">{{ $kpis['costos']['unidades_vendidas'] }}</b> unidades despachadas
                    </p>
                </div>

                <!-- KPI 3: Ganancia Neta -->
                <div class="relative overflow-hidden rounded-2xl border-2 border-emerald-100 bg-gradient-to-b from-emerald-50/40 to-white p-4 print:border-emerald-300 print:bg-white shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700">3. Ganancia Neta</span>
                        <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px]">
                            <i class="fas fa-arrow-trend-up"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xl sm:text-2xl font-black text-emerald-600 tabular-nums">
                        ${{ number_format($kpis['ganancias']['neta'], 2) }}
                    </p>
                    <p class="mt-1 text-[11px] text-emerald-700 font-bold">
                        Margen real: {{ $kpis['ganancias']['margen_porcentaje'] }}%
                    </p>
                </div>

                <!-- KPI 4: Comisiones -->
                <div class="relative overflow-hidden rounded-2xl border-2 border-indigo-100 bg-gradient-to-b from-indigo-50/40 to-white p-4 print:border-indigo-300 print:bg-white shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-indigo-700">4. Comisiones</span>
                        <div class="w-6 h-6 rounded-md bg-indigo-100 text-indigo-600 flex items-center justify-center text-[10px]">
                            <i class="fas fa-hand-holding-dollar"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xl sm:text-2xl font-black text-indigo-700 tabular-nums">
                        ${{ number_format($kpis['ganancias']['comisiones_periodo'], 2) }}
                    </p>
                    <p class="mt-1 text-[11px] text-slate-500 font-medium">
                        Pendiente: <b class="text-indigo-800">${{ number_format($kpis['comisiones_por_pagar']['total_pendiente'], 2) }}</b>
                    </p>
                </div>
            </section>

            <!-- D. GRÁFICOS VISUALES EJECUTIVOS (ESTILO PDF / CORPORATIVO) -->
            <section class="grid grid-cols-1 lg:grid-cols-12 gap-5 print:grid-cols-12 print:gap-4 print:break-inside-avoid">
                <!-- GRÁFICO 1: COMPARATIVA FINANCIERA (INGRESOS VS COSTOS VS GANANCIAS) -->
                <div class="lg:col-span-7 print:col-span-7 bg-slate-50/80 rounded-2xl border border-slate-200 p-4 sm:p-5 print:bg-white print:border-slate-300">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3 pb-2.5 border-b border-slate-200">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Comparativa Financiera del Periodo</h3>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5">Ingresos Brutos vs. Costo de Ventas vs. Ganancias Netas</p>
                        </div>
                        <div class="flex items-center gap-2.5 text-[10px] font-bold">
                            <span class="inline-flex items-center gap-1 text-blue-600">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span> Ingresos
                            </span>
                            <span class="inline-flex items-center gap-1 text-rose-500">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span> Costos
                            </span>
                            <span class="inline-flex items-center gap-1 text-emerald-600">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Ganancias
                            </span>
                        </div>
                    </div>
                    <div class="relative h-[210px] w-full">
                        <canvas id="chartReporteFinanciero"></canvas>
                    </div>
                </div>

                <!-- GRÁFICO 2: FLUJO DE UNIDADES Y OPERACIONES -->
                <div class="lg:col-span-5 print:col-span-5 bg-slate-50/80 rounded-2xl border border-slate-200 p-4 sm:p-5 print:bg-white print:border-slate-300">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3 pb-2.5 border-b border-slate-200">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Flujo de Stock & Comisiones</h3>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5">Entradas físicas vs. Despachos y Comisiones</p>
                        </div>
                        <div class="flex items-center gap-2.5 text-[10px] font-bold">
                            <span class="inline-flex items-center gap-1 text-emerald-600">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Entradas
                            </span>
                            <span class="inline-flex items-center gap-1 text-indigo-600">
                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span> Salidas
                            </span>
                        </div>
                    </div>
                    <div class="relative h-[210px] w-full">
                        <canvas id="chartReporteMovimientos"></canvas>
                    </div>
                </div>
            </section>

            <!-- E. TABLA DETALLADA DE MOVIMIENTOS -->
            <div class="overflow-x-auto print:overflow-visible rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <colgroup>
                        <col style="width: 3.5%;">   <!-- # -->
                        <col style="width: 8.5%;">   <!-- Fecha -->
                        <col style="width: 8%;">     <!-- Tipo -->
                        <col style="width: 8%;">     <!-- Folio -->
                        <col style="width: 23%;">    <!-- Producto / Descripción -->
                        <col style="width: 5.5%;">   <!-- Cant. -->
                        <col style="width: 7%;">     <!-- C. Unit. -->
                        <col style="width: 7.5%;">   <!-- C. Total -->
                        <col style="width: 8%;">     <!-- Ingreso -->
                        <col style="width: 7%;">     <!-- Comisión -->
                        <col style="width: 8%;">     <!-- Ganancia -->
                        <col style="width: 14.5%;">  <!-- Responsable -->
                    </colgroup>
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 text-[10px] font-black uppercase tracking-wider border-b-2 border-blue-600 print:bg-slate-100 print:text-slate-800 print:border-b-2 print:border-blue-600">
                            <th class="py-2.5 px-1 text-center">#</th>
                            <th class="py-2.5 px-1.5 text-center">Fecha</th>
                            <th class="py-2.5 px-1 text-center">Tipo</th>
                            <th class="py-2.5 px-1 text-center">Folio</th>
                            <th class="py-2.5 px-2 text-left">Producto / Descripción</th>
                            <th class="py-2.5 px-1 text-right">Cant.</th>
                            <th class="py-2.5 px-1 text-right">C. Unit.</th>
                            <th class="py-2.5 px-1 text-right">C. Total</th>
                            <th class="py-2.5 px-1 text-right">Ingreso</th>
                            <th class="py-2.5 px-1 text-right">Comisión</th>
                            <th class="py-2.5 px-1 text-right">Ganancia</th>
                            <th class="py-2.5 px-2 text-left">Responsable</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 print:divide-slate-200 text-[11px]">
                        @php
                            $items = (request()->query('modo') === 'imprimir' || request()->has('imprimir')) ? $todosMovimientos : $movimientos;
                        @endphp

                        @forelse ($items as $indice => $mov)
                            <tr class="hover:bg-blue-50/30 transition-colors {{ $loop->even ? 'bg-slate-50/50' : 'bg-white' }} print:break-inside-avoid">
                                <!-- # -->
                                <td class="py-2 px-1 text-center text-slate-400 font-mono text-[10px]">
                                    {{ $indice + 1 }}
                                </td>

                                <!-- Fecha y hora -->
                                <td class="py-2 px-1.5 text-center leading-tight">
                                    <span class="font-bold text-slate-800 block text-[10px] sm:text-[11px]">{{ $mov['fecha_formateada'] }}</span>
                                    <span class="block text-[9px] text-slate-400 font-mono">{{ $mov['hora_formateada'] }}</span>
                                </td>

                                <!-- Tipo Badge -->
                                <td class="py-2 px-1 text-center">
                                    @php
                                        $badgeClasses = match($mov['tipo_badge']) {
                                            'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-300 font-black',
                                            'rose' => 'bg-rose-50 text-rose-800 border-rose-300 font-black',
                                            'amber' => 'bg-amber-50 text-amber-800 border-amber-300 font-black',
                                            'indigo' => 'bg-indigo-50 text-indigo-800 border-indigo-300 font-black',
                                            'purple' => 'bg-purple-50 text-purple-800 border-purple-300 font-black',
                                            default => 'bg-slate-100 text-slate-700 border-slate-300',
                                        };
                                    @endphp
                                    <span class="inline-block px-1.5 py-0.5 rounded-full text-[8.5px] uppercase tracking-tight border leading-none {{ $badgeClasses }}">
                                        {{ $mov['categoria'] }}
                                    </span>
                                </td>

                                <!-- Folio -->
                                <td class="py-2 px-1 text-center font-mono font-bold text-slate-800 text-[10px] sm:text-[11px]">
                                    {{ $mov['folio'] }}
                                </td>

                                <!-- Producto / Descripción & SKU -->
                                <td class="py-2 px-2 print:py-1.5 print:px-1.5 leading-snug">
                                    <p class="font-bold text-slate-900 break-words whitespace-normal print:text-[8pt] print:leading-tight" title="{{ $mov['descripcion'] }}">
                                        {{ $mov['descripcion'] }}
                                    </p>
                                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-mono print:text-[7pt] mt-0.5">
                                        SKU: {{ $mov['sku'] }}
                                    </p>
                                </td>

                                <!-- Cantidad con signo -->
                                <td class="py-2 px-1 text-right font-black tabular-nums {{ $mov['signo_stock'] === '+' ? 'text-emerald-700' : ($mov['signo_stock'] === '-' ? 'text-rose-700' : 'text-slate-700') }}">
                                    {{ $mov['signo_stock'] }}{{ $mov['cantidad'] }}
                                </td>

                                <!-- Costo Unitario -->
                                <td class="py-2 px-1 text-right tabular-nums text-slate-600">
                                    ${{ number_format($mov['costo_unitario'], 2) }}
                                </td>

                                <!-- Costo Total -->
                                <td class="py-2 px-1 text-right tabular-nums font-semibold text-slate-800">
                                    ${{ number_format($mov['costo_total'], 2) }}
                                </td>

                                <!-- Ingreso Total -->
                                <td class="py-2 px-1 text-right tabular-nums font-black text-slate-900">
                                    ${{ number_format($mov['ingreso_total'], 2) }}
                                </td>

                                <!-- Comisión -->
                                <td class="py-2 px-1 text-right tabular-nums text-indigo-700 font-semibold">
                                    {{ $mov['comision'] > 0 ? '$' . number_format($mov['comision'], 2) : '—' }}
                                </td>

                                <!-- Ganancia Neta -->
                                <td class="py-2 px-1 text-right tabular-nums font-black {{ $mov['ganancia_neta'] > 0 ? 'text-emerald-700' : ($mov['ganancia_neta'] < 0 ? 'text-rose-700' : 'text-slate-400') }}">
                                    {{ $mov['ganancia_neta'] != 0 ? '$' . number_format($mov['ganancia_neta'], 2) : '—' }}
                                </td>

                                <!-- Responsable / Origen -->
                                <td class="py-2 px-2 text-slate-600 text-[10px] print:text-[7.5pt] print:py-1.5 print:px-1.5 leading-snug">
                                    <span class="font-bold text-slate-800 block break-words whitespace-normal leading-tight">{{ $mov['usuario_responsable'] }}</span>
                                    <span class="text-slate-500 block break-words whitespace-normal leading-tight mt-0.5 print:text-[7pt]" title="{{ $mov['origen_destino'] }}">{{ $mov['origen_destino'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="py-12 text-center text-slate-400">
                                    <i class="fas fa-boxes-stacked text-3xl mb-3 block text-slate-300"></i>
                                    <p class="font-bold text-sm">No se encontraron movimientos registrados para los filtros seleccionados.</p>
                                    <p class="text-xs text-slate-400 mt-1">Intenta ampliar el rango de fechas o seleccionar "Todo el historial".</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <!-- FILA FINAL DE TOTALES / RESUMEN CON DESTACADO CORPORATIVO -->
                    <tfoot>
                        <tr class="bg-blue-50/70 text-slate-900 font-black text-xs border-t-2 border-blue-600 border-b-2 border-slate-300 print:bg-blue-50/70 print:text-slate-900 print:border-t-2 print:border-blue-600 print:border-b-2 print:border-slate-300">
                            <td colspan="5" class="py-2.5 px-2 text-left uppercase tracking-wider text-[10px] sm:text-[11px] text-blue-950">
                                <i class="fas fa-calculator text-blue-600 mr-1.5"></i> Totales Generales ({{ $totales['cantidad_movimientos'] }} reg.)
                            </td>
                            <td class="py-2.5 px-1 text-right tabular-nums text-slate-900 font-black">
                                {{ $totales['total_unidades'] }}
                            </td>
                            <td class="py-2.5 px-1 text-right tabular-nums text-slate-400">—</td>
                            <td class="py-2.5 px-1 text-right tabular-nums text-rose-600 font-black">
                                ${{ number_format($totales['total_costos'], 2) }}
                            </td>
                            <td class="py-2.5 px-1 text-right tabular-nums text-blue-700 font-black">
                                ${{ number_format($totales['total_ingresos'], 2) }}
                            </td>
                            <td class="py-2.5 px-1 text-right tabular-nums text-indigo-700 font-black">
                                ${{ number_format($totales['total_comisiones'], 2) }}
                            </td>
                            <td class="py-2.5 px-1 text-right tabular-nums text-emerald-700 font-black">
                                ${{ number_format($totales['total_ganancias'], 2) }}
                            </td>
                            <td class="py-2.5 px-2 text-slate-400 text-center">—</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- PAGINACIÓN EN PANTALLA (SOLO PANTALLA) -->
            @if (request()->query('modo') !== 'imprimir' && !request()->has('imprimir') && $movimientos instanceof \Illuminate\Pagination\LengthAwarePaginator && $movimientos->hasPages())
                <div class="no-print mt-4 py-3 px-4 sm:px-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs text-slate-500 font-medium">
                        Mostrando <b class="text-slate-800 font-bold">{{ $movimientos->firstItem() }}</b> a <b class="text-slate-800 font-bold">{{ $movimientos->lastItem() }}</b> de <b class="text-slate-800 font-bold">{{ $movimientos->total() }}</b> registros
                    </div>

                    <nav class="flex items-center gap-1.5" aria-label="Navegación de páginas">
                        {{-- Botón Anterior --}}
                        @if ($movimientos->onFirstPage())
                            <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 text-slate-300 text-xs cursor-not-allowed select-none">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $movimientos->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 text-xs transition-colors cursor-pointer shadow-sm">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        @endif

                        {{-- Números de Página --}}
                        @foreach ($movimientos->getUrlRange(max(1, $movimientos->currentPage() - 2), min($movimientos->lastPage(), $movimientos->currentPage() + 2)) as $page => $url)
                            @if ($page == $movimientos->currentPage())
                                <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-600 text-white text-xs font-black shadow-md shadow-blue-500/25">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 text-xs font-bold transition-colors cursor-pointer">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Botón Siguiente --}}
                        @if ($movimientos->hasMorePages())
                            <a href="{{ $movimientos->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 text-xs transition-colors cursor-pointer shadow-sm">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @else
                            <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 text-slate-300 text-xs cursor-not-allowed select-none">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        @endif
                    </nav>
                </div>
            @endif

            <!-- E. PIE DE PÁGINA FORMAL DEL INFORME CON SELLOS DE AUDITORÍA -->
            <footer id="report-footer" class="report-footer pt-6 border-t-2 border-slate-200 text-slate-500 text-[10px] space-y-4 print:pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 items-end">
                    <!-- Código y autenticidad -->
                    <div class="space-y-1">
                        <p class="font-bold text-slate-800 uppercase tracking-wider">Certificación de Datos</p>
                        <p class="text-[10px] leading-relaxed text-slate-500">Documento emitido conforme a las directrices contables de AXStore. La información refleja fielmente los movimientos físicos y financieros registrados.</p>
                        <!-- Código de barras simulado para estilo formal -->
                        <div class="pt-1 font-mono tracking-widest text-slate-400 text-[10px]">
                            ||||| | ||||| |||| | ||||||| | ||||
                        </div>
                    </div>

                    <!-- Cuadro de Firma y Sello -->
                    <div class="text-center">
                        <div class="w-48 mx-auto border-b-2 border-dashed border-slate-400 mb-1.5 h-10 flex items-end justify-center">
                            <span class="text-[9px] text-slate-400 uppercase tracking-wider mb-0.5">Sello de Control Interno</span>
                        </div>
                        <p class="font-black text-slate-800 uppercase tracking-wider text-[10px]">{{ $usuarioEmisor }}</p>
                        <p class="text-[9px] text-slate-500">Revisor / Auditor Autorizado</p>
                    </div>

                    <!-- Cláusula y Visto Bueno -->
                    <div class="text-left sm:text-right space-y-1">
                        <p class="font-bold text-slate-800 uppercase tracking-wider">Visto Bueno Gerencia</p>
                        <div class="w-48 ml-auto border-b-2 border-dashed border-slate-400 mb-1.5 h-10 flex items-end justify-center">
                            <span class="text-[9px] text-slate-400 uppercase tracking-wider mb-0.5">Firma de Dirección</span>
                        </div>
                        <p class="text-[9px] text-slate-400">Dirección de Operaciones</p>
                    </div>
                </div>

                <div class="pt-3 text-center text-slate-400 border-t border-slate-100 flex flex-wrap items-center justify-between text-[10px]">
                    <span class="font-semibold text-slate-600">AXStore ERP v2.0 · Subsistema de Control & Reportes Financieros</span>
                    <span class="font-mono text-slate-500">Folio: {{ $folioReporte }} · Generado: {{ $fechaGeneracion }}</span>
                </div>
            </footer>

            <!-- BARRA FIJA AL PIE EN TODAS LAS PÁGINAS IMPRESAS (RUNNING FOOTER) -->
            <div class="report-running-footer">
                <span>AXStore ERP v2.0 · Subsistema de Control & Reportes Financieros</span>
                <span>Folio: {{ $folioReporte }} · Generado: {{ $fechaGeneracion }}</span>
            </div>

        </article>
    </div>

    <!-- ESTILOS EXHAUSTIVOS DE IMPRESIÓN (@media print) -->
    <style>
        .report-running-footer {
            display: none;
        }

        @media print {
            /* 1. Márgenes y orientación HORIZONTAL para que las 12 columnas quepan sin cortes */
            @page {
                margin: 8mm 6mm 10mm 6mm;
                size: landscape;
            }

            body {
                background-color: #ffffff !important;
                color: #0f172a !important;
                font-family: 'Inter', system-ui, sans-serif !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* 2. Ocultar estrictamente el navbar superior del sistema, menús y botones */
            body > header,
            body > footer,
            header:not(.report-header),
            footer:not(.report-footer),
            nav,
            .no-print,
            .swal2-container,
            #mobile-menu,
            button,
            .btn {
                display: none !important;
            }

            /* 3. Asegurar que el membrete y encabezado del informe SIEMPRE se muestren */
            .report-header {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
                margin-bottom: 12px !important;
            }

            .logo-box {
                background-color: #ffffff !important;
                border: 1px solid #cbd5e1 !important;
            }

            .logo-box img {
                display: block !important;
                max-height: 55px !important;
                width: auto !important;
            }

            /* 4. Contenedores a ancho completo y layout flexible para empujar el pie de página */
            main {
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            #reporte-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .report-paper {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                display: flex !important;
                flex-direction: column !important;
                min-height: calc(100vh - 18mm) !important;
            }

            /* 5. Prevenir cortes feos a mitad de fila o bloque */
            tr, .print-break-inside-avoid, section {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            /* 5b. Columnas proporcionales y limpias para la tabla */
            table {
                table-layout: fixed !important;
                width: 100% !important;
                font-size: 8pt !important;
            }

            /* Asegurar que las celdas hagan salto de línea completo sin cortar texto */
            table th, table td {
                padding: 3.5px 3px !important;
                font-size: 7.5pt !important;
                line-height: 1.25 !important;
                vertical-align: top !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
            }

            table thead th {
                font-size: 7.5pt !important;
                letter-spacing: 0.02em !important;
                white-space: normal !important;
                word-break: break-word !important;
                vertical-align: middle !important;
            }

            /* El badge de tipo reducido */
            table tbody span[class*="rounded-full"] {
                font-size: 7.2pt !important;
                padding: 1px 3px !important;
                white-space: normal !important;
                display: inline-block !important;
                line-height: 1.1 !important;
            }

            /* 6. TABLA: Encabezados y totales corporativos armónicos (cero negro pesado) */
            table thead tr {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
                border-bottom: 2px solid #2563eb !important;
            }

            table thead th {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
                font-weight: 800 !important;
                border-bottom: 2px solid #2563eb !important;
            }

            /* Evita que los totales tfoot se repitan en la página 1 */
            table tfoot {
                display: table-row-group !important;
            }

            table tfoot tr {
                background-color: #eff6ff !important;
                color: #0f172a !important;
                border-top: 2px solid #2563eb !important;
                border-bottom: 2px solid #cbd5e1 !important;
            }

            table tfoot td {
                background-color: #eff6ff !important;
            }

            table th, table td {
                border-color: #e2e8f0 !important;
            }

            /* 7. Footer formal con firmas empujado al pie de la página */
            .report-footer {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
                border-top: 2px solid #e2e8f0 !important;
                padding-top: 14px !important;
                margin-bottom: 24px !important;
            }

            /* 8. Running footer fijo en la parte inferior de cada hoja */
            .report-running-footer {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                height: 20px !important;
                background: #ffffff !important;
                border-top: 1px solid #cbd5e1 !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                font-size: 9px !important;
                color: #64748b !important;
                padding: 0 4px !important;
                z-index: 9999 !important;
            }
        }
    </style>

    <script>
        /**
         * Calcula el espaciado dinámico necesario para que el bloque de firmas y certificación
         * se posicione exactamente al pie de la página impresa, tanto si queda en la misma hoja
         * como si salta a una nueva página.
         */
        function ajustarPieImpresion() {
            const footer = document.getElementById('report-footer');
            const paper = document.querySelector('.report-paper');
            const table = paper ? paper.querySelector('table') : null;
            if (!footer || !table || !paper) return;

            // Restablecer margen para medir con precisión
            footer.style.marginTop = '0px';

            // Medir la distancia de la tabla respecto al documento impreso real (.report-paper)
            const paperRect = paper.getBoundingClientRect();
            const tableRect = table.getBoundingClientRect();
            const tableBottom = tableRect.bottom - paperRect.top;
            const footerHeight = footer.offsetHeight || 130;

            // Altura útil de una página de impresión estándar en horizontal (A4 / Carta Landscape ~ 700px a 96 DPI)
            const PAGE_HEIGHT = 680;
            const SAFETY_PADDING = 25;

            // Posición vertical donde termina la tabla dentro de la página actual
            const posInCurrentPage = tableBottom % PAGE_HEIGHT;

            // ¿Cabe el footer en la misma página donde termina la tabla?
            if (posInCurrentPage + footerHeight + SAFETY_PADDING <= PAGE_HEIGHT) {
                // Caso 1: Cabe en la misma página, lo empujamos hacia el pie de esa hoja
                const spaceToBottom = PAGE_HEIGHT - posInCurrentPage - footerHeight - SAFETY_PADDING;
                footer.style.marginTop = Math.max(16, Math.floor(spaceToBottom)) + 'px';
            } else {
                // Caso 2: Salta a la siguiente página. En la nueva hoja lo empujamos con margen moderado para no crear otra hoja vacía
                footer.style.marginTop = '24px';
            }
        }

        function prepararEImprimir() {
            ajustarPieImpresion();
            setTimeout(() => {
                window.print();
            }, 60);
        }

        window.addEventListener('beforeprint', ajustarPieImpresion);
        window.addEventListener('afterprint', () => {
            const footer = document.getElementById('report-footer');
            if (footer) footer.style.marginTop = '';
        });

        // Inicialización de Gráficos Ejecutivos con Chart.js
        function inicializarGraficosReporte() {
            if (typeof Chart === 'undefined') return;

            const compData = @js($graficoComparativa ?? ['labels' => [], 'ingresos' => [], 'costos' => [], 'ganancias' => []]);
            const movData = @js($graficoMovimientos ?? ['labels' => [], 'entradas' => [], 'salidas' => [], 'comisiones' => []]);

            // 1. Gráfico Financiero (Ingresos vs Costos vs Ganancias)
            const ctxFin = document.getElementById('chartReporteFinanciero');
            if (ctxFin && compData.labels && compData.labels.length) {
                // Destruir instancia previa si existe para evitar conflictos
                if (window.chartFinancieroInstance) {
                    window.chartFinancieroInstance.destroy();
                }
                window.chartFinancieroInstance = new Chart(ctxFin, {
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
                                borderWidth: 2,
                                pointRadius: compData.labels.length > 25 ? 0 : 2.5,
                                pointHoverRadius: 4,
                            },
                            {
                                label: 'Costos ($)',
                                data: compData.costos,
                                borderColor: '#f43f5e', // Rose 500
                                backgroundColor: 'transparent',
                                borderDash: [3, 3],
                                tension: 0.3,
                                borderWidth: 1.8,
                                pointRadius: compData.labels.length > 25 ? 0 : 2,
                                pointHoverRadius: 4,
                            },
                            {
                                label: 'Ganancia Neta ($)',
                                data: compData.ganancias,
                                borderColor: '#10b981', // Emerald 500
                                backgroundColor: 'rgba(16, 185, 129, 0.08)',
                                fill: true,
                                tension: 0.3,
                                borderWidth: 2,
                                pointRadius: compData.labels.length > 25 ? 0 : 2.5,
                                pointHoverRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: false, // Inmediato para que salga nítido al imprimir o visualizar
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { family: 'Inter', size: 11, weight: 'bold' },
                                bodyFont: { family: 'Inter', size: 11 },
                                padding: 8,
                                cornerRadius: 6,
                                callbacks: {
                                    label: function(ctx) {
                                        return (ctx.dataset.label || '') + ': $' + Number(ctx.parsed.y).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { family: 'Inter', size: 9 },
                                    color: '#64748b',
                                    maxTicksLimit: 12,
                                }
                            },
                            y: {
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    font: { family: 'Inter', size: 9 },
                                    color: '#64748b',
                                    callback: function(val) { return '$' + val; }
                                }
                            }
                        }
                    }
                });
            }

            // 2. Gráfico de Flujo de Stock y Operaciones
            const ctxMov = document.getElementById('chartReporteMovimientos');
            if (ctxMov && movData.labels && movData.labels.length) {
                if (window.chartMovimientosInstance) {
                    window.chartMovimientosInstance.destroy();
                }
                window.chartMovimientosInstance = new Chart(ctxMov, {
                    type: 'bar',
                    data: {
                        labels: movData.labels,
                        datasets: [
                            {
                                label: 'Entradas (Uds)',
                                data: movData.entradas,
                                backgroundColor: '#10b981', // Emerald 500
                                borderRadius: 4,
                                barPercentage: 0.6,
                            },
                            {
                                label: 'Salidas (Uds)',
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
                        animation: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { family: 'Inter', size: 11, weight: 'bold' },
                                bodyFont: { family: 'Inter', size: 11 },
                                padding: 8,
                                cornerRadius: 6,
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { family: 'Inter', size: 9 },
                                    color: '#64748b',
                                    maxTicksLimit: 12,
                                }
                            },
                            y: {
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    font: { family: 'Inter', size: 9 },
                                    color: '#64748b',
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', inicializarGraficosReporte);
        } else {
            inicializarGraficosReporte();
        }

        document.addEventListener('livewire:navigated', inicializarGraficosReporte);
    </script>
</x-app>
