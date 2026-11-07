<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $producto->variantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
<?php
    $isActivo = $variante->estado == 1;
    $delay    = $loop->index * 50;
    $imgP = $variante->imagenes->where('es_principal', 1)->first() ?? $variante->imagenes->first();
    $imgUrl = $imgP 
        ? (str_starts_with($imgP->ruta_imagen, 'http') ? $imgP->ruta_imagen : \Illuminate\Support\Facades\Storage::disk('s3')->url($imgP->ruta_imagen)) 
        : null;
?>

<div class="variante-card group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 relative flex flex-col border border-slate-200/60 <?php echo e(!$isActivo ? 'opacity-70' : ''); ?>"
     data-id="<?php echo e($variante->id); ?>"
     data-nombre="<?php echo e(strtolower($variante->nombre_variante)); ?>"
     data-sku="<?php echo e(strtolower($variante->sku ?? '')); ?>"
     data-estado="<?php echo e($variante->estado); ?>"
     style="animation: cardFadeIn 0.4s ease both; animation-delay: <?php echo e($delay); ?>ms; border-left: 4px solid <?php echo e($isActivo ? '#10b981' : '#ef4444'); ?>;">

    <div class="p-4 flex-1 flex flex-col">
        
        <div class="flex items-start gap-4 mb-4">
            
            <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm bg-slate-100 overflow-hidden <?php echo e(!$isActivo ? 'grayscale' : ''); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imgUrl): ?>
                    <img src="<?php echo e($imgUrl); ?>" alt="img" class="w-full h-full object-cover">
                <?php else: ?>
                    <i class="fas fa-image text-slate-300 text-2xl"></i>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="flex-1 min-w-0 pt-0.5">
                <h3 class="font-bold text-slate-800 text-sm leading-tight line-clamp-1 mb-1 <?php echo e(!$isActivo ? 'text-slate-400' : ''); ?>"
                    title="<?php echo e($variante->nombre_variante); ?>">
                    <?php echo e($variante->nombre_variante); ?>

                </h3>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActivo): ?>
                        <button type="button"
                                class="btn-toggle-estado-variante inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-100 cursor-pointer transition-transform hover:scale-105 active:scale-95"
                                data-id="<?php echo e($variante->id); ?>"
                                data-nombre="<?php echo e($variante->nombre_variante); ?>"
                                data-estado="1"
                                title="Clic para desactivar variante">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="estado-label">Activo</span>
                        </button>
                    <?php else: ?>
                        <button type="button"
                                class="btn-toggle-estado-variante inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 hover:bg-red-100 border border-red-100 cursor-pointer transition-transform hover:scale-105 active:scale-95"
                                data-id="<?php echo e($variante->id); ?>"
                                data-nombre="<?php echo e($variante->nombre_variante); ?>"
                                data-estado="0"
                                title="Clic para activar variante">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            <span class="estado-label">Inactivo</span>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span class="font-mono text-[10px] text-slate-400 uppercase bg-slate-50 px-1.5 py-0.5 rounded"><?php echo e($variante->sku ?? 'NO SKU'); ?></span>
                </div>
            </div>
        </div>

        
        <div class="mt-auto grid grid-cols-2 gap-3 text-sm">
            <div class="bg-slate-50 rounded-lg p-2 text-center border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Precio</span>
                <span class="font-bold text-slate-800">$<?php echo e(number_format($variante->precio_venta, 2)); ?></span>
            </div>
            <div class="bg-slate-50 rounded-lg p-2 text-center border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Stock / Rsv</span>
                <span class="font-bold <?php echo e($variante->stock > 0 ? 'text-emerald-600' : 'text-red-600'); ?>"><?php echo e($variante->stock); ?></span>
                <span class="text-slate-300 mx-0.5">|</span>
                <span class="text-slate-500"><?php echo e($variante->reserva); ?></span>
            </div>
        </div>
    </div>

    
    <div class="px-4"><hr class="border-slate-100"></div>

    
    <div class="p-3 flex flex-wrap items-center justify-end gap-1.5 bg-slate-50/30 rounded-b-xl">
        <button type="button"
                class="btn-duplicar-variante flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md bg-violet-50 hover:bg-violet-100 text-violet-600 font-semibold text-xs transition-colors border border-violet-200"
                data-id="<?php echo e($variante->id); ?>" title="Duplicar">
            <i class="fas fa-copy"></i> Duplicar
        </button>
        <button type="button"
                class="btn-editar-variante flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold text-xs transition-colors border border-blue-200"
                data-id="<?php echo e($variante->id); ?>" title="Editar">
            <i class="fas fa-pencil"></i> Editar
        </button>
        <button type="button"
                class="btn-eliminar-variante inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-xs transition-colors border border-rose-200"
                data-id="<?php echo e($variante->id); ?>" title="Eliminar">
            <i class="fas fa-trash"></i>
        </button>
    </div>
</div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<div class="col-span-full flex flex-col items-center justify-center py-12 text-center empty-cards-variantes" id="rowNoVariantesCards">
    <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-400 flex items-center justify-center text-2xl mb-4">
        <i class="fas fa-layer-group"></i>
    </div>
    <h3 class="text-base font-bold text-slate-700 mb-1">Este producto aún no tiene variantes</h3>
    <p class="text-xs mt-1 text-slate-400">Usa el botón <strong>"Agregar Variante"</strong> para comenzar.</p>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div id="cards-no-results-variantes" class="col-span-full hidden flex-col items-center justify-center py-12 text-center">
    <i class="fas fa-search text-3xl mb-3 block text-slate-300"></i>
    <p class="font-semibold text-sm text-slate-500">No se encontraron variantes con estos filtros.</p>
</div>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/productos/partials/cards_variantes.blade.php ENDPATH**/ ?>