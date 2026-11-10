<x-app title="Control de Dudas y Tickets de Soporte | AXStore">
    <div class="mx-auto max-w-7xl space-y-6" x-data="gestionTicketsAyuda({
        estadoTab: @js($estadoTab),
        soloMisDudas: @js($soloMisDudas),
        misDudasCount: @js($misDudasCount),
        conteoEstados: @js($conteoEstados)
    })">
        
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('ayuda.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 mb-1">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver a Guías de Ayuda</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                        <i class="fab fa-whatsapp"></i>
                    </span>
                    <span>Control de Dudas y Tickets de Soporte</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Seguimiento de consultas enviadas por el equipo, estado de resolución y registro de auditoría.
                </p>
            </div>
            <div class="flex w-full sm:w-auto flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <a href="{{ route('ayuda.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-sm">
                    <i class="fas fa-book-open mr-1.5 text-slate-400"></i>
                    Ver Guías
                </a>
                <a href="{{ route('bitacora.index', ['modulo' => 'Soporte']) }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-sm">
                    <i class="fas fa-clock-rotate-left mr-1.5 text-slate-400"></i>
                    Bitácora de Soporte
                </a>
            </div>
        </div>

        <!-- Barra de Pestañas por Ámbito y Estado -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
            <div class="flex flex-wrap items-center gap-2">
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

            <!-- Selector de Ámbito (Mis Dudas vs Bandeja de Equipo) para roles con permisos administrativos -->
            @if(auth()->user()->isAdmin())
                <div class="flex w-full sm:w-auto flex-col min-[420px]:flex-row items-stretch p-1 rounded-xl bg-slate-100 border border-slate-200 shrink-0">
                    <button type="button"
                       @click="cambiarAmbito(false)"
                       :class="!soloMisDudas ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                       class="flex-1 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                        <i class="fas fa-inbox mr-1 text-[11px]"></i>
                        <span>Bandeja de Equipo</span>
                    </button>
                    <button type="button"
                       @click="cambiarAmbito(true)"
                       :class="soloMisDudas ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                       class="flex-1 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                        <i class="fas fa-user mr-1 text-[11px]"></i>
                        <span>Mis Dudas</span>
                        <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] font-black"
                              :class="soloMisDudas ? 'bg-white/25 text-white' : 'bg-slate-200 text-slate-700'"
                              x-text="misDudasCount">
                            {{ $misDudasCount }}
                        </span>
                    </button>
                </div>
            @endif
        </div>

        <template x-if="soloMisDudas">
            <div class="p-3.5 rounded-xl bg-blue-50/80 border border-blue-200 text-blue-900 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-filter text-blue-600 text-sm"></i>
                    <span>Mostrando <strong>exclusivamente tus dudas personales</strong> registradas en el sistema.</span>
                </div>
                <button type="button" @click="cambiarAmbito(false)" class="font-bold underline hover:text-blue-700 cursor-pointer">
                    Ver todas las dudas asignadas
                </button>
            </div>
        </template>

        <!-- Filtros y Búsqueda -->
        <form @submit.prevent="ejecutarFiltro()" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       x-model="filtroQ"
                       @input.debounce.400ms="ejecutarFiltro()"
                       placeholder="Buscar por código (#TK-...), usuario, módulo o texto de la duda..."
                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-300">
            </div>

            <div class="w-full sm:w-56 shrink-0">
                <select x-model="filtroModulo" @change="ejecutarFiltro()" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-300">
                    <option value="">Todos los módulos</option>
                    @foreach ($modulos as $k => $m)
                        <option value="{{ $m['module'] }}">
                            {{ $m['module'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                <button type="submit" 
                        class="w-full sm:w-auto rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-3 text-xs transition-all shadow-md shadow-blue-600/15 flex items-center justify-center gap-1.5 cursor-pointer">
                    <i :class="cargandoTickets ? 'fas fa-spinner fa-spin' : 'fas fa-filter'"></i>
                    <span x-text="cargandoTickets ? 'Filtrando...' : 'Filtrar'"></span>
                </button>
                <button type="button" 
                        @click="limpiarFiltros()"
                        x-show="filtroQ || filtroModulo || estadoTab !== 'todos'"
                        style="display: none;"
                        class="rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold py-2 px-3 text-xs transition-colors flex items-center justify-center cursor-pointer" 
                        title="Limpiar filtros">
                    <i class="fas fa-rotate-left"></i>
                </button>
            </div>
        </form>

        <!-- Skeleton Loading Dinámico (Refleja exactamente las tarjetas de Tickets) -->
        <div x-show="cargandoTickets" style="display: none;" class="space-y-4 select-none" role="status" aria-label="Cargando tickets...">
            @for($i = 0; $i < 3; $i++)
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 flex flex-col md:flex-row md:items-start justify-between gap-6">
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
                    <div class="flex flex-row md:flex-col items-center gap-2 shrink-0">
                        <div class="h-10 w-36 rounded-xl ax-skeleton-shimmer"></div>
                        <div class="h-9 w-28 rounded-xl ax-skeleton-shimmer"></div>
                    </div>
                </div>
            @endfor
            <span class="sr-only">Cargando tickets...</span>
        </div>

        <!-- Contenedor Asíncrono de Tickets -->
        <div id="contenedorListaTickets" x-show="!cargandoTickets" :aria-busy="cargandoTickets">
            @include('ayuda.tabla-tickets', ['tickets' => $tickets])
        </div>
    </div>
    </div>

    <!-- ─── MODAL DE GESTIÓN Y RESOLUCIÓN DE TICKET (ADMIN) ─── -->
    <div id="modalGestionTicket" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800" id="gestionTituloModal">Gestionar Ticket</h3>
                    <p class="text-[11px] text-slate-500" id="gestionSubtituloModal">Actualizar estado y resolución</p>
                </div>
                <button type="button" onclick="cerrarModalGestion()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="p-5 space-y-3.5">
                <!-- Estado nuevo -->
                <div>
                    <label for="gestionEstadoSelect" class="block text-xs font-bold text-slate-700 mb-1">
                        Estado del Ticket
                    </label>
                    <select id="gestionEstadoSelect" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-300">
                        <option value="Pendiente">Pendiente</option>
                        <option value="En proceso">En proceso</option>
                        <option value="Escalada">Escalada</option>
                        <option value="Resuelta">Resuelta</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>

                <!-- Detalle de la duda -->
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                    <span class="block text-[10px] font-bold uppercase text-slate-400 mb-0.5">Duda del usuario</span>
                    <p id="gestionDudaTexto" class="italic text-slate-700 line-clamp-3">...</p>
                </div>

                <!-- Resolución o Nota Técnica -->
                <div>
                    <label for="gestionResolucionTexto" class="block text-xs font-bold text-slate-700 mb-1">
                        Nota de Resolución / Solución brindada <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="gestionResolucionTexto"
                              rows="3"
                              placeholder="Describe obligatoriamente cómo se resolvió la duda o la acción tomada..."
                              class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all resize-none"></textarea>
                    <p id="errorGestionResolucion" class="hidden text-[11px] font-bold text-rose-600 flex items-center gap-1 mt-1 pl-1">
                        <i class="fas fa-circle-exclamation text-[10px]"></i>
                        <span>Debes ingresar la nota de resolución o motivo del cambio (mínimo 5 caracteres).</span>
                    </p>
                </div>
            </div>

            <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="cerrarModalGestion()" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200 transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="button"
                        id="btnGuardarGestion"
                        onclick="guardarGestionTicket()"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 transition-colors shadow-sm cursor-pointer">
                    Guardar Cambios
                </button>
            </div>
        </div>
    </div>

    <script>
        let ticketActualId = null;
        const currentUserId = {{ auth()->id() }};
        const isSuperAdmin = {{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }};

        function abrirModalGestion(ticket) {
            ticketActualId = ticket.id;
            const esMio = (ticket.id_usuario === currentUserId);

            document.getElementById('gestionTituloModal').textContent = esMio && !isSuperAdmin
                ? `Cancelar Mi Duda #${ticket.codigo_ticket}`
                : `Gestionar Ticket #${ticket.codigo_ticket}`;

            document.getElementById('gestionSubtituloModal').textContent = `${ticket.nombre_usuario} · Módulo: ${ticket.modulo}`;
            
            const selectEstado = document.getElementById('gestionEstadoSelect');
            if (esMio && !isSuperAdmin) {
                selectEstado.innerHTML = `<option value="Cancelada" selected>Cancelada (Cancelar mi propia duda)</option>`;
            } else {
                selectEstado.innerHTML = `
                    <option value="Pendiente">Pendiente</option>
                    <option value="En proceso">En proceso</option>
                    <option value="Escalada">Escalada</option>
                    <option value="Resuelta">Resuelta</option>
                    <option value="Cancelada">Cancelada</option>
                `;
                selectEstado.value = ticket.estado;
            }

            document.getElementById('gestionDudaTexto').textContent = ticket.duda || 'Sin detalle';
            document.getElementById('gestionResolucionTexto').value = ticket.resolucion || '';
            document.getElementById('modalGestionTicket').classList.remove('hidden');
        }

        function cerrarModalGestion() {
            document.getElementById('modalGestionTicket').classList.add('hidden');
            const resArea = document.getElementById('gestionResolucionTexto');
            const errP = document.getElementById('errorGestionResolucion');
            if (resArea && errP) {
                resArea.classList.remove('border-rose-500', 'bg-rose-50/40', 'ring-1', 'ring-rose-400');
                resArea.classList.add('border-slate-300');
                errP.classList.add('hidden');
            }
            ticketActualId = null;
        }

        document.getElementById('gestionResolucionTexto')?.addEventListener('input', function() {
            if (this.value.trim().length >= 5) {
                this.classList.remove('border-rose-500', 'bg-rose-50/40', 'ring-1', 'ring-rose-400');
                this.classList.add('border-slate-300');
                document.getElementById('errorGestionResolucion')?.classList.add('hidden');
            }
        });

        async function guardarGestionTicket() {
            if (!ticketActualId) return;

            const estado = document.getElementById('gestionEstadoSelect').value;
            const resArea = document.getElementById('gestionResolucionTexto');
            const resolucion = resArea.value.trim();
            const errP = document.getElementById('errorGestionResolucion');
            const btn = document.getElementById('btnGuardarGestion');

            if (!resolucion || resolucion.length < 5) {
                resArea.classList.remove('border-slate-300');
                resArea.classList.add('border-rose-500', 'bg-rose-50/40', 'ring-1', 'ring-rose-400');
                errP?.classList.remove('hidden');
                resArea.focus();
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';

            try {
                const response = await fetch(`/ayuda/tickets/${ticketActualId}/estado`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ estado, resolucion })
                });

                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Estado Actualizado!',
                        text: data.message || 'El ticket ha sido modificado correctamente.',
                        timer: 1600,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Transición No Permitida',
                        text: data.message || 'No se pudo actualizar el ticket.',
                        confirmButtonColor: '#1e293b',
                        confirmButtonText: 'Entendido'
                    });
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Servidor',
                    text: 'Ocurrió un error al procesar la solicitud.',
                    confirmButtonColor: '#1e293b'
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Guardar Cambios';
            }
        }

        function gestionTicketsAyuda(config) {
            return {
                estadoTab: config.estadoTab || 'todos',
                soloMisDudas: Boolean(config.soloMisDudas),
                misDudasCount: config.misDudasCount || 0,
                conteoEstados: config.conteoEstados || {},
                filtroQ: '{{ request('q') }}',
                filtroModulo: '{{ request('modulo') }}',
                cargandoTickets: false,
                solicitudTickets: 0,

                async cambiarEstado(estado) {
                    if (this.estadoTab === estado && !this.cargandoTickets) return;
                    this.estadoTab = estado;
                    await this.cargarTickets();
                },

                async cambiarAmbito(soloMias) {
                    if (this.soloMisDudas === soloMias && !this.cargandoTickets) return;
                    this.soloMisDudas = soloMias;
                    await this.cargarTickets();
                },

                async ejecutarFiltro() {
                    await this.cargarTickets();
                },

                async limpiarFiltros() {
                    this.filtroQ = '';
                    this.filtroModulo = '';
                    this.estadoTab = 'todos';
                    await this.cargarTickets();
                },

                construirUrl(pagina = 1) {
                    const params = new URLSearchParams();
                    if (this.estadoTab && this.estadoTab !== 'todos') {
                        params.set('estado', this.estadoTab);
                    }
                    if (this.soloMisDudas) {
                        params.set('mis_dudas', '1');
                    }
                    if (this.filtroQ && this.filtroQ.trim() !== '') {
                        params.set('q', this.filtroQ.trim());
                    }
                    if (this.filtroModulo && this.filtroModulo !== '') {
                        params.set('modulo', this.filtroModulo);
                    }
                    if (pagina > 1) {
                        params.set('page', pagina);
                    }

                    const queryString = params.toString();
                    return `{{ route('ayuda.tickets.index') }}${queryString ? '?' + queryString : ''}`;
                },

                async cargarTickets(urlDestino = null) {
                    const solicitud = ++this.solicitudTickets;
                    this.cargandoTickets = true;
                    const url = urlDestino || this.construirUrl(1);

                    try {
                        const res = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok || res.redirected) throw new Error('No se pudieron cargar los tickets.');
                        const data = await res.json();
                        if (solicitud !== this.solicitudTickets) return;
                        if (!data.success || typeof data.html !== 'string') throw new Error(data.message || 'Respuesta de tickets inválida.');
                        window.history.pushState({}, '', url);
                        if (data.success) {
                            const contenedor = document.getElementById('contenedorListaTickets');
                            if (contenedor) {
                                contenedor.innerHTML = data.html;
                                this.atarPaginacionTickets();
                            }
                            if (data.conteoEstados) {
                                this.conteoEstados = data.conteoEstados;
                            }
                            if (data.misDudasCount !== undefined) {
                                this.misDudasCount = data.misDudasCount;
                            }
                        }
                    } catch (err) {
                        if (solicitud !== this.solicitudTickets) return;
                        window.Swal?.fire({ icon: 'error', title: 'No se pudieron cargar los tickets', text: 'Inténtalo de nuevo. Se conserva la lista anterior.' });
                        console.error('Error al cargar tickets:', err);
                    } finally {
                        if (solicitud === this.solicitudTickets) this.cargandoTickets = false;
                    }
                },

                atarPaginacionTickets() {
                    const contenedor = document.getElementById('contenedorListaTickets');
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
