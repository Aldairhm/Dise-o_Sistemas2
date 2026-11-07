<div class="overflow-x-auto">
    <table class="w-full text-left text-xs min-w-[1000px]">
        <thead class="bg-slate-50/80 text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-200">
            <tr>
                <th class="px-5 py-3.5 w-24 whitespace-nowrap">Folio</th>
                <th class="px-4 py-3.5 whitespace-nowrap">Fecha y Hora</th>
                <th class="px-4 py-3.5 whitespace-nowrap">Tipo y Destino</th>
                <th class="px-4 py-3.5 whitespace-nowrap">Método de Pago</th>
                <th class="px-4 py-3.5 text-center whitespace-nowrap">Estado</th>
                <th class="px-4 py-3.5 text-right whitespace-nowrap w-24">Total</th>
                <th class="px-5 py-3.5 text-right whitespace-nowrap">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($todasVentas as $venta)
                @php
                    $estado = $venta->estado ?? 'Pendiente';
                    $tipo = $venta->tipo_venta ?? 'Tienda';
                    $puedeCancelar = in_array($estado, ['Pendiente', 'Confirmada']);

                    $badgeClasses = match($estado) {
                        'Pendiente' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'Confirmada' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'En ruta' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'Entregada', 'Entregado' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'Cancelada' => 'bg-rose-50 text-rose-700 border-rose-200',
                        'Devolución' => 'bg-purple-50 text-purple-700 border-purple-200',
                        'Cambio' => 'bg-teal-50 text-teal-700 border-teal-200',
                        default => 'bg-slate-50 text-slate-700 border-slate-200'
                    };

                    $badgeIcon = match($estado) {
                        'Pendiente' => 'fa-clock',
                        'Confirmada' => 'fa-circle-check',
                        'En ruta' => 'fa-truck-fast',
                        'Entregada', 'Entregado' => 'fa-box-open',
                        'Cancelada' => 'fa-ban',
                        'Devolución' => 'fa-rotate-left',
                        'Cambio' => 'fa-arrow-right-arrow-left',
                        default => 'fa-info-circle'
                    };
                @endphp

                <tr 
                    x-show="ventaVisible('{{ $estado }}')" 
                    class="hover:bg-slate-50/70 transition-colors"
                >
                    <!-- FOLIO -->
                    <td class="px-5 py-3.5 font-bold text-slate-900">
                        <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[11px] font-mono">
                            #VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>

                    <!-- FECHA Y HORA -->
                    <td class="px-4 py-3.5 text-slate-600">
                        <p class="font-bold text-slate-800">
                            {{ \Carbon\Carbon::parse($venta->fecha)->format('d-m-Y') }}
                        </p>
                        <span class="text-[10px] text-slate-400">
                            {{ \Carbon\Carbon::parse($venta->fecha)->format('h:i A') }}
                        </span>
                    </td>

                    <!-- TIPO Y DESTINO -->
                    <td class="px-4 py-3.5">
                        <div class="space-y-1">
                            @if($tipo === 'Envio')
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <i class="fas fa-truck text-[9px]"></i> Entrega a Domicilio
                                    </span>
                                    @if(!empty($venta->nombre_cliente))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-50 text-blue-800 border border-blue-200" title="Cliente">
                                            <i class="fas fa-user text-[8px] text-blue-600"></i> {{ $venta->nombre_cliente }}
                                        </span>
                                    @endif
                                </div>
                                @if($venta->direccion_entrega)
                                    <p class="text-[11px] text-slate-700 font-medium truncate max-w-[220px]" title="{{ $venta->direccion_entrega }}">
                                        {{ $venta->direccion_entrega }}
                                    </p>
                                @endif
                                @if($venta->telefono_entrega)
                                    <p class="text-[10px] text-slate-400 flex items-center gap-1 font-mono">
                                        <i class="fas fa-phone text-[8px]"></i> {{ $venta->telefono_entrega }}
                                    </p>
                                @endif
                            @else
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($venta->metodo_pago === 'Cambio Físico')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                            <i class="fas fa-arrow-right-arrow-left text-[9px]"></i> Cambio en Tienda
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fas fa-store text-[9px]"></i> Venta en Tienda
                                        </span>
                                    @endif
                                    @if(!empty($venta->nombre_cliente))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200" title="Cliente">
                                            <i class="fas fa-user text-[8px]"></i> {{ $venta->nombre_cliente }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </td>

                    <!-- MÉTODO DE PAGO -->
                    <td class="px-4 py-3.5 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $venta->metodo_pago === 'Efectivo' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($venta->metodo_pago === 'Transferencia Bancaria' ? 'bg-purple-50 text-purple-700 border-purple-200' : ($venta->metodo_pago === 'Cambio Físico' ? 'bg-teal-50 text-teal-700 border-teal-200' : 'bg-blue-50 text-blue-700 border-blue-200')) }}">
                            <i class="fas {{ $venta->metodo_pago === 'Efectivo' ? 'fa-money-bill-wave' : ($venta->metodo_pago === 'Transferencia Bancaria' ? 'fa-building-columns' : ($venta->metodo_pago === 'Cambio Físico' ? 'fa-arrow-right-arrow-left' : 'fa-credit-card')) }} text-[10px]"></i>
                            <span>{{ $venta->metodo_pago }}</span>
                        </span>
                    </td>

                    <!-- ESTADO ACTUAL -->
                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                        <div class="inline-flex flex-col items-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $badgeClasses }}">
                                <i class="fas {{ $badgeIcon }} text-[10px]"></i>
                                <span>{{ $estado }}</span>
                            </span>

                            @if($puedeCancelar)
                                <span class="text-[9px] font-semibold text-emerald-600 mt-1 flex items-center gap-0.5">
                                    <i class="fas fa-clock-rotate-left text-[8px]"></i> Cancelable
                                </span>
                            @endif
                        </div>
                    </td>

                    <!-- TOTAL -->
                    <td class="px-4 py-3.5 text-right font-black text-slate-900 text-sm whitespace-nowrap">
                        ${{ number_format($venta->total, 2) }}
                    </td>

                    <!-- ACCIONES -->
                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-1.5 flex-nowrap">
                            
                            <!-- BOTÓN CANCELAR (SOLO ANTES DE EN RUTA) -->
                            @if($puedeCancelar)
                                <button 
                                    type="button" 
                                    @click="abrirModalCancelar({{ $venta->id }}, 'VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}', '{{ $estado }}')"
                                    class="rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-700 px-2.5 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                    title="Cancelar esta venta (disponible antes de que esté en ruta)"
                                >
                                    <i class="fas fa-ban text-[10px]"></i>
                                    <span>Cancelar</span>
                                </button>
                            @else
                                <span 
                                    class="rounded-lg border border-slate-200 bg-slate-50 text-slate-400 px-2 py-1 text-[11px] font-medium flex items-center gap-1 cursor-not-allowed shrink-0"
                                    title="No se puede modificar ni cancelar porque ya está en ruta o finalizada"
                                >
                                    <i class="fas fa-lock text-[9px]"></i>
                                    <span>Bloqueada</span>
                                </span>
                            @endif

                            <!-- BOTÓN DETALLE -->
                            <button 
                                type="button" 
                                @click="abrirDetalle({{ $venta->id }})" 
                                class="rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 px-2.5 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                title="Ver artículos y desglose"
                            >
                                <i class="fas fa-eye text-slate-400 text-[10px]"></i>
                                <span>Detalle</span>
                            </button>

                            <!-- BOTÓN IMPRIMIR DIRECTO TICKET 80MM -->
                            <button 
                                type="button" 
                                @click="imprimirTicketDirecto({{ $venta->id }})" 
                                class="rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 px-2.5 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                                title="Imprimir tirilla térmica de 80mm"
                            >
                                <i class="fas fa-print text-[10px]"></i>
                                <span>Ticket</span>
                            </button>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-12 bg-white">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-lg mb-3">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <h4 class="text-sm font-bold text-slate-700">Aún no tienes ventas registradas</h4>
                            <p class="text-xs text-slate-400 mt-1">Tus ventas y pedidos realizados se mostrarán en este módulo.</p>
                            <a href="{{ route('home') }}#productos" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-600/20">
                                Crear mi primera venta
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- MENSAJE VACÍO DINÁMICO CUANDO EL FILTRO DE PESTAÑA NO TIENE RESULTADOS -->
<div 
    x-show="conteoFiltradas() === 0 && {{ $todasVentas->count() }} > 0" 
    class="text-center py-12 bg-white"
    style="display: none;"
>
    <div class="flex flex-col items-center justify-center">
        <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg mb-3">
            <i class="fas fa-filter-circle-xmark"></i>
        </div>
        <h4 class="text-sm font-bold text-slate-700">No hay ventas en este apartado</h4>
        <p class="text-xs text-slate-400 mt-1">
            Actualmente no tienes registros con el estado seleccionado.
        </p>
        <button 
            type="button" 
            @click="cambiarTab('todos')"
            class="mt-3 text-xs font-bold text-blue-600 hover:underline cursor-pointer"
        >
            Ver todas mis ventas
        </button>
    </div>
</div>

