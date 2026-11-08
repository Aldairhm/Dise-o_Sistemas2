<div class="overflow-x-auto">
    <table class="w-full text-sm" id="tablaVariantes">
        <thead>
            <tr class="border-b border-slate-100 bg-slate-50/80">
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Imagen</th>
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">SKU</th>
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Variante</th>
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Precio</th>
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Stock</th>
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Reserva</th>
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Estado</th>
                <th class="text-right px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $producto->variantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <tr class="hover:bg-slate-50/60 transition-colors variante-row" 
                data-id="<?php echo e($variante->id); ?>"
                data-nombre="<?php echo e(strtolower($variante->nombre_variante)); ?>"
                data-sku="<?php echo e(strtolower($variante->sku ?? '')); ?>"
                data-estado="<?php echo e($variante->estado); ?>">
                <td class="px-6 py-3">
                    <?php
                        $imgP = $variante->imagenes->where('es_principal', 1)->first() ?? $variante->imagenes->first();
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imgP): ?>
                        <img src="<?php echo e(str_starts_with($imgP->ruta_imagen, 'http') ? $imgP->ruta_imagen : \Illuminate\Support\Facades\Storage::disk('s3')->url($imgP->ruta_imagen)); ?>" alt="img"
                                class="w-10 h-10 rounded-lg object-cover border border-slate-200">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300">
                            <i class="fas fa-image text-sm"></i>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>
                <td class="px-6 py-3 font-mono text-xs text-slate-500"><?php echo e($variante->sku ?? '—'); ?></td>
                <td class="px-6 py-3 font-semibold text-slate-800"><?php echo e($variante->nombre_variante); ?></td>
                <td class="px-6 py-3 font-bold text-slate-800">$<?php echo e(number_format($variante->precio_venta, 2)); ?></td>
                <td class="px-6 py-3">
                    <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full
                        <?php echo e($variante->stock > 10 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                            : ($variante->stock > 0  ? 'bg-amber-50 text-amber-700 border border-amber-200'
                                                    : 'bg-red-50 text-red-600 border border-red-200')); ?>">
                        <?php echo e($variante->stock); ?>

                    </span>
                </td>
                <td class="px-6 py-3 hidden md:table-cell text-slate-500 text-xs"><?php echo e($variante->reserva); ?></td>
                <td class="px-6 py-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variante->estado == 1): ?>
                        <button type="button"
                                class="btn-toggle-estado-variante inline-flex items-center gap-1.5 text-xs font-bold rounded-full px-2.5 py-1 transition-all hover:scale-105 active:scale-95 cursor-pointer border text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border-emerald-200"
                                data-id="<?php echo e($variante->id); ?>"
                                data-nombre="<?php echo e($variante->nombre_variante); ?>"
                                data-estado="1"
                                title="Clic para desactivar variante">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="estado-label">Activo</span>
                        </button>
                    <?php else: ?>
                        <button type="button"
                                class="btn-toggle-estado-variante inline-flex items-center gap-1.5 text-xs font-bold rounded-full px-2.5 py-1 transition-all hover:scale-105 active:scale-95 cursor-pointer border text-red-600 bg-red-50 hover:bg-red-100 border-red-200"
                                data-id="<?php echo e($variante->id); ?>"
                                data-nombre="<?php echo e($variante->nombre_variante); ?>"
                                data-estado="0"
                                title="Clic para activar variante">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            <span class="estado-label">Inactivo</span>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>
                <td class="px-6 py-3 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button"
                                class="btn-duplicar-variante p-2 text-xs font-bold text-violet-600 bg-violet-50 hover:bg-violet-100 border border-violet-200 rounded-lg transition-colors"
                                data-id="<?php echo e($variante->id); ?>" title="Duplicar">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button type="button"
                                class="btn-editar-variante p-2 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors"
                                data-id="<?php echo e($variante->id); ?>" title="Editar">
                            <i class="fas fa-pencil"></i>
                        </button>
                        <button type="button"
                                class="btn-eliminar-variante p-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors"
                                data-id="<?php echo e($variante->id); ?>" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <tr id="rowNoVariantes">
                <td colspan="8" class="py-6">
                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'Este producto aún no tiene variantes','description' => 'Usa el botón \'Agregar Variante\' para comenzar a registrar opciones de stock y precio.','icon' => 'folder-open']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Este producto aún no tiene variantes','description' => 'Usa el botón \'Agregar Variante\' para comenzar a registrar opciones de stock y precio.','icon' => 'folder-open']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                </td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <tr id="table-no-results-variantes" class="hidden">
                <td colspan="8" class="py-6">
                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'No se encontraron variantes','description' => 'Intenta ajustar el término de búsqueda o el filtro de estado.','icon' => 'search']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'No se encontraron variantes','description' => 'Intenta ajustar el término de búsqueda o el filtro de estado.','icon' => 'search']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/productos/partials/table_variantes.blade.php ENDPATH**/ ?>