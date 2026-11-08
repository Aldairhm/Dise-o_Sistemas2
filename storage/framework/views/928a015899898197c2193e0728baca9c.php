<div class="overflow-x-auto">
    <table class="w-full text-left text-xs">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 font-semibold uppercase text-[10px]">
                <th class="py-3 px-4">Usuario</th>
                <th class="py-3 px-4">Rol en Sistema</th>
                <th class="py-3 px-4">Teléfono</th>
                <th class="py-3 px-4">Estado</th>
                <th class="py-3 px-4">Fecha Registro</th>
                <th class="py-3 px-4 text-right">Acciones de Auditoría</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr class="hover:bg-slate-50/60">
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center shrink-0 uppercase text-xs">
                                <?php echo e(substr($u->nombre_real, 0, 2)); ?>

                            </div>
                            <div>
                                <span class="font-bold text-slate-900 block leading-tight"><?php echo e($u->nombre_real); ?></span>
                                <span class="text-[11px] text-slate-400 font-mono"><?php echo e($u->username); ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($u->rol === 'super_admin'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-700">
                                SUPER ADMIN
                            </span>
                        <?php elseif($u->rol === 'admin'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-100 text-blue-700">
                                ADMIN
                            </span>
                        <?php else: ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-700">
                                VENDEDOR
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="py-3 px-4 text-slate-500 font-mono">
                        <?php echo e($u->telefono ?: '—'); ?>

                    </td>
                    <td class="py-3 px-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($u->estado == 1): ?>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Inactivo
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">
                        <?php echo e($u->created_at->format('d/m/Y')); ?>

                    </td>
                    <td class="py-3 px-4 text-right">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($u->isSuperAdmin()): ?>
                            <button type="button"
                                    onclick="abrirModalEditarSuperAdmin(<?php echo e($u->id); ?>, '<?php echo e(addslashes($u->nombre_real)); ?>', '<?php echo e($u->username); ?>', '<?php echo e($u->telefono ?? ''); ?>', <?php echo e($u->estado); ?>)"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition-colors inline-flex items-center gap-1.5 cursor-pointer shadow-xs">
                                <i class="fas fa-user-pen text-[11px]"></i>
                                <span>Editar Super Admin</span>
                            </button>
                        <?php else: ?>
                            <button type="button" onclick="abrirModalAuditoria(<?php echo e($u->id); ?>, '<?php echo e(addslashes($u->nombre_real)); ?>', '<?php echo e($u->username); ?>')" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-purple-50 hover:bg-purple-100 text-purple-700 transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                                <i class="fas fa-user-secret"></i>
                                <span>Auditar Cuenta</span>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="6" class="py-8">
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'No se encontraron usuarios','description' => 'Intenta modificar los filtros de búsqueda o seleccionar otro rol.','icon' => 'search']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'No se encontraron usuarios','description' => 'Intenta modificar los filtros de búsqueda o seleccionar otro rol.','icon' => 'search']); ?>
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

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->hasPages()): ?>
    <div class="p-4 border-t border-slate-100">
        <?php echo e($users->links()); ?>

    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/superadmin/tabla-usuarios.blade.php ENDPATH**/ ?>