<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Historial de compras | AXStore']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Historial de compras | AXStore']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="max-w-[1440px] mx-auto space-y-6" x-data="{
        modalDetalleAbierto: false,
        cargandoDetalle: false,
        compraSeleccionada: null,
        solicitudDetalle: 0,

        async verDetalle(id) {
            const solicitud = ++this.solicitudDetalle;
            this.modalDetalleAbierto = true;
            this.cargandoDetalle = true;
            this.compraSeleccionada = null;

            try {
                const res = await fetch(`<?php echo e(url('compras')); ?>/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) throw new Error('No se pudo cargar la compra.');
                const data = await res.json();
                if (solicitud !== this.solicitudDetalle) return;
                if (data.success && data.compra) {
                    this.compraSeleccionada = data.compra;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo cargar el detalle de la compra.'
                    });
                    this.modalDetalleAbierto = false;
                }
            } catch (err) {
                if (solicitud !== this.solicitudDetalle) return;
                console.error('Error al cargar detalle de compra:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Ocurrió un error al cargar los datos de la compra.'
                });
                this.modalDetalleAbierto = false;
            } finally {
                if (solicitud === this.solicitudDetalle) this.cargandoDetalle = false;
            }
        },

        cerrarDetalle() {
            ++this.solicitudDetalle;
            this.cargandoDetalle = false;
            this.modalDetalleAbierto = false;
            this.compraSeleccionada = null;
        }
    }">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 mb-4"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600 mb-1">Abastecimiento</p>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Compras</h1>
                <p class="text-sm text-slate-500 mt-2">Consulta las recepciones registradas y su estado.</p>
            </div>
        </div>

        <nav class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" aria-label="Secciones de compras">
            <a href="<?php echo e(route('compras.create')); ?>" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-cart-plus mr-2"></i>Nueva compra</a>
            <a href="<?php echo e(route('compras.historial')); ?>" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm"><i class="fas fa-clock-rotate-left mr-2"></i>Historial</a>
            <a href="<?php echo e(route('compras.movimientos')); ?>" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-warehouse mr-2"></i>Gestión de Inventario</a>
            <a href="<?php echo e(route('compras.devoluciones')); ?>" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-truck-arrow-right mr-2"></i>Devoluciones a proveedores</a>
        </nav>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/60 p-5">
                <div><h2 class="text-lg font-black text-slate-900">Compras registradas</h2><p class="text-xs text-slate-500 mt-1"><?php echo e($compras->total()); ?> recepción(es) encontradas.</p></div>
                <i class="fas fa-file-invoice-dollar text-xl text-blue-600"></i>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-[10px] uppercase tracking-widest text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Fecha</th>
                            <th class="px-5 py-4">Proveedor</th>
                            <th class="px-5 py-4">Referencia</th>
                            <th class="px-5 py-4 text-center">Líneas</th>
                            <th class="px-5 py-4 text-right">Total</th>
                            <th class="px-5 py-4">Estado</th>
                            <th class="px-5 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="hover:bg-slate-50/60"><td class="px-5 py-4 font-semibold text-slate-700"><?php echo e($compra->fecha_compra?->format('d/m/Y')); ?></td><td class="px-5 py-4 font-bold text-slate-800"><?php echo e($compra->proveedor?->nombre ?? 'Sin proveedor'); ?></td><td class="px-5 py-4 text-slate-500"><?php echo e($compra->referencia ?: 'Sin referencia'); ?></td><td class="px-5 py-4 text-center text-slate-600"><?php echo e($compra->detalles_count); ?></td><td class="px-5 py-4 text-right font-black text-slate-800">$<?php echo e(number_format((float) $compra->total, 2)); ?></td><td class="px-5 py-4"><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold capitalize text-emerald-700"><?php echo e($compra->estado); ?></span></td><td class="px-5 py-4 text-right"><div class="flex items-center justify-end gap-3"><button type="button" onclick="document.getElementById('compra-detalle-<?php echo e($compra->id); ?>').showModal()" class="text-xs font-bold text-blue-600 hover:text-blue-800"><i class="fas fa-eye mr-1"></i>Ver detalle</button><button type="button" onclick="document.getElementById('compra-devolucion-<?php echo e($compra->id); ?>').showModal()" class="text-xs font-bold text-amber-600 hover:text-amber-800"><i class="fas fa-truck-arrow-right mr-1"></i>Devolver</button></div><dialog id="compra-detalle-<?php echo e($compra->id); ?>" class="m-auto w-[min(680px,calc(100vw-2rem))] max-w-none rounded-2xl border border-slate-200 bg-white p-0 text-left shadow-2xl backdrop:bg-slate-900/50"><div class="p-5"><div class="mb-4 flex items-start justify-between gap-4 border-b border-slate-100 pb-4"><div><p class="text-xs font-black uppercase tracking-wider text-blue-600">Detalle de recepción</p><p class="mt-1 text-sm font-bold text-slate-800"><?php echo e($compra->proveedor?->nombre ?? 'Sin proveedor'); ?> · <?php echo e($compra->fecha_compra?->format('d/m/Y')); ?></p></div><button type="button" onclick="this.closest('dialog').close()" class="h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Cerrar"><i class="fas fa-xmark"></i></button></div><div class="mb-4 flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3"><span class="text-xs font-bold text-slate-500">Total de la recepción</span><span class="text-xl font-black text-slate-900">$<?php echo e(number_format((float) $compra->total, 2)); ?></span></div><p class="mb-4 text-xs text-slate-500"><?php echo e($compra->observaciones ?: 'Sin observaciones'); ?></p><div class="space-y-2"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $compra->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?> <?php $imagen = $detalle->variante?->imagenes?->firstWhere('es_principal', 1) ?? $detalle->variante?->imagenes?->first(); ?><div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2"><div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-white"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imagen): ?><img src="<?php echo e(str_starts_with($imagen->ruta_imagen, 'http') ? $imagen->ruta_imagen : \Illuminate\Support\Facades\Storage::disk('s3')->url($imagen->ruta_imagen)); ?>" alt="<?php echo e($detalle->variante?->nombre_variante); ?>" class="h-full w-full object-cover"><?php else: ?> <div class="flex h-full items-center justify-center text-slate-300"><i class="fas fa-image"></i></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><div class="min-w-0 flex-1"><p class="truncate text-xs font-black text-slate-800"><?php echo e($detalle->variante?->producto?->nombre ?? 'Producto'); ?></p><p class="truncate text-xs text-slate-500"><?php echo e($detalle->variante?->nombre_variante); ?></p><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">SKU: <?php echo e($detalle->variante?->sku ?: 'Sin SKU'); ?></p></div><div class="shrink-0 text-right text-xs"><p class="font-bold text-slate-700"><?php echo e($detalle->cantidad); ?> × $<?php echo e(number_format((float) $detalle->precio_unitario, 2)); ?></p><p class="font-black text-slate-900">$<?php echo e(number_format((float) $detalle->subtotal, 2)); ?></p></div></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></div><div class="mt-4 border-t border-slate-100 pt-3"><div class="mb-2 flex items-center justify-between"><p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Movimientos generados</p><a href="<?php echo e(route('compras.movimientos')); ?>" class="text-[10px] font-bold text-blue-600 hover:text-blue-800">Ver todos</a></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $compra->movimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movimiento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><div class="flex items-center justify-between gap-3 text-xs text-slate-600"><span><?php echo e($movimiento->tipo === 'compra_recibida' ? 'Entrada a bodega' : 'Transferencia a tienda'); ?></span><span><?php echo e($movimiento->cantidad); ?> unidades · <?php echo e($movimiento->created_at?->format('d/m/Y H:i')); ?></span></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><p class="text-xs text-slate-400">Sin movimientos asociados.</p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></div></dialog></td></tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr><td colspan="7" class="px-5 py-16 text-center text-sm text-slate-400"><i class="fas fa-inbox mb-3 block text-2xl"></i>Aún no hay compras registradas.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <dialog id="compra-devolucion-<?php echo e($compra->id); ?>" class="m-auto w-[min(820px,calc(100vw-2rem))] max-w-none rounded-2xl border border-slate-200 bg-white p-0 text-left shadow-2xl backdrop:bg-slate-900/50" x-data="{ cantidades: {}, excede(id, max) { const c = this.cantidades[id] || {}; return (Number(c.stock || 0) + Number(c.reserva || 0) + Number(c.cuarentena || 0)) > max; } }">
                    <form data-loading-form data-loading-label="Registrando devolución..." method="POST" action="<?php echo e(route('devoluciones.compra.store')); ?>" novalidate class="p-6"
                        onsubmit="
                            event.preventDefault(); 
                            let dialog = this.closest('dialog');
                            
                            if (!this.checkValidity()) { 
                                let errorMsg = 'Por favor, completa todos los campos requeridos.'; 
                                this.querySelectorAll('input[type=number]').forEach(i => { 
                                    if(i.validity.rangeOverflow) errorMsg = 'No puedes devolver más unidades de las que compraste.'; 
                                }); 
                                Swal.fire({ 
                                    target: dialog,
                                    toast: true, 
                                    position: 'top-end', 
                                    showConfirmButton: false, 
                                    timer: 5000, 
                                    timerProgressBar: true, 
                                    icon: 'error', 
                                    title: 'Datos inválidos', 
                                    text: errorMsg 
                                }); 
                                return; 
                            } 
                            
                            Swal.fire({ 
                                target: dialog,
                                title: '¿Estás seguro?', 
                                text: 'Verifica que las cantidades sean correctas antes de procesar.', 
                                icon: 'warning', 
                                showCancelButton: true, 
                                confirmButtonColor: '#3085d6', 
                                cancelButtonColor: '#d33', 
                                confirmButtonText: 'Sí, procesar', 
                                cancelButtonText: 'Cancelar' 
                            }).then((result) => { 
                                if (result.isConfirmed && window.AxLoading.beginForm(this)) { this.submit(); }
                            });
                        ">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="compra_id" value="<?php echo e($compra->id); ?>">
                        <div class="mb-5 flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                            <div><p class="text-xs font-black uppercase tracking-widest text-amber-600">Devolución a proveedor</p><h3 class="mt-1 text-xl font-black text-slate-900">Compra #<?php echo e($compra->id); ?></h3><p class="text-sm text-slate-500"><?php echo e($compra->proveedor?->nombre ?? 'Sin proveedor'); ?> · <?php echo e($compra->fecha_compra?->format('d/m/Y')); ?></p></div>
                            <button type="button" onclick="this.closest('dialog').close()" class="h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Cerrar"><i class="fas fa-xmark"></i></button>
                        </div>
                        <div class="mb-5 space-y-3 rounded-xl bg-slate-50 p-4">
                            <p class="text-xs font-black uppercase tracking-wider text-slate-500">Productos a devolver</p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $compra->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="rounded-xl border border-slate-200 bg-white p-4" x-init="cantidades[<?php echo e($detalle->id); ?>] = { stock: 0, reserva: 0, cuarentena: 0 }">
                                    <input type="hidden" name="productos[<?php echo e($detalle->id); ?>][id_compra_detalle]" value="<?php echo e($detalle->id); ?>">
                                    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <div><p class="text-sm font-bold text-slate-800"><?php echo e($detalle->variante?->producto?->nombre ?? 'Producto'); ?></p><p class="text-xs text-slate-500"><?php echo e($detalle->variante?->nombre_variante); ?> · Comprado: <?php echo e($detalle->cantidad); ?></p></div>
                                        <div class="flex flex-wrap gap-1.5 text-[10px] font-bold"><span class="rounded-md bg-blue-50 px-2 py-1 text-blue-700">Tienda: <?php echo e($detalle->variante?->stock ?? 0); ?></span><span class="rounded-md bg-amber-50 px-2 py-1 text-amber-700">Reserva: <?php echo e($detalle->variante?->reserva ?? 0); ?></span><span class="rounded-md bg-red-50 px-2 py-1 text-red-700">Cuarentena: <?php echo e($detalle->variante?->stock_cuarentena ?? 0); ?></span></div>
                                    </div>
                                    <div class="grid gap-2 sm:grid-cols-3"><label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Sacar de tienda<input type="number" min="0" max="<?php echo e($detalle->variante?->stock ?? 0); ?>" x-model.number="cantidades[<?php echo e($detalle->id); ?>].stock" name="productos[<?php echo e($detalle->id); ?>][ubicaciones][stock]" class="mt-1 w-full rounded-lg border border-slate-300 px-2 py-2 text-sm"></label><label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Sacar de reserva<input type="number" min="0" max="<?php echo e($detalle->variante?->reserva ?? 0); ?>" x-model.number="cantidades[<?php echo e($detalle->id); ?>].reserva" name="productos[<?php echo e($detalle->id); ?>][ubicaciones][reserva]" class="mt-1 w-full rounded-lg border border-slate-300 px-2 py-2 text-sm"></label><label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Sacar de cuarentena<input type="number" min="0" max="<?php echo e($detalle->variante?->stock_cuarentena ?? 0); ?>" x-model.number="cantidades[<?php echo e($detalle->id); ?>].cuarentena" name="productos[<?php echo e($detalle->id); ?>][ubicaciones][cuarentena]" class="mt-1 w-full rounded-lg border border-slate-300 px-2 py-2 text-sm"></label></div>
                                    <p x-show="excede(<?php echo e($detalle->id); ?>, <?php echo e($detalle->cantidad); ?>)" class="mt-2 text-xs font-bold text-red-600">La suma de ubicaciones supera las <?php echo e($detalle->cantidad); ?> unidades compradas.</p>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                        <label class="mb-4 block text-xs font-bold uppercase tracking-wider text-slate-600">Motivo<textarea name="motivo" required rows="3" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="Describe el motivo de la devolución al proveedor"></textarea></label>
                        <label class="mb-5 block text-xs font-bold uppercase tracking-wider text-slate-600">Comprobante / evidencia<input type="file" name="comprobante" accept="image/*,.pdf" class="mt-1.5 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm"></label>
                        <div class="flex justify-end gap-3 border-t border-slate-100 pt-4"><button type="button" onclick="this.closest('dialog').close()" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50">Cancelar</button><button type="submit" class="rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-black text-white hover:bg-amber-600"><i class="fas fa-truck-arrow-right mr-2"></i>Registrar devolución</button></div>
                    </form>
                </dialog>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($compras->hasPages()): ?> <div class="border-t border-slate-100 p-4"><?php echo e($compras->links()); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>

        <!-- MODAL ÚNICO DE DETALLE DE COMPRA CON SKELETON -->
        <div x-show="modalDetalleAbierto" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="cerrarDetalle()" class="w-[min(680px,calc(100vw-2rem))] max-h-[90vh] flex flex-col rounded-2xl border border-slate-200 bg-white p-0 text-left shadow-2xl overflow-hidden">
                
                <!-- Encabezado del modal -->
                <div class="p-5 border-b border-slate-100 flex items-start justify-between gap-4 bg-slate-50/50">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-blue-600">Detalle de recepción</p>
                        <template x-if="compraSeleccionada">
                            <p class="mt-1 text-sm font-bold text-slate-800">
                                <span x-text="compraSeleccionada.proveedor_nombre"></span> · <span x-text="compraSeleccionada.fecha_compra"></span>
                            </p>
                        </template>
                        <template x-if="!compraSeleccionada">
                            <div class="mt-2 h-4 w-48 rounded bg-slate-200 animate-pulse"></div>
                        </template>
                    </div>
                    <button type="button" @click="cerrarDetalle()" class="h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 flex items-center justify-center transition-colors" title="Cerrar">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                <!-- Skeleton de Carga adaptado a Compras para evitar Layout Shift -->
                <div x-show="cargandoDetalle" role="status" aria-label="Cargando compra" style="display:none;" class="p-5 overflow-y-auto space-y-4">
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 border border-slate-100">
                        <div class="h-4 w-32 rounded ax-skeleton-shimmer"></div>
                        <div class="h-6 w-24 rounded-md ax-skeleton-shimmer"></div>
                    </div>

                    <div class="h-3.5 w-3/4 rounded ax-skeleton-shimmer"></div>

                    <div class="space-y-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 3; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2">
                                <div class="h-14 w-14 shrink-0 rounded-lg ax-skeleton-shimmer"></div>
                                <div class="min-w-0 flex-1 space-y-1.5">
                                    <div class="h-4 w-40 rounded ax-skeleton-shimmer"></div>
                                    <div class="h-3 w-28 rounded ax-skeleton-shimmer"></div>
                                    <div class="h-2.5 w-16 rounded ax-skeleton-shimmer"></div>
                                </div>
                                <div class="shrink-0 space-y-1.5 text-right">
                                    <div class="h-3 w-20 rounded ax-skeleton-shimmer ml-auto"></div>
                                    <div class="h-4 w-16 rounded ax-skeleton-shimmer ml-auto"></div>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>

                    <div class="mt-4 border-t border-slate-100 pt-3 space-y-2">
                        <div class="h-3.5 w-36 rounded ax-skeleton-shimmer"></div>
                        <div class="h-4 w-full rounded ax-skeleton-shimmer"></div>
                    </div>
                </div>

                <!-- Contenido Dinámico -->
                <template x-if="!cargandoDetalle && compraSeleccionada">
                    <div class="p-5 overflow-y-auto space-y-4">
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 border border-slate-100">
                            <span class="text-xs font-bold text-slate-500">Total de la recepción</span>
                            <span class="text-xl font-black text-slate-900" x-text="'$' + Number(compraSeleccionada.total || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>
                        </div>

                        <p class="text-xs text-slate-500" x-text="compraSeleccionada.observaciones"></p>

                        <div class="space-y-2">
                            <template x-for="detalle in compraSeleccionada.detalles" :key="detalle.id">
                                <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2">
                                    <div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-white">
                                        <template x-if="detalle.imagen_url">
                                            <img :src="detalle.imagen_url" :alt="detalle.variante" class="h-full w-full object-cover">
                                        </template>
                                        <template x-if="!detalle.imagen_url">
                                            <div class="flex h-full items-center justify-center text-slate-300">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-xs font-black text-slate-800" x-text="detalle.producto"></p>
                                        <p class="truncate text-xs text-slate-500" x-text="detalle.variante"></p>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400" x-text="'SKU: ' + detalle.sku"></p>
                                    </div>
                                    <div class="shrink-0 text-right text-xs">
                                        <p class="font-bold text-slate-700" x-text="detalle.cantidad + ' × $' + Number(detalle.precio_unitario || 0).toFixed(2)"></p>
                                        <p class="font-black text-slate-900" x-text="'$' + Number(detalle.subtotal || 0).toFixed(2)"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-4 border-t border-slate-100 pt-3">
                            <div class="mb-2 flex items-center justify-between">
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Movimientos generados</p>
                                <a href="<?php echo e(route('compras.movimientos')); ?>" class="text-[10px] font-bold text-blue-600 hover:text-blue-800">Ver todos</a>
                            </div>
                            <template x-if="compraSeleccionada.movimientos && compraSeleccionada.movimientos.length > 0">
                                <div class="space-y-1">
                                    <template x-for="movimiento in compraSeleccionada.movimientos" :key="movimiento.id">
                                        <div class="flex items-center justify-between gap-3 text-xs text-slate-600 py-1">
                                            <span x-text="movimiento.tipo"></span>
                                            <span x-text="movimiento.cantidad + ' unidades · ' + movimiento.fecha"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!compraSeleccionada.movimientos || compraSeleccionada.movimientos.length === 0">
                                <p class="text-xs text-slate-400">Sin movimientos asociados.</p>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
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
<?php endif; ?><?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/compras/historial.blade.php ENDPATH**/ ?>