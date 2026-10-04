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
            <a href="{{ route('comisiones.porSemana') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-calendar-week"></i>
                <span>Comisiones por Semana</span>
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
                    <select id="vendedor_id" name="vendedor_id"
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- TOTAL GENERADO -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
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
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Por Liquidar</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-amber-700 tracking-tight">${{ number_format($kpis['pendiente'], 2) }}</h3>
                    <p class="text-xs font-semibold text-amber-600 mt-1">Saldo semanal pendiente</p>
                </div>
            </div>

            <!-- PAGADAS / LIQUIDADAS -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
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
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
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

        <!-- LISTA DE SEMANAS CON CORTES Y DESGLOSE -->
        <div class="space-y-4">
            @forelse($reporteSemanas as $sem)
            @php
                $comisiones = $sem['comisiones'];
                $vendedoresSemana = $sem['vendedores'];
                $hasPendiente = $sem['total_pendiente'] > 0;
            @endphp
            <div class="bg-white border {{ $sem['es_semana_actual'] ? 'border-blue-400 ring-2 ring-blue-500/10' : 'border-slate-200' }} rounded-2xl shadow-sm overflow-hidden transition-all duration-200 hover:border-slate-300">
                
                <!-- HEADER DE LA TARJETA DE LA SEMANA -->
                <div class="p-5 sm:p-6 bg-white flex flex-col lg:flex-row lg:items-center justify-between gap-5 border-b border-slate-100">
                    
                    <!-- INFORMACIÓN DE LA SEMANA -->
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl {{ $sem['es_semana_actual'] ? 'bg-gradient-to-tr from-blue-600 to-indigo-600 text-white' : 'bg-slate-100 text-slate-700' }} font-black text-center flex flex-col items-center justify-center shadow-xs flex-shrink-0">
                            <span class="text-[9px] uppercase tracking-wider leading-none">Sem</span>
                            <span class="text-xl leading-none mt-0.5">{{ $sem['semana_numero'] }}</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="text-lg font-black text-slate-900 tracking-tight">
                                    Semana {{ $sem['semana_numero'] }} ({{ $sem['year'] }})
                                </h2>
                                @if($sem['es_semana_actual'])
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-700 border border-blue-200">
                                    Semana Actual
                                </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 font-semibold mt-0.5 flex items-center gap-1.5">
                                <i class="fas fa-calendar-range text-slate-400"></i>
                                <span>{{ $sem['inicio_semana']->format('d/m/Y') }}</span>
                                <span class="text-slate-300">—</span>
                                <span>{{ $sem['fin_semana']->format('d/m/Y') }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- RESUMEN DE CIFRAS DE LA SEMANA -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50/80 p-3.5 rounded-2xl border border-slate-100 flex-1 max-w-2xl">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Semana</span>
                            <span class="text-base font-black text-slate-900">${{ number_format($sem['total_comisiones'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block">Por Liquidar</span>
                            <span class="text-base font-black text-amber-700">${{ number_format($sem['total_pendiente'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Pagado</span>
                            <span class="text-base font-black text-emerald-700">${{ number_format($sem['total_pagada'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Ventas</span>
                            <span class="text-base font-black text-slate-700">{{ $sem['total_ventas'] }}</span>
                        </div>
                    </div>

                    <!-- BOTONES DE ACCIÓN DE LA SEMANA -->
                    <div class="flex items-center gap-2 flex-wrap justify-end">
                        <button type="button" onclick="toggleDesgloseSemana('{{ $sem['key'] }}')"
                                class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5">
                            <i class="fas fa-list-ul text-slate-500"></i>
                            <span>Desglose ({{ $comisiones->count() }})</span>
                            <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" id="icon-chevron-{{ $sem['key'] }}"></i>
                        </button>
                    </div>
                </div>

                <!-- DESGLOSE EXPANDIBLE DE LA SEMANA -->
                <div id="desglose-semana-{{ $sem['key'] }}" class="{{ $sem['es_semana_actual'] ? '' : 'hidden' }} bg-slate-50/40 p-4 sm:p-5 border-t border-slate-100 space-y-4">
                    
                    {{-- Carrusel de Vendedores en esta semana --}}
                    @if($vendedoresSemana->isNotEmpty())
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                                    <i class="fas fa-users text-slate-400"></i>
                                    <span>Vendedores en esta Semana</span>
                                    <span class="px-1.5 py-0.2 rounded-md bg-slate-200/70 text-slate-600 text-[10px] font-black">{{ $vendedoresSemana->count() }}</span>
                                </h4>
                                <span class="text-[10px] text-slate-400 font-medium hidden sm:inline">(Clic para filtrar tabla)</span>
                                
                                <span id="filtro-badge-{{ $sem['key'] }}" class="hidden inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                    <span>Filtrando por: <strong id="filtro-nombre-{{ $sem['key'] }}"></strong></span>
                                    <button type="button" onclick="limpiarFiltroVendedorSemana('{{ $sem['key'] }}')" class="hover:text-red-600 ml-1 cursor-pointer" title="Quitar filtro">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                {{-- Mini buscador siempre visible en el carrusel --}}
                                <div class="relative w-36 sm:w-48">
                                    <input type="text"
                                           placeholder="Buscar vendedor..."
                                           oninput="buscarEnCarruselSemana('{{ $sem['key'] }}', this.value)"
                                           class="w-full pl-7 pr-2 py-1.5 text-[11px] font-medium rounded-lg border border-slate-200 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                    <i class="fas fa-search absolute left-2 top-1/2 -translate-y-1/2 text-[10px] text-slate-400"></i>
                                </div>

                                {{-- Botones de Navegación del Carrusel --}}
                                <div class="flex items-center gap-1 bg-white p-0.5 rounded-lg border border-slate-200 shadow-2xs">
                                    <button type="button"
                                            onclick="scrollCarruselSemana('{{ $sem['key'] }}', -260)"
                                            class="w-6 h-6 rounded-md hover:bg-slate-100 text-slate-600 flex items-center justify-center transition-colors cursor-pointer"
                                            title="Desplazar a la izquierda">
                                        <i class="fas fa-chevron-left text-[9px]"></i>
                                    </button>
                                    <button type="button"
                                            onclick="scrollCarruselSemana('{{ $sem['key'] }}', 260)"
                                            class="w-6 h-6 rounded-md hover:bg-slate-100 text-slate-600 flex items-center justify-center transition-colors cursor-pointer"
                                            title="Desplazar a la derecha">
                                        <i class="fas fa-chevron-right text-[9px]"></i>
                                    </button>
                                </div>

                                <button type="button" id="btn-limpiar-filtro-{{ $sem['key'] }}" onclick="limpiarFiltroVendedorSemana('{{ $sem['key'] }}')"
                                        class="hidden text-[11px] font-bold text-blue-600 hover:text-blue-800 transition-colors cursor-pointer flex items-center gap-1 ml-1">
                                    <i class="fas fa-rotate-left text-[10px]"></i>
                                    <span>Mostrar todos</span>
                                </button>
                            </div>
                        </div>

                        {{-- Carril deslizable de tarjetas --}}
                        <div class="relative">
                            <div id="carrusel-track-{{ $sem['key'] }}"
                                 class="flex items-center gap-3 overflow-x-auto scroll-smooth py-1 px-0.5 no-scrollbar"
                                 style="scrollbar-width: none; -ms-overflow-style: none;">
                                @foreach($vendedoresSemana as $vItem)
                                @if($vItem['vendedor'])
                                @php
                                    $vId = $vItem['vendedor']->id;
                                    $vNombre = $vItem['vendedor']->nombre_real ?: $vItem['vendedor']->username;
                                @endphp
                                <div data-vendedor-card="{{ $vId }}"
                                     data-vendedor-nombre="{{ strtolower($vNombre) }}"
                                     onclick="toggleFiltroVendedorSemana('{{ $sem['key'] }}', {{ $vId }}, '{{ addslashes($vNombre) }}')"
                                     title="Clic para ver comisiones de {{ $vNombre }}"
                                     class="vendedor-card-{{ $sem['key'] }} w-[240px] sm:w-[260px] flex-shrink-0 bg-white p-3 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between gap-2.5 cursor-pointer hover:border-blue-400 hover:shadow-md transition-all duration-200 group select-none relative">
                                    <div class="flex items-center gap-2 overflow-hidden">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0 group-hover:scale-105 transition-transform">
                                            {{ strtoupper(substr($vNombre, 0, 2)) }}
                                        </div>
                                        <div class="truncate">
                                            <div class="flex items-center gap-1">
                                                <p class="font-bold text-xs text-slate-800 truncate group-hover:text-blue-600 transition-colors">{{ $vNombre }}</p>
                                                <span class="badge-filtro hidden px-1 py-0.2 rounded text-[8px] font-black uppercase tracking-wider bg-blue-600 text-white">Activo</span>
                                            </div>
                                            <p class="text-[10px] text-slate-400">{{ $vItem['cantidad'] }} venta{{ $vItem['cantidad'] != 1 ? 's' : '' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0 flex items-center gap-1.5">
                                        <div>
                                            <span class="font-black text-xs text-slate-900 block">${{ number_format($vItem['total'], 2) }}</span>
                                            @if($vItem['pendiente'] > 0)
                                                <span class="text-[10px] text-amber-600 font-bold block">Por pagar: ${{ number_format($vItem['pendiente'], 2) }}</span>
                                            @else
                                                <span class="text-[10px] text-emerald-600 font-bold block">Liquidado</span>
                                            @endif
                                        </div>
                                        <i class="fas fa-filter text-[9px] text-slate-300 group-hover:text-blue-500 transition-colors icon-filtro-hint"></i>
                                    </div>
                                </div>
                                @endif
                                @endforeach

                                <div id="carrusel-empty-{{ $sem['key'] }}" class="hidden py-3 px-4 text-xs text-slate-400 italic">
                                    No se encontró ningún vendedor con ese nombre.
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Tabla de Ventas de la Semana --}}
                    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-2xs">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-bold">
                                    @if($isAdmin)
                                    <th class="px-4 py-3 text-left">Vendedor</th>
                                    @endif
                                    <th class="px-4 py-3 text-left">Fecha y Hora</th>
                                    <th class="px-4 py-3 text-left">Concepto / Venta</th>
                                    <th class="px-4 py-3 text-right">Comisión</th>
                                    <th class="px-4 py-3 text-center">Estado</th>
                                    <th class="px-4 py-3 text-left">Detalle de Pago</th>
                                    <th class="px-4 py-3 text-left">Notas</th>
                                    @if($isAdmin)
                                    <th class="px-4 py-3 text-center">Acciones</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($comisiones as $c)
                                <tr class="fila-comision-{{ $sem['key'] }} hover:bg-slate-50/60 transition-colors" data-vendedor-id="{{ $c->id_vendedor }}">
                                    
                                    @if($isAdmin)
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="font-bold text-slate-800">{{ $c->vendedor->nombre_real ?? '—' }}</span>
                                    </td>
                                    @endif

                                    {{-- Fecha --}}
                                    <td class="px-4 py-3 whitespace-nowrap text-slate-600">
                                        <span class="font-bold text-slate-800 block">{{ $c->fecha_registro ? $c->fecha_registro->format('d/m/Y') : '—' }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $c->fecha_registro ? $c->fecha_registro->format('h:i A') : '' }}</span>
                                    </td>

                                    {{-- Concepto --}}
                                    <td class="px-4 py-3 text-slate-700">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @if($c->id_salida)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md text-[10px] font-bold">
                                                    <i class="fas fa-shopping-bag"></i> Salida #{{ $c->id_salida }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md text-[10px] font-bold">
                                                    <i class="fas fa-award"></i> Bono
                                                </span>
                                            @endif
                                            <span class="font-semibold text-slate-800">
                                                {{ $c->concepto ?? ($c->salida ? "Venta ({$c->salida->cantidad} uds)" : "Comisión") }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Comisión --}}
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="font-black text-sm text-slate-900 block">${{ number_format($c->monto, 2) }}</span>
                                    </td>

                                    {{-- Estado --}}
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        @if($c->estado === 'Pendiente')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Pendiente
                                            </span>
                                        @elseif($c->estado === 'Pagada')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="fas fa-check text-[9px]"></i>
                                                Pagada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                <i class="fas fa-ban text-[9px]"></i>
                                                Cancelada
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Detalle de Pago --}}
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
                                                    <span class="text-[10px] text-slate-500 font-mono block">Ref: {{ $c->referencia_pago }}</span>
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
                                    <td class="px-4 py-3 text-slate-500 text-[11px] max-w-[150px] truncate" title="{{ $c->notas }}">
                                        {{ $c->notas ?? '—' }}
                                    </td>

                                    {{-- Acciones --}}
                                    @if($isAdmin)
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        @if($c->estado === 'Pendiente')
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button"
                                                        onclick="openEditModal({{ $c->id }}, '{{ $c->monto }}', '{{ addslashes($c->concepto ?? '') }}', '{{ addslashes($c->notas ?? '') }}')"
                                                        class="w-7 h-7 rounded-lg flex items-center justify-center text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer" title="Editar">
                                                    <i class="fas fa-pen text-[10px]"></i>
                                                </button>
                                                <button type="button"
                                                        onclick="cancelarComision({{ $c->id }})"
                                                        class="w-7 h-7 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors cursor-pointer" title="Cancelar">
                                                    <i class="fas fa-ban text-[10px]"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-slate-300 text-[11px]">—</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $isAdmin ? 8 : 7 }}" class="px-4 py-6 text-center text-slate-400">
                                        No hay comisiones registradas en esta semana.
                                    </td>
                                </tr>
                                @endforelse
                                <tr id="empty-filter-{{ $sem['key'] }}" class="hidden">
                                    <td colspan="{{ $isAdmin ? 8 : 7 }}" class="px-4 py-8 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                                <i class="fas fa-filter text-sm"></i>
                                            </div>
                                            <p class="font-bold text-slate-700 text-xs">No hay comisiones para este vendedor en esta semana.</p>
                                            <button type="button" onclick="limpiarFiltroVendedorSemana('{{ $sem['key'] }}')" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline inline-flex items-center gap-1 cursor-pointer">
                                                <i class="fas fa-rotate-left text-[10px]"></i> Mostrar todas las comisiones
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

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

    function toggleDesgloseSemana(key) {
        const desglose = document.getElementById(`desglose-semana-${key}`);
        const chevron  = document.getElementById(`icon-chevron-${key}`);
        if (!desglose) return;

        desglose.classList.toggle('hidden');
        if (chevron) {
            chevron.classList.toggle('rotate-180');
        }
    }

    function formatNum(n) {
        return parseFloat(n).toLocaleString('es-SV', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // ── Filtro de Comisiones por Vendedor en Resumen Semanal ─────────────
    const filtrosActivosSemana = {};

    function toggleFiltroVendedorSemana(semanaKey, vendedorId, nombreVendedor) {
        if (filtrosActivosSemana[semanaKey] === vendedorId) {
            limpiarFiltroVendedorSemana(semanaKey);
        } else {
            aplicarFiltroVendedorSemana(semanaKey, vendedorId, nombreVendedor);
        }
    }

    function aplicarFiltroVendedorSemana(semanaKey, vendedorId, nombreVendedor) {
        filtrosActivosSemana[semanaKey] = vendedorId;

        // Tarjetas de vendedor de esa semana
        const cards = document.querySelectorAll(`.vendedor-card-${semanaKey}`);
        cards.forEach(card => {
            const cardVendedorId = parseInt(card.dataset.vendedorCard);
            const badge = card.querySelector('.badge-filtro');
            const hint  = card.querySelector('.icon-filtro-hint');

            if (cardVendedorId === vendedorId) {
                card.classList.add('ring-2', 'ring-blue-600', 'border-blue-500', 'bg-blue-50/40', 'shadow-sm');
                card.classList.remove('border-slate-200', 'opacity-50');
                if (badge) badge.classList.remove('hidden');
                if (hint)  hint.classList.add('hidden');
            } else {
                card.classList.remove('ring-2', 'ring-blue-600', 'border-blue-500', 'bg-blue-50/40', 'shadow-sm');
                card.classList.add('border-slate-200', 'opacity-50');
                if (badge) badge.classList.add('hidden');
                if (hint)  hint.classList.remove('hidden');
            }
        });

        // Filas de la tabla de esa semana
        const filas = document.querySelectorAll(`.fila-comision-${semanaKey}`);
        let visibles = 0;
        filas.forEach(fila => {
            const filaVendedorId = parseInt(fila.dataset.vendedorId);
            if (filaVendedorId === vendedorId) {
                fila.classList.remove('hidden');
                visibles++;
            } else {
                fila.classList.add('hidden');
            }
        });

        // Fila vacía
        const emptyRow = document.getElementById(`empty-filter-${semanaKey}`);
        if (emptyRow) {
            if (visibles === 0) {
                emptyRow.classList.remove('hidden');
            } else {
                emptyRow.classList.add('hidden');
            }
        }

        // Indicador de filtro activo en la cabecera
        const badgeFiltro = document.getElementById(`filtro-badge-${semanaKey}`);
        const nombreFiltro = document.getElementById(`filtro-nombre-${semanaKey}`);
        const btnLimpiar = document.getElementById(`btn-limpiar-filtro-${semanaKey}`);

        if (badgeFiltro && nombreFiltro) {
            nombreFiltro.textContent = `${nombreVendedor} (${visibles})`;
            badgeFiltro.classList.remove('hidden');
        }
        if (btnLimpiar) {
            btnLimpiar.classList.remove('hidden');
        }
    }

    function limpiarFiltroVendedorSemana(semanaKey) {
        delete filtrosActivosSemana[semanaKey];

        // Restaurar tarjetas de vendedor
        const cards = document.querySelectorAll(`.vendedor-card-${semanaKey}`);
        cards.forEach(card => {
            card.classList.remove('ring-2', 'ring-blue-600', 'border-blue-500', 'bg-blue-50/40', 'shadow-sm', 'opacity-50');
            card.classList.add('border-slate-200');
            const badge = card.querySelector('.badge-filtro');
            const hint  = card.querySelector('.icon-filtro-hint');
            if (badge) badge.classList.add('hidden');
            if (hint)  hint.classList.remove('hidden');
        });

        // Mostrar todas las filas
        const filas = document.querySelectorAll(`.fila-comision-${semanaKey}`);
        filas.forEach(fila => fila.classList.remove('hidden'));

        // Ocultar fila vacía
        const emptyRow = document.getElementById(`empty-filter-${semanaKey}`);
        if (emptyRow) emptyRow.classList.add('hidden');

        // Ocultar indicadores de filtro
        const badgeFiltro = document.getElementById(`filtro-badge-${semanaKey}`);
        const btnLimpiar  = document.getElementById(`btn-limpiar-filtro-${semanaKey}`);
        if (badgeFiltro) badgeFiltro.classList.add('hidden');
        if (btnLimpiar)  btnLimpiar.classList.add('hidden');
    }

    // ── Funciones de Carrusel Semanal ─────────────────────────────────────
    function scrollCarruselSemana(semanaKey, delta) {
        const track = document.getElementById(`carrusel-track-${semanaKey}`);
        if (track) {
            track.scrollBy({ left: delta, behavior: 'smooth' });
        }
    }

    function buscarEnCarruselSemana(semanaKey, query) {
        const q = query.trim().toLowerCase();
        const cards = document.querySelectorAll(`.vendedor-card-${semanaKey}`);
        let visibles = 0;
        cards.forEach(card => {
            const nombre = card.dataset.vendedorNombre || '';
            if (!q || nombre.includes(q)) {
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
            const res = await fetch(`${ROUTES.pendientes}/${vendedorId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            loadingBox.classList.add('hidden');

            if (data.success && data.cantidad > 0) {
                document.getElementById('liquidarTotalMonto').textContent = '$' + formatNum(Math.max(0, Number(data.total)));
                document.getElementById('liquidarCountBadge').textContent = `${data.cantidad} comisiones pendientes`;
                if (btnSubmit) btnSubmit.disabled = Number(data.total) <= 0;

                let html = '';
                data.comisiones.forEach(c => {
                    const monto = Number(c.monto);
                    const esNegativo = monto < 0;
                    const montoTexto = esNegativo ? '-$' + formatNum(Math.abs(monto)) : '$' + formatNum(monto);
                    html += `
                        <label class="flex items-center justify-between p-2 bg-white rounded-lg border ${esNegativo ? 'border-rose-200 bg-rose-50/40 cursor-not-allowed' : 'border-emerald-100 hover:bg-emerald-50/50 cursor-pointer'} text-xs">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <input type="checkbox" name="comisiones_ids[]" value="${c.id}" checked ${esNegativo ? 'disabled' : ''} onchange="recalcularTotalSeleccionado()" class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="truncate font-medium text-slate-800">${c.concepto}${esNegativo ? ' · Ajuste obligatorio' : ''}</span>
                            </div>
                            <span class="font-extrabold ${esNegativo ? 'text-rose-700' : 'text-emerald-700'} ml-2 flex-shrink-0" data-monto="${monto}">${montoTexto}</span>
                        </label>
                    `;
                });
                lista.innerHTML = html;
                resumenBox.classList.remove('hidden');
                if (btnSubmit) btnSubmit.disabled = Number(data.total) <= 0;
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
            total = Math.max(0, total);
            document.getElementById('liquidarTotalMonto').textContent = '$' + formatNum(total);
            const btnSubmit = document.getElementById('btnConfirmLiquidar');
            if (btnSubmit) btnSubmit.disabled = total <= 0;
        document.getElementById('liquidarCountBadge').textContent = `${checkboxes.length} seleccionadas`;
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

    function openLiquidarModal() {
        document.getElementById('modalLiquidar').classList.remove('hidden');
        document.getElementById('formLiquidar').reset();
        if (typeof deseleccionarVendedorLiquidar === 'function') {
            deseleccionarVendedorLiquidar();
        }
        if (typeof handleMetodoPagoLiquidar === 'function') {
            handleMetodoPagoLiquidar('Efectivo');
        }
    }

    function closeLiquidarModal() {
        document.getElementById('modalLiquidar').classList.add('hidden');
        // Reset step wizard
        document.getElementById('liquidarStep2').classList.add('hidden');
        document.getElementById('formLiquidar').classList.remove('hidden');
        document.getElementById('stepProgressLine').style.width = '0%';
        document.getElementById('stepIndicator2').classList.add('opacity-35');
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
        const metodoBadge = metodoPago === 'Efectivo' ? 'Efectivo' : 'Transferencia Bancaria';
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
            const monto    = montoEl ? '$' + formatNum(parseFloat(montoEl.dataset.monto)) : '';
            html += `<div class="flex items-center justify-between px-3 py-2 bg-white rounded-xl border border-slate-100 text-xs">
                <span class="text-slate-700 font-medium truncate flex-1 pr-2">${concepto}</span>
                <span class="font-extrabold text-emerald-700 flex-shrink-0">${monto}</span>
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
</x-app>
