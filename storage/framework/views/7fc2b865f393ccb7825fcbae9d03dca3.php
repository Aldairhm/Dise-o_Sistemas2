<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
<?php
    $isActivo = $prod->estado == 1;
    $delay    = $loop->index * 50;
    $imgUrl   = $prod->imagen_principal 
        ? (str_starts_with($prod->imagen_principal, 'http') ? $prod->imagen_principal : \Illuminate\Support\Facades\Storage::disk('s3')->url($prod->imagen_principal)) 
        : null;
?>

<div class="producto-card group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 relative flex flex-col border border-slate-200/60 <?php echo e(!$isActivo ? 'opacity-70' : ''); ?>"
     data-id="<?php echo e($prod->id); ?>"
     data-nombre="<?php echo e(strtolower($prod->nombre)); ?>"
     data-categoria="<?php echo e(strtolower($prod->categoria->nombre ?? '')); ?>"
     data-estado="<?php echo e($prod->estado); ?>"
     style="animation: cardFadeIn 0.4s ease both; animation-delay: <?php echo e($delay); ?>ms; border-top: 4px solid <?php echo e($isActivo ? '#10b981' : '#ef4444'); ?>;">

    <div class="p-5 flex-1 flex flex-col">
        
        <div class="flex items-start gap-4 mb-4">
            
            <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm bg-slate-100 overflow-hidden <?php echo e(!$isActivo ? 'grayscale' : ''); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imgUrl): ?>
                    <img src="<?php echo e($imgUrl); ?>" alt="<?php echo e($prod->nombre); ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <i class="fas fa-box text-slate-300 text-2xl"></i>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="flex-1 min-w-0 pt-0.5">
                <h3 class="font-bold text-slate-800 text-sm leading-tight line-clamp-2 mb-1.5 <?php echo e(!$isActivo ? 'text-slate-400' : ''); ?>"
                    title="<?php echo e($prod->nombre); ?>">
                    <?php echo e($prod->nombre); ?>

                </h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->rol === 'admin'): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActivo): ?>
                        <button type="button"
                                class="btn-toggle-estado-producto inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-100 cursor-pointer transition-transform hover:scale-105 active:scale-95"
                                data-id="<?php echo e($prod->id); ?>"
                                data-nombre="<?php echo e($prod->nombre); ?>"
                                data-estado="1"
                                title="Clic para desactivar producto">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="estado-label">Activo</span>
                        </button>
                    <?php else: ?>
                        <button type="button"
                                class="btn-toggle-estado-producto inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 hover:bg-red-100 border border-red-100 cursor-pointer transition-transform hover:scale-105 active:scale-95"
                                data-id="<?php echo e($prod->id); ?>"
                                data-nombre="<?php echo e($prod->nombre); ?>"
                                data-estado="0"
                                title="Clic para activar producto">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            <span class="estado-label">Inactivo</span>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActivo): ?>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Activo
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 border border-red-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactivo
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="mt-auto space-y-2">
            
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-widest"><?php echo e($prod->categoria->nombre ?? 'Sin categoría'); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prod->marca): ?>
                    <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-1.5 py-0.5 rounded uppercase border border-orange-100"><?php echo e($prod->marca); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="flex items-center justify-between text-sm text-slate-600">
                <span class="font-medium">Variantes:</span>
                <span class="inline-flex items-center gap-1 font-bold bg-slate-100 rounded px-1.5 py-0.5 text-xs">
                    <i class="fas fa-layer-group text-slate-400"></i> <?php echo e($prod->variantes_count ?? 0); ?>

                </span>
            </div>
        </div>
    </div>

    
    <div class="px-5"><hr class="border-slate-100"></div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->rol === 'admin'): ?>
    <div class="p-4 flex flex-wrap items-center justify-end gap-2 bg-slate-50/30 rounded-b-xl">
        
        <a href="<?php echo e(route('productos.edit', $prod->id)); ?>"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold text-xs transition-colors border border-blue-200">
            <i class="fas fa-pen-to-square"></i> Editar
        </a>

        
        <form action="<?php echo e(route('productos.destroy', $prod->id)); ?>" method="POST" class="inline" id="form-delete-card-<?php echo e($prod->id); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="button"
                    onclick="confirmarEliminar(<?php echo e($prod->id); ?>, '<?php echo e(addslashes($prod->nombre)); ?>')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-xs transition-colors border border-rose-200"
                    title="Eliminar Producto">
                <i class="fas fa-trash-can"></i> Eliminar
            </button>
        </form>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<div class="col-span-full flex flex-col items-center justify-center py-20 text-center empty-cards">
    <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-400 flex items-center justify-center text-2xl mb-4">
        <i class="fas fa-box-open"></i>
    </div>
    <h3 class="text-base font-bold text-slate-700 mb-1">No hay productos registrados</h3>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->rol === 'admin'): ?>
    <a href="<?php echo e(route('productos.create')); ?>" class="text-sm font-bold text-blue-600 hover:underline">
        + Crear el primero
    </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div id="cards-no-results" class="col-span-full hidden flex-col items-center justify-center py-20 text-center">
    <i class="fas fa-search text-4xl mb-3 block text-slate-300"></i>
    <p class="font-semibold text-sm text-slate-500">No se encontraron productos con estos filtros.</p>
</div>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/productos/partials/cards.blade.php ENDPATH**/ ?>