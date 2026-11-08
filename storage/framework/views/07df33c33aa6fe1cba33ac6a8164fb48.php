<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                <th class="py-3.5 px-4">Fecha / Hora</th>
                <th class="py-3.5 px-4">Usuario</th>
                <th class="py-3.5 px-4">Módulo</th>
                <th class="py-3.5 px-4">Acción</th>
                <th class="py-3.5 px-4">Descripción del Movimiento</th>
                <th class="py-3.5 px-4">IP / Origen</th>
                <th class="py-3.5 px-4 text-center">Detalle</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $registros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <tr class="hover:bg-slate-50/60 transition-colors">
                <!-- Fecha -->
                <td class="py-3 px-4 whitespace-nowrap">
                    <div class="font-bold text-slate-800"><?php echo e($item->created_at->format('d/m/Y')); ?></div>
                    <div class="text-[10px] text-slate-400 font-medium"><?php echo e($item->created_at->format('h:i:s A')); ?> (<?php echo e($item->created_at->diffForHumans()); ?>)</div>
                </td>

                <!-- Usuario -->
                <td class="py-3 px-4 whitespace-nowrap">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->usuario): ?>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-teal-500 to-blue-500 text-white font-bold text-[11px] flex items-center justify-center shrink-0">
                                <?php echo e(strtoupper(substr($item->usuario->nombre_real, 0, 1))); ?>

                            </div>
                            <div>
                                <div class="font-bold text-slate-900 leading-tight"><?php echo e($item->usuario->nombre_real); ?></div>
                                <div class="text-[10px] text-slate-400 font-medium">@<span><?php echo e($item->usuario->username); ?></span></div>
                            </div>
                        </div>
                    <?php elseif($item->username): ?>
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 font-bold text-[11px] flex items-center justify-center shrink-0">
                                <i class="fas fa-user-secret text-xs"></i>
                            </div>
                            <div>
                                <div class="font-bold text-slate-700"><?php echo e($item->username); ?></div>
                                <div class="text-[10px] text-slate-400 font-medium">Externo / Intento</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 text-slate-400 italic font-medium">
                            <i class="fas fa-robot text-xs"></i>
                            <span>Sistema</span>
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>

                <!-- Módulo -->
                <td class="py-3 px-4 whitespace-nowrap">
                    <?php
                        $modIcon = match($item->modulo) {
                            'Ventas'        => 'fa-cash-register text-blue-500',
                            'Compras'       => 'fa-cart-plus text-emerald-500',
                            'Productos'     => 'fa-shopping-bag text-indigo-500',
                            'Categorías'    => 'fa-folder text-purple-500',
                            'Usuarios'      => 'fa-users text-amber-500',
                            'Autenticación' => 'fa-key text-slate-600',
                            'Bodega'        => 'fa-warehouse text-cyan-500',
                            'Proveedores'   => 'fa-truck text-orange-500',
                            'Devoluciones'  => 'fa-rotate-left text-rose-500',
                            'Comisiones'    => 'fa-hand-holding-dollar text-emerald-600',
                            default         => 'fa-circle-dot text-slate-400',
                        };
                    ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-[11px] font-semibold text-slate-700">
                        <i class="fas <?php echo e($modIcon); ?>"></i>
                        <span><?php echo e($item->modulo); ?></span>
                    </span>
                </td>

                <!-- Acción -->
                <td class="py-3 px-4 whitespace-nowrap">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider badge-<?php echo e($item->accion); ?>">
                        <i class="fas <?php echo e($item->icon); ?> text-[9px]"></i>
                        <span><?php echo e($item->accion); ?></span>
                    </span>
                </td>

                <!-- Descripción -->
                <td class="py-3 px-4">
                    <p class="text-xs text-slate-800 font-medium line-clamp-2" title="<?php echo e($item->descripcion); ?>">
                        <?php echo e($item->descripcion); ?>

                    </p>
                </td>

                <!-- IP -->
                <td class="py-3 px-4 whitespace-nowrap">
                    <div class="font-mono text-[11px] text-slate-600 font-medium flex items-center gap-1.5">
                        <i class="fas fa-network-wired text-slate-400 text-[10px]"></i>
                        <span><?php echo e($item->ip_address ?: '127.0.0.1'); ?></span>
                    </div>
                </td>

                <!-- Detalle -->
                <td class="py-3 px-4 text-center whitespace-nowrap">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->datos_anteriores || $item->datos_nuevos): ?>
                        <button type="button" 
                                onclick='verDetalle(<?php echo json_encode($item, 15, 512) ?>)'
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-bold transition-all cursor-pointer">
                            <i class="fas fa-code-compare"></i>
                            <span>Diff</span>
                        </button>
                    <?php else: ?>
                        <button type="button"
                                onclick='verDetalleSimple(<?php echo json_encode($item, 15, 512) ?>)'
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-all cursor-pointer">
                            <i class="fas fa-eye"></i>
                            <span>Ver</span>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>
            </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <tr>
                <td colspan="7" class="py-8">
                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'No se encontraron movimientos registrados','description' => 'Prueba cambiando o eliminando los filtros aplicados arriba.','icon' => 'search']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'No se encontraron movimientos registrados','description' => 'Prueba cambiando o eliminando los filtros aplicados arriba.','icon' => 'search']); ?>
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

<!-- Paginación -->
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($registros->hasPages()): ?>
<div class="px-6 py-4 bg-slate-50/70 border-t border-slate-200/80">
    <?php echo e($registros->links()); ?>

</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/bitacora/tabla-bitacora.blade.php ENDPATH**/ ?>