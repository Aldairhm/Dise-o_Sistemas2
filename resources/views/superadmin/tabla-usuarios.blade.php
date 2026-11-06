<div class="overflow-x-auto">
    <table class="w-full text-left text-xs">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 font-semibold uppercase text-[10px]">
                <th class="py-3 px-4">Usuario</th>
                <th class="py-3 px-4">Rol en Sistema</th>
                <th class="py-3 px-4">Teléfono</th>
                <th class="py-3 px-4">Estado</th>
                <th class="py-3 px-4">Fecha Registro</th>
                <th class="py-3 px-4 text-right">Acciones de Auditoría</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
            @forelse($users as $u)
                <tr class="hover:bg-slate-50/60">
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center shrink-0 uppercase text-xs">
                                {{ substr($u->nombre_real, 0, 2) }}
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 block leading-tight">{{ $u->nombre_real }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $u->username }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        @if($u->rol === 'super_admin')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-700">
                                SUPER ADMIN
                            </span>
                        @elseif($u->rol === 'admin')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-100 text-blue-700">
                                ADMIN
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-700">
                                VENDEDOR
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-slate-500 font-mono">
                        {{ $u->telefono ?: '—' }}
                    </td>
                    <td class="py-3 px-4">
                        @if($u->estado == 1)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Inactivo
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">
                        {{ $u->created_at->format('d/m/Y') }}
                    </td>
                    <td class="py-3 px-4 text-right">
                        @if($u->isSuperAdmin())
                            <button type="button"
                                    onclick="abrirModalEditarSuperAdmin({{ $u->id }}, '{{ addslashes($u->nombre_real) }}', '{{ $u->username }}', '{{ $u->telefono ?? '' }}', {{ $u->estado }})"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition-colors inline-flex items-center gap-1.5 cursor-pointer shadow-xs">
                                <i class="fas fa-user-pen text-[11px]"></i>
                                <span>Editar Super Admin</span>
                            </button>
                        @else
                            <button type="button" onclick="abrirModalAuditoria({{ $u->id }}, '{{ addslashes($u->nombre_real) }}', '{{ $u->username }}')" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-purple-50 hover:bg-purple-100 text-purple-700 transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                                <i class="fas fa-user-secret"></i>
                                <span>Auditar Cuenta</span>
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-8">
                        <x-empty-state 
                            title="No se encontraron usuarios" 
                            description="Intenta modificar los filtros de búsqueda o seleccionar otro rol."
                            icon="search" 
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($users->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $users->links() }}
    </div>
@endif

