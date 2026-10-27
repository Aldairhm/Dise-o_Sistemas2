<x-app title="Mi Dashboard | AXStore">

    <div class="max-w-[1440px] mx-auto space-y-6"
         x-data="vendedorDashboard({
             ventas: {{ Js::from($ventas) }},
             bonos: {{ Js::from($bonos) }},
             resumen: {{ Js::from($resumen) }},
             salesStats: {{ Js::from($ventasStats) }},
             metaMensual: {{ (float) $metaMensual }},
             periodoActual: '{{ $periodoActual }}'
         })"
         id="dashboardVendedor">

        {{-- ═══════════════════════════════════════════════════════════════════
             1. ENCABEZADO + FILTROS DE PERIODO + BOTONES DE ACCIÓN
        ═══════════════════════════════════════════════════════════════════ --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-blue-600 transition-colors mb-2">
                    <i class="fas fa-arrow-left text-[10px]"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20">
                        <i class="fas fa-chart-line text-lg"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-blue-600">Panel de Rendimiento</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-600">Vendedor</span>
                        </div>
                        <h1 class="text-2xl font-black tracking-tight text-slate-900">
                            Hola, {{ Auth::user()->nombre_real ?? Auth::user()->username }}
                        </h1>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                {{-- Filtro de periodo en píldora redondeada --}}
                <div class="inline-flex items-center rounded-full border border-slate-200 bg-white p-1 text-xs font-bold shadow-xs">
                    @foreach(['hoy' => 'Hoy', 'semana' => 'Semana', 'mes' => 'Mes', 'todo' => 'Todo'] as $key => $label)
                        <a href="{{ route('vendedor.dashboard', ['periodo' => $key]) }}"
                           class="px-3.5 py-1.5 rounded-full transition-all {{ $periodoActual === $key ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                {{-- Botones de acción rápida --}}
                <a href="{{ route('ventas.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md shadow-blue-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-shopping-cart text-[11px]"></i>
                    <span>NUEVA VENTA</span>
                </a>

                <a href="{{ route('comisiones.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-extrabold text-xs shadow-xs transition-all hover:border-slate-300">
                    <i class="fas fa-hand-holding-dollar text-slate-400"></i>
                    <span>MIS COMISIONES</span>
                </a>
            </div>
        </div>

        @php
            $hayDatos = count($ventas) > 0 || count($bonos) > 0;
        @endphp

        @if(!$hayDatos && $periodoActual !== 'todo')
            {{-- ESTADO VACÍO (AMIGABLE) --}}
            <div class="bg-white border border-slate-200 rounded-3xl py-16 px-6 text-center shadow-xs">
                <div class="w-20 h-20 mx-auto bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-5">
                    <i class="fas fa-chart-line text-3xl"></i>
                </div>
                <h2 class="text-xl font-black text-slate-800 mb-1">
                    Aún no tienes actividad {{ $periodoActual === 'hoy' ? 'hoy' : ($periodoActual === 'semana' ? 'esta semana' : 'este mes') }}
                </h2>
                <p class="text-sm text-slate-400 mb-6 max-w-md mx-auto">
                    Registra tu primera venta y cada comisión se calculará y visualizará aquí de forma automática.
                </p>
                <a href="{{ route('ventas.create') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition-all hover:scale-[1.02]">
                    <i class="fas fa-plus"></i> Registrar mi primera venta
                </a>
            </div>
        @else

            {{-- ═══════════════════════════════════════════════════════════════════
                 2. 4 TARJETAS KPIS ESTRATÉGICAS
            ═══════════════════════════════════════════════════════════════════ --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                {{-- 1. TOTAL GANADO --}}
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                    <div class="absolute top-0 left-0 h-1 w-full bg-blue-600"></div>
                    <div class="flex items-start justify-between">
                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">TOTAL GANADO</span>
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-hand-holding-dollar text-xs"></i>
                        </div>
                    </div>
                    <h3 class="mt-2 text-3xl font-black text-slate-900 tracking-tight tabular-nums">
                        $<span x-text="formatMoney(summary.total_ganado)">{{ number_format($resumen['total_ganado'], 2) }}</span>
                    </h3>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5 text-slate-500 font-medium">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            Comisiones + Bonos
                        </span>
                        @if($periodoActual !== 'todo' && $variacion != 0)
                            <span class="font-bold flex items-center gap-0.5 {{ $variacion > 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                                <i class="fas {{ $variacion > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-[10px]"></i>
                                {{ abs($variacion) }}%
                            </span>
                        @endif
                    </div>
                </div>

                {{-- 2. COBRADO --}}
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                    <div class="absolute top-0 left-0 h-1 w-full bg-emerald-500"></div>
                    <div class="flex items-start justify-between">
                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">COBRADO</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    </div>
                    <h3 class="mt-2 text-3xl font-black text-emerald-600 tracking-tight tabular-nums">
                        $<span x-text="formatMoney(summary.cobrado)">{{ number_format($resumen['cobrado'], 2) }}</span>
                    </h3>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5 text-slate-500 font-medium">
                            <i class="fas fa-money-bill-wave text-emerald-500 text-[11px]"></i>
                            Ya liquidado
                        </span>
                        <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-100">
                            Efectivo / Transferencia
                        </span>
                    </div>
                </div>

                {{-- 3. PENDIENTE DE COBRO --}}
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                    <div class="absolute top-0 left-0 h-1 w-full bg-amber-500"></div>
                    <div class="flex items-start justify-between">
                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">PENDIENTE DE COBRO</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i class="fas fa-clock text-xs"></i>
                        </div>
                    </div>
                    <h3 class="mt-2 text-3xl font-black text-amber-600 tracking-tight tabular-nums">
                        $<span x-text="formatMoney(summary.pendiente_cobro)">{{ number_format($resumen['pendiente_cobro'], 2) }}</span>
                    </h3>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5 text-slate-500 font-medium">
                            <i class="fas fa-hourglass-half text-amber-500 text-[11px]"></i>
                            Por liquidar
                        </span>
                        <span class="px-2 py-0.5 rounded-lg bg-amber-50 text-amber-700 font-bold text-[10px] border border-amber-100">
                            Confirmadas
                        </span>
                    </div>
                </div>

                {{-- 4. POR GANAR --}}
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                    <div class="absolute top-0 left-0 h-1 w-full bg-slate-400"></div>
                    <div class="flex items-start justify-between">
                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">POR GANAR</span>
                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                            <i class="fas fa-chart-line text-xs"></i>
                        </div>
                    </div>
                    <h3 class="mt-2 text-3xl font-black text-slate-800 tracking-tight tabular-nums">
                        $<span x-text="formatMoney(summary.proyectada)">{{ number_format($resumen['proyectada'], 2) }}</span>
                    </h3>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5 text-slate-400 font-medium">
                            <i class="far fa-circle-dot text-[11px]"></i>
                            En proceso
                        </span>
                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 font-bold text-[10px] border border-slate-200">
                            En ruta / Pendiente
                        </span>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════════
                 3. META MENSUAL DE GANANCIAS
            ═══════════════════════════════════════════════════════════════════ --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-bullseye text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-800">META MENSUAL DE GANANCIAS</h4>
                            <p class="text-xs text-slate-400">Objetivo de comisiones y bonos acumulados en el mes</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-extrabold text-slate-800">
                            $<span x-text="formatMoney(summary.total_ganado)">{{ number_format($resumen['total_ganado'], 2) }}</span> / ${{ number_format($metaMensual, 2) }}
                        </span>
                    </div>
                </div>

                {{-- Barra de progreso --}}
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden mt-3">
                    <div class="bg-blue-600 h-full rounded-full transition-all duration-700"
                         :style="`width: ${metaProgreso}%`"
                         style="width: {{ $progresoMeta }}%"></div>
                </div>

                <div class="flex items-center justify-between mt-2 text-xs">
                    <span class="text-slate-400 font-medium">Progreso del periodo</span>
                    <span class="text-blue-600 font-bold" x-text="`${metaProgreso}% completado`">{{ $progresoMeta }}% completado</span>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════════
                 4. FRANJA RESUMEN DE VENTAS (Vendido, Entregadas, En Proceso, Canceladas)
            ═══════════════════════════════════════════════════════════════════ --}}
            <x-vendedor.stats-row :stats="$ventasStats" />

            {{-- ═══════════════════════════════════════════════════════════════════
                 5. BONOS E INCENTIVOS (SI EXISTEN)
            ═══════════════════════════════════════════════════════════════════ --}}
            <x-vendedor.bonus-list :bonos="$bonos" />

            {{-- ═══════════════════════════════════════════════════════════════════
                 6. TABLA DE VENTAS Y COMISIONES (BÚSQUEDA, FILTROS Y DETALLE)
            ═══════════════════════════════════════════════════════════════════ --}}
            <x-vendedor.sales-commission-table :ventas="$ventas" />

        @endif

    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         LÓGICA ALPINE.JS (CÁLCULOS DINÁMICOS Y FILTRADO 100% EN TIEMPO REAL)
    ═══════════════════════════════════════════════════════════════════ --}}
    <script>
        function vendedorDashboard(initial) {
            return {
                salesList: initial.ventas || [],
                bonusList: initial.bonos || [],
                metaMensual: initial.metaMensual || 500,
                searchQuery: '',
                filterStatus: 'todos', // todos | pagada | pendiente_cobro | proyectada | anulada
                expandedRow: null,

                toggleRow(id) {
                    this.expandedRow = this.expandedRow === id ? null : id;
                },

                get filteredSales() {
                    const q = this.searchQuery.toLowerCase().trim();
                    const status = this.filterStatus;

                    return this.salesList.filter(item => {
                        const matchesQuery = !q ||
                            (item.folio && item.folio.toLowerCase().includes(q)) ||
                            (item.cliente && item.cliente.toLowerCase().includes(q)) ||
                            (item.id && item.id.toString().includes(q));

                        const matchesStatus = status === 'todos' || (item.comision && item.comision.estado === status);

                        return matchesQuery && matchesStatus;
                    });
                },

                get filteredTotals() {
                    let sumMonto = 0;
                    let sumComision = 0;

                    this.filteredSales.forEach(item => {
                        sumMonto += Number(item.monto || 0);
                        if (item.comision && item.comision.estado !== 'anulada') {
                            sumComision += Number(item.comision.monto || 0);
                        }
                    });

                    const pctAvg = sumMonto > 0 ? ((sumComision / sumMonto) * 100).toFixed(1) : '0.0';

                    return {
                        monto: sumMonto,
                        comision: sumComision,
                        porcentajeAvg: pctAvg
                    };
                },

                get summary() {
                    let comisionesPagadas = 0;
                    let comisionesPendientes = 0;
                    let comisionesProyectadas = 0;
                    let comisionesAnuladas = 0;

                    this.salesList.forEach(v => {
                        const c = v.comision;
                        if (!c) return;
                        const m = Number(c.monto || 0);
                        if (c.estado === 'pagada') comisionesPagadas += m;
                        else if (c.estado === 'pendiente_cobro') comisionesPendientes += m;
                        else if (c.estado === 'proyectada') comisionesProyectadas += m;
                        else if (c.estado === 'anulada') comisionesAnuladas += m;
                    });

                    let bonosPagados = 0;
                    let bonosPendientes = 0;
                    this.bonusList.forEach(b => {
                        const m = Number(b.monto || 0);
                        if (b.estado === 'pagado') bonosPagados += m;
                        else bonosPendientes += m;
                    });

                    const cobrado = comisionesPagadas + bonosPagados;
                    const pendienteCobro = comisionesPendientes + bonosPendientes;
                    const totalGanado = cobrado + pendienteCobro;

                    return {
                        total_ganado: totalGanado,
                        cobrado: cobrado,
                        pendiente_cobro: pendienteCobro,
                        proyectada: comisionesProyectadas,
                        anulada: comisionesAnuladas,
                    };
                },

                get metaProgreso() {
                    if (this.metaMensual <= 0) return 0;
                    return Math.min(100, Math.round((this.summary.total_ganado / this.metaMensual) * 100));
                },

                get salesStats() {
                    return initial.salesStats || {
                        total_monto: 0,
                        total_ordenes: 0,
                        entregadas: 0,
                        monto_entregadas: 0,
                        en_proceso: 0,
                        pendientes: 0,
                        confirmadas: 0,
                        en_ruta: 0,
                        canceladas: 0,
                        devoluciones: 0,
                    };
                },

                formatMoney(amount) {
                    const num = Number(amount || 0);
                    return num.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },

                getVentaBadgeClass(estado) {
                    switch (estado) {
                        case 'entregada': return 'bg-emerald-50 text-emerald-700 border border-emerald-200/80';
                        case 'confirmada': return 'bg-sky-50 text-sky-700 border border-sky-200/80';
                        case 'en_ruta': return 'bg-indigo-50 text-indigo-700 border border-indigo-200/80';
                        case 'pendiente': return 'bg-amber-50 text-amber-700 border border-amber-200/80';
                        case 'cancelada': return 'bg-rose-50 text-rose-700 border border-rose-200/80';
                        case 'devolucion': return 'bg-orange-50 text-orange-700 border border-orange-200/80';
                        case 'cambio': return 'bg-purple-50 text-purple-700 border border-purple-200/80';
                        default: return 'bg-slate-100 text-slate-600 border border-slate-200/80';
                    }
                },

                getVentaDotClass(estado) {
                    switch (estado) {
                        case 'entregada': return 'bg-emerald-500';
                        case 'confirmada': return 'bg-sky-500';
                        case 'en_ruta': return 'bg-indigo-500';
                        case 'pendiente': return 'bg-amber-500';
                        case 'cancelada': return 'bg-rose-500';
                        case 'devolucion': return 'bg-orange-500';
                        case 'cambio': return 'bg-purple-500';
                        default: return 'bg-slate-400';
                    }
                },

                getComisionBadgeClass(estado) {
                    switch (estado) {
                        case 'pagada': return 'bg-emerald-50 text-emerald-700 border border-emerald-200/80';
                        case 'pendiente_cobro': return 'bg-amber-50 text-amber-700 border border-amber-200/80';
                        case 'proyectada': return 'bg-slate-100 text-slate-600 border border-slate-200/80';
                        case 'anulada': return 'bg-rose-50 text-rose-700 border border-rose-200/80';
                        default: return 'bg-slate-100 text-slate-600 border border-slate-200/80';
                    }
                },

                getComisionDotClass(estado) {
                    switch (estado) {
                        case 'pagada': return 'bg-emerald-500';
                        case 'pendiente_cobro': return 'bg-amber-500';
                        case 'proyectada': return 'bg-slate-400';
                        case 'anulada': return 'bg-rose-500';
                        default: return 'bg-slate-400';
                    }
                },

                getComisionLabel(estado) {
                    switch (estado) {
                        case 'pagada': return 'Cobrada';
                        case 'pendiente_cobro': return 'Por cobrar';
                        case 'proyectada': return 'Por ganar';
                        case 'anulada': return 'Anulada';
                        default: return estado || 'Pendiente';
                    }
                }
            };
        }
    </script>
</x-app>
