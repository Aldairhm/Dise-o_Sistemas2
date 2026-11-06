<div class="overflow-x-auto">
    <table class="w-full text-left text-xs min-w-[1050px]">
        <thead class="bg-slate-50/80 text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-200">
            <tr>
                <th class="px-5 py-3.5 w-24">Folio</th>
                <th class="px-4 py-3.5">Fecha y Hora</th>
                <th class="px-4 py-3.5">Vendedor</th>
                <th class="px-4 py-3.5">Tipo y Destino</th>
                <th class="px-4 py-3.5">Método de Pago</th>
                <th class="px-4 py-3.5 text-center">Estado Actual</th>
                <th class="px-4 py-3.5 text-right w-24">Total</th>
                <th class="px-5 py-3.5 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($ventas as $venta)
            @php
            $estado = $venta->estado ?? 'Pendiente';
            $tipo = $venta->tipo_venta ?? 'Tienda';
            $bloqueado = $venta->estado_bloqueado;
            $permitidos = $venta->estados_permitidos;
            $puedeDevolver = $venta->puede_devolver;
            $diasRestantes = $venta->dias_restantes_devolucion;
            $diasGarantia = $venta->dias_garantia;
            $textoGarantia = $venta->texto_garantia_devolucion;
            $fechaLimite = $venta->fecha_limite_devolucion;
            $limiteFechaFormatted = $fechaLimite ? $fechaLimite->format('d/m/Y') : '';

            $badgeClasses = match($estado) {
            'Pendiente' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Confirmada' => 'bg-blue-50 text-blue-700 border-blue-200',
            'En ruta' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'Entregada' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Cancelada' => 'bg-rose-50 text-rose-700 border-rose-200',
            'Devolución' => 'bg-purple-50 text-purple-700 border-purple-200',
            'Cambio' => 'bg-teal-50 text-teal-700 border-teal-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200'
            };

            $badgeIcon = match($estado) {
            'Pendiente' => 'fa-clock',
            'Confirmada' => 'fa-circle-check',
            'En ruta' => 'fa-truck-fast',
            'Entregada' => 'fa-box-open',
            'Cancelada' => 'fa-ban',
            'Devolución' => 'fa-rotate-left',
            'Cambio' => 'fa-arrow-right-arrow-left',
            default => 'fa-info-circle'
            };

            $garantiaBadgeText = $diasRestantes > 1
            ? "Garantía: {$diasRestantes} días"
            : ($diasRestantes === 1 ? "Garantía: 1 día" : "Garantía: Hoy último día");
            @endphp

            <tr class="hover:bg-slate-50/70 transition-colors">
                <!-- FOLIO -->
                <td class="px-5 py-3.5 font-bold text-slate-900">
                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[11px] font-mono">
                        #VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                </td>

                <!-- FECHA Y HORA -->
                <td class="px-4 py-3.5 text-slate-600">
                    <p class="font-bold text-slate-800">
                        {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}
                    </p>
                    <span class="text-[10px] text-slate-400">
                        {{ \Carbon\Carbon::parse($venta->fecha)->format('h:i A') }}
                    </span>
                </td>

                <!-- VENDEDOR -->
                <td class="px-4 py-3.5">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 font-bold text-[10px] flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($venta->usuario?->nombre_real ?? $venta->usuario?->username ?? 'U', 0, 1)) }}
                        </div>
                        <span class="font-bold text-slate-800 truncate max-w-[150px]" title="{{ $venta->usuario?->nombre_real ?? $venta->usuario?->username }}">
                            {{ $venta->usuario?->nombre_real ?? $venta->usuario?->username ?? 'Usuario no asignado' }}
                        </span>
                    </div>
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
                            @if(!empty($venta->departamento))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200" title="Destino">
                                <i class="fas fa-map-pin text-[8px] text-amber-600"></i> {{ $venta->departamento }}{{ !empty($venta->municipio) ? ', ' . $venta->municipio : '' }}
                            </span>
                            @endif
                        </div>
                        @if($venta->direccion_entrega)
                        <p class="text-[11px] text-slate-700 font-medium truncate max-w-[240px]" title="{{ $venta->direccion_entrega }}">
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
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200" title="Venta generada por cambio físico de producto">
                                <i class="fas fa-arrow-right-arrow-left text-[9px] text-teal-600"></i> Cambio en Tienda
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
                <td class="px-4 py-3.5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $venta->metodo_pago === 'Cambio Físico' ? 'bg-teal-50 text-teal-800 border-teal-200' : ($venta->metodo_pago === 'Efectivo' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($venta->metodo_pago === 'Transferencia Bancaria' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-blue-50 text-blue-700 border-blue-200')) }}">
                        <i class="fas {{ $venta->metodo_pago === 'Cambio Físico' ? 'fa-arrow-right-arrow-left text-teal-600' : ($venta->metodo_pago === 'Efectivo' ? 'fa-money-bill-wave' : ($venta->metodo_pago === 'Transferencia Bancaria' ? 'fa-building-columns' : 'fa-credit-card')) }} text-[10px]"></i>
                        <span>{{ $venta->metodo_pago }}</span>
                    </span>
                </td>

                <!-- ESTADO ACTUAL (SOLO INFORMATIVO) -->
                <td class="px-4 py-3.5 text-center">
                    <div class="inline-flex flex-col items-center">
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border shadow-2xs {{ $badgeClasses }}">
                            <i class="fas {{ $badgeIcon }} text-[10px]"></i>
                            <span>{{ $estado }}</span>
                        </span>

                        @if($estado === 'Entregada' && $puedeDevolver)
                        <span class="text-[9px] font-bold text-amber-600 mt-1 flex items-center gap-0.5" title="Garantía de devolución activa hasta el {{ $limiteFechaFormatted }} a las 23:59 ({{ $diasGarantia }} días en {{ $tipo === 'Envio' ? 'envío' : 'tienda' }})">
                            <i class="fas fa-shield-halved text-[8px]"></i>
                            <span>{{ $garantiaBadgeText }}</span>
                        </span>
                        @elseif($estado === 'Entregada' && !$puedeDevolver)
                        <span class="text-[9px] font-medium text-slate-400 mt-0.5" title="Garantía de devolución expirada (plazo de {{ $diasGarantia }} días)">
                            Garantía expirada
                        </span>
                        @elseif($tipo === 'Envio')
                        <span class="text-[9px] font-medium text-slate-400 mt-0.5">Envío</span>
                        @elseif($tipo === 'Tienda')
                        <span class="text-[9px] font-medium text-slate-400 mt-0.5">Tienda</span>
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
                        <!-- COMPROBANTE DE PAGO (TRANSFERENCIA) -->
                        @if($venta->comprobante_pago)
                        <button
                            type="button"
                            @click="verImagenAmpliada('{{ asset('storage/' . $venta->comprobante_pago) }}', 'VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}_{{ \Carbon\Carbon::parse($venta->fecha)->format('Y-m-d') }}_comprobante_pago', 'Comprobante de Pago por Transferencia')"
                            class="rounded-lg border border-purple-200 bg-purple-50 hover:bg-purple-600 hover:text-white text-purple-700 px-2 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                            title="Ver Comprobante de Pago por Transferencia">
                            <i class="fas fa-file-invoice-dollar text-[10px]"></i>
                            <span>Comprobante</span>
                        </button>
                        @endif

                        <!-- COMPROBANTE DE PAQUETE (EN RUTA) -->
                        @if($venta->comprobante_paquete)
                        <button
                            type="button"
                            @click="verImagenAmpliada('{{ asset('storage/' . $venta->comprobante_paquete) }}', 'VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}_{{ \Carbon\Carbon::parse($venta->fecha)->format('Y-m-d') }}_paquete', 'Comprobante de Paquetería')"
                            class="rounded-lg border border-indigo-200 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 px-2 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                            title="Ver Comprobante de Paquetería">
                            <i class="fas fa-truck-ramp-box text-[10px]"></i>
                            <span>Paquete</span>
                        </button>
                        @endif

                        <!-- COMPROBANTE DE DEVOLUCIÓN -->
                        @if($venta->comprobante_devolucion)
                        <button
                            type="button"
                            @click="verImagenAmpliada('{{ asset('storage/' . $venta->comprobante_devolucion) }}', 'VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}_{{ \Carbon\Carbon::parse($venta->fecha)->format('Y-m-d') }}_devolucion', 'Comprobante de Devolución')"
                            class="rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-700 px-2 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                            title="Ver Comprobante de Devolución">
                            <i class="fas fa-box-archive text-[10px]"></i>
                            <span>Devolución</span>
                        </button>
                        @endif

                        <!-- BOTÓN DETALLE -->
                        <button
                            type="button"
                            @click="abrirDetalle({{ $venta->id }})"
                            class="rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 px-2 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                            title="Ver artículos y desglose">
                            <i class="fas fa-eye text-slate-400 text-[10px]"></i>
                            <span>Detalle</span>
                        </button>

                        <!-- BOTÓN IMPRIMIR (SELECTOR DE FACTURA / TICKET) -->
                        <button
                            type="button"
                            @click="abrirModalImpresion({{ $venta->id }}, '{{ $venta->metodo_pago }}', {{ (float) $venta->total }})"
                            class="rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 px-2 py-1 text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer shrink-0"
                            title="Imprimir ticket o factura comercial/tributaria">
                            <i class="fas fa-print text-[10px]"></i>
                            <span>Imprimir</span>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="8" class="py-16 text-center text-slate-400">
                        <div class="w-14 h-14 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-3 border border-slate-100">
                            <i class="fas {{ $estadoTab === 'Devolución' ? 'fa-rotate-left' : ($estadoTab === 'Cambio' ? 'fa-arrow-right-arrow-left' : ($estadoTab === 'Entregada' ? 'fa-box-open' : 'fa-folder-open')) }} text-2xl"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-600">
                            @if($estadoTab === 'Entregada')
                                No se encontraron ventas entregadas en este periodo
                            @elseif($estadoTab === 'Devolución')
                                No se encontraron devoluciones de clientes en este periodo
                            @elseif($estadoTab === 'Cambio')
                                No se encontraron cambios de producto en este periodo
                            @else
                                No se encontraron transacciones en este periodo
                            @endif
                        </p>
                        <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                            @if($estadoTab === 'Devolución')
                                No existen ventas con estado <i>Devolución</i> para los filtros y fechas seleccionadas.
                            @elseif($estadoTab === 'Cambio')
                                No existen ventas con estado <i>Cambio</i> para los filtros y fechas seleccionadas.
                            @elseif($estadoTab === 'Entregada')
                                No existen ventas con estado <i>Entregada</i> para los filtros y fechas seleccionadas.
                            @else
                                Este dashboard muestra exclusivamente ventas entregadas, devoluciones y cambios liquidados. Si tus pedidos están en proceso, consúltalos en el apartado logístico.
                            @endif
                        </p>
                        <div class="mt-4 flex items-center justify-center gap-2 flex-wrap">
                            @if($estadoTab !== 'todos')
                                <a href="{{ route('ventas.index', array_merge(request()->except('page'), ['tab' => 'todos'])) }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 text-xs font-bold transition-all shadow-sm">
                                    <i class="fas fa-layer-group"></i> Ver Todas las Transacciones
                                </a>
                            @endif
                            <a href="{{ route('ventas.pedidos') }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 text-xs font-bold transition-all shadow-sm">
                                <i class="fas fa-boxes-packing"></i> Ver Envíos y Pedidos en Proceso
                            </a>
                            <a href="{{ route('ventas.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 text-xs font-bold transition-all shadow-sm">
                                <i class="fas fa-plus"></i> Registrar Nueva Venta
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- PAGINACIÓN -->
@if($ventas->hasPages())
<div class="p-4 border-t border-slate-100 bg-slate-50/50">
    {{ $ventas->links() }}
</div>
@endif

