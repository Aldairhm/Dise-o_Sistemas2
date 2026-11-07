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
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <x-empty-state 
                title="No hay dudas para Super Administrador" 
                description="En este momento no existen solicitudes pendientes ni casos escalados con estos filtros."
                icon="folder-open" 
            />
        </div>
    @endforelse

    @if ($tickets->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
            {{ $tickets->links() }}
        </div>
    @endif
</div>

