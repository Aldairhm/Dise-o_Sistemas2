<x-app title="Mis Ventas | AXStore">
    <div x-data="gestorMisVentas()" class="space-y-6">

        <!-- ENCABEZADO PRINCIPAL -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                    <a href="/home" class="hover:text-blue-600 transition-colors">Inicio</a>
                    <span>/</span>
                    <span class="text-blue-600">Mis Ventas</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-base shadow-md shadow-blue-500/20">
                        <i class="fas fa-boxes-packing"></i>
                    </div>
                    <span>Mis Ventas</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Historial de ventas completadas y seguimiento de tus pedidos en curso.
                </p>
            </div>

        </div>

        <!-- TARJETAS DE MÉTRICAS RÁPIDAS (COHERENTES CON EL MÓDULO DE VENTAS) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
            <!-- 1. TOTAL VENDIDO (ADMIN) / TOTAL PEDIDOS (VENDEDOR) -->
            @if(Auth::check() && Auth::user()->rol === 'admin')
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-2xs relative overflow-hidden flex flex-col justify-between hover:border-blue-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        Total Vendido
                    </span>
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xs">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-xl sm:text-2xl font-black text-blue-600 tracking-tight" x-text="'$' + (metricasActuales.total_ventas || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                        ${{ number_format($metricas['total_monto'] ?? 0, 2) }}
                    </span>
                    <span class="block text-[11px] text-slate-400 mt-0.5 font-medium">
                        Monto en <strong class="text-slate-600" x-text="(metricasActuales.cantidad || 0)">{{ $metricas['total_ordenes'] ?? 0 }}</strong> órdenes registradas
                    </span>
                </div>
            </div>
            @else
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-2xs relative overflow-hidden flex flex-col justify-between hover:border-blue-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        Total Pedidos
                    </span>
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xs">
                        <i class="fas fa-boxes-packing"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-xl sm:text-2xl font-black text-blue-600 tracking-tight" x-text="(metricasActuales.cantidad || 0)">
                        {{ $metricas['total_ordenes'] ?? 0 }}
                    </span>
                    <span class="block text-[11px] text-slate-400 mt-0.5 font-medium">
                        Órdenes registradas en tu historial
                    </span>
                </div>
            </div>
            @endif

            <!-- 2. VENTAS EN CURSO / EN RUTA -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-2xs relative overflow-hidden flex flex-col justify-between hover:border-amber-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">En Proceso / Ruta</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xs">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-xl sm:text-2xl font-black text-amber-600 tracking-tight">
                        {{ $metricas['total_en_curso'] ?? 0 }}
                    </span>
                    <span class="block text-[11px] text-slate-400 mt-0.5 font-medium">
                        Pendientes, Confirmadas o En camino
                    </span>
                </div>
            </div>

            <!-- 3. VENTAS ENTREGADAS -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-2xs relative overflow-hidden flex flex-col justify-between hover:border-emerald-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Historial Entregadas</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-xl sm:text-2xl font-black text-emerald-600 tracking-tight">
                        {{ $metricas['total_entregadas'] ?? 0 }}
                    </span>
                    <span class="block text-[11px] text-slate-400 mt-0.5 font-medium">
                        Ventas completadas con éxito
                    </span>
                </div>
            </div>

            <!-- 4. TICKET PROMEDIO (ADMIN) / EFECTIVIDAD DE ENTREGA (VENDEDOR) -->
            @if(Auth::check() && Auth::user()->rol === 'admin')
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-2xs relative overflow-hidden flex flex-col justify-between hover:border-purple-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Ticket Promedio</span>
                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xs">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-xl sm:text-2xl font-black text-purple-600 tracking-tight" x-text="'$' + ((metricasActuales.cantidad || 0) > 0 ? (metricasActuales.total_ventas / metricasActuales.cantidad) : 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                        ${{ number_format($metricas['ticket_promedio'] ?? 0, 2) }}
                    </span>
                    <span class="block text-[11px] text-slate-400 mt-0.5 font-medium">
                        Promedio facturado por cliente
                    </span>
                </div>
            </div>
            @else
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-2xs relative overflow-hidden flex flex-col justify-between hover:border-purple-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Efectividad de Entrega</span>
                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xs">
                        <i class="fas fa-bullseye"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-xl sm:text-2xl font-black text-purple-600 tracking-tight">
                        {{ $metricas['tasa_efectividad'] ?? 0 }}%
                    </span>
                    <span class="block text-[11px] text-slate-400 mt-0.5 font-medium">
                        {{ $metricas['total_entregadas'] ?? 0 }} de {{ $metricas['total_ordenes'] ?? 0 }} entregadas con éxito
                    </span>
                </div>
            </div>
            @endif
        </div>

        <!-- 1. BARRA DE FILTROS Y BÚSQUEDA -->
        <section class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
            <form method="GET" action="{{ route('ventas.mis-ventas') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
                
                <!-- BÚSQUEDA RÁPIDA -->
                <div class="lg:col-span-6">
                    <label for="q" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        <i class="fas fa-magnifying-glass text-slate-400 mr-1"></i> Buscar por Folio / Teléfono / Notas
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            name="q" 
                            id="q" 
                            value="{{ $busqueda }}" 
                            placeholder="Ej: 00004 o 7123-4567..." 
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 pl-9 pr-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all"
                        >
                        <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <!-- FECHA DESDE -->
                <div class="lg:col-span-2">
                    <label for="desde" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        <i class="fas fa-calendar-day text-slate-400 mr-1"></i> Desde
                    </label>
                    <input 
                        type="date" 
                        name="desde" 
                        id="desde" 
                        value="{{ $desde }}" 
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all"
                    >
                </div>

                <!-- FECHA HASTA -->
                <div class="lg:col-span-2">
                    <label for="hasta" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        <i class="fas fa-calendar-check text-slate-400 mr-1"></i> Hasta
                    </label>
                    <input 
                        type="date" 
                        name="hasta" 
                        id="hasta" 
                        value="{{ $hasta }}" 
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all"
                    >
                </div>

                <!-- BOTONES -->
                <div class="lg:col-span-2 flex items-center gap-2">
                    <button 
                        type="submit" 
                        class="flex-1 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-3 text-xs transition-all shadow-md shadow-blue-600/15 flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                        <i class="fas fa-filter"></i>
                        <span>Filtrar</span>
                    </button>
                    @if(request()->hasAny(['desde', 'hasta', 'q']))
                        <a 
                            href="{{ route('ventas.mis-ventas') }}" 
                            class="rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold py-2 px-3 text-xs transition-colors flex items-center justify-center"
                            title="Limpiar filtros"
                        >
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>

            </form>
        </section>

        <!-- 2. PESTAÑAS DE APARTADOS POR ESTADO (EN CURSO vs HISTORIAL) -->
        <div class="flex flex-wrap items-center gap-1.5 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Apartados de estado">
            <!-- 1. TODOS -->
            <button 
                type="button" 
                @click="cambiarTab('todos')"
                class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                :class="tabActiva === 'todos' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
            >
                <i class="fas fa-layer-group text-[11px]" :class="tabActiva === 'todos' ? 'text-white' : 'text-slate-400'"></i>
                <span>Todas</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black"
                      :class="tabActiva === 'todos' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                    {{ $conteoEstados['todos'] }}
                </span>
            </button>

            <!-- 2. PENDIENTES -->
            <button 
                type="button" 
                @click="cambiarTab('Pendiente')"
                class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                :class="tabActiva === 'Pendiente' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-700'"
            >
                <i class="fas fa-clock text-[11px]" :class="tabActiva === 'Pendiente' ? 'text-white' : 'text-amber-500'"></i>
                <span>Pendientes</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black"
                      :class="tabActiva === 'Pendiente' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800'">
                    {{ $conteoEstados['Pendiente'] }}
                </span>
            </button>

            <!-- 3. CONFIRMADAS -->
            <button 
                type="button" 
                @click="cambiarTab('Confirmada')"
                class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                :class="tabActiva === 'Confirmada' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700'"
            >
                <i class="fas fa-circle-check text-[11px]" :class="tabActiva === 'Confirmada' ? 'text-white' : 'text-blue-500'"></i>
                <span>Confirmadas</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black"
                      :class="tabActiva === 'Confirmada' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800'">
                    {{ $conteoEstados['Confirmada'] }}
                </span>
            </button>

            <!-- 4. EN RUTA -->
            <button 
                type="button" 
                @click="cambiarTab('En ruta')"
                class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                :class="tabActiva === 'En ruta' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700'"
            >
                <i class="fas fa-truck-fast text-[11px]" :class="tabActiva === 'En ruta' ? 'text-white' : 'text-indigo-500'"></i>
                <span>En ruta</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black"
                      :class="tabActiva === 'En ruta' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800'">
                    {{ $conteoEstados['En ruta'] }}
                </span>
            </button>

            <!-- 5. HISTORIAL ENTREGADAS -->
            <button 
                type="button" 
                @click="cambiarTab('Entregada')"
                class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                :class="tabActiva === 'Entregada' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700'"
            >
                <i class="fas fa-box-open text-[11px]" :class="tabActiva === 'Entregada' ? 'text-white' : 'text-emerald-500'"></i>
                <span>Historial (Entregadas)</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black"
                      :class="tabActiva === 'Entregada' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800'">
                    {{ $conteoEstados['Entregada'] }}
                </span>
            </button>

            <!-- 6. CANCELADAS -->
            <button 
                type="button" 
                @click="cambiarTab('Cancelada')"
                class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                :class="tabActiva === 'Cancelada' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:bg-rose-50 hover:text-rose-700'"
            >
                <i class="fas fa-ban text-[11px]" :class="tabActiva === 'Cancelada' ? 'text-white' : 'text-rose-500'"></i>
                <span>Canceladas</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black"
                      :class="tabActiva === 'Cancelada' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-800'">
                    {{ $conteoEstados['Cancelada'] ?? 0 }}
                </span>
            </button>

            <!-- 7. DEVOLUCIÓN -->
            <button 
                type="button" 
                @click="cambiarTab('Devolución')"
                class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                :class="tabActiva === 'Devolución' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:bg-purple-50 hover:text-purple-700'"
            >
                <i class="fas fa-rotate-left text-[11px]" :class="tabActiva === 'Devolución' ? 'text-white' : 'text-purple-500'"></i>
                <span>Devoluciones</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black"
                      :class="tabActiva === 'Devolución' ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-800'">
                    {{ $conteoEstados['Devolución'] ?? 0 }}
                </span>
            </button>

            <!-- 8. CAMBIO -->
            <button 
                type="button" 
                @click="cambiarTab('Cambio')"
                class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                :class="tabActiva === 'Cambio' ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-700'"
            >
                <i class="fas fa-arrow-right-arrow-left text-[11px]" :class="tabActiva === 'Cambio' ? 'text-white' : 'text-teal-500'"></i>
                <span>Cambios</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black"
                      :class="tabActiva === 'Cambio' ? 'bg-white/20 text-white' : 'bg-teal-100 text-teal-800'">
                    {{ $conteoEstados['Cambio'] ?? 0 }}
                </span>
            </button>
        </div>

        <!-- 3. TABLA DEL APARTADO SELECCIONADO -->
        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-5 border-b border-slate-100 bg-slate-50/60">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm shadow-xs"
                         :class="{
                             'bg-blue-100 text-blue-700': tabActiva === 'todos',
                             'bg-amber-100 text-amber-700': tabActiva === 'Pendiente',
                             'bg-blue-100 text-blue-700': tabActiva === 'Confirmada',
                             'bg-indigo-100 text-indigo-700': tabActiva === 'En ruta',
                             'bg-emerald-100 text-emerald-700': tabActiva === 'Entregada',
                             'bg-rose-100 text-rose-700': tabActiva === 'Cancelada',
                             'bg-purple-100 text-purple-700': tabActiva === 'Devolución',
                             'bg-teal-100 text-teal-700': tabActiva === 'Cambio'
                         }">
                        <i class="fas" :class="{
                            'fa-layer-group': tabActiva === 'todos',
                            'fa-clock': tabActiva === 'Pendiente',
                            'fa-circle-check': tabActiva === 'Confirmada',
                            'fa-truck-fast': tabActiva === 'En ruta',
                            'fa-box-open': tabActiva === 'Entregada',
                            'fa-ban': tabActiva === 'Cancelada',
                            'fa-rotate-left': tabActiva === 'Devolución',
                            'fa-arrow-right-arrow-left': tabActiva === 'Cambio'
                        }"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <span>Apartado:</span>
                            <span class="text-blue-600" x-text="nombreTabActual()"></span>
                        </h2>
                        <p class="text-xs text-slate-500" x-text="descripcionTabActual()"></p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-600 bg-white border border-slate-200 px-3 py-1.5 rounded-xl shadow-2xs">
                        Mostrando <strong class="text-blue-600" x-text="conteoFiltradas()"></strong> ventas
                    </span>
                </div>
            </div>

            <!-- TABLA RESPONSIVA -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[1000px]">
                    <thead class="bg-slate-50/80 text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5 w-24 whitespace-nowrap">Folio</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Fecha y Hora</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Tipo y Destino</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Método de Pago</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Estado</th>
                            <th class="px-4 py-3.5 text-right whitespace-nowrap w-24">Total</th>
                            <th class="px-5 py-3.5 text-right whitespace-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($todasVentas as $venta)
                            @php
                                $estado = $venta->estado ?? 'Pendiente';
                                $tipo = $venta->tipo_venta ?? 'Tienda';
                                $puedeCancelar = in_array($estado, ['Pendiente', 'Confirmada']);

                                $badgeClasses = match($estado) {
                                    'Pendiente' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'Confirmada' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'En ruta' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'Entregada', 'Entregado' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Cancelada' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'Devolución' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'Cambio' => 'bg-teal-50 text-teal-700 border-teal-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-200'
                                };

                                $badgeIcon = match($estado) {
                                    'Pendiente' => 'fa-clock',
                                    'Confirmada' => 'fa-circle-check',
                                    'En ruta' => 'fa-truck-fast',
                                    'Entregada', 'Entregado' => 'fa-box-open',
                                    'Cancelada' => 'fa-ban',
                                    'Devolución' => 'fa-rotate-left',
                                    'Cambio' => 'fa-arrow-right-arrow-left',
                                    default => 'fa-info-circle'
                                };
                            @endphp

                            <tr 
                                x-show="ventaVisible('{{ $estado }}')" 
                                class="hover:bg-slate-50/70 transition-colors"
                            >
                                <!-- FOLIO -->
                                <td class="px-5 py-3.5 font-bold text-slate-900">
                                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[11px] font-mono">
                                        #VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>

                                <!-- FECHA Y HORA -->
                                <td class="px-4 py-3.5 text-slate-600">
                                    <p class="font-bold text-slate-800">
                                        {{ \Carbon\Carbon::parse($venta->fecha)->format('d-m-Y') }}
                                    </p>
                                    <span class="text-[10px] text-slate-400">
                                        {{ \Carbon\Carbon::parse($venta->fecha)->format('h:i A') }}
                                    </span>
                                </td>

                                <!-- TIPO Y DESTINO -->
                                <td class="px-4 py-3.5">
                                    <div class="space-y-1">
                                        @if($tipo === 'Envio')
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    <i class="fas fa-truck text-[9px]"></i> Entrega a Domicilio
                                                </span>
                                                @if(!empty($venta->nombre_cliente))
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-50 text-blue-800 border border-blue-200" title="Cliente">
                                                        <i class="fas fa-user text-[8px] text-blue-600"></i> {{ $venta->nombre_cliente }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($venta->direccion_entrega)
                                                <p class="text-[11px] text-slate-700 font-medium truncate max-w-[220px]" title="{{ $venta->direccion_entrega }}">
                                                    {{ $venta->direccion_entrega }}
                                                </p>
                                            @endif
                                            @if($venta->telefono_entrega)
                                                <p class="text-[10px] text-slate-400 flex items-center gap-1 font-mono">
                                                    <i class="fas fa-phone text-[8px]"></i> {{ $venta->telefono_entrega }}
                                                </p>
                                            @endif
                                        @else
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                @if($venta->metodo_pago === 'Cambio Físico')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200" title="Venta de reposición por cambio físico">
                                                        <i class="fas fa-arrow-right-arrow-left text-[9px] text-teal-600"></i> Cambio en Tienda
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <i class="fas fa-store text-[9px]"></i> Venta en Tienda
                                                    </span>
                                                @endif
                                                @if(!empty($venta->nombre_cliente))
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200" title="Cliente">
                                                        <i class="fas fa-user text-[8px]"></i> {{ $venta->nombre_cliente }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- MÉTODO DE PAGO -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $venta->metodo_pago === 'Cambio Físico' ? 'bg-teal-50 text-teal-800 border-teal-200' : ($venta->metodo_pago === 'Efectivo' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($venta->metodo_pago === 'Transferencia Bancaria' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-blue-50 text-blue-700 border-blue-200')) }}">
                                        <i class="fas {{ $venta->metodo_pago === 'Cambio Físico' ? 'fa-arrow-right-arrow-left text-teal-600' : ($venta->metodo_pago === 'Efectivo' ? 'fa-money-bill-wave' : ($venta->metodo_pago === 'Transferencia Bancaria' ? 'fa-building-columns' : 'fa-credit-card')) }} text-[10px]"></i>
                                        <span>{{ $venta->metodo_pago }}</span>
                                    </span>
                                </td>

                                <!-- ESTADO ACTUAL CON EXPLICACIÓN -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $badgeClasses }}">
                                            <i class="fas {{ $badgeIcon }} text-[10px]"></i>
                                            <span>{{ $estado }}</span>
                                        </span>
                                        @if(in_array($estado, ['En ruta', 'Entregada', 'Entregado']))
                                            <span class="text-[9px] font-medium text-slate-400 mt-0.5 flex items-center gap-0.5" title="Estado protegido contra cancelaciones">
                                                <i class="fas fa-lock text-[8px]"></i> No cancelable
                                            </span>
                                        @elseif($puedeCancelar)
                                            <span class="text-[9px] font-medium text-amber-600 mt-0.5 flex items-center gap-0.5">
                                                <i class="fas fa-clock-rotate-left text-[8px]"></i> Cancelable
                                            </span>
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
                                        
                                        <!-- BOTÓN CANCELAR (SOLO ANTES DE EN RUTA) -->
                                        @if($puedeCancelar)
                                            <button 
                                                type="button" 
                                                @click="abrirModalCancelar({{ $venta->id }}, 'VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}', '{{ $estado }}')"
                                                class="rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-700 px-2.5 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                                title="Cancelar esta venta (disponible antes de que esté en ruta)"
                                            >
                                                <i class="fas fa-ban text-[10px]"></i>
                                                <span>Cancelar</span>
                                            </button>
                                        @else
                                            <span 
                                                class="rounded-lg border border-slate-200 bg-slate-50 text-slate-400 px-2 py-1 text-[11px] font-medium flex items-center gap-1 cursor-not-allowed shrink-0"
                                                title="No se puede modificar ni cancelar porque ya está en ruta o finalizada"
                                            >
                                                <i class="fas fa-lock text-[9px]"></i>
                                                <span>Bloqueada</span>
                                            </span>
                                        @endif

                                        <!-- BOTÓN DETALLE -->
                                        <button 
                                            type="button" 
                                            @click="abrirDetalle({{ $venta->id }})" 
                                            class="rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 px-2.5 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                            title="Ver artículos y desglose"
                                        >
                                            <i class="fas fa-eye text-slate-400 text-[10px]"></i>
                                            <span>Detalle</span>
                                        </button>

                                        <!-- BOTÓN IMPRIMIR DIRECTO TICKET 80MM -->
                                        <button 
                                            type="button" 
                                            @click="imprimirTicketDirecto({{ $venta->id }})" 
                                            class="rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 px-2.5 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                            title="Imprimir tirilla térmica de 80mm"
                                        >
                                            <i class="fas fa-print text-[10px]"></i>
                                            <span>Ticket</span>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 bg-white">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-lg mb-3">
                                            <i class="fas fa-inbox"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-700">Aún no tienes ventas registradas</h4>
                                        <p class="text-xs text-slate-400 mt-1">Tus ventas y pedidos realizados se mostrarán en este módulo.</p>
                                        <a href="{{ route('ventas.create') }}" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-600/20">
                                            Crear mi primera venta
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- MENSAJE VACÍO DINÁMICO CUANDO EL FILTRO DE PESTAÑA NO TIENE RESULTADOS -->
            <div 
                x-show="conteoFiltradas() === 0 && {{ $todasVentas->count() }} > 0" 
                class="text-center py-12 bg-white"
                style="display: none;"
            >
                <div class="flex flex-col items-center justify-center">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg mb-3">
                        <i class="fas fa-filter-circle-xmark"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-700">No hay ventas en este apartado</h4>
                    <p class="text-xs text-slate-400 mt-1">
                        Actualmente no tienes registros con el estado seleccionado.
                    </p>
                    <button 
                        type="button" 
                        @click="cambiarTab('todos')"
                        class="mt-3 text-xs font-bold text-blue-600 hover:underline cursor-pointer"
                    >
                        Ver todas mis ventas
                    </button>
                </div>
            </div>

        </section>

        <!-- ========================================================================= -->
        <!-- MODAL: CANCELACIÓN EXCLUSIVA PARA EL VENDEDOR                             -->
        <!-- ========================================================================= -->
        <div 
            x-show="modalCancelarAbierto" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <div 
                x-show="modalCancelarAbierto"
                x-transition.opacity
                class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                @click="cerrarModalCancelar()"
            ></div>

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
                <div 
                    x-show="modalCancelarAbierto"
                    x-transition
                    @click.away="cerrarModalCancelar()"
                    class="relative z-10 w-full max-w-lg transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-gray-100 flex flex-col my-8"
                >
                    <!-- CABECERA ROJA/ROSE -->
                    <div class="bg-rose-600 px-6 py-4.5 flex items-center justify-between text-white shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white text-sm shadow-xs">
                                <i class="fas fa-ban"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-black tracking-tight text-white flex items-center gap-2">
                                    <span>Cancelar Venta</span>
                                    <span class="font-mono text-rose-200 text-sm" x-text="cancelarModalData.folio"></span>
                                </h3>
                                <p class="text-xs text-rose-100 mt-0.5">
                                    Reintegrará el stock físico al inventario automáticamente
                                </p>
                            </div>
                        </div>

                        <button 
                            type="button" 
                            @click="cerrarModalCancelar()"
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center text-white transition-colors cursor-pointer"
                            title="Cerrar ventana"
                        >
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>

                    <!-- CUERPO -->
                    <div class="p-6 space-y-4 text-xs">
                        
                        <!-- ADVERTENCIA REGLA DE NEGOCIO -->
                        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-900 flex items-start gap-3">
                            <i class="fas fa-triangle-exclamation text-rose-600 text-base shrink-0 mt-0.5"></i>
                            <div class="space-y-1">
                                <p class="font-bold">Política de Cancelación de Vendedor</p>
                                <p class="text-[11px] text-rose-800 leading-relaxed">
                                    Como vendedor únicamente puedes cancelar ventas que se encuentren en estado <b>Pendiente</b> o <b>Confirmada</b>. Una vez que el pedido pasa a <b>En ruta</b> o es entregado, el estado queda bloqueado y no podrás alterarlo.
                                </p>
                            </div>
                        </div>

                        <!-- MOTIVO OBLIGATORIO -->
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Motivo de la Cancelación <span class="text-rose-500">* (Obligatorio)</span>
                            </label>
                            <textarea 
                                x-model="motivoCancelacion"
                                rows="3" 
                                placeholder="Indica detalladamente por qué se cancela la venta (ej. cliente desistió de la compra, falta de confirmación de pago...)"
                                class="w-full rounded-xl border border-slate-300 p-3 text-xs font-semibold focus:border-rose-500 focus:outline-none focus:ring-4 focus:ring-rose-500/10 transition-all resize-none"
                            ></textarea>
                            <p class="text-[10px] text-slate-400 mt-1">
                                Mínimo 5 caracteres. Este motivo quedará registrado en el historial de la venta y en las observaciones del inventario.
                            </p>
                        </div>

                    </div>

                    <!-- FOOTER -->
                    <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            @click="cerrarModalCancelar()"
                            class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs transition-colors cursor-pointer"
                        >
                            Volver
                        </button>
                        <button 
                            type="button" 
                            @click="confirmarCancelacion()"
                            :disabled="procesandoCancelacion || motivoCancelacion.trim().length < 5"
                            class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 disabled:opacity-50 text-white font-bold text-xs transition-all shadow-md shadow-rose-600/20 flex items-center gap-2 cursor-pointer"
                        >
                            <i class="fas fa-spinner fa-spin" x-show="procesandoCancelacion"></i>
                            <i class="fas fa-ban" x-show="!procesandoCancelacion"></i>
                            <span x-text="procesandoCancelacion ? 'Cancelando...' : 'Confirmar Cancelación'"></span>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL: DETALLE COMPLETO DE VENTA                                          -->
        <!-- ========================================================================= -->
        <div 
            x-show="modalDetalleAbierto" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <div 
                x-show="modalDetalleAbierto"
                x-transition.opacity
                class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                @click="if (!modalImprimirAbierto && !comprobanteZoomUrl) cerrarDetalle()"
            ></div>

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
                <div 
                    x-show="modalDetalleAbierto"
                    x-transition
                    @click.away="if (!modalImprimirAbierto && !comprobanteZoomUrl) cerrarDetalle()"
                    class="relative z-10 w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-gray-100 flex flex-col my-8 max-h-[92vh]"
                >
                    <!-- CABECERA AZUL -->
                    <div class="bg-blue-600 px-6 py-4.5 flex items-center justify-between text-white shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white text-sm shadow-xs">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black tracking-tight text-white flex items-center gap-2">
                                    <span>Detalle de Transacción</span>
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
                                          x-text="ventaSeleccionada?.estado || ''">
                                    </span>
                                </h3>
                                <p class="text-xs text-blue-100 mt-0.5" x-show="ventaSeleccionada">
                                    Emitida el <span x-text="formatearFechaModal(ventaSeleccionada?.fecha)"></span>
                                </p>
                            </div>
                        </div>

                        <button 
                            type="button" 
                            @click="cerrarDetalle()"
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center text-white transition-colors cursor-pointer"
                            title="Cerrar ventana"
                        >
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>

                    <!-- CUERPO -->
                    <div class="p-6 overflow-y-auto space-y-5 flex-1 custom-scrollbar text-xs">
                        <template x-if="cargandoDetalle">
                            <div class="py-12 text-center text-slate-400 flex flex-col items-center justify-center gap-2">
                                <i class="fas fa-circle-notch fa-spin text-2xl text-blue-600"></i>
                                <span class="font-bold">Cargando desglose de la venta...</span>
                            </div>
                        </template>

                        <template x-if="!cargandoDetalle && ventaSeleccionada">
                            <div class="space-y-5">
                                
                                <!-- DATOS GENERALES -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 border border-slate-200 rounded-xl p-4">
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Vendedor</span>
                                        <span class="font-bold text-slate-800" x-text="ventaSeleccionada.usuario?.nombre_real || ventaSeleccionada.usuario?.username || 'Tú'"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Método de Pago</span>
                                        <span class="font-bold text-slate-800" x-text="ventaSeleccionada.metodo_pago"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Tipo de Venta</span>
                                        <span class="font-bold text-slate-800" x-text="ventaSeleccionada.tipo_venta === 'Envio' ? 'Entrega a Domicilio' : 'Tienda'"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Estado Actual</span>
                                        <span class="font-black text-blue-600" x-text="ventaSeleccionada.estado"></span>
                                    </div>
                                </div>

                                <!-- SI TIENE DIRECCIÓN DE ENVÍO -->
                                <template x-if="ventaSeleccionada.direccion_entrega">
                                    <div class="p-3.5 bg-indigo-50/70 border border-indigo-200 rounded-xl space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] uppercase font-bold text-indigo-900 block">Datos del Envío:</span>
                                            <template x-if="ventaSeleccionada.nombre_cliente">
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-900 bg-white px-2 py-0.5 rounded-md border border-indigo-200 shadow-2xs">
                                                    <i class="fas fa-user text-indigo-600 text-[10px]"></i>
                                                    <span x-text="ventaSeleccionada.nombre_cliente"></span>
                                                </span>
                                            </template>
                                        </div>
                                        <p class="text-xs text-indigo-950 font-medium" x-text="ventaSeleccionada.direccion_entrega"></p>
                                        <template x-if="ventaSeleccionada.telefono">
                                            <p class="text-[11px] text-indigo-700 font-mono">
                                                Teléfono: <span x-text="ventaSeleccionada.telefono"></span>
                                            </p>
                                        </template>
                                    </div>
                                </template>

                                <!-- COMPROBANTE DE PAGO (SI APLICA) -->
                                <template x-if="ventaSeleccionada.comprobante_url">
                                    <div class="bg-gradient-to-r from-blue-50/70 via-indigo-50/50 to-slate-50 border border-blue-200/80 rounded-2xl p-4 shadow-2xs">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                                <i class="fas fa-file-invoice-dollar text-blue-600"></i>
                                                <span>Comprobante de Pago</span>
                                            </h4>
                                            <a :href="ventaSeleccionada.comprobante_url" target="_blank" class="text-xs font-bold text-blue-700 hover:underline">
                                                Abrir en pestaña nueva
                                            </a>
                                        </div>
                                        <div class="flex items-start gap-3 bg-white p-3 rounded-xl border border-blue-100">
                                            <img :src="ventaSeleccionada.comprobante_url" alt="Comprobante de pago" class="w-16 h-16 object-cover rounded-lg border">
                                            <div class="text-slate-600">
                                                <p class="font-bold text-slate-800">Comprobante de Transferencia Bancaria</p>
                                                <button type="button" @click="verImagenAmpliada(ventaSeleccionada.comprobante_url, 'Comprobante_Pago', 'Comprobante de Pago')" class="text-[11px] text-blue-600 font-bold hover:underline mt-1 block cursor-pointer">
                                                    Ampliar imagen
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- ARTÍCULOS VENDIDOS -->
                                <div>
                                    <h4 class="text-[11px] font-black uppercase tracking-wider text-slate-500 mb-2.5">
                                        Artículos de la Venta
                                    </h4>
                                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                                        <table class="w-full text-left text-xs">
                                            <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase border-b border-slate-200">
                                                <tr>
                                                    <th class="px-4 py-2.5">Producto / Variante</th>
                                                    <th class="px-3 py-2.5 text-center">Cant.</th>
                                                    <th class="px-3 py-2.5 text-right">P. Unit.</th>
                                                    <th class="px-4 py-2.5 text-right">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                <template x-for="item in (ventaSeleccionada.detalles || [])" :key="item.id">
                                                    <tr class="hover:bg-slate-50/50">
                                                        <td class="px-4 py-3">
                                                            <div class="font-bold text-slate-800" x-text="item.variante?.producto?.nombre || 'Producto'"></div>
                                                            <span class="text-[11px] text-slate-500" x-text="item.variante?.nombre_variante || ('SKU: ' + (item.variante?.sku || 'N/A'))"></span>
                                                        </td>
                                                        <td class="px-3 py-3 text-center font-bold text-slate-700" x-text="item.cantidad"></td>
                                                        <td class="px-3 py-3 text-right font-medium text-slate-600" x-text="'$' + parseFloat(item.precio_unitario).toFixed(2)"></td>
                                                        <td class="px-4 py-3 text-right font-black text-slate-900" x-text="'$' + parseFloat(item.subtotal).toFixed(2)"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TOTALES Y OBSERVACIONES -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                    <div class="space-y-2">
                                        <template x-if="ventaSeleccionada.observaciones">
                                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Notas / Observaciones</span>
                                                <p class="text-xs text-slate-700 font-medium" x-text="ventaSeleccionada.observaciones"></p>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="space-y-1.5 p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                                        <div class="flex justify-between text-slate-600" x-show="parseFloat(ventaSeleccionada.precio_envio || 0) > 0">
                                            <span>Costo de Envío:</span>
                                            <span class="font-bold" x-text="'$' + parseFloat(ventaSeleccionada.precio_envio).toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between text-slate-600" x-show="parseFloat(ventaSeleccionada.descuento_aplicado || 0) > 0">
                                            <span>Descuento:</span>
                                            <span class="font-bold text-rose-600" x-text="'-$' + parseFloat(ventaSeleccionada.descuento_aplicado).toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between text-slate-900 font-black text-sm pt-2 border-t border-slate-200">
                                            <span>Total Pagado:</span>
                                            <span class="text-blue-600" x-text="'$' + parseFloat(ventaSeleccionada.total).toFixed(2)"></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </template>
                    </div>

                    <!-- FOOTER -->
                    <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            @click="cerrarDetalle()"
                            class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer"
                        >
                            Cerrar
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL: VISOR DE IMÁGENES / EVIDENCIAS                                     -->
        <!-- ========================================================================= -->
        <div 
            x-show="comprobanteZoomUrl" 
            x-cloak
            class="fixed inset-0 z-[60] overflow-y-auto"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <div 
                x-show="comprobanteZoomUrl"
                x-transition.opacity
                class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity"
                @click="cerrarImagenAmpliada()"
            ></div>

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
                <div 
                    x-show="comprobanteZoomUrl" 
                    x-transition
                    class="relative z-10 max-w-3xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl border border-slate-200 text-left my-8"
                    @click.away="cerrarImagenAmpliada()"
                >
                    <div class="bg-blue-600 px-5 py-3.5 flex items-center justify-between text-white shadow-md">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-image text-white text-base"></i>
                            <span class="text-xs font-black uppercase tracking-wider text-white" x-text="comprobanteZoomTitulo || 'Evidencia de Venta'"></span>
                        </div>
                        <button type="button" @click="cerrarImagenAmpliada()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>

                    <div class="p-4 bg-slate-900/5 flex items-center justify-center max-h-[75vh] overflow-auto">
                        <img :src="comprobanteZoomUrl" alt="Comprobante" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-sm border border-slate-200">
                    </div>

                    <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-end">
                        <button type="button" @click="cerrarImagenAmpliada()" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- SCRIPT ALPINE.JS DE CONTROL -->
    <script>
        function gestorMisVentas() {
            return {
                tabActiva: '{{ $estadoTab ?? "todos" }}',
                conteoEstados: @json($conteoEstados),
                metricasPorTab: @json($metricas['por_tab'] ?? []),

                get metricasActuales() {
                    return this.metricasPorTab[this.tabActiva] || {
                        ganancia: 0,
                        comisiones: 0,
                        extras: 0,
                        total_ventas: 0,
                        cantidad: 0
                    };
                },

                // Modales
                modalDetalleAbierto: false,
                cargandoDetalle: false,
                ventaSeleccionada: null,
                comprobanteZoomUrl: null,
                comprobanteZoomTitulo: '',

                // Cancelación
                modalCancelarAbierto: false,
                cancelarModalData: {
                    id: null,
                    folio: '',
                    estado: ''
                },
                motivoCancelacion: '',
                procesandoCancelacion: false,

                cambiarTab(tab) {
                    this.tabActiva = tab;
                },

                nombreTabActual() {
                    const tabs = {
                        'todos': 'Todas mis ventas',
                        'Pendiente': 'Pendientes de confirmación',
                        'Confirmada': 'Confirmadas (Preparación)',
                        'En ruta': 'En ruta (Paquetería)',
                        'Entregada': 'Historial de ventas entregadas',
                        'Cancelada': 'Ventas canceladas',
                        'Devolución': 'Ventas con devolución registrada',
                        'Cambio': 'Ventas con cambio de producto'
                    };
                    return tabs[this.tabActiva] || this.tabActiva;
                },

                descripcionTabActual() {
                    const descripciones = {
                        'todos': 'Listado completo de todas tus ventas registradas en el sistema.',
                        'Pendiente': 'Ventas pendientes que puedes cancelar si el cliente lo requiere.',
                        'Confirmada': 'Ventas confirmadas en preparación para despacho.',
                        'En ruta': 'Pedidos enviados a paquetería o en camino al cliente (protegidos contra cambios).',
                        'Entregada': 'Historial de ventas completadas y entregadas exitosamente.',
                        'Cancelada': 'Ventas que fueron anuladas y cuyo inventario fue devuelto.',
                        'Devolución': 'Pedidos que registraron devolución física o monetaria por garantía.',
                        'Cambio': 'Órdenes procesadas como cambio físico de mercancía.'
                    };
                    return descripciones[this.tabActiva] || '';
                },

                ventaVisible(estado) {
                    if (this.tabActiva === 'todos') return true;
                    if (this.tabActiva === 'Entregada') {
                        return estado === 'Entregada' || estado === 'Entregado';
                    }
                    return this.tabActiva === estado;
                },

                conteoFiltradas() {
                    return this.conteoEstados[this.tabActiva] ?? 0;
                },

                // Detalle
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
                            throw new Error(data.message || 'Error al obtener la venta');
                        }
                    } catch (e) {
                        console.error('Error al cargar detalle:', e);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo cargar la información de la venta.',
                                customClass: { popup: 'swal-axstore' }
                            });
                        }
                        this.cerrarDetalle();
                    } finally {
                        this.cargandoDetalle = false;
                    }
                },

                cerrarDetalle() {
                    this.modalDetalleAbierto = false;
                    this.ventaSeleccionada = null;
                },

                formatearFechaModal(fecha) {
                    if (!fecha) return '';
                    try {
                        const d = new Date(fecha);
                        if (isNaN(d.getTime())) return fecha;
                        const dia = String(d.getDate()).padStart(2, '0');
                        const mes = String(d.getMonth() + 1).padStart(2, '0');
                        const anio = d.getFullYear();
                        let horas = d.getHours();
                        const minutos = String(d.getMinutes()).padStart(2, '0');
                        const ampm = horas >= 12 ? 'PM' : 'AM';
                        horas = horas % 12;
                        horas = horas ? horas : 12;
                        return `${dia}-${mes}-${anio} ${String(horas).padStart(2, '0')}:${minutos} ${ampm}`;
                    } catch (e) {
                        return fecha;
                    }
                },

                // Zoom de imágenes
                verImagenAmpliada(url, nombre, titulo) {
                    this.comprobanteZoomUrl = url;
                    this.comprobanteZoomTitulo = titulo || 'Comprobante';
                },

                cerrarImagenAmpliada() {
                    this.comprobanteZoomUrl = null;
                    this.comprobanteZoomTitulo = '';
                },

                // Cancelación
                abrirModalCancelar(id, folio, estado) {
                    this.cancelarModalData = { id, folio, estado };
                    this.motivoCancelacion = '';
                    this.modalCancelarAbierto = true;
                },

                cerrarModalCancelar() {
                    this.modalCancelarAbierto = false;
                    this.motivoCancelacion = '';
                },

                async confirmarCancelacion() {
                    if (!this.cancelarModalData.id) return;
                    if (this.motivoCancelacion.trim().length < 5) return;

                    this.procesandoCancelacion = true;
                    const token = document.querySelector('meta[name="csrf-token"]')?.content;

                    try {
                        const response = await fetch(`/ventas/${this.cancelarModalData.id}/cancelar-vendedor`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                motivo: this.motivoCancelacion.trim()
                            })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'No se pudo cancelar la venta.');
                        }

                        this.cerrarModalCancelar();

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Venta Cancelada!',
                                text: data.message,
                                timer: 1700,
                                showConfirmButton: false,
                                customClass: { popup: 'swal-axstore' }
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            window.location.reload();
                        }

                    } catch (err) {
                        console.error('Error al cancelar:', err);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'No se pudo cancelar',
                                text: err.message,
                                customClass: { popup: 'swal-axstore' }
                            });
                        } else {
                            alert(err.message);
                        }
                    } finally {
                        this.procesandoCancelacion = false;
                    }
                },

                // Impresión directa de tirilla térmica 80mm
                imprimirTicketDirecto(id) {
                    if (!id) return;
                    const url = `/ventas/${id}/imprimir?tipo=ticket`;
                    window.open(url, '_blank', 'width=420,height=650,scrollbars=yes');
                }
            };
        }
    </script>
</x-app>
