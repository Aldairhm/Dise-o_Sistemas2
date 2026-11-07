<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categorias->isEmpty()): ?>
    <div class="flex flex-col items-center justify-center py-20 text-center" id="emptyState">
        <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mb-5 text-slate-300 text-4xl">
            <i class="fas fa-tag"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-700 mb-1">
            <?php echo e($estado === 'inactivos' ? 'Sin categorías inactivas' : 'Sin categorías'); ?>

        </h3>
        <p class="text-sm text-slate-400">
            <?php echo e($estado === 'inactivos' ? 'No hay categorías inactivas en este momento.' : 'No se encontraron categorías. Crea una nueva para comenzar.'); ?>

        </p>
    </div>
<?php else: ?>
    <div class="overflow-x-auto w-full">
        <table class="w-full text-sm min-w-[600px]" id="categoriasTable">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/50">
                    <th class="w-10"></th>
                    <th class="text-left px-4 sm:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">#</th>
                    <th class="text-left px-4 sm:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Categoría</th>
                    <th class="text-left px-4 sm:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Descripción</th>
                    <th class="text-left px-4 sm:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:table-cell">Productos</th>
                    <th class="text-left px-4 sm:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden lg:table-cell">Estado</th>
                    <th class="text-right px-4 sm:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Acciones</th>
                </tr>
            </thead>
            <tbody id="categoriasBody">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $color   = $categoria->color ?? '#3b82f6';
                    $icono   = $categoria->icono ?? 'fa-tag';
                    $isBaja  = !$categoria->estado;
                    $tieneProductos = ($categoria->productos_count ?? 0) > 0;
                ?>
                <tr class="border-b border-slate-50 transition-colors categoria-row <?php echo e($isBaja ? 'bg-slate-50/40 opacity-70 hover:opacity-90' : 'hover:bg-slate-50/60'); ?>"
                    data-id="<?php echo e($categoria->id); ?>"
                    data-nombre="<?php echo e(strtolower($categoria->nombre)); ?>"
                    data-desc="<?php echo e(strtolower($categoria->descripcion ?? '')); ?>">

                    <td class="w-10 text-center drag-handle text-slate-300 hover:text-slate-500">
                        <i class="fas fa-grip-vertical"></i>
                    </td>

                    <td class="px-4 sm:px-6 py-4 text-slate-400 font-mono text-xs"><?php echo e($loop->iteration); ?></td>

                    <td class="px-4 sm:px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm flex-shrink-0 transition-transform <?php echo e($isBaja ? '' : 'hover:scale-110'); ?>"
                                 style="background-color: <?php echo e($color); ?><?php echo e($isBaja ? '18' : '20'); ?>; color: <?php echo e($isBaja ? '#94a3b8' : $color); ?>;">
                                <i class="fas <?php echo e($icono); ?>"></i>
                            </div>
                            <div>
                                <span class="font-bold <?php echo e($isBaja ? 'text-slate-400 line-through' : 'text-slate-800'); ?>"><?php echo e($categoria->nombre); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isBaja): ?>
                                    <span class="inline-block w-1.5 h-1.5 rounded-full ml-1.5 mb-0.5 align-middle"
                                          style="background-color: <?php echo e($color); ?>;"></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </td>

                    <td class="px-4 sm:px-6 py-4 hidden md:table-cell">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categoria->descripcion): ?>
                            <p class="text-slate-500 text-sm line-clamp-2 max-w-xs <?php echo e($isBaja ? 'line-through text-slate-400' : ''); ?>"><?php echo e($categoria->descripcion); ?></p>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs italic">Sin descripción</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>

                    <td class="px-4 sm:px-6 py-4 hidden sm:table-cell">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                            <i class="fas fa-box text-slate-400"></i>
                            <?php echo e($categoria->productos_count ?? 0); ?>

                        </span>
                    </td>

                    <td class="px-4 sm:px-6 py-4 hidden lg:table-cell">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isBaja): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Inactiva
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Activa
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>

                    <td class="px-4 sm:px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <!-- Editar -->
                            <button type="button"
                                    onclick="openEditModal(<?php echo e($categoria->id); ?>, '<?php echo e(addslashes($categoria->nombre)); ?>', '<?php echo e(addslashes($categoria->descripcion ?? '')); ?>', '<?php echo e($color); ?>', '<?php echo e($icono); ?>', <?php echo e((int)($categoria->productos_count ?? 0)); ?>)"
                                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-600 flex items-center justify-center transition-colors cursor-pointer"
                                    title="Editar categoría">
                                <i class="fas fa-pencil text-xs"></i>
                            </button>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isBaja): ?>
                                <!-- Habilitar -->
                                <button type="button"
                                        onclick="toggleStatus(<?php echo e($categoria->id); ?>, '<?php echo e(addslashes($categoria->nombre)); ?>', 1)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 font-bold text-xs transition-colors cursor-pointer border border-emerald-100"
                                        title="Habilitar categoría">
                                    <i class="fas fa-check text-xs"></i>
                                    <span class="hidden sm:inline">Habilitar</span>
                                </button>
                            <?php else: ?>
                                <!-- Deshabilitar -->
                                <button type="button"
                                        onclick="toggleStatus(<?php echo e($categoria->id); ?>, '<?php echo e(addslashes($categoria->nombre)); ?>', 0)"
                                        class="w-8 h-8 rounded-lg <?php echo e($tieneProductos ? 'bg-slate-100 text-slate-300 cursor-not-allowed' : 'bg-slate-100 hover:bg-amber-100 text-slate-500 hover:text-amber-600 cursor-pointer'); ?> flex items-center justify-center transition-colors"
                                        title="<?php echo e($tieneProductos ? 'No se puede deshabilitar: tiene productos asociados' : 'Deshabilitar'); ?>"
                                        <?php echo e($tieneProductos ? 'disabled' : ''); ?>>
                                    <i class="fas fa-ban text-xs"></i>
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer de la tabla -->
    <div class="px-4 sm:px-6 py-3 bg-slate-50/60 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-center sm:text-left">
        <span class="text-xs text-slate-400 font-medium">
            Mostrando <span class="font-bold text-slate-600"><?php echo e($categorias->count()); ?></span> categoría(s)
        </span>
        <div class="flex flex-col sm:flex-row items-center gap-1 sm:gap-3">
            <span class="text-xs text-slate-400"><i class="fas fa-info-circle"></i> Arrastra desde el borde para reordenar</span>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/categorias/partials/table.blade.php ENDPATH**/ ?>