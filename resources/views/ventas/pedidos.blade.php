<x-app title="Control de Envíos y Estados de Venta | AXStore">
    <div x-data="gestorPedidosEstados()" class="space-y-6">

        <!-- ENCABEZADO PRINCIPAL -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                    <a href="{{ route('ventas.index') }}" class="hover:text-blue-600 transition-colors">Ventas</a>
                    <span>/</span>
                    <span class="text-blue-600">Control de Envíos y Estados</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900">
                    Control de Envíos y Estados
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Gestión logística y flujo de estados de pedidos organizados por apartados específicos.
                </p>
            </div>

            <!-- BOTONES DE ACCIÓN RÁPIDA -->
            <div class="flex flex-wrap items-center gap-2">
                <a 
                    href="{{ route('ventas.create') }}" 
                    class="rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold px-4 py-2 text-xs transition-all shadow-md shadow-blue-600/20 flex items-center gap-2"
                >
                    <i class="fas fa-plus"></i>
                    <span>Nueva Venta</span>
                </a>
            </div>
        </div>

        <!-- SUB-NAV / PESTAÑAS PRINCIPALES -->
        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones de ventas">
            <a href="{{ route('ventas.create') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-cash-register text-slate-400"></i>
                <span>Terminal de Ventas (Carrito)</span>
            </a>
            <a href="{{ route('ventas.pedidos') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-boxes-packing"></i>
                <span>Control de Envíos y Estados</span>
            </a>
            <a href="{{ route('ventas.index') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-chart-line text-slate-400"></i>
                <span>Dashboard Histórico (Entregadas)</span>
            </a>
            <a href="{{ route('ventas.devoluciones') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-clock-rotate-left text-slate-400"></i>
                <span>Historial de Devoluciones</span>
            </a>
        </nav>

        <!-- 1. BARRA DE FILTROS Y BÚSQUEDA -->
        <section class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
            <form method="GET" action="{{ route('ventas.pedidos') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
                
                <!-- BÚSQUEDA RÁPIDA -->
                <div class="lg:col-span-4">
                    <label for="q" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        <i class="fas fa-magnifying-glass text-slate-400 mr-1"></i> Buscar por Folio / Cliente / Teléfono
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

                <!-- VENDEDOR -->
                <div class="lg:col-span-2">
                    <label for="vendedor_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        <i class="fas fa-user-tie text-slate-400 mr-1"></i> Vendedor
                    </label>
                    <select 
                        name="vendedor_id" 
                        id="vendedor_id" 
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all"
                    >
                        <option value="">Todos</option>
                        @foreach($vendedores as $v)
                            <option value="{{ $v->id }}" {{ (string)$vendedorId === (string)$v->id ? 'selected' : '' }}>
                                {{ $v->nombre_real ?: $v->username }}
                            </option>
                        @endforeach
                    </select>
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
                    @if(request()->hasAny(['desde', 'hasta', 'vendedor_id', 'q']))
                        <a 
                            href="{{ route('ventas.pedidos') }}" 
                            class="rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold py-2 px-3 text-xs transition-colors flex items-center justify-center"
                            title="Limpiar filtros"
                        >
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>

            </form>
        </section>

        <!-- 2. PESTAÑAS DE APARTADOS POR ESTADO (ESTILO ELEGANTE Y COMPACTO) -->
        <div class="flex flex-wrap items-center gap-1.5 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Apartados de estado">
            <!-- 1. TODOS -->
            <button 
                type="button" 
                @click="cambiarTab('todos')"
                class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                :class="tabActiva === 'todos' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
            >
                <i class="fas fa-layer-group text-[11px]" :class="tabActiva === 'todos' ? 'text-white' : 'text-slate-400'"></i>
                <span>Todos</span>
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
                    {{ $conteoEstados['Cancelada'] }}
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
                <span>Devolución</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black"
                      :class="tabActiva === 'Devolución' ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-800'">
                    {{ $conteoEstados['Devolución'] }}
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
                <span>Cambio</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black"
                      :class="tabActiva === 'Cambio' ? 'bg-white/20 text-white' : 'bg-teal-100 text-teal-800'">
                    {{ $conteoEstados['Cambio'] }}
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
                             'bg-rose-100 text-rose-700': tabActiva === 'Cancelada',
                             'bg-purple-100 text-purple-700': tabActiva === 'Devolución',
                             'bg-teal-100 text-teal-700': tabActiva === 'Cambio'
                         }">
                        <i class="fas" :class="{
                            'fa-layer-group': tabActiva === 'todos',
                            'fa-clock': tabActiva === 'Pendiente',
                            'fa-circle-check': tabActiva === 'Confirmada',
                            'fa-truck-fast': tabActiva === 'En ruta',
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
                        Mostrando <strong class="text-blue-600" x-text="conteoFiltradas()"></strong> ventas en este apartado
                    </span>
                </div>
            </div>

            <!-- SUB-APARTADOS CUANDO EL ESTADO ES PENDIENTE: SAN SALVADOR VS OTROS DEPARTAMENTOS -->
            <div 
                x-show="tabActiva === 'Pendiente'" 
                class="px-5 py-3 bg-amber-50/40 border-b border-amber-100 flex flex-wrap items-center justify-between gap-3"
                style="display: none;"
            >
                <div class="flex items-center gap-1.5 text-xs text-amber-900 font-bold">
                    <i class="fas fa-map-location-dot text-amber-600 text-sm"></i>
                    <span>Filtrar Envíos por Zona / Departamento:</span>
                </div>

                <div class="inline-flex p-1 bg-white border border-amber-200 rounded-xl shadow-2xs gap-1">
                    <!-- Opción 1: Todos los Pendientes -->
                    <button 
                        type="button" 
                        @click="subFiltroPendiente = 'todos'"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer"
                        :class="subFiltroPendiente === 'todos' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:text-amber-800 hover:bg-amber-50'"
                    >
                        <span>Todos</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black"
                              :class="subFiltroPendiente === 'todos' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                            {{ $conteoEstados['Pendiente'] }}
                        </span>
                    </button>

                    <!-- Opción 2: San Salvador -->
                    <button 
                        type="button" 
                        @click="subFiltroPendiente = 'san_salvador'"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer"
                        :class="subFiltroPendiente === 'san_salvador' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-blue-800 hover:bg-blue-50'"
                    >
                        <i class="fas fa-city text-[10px]" :class="subFiltroPendiente === 'san_salvador' ? 'text-white' : 'text-blue-500'"></i>
                        <span>San Salvador</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black"
                              :class="subFiltroPendiente === 'san_salvador' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800'">
                            {{ $conteoEstados['Pendiente_SS'] }}
                        </span>
                    </button>

                    <!-- Opción 3: Otros Departamentos -->
                    <button 
                        type="button" 
                        @click="subFiltroPendiente = 'otros'"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer"
                        :class="subFiltroPendiente === 'otros' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-indigo-800 hover:bg-indigo-50'"
                    >
                        <i class="fas fa-signs-post text-[10px]" :class="subFiltroPendiente === 'otros' ? 'text-white' : 'text-indigo-500'"></i>
                        <span>Otros Departamentos</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black"
                              :class="subFiltroPendiente === 'otros' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800'">
                            {{ $conteoEstados['Pendiente_Otros'] }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- TABLA RESPONSIVA -->
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
                        @forelse($todasVentas as $venta)
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
                                $limiteFechaFormatted = $fechaLimite ? $fechaLimite->format('d-m-Y') : '';

                                $badgeClasses = match($estado) {
                                    'Pendiente' => 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100/80',
                                    'Confirmada' => 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100/80',
                                    'En ruta' => 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100/80',
                                    'Entregada', 'Entregado' => 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100/80',
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

                            @php
                                $deptoVenta = trim($venta->departamento ?? '');
                                if (empty($deptoVenta) && !empty($venta->direccion_entrega)) {
                                    if (str_contains(strtolower($venta->direccion_entrega), 'san salvador')) {
                                        $deptoVenta = 'San Salvador';
                                    }
                                }
                            @endphp

                            <tr 
                                x-show="ventaVisible('{{ $estado }}', '{{ addslashes($deptoVenta) }}')" 
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

                                <!-- VENDEDOR -->
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 font-bold text-[10px] flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($venta->usuario?->nombre_real ?? $venta->usuario?->username ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-slate-800 truncate max-w-[130px]" title="{{ $venta->usuario?->nombre_real ?? $venta->usuario?->username }}">
                                            {{ $venta->usuario?->nombre_real ?? $venta->usuario?->username ?? 'Usuario no asignado' }}
                                        </span>
                                    </div>
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
                                                @if(!empty($venta->departamento))
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200" title="Destino">
                                                        <i class="fas fa-map-pin text-[8px] text-amber-600"></i> {{ $venta->departamento }}{{ !empty($venta->municipio) ? ', ' . $venta->municipio : '' }}
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
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <i class="fas fa-store text-[9px]"></i> Venta en Tienda
                                                </span>
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
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $venta->metodo_pago === 'Efectivo' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($venta->metodo_pago === 'Transferencia Bancaria' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-blue-50 text-blue-700 border-blue-200') }}">
                                        <i class="fas {{ $venta->metodo_pago === 'Efectivo' ? 'fa-money-bill-wave' : ($venta->metodo_pago === 'Transferencia Bancaria' ? 'fa-building-columns' : 'fa-credit-card') }} text-[10px]"></i>
                                        <span>{{ $venta->metodo_pago }}</span>
                                    </span>
                                </td>

                                <!-- ESTADO ACTUAL INTERACTIVO -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="inline-flex flex-col items-center">
                                        @if(!$bloqueado && count($permitidos) > 0)
                                            <button 
                                                type="button" 
                                                @click="abrirModalCambiarEstado({{ $venta->id }}, '{{ $estado }}', '{{ $tipo }}', {{ $puedeDevolver ? 'true' : 'false' }}, {{ json_encode($permitidos) }}, {{ $diasRestantes }}, '{{ addslashes($textoGarantia) }}', '{{ $limiteFechaFormatted }}')"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border transition-all duration-200 shadow-2xs hover:shadow-xs hover:scale-105 cursor-pointer {{ $badgeClasses }}"
                                                title="Haz clic para avanzar o cambiar el estado"
                                            >
                                                <i class="fas {{ $badgeIcon }} text-[10px]"></i>
                                                <span>{{ $estado }}</span>
                                                <i class="fas fa-chevron-down text-[8px] opacity-60 ml-0.5"></i>
                                            </button>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $badgeClasses }}">
                                                <i class="fas {{ $badgeIcon }} text-[10px]"></i>
                                                <span>{{ $estado }}</span>
                                            </span>
                                        @endif

                                        @if($estado === 'Entregada' && $puedeDevolver)
                                            <span class="text-[9px] font-black text-amber-600 mt-1 flex items-center gap-0.5" title="{{ $textoGarantia }}">
                                                <i class="fas fa-shield-halved text-[8px]"></i>
                                                {{ $diasRestantes > 1 ? "Garantía: {$diasRestantes} días" : "Garantía: Hoy último día" }}
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
                                        <!-- COMPROBANTE DE PAGO (TRANSFERENCIA) -->
                                        @if($venta->comprobante_pago)
                                            <button 
                                                type="button" 
                                                @click="verImagenAmpliada('{{ asset('storage/' . $venta->comprobante_pago) }}', 'VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}_{{ \Carbon\Carbon::parse($venta->fecha)->format('Y-m-d') }}_comprobante_pago', 'Comprobante de Pago por Transferencia')"
                                                class="rounded-lg border border-purple-200 bg-purple-50 hover:bg-purple-600 hover:text-white text-purple-700 px-2 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                                title="Ver Comprobante de Pago por Transferencia"
                                            >
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
                                                title="Ver Comprobante de Paquetería"
                                            >
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
                                                title="Ver Comprobante de Devolución"
                                            >
                                                <i class="fas fa-box-archive text-[10px]"></i>
                                                <span>Devolución</span>
                                            </button>
                                        @endif

                                        <!-- BOTÓN DETALLE -->
                                        <button 
                                            type="button" 
                                            @click="abrirDetalle({{ $venta->id }})" 
                                            class="rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 px-2 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                            title="Ver artículos y desglose"
                                        >
                                            <i class="fas fa-eye text-slate-400 text-[10px]"></i>
                                            <span>Detalle</span>
                                        </button>

                                        <!-- BOTÓN IMPRIMIR (SELECTOR DE FACTURA / TICKET) -->
                                        <button 
                                            type="button" 
                                            @click="abrirModalImpresion({{ $venta->id }}, '{{ $venta->metodo_pago }}', {{ (float) $venta->total }})" 
                                            class="rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 px-2 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                            title="Imprimir ticket o factura comercial/tributaria"
                                        >
                                            <i class="fas fa-print text-[10px]"></i>
                                            <span>Imprimir</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 bg-white">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-lg mb-3">
                                            <i class="fas fa-inbox"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-700">No se encontraron ventas</h4>
                                        <p class="text-xs text-slate-400 mt-1">Prueba seleccionando otro rango de fechas o limpiando los filtros.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- MENSAJE VACÍO CUANDO EL FILTRO DINÁMICO DE PESTAÑA NO TIENE RESULTADOS -->
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
                        <span x-show="tabActiva === 'Pendiente' && subFiltroPendiente !== 'todos'">No hay pedidos pendientes para la zona o departamento seleccionado.</span>
                        <span x-show="tabActiva !== 'Pendiente' || subFiltroPendiente === 'todos'">Actualmente no existen registros con el estado seleccionado.</span>
                    </p>
                    <div class="flex items-center gap-2 mt-3">
                        <button 
                            type="button" 
                            x-show="tabActiva === 'Pendiente' && subFiltroPendiente !== 'todos'"
                            @click="subFiltroPendiente = 'todos'"
                            class="text-xs font-bold text-amber-600 hover:underline cursor-pointer"
                        >
                            Ver todos los pendientes
                        </button>
                        <span x-show="tabActiva === 'Pendiente' && subFiltroPendiente !== 'todos'" class="text-slate-300">•</span>
                        <button 
                            type="button" 
                            @click="cambiarTab('todos')"
                            class="text-xs font-bold text-blue-600 hover:underline cursor-pointer"
                        >
                            Ver todas las ventas
                        </button>
                    </div>
                </div>
            </div>

        </section>

        <!-- ========================================================================= -->
        <!-- MODAL: CAMBIAR ESTADO DE LA VENTA (CON MÁQUINA DE ESTADOS Y EVIDENCIAS)   -->
        <!-- ========================================================================= -->
        <div 
            x-show="modalCambiarEstadoAbierto" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <div 
                x-show="modalCambiarEstadoAbierto"
                x-transition.opacity
                class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                @click="cerrarModalCambiarEstado()"
            ></div>

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
                <div 
                    x-show="modalCambiarEstadoAbierto"
                    x-transition
                    @click.away="cerrarModalCambiarEstado()"
                    class="relative z-10 w-full max-w-xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-gray-100 flex flex-col my-8 max-h-[92vh]"
                >
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
                            title="Cerrar ventana"
                        >
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

                        <!-- ALERTA INFORMATIVA: EN RUTA NO PUEDE CANCELARSE -->
                        <template x-if="estadoModalData.estadoActual === 'En ruta'">
                            <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 flex items-start gap-2.5 shadow-2xs">
                                <i class="fas fa-truck-fast text-amber-600 mt-0.5 text-base shrink-0"></i>
                                <div>
                                    <p class="font-bold text-amber-950 leading-snug">Envío actualmente en camino</p>
                                    <p class="text-[11px] text-amber-800 mt-0.5 leading-relaxed">
                                        Este paquete ya fue entregado a la paquetería y se encuentra en ruta hacia el cliente. Por políticas de despacho y logística, <strong>este pedido ya no puede ser cancelado</strong>; únicamente puede avanzar a <strong>Entregada</strong> una vez completada la entrega al cliente.
                                    </p>
                                </div>
                            </div>
                        </template>

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
                                        :class="nuevoEstadoSeleccionado === st ? (st === 'Cambio' ? 'border-teal-500 bg-teal-50/70 shadow-sm ring-2 ring-teal-500/25' : (st === 'Devolución' ? 'border-purple-500 bg-purple-50/70 shadow-sm ring-2 ring-purple-500/25' : 'border-blue-600 bg-blue-50/70 shadow-sm ring-2 ring-blue-500/20')) : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50/60'"
                                    >
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
                            x-transition
                        >
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
                                        title="Eliminar imagen"
                                    >
                                        <i class="fas fa-trash-can text-sm"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- SECCIÓN DINÁMICA: DEVOLUCIÓN (EVIDENCIA Y MOTIVO) -->
                        <div 
                            id="seccion-foto-devolucion"
                            x-show="nuevoEstadoSeleccionado === 'Devolución' || nuevoEstadoSeleccionado === 'Cambio'" 
                            class="space-y-3 p-4 rounded-xl border transition-all"
                            :class="errorFotoDevolucion ? 'bg-rose-50/80 border-rose-400 ring-2 ring-rose-500/20' : (nuevoEstadoSeleccionado === 'Cambio' ? 'bg-teal-50/60 border-teal-200' : 'bg-purple-50/60 border-purple-200')"
                            x-transition
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas text-sm" :class="errorFotoDevolucion ? 'fa-triangle-exclamation text-rose-600' : (nuevoEstadoSeleccionado === 'Cambio' ? 'fa-arrows-rotate text-teal-600' : 'fa-rotate-left text-purple-600')"></i>
                                    <h4 class="font-bold text-xs uppercase tracking-wide" :class="errorFotoDevolucion ? 'text-rose-950 font-black' : (nuevoEstadoSeleccionado === 'Cambio' ? 'text-teal-950 font-black' : 'text-purple-950 font-black')">
                                        <span x-text="nuevoEstadoSeleccionado === 'Cambio' ? 'Evidencia y Fotografía para Cambio' : 'Evidencia y Justificación de Devolución'"></span> <span class="text-rose-500">*</span>
                                    </h4>
                                </div>
                                <span x-show="errorFotoDevolucion" class="text-[10px] font-bold px-2 py-0.5 rounded-full text-rose-700 bg-rose-100 border border-rose-300 animate-pulse">
                                    ¡Fotografía requerida!
                                </span>
                            </div>

                            <!-- ALERTA INLINE VISIBLE CUANDO FALTA LA FOTO -->
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
                               x-text="nuevoEstadoSeleccionado === 'Cambio' ? 'Adjunta la fotografía del estado del paquete/producto para procesar el cambio en tienda.' : 'Adjunta la fotografía del estado del paquete devuelto por el cliente o paquetería.'">
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
                                        title="Eliminar imagen"
                                    >
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
                                placeholder="Ingresa notas operativas o motivo de cancelación..."
                                :class="errorObservacionesEstado ? 'border-rose-500 bg-rose-50/40 text-rose-900 focus:border-rose-500 focus:ring-rose-500/20 ring-1 ring-rose-400' : 'border-slate-300 text-slate-700 focus:border-blue-500 focus:ring-blue-500/10'"
                                class="w-full rounded-xl border p-3 text-xs font-semibold focus:outline-none focus:ring-4 transition-all resize-none"
                            ></textarea>
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
                                    Si cancelas la venta, el stock de las variantes se devolverá automáticamente a la bodega y se anularán las comisiones asociadas.
                                </span>
                            </div>
                        </div>

                    </div>

                    <!-- FOOTER DEL MODAL -->
                    <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            @click="cerrarModalCambiarEstado()"
                            class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs transition-colors cursor-pointer"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="button" 
                            @click="guardarCambioEstado()"
                            :disabled="procesandoEstado || !nuevoEstadoSeleccionado"
                            class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-bold text-xs transition-all shadow-md shadow-blue-600/20 flex items-center gap-2 cursor-pointer"
                        >
                            <i class="fas fa-spinner fa-spin" x-show="procesandoEstado"></i>
                            <i class="fas fa-check" x-show="!procesandoEstado"></i>
                            <span x-text="procesandoEstado ? 'Guardando...' : 'Confirmar Cambio'"></span>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL: DETALLE COMPLETO DE VENTA (ARTÍCULOS, PAGOS Y TOTALES)             -->
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
                @click="if (!modalImprimirAbierto && !comprobanteZoomUrl && !openDevolucionModal) cerrarDetalle()"
            ></div>

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
                <div 
                    x-show="modalDetalleAbierto"
                    x-transition
                    @click.away="if (!modalImprimirAbierto && !comprobanteZoomUrl && !openDevolucionModal) cerrarDetalle()"
                    class="relative z-10 w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-gray-100 flex flex-col my-8 max-h-[92vh]"
                >
                    <!-- CABECERA AZUL UNIFICADA -->
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
                                    Emitida el <span x-text="formatearFechaModal(ventaSeleccionada?.fecha)"></span> • Atendido por <span class="font-bold text-white" x-text="ventaSeleccionada?.usuario?.nombre_real || ventaSeleccionada?.usuario?.username || 'Josue'"></span>
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
                                        <span class="font-bold text-slate-800" x-text="ventaSeleccionada.usuario?.nombre_real || ventaSeleccionada.usuario?.username || 'No asignado'"></span>
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
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-600/30">
                                                    <i class="fas fa-file-invoice-dollar text-sm"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                                        <span>Comprobante de Pago</span>
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded-full">
                                                            <i class="fas fa-shield-check text-[9px]"></i> Transferencia
                                                        </span>
                                                    </h4>
                                                    <p class="text-[11px] text-slate-500">Documento digital adjuntado durante la venta</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <a 
                                                    :href="ventaSeleccionada.comprobante_url" 
                                                    target="_blank" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-blue-700 bg-white border border-blue-200 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-2xs"
                                                >
                                                    <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                                                    <span>Abrir pestaña</span>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-4 p-3 bg-white rounded-xl border border-blue-100">
                                            <button 
                                                type="button" 
                                                @click="verImagenAmpliada(ventaSeleccionada.comprobante_url, nombreComprobanteVenta('comprobante_pago'), 'Comprobante de Pago por Transferencia')"
                                                class="group relative overflow-hidden rounded-lg border border-slate-200 shadow-2xs shrink-0 cursor-pointer block"
                                                title="Haz clic para ampliar la imagen"
                                            >
                                                <img 
                                                    :src="ventaSeleccionada.comprobante_url" 
                                                    alt="Comprobante de pago" 
                                                    class="w-20 h-20 object-cover group-hover:scale-105 transition-transform duration-200"
                                                >
                                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1">
                                                    <i class="fas fa-magnifying-glass-plus"></i>
                                                    <span>Ampliar</span>
                                                </div>
                                            </button>
                                            <div class="space-y-1.5 text-xs text-slate-600 flex-1">
                                                <p class="font-bold text-slate-800">Comprobante de Pago Registrado</p>
                                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                                    Comprobante bancario que certifica la transferencia de la transacción.
                                                </p>
                                                <button 
                                                    type="button" 
                                                    @click="verImagenAmpliada(ventaSeleccionada.comprobante_url, nombreComprobanteVenta('comprobante_pago'), 'Comprobante de Pago por Transferencia')"
                                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 hover:text-blue-800 transition-colors cursor-pointer"
                                                >
                                                    <i class="fas fa-expand text-[10px]"></i> Ver imagen ampliada
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- COMPROBANTE DE PAQUETE EN RUTA (SI APLICA) -->
                                <template x-if="ventaSeleccionada.comprobante_paquete_url">
                                    <div class="bg-gradient-to-r from-indigo-50/70 via-blue-50/50 to-slate-50 border border-indigo-200/80 rounded-2xl p-4 shadow-2xs">
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
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-indigo-700 bg-white border border-indigo-200 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-2xs"
                                                >
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
                                                title="Haz clic para ampliar la imagen"
                                            >
                                                <img 
                                                    :src="ventaSeleccionada.comprobante_paquete_url" 
                                                    alt="Paquete en paquetería" 
                                                    class="w-20 h-20 object-cover group-hover:scale-105 transition-transform duration-200"
                                                >
                                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1">
                                                    <i class="fas fa-magnifying-glass-plus"></i>
                                                    <span>Ampliar</span>
                                                </div>
                                            </button>
                                            <div class="space-y-1.5 text-xs text-slate-600 flex-1">
                                                <p class="font-bold text-slate-800">Comprobante de despacho a paquetería</p>
                                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                                    Evidencia del paquete rotulado y recibido por el servicio de mensajería.
                                                </p>
                                                <button 
                                                    type="button" 
                                                    @click="verImagenAmpliada(ventaSeleccionada.comprobante_paquete_url, nombreComprobanteVenta('paquete'), 'Comprobante de Paquetería')"
                                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition-colors cursor-pointer"
                                                >
                                                    <i class="fas fa-expand text-[10px]"></i> Ver imagen ampliada
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- COMPROBANTE DE DEVOLUCIÓN (SI APLICA) -->
                                <template x-if="ventaSeleccionada.comprobante_devolucion_url">
                                    <div class="bg-gradient-to-r from-purple-50/70 via-indigo-50/50 to-slate-50 border border-purple-200/80 rounded-2xl p-4 shadow-2xs">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-purple-600/30">
                                                    <i class="fas fa-rotate-left text-sm"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                                        <span>Evidencia de Paquete Devuelto</span>
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-purple-700 bg-purple-100 border border-purple-200 px-2 py-0.5 rounded-full">
                                                            <i class="fas" :class="ventaSeleccionada.estado === 'Cambio' ? 'fa-arrow-right-arrow-left' : 'fa-arrow-rotate-left'" style="font-size:9px"></i>
                                                            <span x-text="ventaSeleccionada.estado === 'Cambio' ? 'Cambio' : 'Devolución'"></span>
                                                        </span>
                                                    </h4>
                                                    <p class="text-[11px] text-slate-500">Fotografía del paquete o producto retornado al inventario</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <a 
                                                    :href="ventaSeleccionada.comprobante_devolucion_url" 
                                                    target="_blank" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-purple-700 bg-white border border-purple-200 hover:bg-purple-600 hover:text-white hover:border-purple-600 transition-all shadow-2xs"
                                                >
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
                                                title="Haz clic para ampliar la imagen"
                                            >
                                                <img 
                                                    :src="ventaSeleccionada.comprobante_devolucion_url" 
                                                    alt="Paquete devuelto" 
                                                    class="w-20 h-20 object-cover group-hover:scale-105 transition-transform duration-200"
                                                >
                                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1">
                                                    <i class="fas fa-magnifying-glass-plus"></i>
                                                    <span>Ampliar</span>
                                                </div>
                                            </button>
                                            <div class="space-y-1.5 text-xs text-slate-600 flex-1">
                                                <p class="font-bold text-slate-800">Fotografía del producto recibido en devolución</p>
                                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                                    Inspección visual del producto devuelto por el cliente.
                                                </p>
                                                <button 
                                                    type="button" 
                                                    @click="verImagenAmpliada(ventaSeleccionada.comprobante_devolucion_url, nombreComprobanteVenta('devolucion'), 'Comprobante de Devolución')"
                                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-purple-600 hover:text-purple-800 transition-colors cursor-pointer"
                                                >
                                                    <i class="fas fa-expand text-[10px]"></i> Ver imagen ampliada
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- ARTÍCULOS FACTURADOS -->
                                <div class="border border-slate-200 rounded-xl overflow-hidden">
                                    <table class="w-full text-left">
                                        <thead class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase font-bold text-slate-400">
                                            <tr>
                                                <th class="px-4 py-2.5">Producto / Variante</th>
                                                <th class="px-3 py-2.5 text-center">Cant.</th>
                                                <th class="px-3 py-2.5 text-right">P. Unitario</th>
                                                <th class="px-3 py-2.5 text-right">Costo Extra</th>
                                                <th class="px-4 py-2.5 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            <template x-for="det in ventaSeleccionada.detalles" :key="det.id">
                                                <tr class="hover:bg-slate-50/50">
                                                    <td class="px-4 py-2.5">
                                                        <span class="font-bold text-slate-900 block" x-text="det.variante?.producto?.nombre || 'Producto'"></span>
                                                        <span class="text-[11px] text-slate-500" x-text="det.variante?.nombre_variante || ''"></span>
                                                    </td>
                                                    <td class="px-3 py-2.5 text-center font-bold text-slate-700" x-text="det.cantidad"></td>
                                                    <td class="px-3 py-2.5 text-right text-slate-600" x-text="'$' + parseFloat(det.precio_unitario).toFixed(2)"></td>
                                                    <td class="px-3 py-2.5 text-right text-slate-600" x-text="'$' + parseFloat(det.costo_extra || 0).toFixed(2)"></td>
                                                    <td class="px-4 py-2.5 text-right font-bold text-slate-800" x-text="'$' + parseFloat(det.subtotal).toFixed(2)"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- TOTALES -->
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex flex-col items-end gap-1.5">
                                    <div class="flex items-center justify-between w-48 text-xs text-slate-500">
                                        <span>Costo de Envío:</span>
                                        <span class="font-bold text-slate-700" x-text="'$' + parseFloat(ventaSeleccionada.precio_envio || 0).toFixed(2)"></span>
                                    </div>
                                    <div class="flex items-center justify-between w-48 text-sm font-black text-slate-900 pt-2 border-t border-slate-200">
                                        <span>Total Cobrado:</span>
                                        <span class="text-blue-600" x-text="'$' + parseFloat(ventaSeleccionada.total || 0).toFixed(2)"></span>
                                    </div>
                                </div>

                            </div>
                        </template>
                    </div>

                    <!-- PIE DEL MODAL CON ACCIONES (ESTILO UNIFICADO) -->
                    <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl">
                        <button 
                            type="button" 
                            @click="cerrarDetalle()"
                            class="px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer"
                        >
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
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-slate-900 hover:bg-slate-800 shadow-slate-900/20"
                            >
                                <i class="fas fa-print text-xs"></i>
                                <span>Imprimir Comprobante (Ticket / Factura)</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL: REGISTRAR DEVOLUCIÓN / GARANTÍA (UNIFICADO CON COMISIONES)         -->
        <!-- ========================================================================= -->
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
                            <span class="text-sm font-black uppercase tracking-wider text-white">Registrar Devolución / Garantía</span>
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

                                <!-- INFO BOX DINÁMICO UNIFICADO CON COMISIONES -->
                                <template x-if="tipo_resolucion">
                                    <div x-transition.opacity.duration.300ms class="mt-2.5 p-3.5 rounded-xl border text-xs leading-relaxed space-y-2 shadow-2xs"
                                         :class="{
                                             'bg-blue-50/80 border-blue-200 text-blue-950': tipo_resolucion === 'reembolso_tienda',
                                             'bg-amber-50/80 border-amber-200 text-amber-950': tipo_resolucion === 'reembolso_cuarentena',
                                             'bg-teal-50/80 border-teal-200 text-teal-950': tipo_resolucion === 'cambio_tienda',
                                             'bg-purple-50/80 border-purple-200 text-purple-950': tipo_resolucion === 'cambio_cuarentena'
                                         }">
                                        <div class="flex items-center gap-2 font-bold text-[11px] uppercase tracking-wider pb-1 border-b"
                                             :class="{
                                                 'border-blue-200/80 text-blue-900': tipo_resolucion === 'reembolso_tienda',
                                                 'border-amber-200/80 text-amber-900': tipo_resolucion === 'reembolso_cuarentena',
                                                 'border-teal-200/80 text-teal-900': tipo_resolucion === 'cambio_tienda',
                                                 'border-purple-200/80 text-purple-900': tipo_resolucion === 'cambio_cuarentena'
                                             }">
                                            <i class="fas" :class="{
                                                'fa-money-bill-wave text-blue-600': tipo_resolucion === 'reembolso_tienda',
                                                'fa-triangle-exclamation text-amber-600': tipo_resolucion === 'reembolso_cuarentena',
                                                'fa-arrow-right-arrow-left text-teal-600': tipo_resolucion === 'cambio_tienda',
                                                'fa-box-archive text-purple-600': tipo_resolucion === 'cambio_cuarentena'
                                            }"></i>
                                            <span>Impacto Operativo y en Comisiones</span>
                                        </div>

                                        <template x-if="tipo_resolucion === 'reembolso_tienda'">
                                            <div class="space-y-1 text-[11px]">
                                                <p><strong class="text-blue-900">• Inventario:</strong> Los artículos devueltos retornan al stock activo de bodega.</p>
                                                <p><strong class="text-blue-900">• Caja / Dinero:</strong> Se registra egreso por el importe reembolsado al cliente.</p>
                                                <p><strong class="text-blue-900">• Comisiones:</strong> Se anula la comisión del vendedor por los productos devueltos. <template x-if="ventaSeleccionada?.tipo_venta === 'Envio'"><span class="font-bold text-rose-700">Al ser envío no recibido, se aplica deducción del 50% del costo de envío como ajuste negativo al vendedor.</span></template></p>
                                            </div>
                                        </template>

                                        <template x-if="tipo_resolucion === 'reembolso_cuarentena'">
                                            <div class="space-y-1 text-[11px]">
                                                <p><strong class="text-amber-900">• Inventario:</strong> La mercancía defectuosa ingresa a <strong>stock en cuarentena</strong>.</p>
                                                <p><strong class="text-amber-900">• Caja / Dinero:</strong> Se devuelve el dinero al cliente por falla de fábrica.</p>
                                                <p><strong class="text-amber-900">• Comisiones:</strong> Se anula la comisión de los productos devueltos (sin deducción de envío al vendedor por ser falla atribuible a fábrica).</p>
                                            </div>
                                        </template>

                                        <template x-if="tipo_resolucion === 'cambio_tienda'">
                                            <div class="space-y-1 text-[11px]">
                                                <p><strong class="text-teal-900">• Inventario:</strong> Entra la talla/color anterior a bodega y sale la nueva variante de reemplazo.</p>
                                                <p><strong class="text-teal-900">• Caja / Dinero:</strong> <strong>NO</strong> hay movimiento de efectivo (cambio 1 a 1 de igual valor).</p>
                                                <p><strong class="text-teal-900">• Comisiones:</strong> <span class="font-bold text-teal-800">La comisión del vendedor se mantiene activa</span> porque la venta y el ingreso permanecen vigentes.</p>
                                            </div>
                                        </template>

                                        <template x-if="tipo_resolucion === 'cambio_cuarentena'">
                                            <div class="space-y-1 text-[11px]">
                                                <p><strong class="text-purple-900">• Inventario:</strong> La unidad defectuosa va a <strong>cuarentena</strong> y sale una unidad nueva de reposición.</p>
                                                <p><strong class="text-purple-900">• Caja / Dinero:</strong> <strong>NO</strong> hay movimiento de efectivo (reposición por garantía).</p>
                                                <p><strong class="text-purple-900">• Comisiones:</strong> <span class="font-bold text-purple-800">La comisión del vendedor se mantiene activa</span>.</p>
                                            </div>
                                        </template>
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

        <!-- ========================================================================= -->
        <!-- MODAL: SELECTOR DE COMPROBANTE E IMPRESIÓN (LEYES DE EL SALVADOR)         -->
        <!-- ========================================================================= -->
        <div 
            x-show="modalImprimirAbierto" 
            x-cloak
            class="fixed inset-0 z-[70] overflow-y-auto"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <!-- Backdrop oscuro con blur idéntico al sistema -->
            <div 
                x-show="modalImprimirAbierto"
                x-transition.opacity
                class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"
                @click.stop="cerrarModalImpresion()"
            ></div>

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
                <div 
                    x-show="modalImprimirAbierto"
                    x-transition
                    @click.stop
                    @click.away="cerrarModalImpresion()"
                    class="relative z-10 w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-gray-100 flex flex-col my-8 max-h-[90vh]"
                >
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
                            title="Cerrar ventana"
                        >
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
                                    class="border rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between shadow-2xs"
                                >
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
                                    class="border rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between shadow-2xs"
                                >
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
                                    class="border rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between shadow-2xs"
                                >
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
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all bg-white"
                                    >
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
                                            class="w-full rounded-xl border px-3.5 py-2.5 pr-9 text-xs text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:outline-none transition-all bg-white font-mono"
                                        >
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
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all bg-white"
                                >
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
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all"
                                    >
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
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 font-mono placeholder:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all"
                                    >
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
                                            class="w-full rounded-xl border bg-white px-3.5 py-2.5 pr-9 text-xs font-mono placeholder:text-slate-400 focus:ring-2 focus:outline-none transition-all"
                                        >
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
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all"
                                    >
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Dirección Comercial:</label>
                                    <input 
                                        type="text" 
                                        x-model="clienteDireccionCCF"
                                        placeholder="Colonia Escalón, San Salvador"
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all"
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Departamento:</label>
                                    <select 
                                        x-model="clienteDepartamentoCCF"
                                        class="w-full rounded-xl border border-purple-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 focus:outline-none transition-all"
                                    >
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
                                        class="rounded border-purple-300 text-purple-600 focus:ring-purple-500"
                                    >
                                    <span class="text-[11px] font-bold text-slate-700">
                                        Aplicar Retención del 1% de IVA (Cliente es Gran Contribuyente / Agente de Retención)
                                    </span>
                                </label>
                            </div>
                        </div>

                    </div>

                    <!-- ACCIONES INFERIORES -->
                    <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl">
                        <button 
                            type="button" 
                            @click.stop="cerrarModalImpresion()"
                            class="px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer"
                        >
                            Cancelar
                        </button>

                        <button 
                            type="button" 
                            @click="procederImpresion()"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-slate-900 hover:bg-slate-800 shadow-slate-900/20"
                        >
                            <i class="fas fa-print text-xs"></i>
                            <span>Generar e Imprimir Documento</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL: VISOR DE IMÁGENES / COMPROBANTES EN ALTA RESOLUCIÓN                -->
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
                    <!-- CABECERA AZUL CON TÍTULO Y BOTÓN DE DESCARGA -->
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
                                title="Descargar imagen con folio y fecha de venta"
                            >
                                <i class="fas fa-download"></i>
                                <span>Descargar</span>
                            </button>
                            <button type="button" @click="cerrarImagenAmpliada()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- IMAGEN EN ALTA DEFINICIÓN -->
                    <div class="p-4 bg-slate-900/5 flex items-center justify-center max-h-[75vh] overflow-auto">
                        <img :src="comprobanteZoomUrl" alt="Comprobante en Alta Resolución" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-sm border border-slate-200">
                    </div>

                    <!-- PIE CON ACCIONES -->
                    <div class="px-5 py-3 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-500 font-medium">Inspección de evidencia gráfica de la venta</span>
                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                @click="descargarImagenActual()" 
                                class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer flex items-center gap-1.5"
                            >
                                <i class="fas fa-download text-[11px]"></i>
                                <span>Descargar Imagen</span>
                            </button>
                            <button type="button" @click="cerrarImagenAmpliada()" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- SCRIPT ALPINE.JS DE CONTROL -->
    <script>
        function gestorPedidosEstados() {
            return {
                tabActiva: '{{ $estadoTab ?? "todos" }}',
                subFiltroPendiente: 'todos', // 'todos', 'san_salvador', 'otros'

                // Conteos dinámicos
                conteoEstados: @json($conteoEstados),

                // Modales
                modalDetalleAbierto: false,
                openDevolucionModal: false,
                cargandoDetalle: false,
                ventaSeleccionada: null,
                comprobanteZoomUrl: null,
                comprobanteZoomNombre: '',
                comprobanteZoomTitulo: '',

                // Modal Impresión y Comprobantes Tributarios (El Salvador)
                modalImprimirAbierto: false,
                ventaImprimirId: null,
                metodoPagoImprimir: '',
                totalImprimir: 0,
                tipoComprobante: 'ticket',
                clienteNombreComercial: '',
                clienteDuiNitComercial: '',
                clienteDireccionComercial: '',
                clienteRazonSocialCCF: '',
                clienteNrcCCF: '',
                clienteNitCCF: '',
                clienteGiroCCF: '',
                clienteDireccionCCF: '',
                clienteDepartamentoCCF: 'San Salvador',
                aplicaRetencion1: false,

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

                // Estados de error inline para modal cambio de estado
                errorEstadoModal: '',
                errorNuevoEstado: false,
                errorFotoPaquete: false,
                errorFotoPaqueteMensaje: '',
                errorFotoDevolucion: false,
                errorFotoDevolucionMensaje: '',
                errorObservacionesEstado: false,
                errorObservacionesEstadoMensaje: '',

                cambiarTab(tab) {
                    this.tabActiva = tab;
                },

                nombreTabActual() {
                    const nombres = {
                        'todos': 'Todos los Pedidos en Proceso',
                        'Pendiente': 'Pendientes de Confirmar',
                        'Confirmada': 'Confirmadas y Listas',
                        'En ruta': 'En Ruta con Paquetería',
                        'Cancelada': 'Canceladas',
                        'Devolución': 'En Devolución por Garantía',
                        'Cambio': 'En Proceso de Cambio'
                    };
                    return nombres[this.tabActiva] || 'Todos los Pedidos en Proceso';
                },

                descripcionTabActual() {
                    const descripciones = {
                        'todos': 'Listado de órdenes y pedidos pendientes, en preparación, en ruta o con incidencias.',
                        'Pendiente': 'Pedidos recién registrados que requieren confirmación para empaque.',
                        'Confirmada': 'Órdenes listas para ser empaquetadas o despachadas a mensajería.',
                        'En ruta': 'Envíos en tránsito con fotografía de comprobante de paquetería registrada.',
                        'Cancelada': 'Transacciones que han sido anuladas, con stock retornado a bodega.',
                        'Devolución': 'Paquetes de envío no recibidos por el cliente, devueltos a la tienda.',
                        'Cambio': 'Producto entregado al cliente que regresa a la tienda para un cambio.'
                    };
                    return descripciones[this.tabActiva] || '';
                },

                ventaVisible(estado, depto = '') {
                    if (this.tabActiva === 'todos') return true;
                    if (estado !== this.tabActiva) return false;

                    // Si está en el apartado 'Pendiente', evaluar el subfiltro de departamento
                    if (this.tabActiva === 'Pendiente') {
                        if (this.subFiltroPendiente === 'todos') return true;

                        const d = (depto || '').toLowerCase().trim();
                        const esSanSalvador = d.includes('san salvador');

                        if (this.subFiltroPendiente === 'san_salvador') {
                            return esSanSalvador;
                        }
                        if (this.subFiltroPendiente === 'otros') {
                            return !esSanSalvador;
                        }
                    }

                    return true;
                },

                conteoFiltradas() {
                    if (this.tabActiva === 'Pendiente') {
                        if (this.subFiltroPendiente === 'san_salvador') {
                            return this.conteoEstados['Pendiente_SS'] || 0;
                        }
                        if (this.subFiltroPendiente === 'otros') {
                            return this.conteoEstados['Pendiente_Otros'] || 0;
                        }
                    }
                    return this.conteoEstados[this.tabActiva] || 0;
                },

                abrirModalCambiarEstado(id, estadoActual, tipoVenta, puedeDevolver, estadosPermitidos, diasRestantes, textoGarantia, fechaLimite) {
                    let permitidosFiltrados = Array.isArray(estadosPermitidos) 
                        ? estadosPermitidos.filter(s => s !== 'Devolución' && s !== 'Cambio') 
                        : [];

                    // Regla de despacho: un envío en ruta nunca puede cancelarse
                    if (estadoActual === 'En ruta') {
                        permitidosFiltrados = permitidosFiltrados.filter(s => s !== 'Cancelada');
                    }

                    this.estadoModalData = {
                        id: id,
                        folio: '#VNT-' + String(id).padStart(5, '0'),
                        estadoActual: estadoActual,
                        tipoVenta: tipoVenta,
                        puedeDevolver: puedeDevolver,
                        estadosPermitidos: permitidosFiltrados,
                        diasRestantes: diasRestantes || 0,
                        textoGarantia: textoGarantia || '',
                        fechaLimite: fechaLimite || ''
                    };

                    this.nuevoEstadoSeleccionado = this.estadoModalData.estadosPermitidos.length > 0 
                        ? this.estadoModalData.estadosPermitidos[0] 
                        : null;
                    this.observacionesEstado = '';
                    this.removerArchivoPaquete();
                    this.removerArchivoDevolucion();
                    this.errorEstadoModal = '';
                    this.errorNuevoEstado = false;
                    this.errorFotoPaquete = false;
                    this.errorFotoPaqueteMensaje = '';
                    this.errorFotoDevolucion = false;
                    this.errorFotoDevolucionMensaje = '';
                    this.errorObservacionesEstado = false;
                    this.errorObservacionesEstadoMensaje = '';
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
                    this.errorFotoDevolucion = false;
                    this.errorObservacionesEstado = false;
                },

                seleccionarNuevoEstado(st) {
                    this.nuevoEstadoSeleccionado = st;
                    this.errorNuevoEstado = false;
                    this.errorFotoPaquete = false;
                    this.errorFotoDevolucion = false;
                    this.errorObservacionesEstado = false;
                },

                obtenerDescripcionEstado(st) {
                    switch (st) {
                        case 'Confirmada':
                            return 'Venta verificada y lista para empaque y despacho.';
                        case 'En ruta':
                            return 'Entregado a paquetería / encomienda. Requiere foto del paquete.';
                        case 'Entregada':
                            return 'Entregado al cliente exitosamente.';
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
                    reader.onload = (event) => {
                        this.previewPaquete = event.target.result;
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
                    reader.onload = (event) => {
                        this.previewDevolucion = event.target.result;
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
                    this.errorEstadoModal = '';
                    this.errorNuevoEstado = false;
                    this.errorFotoPaquete = false;
                    this.errorFotoDevolucion = false;
                    this.errorObservacionesEstado = false;

                    if (!this.nuevoEstadoSeleccionado) {
                        this.errorNuevoEstado = true;
                        this.$nextTick(() => {
                            document.getElementById('seccion-seleccion-nuevo-estado')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        });
                        return;
                    }

                    // Regla de despacho: validación estricta de que En ruta no puede ser cancelado
                    if (this.estadoModalData.estadoActual === 'En ruta' && this.nuevoEstadoSeleccionado === 'Cancelada') {
                        this.errorEstadoModal = 'No es posible cancelar un pedido que ya se encuentra en ruta hacia el cliente.';
                        return;
                    }

                    if (this.nuevoEstadoSeleccionado === 'En ruta' && !this.archivoPaquete) {
                        this.errorFotoPaquete = true;
                        this.errorFotoPaqueteMensaje = 'Para cambiar al estado "En ruta" es obligatorio adjuntar la fotografía del paquete entregado a la paquetería.';
                        this.$nextTick(() => {
                            document.getElementById('seccion-foto-paquete')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        });
                        return;
                    }

                    if (this.nuevoEstadoSeleccionado === 'Cancelada' && !this.observacionesEstado.trim()) {
                        this.errorObservacionesEstado = true;
                        this.errorObservacionesEstadoMensaje = 'Por favor ingresa en el campo de observaciones el motivo de la cancelación de la venta.';
                        this.$nextTick(() => {
                            const el = document.getElementById('input-observaciones-estado');
                            el?.focus();
                            el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
                                el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            });
                            return;
                        }

                        if (!this.archivoDevolucion) {
                            this.errorFotoDevolucion = true;
                            this.errorFotoDevolucionMensaje = `Para procesar un ${labelEstado} debes adjuntar la fotografía del paquete.`;
                            this.$nextTick(() => {
                                document.getElementById('seccion-foto-devolucion')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
                                document.getElementById('modal-cambiar-estado-body')?.scrollTo({ top: 0, behavior: 'smooth' });
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
                            document.getElementById('modal-cambiar-estado-body')?.scrollTo({ top: 0, behavior: 'smooth' });
                        });
                    } finally {
                        this.procesandoEstado = false;
                    }
                },

                async abrirDetalle(id) {
                    this.cargandoDetalle = true;
                    this.ventaSeleccionada = null;
                    this.modalDetalleAbierto = true;

                    try {
                        const response = await fetch(`/ventas/${id}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) throw new Error('No se pudo cargar la información de la venta.');

                        const data = await response.json();
                        this.ventaSeleccionada = data.venta;
                    } catch (error) {
                        console.error('Error al obtener detalle:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al cargar',
                            text: 'No se pudo obtener la información completa de la venta.'
                        });
                        this.modalDetalleAbierto = false;
                    } finally {
                        this.cargandoDetalle = false;
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

                formatearFechaModal(fechaStr) {
                    if (!fechaStr) return '';
                    try {
                        const d = new Date(fechaStr);
                        if (isNaN(d.getTime())) return fechaStr;
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
                        return fechaStr;
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
                    this.clienteNombreComercial = '';
                    this.clienteDuiNitComercial = '';
                    this.clienteDireccionComercial = '';
                    this.clienteRazonSocialCCF = '';
                    this.clienteNrcCCF = '';
                    this.clienteNitCCF = '';
                    this.clienteGiroCCF = '';
                    this.clienteDireccionCCF = '';
                    this.clienteDepartamentoCCF = 'San Salvador';
                    this.aplicaRetencion1 = false;
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
                            mensaje: esValido 
                                ? '✓ DUI válido (Persona Natural / NIT homologado)'
                                : '✗ Dígito verificador de DUI inválido según normativa de El Salvador'
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
                            mensaje: esValido
                                ? '✓ NIT válido de 14 dígitos (Persona Jurídica)'
                                : '✗ Dígito verificador de NIT inválido según normativa de El Salvador'
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
                }
            };
        }
    </script>
</x-app>
