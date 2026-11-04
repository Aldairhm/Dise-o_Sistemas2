<x-app title="Historial de Auditorías | AXStore Super Admin">
    <div class="mx-auto max-w-7xl space-y-6 pb-12">
        
        <!-- Header con estilo nativo AXStore -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('superadmin.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Panel de Gobernanza</span>
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-base shrink-0 border border-purple-100">
                        <i class="fas fa-user-secret"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black uppercase tracking-[0.18em] text-purple-600">Auditoría Segura</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-100 text-purple-700">Impersonation</span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900">Historial de Sesiones de Auditoría</h1>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('superadmin.usuarios') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-all">
                <i class="fas fa-plus text-xs"></i>
                <span>Iniciar Nueva Auditoría</span>
            </a>
        </div>

        <!-- Tabla de Auditorías -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
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
                                    <span class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200 text-[11px]">
                                        #AUD-{{ str_pad($aud->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-900 block leading-tight">{{ $aud->superAdmin->nombre_real ?? 'Super Admin' }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $aud->superAdmin->username ?? '—' }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-900">{{ $aud->usuarioAuditado->nombre_real ?? 'Usuario' }}</span>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-slate-100 text-slate-600">
                                            {{ $aud->usuarioAuditado->rol ?? '—' }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono block">{{ $aud->usuarioAuditado->username ?? '—' }}</span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    <p class="text-xs text-slate-700 leading-snug font-medium italic" title="{{ $aud->justificacion }}">
                                        "{{ $aud->justificacion }}"
                                    </p>
                                    @if($aud->notas_cierre)
                                        <span class="text-[10px] text-slate-400 block mt-1">
                                            <strong class="text-slate-500">Cierre:</strong> {{ $aud->notas_cierre }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $aud->solo_lectura ? 'bg-slate-100 text-slate-700 border border-slate-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                        {{ $aud->solo_lectura ? 'Solo Lectura' : 'Permisivo' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-800">{{ $aud->iniciada_en->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $aud->iniciada_en->format('h:i A') }}</div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($aud->estado === 'activa' && $aud->expira_en->isFuture())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-purple-100 text-purple-700 animate-pulse border border-purple-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span> ACTIVA
                                        </span>
                                    @elseif($aud->estado === 'finalizada')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fas fa-check text-[9px]"></i> FINALIZADA
                                        </span>
                                    @elseif($aud->estado === 'revocada')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fas fa-ban text-[9px]"></i> REVOCADA
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-600 border border-slate-200">
                                            <i class="fas fa-clock text-[9px]"></i> EXPIRADA
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($aud->estado === 'activa' && $aud->expira_en->isFuture())
                                            <form id="formRevocar{{ $aud->id }}" action="{{ route('superadmin.auditorias.revocar', $aud->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="button" onclick="confirmarRevocacion({{ $aud->id }})" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors inline-flex items-center gap-1 cursor-pointer">
                                                    <i class="fas fa-ban text-[10px]"></i>
                                                    <span>Revocar</span>
                                                </button>
                                            </form>
                                        @endif

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
                                <td colspan="8" class="py-10 text-center text-xs text-slate-400">
                                    <i class="fas fa-shield-cat text-2xl text-slate-300 mb-2 block"></i>
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

    <script>
        function confirmarRevocacion(id) {
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
                    document.getElementById('formRevocar' + id).submit();
                }
            });
        }
    </script>
</x-app>

