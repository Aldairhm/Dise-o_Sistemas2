<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                @if($isAdmin)
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Vendedor</th>
                @endif
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Salida #</th>
                <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">%</th>
                <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Monto</th>
                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Notas</th>
                @if($isAdmin)
                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($comisiones as $c)
            <tr class="hover:bg-slate-50/60 transition-colors" data-id="{{ $c->id }}">

                @if($isAdmin)
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($c->vendedor->nombre_real ?? '?', 0, 2)) }}
                        </div>
                        <span class="font-medium text-slate-800">{{ $c->vendedor->nombre_real ?? '—' }}</span>
                    </div>
                </td>
                @endif

                <td class="px-5 py-4 text-slate-600 whitespace-nowrap">
                    {{ $c->fecha_registro ? $c->fecha_registro->format('d/m/Y') : '—' }}
                </td>

                <td class="px-5 py-4 text-slate-600">
                    @if($c->id_salida)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 rounded-lg text-xs font-mono font-bold text-slate-700">
                            #{{ $c->id_salida }}
                        </span>
                    @else
                        <span class="text-slate-400 text-xs">Manual</span>
                    @endif
                </td>

                <td class="px-5 py-4 text-right font-mono text-slate-600">
                    {{ number_format($c->porcentaje, 1) }}%
                </td>

                <td class="px-5 py-4 text-right">
                    <span class="font-bold text-slate-900">${{ number_format($c->monto, 2) }}</span>
                </td>

                <td class="px-5 py-4 text-center">
                    @if($c->estado === 'Pendiente')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Pendiente
                        </span>
                    @elseif($c->estado === 'Pagada')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i class="fas fa-check text-[10px]"></i>
                            Pagada
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                            <i class="fas fa-ban text-[10px]"></i>
                            Cancelada
                        </span>
                    @endif
                </td>

                <td class="px-5 py-4 text-slate-500 text-xs max-w-[160px] truncate" title="{{ $c->notas }}">
                    {{ $c->notas ?? '—' }}
                </td>

                @if($isAdmin)
                <td class="px-5 py-4">
                    <div class="flex items-center justify-center gap-1.5">
                        @if($c->estado === 'Pendiente')
                            {{-- Editar --}}
                            <button type="button"
                                    onclick="openEditModal({{ $c->id }}, '{{ $c->monto }}', '{{ $c->porcentaje }}', '{{ addslashes($c->notas ?? '') }}')"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer"
                                    title="Editar monto/notas">
                                <i class="fas fa-pen text-xs"></i>
                            </button>
                            {{-- Cancelar --}}
                            <button type="button"
                                    onclick="cancelarComision({{ $c->id }})"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors cursor-pointer"
                                    title="Cancelar comisión">
                                <i class="fas fa-ban text-xs"></i>
                            </button>
                        @else
                            <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </div>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ $isAdmin ? 8 : 6 }}" class="px-5 py-14 text-center">
                    <div class="flex flex-col items-center gap-3 text-slate-400">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl">
                            <i class="fas fa-coins"></i>
                        </div>
                        <p class="font-semibold text-slate-500">No hay comisiones registradas</p>
                        <p class="text-sm">Ajusta los filtros o espera a que se generen nuevas ventas.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
@if($comisiones->hasPages())
<div class="px-5 py-4 border-t border-slate-100">
    {{ $comisiones->links() }}
</div>
@endif
