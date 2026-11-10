<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-100 bg-slate-50/80">
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">#</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Nombre</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:table-cell">Marca</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:table-cell">Categoría</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Variantes</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Estado</th>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->rol === 'admin'): ?>
                <th class="text-right px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Acciones</th>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <tr class="hover:bg-slate-50/60 transition-colors producto-row"
                data-id="<?php echo e($prod->id); ?>"
                data-nombre="<?php echo e(strtolower($prod->nombre)); ?>"
                data-categoria="<?php echo e(strtolower($prod->categoria->nombre ?? '')); ?>"
                data-estado="<?php echo e($prod->estado); ?>">
                
                <td class="px-6 py-4 text-slate-400 font-mono text-xs"><?php echo e($prod->id); ?></td>
                <td class="px-6 py-4 font-semibold text-slate-800"><?php echo e($prod->nombre); ?></td>
                <td class="px-6 py-4 hidden sm:table-cell text-slate-500 text-xs"><?php echo e($prod->marca ?? '—'); ?></td>
                <td class="px-6 py-4 hidden sm:table-cell text-slate-500 text-xs"><?php echo e($prod->categoria->nombre ?? '—'); ?></td>
                <td class="px-6 py-4 hidden md:table-cell">
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 bg-slate-100 rounded-full px-2.5 py-1">
                        <i class="fas fa-layer-group text-xs"></i>
                        <?php echo e($prod->variantes_count ?? 0); ?>

                    </span>
                </td>
                <td class="px-6 py-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->rol === 'admin'): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prod->estado == 1): ?>
                            <button type="button"
                                    class="btn-toggle-estado-producto inline-flex items-center gap-1.5 text-xs font-bold rounded-full px-2.5 py-1 transition-all hover:scale-105 active:scale-95 cursor-pointer border text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border-emerald-200"
                                    data-id="<?php echo e($prod->id); ?>"
                                    data-nombre="<?php echo e($prod->nombre); ?>"
                                    data-estado="1"
                                    title="Clic para desactivar producto">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="estado-label">Activo</span>
                            </button>
                        <?php else: ?>
                            <button type="button"
                                    class="btn-toggle-estado-producto inline-flex items-center gap-1.5 text-xs font-bold rounded-full px-2.5 py-1 transition-all hover:scale-105 active:scale-95 cursor-pointer border text-red-600 bg-red-50 hover:bg-red-100 border-red-200"
                                    data-id="<?php echo e($prod->id); ?>"
                                    data-nombre="<?php echo e($prod->nombre); ?>"
                                    data-estado="0"
                                    title="Clic para activar producto">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                <span class="estado-label">Inactivo</span>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prod->estado == 1): ?>
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-2.5 py-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Activo
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 bg-red-50 border border-red-200 rounded-full px-2.5 py-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactivo
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->rol === 'admin'): ?>
                <td class="px-6 py-4 text-right">
                    <div class="inline-flex items-center gap-2">
                        <a href="<?php echo e(route('productos.edit', $prod->id)); ?>"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors">
                            <i class="fas fa-pencil text-xs"></i> Editar / Variantes
                        </a>
                        <form data-loading-form data-loading-label="Eliminando..." action="<?php echo e(route('productos.destroy', $prod->id)); ?>" method="POST"
                                class="inline" id="form-delete-<?php echo e($prod->id); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="button"
                                    onclick="confirmarEliminar(<?php echo e($prod->id); ?>, '<?php echo e(addslashes($prod->nombre)); ?>', this)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
                                <i class="fas fa-trash text-xs"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </td>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <tr class="empty-row">
                <td colspan="<?php echo e(Auth::user()->rol === 'admin' ? 7 : 6); ?>" class="py-8">
                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'No hay productos registrados','description' => 'Comienza agregando un nuevo producto al catálogo general.','icon' => 'folder-open']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'No hay productos registrados','description' => 'Comienza agregando un nuevo producto al catálogo general.','icon' => 'folder-open']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->rol === 'admin'): ?>
                             <?php $__env->slot('action', null, []); ?> 
                                <a href="<?php echo e(route('productos.create')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">
                                    <i class="fas fa-plus"></i> Crear el primero
                                </a>
                             <?php $__env->endSlot(); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
            <tr id="table-no-results" class="hidden">
                <td colspan="<?php echo e(Auth::user()->rol === 'admin' ? 7 : 6); ?>" class="py-8">
                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'No se encontraron productos','description' => 'Intenta ajustar el término de búsqueda, categoría o estado seleccionado.','icon' => 'search']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'No se encontraron productos','description' => 'Intenta ajustar el término de búsqueda, categoría o estado seleccionado.','icon' => 'search']); ?>
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
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/productos/partials/table.blade.php ENDPATH**/ ?>