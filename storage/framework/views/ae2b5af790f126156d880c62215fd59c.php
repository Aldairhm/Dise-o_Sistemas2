<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Devoluciones a proveedores | AXStore']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Devoluciones a proveedores | AXStore']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="mx-auto max-w-[1440px] space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="<?php echo e(route('compras.historial')); ?>" class="mb-4 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600"><i class="fas fa-arrow-left"></i> Volver a compras</a>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-amber-600">Módulo de Compras</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">Devoluciones a proveedores</h1>
                <p class="mt-2 text-sm text-slate-500">Revisa los tickets pendientes y aplica su resolución.</p>
            </div>
            <div class="flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 shadow-sm"><i class="fas fa-ticket text-amber-600"></i><span class="text-sm font-bold text-amber-800"><?php echo e($devoluciones->total()); ?> tickets</span></div>
        </div>

        <nav class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" aria-label="Secciones de compras">
            <a href="<?php echo e(route('compras.create')); ?>" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-cart-plus mr-2"></i>Nueva compra</a>
            <a href="<?php echo e(route('compras.historial')); ?>" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-clock-rotate-left mr-2"></i>Historial</a>
            <a href="<?php echo e(route('compras.movimientos')); ?>" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-warehouse mr-2"></i>Gestión de Inventario</a>
            <a href="<?php echo e(route('compras.devoluciones')); ?>" class="rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm"><i class="fas fa-truck-arrow-right mr-2"></i>Devoluciones a proveedores</a>
        </nav>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4"><h2 class="text-base font-black text-slate-900">Tickets registrados</h2><p class="mt-1 text-xs text-slate-500">El inventario y el monto solo cambian al resolver un ticket pendiente.</p></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1100px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-white text-[10px] font-black uppercase tracking-widest text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Ticket / fecha</th>
                            <th class="px-5 py-4">Compra</th>
                            <th class="px-5 py-4">Ubicación</th>
                            <th class="px-5 py-4">Motivo</th>
                            <th class="px-5 py-4">Estado / Resolución</th>
                            <th class="px-5 py-4 text-right">Monto</th>
                            <th class="px-5 py-4 text-right">Evidencia</th>
                            <th class="px-5 py-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $devoluciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $devolucion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-5 py-4"><p class="font-mono text-xs font-black text-slate-800">DEV-<?php echo e(str_pad($devolucion->id, 5, '0', STR_PAD_LEFT)); ?></p><p class="mt-1 text-xs text-slate-500"><?php echo e(optional($devolucion->created_at)->format('d/m/Y H:i')); ?></p></td>
                                <td class="px-5 py-4"><span class="inline-flex rounded-lg border border-violet-200 bg-violet-50 px-2.5 py-1.5 text-xs font-bold text-violet-700"><i class="fas fa-cart-shopping mr-1.5"></i>Compra #<?php echo e($devolucion->origen_id); ?></span></td>
                                <!-- UBICACIÓN (Leyendo desde detalles_json) -->
<td class="px-5 py-4 max-w-[150px]">
    <?php
        $ubicacionesUnicas = [];
        $detallesDev = is_array($devolucion->detalles_json) 
            ? $devolucion->detalles_json 
            : json_decode($devolucion->detalles_json ?? '[]', true);

        if (is_array($detallesDev)) {
            foreach ($detallesDev as $prod) {
                if (isset($prod['ubicaciones']['stock']) && $prod['ubicaciones']['stock'] > 0) $ubicacionesUnicas['stock'] = true;
                if (isset($prod['ubicaciones']['reserva']) && $prod['ubicaciones']['reserva'] > 0) $ubicacionesUnicas['reserva'] = true;
                if (isset($prod['ubicaciones']['cuarentena']) && $prod['ubicaciones']['cuarentena'] > 0) $ubicacionesUnicas['cuarentena'] = true;
            }
        }
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($ubicacionesUnicas) > 0): ?>
        <div class="flex flex-wrap gap-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($ubicacionesUnicas['stock'])): ?>
                <span class="rounded-md border border-blue-200 bg-blue-50 px-2 py-1 text-[10px] font-bold text-blue-700">Tienda</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($ubicacionesUnicas['reserva'])): ?>
                <span class="rounded-md border border-amber-200 bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-700">Reserva</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($ubicacionesUnicas['cuarentena'])): ?>
                <span class="rounded-md border border-rose-200 bg-rose-50 px-2 py-1 text-[10px] font-bold text-rose-700">Cuarentena</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php else: ?>
        <span class="rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-bold text-slate-400">Sin ubicación</span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</td>
                                <td class="max-w-[250px] px-5 py-4"><span class="block truncate text-xs text-slate-600" title="<?php echo e($devolucion->motivo); ?>"><?php echo e(\Illuminate\Support\Str::limit($devolucion->motivo, 60)); ?></span></td>
                                <td class="px-5 py-4">
    <span class="rounded-lg border px-2.5 py-1.5 text-xs font-bold <?php echo e($devolucion->estado === 'pendiente' ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'); ?>">
        <?php echo e(ucfirst($devolucion->estado)); ?>

    </span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($devolucion->estado === 'resuelto' && $devolucion->tipo_resolucion): ?>
        <p class="mt-2.5 text-[10px] font-black uppercase tracking-wider text-slate-500">
            <i class="fas fa-turn-down text-slate-300 mr-1"></i> 
            <?php echo e(str_replace('_', ' ', $devolucion->tipo_resolucion)); ?>

        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</td>
                                
                                <!-- LÓGICA DE MONTO ACTUALIZADA -->
                                <td class="px-5 py-4 text-right">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($devolucion->estado !== 'pendiente'): ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($devolucion->tipo_resolucion === 'merma'): ?>
                                            <span class="inline-flex items-center gap-1 rounded-lg bg-rose-50 px-2.5 py-1 text-sm font-black text-rose-600 border border-rose-100">
                                                <i class="fas fa-minus text-[10px]"></i> $<?php echo e(number_format($devolucion->monto_reembolsado ?? 0, 2)); ?>

                                            </span>
                                        <?php elseif($devolucion->tipo_resolucion === 'reembolso'): ?>
                                            <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-1 text-sm font-black text-emerald-600 border border-emerald-100">
                                                <i class="fas fa-plus text-[10px]"></i> $<?php echo e(number_format($devolucion->monto_reembolsado ?? 0, 2)); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-sm font-black text-slate-600">
                                                $<?php echo e(number_format($devolucion->monto_reembolsado ?? 0, 2)); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-sm font-black text-slate-800">
                                            $<?php echo e(number_format($devolucion->monto_reembolsado ?? 0, 2)); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <!-- FIN DE LÓGICA DE MONTO -->

                                <td class="px-5 py-4 text-right"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($devolucion->comprobante): ?><a href="<?php echo e(str_starts_with($devolucion->comprobante, 'http') ? $devolucion->comprobante : \Illuminate\Support\Facades\Storage::disk('s3')->url($devolucion->comprobante)); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 hover:bg-blue-600 hover:text-white"><i class="fas fa-paperclip"></i> Ver archivo</a><?php else: ?><span class="text-xs text-slate-400">Sin evidencia</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                                <td class="px-5 py-4 text-right">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($devolucion->estado === 'pendiente'): ?>
                                        <button type="button" onclick="document.getElementById('resolver-proveedor-<?php echo e($devolucion->id); ?>').showModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-black text-white hover:bg-amber-600"><i class="fas fa-check"></i> Resolver</button>
                                        <dialog id="resolver-proveedor-<?php echo e($devolucion->id); ?>" class="m-auto w-[min(440px,calc(100vw-2rem))] rounded-2xl border border-slate-200 bg-white p-0 text-left shadow-2xl backdrop:bg-slate-900/50">
                                            <form method="POST" action="<?php echo e(route('devoluciones.compra.resolver', $devolucion->id)); ?>" class="p-5">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                                <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                                                    <div>
                                                        <p class="text-xs font-black uppercase tracking-widest text-amber-600">Resolver ticket</p>
                                                        <h3 class="text-lg font-black text-slate-900">DEV-<?php echo e(str_pad($devolucion->id, 5, '0', STR_PAD_LEFT)); ?></h3>
                                                    </div>
                                                    <button type="button" onclick="this.closest('dialog').close()" class="h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-100"><i class="fas fa-xmark"></i></button>
                                                </div>
                                                <p class="mb-4 text-xs text-slate-500">La falla está registrada en <strong><?php echo e($devolucion->ubicacion_falla); ?></strong>.</p>
                                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Resolución final
                                                    <select name="resolucion_final" required class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                                        <option value="" disabled selected>Selecciona una opción</option>
                                                        <option value="cambio_fisico">Cambio físico</option>
                                                        <option value="reembolso">Reembolso</option>
                                                        <option value="merma">Merma / pérdida</option>
                                                    </select>
                                                </label>
                                                <div class="mt-5 flex justify-end gap-2">
                                                    <button type="button" onclick="this.closest('dialog').close()" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50">Cancelar</button>
                                                    <button type="submit" class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-black text-white hover:bg-amber-600">Aplicar resolución</button>
                                                </div>
                                            </form>
                                        </dialog>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-slate-100 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-400">
                                            <i class="fas fa-lock text-slate-300"></i> Resuelto
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="8" class="px-5 py-16 text-center">
                                    <i class="fas fa-ticket mb-3 block text-2xl text-slate-300"></i>
                                    <p class="text-sm font-bold text-slate-700">No hay tickets de proveedores</p>
                                    <p class="mt-1 text-xs text-slate-400">Créalo desde Historial de compras con el botón Devolver.</p>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($devoluciones->hasPages()): ?><div class="border-t border-slate-100 bg-slate-50/50 p-4"><?php echo e($devoluciones->links()); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>
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
<?php endif; ?><?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/compras/devoluciones.blade.php ENDPATH**/ ?>