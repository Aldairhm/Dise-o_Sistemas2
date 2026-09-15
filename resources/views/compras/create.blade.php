<x-app title="Registrar compra | AXStore">
    <div x-data="registroCompra({{ Js::from($proveedores) }}, {{ Js::from($variantes) }})" class="max-w-[1440px] mx-auto space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <i class="fas fa-cart-plus text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600">Abastecimiento</p>
                        <h1 class="text-3xl font-black tracking-tight text-slate-900">Registrar compra</h1>
                    </div>
                </div>
                <p class="text-sm text-slate-500 mt-3 max-w-2xl">Agrega las variantes recibidas, define el costo y revisa el precio sugerido antes de confirmar.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                <i class="fas fa-flask"></i> Vista de demostración
            </div>
        </div>

        <div x-show="guardado" x-transition class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700" style="display:none;">
            <i class="fas fa-circle-check"></i>
            <span>La vista está lista: esta demostración no persiste datos todavía.</span>
        </div>

        <section class="bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <i class="fas fa-file-invoice text-blue-600"></i>
                <h2 class="font-black text-slate-900">Datos de la compra</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="block md:col-span-1">
                    <span class="field-label">Proveedor <b>*</b></span>
                    <select x-model="proveedorId" class="field-input">
                        <option value="">Selecciona un proveedor</option>
                        @foreach ($proveedores as $proveedor)
                            <option value="{{ $proveedor['id'] }}">{{ $proveedor['nombre'] }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="field-label">Fecha de compra <b>*</b></span>
                    <input type="date" x-model="fecha" class="field-input">
                </label>
                <label class="block">
                    <span class="field-label">Factura o referencia</span>
                    <input type="text" x-model="referencia" placeholder="Ej. FAC-000124" class="field-input">
                </label>
            </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_360px] gap-6 items-start">
            <section class="space-y-5">
                <div class="bg-slate-900 rounded-2xl p-5 text-white shadow-xl shadow-slate-900/10">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-300">Catálogo de variantes</p>
                            <h2 class="text-xl font-black mt-1">¿Qué recibiste?</h2>
                        </div>
                        <span class="text-xs text-slate-400" x-text="variantesDisponibles.length + ' disponibles'"></span>
                    </div>
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input x-model="busqueda" type="search" placeholder="Buscar por producto, variante o SKU..." class="w-full rounded-xl border border-slate-700 bg-slate-800 py-3 pl-11 pr-4 text-sm text-white placeholder:text-slate-500 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-400/20">
                    </div>
                    <div class="grid sm:grid-cols-2 gap-2 mt-4 max-h-56 overflow-y-auto pr-1">
                        <template x-for="variante in variantesDisponibles" :key="variante.id">
                            <button type="button" @click="agregarVariante(variante)" class="text-left rounded-xl border border-slate-700 bg-slate-800/70 hover:bg-blue-600 hover:border-blue-400 px-3 py-3 transition-colors group">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold truncate" x-text="variante.producto"></p>
                                        <p class="text-xs text-slate-400 group-hover:text-blue-100 truncate mt-1" x-text="variante.variante"></p>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 group-hover:text-blue-100 mt-2" x-text="variante.sku"></p>
                                    </div>
                                    <i class="fas fa-plus-circle text-slate-500 group-hover:text-white mt-1"></i>
                                </div>
                            </button>
                        </template>
                        <p x-show="variantesDisponibles.length === 0" class="sm:col-span-2 text-sm text-slate-400 py-4 text-center" style="display:none;">No hay variantes que coincidan.</p>
                    </div>
                </div>

                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between gap-3 p-5 border-b border-slate-100">
                        <div>
                            <h2 class="font-black text-slate-900">Detalle de recepción</h2>
                            <p class="text-xs text-slate-500 mt-1" x-text="lineas.length + ' variante(s) agregada(s)'"></p>
                        </div>
                        <span class="text-xs font-bold text-slate-400" x-text="unidades + ' unidades'"></span>
                    </div>
                    <div x-show="lineas.length === 0" class="py-16 px-5 text-center" style="display:none;">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4"><i class="fas fa-box-open text-xl"></i></div>
                        <p class="font-bold text-slate-700">Todavía no hay variantes</p>
                        <p class="text-sm text-slate-400 mt-1">Usa el buscador de arriba para agregar la primera línea.</p>
                    </div>
                    <div x-show="lineas.length > 0" class="overflow-x-auto" style="display:none;">
                        <table class="w-full min-w-[760px] text-sm">
                            <thead class="bg-slate-50 text-[10px] uppercase tracking-widest text-slate-400">
                                <tr>
                                    <th class="text-left px-5 py-3">Variante</th><th class="text-left px-3 py-3 w-28">Cantidad</th><th class="text-left px-3 py-3 w-36">Costo unit.</th><th class="text-right px-3 py-3">Subtotal</th><th class="text-right px-5 py-3">Venta sugerida</th><th></th>
                                </tr>
                            </thead>
                            <template x-for="linea in lineas" :key="linea.id">
                                <tbody>
                                    <tr class="border-t border-slate-100 align-top">
                                        <td class="px-5 py-4"><p class="font-bold text-slate-800" x-text="linea.producto"></p><p class="text-xs text-slate-500 mt-1" x-text="linea.variante + ' · ' + linea.sku"></p><button type="button" @click="alternarPersonalizacion(linea)" class="mt-2 inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider transition-colors" :class="linea.personalizado ? 'text-blue-600' : 'text-slate-400 hover:text-blue-600'"><i class="fas" :class="linea.personalizado ? 'fa-sliders' : 'fa-sliders-h'"></i><span x-text="linea.personalizado ? 'Configuración personalizada' : 'Usar valores generales'"></span></button></td>
                                        <td class="px-3 py-4"><input type="number" min="1" step="1" x-model.number="linea.cantidad" class="table-input"></td>
                                        <td class="px-3 py-4"><div class="relative"><span class="currency">$</span><input type="number" min="0" step="0.01" x-model.number="linea.costo" class="table-input pl-7"></div></td>
                                        <td class="px-3 py-4 text-right font-bold text-slate-700" x-text="moneda(costoLinea(linea))"></td>
                                        <td class="px-5 py-4 text-right"><p class="font-black text-blue-700" x-text="moneda(precioVenta(linea))"></p><p class="text-[10px] text-slate-400 mt-1" x-text="moneda(precioVenta(linea) / Math.max(1, Number(linea.cantidad || 1))) + ' / unidad'"></p></td>
                                        <td class="px-3 py-4"><button type="button" @click="quitarLinea(linea.id)" class="icon-button text-slate-400 hover:text-red-600 hover:bg-red-50" title="Quitar variante"><i class="fas fa-trash-can"></i></button></td>
                                    </tr>
                                    <tr x-show="linea.personalizado" x-transition class="bg-blue-50/50 border-t border-blue-100" style="display:none;">
                                        <td colspan="6" class="px-5 py-4">
                                            <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                                                <div class="flex-1"><label class="field-label mb-2">Comisión de esta variante</label><div class="flex gap-2"><select x-model="linea.comisionTipo" class="mini-select"><option value="porcentaje">%</option><option value="valor">$</option></select><input type="number" min="0" step="0.01" x-model.number="linea.comisionValor" class="field-input"></div></div>
                                                <div class="flex-1"><label class="field-label mb-2">Margen de esta variante</label><div class="flex gap-2"><select x-model="linea.margenTipo" class="mini-select"><option value="porcentaje">%</option><option value="valor">$</option></select><input type="number" min="0" step="0.01" x-model.number="linea.margenValor" class="field-input"></div></div>
                                                <div class="lg:min-w-44 rounded-xl bg-white border border-blue-100 px-4 py-3"><p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Venta sugerida</p><p class="font-black text-blue-700 mt-1" x-text="moneda(precioVenta(linea))"></p></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </template>
                        </table>
                    </div>
                </div>
            </section>

            <aside class="space-y-5 xl:sticky xl:top-24">
                <section class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center gap-2 mb-5"><i class="fas fa-sliders text-blue-600"></i><h2 class="font-black text-slate-900">Reglas de precio</h2></div>
                    <div class="space-y-4">
                        <div><div class="flex justify-between items-center mb-2"><label class="field-label mb-0">Comisión</label><select x-model="comisionTipo" class="mini-select"><option value="porcentaje">%</option><option value="valor">$</option></select></div><div class="relative"><input type="number" min="0" step="0.01" x-model.number="comisionValor" class="field-input pr-10"><span class="suffix" x-text="comisionTipo === 'porcentaje' ? '%' : '$'"></span></div></div>
                        <div><div class="flex justify-between items-center mb-2"><label class="field-label mb-0">Margen de ganancia</label><select x-model="margenTipo" class="mini-select"><option value="porcentaje">%</option><option value="valor">$</option></select></div><div class="relative"><input type="number" min="0" step="0.01" x-model.number="margenValor" class="field-input pr-10"><span class="suffix" x-text="margenTipo === 'porcentaje' ? '%' : '$'"></span></div></div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed mt-4"><i class="fas fa-circle-info mr-1"></i> El precio sugerido suma costo, comisión y margen.</p>
                </section>
                <section class="bg-slate-900 text-white rounded-2xl p-5 shadow-xl shadow-slate-900/15">
                    <div class="flex items-center justify-between mb-5"><h2 class="font-black">Resumen</h2><i class="fas fa-calculator text-blue-300"></i></div>
                    <div class="space-y-3 text-sm"><div class="flex justify-between text-slate-400"><span>Variantes</span><strong class="text-white" x-text="lineas.length"></strong></div><div class="flex justify-between text-slate-400"><span>Unidades</span><strong class="text-white" x-text="unidades"></strong></div><div class="flex justify-between text-slate-400"><span>Subtotal compra</span><strong class="text-white" x-text="moneda(subtotal)"></strong></div><div class="flex justify-between text-slate-400"><span>Comisión</span><strong class="text-white" x-text="moneda(totalComision)"></strong></div><div class="flex justify-between text-slate-400"><span>Margen estimado</span><strong class="text-emerald-300" x-text="moneda(totalMargen)"></strong></div></div>
                    <div class="border-t border-slate-700 mt-5 pt-5 flex justify-between items-end"><div><p class="text-xs uppercase tracking-widest text-slate-500">Inversión total</p><p class="text-3xl font-black mt-1" x-text="moneda(totalCompra)"></p></div><div class="text-right"><p class="text-xs text-slate-500">Venta sugerida</p><p class="font-black text-blue-300 mt-1" x-text="moneda(precioVentaTotal)"></p></div></div>
                    <button type="button" @click="guardarDemo()" :disabled="!formularioValido()" class="w-full mt-6 rounded-xl bg-blue-500 hover:bg-blue-400 disabled:bg-slate-700 disabled:text-slate-500 disabled:cursor-not-allowed py-3 text-sm font-black transition-colors"><i class="fas fa-check mr-2"></i> Confirmar compra</button>
                </section>
                <label class="block"><span class="field-label">Observaciones</span><textarea x-model="observaciones" rows="3" placeholder="Notas sobre la recepción..." class="field-input resize-none"></textarea></label>
            </aside>
        </div>
    </div>
</x-app>