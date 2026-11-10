<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categorias->isEmpty()): ?>
    <div class="flex flex-col items-center justify-center py-20 text-center col-span-full">
        <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mb-5 text-slate-300 text-4xl">
            <i class="fas fa-tag"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-700 mb-1">
            <?php echo e($estado === 'inactivos' ? 'Sin categorías inactivas' : 'Sin categorías'); ?>

        </h3>
        <p class="text-sm text-slate-400">
            <?php echo e($estado === 'inactivos' ? 'No hay categorías inactivas en este momento.' : 'No se encontraron categorías activas.'); ?>

        </p>
    </div>
<?php else: ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <?php
        $color  = $categoria->color ?? '#3b82f6';
        $icono  = $categoria->icono ?? 'fa-tag';
        $isBaja = !$categoria->estado;
        $delay  = $loop->index * 50;
        $tieneProductos = ($categoria->productos_count ?? 0) > 0;
    ?>

    <div class="categoria-card drag-handle group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 relative flex flex-col border border-slate-200/60 <?php echo e($isBaja ? 'opacity-75 grayscale-[20%]' : ''); ?>"
         data-id="<?php echo e($categoria->id); ?>"
         data-nombre="<?php echo e(strtolower($categoria->nombre)); ?>"
         data-desc="<?php echo e(strtolower($categoria->descripcion ?? '')); ?>"
         style="animation: cardFadeIn 0.4s ease both; animation-delay: <?php echo e($delay); ?>ms; border-left: 4px solid <?php echo e($color); ?>;">

        <div class="p-5 flex-1">
            <!-- Header de la tarjeta (Ícono + Título + Badge) -->
            <div class="flex items-start gap-4">
                <!-- Ícono circular -->
                <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
                     style="background-color: <?php echo e($color); ?>15; color: <?php echo e($color); ?>;">
                    <i class="fas <?php echo e($icono); ?> text-lg"></i>
                </div>

                <!-- Título y Estado -->
                <div class="flex-1 min-w-0 pt-0.5">
                    <h3 class="font-bold text-slate-800 text-lg leading-tight truncate mb-1.5 <?php echo e($isBaja ? 'text-slate-500 line-through' : ''); ?>" title="<?php echo e($categoria->nombre); ?>">
                        <?php echo e($categoria->nombre); ?>

                    </h3>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isBaja): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-600">
                            Habilitado
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-500">
                            Deshabilitado
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Detalles (Descripción, Productos y Color) -->
            <div class="mt-5 space-y-3">
                <!-- Descripción -->
                <div class="flex items-start gap-3">
                    <div class="w-5 flex justify-center mt-0.5">
                        <i class="fas fa-align-left text-slate-400 text-sm"></i>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categoria->descripcion): ?>
                        <p class="text-sm text-slate-600 line-clamp-2 <?php echo e($isBaja ? 'line-through text-slate-400' : ''); ?>" title="<?php echo e($categoria->descripcion); ?>">
                            <?php echo e($categoria->descripcion); ?>

                        </p>
                    <?php else: ?>
                        <p class="text-sm text-slate-400 italic">Sin descripción general.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                
                <!-- Contador de Productos -->
                <div class="flex items-center gap-3">
                    <div class="w-5 flex justify-center">
                        <i class="fas fa-box text-slate-400 text-sm"></i>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-slate-700"><?php echo e($categoria->productos_count ?? 0); ?></span>
                        <span class="text-xs text-slate-500">productos</span>
                    </div>
                </div>

                <!-- Código de color (Opcional, lo ocultaremos si no hay espacio, pero lo dejamos por estética) -->
                <div class="flex items-center gap-3">
                    <div class="w-5 flex justify-center">
                        <i class="fas fa-palette text-slate-400 text-sm"></i>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-600 font-mono"><?php echo e(strtoupper($color)); ?></span>
                        <span class="w-3.5 h-3.5 rounded-full border border-slate-200" style="background-color: <?php echo e($color); ?>;"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divisor -->
        <div class="px-5">
            <hr class="border-slate-100">
        </div>

        <!-- Botones de Acción -->
        <div class="p-4 flex items-center justify-end gap-2 bg-slate-50/30 rounded-b-xl">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isBaja): ?>
                <button type="button" 
                        onclick="openEditModal(<?php echo e($categoria->id); ?>, '<?php echo e(addslashes($categoria->nombre)); ?>', '<?php echo e(addslashes($categoria->descripcion ?? '')); ?>', '<?php echo e($color); ?>', '<?php echo e($icono); ?>', <?php echo e((int)($categoria->productos_count ?? 0)); ?>)"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold text-xs transition-colors cursor-pointer">
                    <i class="fas fa-edit"></i>
                    <span>Editar</span>
                </button>

                <button type="button"
                        onclick="toggleStatus(<?php echo e($categoria->id); ?>, '<?php echo e(addslashes($categoria->nombre)); ?>', 0, this)"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md <?php echo e($tieneProductos ? 'bg-slate-100 text-slate-300 cursor-not-allowed' : 'bg-red-50 hover:bg-red-100 text-red-600 cursor-pointer'); ?> font-semibold text-xs transition-colors"
                        title="<?php echo e($tieneProductos ? 'No se puede deshabilitar: tiene productos asociados' : 'Deshabilitar categoría'); ?>"
                        <?php echo e($tieneProductos ? 'disabled' : ''); ?>>
                    <i class="fas fa-ban"></i>
                    <span>Deshabilitar</span>
                </button>
            <?php else: ?>
                <button type="button"
                        onclick="toggleStatus(<?php echo e($categoria->id); ?>, '<?php echo e(addslashes($categoria->nombre)); ?>', 1, this)"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-600 font-semibold text-xs transition-colors cursor-pointer">
                    <i class="fas fa-check"></i>
                    <span>Habilitar</span>
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/categorias/partials/cards.blade.php ENDPATH**/ ?>