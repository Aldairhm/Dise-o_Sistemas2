<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Centro de Dudas Administrativas | AXStore Super Admin']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Centro de Dudas Administrativas | AXStore Super Admin']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="mx-auto max-w-7xl space-y-6 pb-12">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="<?php echo e(route('superadmin.dashboard')); ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 mb-1">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Panel de Gobernanza</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-lg shrink-0">
                        <i class="fas fa-bolt"></i>
                    </span>
                    <span>Centro de Dudas y Casos Escalados (Super Admin)</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Atención de solicitudes provenientes de Administradores o casos complejos escalados por el equipo.
                </p>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <form method="GET" action="<?php echo e(route('superadmin.tickets')); ?>" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[240px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Buscar por código, título, duda o usuario..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div>
                <select name="estado" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="todos">Todos los estados</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Models\SoporteTicket::ESTADOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $est): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($est); ?>" <?php echo e(request('estado') === $est ? 'selected' : ''); ?>><?php echo e($est); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>

            <div>
                <select name="prioridad" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="todos">Todas las prioridades</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Models\SoporteTicket::PRIORIDADES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($prio); ?>" <?php echo e(request('prioridad') === $prio ? 'selected' : ''); ?>><?php echo e($prio); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition-colors">
                Filtrar
            </button>
        </form>

        <!-- Lista de Tickets -->
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
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold 
                                <?php echo e($tk->prioridad === 'Urgente' ? 'bg-rose-100 text-rose-800 animate-pulse' : ($tk->prioridad === 'Alta' ? 'bg-amber-100 text-amber-800' : 'bg-blue-50 text-blue-700')); ?>">
                                Prioridad: <?php echo e($tk->prioridad); ?>

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
                <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center text-slate-400">
                    <i class="fas fa-inbox text-3xl mb-2 text-slate-300"></i>
                    <p class="text-sm font-medium">No hay dudas que requieran atención de Super Administrador en este momento.</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="p-4">
                <?php echo e($tickets->links()); ?>

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
<?php endif; ?>

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/superadmin/tickets.blade.php ENDPATH**/ ?>