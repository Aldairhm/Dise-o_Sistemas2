<x-app title="Mi Dashboard | AXStore">

    <div class="max-w-[1440px] mx-auto space-y-7"
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
             1. ENCABEZADO + FILTRO DE PERIODO + BOTONES
        ═══════════════════════════════════════════════════════════════════ --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                    <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Inicio</a>
                    <span>/</span>
                    <span class="text-slate-600">Mi Dashboard</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900">
                    Hola, {{ Auth::user()->nombre_real ?? Auth::user()->username }} 👋
                </h1>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                {{-- Filtro de periodo --}}
                <div class="inline-flex items-center rounded-xl border border-slate-200 bg-white p-0.5 text-xs font-bold shadow-xs">
                    @foreach(['hoy' => 'Hoy', 'semana' => 'Semana', 'mes' => 'Mes', 'todo' => 'Todo'] as $key => $label)
                    <a href="{{ route('vendedor.dashboard', ['periodo' => $key]) }}"
                       class="px-3.5 py-1.5 rounded-[10px] transition-all {{ $periodoActual === $key ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>

                {{-- Botones de acción solicitados --}}
                <a href="{{ route('ventas.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-plus text-[10px]"></i> Nueva venta
                </a>
                <a href="{{ route('comisiones.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:text-indigo-600 hover:border-indigo-200 font-bold text-xs transition-all shadow-xs">
                    <i class="fas fa-hand-holding-dollar text-[10px] text-indigo-500"></i> Mis comisiones
                </a>
            </div>
        </div>

        @php
            $hayDatos = count($ventas) > 0 || count($bonos) > 0;
        @endphp

        @if(!$hayDatos && $periodoActual !== 'todo')
        {{-- ═══════════════════════════════════════════════════════════════════
             6. ESTADO VACÍO (AMIGABLE)
        ═══════════════════════════════════════════════════════════════════ --}}
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
             1. TARJETA HÉROE — TOTAL GANADO (ÚNICA CON FONDO FUERTE)
        ═══════════════════════════════════════════════════════════════════ --}}
        <x-vendedor.hero-card
            :resumen="$resumen"
            :metaMensual="$metaMensual"
            :progresoMeta="$progresoMeta"
            :variacion="$variacion"
            :periodoActual="$periodoActual"
        />

        {{-- ═══════════════════════════════════════════════════════════════════
             3. FRANJA COMPACTA DE RESUMEN DE VENTAS
        ═══════════════════════════════════════════════════════════════════ --}}
        <x-vendedor.stats-row :stats="$ventasStats" />

        {{-- ═══════════════════════════════════════════════════════════════════
             4. BONOS E INCENTIVOS (SOLO SI EXISTEN)
        ═══════════════════════════════════════════════════════════════════ --}}
        <x-vendedor.bonus-list :bonos="$bonos" />

        {{-- ═══════════════════════════════════════════════════════════════════
             2. TABLA PRINCIPAL: VENTAS Y COMISIONES (EL CENTRO DEL DASHBOARD)
        ═══════════════════════════════════════════════════════════════════ --}}
        <x-vendedor.sales-commission-table :ventas="$ventas" />

        {{-- ═══════════════════════════════════════════════════════════════════
             7. GRÁFICA DE COMISIONES POR DÍA (APILADA POR ESTADO)
        ═══════════════════════════════════════════════════════════════════ --}}
        @php
            $totalComisionesChart = collect($chartDias)->sum('total_comision');
            $maxComisionDia = max(1, collect($chartDias)->max('total_comision'));
        @endphp

        @if($totalComisionesChart > 0)
        <section class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black tracking-tight text-slate-900">Comisiones por día</h2>
                    <p class="text-xs text-slate-400">Desglose de los últimos 14 días según estado de comisión</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold text-slate-500">
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-emerald-500"></span> Cobrada</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-amber-500"></span> Por cobrar</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-slate-300"></span> Por ganar</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-sm">
                <div class="flex items-end gap-2 sm:gap-3" style="height: 180px;">
                    @foreach($chartDias as $d)
                    @php
                        $pctTotal = $maxComisionDia > 0 ? min(100, round(($d['total_comision'] / $maxComisionDia) * 100)) : 0;
                        $pctPagada = $d['total_comision'] > 0 ? round(($d['pagada'] / $d['total_comision']) * 100) : 0;
                        $pctPendiente = $d['total_comision'] > 0 ? round(($d['pendiente_cobro'] / $d['total_comision']) * 100) : 0;
                        $pctProyectada = $d['total_comision'] > 0 ? round(($d['proyectada'] / $d['total_comision']) * 100) : 0;
                    @endphp
                    <div class="flex-1 flex flex-col items-center justify-end h-full group cursor-pointer relative">
                        {{-- Tooltip hover con desglose --}}
                        <div class="absolute -top-14 left-1/2 -translate-x-1/2 hidden group-hover:flex flex-col items-center z-20 pointer-events-none">
                            <div class="bg-slate-900 text-white rounded-lg px-2.5 py-1.5 text-[10px] font-bold shadow-lg whitespace-nowrap space-y-0.5">
                                <div class="text-slate-300">{{ $d['dia'] }} · {{ $d['cantidad'] }} venta(s)</div>
                                <div>Total: ${{ number_format($d['total_comision'], 2) }}</div>
                                @if($d['pagada'] > 0)
                                <div class="text-emerald-300">Cobrado: ${{ number_format($d['pagada'], 2) }}</div>
                                @endif
                                @if($d['pendiente_cobro'] > 0)
                                <div class="text-amber-300">Por cobrar: ${{ number_format($d['pendiente_cobro'], 2) }}</div>
                                @endif
                                @if($d['proyectada'] > 0)
                                <div class="text-slate-300">Por ganar: ${{ number_format($d['proyectada'], 2) }}</div>
                                @endif
                            </div>
                            <div class="w-2 h-2 bg-slate-900 rotate-45 -mt-1"></div>
                        </div>

                        {{-- Barra apilada --}}
                        <div class="w-full flex justify-center items-end" style="height: 140px;">
                            <div class="w-full max-w-[36px] rounded-t-md overflow-hidden flex flex-col-reverse transition-all duration-300 {{ $d['esHoy'] ? 'ring-2 ring-blue-500 ring-offset-2' : '' }}"
                                 style="height: {{ max($d['total_comision'] > 0 ? 8 : 2, $pctTotal) }}%;">
                                @if($d['total_comision'] > 0)
                                    @if($d['pagada'] > 0)
                                    <div class="w-full bg-emerald-500" style="height: {{ $pctPagada }}%;"></div>
                                    @endif
                                    @if($d['pendiente_cobro'] > 0)
                                    <div class="w-full bg-amber-500" style="height: {{ $pctPendiente }}%;"></div>
                                    @endif
                                    @if($d['proyectada'] > 0)
                                    <div class="w-full bg-slate-300" style="height: {{ $pctProyectada }}%;"></div>
                                    @endif
                                @else
                                    <div class="w-full h-full bg-slate-100"></div>
                                @endif
                            </div>
                        </div>

                        {{-- Etiqueta del día --}}
                        <p class="text-[10px] sm:text-[11px] font-bold mt-2 whitespace-nowrap {{ $d['esHoy'] ? 'text-blue-600 font-black' : 'text-slate-400' }}">
                            {{ $d['label'] }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        @endif {{-- /hayDatos --}}

    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         LÓGICA CLIENTE CON ALPINE.JS (CÁLCULOS DERIVADOS REUTILIZABLES)
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

                /**
                 * Ventas filtradas en tiempo real por búsqueda y estado
                 */
                get filteredSales() {
                    const q = this.searchQuery.toLowerCase().trim();
                    const status = this.filterStatus;

                    return this.salesList.filter(item => {
                        // Filtro de búsqueda por folio o cliente
                        const matchesQuery = !q ||
                            (item.folio && item.folio.toLowerCase().includes(q)) ||
                            (item.cliente && item.cliente.toLowerCase().includes(q)) ||
                            (item.id && item.id.toString().includes(q));

                        // Filtro por estado de comisión
                        const matchesStatus = status === 'todos' || (item.comision && item.comision.estado === status);

                        return matchesQuery && matchesStatus;
                    });
                },

                /**
                 * Totales calculados a partir de las ventas filtradas visibles
                 * (Garantiza que la tabla y el resumen coincidan al centavo)
                 */
                get filteredTotals() {
                    let sumMonto = 0;
                    let sumComision = 0;

                    this.filteredSales.forEach(item => {
                        sumMonto += Number(item.monto || 0);
                        // Sumamos la comisión si no está anulada (o el monto registrado)
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

                /**
                 * Resumen derivado global sincronizado
                 */
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
