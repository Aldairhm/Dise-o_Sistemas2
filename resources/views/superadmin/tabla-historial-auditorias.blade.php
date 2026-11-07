<div class="overflow-x-auto">
    <table class="w-full text-left text-xs">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                <th class="py-3 px-4">Sesión</th>
                <th class="py-3 px-4">Super Admin Auditor</th>
                <th class="py-3 px-4">Cuenta Auditada</th>
                <th class="py-3 px-4 min-w-[220px]">Justificación & Cierre</th>
                <th class="py-3 px-4 text-center">Modalidad</th>
                <th class="py-3 px-4">Fecha / Hora</th>
                <th class="py-3 px-4 text-center">Estado</th>
                <th class="py-3 px-4 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
            @forelse($auditorias as $aud)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">
                        <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200/60">
                            #AUD-{{ str_pad($aud->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-bold text-slate-900">{{ $aud->superAdmin->nombre_real }}</div>
                        <div class="text-[11px] text-slate-400 font-mono">{{ $aud->superAdmin->username }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-bold text-slate-900">{{ $aud->usuarioAuditado->nombre_real }}</div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[10px] font-black uppercase px-1.5 py-0.2 rounded bg-slate-100 text-slate-600">
                                {{ $aud->usuarioAuditado->rol }}
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">{{ $aud->usuarioAuditado->username }}</span>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-slate-600">
                        <div class="text-xs line-clamp-2" title="{{ $aud->justificacion }}">
                            {{ $aud->justificacion }}
                        </div>
                        @if($aud->notas_cierre)
                            <div class="text-[11px] text-slate-400 mt-1 italic flex items-center gap-1">
                                <i class="fas fa-check-double text-slate-300"></i>
                                <span class="truncate">Cierre: {{ $aud->notas_cierre }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($aud->solo_lectura)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                                <i class="fas fa-eye text-[9px]"></i>
                                <span>Solo Lectura</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200/60">
                                <i class="fas fa-pen-ruler text-[9px]"></i>
                                <span>Completa</span>
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                        <div>{{ $aud->iniciada_en->format('d/m/Y H:i') }}</div>
                        <div class="text-[10px] text-slate-400">
                            @if($aud->finalizada_en)
                                {{ $aud->iniciada_en->diffInMinutes($aud->finalizada_en) }} min de sesión
                            @else
                                Expira: {{ $aud->expira_en->format('H:i') }}
                            @endif
                        </div>
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($aud->estado === 'activa' && $aud->expira_en > now())
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200 animate-pulse">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                EN CURSO
                            </span>
                        @elseif($aud->estado === 'completada')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700">
                                <i class="fas fa-check text-slate-400 text-[9px]"></i>
                                Completada
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
                                <i class="fas fa-ban text-rose-400 text-[9px]"></i>
                                Revocada
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1.5">
                            @if($aud->estado === 'activa' && $aud->expira_en > now())
                                <form id="formRevocar{{ $aud->id }}" action="{{ route('superadmin.auditorias.revocar', $aud->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="button"
                                            onclick="confirmarRevocacion({{ $aud->id }})"
                                            class="px-2.5 py-1 rounded-lg text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors inline-flex items-center gap-1">
                                        <i class="fas fa-hand text-rose-500"></i>
                                        <span>Revocar</span>
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('superadmin.auditorias.informe', $aud->id) }}"
                               class="px-2.5 py-1 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors inline-flex items-center gap-1">
                                <i class="fas fa-file-shield text-indigo-500"></i>
                                <span>Informe</span>
                            </a>

                            <a href="{{ route('bitacora.index', ['auditoria_id' => $aud->id]) }}"
                               title="Ver trazabilidad de eventos de la sesión #AUD-{{ str_pad($aud->id, 4, '0', STR_PAD_LEFT) }}"
                               class="px-2.5 py-1 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors inline-flex items-center gap-1">
                                <i class="fas fa-clipboard-list text-blue-500"></i>
                                <span>Trazabilidad</span>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="py-8">
                        <x-empty-state 
                            title="No hay registros históricos de sesiones de auditoría" 
                            description="Las auditorías iniciadas bajo gobernanza quedarán documentadas de forma inmutable aquí."
                            icon="folder-open" 
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($auditorias->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $auditorias->links() }}
    </div>
@endif

