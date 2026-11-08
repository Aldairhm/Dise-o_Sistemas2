<div class="overflow-x-auto">
    <table class="w-full text-left text-xs">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                <th class="py-3 px-4">Sesión</th>
                <th class="py-3 px-4">Super Admin Auditor</th>
                <th class="py-3 px-4">Cuenta Auditada</th>
                <th class="py-3 px-4 min-w-[220px]">Justificación & Cierre</th>
                <th class="py-3 px-4 text-center">Modalidad</th>
                <th class="py-3 px-4">Fecha / Hora</th>
                <th class="py-3 px-4 text-center">Estado</th>
                <th class="py-3 px-4 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $auditorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $aud): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">
                        <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200/60">
                            #AUD-<?php echo e(str_pad($aud->id, 4, '0', STR_PAD_LEFT)); ?>

                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-bold text-slate-900"><?php echo e($aud->superAdmin->nombre_real); ?></div>
                        <div class="text-[11px] text-slate-400 font-mono"><?php echo e($aud->superAdmin->username); ?></div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-bold text-slate-900"><?php echo e($aud->usuarioAuditado->nombre_real); ?></div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[10px] font-black uppercase px-1.5 py-0.2 rounded bg-slate-100 text-slate-600">
                                <?php echo e($aud->usuarioAuditado->rol); ?>

                            </span>
                            <span class="text-[11px] text-slate-400 font-mono"><?php echo e($aud->usuarioAuditado->username); ?></span>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-slate-600">
                        <div class="text-xs line-clamp-2" title="<?php echo e($aud->justificacion); ?>">
                            <?php echo e($aud->justificacion); ?>

                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aud->notas_cierre): ?>
                            <div class="text-[11px] text-slate-400 mt-1 italic flex items-center gap-1">
                                <i class="fas fa-check-double text-slate-300"></i>
                                <span class="truncate">Cierre: <?php echo e($aud->notas_cierre); ?></span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aud->solo_lectura): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                                <i class="fas fa-eye text-[9px]"></i>
                                <span>Solo Lectura</span>
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200/60">
                                <i class="fas fa-pen-ruler text-[9px]"></i>
                                <span>Completa</span>
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="py-3 px-4 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                        <div><?php echo e($aud->iniciada_en->format('d/m/Y H:i')); ?></div>
                        <div class="text-[10px] text-slate-400">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aud->finalizada_en): ?>
                                <?php echo e($aud->iniciada_en->diffInMinutes($aud->finalizada_en)); ?> min de sesión
                            <?php else: ?>
                                Expira: <?php echo e($aud->expira_en->format('H:i')); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aud->estado === 'activa' && $aud->expira_en > now()): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200 animate-pulse">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                EN CURSO
                            </span>
                        <?php elseif($aud->estado === 'completada'): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700">
                                <i class="fas fa-check text-slate-400 text-[9px]"></i>
                                Completada
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
                                <i class="fas fa-ban text-rose-400 text-[9px]"></i>
                                Revocada
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="py-3 px-4 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1.5">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aud->estado === 'activa' && $aud->expira_en > now()): ?>
                                <form id="formRevocar<?php echo e($aud->id); ?>" action="<?php echo e(route('superadmin.auditorias.revocar', $aud->id)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="button"
                                            onclick="confirmarRevocacion(<?php echo e($aud->id); ?>)"
                                            class="px-2.5 py-1 rounded-lg text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors inline-flex items-center gap-1">
                                        <i class="fas fa-hand text-rose-500"></i>
                                        <span>Revocar</span>
                                    </button>
                                </form>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <a href="<?php echo e(route('superadmin.auditorias.informe', $aud->id)); ?>"
                               class="px-2.5 py-1 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors inline-flex items-center gap-1">
                                <i class="fas fa-file-shield text-indigo-500"></i>
                                <span>Informe</span>
                            </a>

                            <a href="<?php echo e(route('bitacora.index', ['auditoria_id' => $aud->id])); ?>"
                               title="Ver trazabilidad de eventos de la sesión #AUD-<?php echo e(str_pad($aud->id, 4, '0', STR_PAD_LEFT)); ?>"
                               class="px-2.5 py-1 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors inline-flex items-center gap-1">
                                <i class="fas fa-clipboard-list text-blue-500"></i>
                                <span>Trazabilidad</span>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="8" class="py-8">
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'No hay registros históricos de sesiones de auditoría','description' => 'Las auditorías iniciadas bajo gobernanza quedarán documentadas de forma inmutable aquí.','icon' => 'folder-open']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'No hay registros históricos de sesiones de auditoría','description' => 'Las auditorías iniciadas bajo gobernanza quedarán documentadas de forma inmutable aquí.','icon' => 'folder-open']); ?>
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
        </tbody>
    </table>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($auditorias->hasPages()): ?>
    <div class="p-4 border-t border-slate-100">
        <?php echo e($auditorias->links()); ?>

    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/superadmin/tabla-historial-auditorias.blade.php ENDPATH**/ ?>