<div id="contenedorListaVendedores" class="space-y-4">
    @forelse($reporte as $item)
        @php
            $vendedor = $item['vendedor'];
            $comisiones = $item['comisiones'];
            $hasPendiente = $item['total_pendiente'] > 0;
        @endphp
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-200 hover:border-slate-300"
            id="card-vendedor-{{ $vendedor->id }}">

            <!-- HEADER DE LA TARJETA DEL VENDEDOR -->
            <div
                class="p-5 sm:p-6 bg-white flex flex-col lg:flex-row lg:items-center justify-between gap-5 border-b border-slate-100">

                <!-- INFORMACIÓN DEL VENDEDOR -->
                <div class="flex items-center gap-4">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-lg flex items-center justify-center shadow-md shadow-blue-500/20 flex-shrink-0">
                        {{ strtoupper(substr($vendedor->nombre_real ?: $vendedor->username, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black text-slate-900">
                                {{ $vendedor->nombre_real ?: $vendedor->username }}</h3>
                            @if($hasPendiente)
                                <span
                                    class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black uppercase tracking-wider">
                                    Pendiente
                                </span>
                            @else
                                <span
                                    class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-wider">
                                    Al día
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">
                            {{ $vendedor->username }}
                            @if($vendedor->telefono)
                                &bull; {{ $vendedor->telefono }}
                            @endif
                            &bull; <span class="text-slate-500">{{ $item['total_ventas'] }} comisiones en el
                                período</span>
                        </p>
                    </div>
                </div>

                <!-- RESUMEN DE CIFRAS -->
                <div
                    class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-100 flex-1 max-w-xl">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total
                            Generado</span>
                        <span
                            class="text-base font-black text-slate-900">${{ number_format($item['total_comisiones'], 2) }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block">Por
                            Liquidar</span>
                        <span
                            class="text-base font-black text-amber-700">${{ number_format($item['total_pendiente'], 2) }}</span>
                        @if(($item['saldo_en_garantia'] ?? 0) > 0)
                            <span class="block text-[10px] font-semibold text-amber-600 truncate"
                                title="${{ number_format($item['saldo_en_garantia'], 2) }} en período de garantía de devolución">
                                <i class="fas fa-shield-halved text-[9px]"></i>
                                ${{ number_format($item['saldo_en_garantia'], 2) }} en garantía
                            </span>
                        @endif
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Total
                            Pagado</span>
                        <span
                            class="text-base font-black text-emerald-700">${{ number_format($item['total_pagada'], 2) }}</span>
                    </div>
                    <div>
                        <span
                            class="text-[10px] font-bold uppercase tracking-wider text-rose-500 block">Canceladas</span>
                        <span
                            class="text-base font-black text-slate-600">${{ number_format($item['total_cancelada'], 2) }}</span>
                    </div>
                </div>

                <!-- BOTONES DE ACCIÓN (SOLO ADMIN) -->
                @if($isAdmin)
                    <div class="flex items-center gap-2 self-end lg:self-center">
                        @php
                            $montoLiquidable = $item['saldo_liquidable'] ?? 0;
                            $montoGarantia = $item['saldo_en_garantia'] ?? 0;
                            $montoHistorico = $item['saldo_liquidable_total'] ?? 0;
                            $nombreVendedorSeguro = addslashes($vendedor->nombre_real ?: $vendedor->username);
                            $usernameVendedorSeguro = addslashes($vendedor->username);
                        @endphp

                        @if($montoLiquidable > 0)
                            <button type="button"
                                onclick="openLiquidarModal({{ $vendedor->id }}, '{{ $nombreVendedorSeguro }}', '{{ $usernameVendedorSeguro }}', '{{ $desde }}', '{{ $hasta }}')"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                                title="Liquidar comisiones de este período">
                                <i class="fas fa-circle-check"></i>
                                <span>Liquidar Período (${{ number_format($montoLiquidable, 2) }})</span>
                            </button>
                        @elseif($montoGarantia > 0)
                            <button type="button"
                                onclick="openLiquidarModal({{ $vendedor->id }}, '{{ $nombreVendedorSeguro }}', '{{ $usernameVendedorSeguro }}', '{{ $desde }}', '{{ $hasta }}')"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-black text-xs shadow-sm transition-all cursor-pointer"
                                title="Comisiones en período de garantía de devolución (aún no liquidables)">
                                <i class="fas fa-shield-halved text-amber-600"></i>
                                <span>En garantía (${{ number_format($montoGarantia, 2) }})</span>
                            </button>
                        @endif

                        @if($montoLiquidable <= 0 && $montoHistorico > 0)
                            <button type="button"
                                onclick="openLiquidarModal({{ $vendedor->id }}, '{{ $nombreVendedorSeguro }}', '{{ $usernameVendedorSeguro }}', '', '')"
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors cursor-pointer"
                                title="Liquidar todas las comisiones pendientes históricas disponibles de este vendedor">
                                <i class="fas fa-clock-rotate-left text-slate-500"></i>
                                <span>Histórico (${{ number_format($montoHistorico, 2) }})</span>
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            <!-- TABLA DE DETALLE / HISTORIAL -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                            <th class="px-5 py-3">Fecha y Hora</th>
                            <th class="px-4 py-3">Concepto / Venta</th>
                            <th class="px-4 py-3 text-right">Comisión</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3">Método de Pago</th>
                            <th class="px-4 py-3">Notas</th>
                            <th class="px-5 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($comisiones as $c)
                            <tr class="hover:bg-slate-50/60 transition-colors {{ $c->monto < 0 ? 'bg-red-50/40' : '' }}">
                                {{-- Fecha y Hora --}}
                                <td class="px-5 py-3 font-semibold text-slate-700 whitespace-nowrap">
                                    {{ $c->fecha_registro ? $c->fecha_registro->format('d/m/Y H:i') : '—' }}
                                </td>

                                {{-- Concepto / Venta --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @if($c->id_salida)
                                            <div
                                                class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                                                <i class="fas fa-receipt"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800">
                                                    {{ $c->salida->variante->producto->nombre ?? 'Venta #' . $c->id_salida }}
                                                </p>
                                                <p class="text-[10px] text-slate-400">Salida #{{ $c->id_salida }}
                                                    &bull; {{ $c->salida->cantidad ?? 1 }} uds</p>
                                            </div>
                                        @else
                                            <div
                                                class="w-6 h-6 rounded-lg {{ $c->monto < 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                                                <i class="fas {{ $c->monto < 0 ? 'fa-arrow-down' : 'fa-gift' }}"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800">
                                                    {{ $c->concepto ?? 'Ajuste / Bono' }}</p>
                                                <p class="text-[10px] text-slate-400">Ajuste manual de saldo</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Monto --}}
                                <td
                                    class="px-4 py-3 text-right font-black whitespace-nowrap {{ $c->monto < 0 ? 'text-red-600' : 'text-slate-900' }}">
                                    {{ $c->monto < 0 ? '-$' . number_format(abs($c->monto), 2) : '$' . number_format($c->monto, 2) }}
                                </td>

                                {{-- Estado --}}
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @php
                                        $badgeColor = match ($c->estado) {
                                            'Pagada' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Cancelada' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                                        };
                                        $badgeIcon = match ($c->estado) {
                                            'Pagada' => 'fa-circle-check',
                                            'Cancelada' => 'fa-ban',
                                            default => 'fa-clock',
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $badgeColor }}">
                                        <i class="fas {{ $badgeIcon }} text-[9px]"></i>
                                        <span>{{ $c->estado }}</span>
                                    </span>
                                    @if($c->estado === 'Pendiente' && $c->es_liquidacion_bloqueada)
                                        <span
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200 mt-1 block"
                                            title="{{ $c->motivo_bloqueo_liquidacion }}">
                                            <i class="fas fa-shield-halved text-[8px]"></i> En garantía
                                        </span>
                                    @endif
                                </td>

                                {{-- Método de Pago --}}
                                <td class="px-4 py-3 text-slate-600 text-[11px]">
                                    @if($c->estado === 'Pagada')
                                        <div class="space-y-1.5">
                                            <span class="font-bold text-emerald-700 flex items-center gap-1.5 text-xs">
                                                @if($c->metodo_pago === 'Transferencia Bancaria')
                                                    <i class="fas fa-university text-blue-600"></i>
                                                @elseif($c->metodo_pago === 'Efectivo')
                                                    <i class="fas fa-money-bill-wave text-emerald-600"></i>
                                                @else
                                                    <i class="fas fa-money-bill-transfer text-emerald-600"></i>
                                                @endif
                                                {{ $c->metodo_pago ?? 'Efectivo' }}
                                            </span>
                                            @if($c->referencia_pago)
                                                <span class="text-[10px] text-slate-500 font-mono block">Ref:
                                                    {{ $c->referencia_pago }}</span>
                                            @endif
                                            @if($c->comprobante_url)
                                                <a href="{{ $c->comprobante_url }}" target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold border border-indigo-200 text-[11px] shadow-2xs transition-all hover:scale-[1.02] cursor-pointer w-fit">
                                                    <i class="fas fa-file-invoice-dollar text-indigo-600"></i>
                                                    <span>Ver Comprobante</span>
                                                    <i class="fas fa-arrow-up-right-from-square text-[9px] opacity-70"></i>
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Por liquidar</span>
                                    @endif
                                </td>

                                {{-- Notas --}}
                                <td class="px-4 py-3 text-slate-500 text-[11px] max-w-[150px] truncate"
                                    title="{{ $c->notas }}">
                                    {{ $c->notas ?? '—' }}
                                </td>

                                {{-- Acciones --}}
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        {{-- Ver Detalle Completo --}}
                                        <button type="button"
                                            onclick="verDetalleComision({{ $c->id }})"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 transition-colors cursor-pointer"
                                            title="Ver Detalle Completo">
                                            <i class="fas fa-eye text-[11px]"></i>
                                        </button>

                                        @if($isAdmin && $c->estado === 'Pendiente')
                                            {{-- Editar Monto / Concepto --}}
                                            <button type="button"
                                                onclick="openEditModal({{ $c->id }}, {{ $c->monto }}, '{{ addslashes($c->concepto ?? '') }}', '{{ addslashes($c->notas ?? '') }}')"
                                                class="w-7 h-7 rounded-lg flex items-center justify-center text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition-colors cursor-pointer"
                                                title="Editar Monto / Concepto">
                                                <i class="fas fa-pen-to-square text-[11px]"></i>
                                            </button>

                                            {{-- Cancelar Comisión --}}
                                            <button type="button" onclick="cancelarComision({{ $c->id }})"
                                                class="w-7 h-7 rounded-lg flex items-center justify-center text-red-600 hover:text-red-800 hover:bg-red-50 transition-colors cursor-pointer"
                                                title="Cancelar Comisión">
                                                <i class="fas fa-ban text-[11px]"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-6 text-center text-slate-400">
                                    Sin registros de comisiones para este vendedor en el período.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    @empty
        <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center text-slate-400 shadow-sm">
            <i class="fas fa-inbox text-4xl mb-3 text-slate-300"></i>
            <h3 class="text-base font-bold text-slate-700">Sin datos para mostrar</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">No se encontraron comisiones para los filtros
                seleccionados en este período.</p>
        </div>
    @endforelse
</div>

