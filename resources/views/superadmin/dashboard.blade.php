<x-app title="Panel de Control Super Administrador | AXStore">
    <div class="mx-auto max-w-7xl space-y-6 pb-12">
        
        <!-- 1. ENCABEZADO PRINCIPAL (Estilo Oficial AXStore) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-lg shadow-slate-900/20">
                        <i class="fas fa-shield-halved text-xl text-blue-400"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-black uppercase tracking-[0.18em] text-blue-600">Gobernanza & Auditoría</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-700">SUPER ADMIN</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Panel de Super Administrador</h1>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN RÁPIDA -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('superadmin.usuarios') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-sm transition-all hover:border-blue-300">
                    <i class="fas fa-users-gear text-blue-600"></i>
                    <span>Gestión de Cuentas</span>
                </a>

                <a href="{{ route('superadmin.tickets') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-headset"></i>
                    <span>Centro de Dudas</span>
                </a>

                <a href="{{ route('superadmin.auditorias.historial') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-sm transition-all hover:border-purple-300">
                    <i class="fas fa-user-secret text-purple-600"></i>
                    <span>Auditorías</span>
                </a>
            </div>
        </div>

        <!-- 2. SUB-NAV / PESTAÑAS DE GOBERNANZA -->
        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones de superadministración">
            <a href="{{ route('superadmin.dashboard') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-gauge-high"></i>
                <span>Vista General</span>
            </a>
            <a href="{{ route('superadmin.usuarios') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-users-gear text-slate-400"></i>
                <span>Usuarios & Permisos</span>
            </a>
            <a href="{{ route('superadmin.tickets') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-bolt text-slate-400"></i>
                <span>Casos Escalados</span>
            </a>
            <a href="{{ route('superadmin.auditorias.historial') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-user-shield text-slate-400"></i>
                <span>Sesiones de Auditoría</span>
            </a>
            <a href="{{ route('bitacora.index') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-clipboard-list text-slate-400"></i>
                <span>Bitácora Global</span>
            </a>
        </nav>

        <!-- Indicadores KPI Globales -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Usuarios</span>
                <span class="text-2xl font-black text-slate-800 mt-1">{{ $stats['total_usuarios'] }}</span>
                <span class="text-[11px] text-emerald-600 font-medium mt-auto pt-2 flex items-center gap-1">
                    <i class="fas fa-circle-check"></i> {{ $stats['usuarios_activos'] }} activos
                </span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Super Admins</span>
                <span class="text-2xl font-black text-indigo-600 mt-1">{{ $stats['total_superadmins'] }}</span>
                <span class="text-[11px] text-slate-400 font-normal mt-auto pt-2">Máximo privilegio</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Administradores</span>
                <span class="text-2xl font-black text-blue-600 mt-1">{{ $stats['total_admins'] }}</span>
                <span class="text-[11px] text-slate-400 font-normal mt-auto pt-2">Gestión operativa</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Vendedores</span>
                <span class="text-2xl font-black text-slate-700 mt-1">{{ $stats['total_vendedores'] }}</span>
                <span class="text-[11px] text-slate-400 font-normal mt-auto pt-2">Fuerza comercial</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dudas Escaladas</span>
                <span class="text-2xl font-black text-amber-600 mt-1">{{ $stats['dudas_escaladas'] }}</span>
                <span class="text-[11px] text-amber-600 font-semibold mt-auto pt-2 flex items-center gap-1">
                    <i class="fas fa-triangle-exclamation"></i> Requieren atención
                </span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Auditorías Vivas</span>
                <span class="text-2xl font-black text-purple-600 mt-1">{{ $stats['auditorias_activas'] }}</span>
                <span class="text-[11px] text-purple-600 font-medium mt-auto pt-2">Sesiones en curso</span>
            </div>
        </div>

        <!-- Secciones Principales: Dudas Escaladas + Auditorías Recientes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Casos y Dudas que conciernen al Super Administrador -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                            <i class="fas fa-bolt"></i>
                        </span>
                        <div>
                            <h2 class="text-base font-black text-slate-800">Casos Escalados y Consultas Directas</h2>
                            <p class="text-xs text-slate-400">Solicitudes enviadas por administradores o casos de alta prioridad</p>
                        </div>
                    </div>
                    <a href="{{ route('superadmin.tickets') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Ver todo &rarr;
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($ticketsSuperAdmin as $tk)
                        <div class="py-3 flex items-start justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-800">#{{ $tk->codigo_ticket }}</span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold 
                                        {{ $tk->estado === 'Escalada' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $tk->estado }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold 
                                        {{ $tk->prioridad === 'Urgente' ? 'bg-rose-100 text-rose-800' : 'bg-blue-50 text-blue-700' }}">
                                        {{ $tk->prioridad }}
                                    </span>
                                </div>
                                <h3 class="text-xs font-bold text-slate-700 line-clamp-1">
                                    {{ $tk->titulo ?? $tk->modulo }}
                                </h3>
                                <p class="text-[11px] text-slate-500 line-clamp-2">{{ $tk->duda }}</p>
                                <div class="flex items-center gap-2 text-[10px] text-slate-400 pt-1">
                                    <span><i class="fas fa-user text-slate-300"></i> {{ $tk->nombre_usuario }} ({{ $tk->rol_usuario }})</span>
                                    <span>&bull;</span>
                                    <span>{{ $tk->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <a href="{{ route('ayuda.tickets.show', $tk->id) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0 transition-colors">
                                Revisar
                            </a>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            <i class="fas fa-check-circle text-2xl text-emerald-400 mb-2 block"></i>
                            No hay casos escalados pendientes de atención en este momento.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Trazabilidad de Auditorías Recientes (Secure Impersonation) -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold">
                            <i class="fas fa-user-shield"></i>
                        </span>
                        <div>
                            <h2 class="text-base font-black text-slate-800">Sesiones de Auditoría Recientes</h2>
                            <p class="text-xs text-slate-400">Accesos administrativos controlados con trazabilidad de doble identidad</p>
                        </div>
                    </div>
                    <a href="{{ route('superadmin.auditorias.historial') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Ver todo &rarr;
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($ultimasAuditorias as $aud)
                        <div class="py-3 flex items-start justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-800">
                                        {{ $aud->usuarioAuditado->nombre_real ?? 'Usuario' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold 
                                        {{ $aud->estado === 'activa' ? 'bg-purple-100 text-purple-800 animate-pulse' : 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($aud->estado) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono">
                                        {{ $aud->solo_lectura ? 'Solo Lectura' : 'Permisivo' }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 italic">"{{ Str::limit($aud->justificacion, 80) }}"</p>
                                <div class="text-[10px] text-slate-400">
                                    Auditor: {{ $aud->superAdmin->nombre_real ?? 'Super Admin' }} &bull; {{ $aud->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            @if($aud->estado === 'activa')
                                <form id="formRevocarDash{{ $aud->id }}" action="{{ route('superadmin.auditorias.revocar', $aud->id) }}" method="POST">
                                    @csrf
                                    <button type="button" onclick="confirmarRevocacionDash({{ $aud->id }})" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors cursor-pointer">
                                        Revocar
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            <i class="fas fa-shield-cat text-2xl text-slate-300 mb-2 block"></i>
                            No se han registrado sesiones de auditoría en los últimos días.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Eventos de Auditoría y Seguridad del Sistema -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-clock-rotate-left"></i>
                    </span>
                    <div>
                        <h2 class="text-base font-black text-slate-800">Eventos de Seguridad Críticos</h2>
                        <p class="text-xs text-slate-400">Registro inmutable en bitácora de inicios de sesión, cambios y auditorías</p>
                    </div>
                </div>
                <a href="{{ route('bitacora.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                    Explorar Bitácora Completa &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-semibold uppercase text-[10px]">
                            <th class="py-2.5 px-3">Fecha / Hora</th>
                            <th class="py-2.5 px-3">Acción</th>
                            <th class="py-2.5 px-3">Módulo</th>
                            <th class="py-2.5 px-3">Actor Real / Usuario</th>
                            <th class="py-2.5 px-3">Descripción</th>
                            <th class="py-2.5 px-3">IP Origen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach($eventosSeguridad as $ev)
                            <tr class="hover:bg-slate-50/60">
                                <td class="py-2.5 px-3 font-mono text-[11px] text-slate-400">
                                    {{ $ev->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="py-2.5 px-3 font-bold text-slate-800">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-[10px]">
                                        {{ $ev->accion }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-slate-500 font-medium">
                                    {{ $ev->modulo }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="font-bold text-slate-700">{{ $ev->nombre_usuario ?? $ev->username ?? 'Anónimo' }}</span>
                                    @if($ev->en_modo_auditoria)
                                        <span class="block text-[9px] text-purple-600 font-bold">
                                            (Auditado por {{ $ev->username_actor_real }})
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 max-w-xs truncate text-slate-600">
                                    {{ $ev->descripcion }}
                                </td>
                                <td class="py-2.5 px-3 font-mono text-[10px] text-slate-400">
                                    {{ $ev->ip_address }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        function confirmarRevocacionDash(id) {
            Swal.fire({
                title: '¿Revocar Sesión de Auditoría?',
                text: 'Esta acción cancelará inmediatamente el acceso temporal de supervisión y reactivará la cuenta del usuario para el inicio de sesión ordinario.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, revocar acceso',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formRevocarDash' + id).submit();
                }
            });
        }
    </script>
</x-app>

