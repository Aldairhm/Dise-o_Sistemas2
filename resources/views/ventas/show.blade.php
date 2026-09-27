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
                                <i class="fas {{ $venta->metodo_pago === 'Efectivo' ? 'fa-money-bill-wave text-emerald-600' : 'fa-building-columns text-purple-600' }} mr-1"></i>
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
                        @if($venta->precio_envio > 0)
                        <div class="flex justify-between text-blue-700 font-bold bg-blue-50 px-2 py-1 rounded">
                            <span>Costo de envío:</span>
                            <span>+${{ number_format($venta->precio_envio, 2) }}</span>
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

        @if(!empty($venta->direccion_entrega) && $venta->direccion_entrega !== 'Venta en mostrador / POS')
        <!-- TARJETA DE ENTREGA A DOMICILIO -->
        <div class="bg-white border border-blue-200 rounded-2xl p-5 shadow-sm flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-base shadow-sm shrink-0">
                <i class="fas fa-truck-fast"></i>
            </div>
            <div class="space-y-1 text-xs flex-1">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-tight">Datos de Entrega a Domicilio</h2>
                    <span class="text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-0.5 rounded-full">
                        Despachado
                    </span>
                </div>
                <p class="text-slate-800 font-bold text-sm">
                    {{ $venta->direccion_entrega }}
                </p>
                @if($venta->fecha_salida)
                <p class="text-slate-500 text-[11px]">
                    Salida registrada: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($venta->fecha_salida)->format('d/m/Y') }}</strong>
                    @if($venta->hora_salida)
                    a las <strong class="text-slate-700">{{ $venta->hora_salida }}</strong>
                    @endif
                </p>
                @endif
                @if($venta->telefono)
                <p class="text-slate-600 text-xs flex items-center gap-1.5 pt-1">
                    <i class="fas fa-phone text-blue-600 text-[11px]"></i>
                    <span>Teléfono de contacto:</span>
                    <strong class="text-slate-900 font-mono">{{ $venta->telefono }}</strong>
                </p>
                @endif
            </div>
        </div>
        @endif

        @php
            $folioDescarga = 'VNT-' . str_pad($venta->id, 5, '0', STR_PAD_LEFT);
            $fechaDescarga = \Carbon\Carbon::parse($venta->fecha)->format('Y-m-d');
        @endphp

        <!-- 1. COMPROBANTE DE TRANSFERENCIA BANCARIA -->
        @if($venta->comprobante_url)
        <div class="bg-white border border-blue-200 rounded-2xl shadow-sm overflow-hidden" x-data="{ zoomComprobante: false }">
            <div class="p-5 border-b border-blue-100 bg-gradient-to-r from-blue-50/70 to-indigo-50/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-base shadow-sm shadow-blue-500/20">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-tight">Comprobante de Transferencia Bancaria</h2>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                                Verificado
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">Documento de respaldo adjuntado por el vendedor</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a 
                        href="{{ $venta->comprobante_url }}" 
                        target="_blank" 
                        class="rounded-xl border border-blue-200 bg-white hover:bg-blue-600 hover:text-white text-blue-700 font-bold px-3.5 py-2 text-xs transition-all shadow-2xs flex items-center gap-1.5"
                    >
                        <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                        <span>Abrir pestaña</span>
                    </a>
                    <button 
                        type="button" 
                        onclick="descargarArchivoVenta('{{ $venta->comprobante_url }}', '{{ $folioDescarga }}_{{ $fechaDescarga }}_comprobante_pago')"
                        class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold px-3.5 py-2 text-xs transition-all shadow-sm shadow-blue-500/20 flex items-center gap-1.5 cursor-pointer"
                        title="Descargar con folio y fecha"
                    >
                        <i class="fas fa-download text-[11px]"></i>
                        <span>Descargar</span>
                    </button>
                </div>
            </div>

            <div class="p-6 flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <!-- MINIATURA INTERACTIVA -->
                <button 
                    type="button" 
                    @click="zoomComprobante = true"
                    class="block group relative overflow-hidden rounded-2xl border border-slate-200 shadow-md shrink-0 cursor-pointer focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    title="Clic para ampliar imagen"
                >
                    <img 
                        src="{{ $venta->comprobante_url }}" 
                        alt="Comprobante de transferencia" 
                        class="w-36 h-36 sm:w-44 sm:h-44 object-cover group-hover:scale-105 transition-transform duration-300"
                    >
                    <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5">
                        <i class="fas fa-magnifying-glass-plus text-sm"></i>
                        <span>Ampliar</span>
                    </div>
                </button>

                <!-- DETALLES DEL COMPROBANTE -->
                <div class="space-y-2.5 text-xs text-slate-600 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            <i class="fas fa-shield-check"></i> Archivo Registrado en Sistema
                        </span>
                        <span class="text-slate-400">•</span>
                        <span class="text-slate-500 font-medium">Método: {{ $venta->metodo_pago }}</span>
                    </div>
                    <p class="text-slate-700 font-medium leading-relaxed">
                        Este comprobante fue cargado durante el proceso de facturación en caja para certificar la transferencia bancaria correspondiente al total de <strong class="text-emerald-700 font-bold">${{ number_format($venta->total, 2) }}</strong>.
                    </p>
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl space-y-1">
                        <p class="text-[11px] text-slate-500">
                            <strong>Identificador de archivo:</strong>
                            <span class="font-mono text-slate-600">{{ $venta->comprobante_pago }}</span>
                        </p>
                        <p class="text-[11px] text-slate-500">
                            <strong>Fecha de registro:</strong> {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y h:i A') }}
                        </p>
                    </div>
                    <div>
                        <button 
                            type="button" 
                            @click="zoomComprobante = true"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors cursor-pointer"
                        >
                            <i class="fas fa-expand text-[10px]"></i> Ver comprobante en pantalla completa
                        </button>
                    </div>
                </div>
            </div>

            <!-- MODAL LIGHTBOX EN SHOW -->
            <div 
                x-show="zoomComprobante" 
                x-cloak
                class="fixed inset-0 z-50 overflow-y-auto"
                style="display: none;"
                role="dialog"
                aria-modal="true"
            >
                <div 
                    x-show="zoomComprobante"
                    x-transition.opacity
                    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
                    @click="zoomComprobante = false"
                ></div>

                <div class="flex min-h-screen items-center justify-center p-4 text-center">
                    <div 
                        x-show="zoomComprobante"
                        x-transition
                        class="relative z-10 max-w-4xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200"
                        @click.away="zoomComprobante = false"
                    >
                        <div class="bg-blue-600 px-6 py-4 flex items-center justify-between text-white shadow-md">
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-file-invoice-dollar text-white text-base"></i>
                                <span class="text-sm font-black uppercase tracking-wider text-white">Comprobante de Transferencia Bancaria</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button 
                                    type="button" 
                                    onclick="descargarArchivoVenta('{{ $venta->comprobante_url }}', '{{ $folioDescarga }}_{{ $fechaDescarga }}_comprobante_pago')"
                                    class="text-xs text-blue-100 hover:text-white flex items-center gap-1 font-bold transition-colors cursor-pointer"
                                    title="Descargar con folio y fecha"
                                >
                                    <i class="fas fa-download"></i> Descargar
                                </button>
                                <button type="button" @click="zoomComprobante = false" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-4 bg-slate-900/5 flex items-center justify-center max-h-[75vh] overflow-auto">
                            <img src="{{ $venta->comprobante_url }}" alt="Comprobante en alta resolución" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-sm border border-slate-200">
                        </div>
                        <div class="px-6 py-3.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-medium">Venta #{{ $folioDescarga }}</span>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    onclick="descargarArchivoVenta('{{ $venta->comprobante_url }}', '{{ $folioDescarga }}_{{ $fechaDescarga }}_comprobante_pago')" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer flex items-center gap-1.5"
                                >
                                    <i class="fas fa-download text-[11px]"></i>
                                    <span>Descargar</span>
                                </button>
                                <button type="button" @click="zoomComprobante = false" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer">
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- 2. COMPROBANTE DE PAQUETE (EN RUTA) -->
        @if($venta->comprobante_paquete_url)
        <div class="bg-white border border-indigo-200 rounded-2xl shadow-sm overflow-hidden" x-data="{ zoomPaquete: false }">
            <div class="p-5 border-b border-indigo-100 bg-gradient-to-r from-indigo-50/70 to-blue-50/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-sm shadow-indigo-500/20">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-tight">Comprobante de Paquetería (En Ruta)</h2>
                            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-100 border border-indigo-200 px-2.5 py-0.5 rounded-full">
                                En Ruta
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">Evidencia de entrega y rotulado a mensajería / delivery</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a 
                        href="{{ $venta->comprobante_paquete_url }}" 
                        target="_blank" 
                        class="rounded-xl border border-indigo-200 bg-white hover:bg-indigo-600 hover:text-white text-indigo-700 font-bold px-3.5 py-2 text-xs transition-all shadow-2xs flex items-center gap-1.5"
                    >
                        <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                        <span>Abrir pestaña</span>
                    </a>
                    <button 
                        type="button" 
                        onclick="descargarArchivoVenta('{{ $venta->comprobante_paquete_url }}', '{{ $folioDescarga }}_{{ $fechaDescarga }}_paquete')"
                        class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-3.5 py-2 text-xs transition-all shadow-sm shadow-indigo-500/20 flex items-center gap-1.5 cursor-pointer"
                        title="Descargar con folio y fecha"
                    >
                        <i class="fas fa-download text-[11px]"></i>
                        <span>Descargar</span>
                    </button>
                </div>
            </div>

            <div class="p-6 flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <!-- MINIATURA INTERACTIVA -->
                <button 
                    type="button" 
                    @click="zoomPaquete = true"
                    class="block group relative overflow-hidden rounded-2xl border border-slate-200 shadow-md shrink-0 cursor-pointer focus:outline-none focus:ring-4 focus:ring-indigo-500/20"
                    title="Clic para ampliar imagen"
                >
                    <img 
                        src="{{ $venta->comprobante_paquete_url }}" 
                        alt="Comprobante de paquete" 
                        class="w-36 h-36 sm:w-44 sm:h-44 object-cover group-hover:scale-105 transition-transform duration-300"
                    >
                    <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5">
                        <i class="fas fa-magnifying-glass-plus text-sm"></i>
                        <span>Ampliar</span>
                    </div>
                </button>

                <div class="space-y-2.5 text-xs text-slate-600 flex-1">
                    <p class="font-bold text-slate-800">Fotografía tomada al entregar el paquete a la mensajería</p>
                    <p class="text-slate-600 leading-relaxed">
                        Permite auditar el estado del paquete al momento de despacho para envíos a domicilio.
                    </p>
                    <div>
                        <button 
                            type="button" 
                            @click="zoomPaquete = true"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors cursor-pointer"
                        >
                            <i class="fas fa-expand text-[10px]"></i> Ver paquete en pantalla completa
                        </button>
                    </div>
                </div>
            </div>

            <!-- MODAL LIGHTBOX PAQUETE -->
            <div 
                x-show="zoomPaquete" 
                x-cloak
                class="fixed inset-0 z-50 overflow-y-auto"
                style="display: none;"
                role="dialog"
                aria-modal="true"
            >
                <div 
                    x-show="zoomPaquete"
                    x-transition.opacity
                    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
                    @click="zoomPaquete = false"
                ></div>

                <div class="flex min-h-screen items-center justify-center p-4 text-center">
                    <div 
                        x-show="zoomPaquete"
                        x-transition
                        class="relative z-10 max-w-4xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200"
                        @click.away="zoomPaquete = false"
                    >
                        <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between text-white shadow-md">
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-box-open text-white text-base"></i>
                                <span class="text-sm font-black uppercase tracking-wider text-white">Comprobante de Paquetería</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button 
                                    type="button" 
                                    onclick="descargarArchivoVenta('{{ $venta->comprobante_paquete_url }}', '{{ $folioDescarga }}_{{ $fechaDescarga }}_paquete')"
                                    class="text-xs text-indigo-100 hover:text-white flex items-center gap-1 font-bold transition-colors cursor-pointer"
                                >
                                    <i class="fas fa-download"></i> Descargar
                                </button>
                                <button type="button" @click="zoomPaquete = false" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-4 bg-slate-900/5 flex items-center justify-center max-h-[75vh] overflow-auto">
                            <img src="{{ $venta->comprobante_paquete_url }}" alt="Comprobante paquete" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-sm border border-slate-200">
                        </div>
                        <div class="px-6 py-3.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-medium">Venta #{{ $folioDescarga }}</span>
                            <button type="button" @click="zoomPaquete = false" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- 3. COMPROBANTE DE DEVOLUCIÓN -->
        @if($venta->comprobante_devolucion_url)
        <div class="bg-white border border-rose-200 rounded-2xl shadow-sm overflow-hidden" x-data="{ zoomDevolucion: false }">
            <div class="p-5 border-b border-rose-100 bg-gradient-to-r from-rose-50/70 to-purple-50/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold text-base shadow-sm shadow-rose-500/20">
                        <i class="fas fa-arrow-rotate-left"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-tight">Comprobante de Devolución por Garantía</h2>
                            <span class="text-[10px] font-bold text-rose-700 bg-rose-100 border border-rose-200 px-2.5 py-0.5 rounded-full">
                                Devolución
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">Fotografía del artículo recibido en retorno al almacén</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a 
                        href="{{ $venta->comprobante_devolucion_url }}" 
                        target="_blank" 
                        class="rounded-xl border border-rose-200 bg-white hover:bg-rose-600 hover:text-white text-rose-700 font-bold px-3.5 py-2 text-xs transition-all shadow-2xs flex items-center gap-1.5"
                    >
                        <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                        <span>Abrir pestaña</span>
                    </a>
                    <button 
                        type="button" 
                        onclick="descargarArchivoVenta('{{ $venta->comprobante_devolucion_url }}', '{{ $folioDescarga }}_{{ $fechaDescarga }}_devolucion')"
                        class="rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold px-3.5 py-2 text-xs transition-all shadow-sm shadow-rose-500/20 flex items-center gap-1.5 cursor-pointer"
                        title="Descargar con folio y fecha"
                    >
                        <i class="fas fa-download text-[11px]"></i>
                        <span>Descargar</span>
                    </button>
                </div>
            </div>

            <div class="p-6 flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <!-- MINIATURA INTERACTIVA -->
                <button 
                    type="button" 
                    @click="zoomDevolucion = true"
                    class="block group relative overflow-hidden rounded-2xl border border-slate-200 shadow-md shrink-0 cursor-pointer focus:outline-none focus:ring-4 focus:ring-rose-500/20"
                    title="Clic para ampliar imagen"
                >
                    <img 
                        src="{{ $venta->comprobante_devolucion_url }}" 
                        alt="Comprobante de devolucion" 
                        class="w-36 h-36 sm:w-44 sm:h-44 object-cover group-hover:scale-105 transition-transform duration-300"
                    >
                    <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5">
                        <i class="fas fa-magnifying-glass-plus text-sm"></i>
                        <span>Ampliar</span>
                    </div>
                </button>

                <div class="space-y-2.5 text-xs text-slate-600 flex-1">
                    <p class="font-bold text-slate-800">Inspección física del artículo devuelto</p>
                    <p class="text-slate-600 leading-relaxed">
                        Fotografía adjunta para soporte del retorno físico de mercancía al stock de inventario.
                    </p>
                    <div>
                        <button 
                            type="button" 
                            @click="zoomDevolucion = true"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 hover:text-rose-800 transition-colors cursor-pointer"
                        >
                            <i class="fas fa-expand text-[10px]"></i> Ver evidencia en pantalla completa
                        </button>
                    </div>
                </div>
            </div>

            <!-- MODAL LIGHTBOX DEVOLUCIÓN -->
            <div 
                x-show="zoomDevolucion" 
                x-cloak
                class="fixed inset-0 z-50 overflow-y-auto"
                style="display: none;"
                role="dialog"
                aria-modal="true"
            >
                <div 
                    x-show="zoomDevolucion"
                    x-transition.opacity
                    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
                    @click="zoomDevolucion = false"
                ></div>

                <div class="flex min-h-screen items-center justify-center p-4 text-center">
                    <div 
                        x-show="zoomDevolucion"
                        x-transition
                        class="relative z-10 max-w-4xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200"
                        @click.away="zoomDevolucion = false"
                    >
                        <div class="bg-rose-600 px-6 py-4 flex items-center justify-between text-white shadow-md">
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-arrow-rotate-left text-white text-base"></i>
                                <span class="text-sm font-black uppercase tracking-wider text-white">Comprobante de Devolución</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button 
                                    type="button" 
                                    onclick="descargarArchivoVenta('{{ $venta->comprobante_devolucion_url }}', '{{ $folioDescarga }}_{{ $fechaDescarga }}_devolucion')"
                                    class="text-xs text-rose-100 hover:text-white flex items-center gap-1 font-bold transition-colors cursor-pointer"
                                >
                                    <i class="fas fa-download"></i> Descargar
                                </button>
                                <button type="button" @click="zoomDevolucion = false" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-4 bg-slate-900/5 flex items-center justify-center max-h-[75vh] overflow-auto">
                            <img src="{{ $venta->comprobante_devolucion_url }}" alt="Comprobante devolucion" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-sm border border-slate-200">
                        </div>
                        <div class="px-6 py-3.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-medium">Venta #{{ $folioDescarga }}</span>
                            <button type="button" @click="zoomDevolucion = false" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <script>
            function descargarArchivoVenta(url, nombreBase) {
                if (!url) return;
                const extMatch = url.match(/\.([a-zA-Z0-9]+)(?:\?|#|$)/);
                const ext = extMatch ? extMatch[1] : 'jpg';
                const filename = (nombreBase || 'VNT_comprobante') + '.' + ext;

                fetch(url)
                    .then(res => res.blob())
                    .then(blob => {
                        const blobUrl = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = blobUrl;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(blobUrl);
                        document.body.removeChild(a);
                    })
                    .catch(() => {
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        a.target = '_blank';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    });
            }
        </script>

    </div>
</x-app>
