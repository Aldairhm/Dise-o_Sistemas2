<x-app title="Registrar compra | AXStore">
    <meta name="compras-store-url" content="{{ route('compras.store') }}">
    <div x-data="registroCompra({{ Js::from($proveedores) }}, {{ Js::from($variantes) }})" class="max-w-[1440px] mx-auto space-y-6">
        
        <!-- HEADER (Sin cambios lógicos, refinado visualmente) -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-4">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <i class="fas fa-cart-plus text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600 mb-1">Abastecimiento</p>
                        <h1 class="text-3xl font-black tracking-tight text-slate-900">Registrar compra</h1>
                    </div>
                </div>
                <p class="text-sm text-slate-500 mt-3 max-w-2xl">Registra las unidades recibidas. La compra entrará a bodega y no cambiará el stock disponible en tienda.</p>
            </div>
            <div class="flex items-center gap-2 text-sm font-bold text-amber-800 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 shadow-sm">
                <i class="fas fa-warehouse text-amber-600"></i> La recepción aumenta bodega
            </div>
        </div>

        <nav class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" aria-label="Secciones de compras">
            <a href="{{ route('compras.create') }}" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm"><i class="fas fa-cart-plus mr-2"></i>Nueva compra</a>
            <a href="{{ route('compras.historial') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-clock-rotate-left mr-2"></i>Historial</a>
            <a href="{{ route('compras.movimientos') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-warehouse mr-2"></i>Bodega a tienda</a>
        </nav>

        <div x-show="guardado" x-transition class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700 shadow-sm" style="display:none;">
            <i class="fas fa-circle-check text-lg"></i>
            <span>Compra registrada correctamente. Las unidades ya están en bodega.</span>
        </div>

        <!-- DATOS PRINCIPALES -->
        <section class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-file-invoice text-lg"></i></div>
                <div>
                    <h2 class="text-lg font-black text-slate-900">Datos de la compra</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Información principal de la recepción.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                <label class="block md:col-span-5">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Proveedor <b class="text-red-500">*</b></span>
                    <select x-model="proveedorId" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <option value="">Selecciona un proveedor</option>
                        @foreach ($proveedores as $proveedor)
                            <option value="{{ $proveedor['id'] }}">{{ $proveedor['nombre'] }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block md:col-span-3">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Recepción <b class="text-red-500">*</b></span>
                    <input type="date" x-model="fecha" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                </label>
                <label class="block md:col-span-4">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Referencia <span class="font-normal text-slate-400 normal-case">(opcional)</span></span>
                    <input type="text" x-model="referencia" placeholder="Ej. FAC-000124" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                </label>
                <label class="block md:col-span-12">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Descripción <span class="font-normal text-slate-400 normal-case">(opcional)</span></span>
                    <textarea x-model="observaciones" rows="2" placeholder="Estado de las cajas, acuerdos o cualquier detalle importante..." class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all resize-none"></textarea>
                </label>
            </div>
        </section>

        <!-- ÁREA DE TRABAJO -->
        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_340px] gap-6 items-start">
            <section class="space-y-6">
                
                <!-- EL NUEVO BUSCADOR (Elegante y claro) -->
                <div class="bg-white border-2 border-dashed border-slate-300 rounded-2xl p-6 transition-colors focus-within:border-blue-400 focus-within:bg-blue-50/30">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600">Catálogo</p>
                            <h2 class="text-xl font-black text-slate-900 mt-1">Buscar y agregar</h2>
                        </div>
                        <span class="text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-lg" x-text="variantesDisponibles.length + ' disponibles'"></span>
                    </div>
                    
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input x-model="busqueda" type="search" placeholder="Escribe producto, variante o SKU..." class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3.5 pl-11 pr-4 text-sm text-slate-800 placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner">
                    </div>
                    
                    <div class="grid sm:grid-cols-2 gap-3 mt-4 max-h-60 overflow-y-auto pr-2">
                        <template x-for="variante in variantesDisponibles" :key="variante.id">
                            <button type="button" @click="agregarVariante(variante)" class="text-left rounded-xl border border-slate-200 bg-white hover:border-blue-400 hover:shadow-md hover:shadow-blue-500/5 px-4 py-3 transition-all group">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="mb-2 h-12 w-12 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                            <template x-if="variante.imagen"><img :src="variante.imagen" :alt="variante.variante" class="h-full w-full object-cover"></template>
                                            <template x-if="!variante.imagen"><div class="flex h-full items-center justify-center text-slate-300"><i class="fas fa-image"></i></div></template>
                                        </div>
                                        <p class="text-sm font-bold text-slate-800 truncate group-hover:text-blue-700" x-text="variante.producto"></p>
                                        <p class="text-xs text-slate-500 truncate mt-0.5" x-text="variante.variante"></p>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-2" x-text="variante.sku"></p>
                                        <p class="text-xs font-black text-blue-700 mt-1" x-text="'Venta: ' + moneda(variante.precio_venta)"></p>
                                    </div>
                                    <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-colors flex-shrink-0">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                </div>
                            </button>
                        </template>
                        <p x-show="variantesDisponibles.length === 0" class="sm:col-span-2 text-sm font-medium text-slate-400 py-6 text-center" style="display:none;">No se encontraron resultados.</p>
                    </div>
                </div>

                <!-- LA NUEVA TABLA (Minimalista) -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between gap-3 p-5 border-b border-slate-100 bg-slate-50/50">
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Detalle de recepción</h2>
                        </div>
                        <span class="text-xs font-bold text-blue-700 bg-blue-100 px-3 py-1 rounded-lg" x-text="unidades + ' unidades totales'"></span>
                    </div>
                    
                    <div x-show="lineas.length === 0" class="py-16 px-5 text-center" style="display:none;">
                        <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-4 border border-slate-100"><i class="fas fa-box-open text-2xl"></i></div>
                        <p class="font-bold text-slate-600">Ninguna variante agregada</p>
                        <p class="text-sm text-slate-400 mt-1">Usa el buscador de arriba para comenzar.</p>
                    </div>
                    
                    <div x-show="lineas.length > 0" class="overflow-x-auto" style="display:none;">
                        <table class="w-full min-w-[760px] text-sm text-left">
                            <thead class="bg-slate-50 text-[10px] uppercase tracking-widest text-slate-500 border-b border-slate-200">
                                <tr>
                                    <th class="px-5 py-4 font-bold">Variante</th>
                                    <th class="px-3 py-4 font-bold w-28 text-center">Cantidad</th>
                                    <th class="px-3 py-4 font-bold w-36 text-center">Costo proveedor</th>
                                    <th class="px-5 py-4 font-bold text-right w-32">Subtotal</th>
                                    <th class="px-5 py-4 font-bold text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="linea in lineas" :key="linea.id">
                                    <tr class="hover:bg-slate-50/50 transition-colors group">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                                    <template x-if="linea.imagen"><img :src="linea.imagen" :alt="linea.variante" class="h-full w-full object-cover"></template>
                                                    <template x-if="!linea.imagen"><div class="flex h-full items-center justify-center text-slate-300"><i class="fas fa-image"></i></div></template>
                                                </div>
                                                <div><p class="font-bold text-slate-800" x-text="linea.producto"></p>
                                            <p class="text-xs text-slate-500 mt-0.5" x-text="linea.variante"></p>
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1" x-text="linea.sku || 'Sin SKU'"></p>
                                            <p class="text-xs font-black text-blue-700 mt-1" x-text="'Precio venta: ' + moneda(linea.precio_venta)"></p>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Inputs "Invisibles" pero accesibles -->
                                        <td class="px-3 py-4">
                                            <input type="number" min="1" step="1" x-model.number="linea.cantidad" class="w-full text-center rounded-lg border-transparent bg-transparent hover:bg-slate-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 py-2 text-slate-800 font-bold transition-all">
                                        </td>
                                        <td class="px-3 py-4">
                                            <div class="relative flex items-center justify-center">
                                                <span class="absolute left-4 text-slate-400 font-bold">$</span>
                                                <input type="number" min="0" step="0.01" x-model.number="linea.costo" class="w-full pl-8 pr-2 rounded-lg border-transparent bg-transparent hover:bg-slate-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 py-2 text-slate-800 font-bold transition-all">
                                            </div>
                                        </td>
                                        
                                        <td class="px-5 py-4 text-right">
                                            <p class="font-black text-slate-800 text-base" x-text="moneda(costoLinea(linea))"></p>
                                            <p class="text-[10px] text-emerald-600 font-bold mt-1" x-show="utilidadLinea(linea) > 0" title="Utilidad por unidad estimada">
                                                +<span x-text="moneda(utilidadLinea(linea))"></span> utl.
                                            </p>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <button type="button" @click="quitarLinea(linea.id)" class="w-8 h-8 rounded-lg text-slate-300 hover:text-red-600 hover:bg-red-50 flex items-center justify-center transition-colors mx-auto" title="Quitar variante">
                                                <i class="fas fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- SIDEBAR ELEGANTE -->
            <aside class="space-y-6 xl:sticky xl:top-24">
                <section class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-3"><i class="fas fa-circle-info text-blue-600"></i><h2 class="font-black text-slate-900">Ayuda rápida</h2></div>
                    <p class="text-sm text-slate-500 leading-relaxed">El <strong class="text-slate-700">costo</strong> es lo que le pagas al proveedor. Tu precio de venta ya está configurado en el catálogo y se usa para estimar la utilidad.</p>
                </section>
                
                <section class="bg-gradient-to-b from-slate-800 to-slate-900 text-white rounded-3xl p-6 shadow-xl shadow-slate-900/20 border border-slate-700/50">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-black">Resumen</h2>
                        <div class="w-8 h-8 rounded-full bg-slate-700/50 flex items-center justify-center"><i class="fas fa-calculator text-blue-400 text-sm"></i></div>
                    </div>
                    
                    <div class="space-y-4 text-sm font-medium">
                        <div class="flex justify-between items-center text-slate-300">
                            <span>Variantes</span>
                            <strong class="text-white bg-slate-700/50 px-2 py-0.5 rounded" x-text="lineas.length"></strong>
                        </div>
                        <div class="flex justify-between items-center text-slate-300">
                            <span>Unidades</span>
                            <strong class="text-white" x-text="unidades"></strong>
                        </div>
                        <div class="flex justify-between items-center text-slate-300">
                            <span>Utilidad est.</span>
                            <strong class="text-emerald-400" x-text="moneda(totalUtilidad)"></strong>
                        </div>
                    </div>
                    
                    <div class="border-t border-slate-700 mt-6 pt-6 flex justify-between items-end">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">Total a pagar</p>
                            <p class="text-3xl font-black text-white leading-none" x-text="moneda(subtotal)"></p>
                        </div>
                    </div>
                    
                    <button type="button" @click="guardarCompra()" :disabled="!formularioValido()" class="w-full mt-8 rounded-xl bg-blue-600 hover:bg-blue-500 disabled:bg-slate-800 disabled:text-slate-500 disabled:border border-slate-700 disabled:cursor-not-allowed py-3.5 text-sm font-black transition-all shadow-lg shadow-blue-600/20 disabled:shadow-none flex items-center justify-center gap-2">
                        <i class="fas fa-check"></i> Confirmar recepción
                    </button>
                </section>
            </aside>
        </div>
    </div>
</x-app>