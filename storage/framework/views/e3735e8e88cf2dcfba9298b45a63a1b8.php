<div class="overflow-x-auto">
    <table class="w-full min-w-[760px] text-left text-sm">
        <thead class="border-b border-slate-200 bg-slate-50 text-[10px] uppercase tracking-widest text-slate-500">
            <tr>
                <th class="px-5 py-4">Fecha</th>
                <th class="px-5 py-4">Tipo</th>
                <th class="px-5 py-4">Producto / variante</th>
                <th class="px-5 py-4 text-center">Cantidad</th>
                <th class="px-5 py-4">Origen</th>
                <th class="px-5 py-4 text-right">Detalle</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $movimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movimiento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $imagenMovimiento = $movimiento->variante?->imagenes?->firstWhere('es_principal', 1) ?? $movimiento->variante?->imagenes?->first();
                    $esDevolucion = $movimiento->devolucion_id || in_array($movimiento->tipo, ['devolucion_cliente', 'devolucion_proveedor']) || str_contains(strtolower($movimiento->observacion ?? ''), 'devolu');
                    $etiquetaTipo = match ($movimiento->tipo) {
                        'transferencia_tienda' => 'Transferencia',
                        'compra_recibida' => 'Compra recibida',
                        'devolucion_cliente' => 'Devolución cliente',
                        'devolucion_proveedor' => 'Devolución proveedor',
                        'Entrada', 'entrada' => 'Entrada',
                        'Salida', 'salida' => 'Salida',
                        default => $movimiento->tipo,
                    };
                ?>
                <tr class="hover:bg-slate-50/60">
                    <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500"><?php echo e($movimiento->created_at?->format('d/m/Y H:i')); ?></td>
                    <td class="px-5 py-4">
                        <span class="inline-flex whitespace-nowrap rounded-lg border px-2.5 py-1 text-[11px] font-bold <?php echo e($esDevolucion ? 'border-red-200 bg-red-50 text-red-700' : 'border-slate-200 bg-slate-50 text-slate-700'); ?>">
                            <?php echo e($etiquetaTipo); ?>

                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($movimiento->variante?->imagen_url): ?>
                                    <img src="<?php echo e($movimiento->variante->imagen_url); ?>" alt="<?php echo e($movimiento->variante?->nombre_variante ?? 'Producto'); ?>" class="h-full w-full object-cover">
                                <?php else: ?>
                                    <div class="flex h-full items-center justify-center text-slate-300"><i class="fas fa-image text-xs"></i></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-slate-800"><?php echo e($movimiento->variante?->producto?->nombre ?? 'Producto'); ?></p>
                                <p class="text-xs text-slate-500"><?php echo e($movimiento->variante?->nombre_variante ?? 'Variante sin nombre'); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-center font-black text-amber-700"><?php echo e($movimiento->cantidad); ?></td>
                    <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($movimiento->devolucion): ?>
                            <span class="font-bold text-blue-600">DEV-<?php echo e(str_pad($movimiento->devolucion_id, 5, '0', STR_PAD_LEFT)); ?></span>
                        <?php elseif($movimiento->compra): ?>
                            <span>Compra #<?php echo e($movimiento->id_compra); ?></span>
                        <?php else: ?>
                            <span class="text-slate-400">Inventario</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <button type="button" 
                                @click="abrirDetalleMovimiento(<?php echo e($movimiento->id); ?>)" 
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-blue-600 shadow-sm hover:bg-blue-50 cursor-pointer">
                            <i class="fas fa-eye"></i> Ver detalle
                        </button>
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="6" class="px-5 py-12">
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'No hay movimientos registrados','description' => 'No se encontraron registros para el filtro seleccionado en el kardex.','icon' => 'folder-open']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'No hay movimientos registrados','description' => 'No se encontraron registros para el filtro seleccionado en el kardex.','icon' => 'folder-open']); ?>
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
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($movimientos->hasPages()): ?>
    <div class="border-t border-slate-100 p-4"><?php echo e($movimientos->links()); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/compras/tabla-movimientos.blade.php ENDPATH**/ ?>