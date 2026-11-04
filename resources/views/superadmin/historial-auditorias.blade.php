<x-app title="Historial de Auditorías | AXStore Super Admin">
    <div class="mx-auto max-w-7xl space-y-6 pb-12">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('superadmin.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 mb-1">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Panel de Gobernanza</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg shrink-0">
                        <i class="fas fa-user-secret"></i>
                    </span>
                    <span>Historial de Sesiones de Auditoría (Impersonation)</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Registro de todas las sesiones de acceso controlado ejecutadas por Super Administradores.
                </p>
            </div>
            
            <a href="{{ route('superadmin.usuarios') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm">
                <i class="fas fa-plus mr-1.5"></i>
                Nueva Auditoría
            </a>
        </div>

        <!-- Tabla de Auditorías -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 font-semibold uppercase text-[10px]">
                            <th class="py-3 px-4">ID Sesión</th>
                            <th class="py-3 px-4">Super Admin Auditor</th>
                            <th class="py-3 px-4">Cuenta Auditada</th>
                            <th class="py-3 px-4">Justificación Registrada</th>
                            <th class="py-3 px-4">Modo / Duración</th>
                            <th class="py-3 px-4">Iniciada</th>
                            <th class="py-3 px-4">Estado</th>
                            <th class="py-3 px-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($auditorias as $aud)
                            <tr class="hover:bg-slate-50/60">
                                <td class="py-3 px-4 font-mono text-[11px] text-slate-500">
                                    #AUD-{{ str_pad($aud->id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-900 block">{{ $aud->superAdmin->nombre_real ?? 'Super Admin' }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $aud->superAdmin->username ?? '—' }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-purple-950 block">{{ $aud->usuarioAuditado->nombre_real ?? 'Usuario' }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $aud->usuarioAuditado->username ?? '—' }} ({{ $aud->usuarioAuditado->rol ?? '—' }})</span>
                                </td>
                                <td class="py-3 px-4 max-w-xs text-slate-600">
                                    <p class="truncate text-[11px] italic" title="{{ $aud->justificacion }}">
                                        "{{ $aud->justificacion }}"
                                    </p>
                                    @if($aud->notas_cierre)
                                        <span class="text-[10px] text-slate-400 block mt-0.5 truncate" title="{{ $aud->notas_cierre }}">
                                            Cierre: {{ $aud->notas_cierre }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $aud->solo_lectura ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $aud->solo_lectura ? 'Solo Lectura' : 'Permisivo' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono text-[11px] text-slate-500">
                                    {{ $aud->iniciada_en->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($aud->estado === 'activa' && $aud->expira_en->isFuture())
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-purple-100 text-purple-700 animate-pulse">
                                            ACTIVA
                                        </span>
                                    @elseif($aud->estado === 'finalizada')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700">
                                            FINALIZADA
                                        </span>
                                    @elseif($aud->estado === 'revocada')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-700">
                                            REVOCADA
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-500">
                                            EXPIRADA
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @if($aud->estado === 'activa' && $aud->expira_en->isFuture())
                                        <form action="{{ route('superadmin.auditorias.revocar', $aud->id) }}" method="POST" onsubmit="return confirm('¿Deseas revocar inmediatamente esta sesión de auditoría?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors">
                                                Revocar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-xs text-slate-400">
                                    No hay registros históricos de sesiones de auditoría.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $auditorias->links() }}
            </div>
        </div>

    </div>
</x-app>

