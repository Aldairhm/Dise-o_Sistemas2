<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                <th class="py-3.5 px-4">Fecha / Hora</th>
                <th class="py-3.5 px-4">Usuario</th>
                <th class="py-3.5 px-4">Módulo</th>
                <th class="py-3.5 px-4">Acción</th>
                <th class="py-3.5 px-4">Descripción del Movimiento</th>
                <th class="py-3.5 px-4">IP / Origen</th>
                <th class="py-3.5 px-4 text-center">Detalle</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
            @forelse($registros as $item)
            <tr class="hover:bg-slate-50/60 transition-colors">
                <!-- Fecha -->
                <td class="py-3 px-4 whitespace-nowrap">
                    <div class="font-bold text-slate-800">{{ $item->created_at->format('d/m/Y') }}</div>
                    <div class="text-[10px] text-slate-400 font-medium">{{ $item->created_at->format('h:i:s A') }} ({{ $item->created_at->diffForHumans() }})</div>
                </td>

                <!-- Usuario -->
                <td class="py-3 px-4 whitespace-nowrap">
                    @if($item->usuario)
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-teal-500 to-blue-500 text-white font-bold text-[11px] flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($item->usuario->nombre_real, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 leading-tight">{{ $item->usuario->nombre_real }}</div>
                                <div class="text-[10px] text-slate-400 font-medium">@<span>{{ $item->usuario->username }}</span></div>
                            </div>
                        </div>
                    @elseif($item->username)
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 font-bold text-[11px] flex items-center justify-center shrink-0">
                                <i class="fas fa-user-secret text-xs"></i>
                            </div>
                            <div>
                                <div class="font-bold text-slate-700">{{ $item->username }}</div>
                                <div class="text-[10px] text-slate-400 font-medium">Externo / Intento</div>
                            </div>
                        </div>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-slate-400 italic font-medium">
                            <i class="fas fa-robot text-xs"></i>
                            <span>Sistema</span>
                        </span>
                    @endif
                </td>

                <!-- Módulo -->
                <td class="py-3 px-4 whitespace-nowrap">
                    @php
                        $modIcon = match($item->modulo) {
                            'Ventas'        => 'fa-cash-register text-blue-500',
                            'Compras'       => 'fa-cart-plus text-emerald-500',
                            'Productos'     => 'fa-shopping-bag text-indigo-500',
                            'Categorías'    => 'fa-folder text-purple-500',
                            'Usuarios'      => 'fa-users text-amber-500',
                            'Autenticación' => 'fa-key text-slate-600',
                            'Bodega'        => 'fa-warehouse text-cyan-500',
                            'Proveedores'   => 'fa-truck text-orange-500',
                            'Devoluciones'  => 'fa-rotate-left text-rose-500',
                            'Comisiones'    => 'fa-hand-holding-dollar text-emerald-600',
                            default         => 'fa-circle-dot text-slate-400',
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-[11px] font-semibold text-slate-700">
                        <i class="fas {{ $modIcon }}"></i>
                        <span>{{ $item->modulo }}</span>
                    </span>
                </td>

                <!-- Acción -->
                <td class="py-3 px-4 whitespace-nowrap">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider badge-{{ $item->accion }}">
                        <i class="fas {{ $item->icon }} text-[9px]"></i>
                        <span>{{ $item->accion }}</span>
                    </span>
                </td>

                <!-- Descripción -->
                <td class="py-3 px-4">
                    <p class="text-xs text-slate-800 font-medium line-clamp-2" title="{{ $item->descripcion }}">
                        {{ $item->descripcion }}
                    </p>
                </td>

                <!-- IP -->
                <td class="py-3 px-4 whitespace-nowrap">
                    <div class="font-mono text-[11px] text-slate-600 font-medium flex items-center gap-1.5">
                        <i class="fas fa-network-wired text-slate-400 text-[10px]"></i>
                        <span>{{ $item->ip_address ?: '127.0.0.1' }}</span>
                    </div>
                </td>

                <!-- Detalle -->
                <td class="py-3 px-4 text-center whitespace-nowrap">
                    @if($item->datos_anteriores || $item->datos_nuevos)
                        <button type="button" 
                                onclick='verDetalle(@json($item))'
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-bold transition-all cursor-pointer">
                            <i class="fas fa-code-compare"></i>
                            <span>Diff</span>
                        </button>
                    @else
                        <button type="button"
                                onclick='verDetalleSimple(@json($item))'
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-all cursor-pointer">
                            <i class="fas fa-eye"></i>
                            <span>Ver</span>
                        </button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-8">
                    <x-empty-state 
                        title="No se encontraron movimientos registrados" 
                        description="Prueba cambiando o eliminando los filtros aplicados arriba."
                        icon="search" 
                    />
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Paginación -->
@if($registros->hasPages())
<div class="px-6 py-4 bg-slate-50/70 border-t border-slate-200/80">
    {{ $registros->links() }}
</div>
@endif

