<x-app title="Centro de Dudas Administrativas | AXStore Super Admin">
    <div class="mx-auto max-w-7xl space-y-6 pb-12" x-data="gestionSuperAdminTickets({
        estadoTab: @js($estadoTab),
        conteoEstados: @js($conteoEstados)
    })">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('superadmin.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 mb-1">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Panel de Gobernanza</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-lg shrink-0">
                        <i class="fas fa-bolt"></i>
                    </span>
                    <span>Centro de Dudas y Casos Escalados (Super Admin)</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Atención de solicitudes provenientes de Administradores o casos complejos escalados por el equipo.
                </p>
            </div>
        </div>

        <!-- Barra de Pestañas por Estado (Mismo estilo que Control de Dudas) -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
            @php
                $tabs = [
                    'todos'      => ['label' => 'Todos',       'badge_bg' => 'bg-slate-100',   'badge_fg' => 'text-slate-700'],
                    'Pendiente'  => ['label' => 'Pendientes',  'badge_bg' => 'bg-amber-100',   'badge_fg' => 'text-amber-800'],
                    'En proceso' => ['label' => 'En proceso',  'badge_bg' => 'bg-blue-100',    'badge_fg' => 'text-blue-800'],
                    'Escalada'   => ['label' => 'Escaladas',   'badge_bg' => 'bg-purple-100',  'badge_fg' => 'text-purple-800'],
                    'Resuelta'   => ['label' => 'Resueltas',   'badge_bg' => 'bg-emerald-100', 'badge_fg' => 'text-emerald-800'],
                    'Cancelada'  => ['label' => 'Canceladas',  'badge_bg' => 'bg-rose-100',    'badge_fg' => 'text-rose-800'],
                ];
            @endphp

            @foreach ($tabs as $key => $t)
                <button type="button"
                   @click="cambiarEstado('{{ $key }}')"
                   :class="estadoTab === '{{ $key }}' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer">
                    <span>{{ $t['label'] }}</span>
                    <span :class="estadoTab === '{{ $key }}' ? 'bg-white/20 text-white' : '{{ $t['badge_bg'] }} {{ $t['badge_fg'] }}'"
                          class="px-2 py-0.5 rounded-full text-[10px] font-black"
                          x-text="conteoEstados['{{ $key }}'] ?? 0">
                        {{ $conteoEstados[$key] ?? 0 }}
                    </span>
                </button>
            @endforeach
        </div>

        <!-- Filtros y Búsqueda -->
        <form @submit.prevent="ejecutarFiltro()" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[240px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text"
                           x-model="filtroSearch"
                           @input.debounce.400ms="ejecutarFiltro()"
                           placeholder="Buscar por código, título, duda o usuario..."
                           class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" 
                        class="rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-3 text-xs transition-all shadow-md shadow-blue-600/15 flex items-center justify-center gap-1.5 cursor-pointer">
                    <i :class="cargandoTickets ? 'fas fa-spinner fa-spin' : 'fas fa-filter'"></i>
                    <span x-text="cargandoTickets ? 'Filtrando...' : 'Filtrar'"></span>
                </button>
                <button type="button" 
                        @click="limpiarFiltros()"
                        x-show="filtroSearch || estadoTab !== 'Pendiente'"
                        style="display: none;"
                        class="rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold py-2 px-3 text-xs transition-colors flex items-center justify-center cursor-pointer" 
                        title="Restablecer filtros">
                    <i class="fas fa-rotate-left"></i>
                </button>
            </div>
        </form>

        <!-- Skeleton Loading Dinámico para Super Admin -->
        <p x-show="errorCarga" x-cloak x-text="errorCarga" role="alert" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700"></p>
        <div x-show="cargandoTickets" style="display: none;" class="space-y-4 select-none" role="status" aria-label="Cargando dudas...">
            @for($i = 0; $i < 3; $i++)
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 flex flex-col md:flex-row md:items-start justify-between gap-6">
                    <div class="space-y-3 flex-1">
                        <div class="flex items-center gap-2">
                            <div class="h-6 w-20 rounded-lg ax-skeleton-shimmer"></div>
                            <div class="h-6 w-20 rounded-md ax-skeleton-shimmer"></div>
                            <div class="h-6 w-24 rounded-md ax-skeleton-shimmer"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-5 w-72 rounded-md ax-skeleton-shimmer"></div>
                            <div class="h-4 w-full rounded ax-skeleton-shimmer"></div>
                            <div class="h-4 w-3/4 rounded ax-skeleton-shimmer"></div>
                        </div>
                        <div class="flex items-center gap-4 pt-2">
                            <div class="h-4 w-28 rounded ax-skeleton-shimmer"></div>
                            <div class="h-4 w-36 rounded ax-skeleton-shimmer"></div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row md:flex-col gap-2 shrink-0">
                        <div class="h-10 w-36 rounded-xl ax-skeleton-shimmer"></div>
                    </div>
                </div>
            @endfor
            <span class="sr-only">Cargando dudas...</span>
        </div>

        <!-- Contenedor de Tickets -->
        <div id="contenedorListaSuperAdminTickets" x-show="!cargandoTickets" :aria-busy="cargandoTickets">
            @include('superadmin.tabla-tickets', ['tickets' => $tickets])
        </div>

    </div>

    <script>
        function gestionSuperAdminTickets(config) {
            return {
                estadoTab: config.estadoTab || 'Pendiente',
                conteoEstados: config.conteoEstados || {},
                filtroSearch: @js(request('search', '')),
                cargandoTickets: false,
                solicitudCarga: 0,
                errorCarga: '',

                async cambiarEstado(estado) {
                    if (this.estadoTab === estado && !this.cargandoTickets) return;
                    this.estadoTab = estado;
                    await this.cargarTickets();
                },

                async ejecutarFiltro() {
                    await this.cargarTickets();
                },

                async limpiarFiltros() {
                    this.filtroSearch = '';
                    this.estadoTab = 'Pendiente';
                    await this.cargarTickets();
                },

                construirUrl(pagina = 1) {
                    const params = new URLSearchParams();
                    params.set('estado', this.estadoTab);
                    if (this.filtroSearch && this.filtroSearch.trim() !== '') {
                        params.set('search', this.filtroSearch.trim());
                    }
                    if (pagina > 1) {
                        params.set('page', pagina);
                    }

                    const queryString = params.toString();
                    return `{{ route('superadmin.tickets') }}${queryString ? '?' + queryString : ''}`;
                },

                async cargarTickets(urlDestino = null) {
                    const solicitud = ++this.solicitudCarga;
                    this.cargandoTickets = true;
                    this.errorCarga = '';
                    const url = urlDestino || this.construirUrl(1);

                    try {
                        const res = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error('No se pudo completar la solicitud.');
                        const data = await res.json();
                        if (solicitud !== this.solicitudCarga) return;
                        if (!data.success || typeof data.html !== 'string') {
                            throw new Error('La respuesta no contiene los datos esperados.');
                        }
                        if (data.success) {
                            window.history.pushState({}, '', url);
                            const contenedor = document.getElementById('contenedorListaSuperAdminTickets');
                            if (contenedor) {
                                contenedor.innerHTML = data.html;
                                this.atarPaginacionTickets();
                            }
                            if (data.conteoEstados) {
                                this.conteoEstados = data.conteoEstados;
                            }
                        }
                    } catch (err) {
                        if (solicitud !== this.solicitudCarga) return;
                        this.errorCarga = 'No se pudieron cargar las dudas. Intenta de nuevo.';
                        console.error('Error al cargar tickets super admin:', err);
                    } finally {
                        if (solicitud === this.solicitudCarga) this.cargandoTickets = false;
                    }
                },

                atarPaginacionTickets() {
                    const contenedor = document.getElementById('contenedorListaSuperAdminTickets');
                    if (!contenedor) return;
                    const links = contenedor.querySelectorAll('a[href*="page="]');
                    links.forEach(link => {
                        link.addEventListener('click', async (e) => {
                            e.preventDefault();
                            const href = link.getAttribute('href');
                            if (!href) return;
                            await this.cargarTickets(href);
                        });
                    });
                },

                init() {
                    this.atarPaginacionTickets();
                }
            };
        }
    </script>
</x-app>

