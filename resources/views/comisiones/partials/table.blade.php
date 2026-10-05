<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                @if($isAdmin)
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Vendedor</th>
                @endif
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Concepto / Venta</th>
                <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Comisión</th>
                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Método de Pago</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Notas</th>
                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
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
                        <div>
                            <span class="font-bold text-slate-800 block text-xs">{{ $c->vendedor->nombre_real ?? '—' }}</span>
                            <span class="text-[10px] text-slate-400 capitalize">{{ $c->vendedor->rol ?? 'vendedor' }}</span>
                        </div>
                    </div>
                </td>
                @endif

                <td class="px-5 py-4 text-slate-600 whitespace-nowrap">
                    <span class="font-medium text-xs text-slate-800 block">{{ $c->fecha_registro ? $c->fecha_registro->format('d/m/Y') : '—' }}</span>
                    <span class="text-[10px] text-slate-400">{{ $c->fecha_registro ? $c->fecha_registro->format('h:i A') : '' }}</span>
                </td>

                <td class="px-5 py-4 text-slate-700">
                    <div class="flex flex-col gap-0.5">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            @if($c->id_salida)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md text-[11px] font-bold">
                                    <i class="fas fa-shopping-bag text-[10px]"></i> Salida #{{ $c->id_salida }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md text-[11px] font-bold">
                                    <i class="fas fa-award text-[10px]"></i> Bono
                                </span>
                            @endif

                            <span class="font-semibold text-xs text-slate-800">
                                {{ $c->concepto ?? ($c->salida ? "Venta directa de {$c->salida->cantidad} unidad(es)" : "Comisión directa") }}
                            </span>
                        </div>

                        @if($c->salida && $c->salida->variante && $c->salida->variante->producto)
                            <span class="text-[11px] text-slate-500 pl-1">
                                {{ $c->salida->variante->producto->nombre }} - {{ $c->salida->variante->nombre_variante }} (Total salida: ${{ number_format($c->salida->total, 2) }})
                            </span>
                        @endif
                    </div>
                </td>

                <td class="px-5 py-4 text-right">
                    <span class="font-extrabold text-sm text-slate-900 block">${{ number_format($c->monto, 2) }}</span>
                    @if($c->porcentaje && $c->porcentaje > 0)
                        <span class="text-[10px] text-slate-400 font-medium">Ref: ${{ number_format($c->porcentaje, 2) }}/ud</span>
                    @endif
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

                <td class="px-5 py-4 text-slate-700 text-xs">
                    @if($c->estado === 'Pagada')
                        <div class="flex flex-col gap-1">
                            <span class="font-bold text-emerald-800 flex items-center gap-1.5">
                                @if($c->metodo_pago === 'Transferencia Bancaria')
                                    <i class="fas fa-university text-blue-600"></i>
                                @elseif($c->metodo_pago === 'Efectivo')
                                    <i class="fas fa-money-bill-wave text-emerald-600"></i>
                                @elseif($c->metodo_pago === 'Cheque')
                                    <i class="fas fa-money-check text-indigo-600"></i>
                                @else
                                    <i class="fas fa-wallet text-purple-600"></i>
                                @endif
                                {{ $c->metodo_pago ?? 'Efectivo' }}
                            </span>

                            @if($c->referencia_pago)
                                <span class="text-[10px] text-slate-500 font-mono">Ref: {{ $c->referencia_pago }}</span>
                            @endif

                            @if($c->comprobante_url)
                                <a href="{{ $c->comprobante_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold border border-indigo-200 text-[11px] shadow-2xs transition-all w-fit hover:scale-[1.02]">
                                    <i class="fas fa-file-invoice-dollar text-indigo-600"></i>
                                    <span>Ver Comprobante</span>
                                    <i class="fas fa-arrow-up-right-from-square text-[9px] opacity-70"></i>
                                </a>
                            @endif

                            @if($c->fecha_liquidacion)
                                <span class="text-[10px] text-slate-400">Pagado: {{ $c->fecha_liquidacion->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    @else
                        <span class="text-slate-400 text-xs italic">Por liquidar</span>
                    @endif
                </td>

                <td class="px-5 py-4 text-slate-500 text-xs max-w-[160px] truncate" title="{{ $c->notas }}">
                    {{ $c->notas ?? '—' }}
                </td>

                <td class="px-5 py-4">
                    <div class="flex items-center justify-center gap-1.5">
                        {{-- Ver Detalle --}}
                        <button type="button"
                                onclick="verDetalleComision({{ $c->id }})"
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 transition-colors cursor-pointer"
                                title="Ver detalle completo de la comisión">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                        @if($isAdmin && $c->estado === 'Pendiente')
                            {{-- Editar --}}
                            <button type="button"
                                    onclick="openEditModal({{ $c->id }}, '{{ $c->monto }}', '{{ addslashes($c->concepto ?? '') }}', '{{ addslashes($c->notas ?? '') }}')"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer"
                                    title="Editar monto/concepto">
                                <i class="fas fa-pen text-xs"></i>
                            </button>
                            {{-- Cancelar --}}
                            <button type="button"
                                    onclick="cancelarComision({{ $c->id }})"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors cursor-pointer"
                                    title="Cancelar comisión">
                                <i class="fas fa-ban text-xs"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $isAdmin ? 8 : 7 }}" class="px-5 py-14 text-center">
                    <div class="flex flex-col items-center gap-3 text-slate-400">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl">
                            <i class="fas fa-coins"></i>
                        </div>
                        <p class="font-semibold text-slate-500">No hay comisiones registradas</p>
                        <p class="text-sm">Ajusta los filtros o realiza ventas para generar comisiones.</p>
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
