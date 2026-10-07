<x-app title="Comisiones por Semana | AXStore">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="max-w-[1440px] mx-auto space-y-6">

        <!-- HEADER DEL DASHBOARD -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <i class="fas fa-calendar-week text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600 mb-0.5">Cortes Periódicos</p>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                            Comisiones por Semana
                        </h1>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN RÁPIDA -->
            @if($isAdmin)
            <div class="flex items-center gap-3 flex-wrap">
                <button type="button" onclick="openLiquidarModal()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <i class="fas fa-circle-check"></i>
                    <span>LIQUIDAR PAGO</span>
                </button>
                <button type="button" onclick="openCreateModal()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-lg shadow-blue-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <i class="fas fa-plus"></i>
                    <span>NUEVO BONO</span>
                </button>
            </div>
            @endif
        </div>

        <!-- SUB-NAV / PESTAÑAS (SOLO COMISIONES) -->
        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones de comisiones">
            <a href="{{ route('comisiones.porSemana') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-calendar-week"></i>
                <span>Comisiones por Semana</span>
            </a>
            <a href="{{ route('comisiones.porVendedor') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-users-gear text-slate-400"></i>
                <span>Comisiones por Vendedor</span>
            </a>
            <a href="{{ route('comisiones.index') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-coins text-slate-400"></i>
                <span>Comisiones</span>
            </a>
            <a href="{{ route('comisiones.ajustes') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-arrows-rotate text-slate-400"></i>
                <span>Ajustes</span>
            </a>
        </nav>

        <!-- FILTROS GLOBALES DE SEMANAS -->
        <section class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
            <form method="GET" action="{{ route('comisiones.porSemana') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3.5 items-end">

                {{-- Selector de Año --}}
                <div class="sm:col-span-1 md:col-span-3">
                    <label for="anio" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-calendar text-slate-400 mr-1"></i> Año
                    </label>
                    <select id="anio" name="anio"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                        @for($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" {{ (int)$anio === $y ? 'selected' : '' }}>Año {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                @if($isAdmin)
                {{-- Selector de Vendedor --}}
                <div class="sm:col-span-1 md:col-span-6">
                    <label for="vendedor_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-user-tie text-slate-400 mr-1"></i> Vendedor
                    </label>
                    <select id="vendedor_id" name="vendedor_id" data-searchable-vendedor
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <option value="">Todos los vendedores</option>
                        @foreach($vendedores as $v)
                            <option value="{{ $v->id }}" {{ (string)$vendedorId === (string)$v->id ? 'selected' : '' }}>
                                {{ $v->nombre_real }} ({{ $v->username }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Botones --}}
                <div class="sm:col-span-2 md:col-span-3 flex items-center gap-1.5">
                    <button type="submit"
                            class="w-full rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-2.5 text-xs transition-all shadow-md shadow-blue-600/15 flex items-center justify-center gap-1 cursor-pointer">
                        <i class="fas fa-filter text-[11px]"></i>
                        <span>Filtrar</span>
                    </button>
                    @if($vendedorId || $anio != now()->year)
                    <a href="{{ route('comisiones.porSemana') }}"
                       class="rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-500 p-2 text-xs transition-all flex items-center justify-center"
                       title="Restablecer filtros">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- KPI CARDS GLOBALES DE SEMANAS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" aria-label="Resumen de comisiones semanales">
            <!-- TOTAL GENERADO -->
            <div role="button" tabindex="0" data-weekly-kpi="all" aria-pressed="false" title="Ver todas las semanas y vendedores" class="weekly-kpi-card text-left bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between transition-all hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Total Año {{ $anio }}</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">${{ number_format($kpis['total'], 2) }}</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>{{ $kpis['semanas'] }} semanas con actividad</span>
                    </p>
                </div>
            </div>

            <!-- POR PAGAR (PENDIENTE) -->
            <div role="button" tabindex="0" data-weekly-kpi="pending" aria-pressed="false" title="Filtrar comisiones pendientes y ver su desglose" class="weekly-kpi-card text-left bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between transition-all hover:-translate-y-0.5 hover:border-amber-300 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-amber-500/20">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Por Liquidar</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-amber-700 tracking-tight">${{ number_format($kpis['pendiente'], 2) }}</h3>
                    <p class="text-xs font-semibold text-amber-600 mt-1">
                        @if(($kpis['saldo_en_garantia'] ?? 0) > 0)
                            <span title="${{ number_format($kpis['saldo_en_garantia'], 2) }} en garantía de devolución">
                                ${{ number_format($kpis['saldo_liquidable'], 2) }} liquidable · <i class="fas fa-shield-halved text-[10px]"></i> ${{ number_format($kpis['saldo_en_garantia'], 2) }} en garantía
                            </span>
                        @else
                            <span>Saldo semanal pendiente</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- PAGADAS / LIQUIDADAS -->
            <div role="button" tabindex="0" data-weekly-kpi="paid" aria-pressed="false" title="Filtrar comisiones pagadas y ver su desglose" class="weekly-kpi-card text-left bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between transition-all hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-emerald-500/20">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Total Liquidado</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-emerald-700 tracking-tight">${{ number_format($kpis['pagada'], 2) }}</h3>
                    <p class="text-xs font-semibold text-emerald-600 mt-1">Comisiones semanales pagadas</p>
                </div>
            </div>

            <!-- ESTADO SEMANA ACTUAL -->
            <div role="button" tabindex="0" data-weekly-kpi="current" aria-pressed="false" title="Ir a la semana actual y abrir su desglose" class="weekly-kpi-card text-left bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between transition-all hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-indigo-500/20">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Semana En Curso</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                        <i class="fas fa-stopwatch"></i>
                    </div>
                </div>
                <div>
                    @php
                        $semanaActualData = $reporteSemanas->firstWhere('es_semana_actual', true);
                    @endphp
                    <h3 class="text-3xl font-black text-indigo-700 tracking-tight">
                        ${{ number_format($semanaActualData['total_comisiones'] ?? 0, 2) }}
                    </h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Semana {{ now()->weekOfYear }} en curso</p>
                </div>
            </div>
        </div>

        <!-- BARRA DE BÚSQUEDA Y CONTROL DE SEMANAS (INDEPENDIENTES - TODAS VISIBLES) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex flex-col xl:flex-row xl:items-center justify-between gap-3.5">
            <!-- BUSCADOR RÁPIDO Y SELECTOR DIRECTO -->
            <div class="flex flex-col sm:flex-row items-center gap-2.5 flex-1 max-w-2xl">
                <!-- SELECTOR DIRECTO DE SEMANA (DESPLAZA Y DESPLIEGA SIN OCULTAR NINGUNA) -->
                <div class="relative w-full sm:w-64 flex-shrink-0">
                    <select id="selectorDirectoSemana"
                            onchange="irASemana(this.value)"
                            aria-label="Seleccionar semana para ubicar y desplegar"
                            class="w-full pl-3 pr-8 py-2.5 text-xs font-bold rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all cursor-pointer">
                        <option value="">Ir a semana y desplegar...</option>
                        @foreach($reporteSemanas as $semOpt)
                            <option value="{{ $semOpt['key'] }}">
                                Sem {{ $semOpt['semana_numero'] }} ({{ $semOpt['inicio_semana']->format('d/m') }} - {{ $semOpt['fin_semana']->format('d/m') }}) {{ $semOpt['es_semana_actual'] ? '★ En curso' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- BUSCADOR EN VIVO (UBICA Y DESPLIEGA LA COINCIDENCIA SIN OCULTAR LAS DEMÁS) -->
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-search text-xs"></i>
                    </div>
                    <input type="text"
                           id="buscadorSemanasInput"
                           oninput="buscarYDesplegarSemana(this.value)"
                           placeholder="Buscar semana (ej: 40, Sep, Oct, Ana Plasma...)"
                           class="w-full pl-9 pr-9 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                    <button type="button"
                            id="btnLimpiarBusquedaSemanas"
                            onclick="limpiarBuscadorSemanas()"
                            class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors cursor-pointer"
                            title="Limpiar búsqueda">
                        <i class="fas fa-circle-xmark text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- CONTROLES RÁPIDOS: CONTADOR DE DESPLEGADAS / EXPANDIR / COLAPSAR -->
            <div class="flex items-center gap-2 flex-wrap justify-between xl:justify-end">
                <span id="contadorSemanas" class="text-[11px] font-bold text-slate-500 mr-1" title="Semanas desplegadas de forma independiente">
                    {{ $reporteSemanas->count() }} {{ $reporteSemanas->count() === 1 ? 'semana' : 'semanas' }}
                </span>
                <div class="inline-flex rounded-xl border border-slate-200 bg-slate-50 p-1 gap-1">
                    <button type="button"
                            onclick="expandirTodasSemanas()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold shadow-2xs transition-all cursor-pointer"
                            title="Desplegar todas las semanas">
                        <i class="fas fa-angles-down text-[10px] text-blue-600"></i>
                        <span>Desplegar todas</span>
                    </button>
                    <button type="button"
                            onclick="colapsarTodasSemanas()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-slate-200/60 text-slate-600 text-xs font-bold transition-all cursor-pointer"
                            title="Plegar todas las semanas">
                        <i class="fas fa-angles-up text-[10px] text-slate-400"></i>
                        <span>Plegar todas</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- LISTA DE SEMANAS CON CORTES Y DESGLOSE -->
        <div id="listaSemanas" class="space-y-4 scroll-mt-6" aria-live="polite">
            <div id="filtroKpiAnuncio" class="hidden rounded-xl border border-blue-100 bg-blue-50 px-4 py-2.5 text-xs font-bold text-blue-700 flex items-center justify-between gap-3" role="status">
                <span id="filtroKpiAnuncioTexto"></span>
                <button type="button" onclick="aplicarFiltroKpiSemanal('all')" class="text-[11px] underline font-extrabold hover:text-blue-900 cursor-pointer">
                    Quitar enfoque
                </button>
            </div>

            @forelse($reporteSemanas as $sem)
            @php
                $comisiones = $sem['comisiones'];
                $vendedoresSemana = $sem['vendedores'];
                $hasPendiente = $sem['total_pendiente'] > 0;
                $fechaDesdeSemana = $sem['inicio_semana']->format('Y-m-d');
                $fechaHastaSemana = $sem['fin_semana']->format('Y-m-d');

                $nombresMeses = [
                    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
                ];
                $mesInicio = $nombresMeses[$sem['inicio_semana']->month] ?? '';
                $mesFin = $nombresMeses[$sem['fin_semana']->month] ?? '';
                $vendedoresTexto = strtolower($vendedoresSemana->map(fn($v) => ($v['vendedor']->nombre_real ?? '') . ' ' . ($v['vendedor']->username ?? ''))->implode(' '));
                $searchableText = "semana {$sem['semana_numero']} sem {$sem['semana_numero']} {$sem['semana_numero']} {$sem['year']} {$sem['inicio_semana']->format('d/m/Y')} {$sem['fin_semana']->format('d/m/Y')} {$mesInicio} {$mesFin} {$vendedoresTexto}";
            @endphp
            <div id="semana-{{ $sem['key'] }}"
                 data-week-card
                 data-week-key="{{ $sem['key'] }}"
                 data-week-number="{{ $sem['semana_numero'] }}"
                 data-week-year="{{ $sem['year'] }}"
                 data-week-searchable="{{ $searchableText }}"
                 data-week-current="{{ $sem['es_semana_actual'] ? '1' : '0' }}"
                 data-week-has-pending="{{ $sem['total_pendiente'] > 0 ? '1' : '0' }}"
                 data-week-has-paid="{{ $comisiones->where('estado', 'Pagada')->isNotEmpty() ? '1' : '0' }}"
                 class="bg-white border {{ $sem['es_semana_actual'] ? 'border-blue-400 ring-2 ring-blue-500/10' : 'border-slate-200' }} rounded-2xl shadow-sm overflow-hidden transition-all duration-200 hover:border-slate-300">
                
                <!-- HEADER DE LA TARJETA DE LA SEMANA (INTERACTIVO / CLICKABLE) -->
                <div onclick="toggleSemana('{{ $sem['key'] }}')"
                     role="button"
                     tabindex="0"
                     onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleSemana('{{ $sem['key'] }}');}"
                     class="p-4 sm:p-5 bg-white flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 cursor-pointer select-none hover:bg-slate-50/70 transition-colors group">
                    
                    <!-- INFORMACIÓN DE LA SEMANA -->
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-12 h-12 rounded-2xl {{ $sem['es_semana_actual'] ? 'bg-gradient-to-tr from-blue-600 to-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700' }} font-black text-center flex flex-col items-center justify-center flex-shrink-0 transition-transform group-hover:scale-105">
                            <span class="text-[9px] uppercase tracking-wider leading-none">Sem</span>
                            <span class="text-lg leading-none mt-0.5">{{ $sem['semana_numero'] }}</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight group-hover:text-blue-600 transition-colors">
                                    Semana {{ $sem['semana_numero'] }} <span class="text-slate-400 font-semibold text-sm">({{ $sem['year'] }})</span>
                                </h2>
                                @if($sem['es_semana_actual'])
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-700 border border-blue-200">
                                    Semana Actual
                                </span>
                                @endif
                                @if($sem['total_pendiente'] > 0)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                    Por Liquidar
                                </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 font-semibold mt-0.5 flex items-center gap-1.5 flex-wrap">
                                <i class="fas fa-calendar-week text-slate-400 text-[11px]"></i>
                                <span>{{ $sem['inicio_semana']->format('d/m/Y') }}</span>
                                <span class="text-slate-300">—</span>
                                <span>{{ $sem['fin_semana']->format('d/m/Y') }}</span>
                                <span class="text-slate-300 hidden sm:inline">·</span>
                                <span class="text-slate-400 font-medium text-[11px] hidden sm:inline">
                                    {{ $vendedoresSemana->count() }} {{ $vendedoresSemana->count() === 1 ? 'vendedor' : 'vendedores' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- RESUMEN DE CIFRAS DE LA SEMANA -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 bg-slate-50/90 p-3 rounded-xl border border-slate-100 flex-1 max-w-xl">
                        <div>
                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Total Semana</span>
                            <span class="text-sm sm:text-base font-black text-slate-900">${{ number_format($sem['total_comisiones'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold uppercase tracking-wider text-amber-600 block">Por Liquidar</span>
                            <span class="text-sm sm:text-base font-black text-amber-700">${{ number_format($sem['total_pendiente'], 2) }}</span>
                            @if(($sem['saldo_en_garantia'] ?? 0) > 0)
                                <span class="block text-[10px] font-semibold text-amber-600 truncate" title="${{ number_format($sem['saldo_en_garantia'], 2) }} en período de garantía de devolución">
                                    <i class="fas fa-shield-halved text-[9px]"></i> ${{ number_format($sem['saldo_en_garantia'], 2) }} en garantía
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-600 block">Pagado</span>
                            <span class="text-sm sm:text-base font-black text-emerald-700">${{ number_format($sem['total_pagada'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Ventas</span>
                            <span class="text-sm sm:text-base font-black text-slate-700">{{ $sem['total_ventas'] }}</span>
                        </div>
                    </div>

                    <!-- BOTÓN ACCIÓN DE DESPLIEGUE / COLAPSO -->
                    <div class="flex items-center gap-2 self-end lg:self-center flex-shrink-0" onclick="event.stopPropagation()">
                        <button type="button"
                                onclick="toggleSemana('{{ $sem['key'] }}')"
                                id="btn-toggle-semana-{{ $sem['key'] }}"
                                aria-expanded="{{ $sem['es_semana_actual'] ? 'true' : 'false' }}"
                                class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 text-slate-700 text-xs font-black transition-all cursor-pointer shadow-2xs group-hover:border-blue-300 group-hover:text-blue-600">
                            <span id="btn-text-semana-{{ $sem['key'] }}">{{ $sem['es_semana_actual'] ? 'Plegar' : 'Desplegar' }}</span>
                            <i id="chevron-semana-{{ $sem['key'] }}" class="fas fa-chevron-down text-[11px] transition-transform duration-300 {{ $sem['es_semana_actual'] ? 'rotate-180' : '' }}"></i>
                        </button>
                    </div>

                </div>

                <!-- CUERPO DE LA SEMANA (COLAPSABLE / DESPLEGABLE) -->
                <div id="semana-body-{{ $sem['key'] }}" class="{{ $sem['es_semana_actual'] ? '' : 'hidden' }} bg-slate-50/40 p-4 sm:p-5 border-t border-slate-100 space-y-4 transition-all">
                    
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                                    <i class="fas fa-users text-slate-400"></i>
                                    <span>Vendedores en esta semana</span>
                                    <span class="px-1.5 py-0.2 rounded-md bg-slate-200/70 text-slate-600 text-[10px] font-black">{{ $vendedoresSemana->count() }}</span>
                                </h4>
                                
                            </div>

                            <div class="relative w-full sm:w-64">
                                    <input type="text"
                                           placeholder="Buscar vendedor..."
                                           oninput="buscarEnCarruselSemana('{{ $sem['key'] }}', this.value)"
                                           class="w-full pl-9 pr-3 py-2 text-xs font-medium rounded-lg border border-slate-200 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                            </div>
                        </div>

                        @if($vendedoresSemana->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($vendedoresSemana as $vItem)
                                @if($vItem['vendedor'])
                                @php
                                    $vId = $vItem['vendedor']->id;
                                    $vNombre = $vItem['vendedor']->nombre_real ?: $vItem['vendedor']->username;
                                    $vUsername = $vItem['vendedor']->username ?? '';
                                    $detalleId = 'desglose-' . $sem['key'] . '-' . $vId;
                                @endphp
                                <div data-vendedor-card="{{ $vId }}"
                                     data-vendedor-has-pending="{{ $vItem['pendiente'] > 0 ? '1' : '0' }}"
                                     data-vendedor-has-paid="{{ $vItem['comisiones']->where('estado', 'Pagada')->isNotEmpty() ? '1' : '0' }}"
                                     data-vendedor-nombre="{{ strtolower($vNombre) }}"
                                     data-vendedor-username="{{ strtolower($vUsername) }}"
                                     class="vendedor-card-{{ $sem['key'] }} space-y-2">
                                  <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs grid grid-cols-1 lg:grid-cols-[minmax(210px,1.2fr)_minmax(340px,2fr)_auto] items-center gap-4 transition-all duration-200">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm font-black flex-shrink-0 shadow-sm">
                                            {{ strtoupper(substr($vNombre, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-black text-sm text-slate-900 truncate">{{ $vNombre }}</p>
                                            <p class="text-[11px] text-slate-500 truncate">{{ $vUsername }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 rounded-xl border border-slate-100 px-3.5 py-3">
                                        <div>
                                            <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Ganado semana</span>
                                            <span class="text-sm font-black text-slate-900">${{ number_format($vItem['total'], 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[9px] font-black uppercase tracking-wider text-amber-600">Por liquidar</span>
                                            <span class="text-sm font-black text-amber-700">${{ number_format($vItem['pendiente'], 2) }}</span>
                                            @if(($vItem['saldo_en_garantia'] ?? 0) > 0)
                                                <span class="block text-[10px] font-semibold text-amber-600" title="En período de garantía de devolución">
                                                    <i class="fas fa-shield-halved text-[9px]"></i> ${{ number_format($vItem['saldo_en_garantia'], 2) }} en garantía
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="block text-[9px] font-black uppercase tracking-wider text-emerald-600">Pagado</span>
                                            <span class="text-sm font-black text-emerald-700">${{ number_format($vItem['pagada'], 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Ventas</span>
                                            <span class="text-sm font-black text-slate-700">{{ $vItem['ventas'] }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                        @if($isAdmin)
                                            @php
                                                $montoLiquidable = $vItem['saldo_liquidable'] ?? 0;
                                                $montoGarantia   = $vItem['saldo_en_garantia'] ?? 0;
                                                $montoHistorico  = $vItem['saldo_liquidable_total'] ?? 0;
                                            @endphp

                                            @if($montoLiquidable > 0)
                                                <button type="button"
                                                        onclick="abrirLiquidacionSemanal({{ $vId }}, '{{ addslashes($vNombre) }}', '{{ addslashes($vUsername) }}', '{{ $fechaDesdeSemana }}', '{{ $fechaHastaSemana }}')"
                                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-extrabold transition-colors cursor-pointer whitespace-nowrap shadow-xs"
                                                        title="Liquidar comisiones elegibles para este vendedor en esta semana">
                                                    <i class="fas fa-circle-check"></i>
                                                    <span>Liquidar (${{ number_format($montoLiquidable, 2) }})</span>
                                                </button>
                                            @elseif($montoGarantia > 0)
                                                <button type="button"
                                                        onclick="abrirLiquidacionSemanal({{ $vId }}, '{{ addslashes($vNombre) }}', '{{ addslashes($vUsername) }}', '{{ $fechaDesdeSemana }}', '{{ $fechaHastaSemana }}')"
                                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 text-[11px] font-extrabold transition-colors cursor-pointer whitespace-nowrap shadow-xs"
                                                        title="Comisiones en período de garantía de devolución (aún no liquidables)">
                                                    <i class="fas fa-shield-halved text-amber-600"></i>
                                                    <span>En garantía (${{ number_format($montoGarantia, 2) }})</span>
                                                </button>
                                            @endif

                                            @if($montoLiquidable <= 0 && $montoHistorico > 0)
                                                <button type="button"
                                                        onclick="abrirLiquidacionSemanal({{ $vId }}, '{{ addslashes($vNombre) }}', '{{ addslashes($vUsername) }}', '', '')"
                                                        class="inline-flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition-colors cursor-pointer whitespace-nowrap"
                                                        title="Ver y liquidar comisiones pendientes disponibles de otros periodos">
                                                    <i class="fas fa-clock-rotate-left text-slate-500"></i>
                                                    <span>Histórico (${{ number_format($montoHistorico, 2) }})</span>
                                                </button>
                                            @endif
                                        @endif

                                        <button type="button"
                                                onclick="toggleDesgloseVendedor('{{ $detalleId }}')"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition-colors cursor-pointer whitespace-nowrap">
                                            <i class="fas fa-list-ul text-slate-500"></i>
                                            <span>Desglose ({{ $vItem['cantidad'] }})</span>
                                            <i id="icon-{{ $detalleId }}" class="fas fa-chevron-down text-[9px] transition-transform"></i>
                                        </button>
                                    </div>
                                  </div>

                                  <div id="{{ $detalleId }}" class="hidden overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-2xs">
                                    <table class="w-full min-w-[850px] text-xs">
                                      <thead>
                                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-bold">
                                          <th class="px-4 py-3 text-left">Fecha y Hora</th>
                                          <th class="px-4 py-3 text-left">Concepto / Venta</th>
                                          <th class="px-4 py-3 text-right">Comisión</th>
                                          <th class="px-4 py-3 text-center">Estado</th>
                                          <th class="px-4 py-3 text-left">Detalle de Pago</th>
                                          <th class="px-4 py-3 text-left">Notas</th>
                                          @if($isAdmin)<th class="px-4 py-3 text-center">Acciones</th>@endif
                                        </tr>
                                      </thead>
                                      <tbody class="divide-y divide-slate-100">
                                        @foreach($vItem['comisiones'] as $c)
                                          <tr data-comision-estado="{{ strtolower($c->estado) }}" data-comision-pendiente="{{ $c->estado === 'Pendiente' ? '1' : '0' }}" class="hover:bg-slate-50/60 transition-colors">
                                            <td class="px-4 py-3 whitespace-nowrap text-slate-600">
                                              <span class="font-bold text-slate-800 block">{{ $c->fecha_registro ? $c->fecha_registro->format('d/m/Y') : '—' }}</span>
                                              <span class="text-[10px] text-slate-400">{{ $c->fecha_registro ? $c->fecha_registro->format('h:i A') : '' }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-slate-700">
                                              <div class="flex items-center gap-1.5 flex-wrap">
                                                @if($c->id_salida)
                                                  <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md text-[10px] font-bold"><i class="fas fa-shopping-bag"></i> Salida #{{ $c->id_salida }}</span>
                                                @else
                                                  <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md text-[10px] font-bold"><i class="fas fa-award"></i> Bono</span>
                                                @endif
                                                <span class="font-semibold text-slate-800">{{ $c->concepto ?? ($c->salida ? "Venta ({$c->salida->cantidad} uds)" : "Comisión") }}</span>
                                              </div>
                                            </td>
                                            <td class="px-4 py-3 text-right whitespace-nowrap"><span class="font-black text-sm text-slate-900">${{ number_format($c->monto, 2) }}</span></td>
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                              @if($c->estado === 'Pendiente')
                                                @if($c->es_liquidacion_bloqueada)
                                                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200" title="En período de garantía de devolución"><i class="fas fa-shield-halved text-[9px] text-amber-600"></i>Garantía</span>
                                                @else
                                                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Lista para liquidar"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Por liquidar</span>
                                                @endif
                                              @elseif($c->estado === 'Pagada')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fas fa-check text-[9px]"></i>Pagada</span>
                                              @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200"><i class="fas fa-ban text-[9px]"></i>Cancelada</span>
                                              @endif
                                            </td>
                                            <td class="px-4 py-3 text-slate-600 text-[11px]">
                                              @if($c->estado === 'Pagada')
                                                <div class="space-y-1">
                                                  <span class="font-bold text-emerald-700 block">{{ $c->metodo_pago ?? 'Efectivo' }}</span>
                                                  @if($c->referencia_pago)<span class="text-[10px] text-slate-500 font-mono block">Ref: {{ $c->referencia_pago }}</span>@endif
                                                  @if($c->comprobante_url)<a href="{{ $c->comprobante_url }}" target="_blank" class="text-indigo-700 font-bold hover:underline">Ver comprobante</a>@endif
                                                </div>
                                              @else
                                                @if($c->es_liquidacion_bloqueada)
                                                  <span class="text-amber-600 font-medium text-[10px]"><i class="fas fa-clock mr-0.5"></i> En garantía</span>
                                                @else
                                                  <span class="text-slate-400 italic">Por liquidar</span>
                                                @endif
                                              @endif
                                            </td>
                                            <td class="px-4 py-3 text-slate-500 text-[11px] max-w-[150px] truncate" title="{{ $c->notas }}">{{ $c->notas ?? '—' }}</td>
                                            @if($isAdmin)
                                              <td class="px-4 py-3 text-center whitespace-nowrap">
                                                <div class="flex items-center justify-center gap-1">
                                                  <button type="button" onclick="verDetalleComision({{ $c->id }})" class="w-7 h-7 rounded-lg flex items-center justify-center text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 cursor-pointer" title="Ver Detalle Completo"><i class="fas fa-eye text-[10px]"></i></button>
                                                  @if($c->estado === 'Pendiente')
                                                    @if($c->es_liquidacion_bloqueada)
                                                      <span class="w-7 h-7 rounded-lg flex items-center justify-center text-amber-500 bg-amber-50" title="En período de garantía (no liquidable aún)"><i class="fas fa-shield-halved text-[10px]"></i></span>
                                                    @else
                                                      <button type="button" onclick="abrirLiquidacionSemanal({{ $vId }}, '{{ addslashes($vNombre) }}', '{{ addslashes($vUsername) }}', '{{ $fechaDesdeSemana }}', '{{ $fechaHastaSemana }}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-emerald-600 hover:bg-emerald-50 cursor-pointer" title="Liquidar"><i class="fas fa-hand-holding-dollar text-[10px]"></i></button>
                                                    @endif
                                                    <button type="button" onclick="openEditModal({{ $c->id }}, '{{ $c->monto }}', '{{ addslashes($c->concepto ?? '') }}', '{{ addslashes($c->notas ?? '') }}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-blue-600 hover:bg-blue-50 cursor-pointer" title="Editar"><i class="fas fa-pen text-[10px]"></i></button>
                                                    <button type="button" onclick="cancelarComision({{ $c->id }})" class="w-7 h-7 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-50 cursor-pointer" title="Cancelar"><i class="fas fa-ban text-[10px]"></i></button>
                                                  @endif
                                                </div>
                                              </td>
                                            @endif
                                          </tr>
                                        @endforeach
                                      </tbody>
                                    </table>
                                  </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            <div id="carrusel-empty-{{ $sem['key'] }}" class="hidden py-4 text-center text-xs text-slate-400">
                                No se encontró ningún vendedor con ese nombre.
                            </div>
                        @else
                            <div class="rounded-xl border border-dashed border-slate-300 bg-white py-6 text-center text-xs text-slate-400">
                                No hay vendedores con comisiones registradas en esta semana.
                            </div>
                        @endif

                </div>

            </div>
            @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400 shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl mx-auto mb-3 text-slate-400">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h3 class="text-base font-bold text-slate-700">No hay semanas registradas</h3>
                <p class="text-xs text-slate-400 mt-1">Selecciona otro año o realiza ventas.</p>
            </div>
            @endforelse
        </div>


    </div>

    {{-- MODALES --}}
    @include('comisiones.modals')

    {{-- SCRIPTS --}}
    <script>
    const ROUTES = {
        index:      "{{ route('comisiones.index') }}",
        pendientes: "{{ url('comisiones/pendientes') }}",
        store:      @if($isAdmin) "{{ route('comisiones.store') }}" @else "null" @endif,
        update:     @if($isAdmin) "{{ url('comisiones') }}" @else "null" @endif,
        liquidar:   @if($isAdmin) "{{ route('comisiones.liquidar') }}" @else "null" @endif,
        cancelar:   @if($isAdmin) "{{ url('comisiones') }}" @else "null" @endif,
    };
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let filtroKpiSemanal = 'all';

    // ── Funciones de Control y Despliegue Independiente de Semanas ──

    function toggleSemana(semanaKey, forceState = null) {
        const body = document.getElementById(`semana-body-${semanaKey}`);
        const chevron = document.getElementById(`chevron-semana-${semanaKey}`);
        const btnText = document.getElementById(`btn-text-semana-${semanaKey}`);
        const btnToggle = document.getElementById(`btn-toggle-semana-${semanaKey}`);
        if (!body) return;

        // Cada semana es 100% independiente: desplegar o replegar una jamás afecta o cierra otra
        const willOpen = forceState !== null ? forceState : body.classList.contains('hidden');

        if (willOpen) {
            body.classList.remove('hidden');
            chevron?.classList.add('rotate-180');
            if (btnText) btnText.textContent = 'Plegar';
            btnToggle?.setAttribute('aria-expanded', 'true');
        } else {
            body.classList.add('hidden');
            chevron?.classList.remove('rotate-180');
            if (btnText) btnText.textContent = 'Desplegar';
            btnToggle?.setAttribute('aria-expanded', 'false');
        }

        actualizarContadorSemanas();
    }

    function actualizarContadorSemanas() {
        const cards = document.querySelectorAll('[data-week-card]');
        const total = cards.length;
        const abiertas = document.querySelectorAll('[data-week-card] [id^="semana-body-"]:not(.hidden)').length;
        const contador = document.getElementById('contadorSemanas');
        if (contador) {
            contador.textContent = `${abiertas} de ${total} ${total === 1 ? 'semana desplegada' : 'semanas desplegadas'}`;
        }
    }

    function irASemana(semanaKey) {
        if (!semanaKey) return;
        const card = document.getElementById(`semana-${semanaKey}`);
        if (!card) return;

        // Asegurar que la semana esté visible
        card.classList.remove('hidden');

        // Desplegar esta semana exclusivamente, de forma independiente
        toggleSemana(semanaKey, true);

        // Desplazamiento suave al centro de la pantalla
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Resaltar con destello visual temporal y estilo premium
        card.classList.remove('semana-highlight-search', 'semana-flash-anim');
        void card.offsetWidth; // forzar reflow para reiniciar la animación
        card.classList.add('semana-highlight-search', 'semana-flash-anim');
        setTimeout(() => {
            card.classList.remove('semana-highlight-search', 'semana-flash-anim');
        }, 2400);

        const sel = document.getElementById('selectorDirectoSemana');
        if (sel) sel.value = semanaKey;
    }

    function buscarYDesplegarSemana(query) {
        const q = (query || '').trim().toLowerCase();
        const btnLimpiar = document.getElementById('btnLimpiarBusquedaSemanas');
        if (btnLimpiar) btnLimpiar.classList.toggle('hidden', q === '');

        const cards = Array.from(document.querySelectorAll('[data-week-card]'));

        if (!q) {
            cards.forEach(card => {
                card.classList.remove('semana-highlight-search', 'semana-flash-anim');
            });
            actualizarContadorSemanas();
            return;
        }

        let primerMatch = null;
        let coincidencias = 0;

        cards.forEach(card => {
            // NUNCA OCULTAR LAS SEMANAS: TODAS PERMANECEN VISIBLES EN LA LISTA
            card.classList.remove('hidden');

            const searchable = (card.dataset.weekSearchable || '').toLowerCase();
            const coincide = searchable.includes(q);

            if (coincide) {
                coincidencias++;
                card.classList.add('semana-highlight-search');
                // Desplegar la semana coincidente de forma independiente
                toggleSemana(card.dataset.weekKey, true);
                if (!primerMatch) primerMatch = card;
            } else {
                card.classList.remove('semana-highlight-search', 'semana-flash-anim');
            }
        });

        if (primerMatch) {
            primerMatch.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        const contador = document.getElementById('contadorSemanas');
        if (contador) {
            contador.textContent = `${coincidencias} coincidencia${coincidencias !== 1 ? 's' : ''} (todas visibles)`;
        }
    }

    function limpiarBuscadorSemanas() {
        const input = document.getElementById('buscadorSemanasInput');
        if (input) {
            input.value = '';
            buscarYDesplegarSemana('');
            input.focus();
        }
    }

    function expandirTodasSemanas() {
        document.querySelectorAll('[data-week-card]').forEach(card => {
            toggleSemana(card.dataset.weekKey, true);
        });
    }

    function colapsarTodasSemanas() {
        document.querySelectorAll('[data-week-card]').forEach(card => {
            toggleSemana(card.dataset.weekKey, false);
        });
    }

    function toggleDesgloseVendedor(detalleId) {
        const desglose = document.getElementById(detalleId);
        const chevron = document.getElementById(`icon-${detalleId}`);
        if (!desglose) return;

        desglose.classList.toggle('hidden');
        if (chevron) chevron.classList.toggle('rotate-180');
    }

    function abrirLiquidacionSemanal(vendedorId, nombreVendedor, usernameVendedor = '', fechaDesde = '', fechaHasta = '') {
        if (!vendedorId) return;
        if (typeof openLiquidarModal === 'function') {
            openLiquidarModal(vendedorId, nombreVendedor, usernameVendedor, fechaDesde, fechaHasta, true);
        }
    }

    function formatNum(n) {
        return parseFloat(n).toLocaleString('es-SV', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function buscarEnCarruselSemana(semanaKey, query) {
        const q = query.trim().toLowerCase();
        const cards = document.querySelectorAll(`.vendedor-card-${semanaKey}`);
        let visibles = 0;
        cards.forEach(card => {
            const nombre = card.dataset.vendedorNombre || '';
            const username = card.dataset.vendedorUsername || '';
            const coincideNombre = !q || nombre.includes(q) || username.includes(q);
            const coincideFiltro = filtroKpiSemanal === 'all'
                || filtroKpiSemanal === 'current'
                || card.dataset.vendedorHasPending === '1' && filtroKpiSemanal === 'pending'
                || card.dataset.vendedorHasPaid === '1' && filtroKpiSemanal === 'paid';
            if (coincideNombre && coincideFiltro) {
                card.classList.remove('hidden');
                visibles++;
            } else {
                card.classList.add('hidden');
            }
        });

        const noMatchesEl = document.getElementById(`carrusel-empty-${semanaKey}`);
        if (noMatchesEl) {
            if (visibles === 0) {
                noMatchesEl.classList.remove('hidden');
            } else {
                noMatchesEl.classList.add('hidden');
            }
        }
    }

    function aplicarFiltroKpiSemanal(filtro) {
        const listaSemanas = document.getElementById('listaSemanas');
        const aviso = document.getElementById('filtroKpiAnuncio');
        const avisoTexto = document.getElementById('filtroKpiAnuncioTexto');
        const tarjetasKpi = document.querySelectorAll('[data-weekly-kpi]');
        const semanaActual = document.querySelector('[data-week-card][data-week-current="1"]');

        if (filtro === 'current' && !semanaActual && Number(document.getElementById('anio')?.value) !== new Date().getFullYear()) {
            const selectorAnio = document.getElementById('anio');
            selectorAnio.value = new Date().getFullYear();
            selectorAnio.form.requestSubmit();
            return;
        }

        filtroKpiSemanal = filtroKpiSemanal === filtro && filtro !== 'all' ? 'all' : filtro;

        // Limpiar resaltes previos de KPI pero NUNCA OCULTAR SEMANAS
        const cards = document.querySelectorAll('[data-week-card]');
        cards.forEach(card => {
            card.classList.remove('hidden');
            card.classList.remove('semana-highlight-search', 'semana-highlight-pending', 'semana-highlight-paid', 'semana-highlight-current', 'semana-flash-anim');
        });

        let primeraSemanaMatch = null;

        if (filtroKpiSemanal === 'current') {
            if (semanaActual) {
                toggleSemana(semanaActual.dataset.weekKey, true);
                semanaActual.classList.add('semana-highlight-current', 'semana-flash-anim');
                primeraSemanaMatch = semanaActual;
            }
        } else if (filtroKpiSemanal === 'pending') {
            cards.forEach(card => {
                if (card.dataset.weekHasPending === '1') {
                    toggleSemana(card.dataset.weekKey, true);
                    card.classList.add('semana-highlight-pending', 'semana-flash-anim');
                    if (!primeraSemanaMatch) primeraSemanaMatch = card;
                }
            });
        } else if (filtroKpiSemanal === 'paid') {
            cards.forEach(card => {
                if (card.dataset.weekHasPaid === '1') {
                    toggleSemana(card.dataset.weekKey, true);
                    card.classList.add('semana-highlight-paid', 'semana-flash-anim');
                    if (!primeraSemanaMatch) primeraSemanaMatch = card;
                }
            });
        }

        // Actualizar estados visuales de las tarjetas KPI
        tarjetasKpi.forEach((tarjeta) => {
            const activa = tarjeta.dataset.weeklyKpi === filtroKpiSemanal;
            tarjeta.setAttribute('aria-pressed', activa ? 'true' : 'false');
            tarjeta.classList.toggle('ring-2', activa);
            tarjeta.classList.toggle('ring-blue-300', activa && tarjeta.dataset.weeklyKpi === 'all');
            tarjeta.classList.toggle('ring-amber-300', activa && tarjeta.dataset.weeklyKpi === 'pending');
            tarjeta.classList.toggle('ring-emerald-300', activa && tarjeta.dataset.weeklyKpi === 'paid');
            tarjeta.classList.toggle('ring-indigo-300', activa && tarjeta.dataset.weeklyKpi === 'current');
        });

        const descripciones = {
            all: '',
            pending: 'Enfocando y desplegando semanas con saldo por liquidar. Las demás semanas permanecen visibles e independientes abajo.',
            paid: 'Enfocando y desplegando semanas con comisiones pagadas. Las demás semanas permanecen visibles e independientes abajo.',
            current: 'Enfocando y desplegando la semana en curso. Las demás semanas permanecen visibles e independientes abajo.'
        };

        if (filtroKpiSemanal !== 'all' && descripciones[filtroKpiSemanal]) {
            if (avisoTexto) avisoTexto.textContent = descripciones[filtroKpiSemanal];
            aviso?.classList.remove('hidden');
            if (primeraSemanaMatch) {
                primeraSemanaMatch.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            aviso?.classList.add('hidden');
        }

        actualizarContadorSemanas();
    }

    document.querySelectorAll('[data-weekly-kpi]').forEach((tarjeta) => {
        const activate = () => aplicarFiltroKpiSemanal(tarjeta.dataset.weeklyKpi);
        tarjeta.addEventListener('click', activate);
        tarjeta.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                activate();
            }
        });
    });

    const filtroVendedorSemana = document.querySelector('select[data-searchable-vendedor]');
    filtroVendedorSemana?.addEventListener('change', () => filtroVendedorSemana.form.requestSubmit());

    // Inicializar contador de semanas desplegadas
    actualizarContadorSemanas();

    // ── Cargar pendientes dinámicos al seleccionar vendedor para liquidar ──

    async function cargarPendientesVendedor(vendedorId) {
        const resumenBox = document.getElementById('liquidarResumenBox');
        const emptyBox   = document.getElementById('liquidarEmptyBox');
        const loadingBox = document.getElementById('liquidarLoadingBox');
        const lista      = document.getElementById('liquidarDetalleLista');
        const btnSubmit  = document.getElementById('btnConfirmLiquidar');

        resumenBox.classList.add('hidden');
        emptyBox.classList.add('hidden');
        lista.classList.add('hidden');
        lista.innerHTML = '';

        if (!vendedorId) return;

        loadingBox.classList.remove('hidden');

        try {
            const params = new URLSearchParams();
            const fechaDesde = document.getElementById('liquidarFechaDesde')?.value;
            const fechaHasta = document.getElementById('liquidarFechaHasta')?.value;
            if (fechaDesde && fechaHasta) {
                params.set('fecha_desde', fechaDesde);
                params.set('fecha_hasta', fechaHasta);
            }
            const queryString = params.toString();
            const res = await fetch(`${ROUTES.pendientes}/${vendedorId}${queryString ? `?${queryString}` : ''}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            loadingBox.classList.add('hidden');

            if (data.success && (data.cantidad > 0 || (data.comisiones && data.comisiones.length > 0))) {
                const totalLiquidable = Number(data.total || 0);
                const totalEl = document.getElementById('liquidarTotalMonto');
                if (totalLiquidable < 0) {
                    totalEl.textContent = '-$' + formatNum(Math.abs(totalLiquidable));
                    totalEl.className = 'text-2xl font-black text-rose-600 mt-0.5';
                } else if (totalLiquidable === 0 && data.cantidad > 0) {
                    totalEl.textContent = '$0.00';
                    totalEl.className = 'text-2xl font-black text-indigo-600 mt-0.5';
                } else {
                    totalEl.textContent = '$' + formatNum(totalLiquidable);
                    totalEl.className = 'text-2xl font-black text-emerald-700 mt-0.5';
                }

                const countTexto = data.cantidad_bloqueada > 0
                    ? `${data.cantidad} liquidable(s) · ${data.cantidad_bloqueada} en garantía`
                    : `${data.cantidad} comisiones pendientes`;
                document.getElementById('liquidarCountBadge').textContent = countTexto;

                let html = '';
                if (data.cantidad_bloqueada > 0) {
                    html += `
                    <div class="p-2.5 mb-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px] space-y-1.5">
                        <div class="flex items-center gap-1.5 font-bold">
                            <i class="fas fa-shield-halved text-amber-600 flex-shrink-0"></i>
                            <span>${data.cantidad_bloqueada} comisión(es) ($${formatNum(data.total_bloqueado)}) están en garantía de devolución y no pueden ser liquidadas aún.</span>
                        </div>
                        ${fechaDesde && fechaHasta ? `
                        <div class="pt-1.5 border-t border-amber-200/60 flex items-center justify-between gap-2 flex-wrap">
                            <span class="text-amber-800 text-[10px]">¿Deseas ver pendientes acumuladas de todos los periodos?</span>
                            <button type="button" onclick="quitarFiltroFechasLiquidar()" class="px-2.5 py-1 rounded-lg bg-amber-200 hover:bg-amber-300 text-amber-950 font-black text-[10px] cursor-pointer transition-colors shadow-2xs">
                                <i class="fas fa-filter-circle-xmark mr-1"></i> Quitar filtro de semana
                            </button>
                        </div>
                        ` : ''}
                    </div>
                    `;
                }

                data.comisiones.forEach(c => {
                    const monto = Number(c.monto);
                    const esNegativo = monto < 0;
                    const esBloqueada = Boolean(c.bloqueada_garantia);
                    const montoTexto = esNegativo ? '-$' + formatNum(Math.abs(monto)) : '$' + formatNum(monto);

                    if (esBloqueada) {
                        html += `
                        <div class="flex items-center justify-between p-2.5 bg-amber-50/70 rounded-xl border border-amber-200/90 text-xs">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center flex-shrink-0 text-[10px]" title="En período de garantía de devolución">
                                    <i class="fas fa-shield-halved"></i>
                                </span>
                                <div class="truncate">
                                    <span class="truncate font-semibold text-slate-800 block">${c.concepto}</span>
                                    <span class="text-[10px] font-bold text-amber-800 flex items-center gap-1">
                                        ${c.motivo_bloqueo || 'Período de garantía activo'} · No liquidable
                                    </span>
                                </div>
                            </div>
                            <span class="font-extrabold text-amber-800 ml-2 flex-shrink-0" title="En garantía">${montoTexto}</span>
                        </div>
                        `;
                    } else {
                        html += `
                        <label class="flex items-center justify-between p-2 bg-white rounded-lg border ${esNegativo ? 'border-rose-200 bg-rose-50/40 hover:bg-rose-50/70' : 'border-emerald-100 hover:bg-emerald-50/50'} cursor-pointer text-xs transition-colors">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <input type="checkbox" name="comisiones_ids[]" value="${c.id}" checked onchange="recalcularTotalSeleccionado()" class="rounded ${esNegativo ? 'border-rose-300 text-rose-600 focus:ring-rose-500' : 'border-emerald-300 text-emerald-600 focus:ring-emerald-500'} cursor-pointer">
                                <span class="truncate font-medium text-slate-800">${c.concepto}${esNegativo ? ' · Deducción' : ''}</span>
                            </div>
                            <span class="font-extrabold ${esNegativo ? 'text-rose-700' : 'text-emerald-700'} ml-2 flex-shrink-0" data-monto="${monto}">${montoTexto}</span>
                        </label>
                        `;
                    }
                });
                lista.innerHTML = html;
                resumenBox.classList.remove('hidden');

                if (typeof recalcularTotalSeleccionado === 'function') {
                    recalcularTotalSeleccionado();
                }
            } else {
                emptyBox.classList.remove('hidden');
                if (btnSubmit) btnSubmit.disabled = true;
            }
        } catch (e) {
            loadingBox.classList.add('hidden');
            console.error(e);
        }
    }

    function toggleDetallePendientes() {
        const lista = document.getElementById('liquidarDetalleLista');
        lista.classList.toggle('hidden');
    }

    function recalcularTotalSeleccionado() {
        const checkboxes = document.querySelectorAll('input[name="comisiones_ids[]"]:checked');
        let total = 0;
        checkboxes.forEach(cb => {
            const montoEl = cb.closest('label')?.querySelector('[data-monto]');
            if (montoEl) {
                total += parseFloat(montoEl.dataset.monto || 0);
            }
        });

        total = Math.round(total * 100) / 100;
        const totalEl = document.getElementById('liquidarTotalMonto');
        const btnSubmit = document.getElementById('btnConfirmLiquidar');
        const metodoSelect = document.getElementById('liquidarMetodoPago');
        const optCompensacion = document.getElementById('optCompensacionSaldo');

        if (total < 0) {
            totalEl.textContent = '-$' + formatNum(Math.abs(total));
            totalEl.className = 'text-2xl font-black text-rose-600 mt-0.5';
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.title = 'El saldo neto no puede ser negativo.';
            }
            if (optCompensacion) optCompensacion.classList.add('hidden');
        } else if (total === 0) {
            totalEl.textContent = '$0.00';
            totalEl.className = 'text-2xl font-black text-indigo-600 mt-0.5';
            if (checkboxes.length > 0) {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.title = 'Liquidación por compensación mutua de saldos ($0.00)';
                }
                if (optCompensacion) {
                    optCompensacion.classList.remove('hidden');
                    optCompensacion.selected = true;
                    if (typeof handleMetodoPagoLiquidar === 'function') {
                        handleMetodoPagoLiquidar('Compensación de Saldo');
                    }
                }
            } else {
                if (btnSubmit) btnSubmit.disabled = true;
            }
        } else {
            totalEl.textContent = '$' + formatNum(total);
            totalEl.className = 'text-2xl font-black text-emerald-700 mt-0.5';
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.title = '';
            }
            if (optCompensacion) {
                optCompensacion.classList.add('hidden');
                if (metodoSelect && metodoSelect.value === 'Compensación de Saldo') {
                    metodoSelect.value = 'Efectivo';
                    if (typeof handleMetodoPagoLiquidar === 'function') {
                        handleMetodoPagoLiquidar('Efectivo');
                    }
                }
            }
        }

        document.getElementById('liquidarCountBadge').textContent = `${checkboxes.length} seleccionada${checkboxes.length !== 1 ? 's' : ''}`;
    }

    // ── Editar comisión (admin) ───────────────────────────────────────────

    function openEditModal(id, monto, concepto, notas) {
        document.getElementById('editComisionId').value = id;
        document.getElementById('editMonto').value      = monto;
        document.getElementById('editConcepto').value   = concepto || '';
        document.getElementById('editNotas').value      = notas || '';
        document.getElementById('modalEdit').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modalEdit').classList.add('hidden');
    }

    document.getElementById('formEdit')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('editComisionId').value;
        const body = {
            monto:    document.getElementById('editMonto').value,
            concepto: document.getElementById('editConcepto').value,
            notas:    document.getElementById('editNotas').value,
            _method:  'PUT',
        };

        try {
            const res = await fetch(`${ROUTES.update}/${id}`, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body:    JSON.stringify(body),
            });
            const data = await res.json();
            if (data.success) {
                closeEditModal();
                Swal.fire({ icon: 'success', title: '¡Actualizado!', text: data.message, timer: 2000, showConfirmButton: false });
                setTimeout(() => window.location.reload(), 1200);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            }
        } catch(e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo actualizar la comisión.' });
        }
    });

    // ── Cancelar comisión (admin) ─────────────────────────────────────────

    async function cancelarComision(id) {
        const result = await Swal.fire({
            icon: 'warning',
            title: '¿Cancelar comisión?',
            text: 'Esta acción cambiará el estado a Cancelada. No se puede revertir.',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No',
            confirmButtonColor: '#ef4444',
        });

        if (!result.isConfirmed) return;

        try {
            const res = await fetch(`${ROUTES.cancelar}/${id}/cancelar`, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body:    JSON.stringify({ _method: 'PATCH' }),
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire({ icon: 'success', title: '¡Cancelada!', text: data.message, timer: 2000, showConfirmButton: false });
                setTimeout(() => window.location.reload(), 1200);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            }
        } catch(e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cancelar la comisión.' });
        }
    }

    // ── Liquidar con Método de Pago (admin) ────────────────────────────────

    function openLiquidarModal(vendedorId = null, nombreVendedor = '', usernameVendedor = '', fechaDesde = '', fechaHasta = '', bloquear = false) {
        const modal = document.getElementById('modalLiquidar');
        const form = document.getElementById('formLiquidar');
        if (!modal || !form) return;

        modal.classList.remove('hidden');
        form.reset();
        window._vendedorLiquidarBloqueado = false;
        document.getElementById('liquidarFechaDesde').value = fechaDesde;
        document.getElementById('liquidarFechaHasta').value = fechaHasta;
        const alcance = document.getElementById('liquidarAlcance');
        if (alcance) {
            if (fechaDesde && fechaHasta) {
                alcance.textContent = `Pago limitado del ${fechaDesde} al ${fechaHasta}, inclusive.`;
                alcance.classList.remove('hidden');
            } else {
                alcance.textContent = '';
                alcance.classList.add('hidden');
            }
        }

        if (typeof deseleccionarVendedorLiquidar === 'function') {
            deseleccionarVendedorLiquidar();
        }
        if (typeof handleMetodoPagoLiquidar === 'function') {
            handleMetodoPagoLiquidar('Efectivo');
        }

        if (vendedorId && typeof seleccionarVendedorLiquidar === 'function') {
            const initials = (nombreVendedor || usernameVendedor || 'VN').substring(0, 2).toUpperCase();
            seleccionarVendedorLiquidar(vendedorId, nombreVendedor || usernameVendedor, usernameVendedor, initials, Boolean(bloquear));
        }
    }

    function quitarFiltroFechasLiquidar() {
        const inputDesde = document.getElementById('liquidarFechaDesde');
        const inputHasta = document.getElementById('liquidarFechaHasta');
        if (inputDesde) inputDesde.value = '';
        if (inputHasta) inputHasta.value = '';
        const alcance = document.getElementById('liquidarAlcance');
        if (alcance) {
            alcance.textContent = '';
            alcance.classList.add('hidden');
        }
        const vendedorId = document.getElementById('liquidarVendedor')?.value;
        if (vendedorId) {
            cargarPendientesVendedor(vendedorId);
        }
    }

    window.openLiquidarModal = openLiquidarModal;
    window.closeLiquidarModal = closeLiquidarModal;
    window.abrirLiquidacionSemanal = abrirLiquidacionSemanal;
    window.quitarFiltroFechasLiquidar = quitarFiltroFechasLiquidar;

    function closeLiquidarModal() {
        window._vendedorLiquidarBloqueado = false;
        document.getElementById('modalLiquidar').classList.add('hidden');
        if (document.getElementById('liquidarFechaDesde')) document.getElementById('liquidarFechaDesde').value = '';
        if (document.getElementById('liquidarFechaHasta')) document.getElementById('liquidarFechaHasta').value = '';
        const alcance = document.getElementById('liquidarAlcance');
        if (alcance) {
            alcance.textContent = '';
            alcance.classList.add('hidden');
        }
        // Reset step wizard
        document.getElementById('liquidarStep2')?.classList.add('hidden');
        document.getElementById('formLiquidar')?.classList.remove('hidden');
        if (document.getElementById('stepProgressLine')) document.getElementById('stepProgressLine').style.width = '0%';
        if (document.getElementById('stepIndicator2')) document.getElementById('stepIndicator2').classList.add('opacity-35');
        const s2 = document.getElementById('step2Circle');
        if (s2) { s2.classList.replace('bg-emerald-600', 'bg-slate-300'); }
    }

    document.getElementById('formLiquidar')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (typeof clearAllFieldErrors === 'function') clearAllFieldErrors('formLiquidar');

        const vendedorId = document.getElementById('liquidarVendedor').value;
        let valid = true;

        if (!vendedorId) {
            if (typeof showFieldError === 'function') showFieldError('liquidarVendedor', 'Debes seleccionar un vendedor para continuar.');
            document.getElementById('liquidarVendedor-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            valid = false;
        }

        const metodoPago = document.getElementById('liquidarMetodoPago').value;
        const comprobante = document.getElementById('liquidarComprobante').files[0];
        if (metodoPago === 'Transferencia Bancaria') {
            if (!comprobante) {
                if (typeof showFieldError === 'function') showFieldError('liquidarComprobante', 'El comprobante es obligatorio para transferencias bancarias.');
                document.getElementById('liquidarComprobante-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                valid = false;
            } else {
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
                const fileExt = comprobante.name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(fileExt)) {
                    if (typeof showFieldError === 'function') showFieldError('liquidarComprobante', 'El comprobante debe ser una imagen (JPG, PNG, WEBP) o un documento PDF.');
                    valid = false;
                } else if (comprobante.size > 5 * 1024 * 1024) {
                    if (typeof showFieldError === 'function') showFieldError('liquidarComprobante', 'El comprobante no debe superar el límite de 5MB.');
                    valid = false;
                }
            }
        }

        if (!valid) return;

        const checkboxes = document.querySelectorAll('input[name="comisiones_ids[]"]:checked');
        if (checkboxes.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Sin comisiones seleccionadas', text: 'Debes seleccionar al menos una comisión pendiente para proceder con el pago.', confirmButtonColor: '#059669' });
            return;
        }

        // — Pasar a Step 2: poblar el resumen —
        const nombre   = document.getElementById('selectedVendedorNombre')?.textContent || '—';
        const username = document.getElementById('selectedVendedorUsername')?.textContent || '—';
        const avatar   = document.getElementById('selectedVendedorAvatar')?.textContent  || '??';
        const total    = document.getElementById('liquidarTotalMonto')?.textContent       || '$0.00';
        const referencia = document.getElementById('liquidarReferencia')?.value || '';
        const notas      = document.getElementById('liquidarNotas')?.value || '';

        // Encabezado vendedor
        document.getElementById('reviewVendedorAvatar').textContent = avatar.trim();
        document.getElementById('reviewVendedorNombre').textContent = nombre;
        document.getElementById('reviewVendedorUser').textContent   = username;
        document.getElementById('reviewTotal').textContent          = total;

        // Conteo
        document.getElementById('reviewCantidad').textContent = `${checkboxes.length} comisión${checkboxes.length !== 1 ? 'es' : ''}`;

        // Método
        let metodoBadge = 'Efectivo';
        if (metodoPago === 'Transferencia Bancaria') {
            metodoBadge = 'Transferencia Bancaria';
        } else if (metodoPago === 'Compensación de Saldo') {
            metodoBadge = 'Compensación de Saldo (Neto $0.00)';
        }
        document.getElementById('reviewMetodo').textContent = metodoBadge;

        // Referencia
        const refRow = document.getElementById('reviewReferenciaRow');
        if (referencia) {
            document.getElementById('reviewReferencia').textContent = referencia;
            refRow.classList.remove('hidden');
        } else { refRow.classList.add('hidden'); }

        // Comprobante
        const compRow = document.getElementById('reviewComprobanteRow');
        if (comprobante) {
            document.getElementById('reviewComprobanteNombre').textContent = comprobante.name;
            compRow.classList.remove('hidden');
        } else { compRow.classList.add('hidden'); }

        // Notas
        const notasRow = document.getElementById('reviewNotasRow');
        if (notas.trim()) {
            document.getElementById('reviewNotas').textContent = notas;
            notasRow.classList.remove('hidden');
        } else { notasRow.classList.add('hidden'); }

        // Lista de comisiones
        const listaReview = document.getElementById('reviewListaComisiones');
        let html = '';
        checkboxes.forEach(cb => {
            const label = cb.closest('label');
            const concepto = label?.querySelector('.truncate')?.textContent?.trim() || `Comisión #${cb.value}`;
            const montoEl  = label?.querySelector('[data-monto]');
            const val      = montoEl ? parseFloat(montoEl.dataset.monto) : 0;
            const isNeg    = val < 0;
            const monto    = (isNeg ? '-$' : '$') + formatNum(Math.abs(val));
            const colorClass = isNeg ? 'text-rose-600' : 'text-emerald-700';
            html += `<div class="flex items-center justify-between px-3 py-2 bg-white rounded-xl border border-slate-100 text-xs">
                <span class="text-slate-700 font-medium truncate flex-1 pr-2">${concepto}</span>
                <span class="font-extrabold ${colorClass} flex-shrink-0">${monto}</span>
            </div>`;
        });
        listaReview.innerHTML = html;

        // Activar indicador de paso 2
        document.getElementById('stepProgressLine').style.width = '100%';
        document.getElementById('stepIndicator2').classList.remove('opacity-35');
        document.getElementById('step2Circle').classList.replace('bg-slate-300', 'bg-emerald-600');

        // Mostrar Step 2, ocultar Step 1
        document.getElementById('formLiquidar').classList.add('hidden');
        document.getElementById('liquidarStep2').classList.remove('hidden');
    });

    function volverStep1Liquidar() {
        document.getElementById('liquidarStep2').classList.add('hidden');
        document.getElementById('formLiquidar').classList.remove('hidden');
        // Reiniciar indicador
        document.getElementById('stepProgressLine').style.width = '0%';
        document.getElementById('stepIndicator2').classList.add('opacity-35');
        document.getElementById('step2Circle').classList.replace('bg-emerald-600', 'bg-slate-300');
    }

    async function ejecutarLiquidacion() {
        const vendedorId = document.getElementById('liquidarVendedor').value;
        const metodoPago = document.getElementById('liquidarMetodoPago').value;
        const comprobante = document.getElementById('liquidarComprobante').files[0];
        const checkboxes = document.querySelectorAll('input[name="comisiones_ids[]"]:checked');

        const formData = new FormData();
        formData.append('id_vendedor', vendedorId);
        formData.append('metodo_pago', metodoPago);
        const fechaDesde = document.getElementById('liquidarFechaDesde')?.value;
        const fechaHasta = document.getElementById('liquidarFechaHasta')?.value;
        if (fechaDesde && fechaHasta) {
            formData.append('fecha_desde', fechaDesde);
            formData.append('fecha_hasta', fechaHasta);
        }
        formData.append('referencia_pago', document.getElementById('liquidarReferencia').value || '');
        formData.append('notas', document.getElementById('liquidarNotas').value || '');
        if (metodoPago === 'Transferencia Bancaria' && comprobante) {
            formData.append('comprobante_pago', comprobante);
        }
        checkboxes.forEach(cb => formData.append('comisiones_ids[]', cb.value));

        document.getElementById('btnFinalConfirmLiquidar').disabled = true;
        document.getElementById('btnFinalConfirmLiquidar').innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Procesando...</span>';

        Swal.fire({ title: 'Procesando pago...', text: 'Por favor espera un momento', allowOutsideClick: false, allowEscapeKey: false, didOpen: () => Swal.showLoading() });

        try {
            const res = await fetch(ROUTES.liquidar, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                body: formData,
            });
            const data = await res.json();
            if (data.success) {
                closeLiquidarModal();
                Swal.fire({ icon: 'success', title: '¡Liquidación Completada!', text: data.message, timer: 2500, showConfirmButton: false });
                setTimeout(() => window.location.reload(), 1200);
            } else {
                document.getElementById('btnFinalConfirmLiquidar').disabled = false;
                document.getElementById('btnFinalConfirmLiquidar').innerHTML = '<i class="fas fa-check-circle"></i><span>Confirmar Pago</span>';
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo procesar la liquidación.' });
            }
        } catch(e) {
            document.getElementById('btnFinalConfirmLiquidar').disabled = false;
            document.getElementById('btnFinalConfirmLiquidar').innerHTML = '<i class="fas fa-check-circle"></i><span>Confirmar Pago</span>';
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo procesar la liquidación.' });
        }
    }

    // ── Crear manual / Bono (admin) ──────────────────────────────────────

    function openCreateModal() {
        document.getElementById('modalCreate').classList.remove('hidden');
        document.getElementById('formCreate').reset();
        if (typeof deseleccionarVendedorCreate === 'function') {
            deseleccionarVendedorCreate();
        }
    }

    function closeCreateModal() {
        document.getElementById('modalCreate').classList.add('hidden');
        document.getElementById('formCreate').reset();
        if (typeof deseleccionarVendedorCreate === 'function') {
            deseleccionarVendedorCreate();
        }
    }

    function setConcepto(texto) {
        document.getElementById('createConcepto').value = texto;
    }

    document.getElementById('formCreate')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (typeof clearAllFieldErrors === 'function') clearAllFieldErrors('formCreate');

        const idVendedor = document.getElementById('createVendedor').value;
        const concepto   = document.getElementById('createConcepto').value.trim();
        const monto      = parseFloat(document.getElementById('createMonto').value);

        let valid = true;
        if (!idVendedor) {
            if (typeof showFieldError === 'function') showFieldError('createVendedor', 'Debes seleccionar un vendedor para asignar el bono.');
            valid = false;
        }
        if (!concepto) {
            if (typeof showFieldError === 'function') showFieldError('createConcepto', 'El concepto del bono es obligatorio.');
            valid = false;
        }
        if (isNaN(monto) || monto <= 0) {
            if (typeof showFieldError === 'function') showFieldError('createMonto', 'El monto del bono es obligatorio y debe ser mayor a $0.');
            valid = false;
        }
        if (!valid) return;

        const body = {
            id_vendedor: idVendedor,
            concepto:    concepto,
            monto:       monto,
            notas:       document.getElementById('createNotas').value || null,
        };

        try {
            const res = await fetch(ROUTES.store, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body:    JSON.stringify(body),
            });
            const data = await res.json();
            if (data.success) {
                closeCreateModal();
                Swal.fire({ icon: 'success', title: '¡Registrado!', text: data.message, timer: 2000, showConfirmButton: false });
                setTimeout(() => window.location.reload(), 1200);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Verifica los datos ingresados.' });
            }
        } catch(e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo registrar la comisión.' });
        }
    });

    // ── Cerrar modales con ESC ────────────────────────────────────────────
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeEditModal();
            closeLiquidarModal();
            closeCreateModal();
        }
    });
    </script>
    @if($isAdmin)
        @include('comisiones.partials.searchable-vendedor-select')
    @endif
</x-app>
