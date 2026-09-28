<x-app title="Comisiones por Vendedor | AXStore">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="max-w-[1440px] mx-auto space-y-6">

        <!-- HEADER DEL DASHBOARD -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-600/20">
                        <i class="fas fa-users-gear text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-indigo-600 mb-0.5">Analítica de Rendimiento</p>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                            Comisiones por Vendedor
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

        <!-- SUB-NAV / PESTAÑAS -->
        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones">
            <a href="{{ route('comisiones.index') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-coins text-slate-400"></i>
                <span>Comisiones</span>
            </a>
            <a href="{{ route('comisiones.porVendedor') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-users-gear"></i>
                <span>Comisiones por Vendedor</span>
            </a>
            @if($isAdmin)
            <a href="{{ route('ventas.index') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-chart-line text-slate-400"></i>
                <span>Dashboard de Ventas</span>
            </a>
            <a href="{{ route('ventas.pedidos') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-boxes-packing text-slate-400"></i>
                <span>Control de Envíos</span>
            </a>
            @endif
            <a href="{{ route('ventas.create') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-cash-register text-slate-400"></i>
                <span>Terminal de Ventas</span>
            </a>
        </nav>

        <!-- FILTROS GLOBALES -->
        <section class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
            <form method="GET" action="{{ route('comisiones.porVendedor') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3.5 items-end">

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

                {{-- Fecha Desde --}}
                <div class="{{ $isAdmin ? 'md:col-span-3' : 'md:col-span-5' }}">
                    <label for="fecha_desde" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-calendar-day text-slate-400 mr-1"></i> Desde
                    </label>
                    <input type="date" name="fecha_desde" id="fecha_desde" value="{{ $desde }}"
                           class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                </div>

                {{-- Fecha Hasta --}}
                <div class="{{ $isAdmin ? 'md:col-span-3' : 'md:col-span-5' }}">
                    <label for="fecha_hasta" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-calendar-check text-slate-400 mr-1"></i> Hasta
                    </label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ $hasta }}"
                           class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                </div>

                {{-- Botones --}}
                <div class="md:col-span-2 flex items-center gap-1.5">
                    <button type="submit"
                            class="w-full rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-2.5 text-xs transition-all shadow-md shadow-blue-600/15 flex items-center justify-center gap-1 cursor-pointer">
                        <i class="fas fa-filter text-[11px]"></i>
                        <span>Filtrar</span>
                    </button>
                    @if($desde || $hasta || $busqueda)
                    <a href="{{ route('comisiones.porVendedor') }}"
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
            <!-- TOTAL ACUMULADO -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Total Generado</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                        <i class="fas fa-vault"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">${{ number_format($kpis['total'], 2) }}</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>{{ $kpis['vendedores'] }} vendedores activos</span>
                    </p>
                </div>
            </div>

            <!-- POR PAGAR (PENDIENTE) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Total por Liquidar</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-amber-700 tracking-tight">${{ number_format($kpis['pendiente'], 2) }}</h3>
                    <p class="text-xs font-semibold text-amber-600 mt-1">Saldo pendiente acumulado</p>
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
                    <p class="text-xs font-semibold text-emerald-600 mt-1">Total transferido / entregado</p>
                </div>
            </div>

            <!-- CANCELADAS -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Canceladas</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">${{ number_format($kpis['cancelada'], 2) }}</h3>
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
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-200 hover:border-slate-300" id="card-vendedor-{{ $vendedor->id }}">
                
                <!-- HEADER DE LA TARJETA DEL VENDEDOR -->
                <div class="p-5 sm:p-6 bg-white flex flex-col lg:flex-row lg:items-center justify-between gap-5 border-b border-slate-100">
                    
                    <!-- INFORMACIÓN DEL VENDEDOR -->
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-lg flex items-center justify-center shadow-md shadow-blue-500/20 flex-shrink-0">
                            {{ strtoupper(substr($vendedor->nombre_real ?: $vendedor->username, 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="text-lg font-black text-slate-900 tracking-tight">{{ $vendedor->nombre_real ?: $vendedor->username }}</h2>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $vendedor->rol === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $vendedor->rol }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium mt-0.5 flex items-center gap-2">
                                <span><i class="fas fa-envelope text-slate-400 mr-1"></i>{{ $vendedor->username }}</span>
                                @if($vendedor->telefono)
                                <span><i class="fas fa-phone text-slate-400 mr-1"></i>{{ $vendedor->telefono }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- RESUMEN DE CIFRAS DEL VENDEDOR -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50/80 p-3.5 rounded-2xl border border-slate-100 flex-1 max-w-2xl">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Ganado</span>
                            <span class="text-base font-black text-slate-900">${{ number_format($item['total_comisiones'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block">Por Liquidar</span>
                            <span class="text-base font-black text-amber-700">${{ number_format($item['total_pendiente'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Pagado</span>
                            <span class="text-base font-black text-emerald-700">${{ number_format($item['total_pagada'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Ventas/Registros</span>
                            <span class="text-base font-black text-slate-700">{{ $item['total_ventas'] }}</span>
                        </div>
                    </div>

                    <!-- BOTONES DE ACCIÓN DE ESTE VENDEDOR -->
                    <div class="flex items-center gap-2 flex-wrap justify-end">
                        @if($isAdmin && $hasPendiente)
                        <button type="button" onclick="liquidarDirectoVendedor({{ $vendedor->id }})"
                                class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all cursor-pointer flex items-center gap-1.5">
                            <i class="fas fa-circle-check"></i>
                            <span>Liquidar (${{ number_format($item['total_pendiente'], 2) }})</span>
                        </button>
                        @endif

                        <button type="button" onclick="toggleDesgloseVendedor({{ $vendedor->id }})"
                                class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5">
                            <i class="fas fa-list-ul text-slate-500"></i>
                            <span>Desglose de Ventas ({{ $comisiones->count() }})</span>
                            <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" id="icon-chevron-{{ $vendedor->id }}"></i>
                        </button>
                    </div>
                </div>

                <!-- DESGLOSE EXPANDIBLE DE TODAS LAS VENTAS / COMISIONES -->
                <div id="desglose-vendedor-{{ $vendedor->id }}" class="hidden bg-slate-50/40 p-4 sm:p-5 border-t border-slate-100">
                    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-2xs">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-bold">
                                    <th class="px-4 py-3 text-left">Fecha y Hora</th>
                                    <th class="px-4 py-3 text-left">Concepto / Detalle de la Venta</th>
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
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    
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
                                                    <i class="fas fa-award"></i> Bono Manual
                                                </span>
                                            @endif
                                            <span class="font-semibold text-slate-800">
                                                {{ $c->concepto ?? ($c->salida ? "Venta directa ({$c->salida->cantidad} uds)" : "Comisión directa") }}
                                            </span>
                                        </div>
                                        @if($c->salida && $c->salida->variante && $c->salida->variante->producto)
                                            <p class="text-[11px] text-slate-500 mt-0.5">
                                                {{ $c->salida->variante->producto->nombre }} - {{ $c->salida->variante->nombre_variante }} | Total salida: ${{ number_format($c->salida->total, 2) }}
                                            </p>
                                        @endif
                                    </td>

                                    {{-- Comisión --}}
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="font-black text-sm text-slate-900 block">${{ number_format($c->monto, 2) }}</span>
                                        @if($c->porcentaje && $c->porcentaje > 0)
                                            <span class="text-[10px] text-slate-400">Ref: ${{ number_format($c->porcentaje, 2) }}/ud</span>
                                        @endif
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

                                    {{-- Método de Pago --}}
                                    <td class="px-4 py-3 text-slate-600 text-[11px]">
                                        @if($c->estado === 'Pagada')
                                            <span class="font-bold text-emerald-700 flex items-center gap-1">
                                                <i class="fas fa-money-bill-transfer"></i> {{ $c->metodo_pago ?? 'Efectivo' }}
                                            </span>
                                            @if($c->referencia_pago)
                                                <span class="text-[10px] text-slate-400 font-mono block">Ref: {{ $c->referencia_pago }}</span>
                                            @endif
                                            @if($c->comprobante_url)
                                                <a href="{{ $c->comprobante_url }}" target="_blank" class="text-[10px] text-blue-600 hover:underline inline-flex items-center gap-1 mt-0.5">
                                                    <i class="fas fa-paperclip"></i> Ver comprobante
                                                </a>
                                            @endif
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
                                    <td colspan="{{ $isAdmin ? 7 : 6 }}" class="px-4 py-6 text-center text-slate-400">
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
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl mx-auto mb-3 text-slate-400">
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
        index:      "{{ route('comisiones.index') }}",
        pendientes: "{{ url('comisiones/pendientes') }}",
        store:      @if($isAdmin) "{{ route('comisiones.store') }}" @else "null" @endif,
        update:     @if($isAdmin) "{{ url('comisiones') }}" @else "null" @endif,
        liquidar:   @if($isAdmin) "{{ route('comisiones.liquidar') }}" @else "null" @endif,
        cancelar:   @if($isAdmin) "{{ url('comisiones') }}" @else "null" @endif,
    };
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    function toggleDesgloseVendedor(id) {
        const desglose = document.getElementById(`desglose-vendedor-${id}`);
        const chevron  = document.getElementById(`icon-chevron-${id}`);
        if (!desglose) return;

        desglose.classList.toggle('hidden');
        if (chevron) {
            chevron.classList.toggle('rotate-180');
        }
    }

    function liquidarDirectoVendedor(vendedorId) {
        openLiquidarModal();
        const selectVendedor = document.getElementById('liquidarVendedor');
        if (selectVendedor) {
            selectVendedor.value = vendedorId;
            cargarPendientesVendedor(vendedorId);
        }
    }

    function formatNum(n) {
        return parseFloat(n).toLocaleString('es-SV', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
                document.getElementById('liquidarTotalMonto').textContent = '$' + formatNum(data.total);
                document.getElementById('liquidarCountBadge').textContent = `${data.cantidad} comisiones pendientes`;

                let html = '';
                data.comisiones.forEach(c => {
                    html += `
                        <label class="flex items-center justify-between p-2 bg-white rounded-lg border border-emerald-100 text-xs hover:bg-emerald-50/50 cursor-pointer">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <input type="checkbox" name="comisiones_ids[]" value="${c.id}" checked onchange="recalcularTotalSeleccionado()" class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="truncate font-medium text-slate-800">${c.concepto}</span>
                            </div>
                            <span class="font-extrabold text-emerald-700 ml-2 flex-shrink-0" data-monto="${c.monto}">$${formatNum(c.monto)}</span>
                        </label>
                    `;
                });
                lista.innerHTML = html;
                resumenBox.classList.remove('hidden');
                if (btnSubmit) btnSubmit.disabled = false;
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
        document.getElementById('liquidarTotalMonto').textContent = '$' + formatNum(total);
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
        document.getElementById('liquidarResumenBox').classList.add('hidden');
        document.getElementById('liquidarEmptyBox').classList.add('hidden');
    }

    function closeLiquidarModal() {
        document.getElementById('modalLiquidar').classList.add('hidden');
    }

    document.getElementById('formLiquidar')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const vendedorId = document.getElementById('liquidarVendedor').value;

        if (!vendedorId) {
            Swal.fire({ icon: 'warning', title: 'Selecciona un vendedor', showConfirmButton: false, timer: 1800 });
            return;
        }

        const formData = new FormData();
        formData.append('id_vendedor', vendedorId);
        formData.append('metodo_pago', document.getElementById('liquidarMetodoPago').value);
        formData.append('referencia_pago', document.getElementById('liquidarReferencia').value || '');
        formData.append('notas', document.getElementById('liquidarNotas').value || '');

        const comprobante = document.getElementById('liquidarComprobante').files[0];
        if (comprobante) {
            formData.append('comprobante_pago', comprobante);
        }

        const checkboxes = document.querySelectorAll('input[name="comisiones_ids[]"]:checked');
        checkboxes.forEach(cb => {
            formData.append('comisiones_ids[]', cb.value);
        });

        try {
            const res = await fetch(ROUTES.liquidar, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                body:    formData,
            });
            const data = await res.json();
            if (data.success) {
                closeLiquidarModal();
                Swal.fire({ icon: 'success', title: '¡Liquidación Completada!', text: data.message, timer: 2500, showConfirmButton: false });
                setTimeout(() => window.location.reload(), 1200);
            } else {
                Swal.fire({ icon: 'warning', title: 'Sin cambios', text: data.message });
            }
        } catch(e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo procesar la liquidación.' });
        }
    });

    // ── Crear manual / Bono (admin) ──────────────────────────────────────

    function openCreateModal() {
        document.getElementById('modalCreate').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('modalCreate').classList.add('hidden');
        document.getElementById('formCreate').reset();
    }

    function setConcepto(texto) {
        document.getElementById('createConcepto').value = texto;
    }

    document.getElementById('formCreate')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const body = {
            id_vendedor: document.getElementById('createVendedor').value,
            concepto:    document.getElementById('createConcepto').value,
            monto:       document.getElementById('createMonto').value,
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
