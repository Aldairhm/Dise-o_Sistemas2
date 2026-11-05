<x-app title="Centro de Dudas Administrativas | AXStore Super Admin">
    <div class="mx-auto max-w-7xl space-y-6 pb-12">
        
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
                <a href="{{ route('superadmin.tickets', array_merge(request()->query(), ['estado' => $key, 'page' => 1])) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $estadoTab === $key ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <span>{{ $t['label'] }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $estadoTab === $key ? 'bg-white/20 text-white' : $t['badge_bg'] . ' ' . $t['badge_fg'] }}">
                        {{ $conteoEstados[$key] ?? 0 }}
                    </span>
                </a>
            @endforeach
        </div>

        <!-- Filtros y Búsqueda -->
        <form method="GET" action="{{ route('superadmin.tickets') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center gap-3">
            <input type="hidden" name="estado" value="{{ $estadoTab }}">
            <div class="flex-1 min-w-[240px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por código, título, duda o usuario..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition-colors">
                    Filtrar
                </button>
                @if (request()->hasAny(['search']) || $estadoTab !== 'Pendiente')
                    <a href="{{ route('superadmin.tickets') }}" class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors" title="Restablecer filtros">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>

        <!-- Lista de Tickets -->
        <div class="space-y-4">
            @forelse($tickets as $tk)
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 hover:border-indigo-200 transition-all flex flex-col md:flex-row md:items-start justify-between gap-6">
                    <div class="space-y-3 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-xs font-black text-indigo-900 bg-indigo-50 px-2.5 py-1 rounded-lg">
                                #{{ $tk->codigo_ticket }}
                            </span>
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold 
                                {{ $tk->estado === 'Escalada' ? 'bg-amber-100 text-amber-800' : ($tk->estado === 'Resuelta' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700') }}">
                                {{ $tk->estado }}
                            </span>
                            <span class="text-xs text-slate-400">&bull;</span>
                            <span class="text-xs font-bold text-slate-600">{{ $tk->modulo }}</span>
                        </div>

                        <div>
                            <h2 class="text-base font-black text-slate-900">
                                {{ $tk->titulo ?? 'Consulta sobre ' . $tk->modulo }}
                            </h2>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                {{ $tk->duda }}
                            </p>
                        </div>

                        @if($tk->justificacion_escalamiento)
                            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900">
                                <span class="font-bold flex items-center gap-1.5 text-amber-800 mb-0.5">
                                    <i class="fas fa-triangle-exclamation"></i> Justificación de escalamiento:
                                </span>
                                {{ $tk->justificacion_escalamiento }}
                                @if($tk->escalador)
                                    <span class="block text-[10px] text-amber-700 mt-1">
                                        Escalado por: {{ $tk->escalador->nombre_real }} (Admin)
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="flex flex-wrap items-center gap-4 text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-user text-slate-300"></i> Creador: <strong class="text-slate-600">{{ $tk->nombre_usuario }}</strong> ({{ $tk->rol_usuario }})
                            </span>
                            <span>&bull;</span>
                            <span>Fecha: {{ $tk->created_at->format('d/m/Y H:i') }}</span>
                            @if($tk->mensajes_count ?? false)
                                <span>&bull;</span>
                                <span class="font-bold text-indigo-600"><i class="fas fa-comments"></i> {{ $tk->mensajes_count }} mensajes</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row md:flex-col gap-2 shrink-0">
                        <a href="{{ route('ayuda.tickets.show', $tk->id) }}" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white text-center transition-colors shadow-sm">
                            <i class="fas fa-comments mr-1.5"></i> Ver Conversación
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center text-slate-400">
                    <i class="fas fa-inbox text-3xl mb-2 text-slate-300"></i>
                    <p class="text-sm font-medium">No hay dudas que requieran atención de Super Administrador en este momento.</p>
                </div>
            @endforelse

            <div class="p-4">
                {{ $tickets->links() }}
            </div>
        </div>

    </div>
</x-app>

