<div class="space-y-4">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php
            $badgeStyles = [
                'Pendiente'  => 'bg-amber-100 text-amber-800',
                'En proceso' => 'bg-blue-100 text-blue-800',
                'Escalada'   => 'bg-purple-100 text-purple-800',
                'Resuelta'   => 'bg-emerald-100 text-emerald-800',
                'Cancelada'  => 'bg-rose-100 text-rose-800',
            ];

            $esMio = ($ticket->id_usuario === auth()->id());
            $puedeGestionar = (auth()->user()->isSuperAdmin() || (auth()->user()->isAdmin() && !$esMio))
                && (!in_array($ticket->estado, ['Resuelta', 'Cancelada']) || auth()->user()->isSuperAdmin());
            $puedeCancelarPropio = $esMio && in_array($ticket->estado, ['Pendiente', 'En proceso']);
        ?>

        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 hover:border-blue-200 hover:shadow-md transition-all flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="space-y-3 flex-1">
                <!-- Badges Superiores -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono text-xs font-black text-blue-900 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">
                        #<?php echo e($ticket->codigo_ticket); ?>

                    </span>
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold <?php echo e($badgeStyles[$ticket->estado] ?? 'bg-slate-100 text-slate-700'); ?>">
                        <?php echo e($ticket->estado); ?>

                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs font-bold text-slate-600 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100">
                        <?php echo e($ticket->modulo); ?>

                    </span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->tipo_consulta): ?>
                        <span class="text-[11px] text-slate-400">(<?php echo e($ticket->tipo_consulta); ?>)</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <!-- Título y Detalle de la Duda -->
                <div>
                    <h2 class="text-base font-black text-slate-900 leading-snug">
                        <?php echo e($ticket->titulo ?? 'Consulta sobre ' . $ticket->modulo); ?>

                    </h2>
                    <p class="text-xs text-slate-600 mt-1.5 leading-relaxed whitespace-pre-line">
                        <?php echo e($ticket->duda); ?>

                    </p>
                </div>

                <!-- Resolución si ya existe -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->resolucion): ?>
                    <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200 text-xs text-emerald-900">
                        <span class="font-bold flex items-center gap-1.5 text-emerald-800 mb-0.5">
                            <i class="fas fa-check-circle"></i> Resolución:
                        </span>
                        <?php echo e($ticket->resolucion); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->resolvedor): ?>
                            <span class="block text-[10px] text-emerald-700 mt-1">
                                Atendido por: <?php echo e($ticket->resolvedor->nombre_real); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <!-- Metadata Inferior: Creador, Fecha, Evidencia -->
                <div class="flex flex-wrap items-center gap-4 text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-user text-slate-300"></i>
                        Creador: <strong class="text-slate-700 font-semibold"><?php echo e($ticket->nombre_usuario); ?></strong> (<?php echo e($ticket->rol_usuario ?? 'usuario'); ?>)
                    </span>
                    <span>&bull;</span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-calendar-day text-slate-300"></i>
                        Fecha: <?php echo e($ticket->created_at->format('d/m/Y H:i')); ?> (<?php echo e($ticket->created_at->diffForHumans()); ?>)
                    </span>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->evidencia_url): ?>
                        <span>&bull;</span>
                        <a href="<?php echo e($ticket->evidencia_url); ?>" target="_blank" class="inline-flex items-center gap-1 text-blue-600 font-bold hover:underline">
                            <i class="fas fa-paperclip"></i>
                            <span>Ver Evidencia Adjunta</span>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Botones de Acción Laterales -->
            <div class="flex flex-row md:flex-col items-center sm:items-end md:items-stretch gap-2 shrink-0">
                <a href="<?php echo e(route('ayuda.tickets.show', $ticket->id)); ?>"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-sm hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-comments text-xs"></i>
                    <span>Ver Conversación</span>
                </a>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($puedeGestionar): ?>
                    <button type="button"
                            onclick='abrirModalGestion(<?php echo json_encode($ticket, 15, 512) ?>)'
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
                        <i class="fas fa-sliders text-slate-400"></i>
                        <span>Gestionar</span>
                    </button>
                <?php elseif($puedeCancelarPropio): ?>
                    <button type="button"
                            onclick='abrirModalGestion(<?php echo json_encode($ticket, 15, 512) ?>)'
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors cursor-pointer">
                        <i class="fas fa-ban text-rose-400"></i>
                        <span>Cancelar Duda</span>
                    </button>
                <?php else: ?>
                    <span class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-xl text-[10px] font-bold text-slate-400 bg-slate-50 border border-slate-100">
                        <i class="fas fa-lock"></i>
                        <span>Cerrado</span>
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'No se encontraron tickets registrados','description' => 'Las consultas o dudas enviadas por los usuarios se listarán en este panel automáticamente.','icon' => 'folder-open']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'No se encontraron tickets registrados','description' => 'Las consultas o dudas enviadas por los usuarios se listarán en este panel automáticamente.','icon' => 'folder-open']); ?>
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

    <!-- Paginación -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tickets->hasPages()): ?>
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <?php echo e($tickets->links()); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/ayuda/tabla-tickets.blade.php ENDPATH**/ ?>