<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Registrar compra | AXStore']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Registrar compra | AXStore']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <meta name="compras-store-url" content="<?php echo e(route('compras.store')); ?>">
    <div x-data="registroCompra(<?php echo e(Js::from($proveedores)); ?>, <?php echo e(Js::from($variantes)); ?>)" class="max-w-[1440px] mx-auto space-y-6">
        
        <!-- HEADER (Sin cambios lógicos, refinado visualmente) -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div>
                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-4">
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
            <a href="<?php echo e(route('compras.create')); ?>" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm"><i class="fas fa-cart-plus mr-2"></i>Nueva compra</a>
            <a href="<?php echo e(route('compras.historial')); ?>" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-clock-rotate-left mr-2"></i>Historial</a>
            <a href="<?php echo e(route('compras.movimientos')); ?>" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-warehouse mr-2"></i>Gestión de Inventario</a>
            <a href="<?php echo e(route('compras.devoluciones')); ?>" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-truck-arrow-right mr-2"></i>Devoluciones a proveedores</a>
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
                
                <div class="block md:col-span-5 relative" x-data="{ openProv: false }">
                    <label class="mb-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-tight">
                        <i class="fas fa-truck text-blue-600 text-xs"></i>
                        <span>Proveedor</span> <b class="text-red-500">*</b>
                    </label>
                    <input type="hidden" x-model="proveedorId">
                    <button type="button" @click="openProv = !openProv" @click.outside="openProv = false"
                        class="h-11 w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50/90 px-3.5 text-xs font-bold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 flex items-center justify-between gap-2 cursor-pointer text-left">
                        <span class="flex items-center gap-2 truncate">
                            <i class="fas fa-building text-slate-400" :class="proveedorId ? 'text-blue-600' : 'text-slate-400'"></i>
                            <span class="truncate" x-text="proveedores.find(p => String(p.id) === String(proveedorId))?.nombre || 'Selecciona un proveedor'">Selecciona un proveedor</span>
                        </span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="openProv ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openProv" x-transition.origin.top.left class="absolute left-0 top-full z-50 mt-1.5 w-full min-w-[220px] rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xl max-h-64 overflow-y-auto" style="display: none;">
                        <div class="space-y-0.5">
                            <button type="button" @click="proveedorId = ''; openProv = false"
                                class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left text-slate-600 hover:bg-slate-50">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-building text-slate-400"></i>
                                    <span>Selecciona un proveedor</span>
                                </span>
                                <i class="fas fa-check text-blue-600 text-[10px]" x-show="!proveedorId"></i>
                            </button>
                            <template x-for="p in proveedores" :key="p.id">
                                <button type="button" @click="proveedorId = p.id; openProv = false"
                                    class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left hover:bg-slate-50 hover:text-slate-900"
                                    :class="String(proveedorId) === String(p.id) ? 'bg-blue-50 text-blue-700' : 'text-slate-700'">
                                    <span class="flex items-center gap-2 truncate">
                                        <i class="fas fa-truck text-blue-500"></i>
                                        <span x-text="p.nombre"></span>
                                    </span>
                                    <i class="fas fa-check text-blue-600 text-[10px]" x-show="String(proveedorId) === String(p.id)"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                
                <div class="block md:col-span-3">
                    <label class="mb-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-tight">
                        <i class="fas fa-calendar-day text-blue-600 text-xs"></i>
                        <span>Recepción</span> <b class="text-red-500">*</b>
                    </label>
                    <input type="date" x-model="fecha" class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50/90 px-3.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                </div>

                
                <div class="block md:col-span-4">
                    <label class="mb-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-tight">
                        <i class="fas fa-hashtag text-blue-600 text-xs"></i>
                        <span>Referencia</span> <span class="font-normal text-slate-400 text-[11px]">(opcional)</span>
                    </label>
                    <input type="text" x-model="referencia" placeholder="Ej. FAC-000124" class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50/90 px-3.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                </div>

                
                <div class="block md:col-span-12">
                    <label class="mb-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-tight">
                        <i class="fas fa-comment-dots text-blue-600 text-xs"></i>
                        <span>Descripción</span> <span class="font-normal text-slate-400 text-[11px]">(opcional)</span>
                    </label>
                    <textarea x-model="observaciones" rows="2" placeholder="Estado de las cajas, acuerdos o cualquier detalle importante..." class="w-full rounded-xl border border-slate-200 bg-slate-50/90 px-3.5 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 resize-none"></textarea>
                </div>
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
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" x-show="!buscando"></i>
                        <i class="fas fa-spinner fa-spin absolute left-4 top-1/2 -translate-y-1/2 text-blue-500" x-show="buscando" style="display:none;"></i>
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
                        <p x-show="variantesDisponibles.length === 0 && !buscando" class="sm:col-span-2 text-sm font-medium text-slate-400 py-6 text-center" style="display:none;">No se encontraron resultados.</p>
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
                    <p class="text-sm text-slate-500 leading-relaxed">El <strong class="text-slate-700">costo</strong> es lo que le pagas al proveedor por cada unidad recibida.</p>
                </section>
                
                <section class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-black text-slate-900">Resumen</h2>
                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center"><i class="fas fa-calculator text-blue-600 text-sm"></i></div>
                    </div>
                    
                    <div class="space-y-4 text-sm font-medium">
                        <div class="flex justify-between items-center text-slate-500">
                            <span>Variantes</span>
                            <strong class="text-slate-800 bg-slate-100 px-2 py-0.5 rounded font-bold" x-text="lineas.length"></strong>
                        </div>
                        <div class="flex justify-between items-center text-slate-500">
                            <span>Unidades</span>
                            <strong class="text-slate-800 font-bold" x-text="unidades"></strong>
                        </div>
                    </div>
                    
                    <div class="border-t border-slate-100 mt-6 pt-6 flex justify-between items-end">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">Total a pagar</p>
                            <p class="text-3xl font-black text-blue-600 leading-none" x-text="moneda(subtotal)"></p>
                        </div>
                    </div>
                    
                    <button type="button" @click="Swal.fire({ title: '¿Estás seguro?', text: 'Verifica que las cantidades sean correctas antes de confirmar la recepción.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Sí, confirmar', cancelButtonText: 'Cancelar' }).then((result) => { if (result.isConfirmed) { guardarCompra(); } })" :disabled="!formularioValido()" class="w-full mt-8 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:bg-slate-100 disabled:text-slate-400 disabled:border border-slate-200 disabled:cursor-not-allowed py-3.5 text-sm font-extrabold text-white transition-all shadow-lg shadow-blue-500/25 disabled:shadow-none flex items-center justify-center gap-2 cursor-pointer hover:scale-[1.01] active:scale-[0.99]">
                        <i class="fas fa-check"></i> Confirmar recepción
                    </button>
                </section>
            </aside>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7ae6b45c011e855a5545a671a7f3568e)): ?>
<?php $attributes = $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e; ?>
<?php unset($__attributesOriginal7ae6b45c011e855a5545a671a7f3568e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7ae6b45c011e855a5545a671a7f3568e)): ?>
<?php $component = $__componentOriginal7ae6b45c011e855a5545a671a7f3568e; ?>
<?php unset($__componentOriginal7ae6b45c011e855a5545a671a7f3568e); ?>
<?php endif; ?><?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/compras/create.blade.php ENDPATH**/ ?>