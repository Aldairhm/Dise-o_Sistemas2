<x-app title="Detalle de Venta #{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }} | AXStore">
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- HEADER CON NAVEGACIÓN -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('ventas.index') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-2">
                    <i class="fas fa-arrow-left"></i> Volver al historial de ventas
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-600/20">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                                Venta #VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}
                            </h1>
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                                Completada
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">
                            Registrada el {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y h:i A') }} • Atendido por {{ $venta->usuario?->nombre_real ?? $venta->usuario?->username }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE IMPRESIÓN RÁPIDA -->
            <div class="flex flex-wrap items-center gap-2">
                <a 
                    href="{{ route('ventas.imprimir', ['id' => $venta->id, 'tipo' => 'ticket']) }}" 
                    target="_blank"
                    class="rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold px-3.5 py-2 text-xs transition-all shadow-sm flex items-center gap-2"
                >
                    <i class="fas fa-receipt text-slate-400"></i>
                    <span>Ticket Térmico</span>
                </a>
                <a 
                    href="{{ route('ventas.imprimir', ['id' => $venta->id, 'tipo' => 'factura_comercial']) }}" 
                    target="_blank"
                    class="rounded-xl border border-blue-200 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 font-bold px-3.5 py-2 text-xs transition-all shadow-sm flex items-center gap-2"
                >
                    <i class="fas fa-file-invoice"></i>
                    <span>Factura Comercial</span>
                </a>
                <a 
                    href="{{ route('ventas.imprimir', ['id' => $venta->id, 'tipo' => 'credito_fiscal']) }}" 
                    target="_blank"
                    class="rounded-xl bg-purple-700 hover:bg-purple-600 text-white font-bold px-3.5 py-2 text-xs transition-all shadow-md shadow-purple-700/20 flex items-center gap-2"
                >
                    <i class="fas fa-building-columns"></i>
                    <span>Crédito Fiscal (CCF)</span>
                </a>
            </div>
        </div>

        <!-- TARJETA PRINCIPAL: DETALLE DE PRODUCTOS -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                        <i class="fas fa-basket-shopping"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">Productos Facturados</h2>
                        <p class="text-xs text-slate-500">Desglose de artículos y variantes en esta transacción</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-lg">
                    {{ $venta->detalles->sum('cantidad') }} unidades en total
                </span>
            </div>

            <!-- TABLA DE ARTÍCULOS -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3">Producto / Variante</th>
                            <th class="px-3 py-3 text-center w-20">Cant.</th>
                            <th class="px-3 py-3 text-right w-28">P. Unitario</th>
                            <th class="px-3 py-3 text-right w-28">Costo Extra</th>
                            <th class="px-5 py-3 text-right w-32">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($venta->detalles as $det)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="font-bold text-slate-900 text-xs">{{ $det->variante?->producto?->nombre ?? 'Producto no especificado' }}</p>
                                <p class="text-[11px] text-slate-500">{{ $det->variante?->nombre_variante ?? 'Variante estándar' }}</p>
                                <span class="text-[10px] font-mono text-slate-400">SKU: {{ $det->variante?->sku ?? 'Sin SKU' }}</span>
                            </td>
                            <td class="px-3 py-3.5 text-center font-bold text-slate-800">
                                {{ $det->cantidad }}
                            </td>
                            <td class="px-3 py-3.5 text-right font-medium text-slate-700">
                                ${{ number_format($det->precio_unitario, 2) }}
                            </td>
                            <td class="px-3 py-3.5 text-right font-medium">
                                @if($det->costo_extra > 0)
                                <span class="text-amber-700 font-bold bg-amber-50 border border-amber-200/70 px-2 py-0.5 rounded text-[11px]">
                                    +${{ number_format($det->costo_extra, 2) }}
                                </span>
                                @else
                                <span class="text-slate-400">$0.00</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right font-black text-slate-900 text-sm">
                                ${{ number_format($det->subtotal, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- RESUMEN FINANCIERO INFERIOR -->
            <div class="p-6 bg-slate-50/80 border-t border-slate-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500">Método de Pago:</span>
                            <span class="text-xs font-bold text-slate-800 bg-white border border-slate-200 px-2.5 py-1 rounded-lg">
                                <i class="fas {{ $venta->metodo_pago === 'Efectivo' ? 'fa-money-bill-wave text-emerald-600' : 'fa-credit-card text-blue-600' }} mr-1"></i>
                                {{ $venta->metodo_pago }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400">
                            Precios con IVA (13%) incluido conforme al Código Tributario de El Salvador.
                        </p>
                    </div>

                    <div class="space-y-2 text-xs max-w-xs ml-auto w-full">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal productos:</span>
                            <span class="font-bold text-slate-800">${{ number_format($venta->detalles->sum('subtotal_base'), 2) }}</span>
                        </div>
                        @if($venta->total_costo_extra > 0)
                        <div class="flex justify-between text-amber-700 font-bold bg-amber-50 px-2 py-1 rounded">
                            <span>Costos extras:</span>
                            <span>+${{ number_format($venta->total_costo_extra, 2) }}</span>
                        </div>
                        @endif
                        @if($venta->descuento_aplicado > 0)
                        <div class="flex justify-between text-emerald-600 font-bold">
                            <span>Descuentos:</span>
                            <span>-${{ number_format($venta->descuento_aplicado, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-baseline pt-2 border-t border-slate-200 text-slate-900">
                            <span class="text-sm font-black uppercase">Total Facturado:</span>
                            <span class="text-2xl font-black text-emerald-600">${{ number_format($venta->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app>
