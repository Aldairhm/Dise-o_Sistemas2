<x-app title="Control de Dudas y Tickets de Soporte | AXStore">
    <div class="mx-auto max-w-7xl space-y-6">
        
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
            <div class="flex items-center gap-2">
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

        <!-- Barra de Pestañas por Estado -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
            @php
                $tabs = [
                    'todos'      => ['label' => 'Todos',       'badge_bg' => 'bg-slate-100',   'badge_fg' => 'text-slate-700'],
                    'Pendiente'  => ['label' => 'Pendientes',  'badge_bg' => 'bg-amber-100',   'badge_fg' => 'text-amber-800'],
                    'En proceso' => ['label' => 'En proceso',  'badge_bg' => 'bg-blue-100',    'badge_fg' => 'text-blue-800'],
                    'Resuelta'   => ['label' => 'Resueltas',   'badge_bg' => 'bg-emerald-100', 'badge_fg' => 'text-emerald-800'],
                    'Cancelada'  => ['label' => 'Canceladas',  'badge_bg' => 'bg-rose-100',    'badge_fg' => 'text-rose-800'],
                ];
            @endphp

            @foreach ($tabs as $key => $t)
                <a href="{{ route('ayuda.tickets.index', array_merge(request()->query(), ['estado' => $key, 'page' => 1])) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $estadoTab === $key ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <span>{{ $t['label'] }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $estadoTab === $key ? 'bg-white/20 text-white' : $t['badge_bg'] . ' ' . $t['badge_fg'] }}">
                        {{ $conteoEstados[$key] ?? 0 }}
                    </span>
                </a>
            @endforeach
        </div>

        <!-- Filtros y Búsqueda -->
        <form action="{{ route('ayuda.tickets.index') }}" method="GET" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col sm:flex-row items-center gap-3">
            <input type="hidden" name="estado" value="{{ $estadoTab }}">
            <div class="relative flex-1 w-full">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Buscar por código (#TK-...), usuario, módulo o texto de la duda..."
                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-300">
            </div>

            <div class="w-full sm:w-56 shrink-0">
                <select name="modulo" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-300">
                    <option value="">Todos los módulos</option>
                    @foreach ($modulos as $k => $m)
                        <option value="{{ $m['module'] }}" {{ request('modulo') === $m['module'] ? 'selected' : '' }}>
                            {{ $m['module'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 transition-colors shadow-sm">
                    Filtrar
                </button>
                @if (request()->hasAny(['q', 'modulo']) || $estadoTab !== 'todos')
                    <a href="{{ route('ayuda.tickets.index') }}" class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors" title="Limpiar filtros">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>

        <!-- Tabla de Tickets -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-4 whitespace-nowrap">Ticket</th>
                            <th class="py-3 px-4 whitespace-nowrap">Fecha / Hora</th>
                            <th class="py-3 px-4 whitespace-nowrap">Solicitante</th>
                            <th class="py-3 px-4 whitespace-nowrap">Módulo / Tipo</th>
                            <th class="py-3 px-4 min-w-[260px]">Duda planteada</th>
                            <th class="py-3 px-4 text-center whitespace-nowrap">Evidencia</th>
                            <th class="py-3 px-4 whitespace-nowrap">Estado</th>
                            <th class="py-3 px-4 text-center whitespace-nowrap">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($tickets as $ticket)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- Código de Ticket -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                        {{ $ticket->codigo_ticket }}
                                    </span>
                                </td>

                                <!-- Fecha / Hora -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800">{{ $ticket->created_at->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium">{{ $ticket->created_at->format('h:i A') }} ({{ $ticket->created_at->diffForHumans() }})</div>
                                </td>

                                <!-- Solicitante -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-600 text-white font-bold text-[10px] flex items-center justify-center shrink-0 uppercase">
                                            {{ substr($ticket->nombre_usuario, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 leading-tight">{{ $ticket->nombre_usuario }}</p>
                                            <p class="text-[10px] text-slate-400 uppercase font-semibold">{{ $ticket->rol_usuario ?? 'usuario' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Módulo / Tipo -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-block font-bold text-slate-800 bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-100">
                                        {{ $ticket->modulo }}
                                    </span>
                                    <p class="text-[10px] text-slate-500 mt-0.5 truncate max-w-[140px]">{{ $ticket->tipo_consulta }}</p>
                                </td>

                                <!-- Duda -->
                                <td class="py-3 px-4">
                                    <p class="text-xs text-slate-700 line-clamp-2 leading-relaxed" title="{{ $ticket->duda }}">
                                        {{ $ticket->duda }}
                                    </p>
                                    @if ($ticket->resolucion)
                                        <div class="mt-1.5 p-1.5 rounded bg-emerald-50 border border-emerald-100 text-[10px] text-emerald-800">
                                            <span class="font-bold">Resolución:</span> {{ $ticket->resolucion }}
                                            @if ($ticket->resolvedor)
                                                <span class="text-emerald-600">({{ $ticket->resolvedor->nombre_real }})</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <!-- Evidencia -->
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if ($ticket->evidencia_url)
                                        <a href="{{ $ticket->evidencia_url }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition-colors" title="Abrir imagen de evidencia">
                                            <i class="fas fa-image text-slate-500"></i>
                                            <span>Ver foto</span>
                                        </a>
                                    @else
                                        <span class="text-slate-300 text-[11px]">—</span>
                                    @endif
                                </td>

                                <!-- Estado -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @php
                                        $badgeStyles = [
                                            'Pendiente'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'En proceso' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'Resuelta'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Cancelada'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badgeStyles[$ticket->estado] ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $ticket->estado === 'Resuelta' ? 'bg-emerald-500' : ($ticket->estado === 'Pendiente' ? 'bg-amber-500' : ($ticket->estado === 'En proceso' ? 'bg-blue-500' : 'bg-rose-500')) }}"></span>
                                        {{ $ticket->estado }}
                                    </span>
                                </td>

                                <!-- Acciones -->
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('ayuda.tickets.show', $ticket->id) }}"
                                           class="px-2.5 py-1 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors inline-flex items-center gap-1">
                                            <i class="fas fa-comments text-blue-500"></i>
                                            <span>Hilo</span>
                                        </a>

                                        @if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                            <button type="button"
                                                    onclick='abrirModalGestion(@json($ticket))'
                                                    class="px-2.5 py-1 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
                                                <i class="fas fa-sliders mr-1 text-slate-400"></i>
                                                Gestionar
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <i class="fas fa-ticket text-3xl mb-2 text-slate-300 block"></i>
                                    <p class="font-bold text-sm text-slate-600">No se encontraron tickets registrados</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Las dudas enviadas por WhatsApp se listarán en este panel automáticamente.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if ($tickets->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $tickets->links() }}
                </div>
            @endif
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
                        Nota de Resolución / Solución brindada <span class="text-slate-400 font-normal">(opcional)</span>
                    </label>
                    <textarea id="gestionResolucionTexto"
                              rows="3"
                              placeholder="Describe brevemente cómo se resolvió la duda o la acción tomada..."
                              class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-300"></textarea>
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

        function abrirModalGestion(ticket) {
            ticketActualId = ticket.id;
            document.getElementById('gestionTituloModal').textContent = `Gestionar Ticket #${ticket.codigo_ticket}`;
            document.getElementById('gestionSubtituloModal').textContent = `${ticket.nombre_usuario} · Módulo: ${ticket.modulo}`;
            document.getElementById('gestionEstadoSelect').value = ticket.estado;
            document.getElementById('gestionDudaTexto').textContent = ticket.duda || 'Sin detalle';
            document.getElementById('gestionResolucionTexto').value = ticket.resolucion || '';
            document.getElementById('modalGestionTicket').classList.remove('hidden');
        }

        function cerrarModalGestion() {
            document.getElementById('modalGestionTicket').classList.add('hidden');
            ticketActualId = null;
        }

        async function guardarGestionTicket() {
            if (!ticketActualId) return;

            const estado = document.getElementById('gestionEstadoSelect').value;
            const resolucion = document.getElementById('gestionResolucionTexto').value;
            const btn = document.getElementById('btnGuardarGestion');

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
                    window.location.reload();
                } else {
                    alert(data.message || 'Error al actualizar el ticket');
                }
            } catch (err) {
                console.error(err);
                alert('Ocurrió un error al procesar la solicitud.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Guardar Cambios';
            }
        }
    </script>
</x-app>

