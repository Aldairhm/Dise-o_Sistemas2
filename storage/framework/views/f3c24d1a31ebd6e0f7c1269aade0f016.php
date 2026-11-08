<div class="space-y-4">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 hover:border-indigo-200 transition-all flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="space-y-3 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono text-xs font-black text-indigo-900 bg-indigo-50 px-2.5 py-1 rounded-lg">
                        #<?php echo e($tk->codigo_ticket); ?>

                    </span>
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold 
                        <?php echo e($tk->estado === 'Escalada' ? 'bg-amber-100 text-amber-800' : ($tk->estado === 'Resuelta' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700')); ?>">
                        <?php echo e($tk->estado); ?>

                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs font-bold text-slate-600"><?php echo e($tk->modulo); ?></span>
                </div>

                <div>
                    <h2 class="text-base font-black text-slate-900">
                        <?php echo e($tk->titulo ?? 'Consulta sobre ' . $tk->modulo); ?>

                    </h2>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        <?php echo e($tk->duda); ?>

                    </p>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tk->justificacion_escalamiento): ?>
                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900">
                        <span class="font-bold flex items-center gap-1.5 text-amber-800 mb-0.5">
                            <i class="fas fa-triangle-exclamation"></i> Justificación de escalamiento:
                        </span>
                        <?php echo e($tk->justificacion_escalamiento); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tk->escalador): ?>
                            <span class="block text-[10px] text-amber-700 mt-1">
                                Escalado por: <?php echo e($tk->escalador->nombre_real); ?> (Admin)
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="flex flex-wrap items-center gap-4 text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-user text-slate-300"></i> Creador: <strong class="text-slate-600"><?php echo e($tk->nombre_usuario); ?></strong> (<?php echo e($tk->rol_usuario); ?>)
                    </span>
                    <span>&bull;</span>
                    <span>Fecha: <?php echo e($tk->created_at->format('d/m/Y H:i')); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tk->mensajes_count ?? false): ?>
                        <span>&bull;</span>
                        <span class="font-bold text-indigo-600"><i class="fas fa-comments"></i> <?php echo e($tk->mensajes_count); ?> mensajes</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row md:flex-col gap-2 shrink-0">
                <a href="<?php echo e(route('ayuda.tickets.show', $tk->id)); ?>" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white text-center transition-colors shadow-sm">
                    <i class="fas fa-comments mr-1.5"></i> Ver Conversación
                </a>
            </div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'No hay dudas para Super Administrador','description' => 'En este momento no existen solicitudes pendientes ni casos escalados con estos filtros.','icon' => 'folder-open']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'No hay dudas para Super Administrador','description' => 'En este momento no existen solicitudes pendientes ni casos escalados con estos filtros.','icon' => 'folder-open']); ?>
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
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tickets->hasPages()): ?>
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <?php echo e($tickets->links()); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/superadmin/tabla-tickets.blade.php ENDPATH**/ ?>