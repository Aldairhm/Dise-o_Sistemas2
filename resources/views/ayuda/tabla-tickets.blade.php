<div class="space-y-4">
    @forelse ($tickets as $ticket)
        @php
            $badgeStyles = [
                'Pendiente'  => 'bg-amber-100 text-amber-800',
                'En proceso' => 'bg-blue-100 text-blue-800',
                'Escalada'   => 'bg-purple-100 text-purple-800',
                'Resuelta'   => 'bg-emerald-100 text-emerald-800',
                'Cancelada'  => 'bg-rose-100 text-rose-800',
            ];

            $esMio = ($ticket->id_usuario === auth()->id());
            $puedeGestionar = (auth()->user()->isSuperAdmin() || (auth()->user()->isAdmin() && !$esMio))
                && (!in_array($ticket->estado, ['Resuelta', 'Cancelada']) || auth()->user()->isSuperAdmin());
            $puedeCancelarPropio = $esMio && in_array($ticket->estado, ['Pendiente', 'En proceso']);
        @endphp

        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-4 sm:p-6 hover:border-blue-200 hover:shadow-md transition-all flex flex-col md:flex-row md:items-start justify-between gap-6 min-w-0">
            <div class="space-y-3 flex-1 min-w-0">
                <!-- Badges Superiores -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono text-xs font-black text-blue-900 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">
                        #{{ $ticket->codigo_ticket }}
                    </span>
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold {{ $badgeStyles[$ticket->estado] ?? 'bg-slate-100 text-slate-700' }}">
                        {{ $ticket->estado }}
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs font-bold text-slate-600 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100">
                        {{ $ticket->modulo }}
                    </span>
                    @if($ticket->tipo_consulta)
                        <span class="text-[11px] text-slate-400">({{ $ticket->tipo_consulta }})</span>
                    @endif
                </div>

                <!-- Título y Detalle de la Duda -->
                <div>
                    <h2 class="text-base font-black text-slate-900 leading-snug">
                        {{ $ticket->titulo ?? 'Consulta sobre ' . $ticket->modulo }}
                    </h2>
                    <p class="text-xs text-slate-600 mt-1.5 leading-relaxed whitespace-pre-line break-words">
                        {{ $ticket->duda }}
                    </p>
                </div>

                <!-- Resolución si ya existe -->
                @if ($ticket->resolucion)
                    <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200 text-xs text-emerald-900">
                        <span class="font-bold flex items-center gap-1.5 text-emerald-800 mb-0.5">
                            <i class="fas fa-check-circle"></i> Resolución:
                        </span>
                        {{ $ticket->resolucion }}
                        @if ($ticket->resolvedor)
                            <span class="block text-[10px] text-emerald-700 mt-1">
                                Atendido por: {{ $ticket->resolvedor->nombre_real }}
                            </span>
                        @endif
                    </div>
                @endif

                <!-- Metadata Inferior: Creador, Fecha, Evidencia -->
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-[11px] text-slate-400 pt-2 border-t border-slate-100 break-words">
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-user text-slate-300"></i>
                        Creador: <strong class="text-slate-700 font-semibold">{{ $ticket->nombre_usuario }}</strong> ({{ $ticket->rol_usuario ?? 'usuario' }})
                    </span>
                    <span>&bull;</span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-calendar-day text-slate-300"></i>
                        Fecha: {{ $ticket->created_at->format('d/m/Y H:i') }} ({{ $ticket->created_at->diffForHumans() }})
                    </span>

                    @if ($ticket->evidencia_url)
                        <span>&bull;</span>
                        <a href="{{ $ticket->evidencia_url }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 font-bold hover:underline">
                            <i class="fas fa-paperclip"></i>
                            <span>Ver Evidencia Adjunta</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Botones de Acción Laterales -->
            <div class="flex flex-col sm:flex-row md:flex-col items-stretch sm:items-center md:items-stretch gap-2 shrink-0 w-full md:w-auto">
                <a href="{{ route('ayuda.tickets.show', $ticket->id) }}"
                   class="inline-flex w-full md:w-auto items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-sm hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-comments text-xs"></i>
                    <span>Ver Conversación</span>
                </a>

                @if ($puedeGestionar)
                    <button type="button"
                            onclick='abrirModalGestion(@json($ticket))'
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
                        <i class="fas fa-sliders text-slate-400"></i>
                        <span>Gestionar</span>
                    </button>
                @elseif ($puedeCancelarPropio)
                    <button type="button"
                            onclick='abrirModalGestion(@json($ticket))'
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors cursor-pointer">
                        <i class="fas fa-ban text-rose-400"></i>
                        <span>Cancelar Duda</span>
                    </button>
                @else
                    <span class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-xl text-[10px] font-bold text-slate-400 bg-slate-50 border border-slate-100">
                        <i class="fas fa-lock"></i>
                        <span>Cerrado</span>
                    </span>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <x-empty-state 
                title="No se encontraron tickets registrados" 
                description="Las consultas o dudas enviadas por los usuarios se listarán en este panel automáticamente."
                icon="folder-open" 
            />
        </div>
    @endforelse

    <!-- Paginación -->
    @if ($tickets->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
