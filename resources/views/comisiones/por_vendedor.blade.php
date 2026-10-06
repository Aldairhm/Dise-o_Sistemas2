<x-app title="Comisiones por Vendedor | AXStore">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @php
        if ($periodo === 'mes') {
            $fechaDesdeFiltro = \Carbon\Carbon::createFromDate($anio, $mes, 1)->startOfMonth()->toDateString();
            $fechaHastaFiltro = \Carbon\Carbon::createFromDate($anio, $mes, 1)->endOfMonth()->toDateString();
        } else {
            $fechaDesdeFiltro = $desde ?? '';
            $fechaHastaFiltro = $hasta ?? '';
        }
    @endphp
    <input type="hidden" id="filtroFechaDesdeActiva" value="{{ $fechaDesdeFiltro }}">
    <input type="hidden" id="filtroFechaHastaActiva" value="{{ $fechaHastaFiltro }}">

    <div class="max-w-[1440px] mx-auto space-y-6">

        <!-- HEADER DEL DASHBOARD -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3.5">
                    <div
                        class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-600/20">
                        <i class="fas fa-users-gear text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-indigo-600 mb-0.5">Analítica de
                            Rendimiento</p>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                            Comisiones por Vendedor
                        </h1>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN RÁPIDA -->
            @if($isAdmin)
                <div class="flex items-center gap-3 flex-wrap">
                    <button type="button" onclick="openLiquidarModal(null, '', '', '{{ $fechaDesdeFiltro }}', '{{ $fechaHastaFiltro }}')"
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
        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm"
            aria-label="Secciones de comisiones">
            <a href="{{ route('comisiones.porSemana') }}"
                class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-calendar-week text-slate-400"></i>
                <span>Comisiones por Semana</span>
            </a>
            <a href="{{ route('comisiones.porVendedor') }}"
                class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-users-gear"></i>
                <span>Comisiones por Vendedor</span>
            </a>
            <a href="{{ route('comisiones.index') }}"
                class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-coins text-slate-400"></i>
                <span>Comisiones</span>
            </a>
            <a href="{{ route('comisiones.ajustes') }}"
                class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-arrows-rotate text-slate-400"></i>
                <span>Ajustes</span>
            </a>
        </nav>

        <!-- SELECTOR DE MODO: VISTA MENSUAL VS HISTORIAL COMPLETO -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl border border-slate-200/80 w-fit">
                <a href="{{ route('comisiones.porVendedor', array_merge(request()->except(['periodo', 'mes', 'anio', 'fecha_desde', 'fecha_hasta']), ['periodo' => 'mes', 'mes' => now()->month, 'anio' => now()->year])) }}"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ $periodo === 'mes' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900' }}">
                    <i
                        class="fas fa-calendar-alt text-[11px] {{ $periodo === 'mes' ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Resumen por Mes</span>
                    @if($periodo === 'mes')
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    @endif
                </a>
                <a href="{{ route('comisiones.porVendedor', array_merge(request()->except(['periodo', 'mes', 'anio']), ['periodo' => 'historico'])) }}"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ $periodo === 'historico' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900' }}">
                    <i
                        class="fas fa-clock-rotate-left text-[11px] {{ $periodo === 'historico' ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Historial de Todos</span>
                    @if($periodo === 'historico')
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    @endif
                </a>
            </div>

            @if($periodo === 'mes')
                {{-- Navegación rápida entre meses --}}
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('comisiones.porVendedor', array_merge(request()->all(), ['periodo' => 'mes', 'mes' => $mesAnterior->month, 'anio' => $mesAnterior->year])) }}"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition-all"
                        title="Mes anterior ({{ $nombresMeses[$mesAnterior->month] }})">
                        <i class="fas fa-chevron-left text-[10px]"></i>
                        <span class="hidden sm:inline">{{ $nombresMeses[$mesAnterior->month] }}</span>
                    </a>

                    <div
                        class="px-3 py-1 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 font-black text-xs flex items-center gap-1.5">
                        <i class="fas fa-calendar-check text-blue-600"></i>
                        <span>{{ $mesNombre }} {{ $anio }}</span>
                        @if($esMesActual)
                            <span
                                class="px-1.5 py-0.2 rounded-md bg-blue-600 text-white text-[9px] font-black uppercase tracking-wider">Mes
                                Actual</span>
                        @endif
                    </div>

                    <a href="{{ route('comisiones.porVendedor', array_merge(request()->all(), ['periodo' => 'mes', 'mes' => $mesSiguiente->month, 'anio' => $mesSiguiente->year])) }}"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition-all"
                        title="Mes siguiente ({{ $nombresMeses[$mesSiguiente->month] }})">
                        <span class="hidden sm:inline">{{ $nombresMeses[$mesSiguiente->month] }}</span>
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </a>

                    @if(!$esMesActual)
                        <a href="{{ route('comisiones.porVendedor', array_merge(request()->except(['mes', 'anio']), ['periodo' => 'mes', 'mes' => now()->month, 'anio' => now()->year])) }}"
                            class="px-2.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all flex items-center gap-1 shadow-2xs"
                            title="Ir al mes actual">
                            <i class="fas fa-rotate-left text-[10px]"></i>
                            <span>Mes Actual</span>
                        </a>
                    @endif
                </div>
            @else
                <div class="text-xs font-semibold text-slate-500 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Visualizando acumulado histórico de todos los tiempos</span>
                </div>
            @endif
        </div>

        <!-- FILTROS GLOBALES -->
        <section class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
            <form method="GET" action="{{ route('comisiones.porVendedor') }}"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3.5 items-end">
                <input type="hidden" name="periodo" value="{{ $periodo }}">

                @if($isAdmin)
                    {{-- Búsqueda rápida por nombre --}}
                    <div class="sm:col-span-2 md:col-span-4">
                        <label for="q" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            <i class="fas fa-magnifying-glass text-slate-400 mr-1"></i> Buscar Vendedor
                        </label>
                        <div class="relative">
                            <input type="text" name="q" id="q" value="{{ $busqueda ?? '' }}"
                                placeholder="Nombre o correo del vendedor..."
                                class="w-full rounded-xl border border-slate-300 bg-slate-50 pl-9 pr-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                            <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        </div>
                    </div>
                @endif

                @if($periodo === 'mes')
                    {{-- Selector de Mes --}}
                    <div class="sm:col-span-1 {{ $isAdmin ? 'md:col-span-3' : 'md:col-span-5' }}">
                        <label for="mes" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            <i class="fas fa-calendar-alt text-slate-400 mr-1"></i> Mes
                        </label>
                        <select name="mes" id="mes"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                            @foreach($nombresMeses as $mNum => $mNombre)
                                <option value="{{ $mNum }}" {{ (int) $mes === $mNum ? 'selected' : '' }}>
                                    {{ $mNombre }}
                                    {{ (int) now()->month === $mNum && (int) now()->year === (int) $anio ? '(Actual)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Selector de Año --}}
                    <div class="sm:col-span-1 {{ $isAdmin ? 'md:col-span-3' : 'md:col-span-5' }}">
                        <label for="anio"
                            class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            <i class="fas fa-calendar text-slate-400 mr-1"></i> Año
                        </label>
                        <select name="anio" id="anio"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                            @for($y = now()->year; $y >= now()->year - 3; $y--)
                                <option value="{{ $y }}" {{ (int) $anio === $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                @else
                    {{-- Rango Histórico: Fecha Desde --}}
                    <div class="{{ $isAdmin ? 'md:col-span-3' : 'md:col-span-5' }}">
                        <label for="fecha_desde"
                            class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            <i class="fas fa-calendar-day text-slate-400 mr-1"></i> Desde
                        </label>
                        <input type="date" name="fecha_desde" id="fecha_desde" value="{{ $desde }}"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    {{-- Rango Histórico: Fecha Hasta --}}
                    <div class="{{ $isAdmin ? 'md:col-span-3' : 'md:col-span-5' }}">
                        <label for="fecha_hasta"
                            class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            <i class="fas fa-calendar-check text-slate-400 mr-1"></i> Hasta
                        </label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ $hasta }}"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>
                @endif

                {{-- Botones --}}
                <div class="md:col-span-2 flex items-center gap-1.5">
                    <button type="submit"
                        class="w-full rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-2.5 text-xs transition-all shadow-md shadow-blue-600/15 flex items-center justify-center gap-1 cursor-pointer">
                        <i class="fas fa-filter text-[11px]"></i>
                        <span>Filtrar</span>
                    </button>
                    @if($desde || $hasta || $busqueda || ($periodo === 'mes' && (!$esMesActual || $anio != now()->year)))
                        <a href="{{ route('comisiones.porVendedor', ['periodo' => $periodo]) }}"
                            class="rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-500 p-2 text-xs transition-all flex items-center justify-center"
                            title="Restablecer filtros">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- KPI CARDS GLOBALES -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- TOTAL GENERADO -->
            <div
                class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">
                        {{ $periodo === 'mes' ? "Total {$mesNombre} {$anio}" : "Total Histórico" }}
                    </span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                        <i class="fas fa-vault"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">
                        ${{ number_format($kpis['total'], 2) }}</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>{{ $kpis['vendedores'] }} vendedores activos en periodo</span>
                    </p>
                </div>
            </div>

            <!-- POR PAGAR (PENDIENTE) -->
            <div
                class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">
                        {{ $periodo === 'mes' ? "Por Liquidar ({$mesNombre})" : "Por Liquidar (Histórico)" }}
                    </span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-amber-700 tracking-tight">
                        ${{ number_format($kpis['pendiente'], 2) }}</h3>
                    <p class="text-xs font-semibold text-amber-600 mt-1">
                        {{ $periodo === 'mes' ? "Saldo pendiente de {$mesNombre}" : "Saldo pendiente acumulado" }}
                    </p>
                </div>
            </div>

            <!-- PAGADAS / LIQUIDADAS -->
            <div
                class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">
                        {{ $periodo === 'mes' ? "Liquidado ({$mesNombre})" : "Total Liquidado" }}
                    </span>
                    <div
                        class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-emerald-700 tracking-tight">
                        ${{ number_format($kpis['pagada'], 2) }}</h3>
                    <p class="text-xs font-semibold text-emerald-600 mt-1">Total transferido / entregado</p>
                </div>
            </div>

            <!-- CANCELADAS -->
            <div
                class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">
                        {{ $periodo === 'mes' ? "Canceladas ({$mesNombre})" : "Canceladas" }}
                    </span>
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">
                        ${{ number_format($kpis['cancelada'], 2) }}</h3>
                    <p class="text-xs font-semibold text-slate-400 mt-1">Por ventas anuladas</p>
                </div>
            </div>
        </div>

        <!-- LISTA DE VENDEDORES CON HISTORIAL Y DESGLOSE -->
        <div class="space-y-4">
            @forelse($reporte as $item)
                @php
                    $vendedor = $item['vendedor'];
                    $comisiones = $item['comisiones'];
                    $hasPendiente = $item['total_pendiente'] > 0;
                @endphp
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-200 hover:border-slate-300"
                    id="card-vendedor-{{ $vendedor->id }}">

                    <!-- HEADER DE LA TARJETA DEL VENDEDOR -->
                    <div
                        class="p-5 sm:p-6 bg-white flex flex-col lg:flex-row lg:items-center justify-between gap-5 border-b border-slate-100">

                        <!-- INFORMACIÓN DEL VENDEDOR -->
                        <div class="flex items-center gap-4">
                            <div
                                class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-lg flex items-center justify-center shadow-md shadow-blue-500/20 flex-shrink-0">
                                {{ strtoupper(substr($vendedor->nombre_real ?: $vendedor->username, 0, 2)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h2 class="text-lg font-black text-slate-900 tracking-tight">
                                        {{ $vendedor->nombre_real ?: $vendedor->username }}</h2>
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $vendedor->rol === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $vendedor->rol }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium mt-0.5 flex items-center gap-2">
                                    <span><i
                                            class="fas fa-envelope text-slate-400 mr-1"></i>{{ $vendedor->username }}</span>
                                    @if($vendedor->telefono)
                                        <span><i class="fas fa-phone text-slate-400 mr-1"></i>{{ $vendedor->telefono }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- RESUMEN DE CIFRAS DEL VENDEDOR -->
                        <div
                            class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50/80 p-3.5 rounded-2xl border border-slate-100 flex-1 max-w-2xl">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                                    {{ $periodo === 'mes' ? "Ganado ({$mesNombre})" : "Total Ganado" }}
                                </span>
                                <span
                                    class="text-base font-black text-slate-900">${{ number_format($item['total_comisiones'], 2) }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block">Por
                                    Liquidar</span>
                                <span
                                    class="text-base font-black text-amber-700">${{ number_format($item['total_pendiente'], 2) }}</span>
                                @if(($item['saldo_garantia_total'] ?? 0) > 0)
                                    <span class="text-[9px] text-amber-800 font-semibold block leading-tight mt-0.5"
                                        title="En período de garantía de devolución (aún no liquidable)">
                                        <i class="fas fa-shield-halved text-[8px] text-amber-600"></i> En garantía: ${{ number_format($item['saldo_garantia_total'], 2) }}
                                    </span>
                                @endif
                                @if($periodo === 'mes' && $item['saldo_pendiente_total'] > $item['total_pendiente'])
                                    <span class="text-[9px] text-slate-500 font-semibold block leading-tight mt-0.5"
                                        title="Incluye pendientes de meses anteriores">
                                        Total acum: ${{ number_format($item['saldo_pendiente_total'], 2) }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <span
                                    class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Pagado</span>
                                <span
                                    class="text-base font-black text-emerald-700">${{ number_format($item['total_pagada'], 2) }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                                    {{ $periodo === 'mes' ? "Ventas en {$mesNombre}" : "Ventas/Registros" }}
                                </span>
                                <span class="text-base font-black text-slate-700">{{ $item['total_ventas'] }}</span>
                            </div>
                        </div>

                        <!-- BOTONES DE ACCIÓN DE ESTE VENDEDOR -->
                        <div class="flex items-center gap-2 flex-wrap justify-end">
                            @if($isAdmin && ($hasPendiente || ($item['saldo_pendiente_total'] ?? 0) > 0))
                                @php
                                    if ($periodo === 'mes') {
                                        $montoLiquidable = $item['saldo_liquidable'] ?? 0;
                                        $montoGarantia   = $item['saldo_en_garantia'] ?? 0;
                                    } else {
                                        $montoLiquidable = $item['saldo_liquidable_total'] ?? 0;
                                        $montoGarantia   = $item['saldo_garantia_total'] ?? 0;
                                    }
                                @endphp
                                @if($montoLiquidable > 0)
                                    <button type="button" onclick="liquidarDirectoVendedor({{ $vendedor->id }}, '{{ addslashes($vendedor->nombre_real) }}', '{{ addslashes($vendedor->username) }}', '{{ $fechaDesdeFiltro }}', '{{ $fechaHastaFiltro }}')"
                                        class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all cursor-pointer flex items-center gap-1.5"
                                        title="Liquidar comisiones elegibles para este vendedor en el período">
                                        <i class="fas fa-circle-check"></i>
                                        <span>Liquidar (${{ number_format($montoLiquidable, 2) }})</span>
                                    </button>
                                @elseif($montoGarantia > 0)
                                    <button type="button" onclick="liquidarDirectoVendedor({{ $vendedor->id }}, '{{ addslashes($vendedor->nombre_real) }}', '{{ addslashes($vendedor->username) }}', '{{ $fechaDesdeFiltro }}', '{{ $fechaHastaFiltro }}')"
                                        class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5"
                                        title="Comisiones en período de garantía de devolución (aún no liquidables)">
                                        <i class="fas fa-shield-halved text-amber-600"></i>
                                        <span>En garantía (${{ number_format($montoGarantia, 2) }})</span>
                                    </button>
                                @elseif(($item['saldo_liquidable_total'] ?? 0) > 0)
                                    <button type="button" onclick="liquidarDirectoVendedor({{ $vendedor->id }}, '{{ addslashes($vendedor->nombre_real) }}', '{{ addslashes($vendedor->username) }}', '', '')"
                                        class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5"
                                        title="Ver y liquidar comisiones pendientes de otros periodos">
                                        <i class="fas fa-clock-rotate-left text-slate-500"></i>
                                        <span>Histórico (${{ number_format($item['saldo_liquidable_total'], 2) }})</span>
                                    </button>
                                @endif
                            @endif

                            <button type="button" onclick="toggleDesgloseVendedor({{ $vendedor->id }})"
                                class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5">
                                <i class="fas fa-list-ul text-slate-500"></i>
                                <span>Desglose
                                    {{ $periodo === 'mes' ? "({$mesNombre}: {$comisiones->count()})" : "({$comisiones->count()})" }}</span>
                                <i class="fas fa-chevron-down text-[10px] transition-transform duration-200"
                                    id="icon-chevron-{{ $vendedor->id }}"></i>
                            </button>
                        </div>
                    </div>

                    <!-- DESGLOSE EXPANDIBLE DE TODAS LAS VENTAS / COMISIONES -->
                    <div id="desglose-vendedor-{{ $vendedor->id }}"
                        class="hidden bg-slate-50/40 p-4 sm:p-5 border-t border-slate-100">
                        <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-2xs">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr
                                        class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-bold">
                                        <th class="px-4 py-3 text-left">Fecha y Hora</th>
                                        <th class="px-4 py-3 text-left">Concepto / Detalle de la Venta</th>
                                        <th class="px-4 py-3 text-right">Comisión</th>
                                        <th class="px-4 py-3 text-center">Estado</th>
                                        <th class="px-4 py-3 text-left">Detalle de Pago</th>
                                        <th class="px-4 py-3 text-left">Notas</th>
                                        <th class="px-4 py-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($comisiones as $c)
                                        <tr class="hover:bg-slate-50/60 transition-colors">

                                            {{-- Fecha --}}
                                            <td class="px-4 py-3 whitespace-nowrap text-slate-600">
                                                <span
                                                    class="font-bold text-slate-800 block">{{ $c->fecha_registro ? $c->fecha_registro->format('d/m/Y') : '—' }}</span>
                                                <span
                                                    class="text-[10px] text-slate-400">{{ $c->fecha_registro ? $c->fecha_registro->format('h:i A') : '' }}</span>
                                            </td>

                                            {{-- Concepto --}}
                                            <td class="px-4 py-3 text-slate-700">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    @if($c->id_salida)
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md text-[10px] font-bold">
                                                            <i class="fas fa-shopping-bag"></i> Salida #{{ $c->id_salida }}
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md text-[10px] font-bold">
                                                            <i class="fas fa-award"></i> Bono
                                                        </span>
                                                    @endif
                                                    <span class="font-semibold text-slate-800">
                                                        {{ $c->concepto ?? ($c->salida ? "Venta directa ({$c->salida->cantidad} uds)" : "Comisión directa") }}
                                                    </span>
                                                </div>
                                                @if($c->salida && $c->salida->variante && $c->salida->variante->producto)
                                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                                        {{ $c->salida->variante->producto->nombre }} -
                                                        {{ $c->salida->variante->nombre_variante }} | Total salida:
                                                        ${{ number_format($c->salida->total, 2) }}
                                                    </p>
                                                @endif
                                            </td>

                                            {{-- Comisión --}}
                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                <span
                                                    class="font-black text-sm text-slate-900 block">${{ number_format($c->monto, 2) }}</span>
                                                @if($c->porcentaje && $c->porcentaje > 0)
                                                    <span class="text-[10px] text-slate-400">Ref:
                                                        ${{ number_format($c->porcentaje, 2) }}/ud</span>
                                                @endif
                                            </td>

                                            {{-- Estado --}}
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                                @if($c->estado === 'Pendiente')
                                                    @if($c->es_liquidacion_bloqueada)
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300 shadow-2xs"
                                                            title="{{ $c->motivo_bloqueo_liquidacion }}">
                                                            <i class="fas fa-shield-halved text-[9px] text-amber-600"></i>
                                                            En garantía
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                            Liquidación pendiente
                                                        </span>
                                                    @endif
                                                @elseif($c->estado === 'Pagada')
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <i class="fas fa-check text-[9px]"></i>
                                                        Pagada
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                        <i class="fas fa-ban text-[9px]"></i>
                                                        Cancelada
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Método de Pago --}}
                                            <td class="px-4 py-3 text-slate-600 text-[11px]">
                                                @if($c->estado === 'Pagada')
                                                    <div class="space-y-1.5">
                                                        <span class="font-bold text-emerald-700 flex items-center gap-1.5 text-xs">
                                                            @if($c->metodo_pago === 'Transferencia Bancaria')
                                                                <i class="fas fa-university text-blue-600"></i>
                                                            @elseif($c->metodo_pago === 'Efectivo')
                                                                <i class="fas fa-money-bill-wave text-emerald-600"></i>
                                                            @else
                                                                <i class="fas fa-money-bill-transfer text-emerald-600"></i>
                                                            @endif
                                                            {{ $c->metodo_pago ?? 'Efectivo' }}
                                                        </span>
                                                        @if($c->referencia_pago)
                                                            <span class="text-[10px] text-slate-500 font-mono block">Ref:
                                                                {{ $c->referencia_pago }}</span>
                                                        @endif
                                                        @if($c->comprobante_url)
                                                            <a href="{{ $c->comprobante_url }}" target="_blank"
                                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold border border-indigo-200 text-[11px] shadow-2xs transition-all hover:scale-[1.02] cursor-pointer w-fit">
                                                                <i class="fas fa-file-invoice-dollar text-indigo-600"></i>
                                                                <span>Ver Comprobante</span>
                                                                <i class="fas fa-arrow-up-right-from-square text-[9px] opacity-70"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-slate-400 italic">Por liquidar</span>
                                                @endif
                                            </td>

                                            {{-- Notas --}}
                                            <td class="px-4 py-3 text-slate-500 text-[11px] max-w-[150px] truncate"
                                                title="{{ $c->notas }}">
                                                {{ $c->notas ?? '—' }}
                                            </td>

                                            {{-- Acciones --}}
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                                <div class="flex items-center justify-center gap-1">
                                                    {{-- Ver Detalle Completo --}}
                                                    <button type="button"
                                                        onclick="verDetalleComision({{ $c->id }})"
                                                        class="w-7 h-7 rounded-lg flex items-center justify-center text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 transition-colors cursor-pointer"
                                                        title="Ver Detalle Completo">
                                                        <i class="fas fa-eye text-[11px]"></i>
                                                    </button>

                                                    @if($isAdmin && $c->estado === 'Pendiente')
                                                        <button type="button"
                                                            onclick="openEditModal({{ $c->id }}, '{{ $c->monto }}', '{{ addslashes($c->concepto ?? '') }}', '{{ addslashes($c->notas ?? '') }}')"
                                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer"
                                                            title="Editar">
                                                            <i class="fas fa-pen text-[10px]"></i>
                                                        </button>
                                                        <button type="button" onclick="cancelarComision({{ $c->id }})"
                                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors cursor-pointer"
                                                            title="Cancelar">
                                                            <i class="fas fa-ban text-[10px]"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-6 text-center text-slate-400">
                                                No hay registros de comisiones para este vendedor en el rango seleccionado.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400 shadow-sm">
                    <div
                        class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl mx-auto mb-3 text-slate-400">
                        <i class="fas fa-users-slash"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700">No se encontraron vendedores</h3>
                    <p class="text-xs text-slate-400 mt-1">Ajusta los filtros de búsqueda o fechas.</p>
                </div>
            @endforelse
        </div>

    </div>

    {{-- MODALES --}}
    @include('comisiones.modals')

    {{-- SCRIPTS --}}
    <script>
        const ROUTES = {
            index: "{{ route('comisiones.index') }}",
            pendientes: "{{ url('comisiones/pendientes') }}",
            store: @if($isAdmin) "{{ route('comisiones.store') }}" @else"null" @endif,
            update: @if($isAdmin) "{{ url('comisiones') }}" @else"null" @endif,
            liquidar: @if($isAdmin) "{{ route('comisiones.liquidar') }}" @else"null" @endif,
            cancelar: @if($isAdmin) "{{ url('comisiones') }}" @else"null" @endif,
        };
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        function toggleDesgloseVendedor(id) {
            const desglose = document.getElementById(`desglose-vendedor-${id}`);
            const chevron = document.getElementById(`icon-chevron-${id}`);
            if (!desglose) return;

            desglose.classList.toggle('hidden');
            if (chevron) {
                chevron.classList.toggle('rotate-180');
            }
        }

        function liquidarDirectoVendedor(vendedorId, nombreVendedor = '', usernameVendedor = '', fechaDesde = '', fechaHasta = '') {
            openLiquidarModal(vendedorId, nombreVendedor, usernameVendedor, fechaDesde, fechaHasta, true);
        }

        function formatNum(n) {
            return parseFloat(n).toLocaleString('es-SV', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // ── Cargar pendientes dinámicos al seleccionar vendedor para liquidar ──

        async function cargarPendientesVendedor(vendedorId) {
            const resumenBox = document.getElementById('liquidarResumenBox');
            const emptyBox = document.getElementById('liquidarEmptyBox');
            const loadingBox = document.getElementById('liquidarLoadingBox');
            const lista = document.getElementById('liquidarDetalleLista');
            const btnSubmit = document.getElementById('btnConfirmLiquidar');

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
                        <div class="p-2 mb-2 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-[11px] flex items-center gap-1.5">
                            <i class="fas fa-shield-halved text-amber-600 flex-shrink-0"></i>
                            <span><strong>${data.cantidad_bloqueada} comisión(es)</strong> ($${formatNum(data.total_bloqueado)}) están en garantía de devolución y no pueden ser liquidadas aún.</span>
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
            document.getElementById('editMonto').value = monto;
            document.getElementById('editConcepto').value = concepto || '';
            document.getElementById('editNotas').value = notas || '';
            document.getElementById('modalEdit').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('modalEdit').classList.add('hidden');
        }

        document.getElementById('formEdit')?.addEventListener('submit', async function (e) {
            e.preventDefault();
            const id = document.getElementById('editComisionId').value;
            const body = {
                monto: document.getElementById('editMonto').value,
                concepto: document.getElementById('editConcepto').value,
                notas: document.getElementById('editNotas').value,
                _method: 'PUT',
            };

            try {
                const res = await fetch(`${ROUTES.update}/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(body),
                });
                const data = await res.json();
                if (data.success) {
                    closeEditModal();
                    Swal.fire({ icon: 'success', title: '¡Actualizado!', text: data.message, timer: 2000, showConfirmButton: false });
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                }
            } catch (e) {
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
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ _method: 'PATCH' }),
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: '¡Cancelada!', text: data.message, timer: 2000, showConfirmButton: false });
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                }
            } catch (e) {
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

            const fDesde = fechaDesde || document.getElementById('filtroFechaDesdeActiva')?.value || '';
            const fHasta = fechaHasta || document.getElementById('filtroFechaHastaActiva')?.value || '';

            if (document.getElementById('liquidarFechaDesde')) document.getElementById('liquidarFechaDesde').value = fDesde;
            if (document.getElementById('liquidarFechaHasta')) document.getElementById('liquidarFechaHasta').value = fHasta;

            const alcance = document.getElementById('liquidarAlcance');
            if (alcance) {
                if (fDesde && fHasta) {
                    alcance.textContent = `Pago limitado del ${fDesde} al ${fHasta}, inclusive.`;
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
            } else if (vendedorId && typeof cargarPendientesVendedor === 'function') {
                const inputHidden = document.getElementById('liquidarVendedor');
                if (inputHidden) inputHidden.value = vendedorId;
                cargarPendientesVendedor(vendedorId);
            }
        }

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

        document.getElementById('formLiquidar')?.addEventListener('submit', async function (e) {
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
            const nombre = document.getElementById('selectedVendedorNombre')?.textContent || '—';
            const username = document.getElementById('selectedVendedorUsername')?.textContent || '—';
            const avatar = document.getElementById('selectedVendedorAvatar')?.textContent || '??';
            const total = document.getElementById('liquidarTotalMonto')?.textContent || '$0.00';
            const referencia = document.getElementById('liquidarReferencia')?.value || '';
            const notas = document.getElementById('liquidarNotas')?.value || '';

            // Encabezado vendedor
            document.getElementById('reviewVendedorAvatar').textContent = avatar.trim();
            document.getElementById('reviewVendedorNombre').textContent = nombre;
            document.getElementById('reviewVendedorUser').textContent = username;
            document.getElementById('reviewTotal').textContent = total;

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
                const montoEl = label?.querySelector('[data-monto]');
                const val = montoEl ? parseFloat(montoEl.dataset.monto) : 0;
                const isNeg = val < 0;
                const monto = (isNeg ? '-$' : '$') + formatNum(Math.abs(val));
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
            formData.append('referencia_pago', document.getElementById('liquidarReferencia').value || '');
            formData.append('notas', document.getElementById('liquidarNotas').value || '');
            if (metodoPago === 'Transferencia Bancaria' && comprobante) {
                formData.append('comprobante_pago', comprobante);
            }
            const fechaDesde = document.getElementById('liquidarFechaDesde')?.value;
            const fechaHasta = document.getElementById('liquidarFechaHasta')?.value;
            if (fechaDesde) formData.append('fecha_desde', fechaDesde);
            if (fechaHasta) formData.append('fecha_hasta', fechaHasta);
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
            } catch (e) {
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

        document.getElementById('formCreate')?.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (typeof clearAllFieldErrors === 'function') clearAllFieldErrors('formCreate');

            const idVendedor = document.getElementById('createVendedor').value;
            const concepto = document.getElementById('createConcepto').value.trim();
            const monto = parseFloat(document.getElementById('createMonto').value);

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
                concepto: concepto,
                monto: monto,
                notas: document.getElementById('createNotas').value || null,
            };

            try {
                const res = await fetch(ROUTES.store, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(body),
                });
                const data = await res.json();
                if (data.success) {
                    closeCreateModal();
                    Swal.fire({ icon: 'success', title: '¡Registrado!', text: data.message, timer: 2000, showConfirmButton: false });
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Verifica los datos ingresados.' });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo registrar la comisión.' });
            }
        });

        // ── Cerrar modales con ESC ────────────────────────────────────────────
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeEditModal();
                closeLiquidarModal();
                closeCreateModal();
                if (typeof closeDetalleModal === 'function') closeDetalleModal();
            }
        });
    </script>
</x-app>