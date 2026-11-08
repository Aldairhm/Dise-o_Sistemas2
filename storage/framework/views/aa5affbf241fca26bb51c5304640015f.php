<!-- TABLA DETALLADA DE MOVIMIENTOS -->
<div class="overflow-x-auto print:overflow-visible rounded-2xl border border-slate-200">
    <table class="w-full text-left text-xs border-collapse">
        <colgroup>
            <col style="width: 3.5%;">   <!-- # -->
            <col style="width: 8.5%;">   <!-- Fecha -->
            <col style="width: 8%;">     <!-- Tipo -->
            <col style="width: 8%;">     <!-- Folio -->
            <col style="width: 23%;">    <!-- Producto / Descripción -->
            <col style="width: 5.5%;">   <!-- Cant. -->
            <col style="width: 7%;">     <!-- C. Unit. -->
            <col style="width: 7.5%;">   <!-- C. Total -->
            <col style="width: 8%;">     <!-- Ingreso -->
            <col style="width: 7%;">     <!-- Comisión -->
            <col style="width: 8%;">     <!-- Ganancia -->
            <col style="width: 14.5%;">  <!-- Responsable -->
        </colgroup>
        <thead>
            <tr class="bg-slate-100 text-slate-800 text-[10px] font-black uppercase tracking-wider border-b-2 border-blue-600 print:bg-slate-100 print:text-slate-800 print:border-b-2 print:border-blue-600">
                <th class="py-2.5 px-1 text-center">#</th>
                <th class="py-2.5 px-1.5 text-center">Fecha</th>
                <th class="py-2.5 px-1 text-center">Tipo</th>
                <th class="py-2.5 px-1 text-center">Folio</th>
                <th class="py-2.5 px-2 text-left">Producto / Descripción</th>
                <th class="py-2.5 px-1 text-right">Cant.</th>
                <th class="py-2.5 px-1 text-right">C. Unit.</th>
                <th class="py-2.5 px-1 text-right">C. Total</th>
                <th class="py-2.5 px-1 text-right">Ingreso</th>
                <th class="py-2.5 px-1 text-right">Comisión</th>
                <th class="py-2.5 px-1 text-right">Ganancia</th>
                <th class="py-2.5 px-2 text-left">Responsable</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 print:divide-slate-200 text-[11px]">
            <?php
                $items = (request()->query('modo') === 'imprimir' || request()->has('imprimir')) ? $todosMovimientos : $movimientos;
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $indice => $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr class="hover:bg-blue-50/30 transition-colors <?php echo e($loop->even ? 'bg-slate-50/50' : 'bg-white'); ?> print:break-inside-avoid">
                    <!-- # -->
                    <td class="py-2 px-1 text-center text-slate-400 font-mono text-[10px]">
                        <?php echo e($indice + 1); ?>

                    </td>

                    <!-- Fecha y hora -->
                    <td class="py-2 px-1.5 text-center leading-tight">
                        <span class="font-bold text-slate-800 block text-[10px] sm:text-[11px]"><?php echo e($mov['fecha_formateada']); ?></span>
                        <span class="block text-[9px] text-slate-400 font-mono"><?php echo e($mov['hora_formateada']); ?></span>
                    </td>

                    <!-- Tipo Badge -->
                    <td class="py-2 px-1 text-center">
                        <?php
                            $badgeClasses = match($mov['tipo_badge']) {
                                'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-300 font-black',
                                'rose' => 'bg-rose-50 text-rose-800 border-rose-300 font-black',
                                'amber' => 'bg-amber-50 text-amber-800 border-amber-300 font-black',
                                'indigo' => 'bg-indigo-50 text-indigo-800 border-indigo-300 font-black',
                                'purple' => 'bg-purple-50 text-purple-800 border-purple-300 font-black',
                                default => 'bg-slate-100 text-slate-700 border-slate-300',
                            };
                        ?>
                        <span class="inline-block px-1.5 py-0.5 rounded-full text-[8.5px] uppercase tracking-tight border leading-none <?php echo e($badgeClasses); ?>">
                            <?php echo e($mov['categoria']); ?>

                        </span>
                    </td>

                    <!-- Folio -->
                    <td class="py-2 px-1 text-center font-mono font-bold text-slate-800 text-[10px] sm:text-[11px]">
                        <?php echo e($mov['folio']); ?>

                    </td>

                    <!-- Producto / Descripción & SKU -->
                    <td class="py-2 px-2 print:py-1.5 print:px-1.5 leading-snug">
                        <p class="font-bold text-slate-900 break-words whitespace-normal print:text-[8pt] print:leading-tight" title="<?php echo e($mov['descripcion']); ?>">
                            <?php echo e($mov['descripcion']); ?>

                        </p>
                        <p class="text-[9px] text-slate-400 uppercase tracking-wider font-mono print:text-[7pt] mt-0.5">
                            SKU: <?php echo e($mov['sku']); ?>

                        </p>
                    </td>

                    <!-- Cantidad con signo -->
                    <td class="py-2 px-1 text-right font-black tabular-nums <?php echo e($mov['signo_stock'] === '+' ? 'text-emerald-700' : ($mov['signo_stock'] === '-' ? 'text-rose-700' : 'text-slate-700')); ?>">
                        <?php echo e($mov['signo_stock']); ?><?php echo e($mov['cantidad']); ?>

                    </td>

                    <!-- Costo Unitario -->
                    <td class="py-2 px-1 text-right tabular-nums text-slate-600">
                        $<?php echo e(number_format($mov['costo_unitario'], 2)); ?>

                    </td>

                    <!-- Costo Total -->
                    <td class="py-2 px-1 text-right tabular-nums font-semibold text-slate-800">
                        $<?php echo e(number_format($mov['costo_total'], 2)); ?>

                    </td>

                    <!-- Ingreso Total -->
                    <td class="py-2 px-1 text-right tabular-nums font-black text-slate-900">
                        $<?php echo e(number_format($mov['ingreso_total'], 2)); ?>

                    </td>

                    <!-- Comisión -->
                    <td class="py-2 px-1 text-right tabular-nums text-indigo-700 font-semibold">
                        <?php echo e($mov['comision'] > 0 ? '$' . number_format($mov['comision'], 2) : '—'); ?>

                    </td>

                    <!-- Ganancia Neta -->
                    <td class="py-2 px-1 text-right tabular-nums font-black <?php echo e($mov['ganancia_neta'] > 0 ? 'text-emerald-700' : ($mov['ganancia_neta'] < 0 ? 'text-rose-700' : 'text-slate-400')); ?>">
                        <?php echo e($mov['ganancia_neta'] != 0 ? '$' . number_format($mov['ganancia_neta'], 2) : '—'); ?>

                    </td>

                    <!-- Responsable / Origen -->
                    <td class="py-2 px-2 text-slate-600 text-[10px] print:text-[7.5pt] print:py-1.5 print:px-1.5 leading-snug">
                        <span class="font-bold text-slate-800 block break-words whitespace-normal leading-tight"><?php echo e($mov['usuario_responsable']); ?></span>
                        <span class="text-slate-500 block break-words whitespace-normal leading-tight mt-0.5 print:text-[7pt]" title="<?php echo e($mov['origen_destino']); ?>"><?php echo e($mov['origen_destino']); ?></span>
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="12" class="py-12 text-center text-slate-400">
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'No se encontraron movimientos registrados','description' => 'Intenta ampliar el rango de fechas, cambiar los filtros o seleccionar \'Todo el historial\'.','icon' => 'search']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'No se encontraron movimientos registrados','description' => 'Intenta ampliar el rango de fechas, cambiar los filtros o seleccionar \'Todo el historial\'.','icon' => 'search']); ?>
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

        <!-- FILA FINAL DE TOTALES / RESUMEN CON DESTACADO CORPORATIVO -->
        <tfoot>
            <tr class="bg-blue-50/70 text-slate-900 font-black text-xs border-t-2 border-blue-600 border-b-2 border-slate-300 print:bg-blue-50/70 print:text-slate-900 print:border-t-2 print:border-blue-600 print:border-b-2 print:border-slate-300">
                <td colspan="5" class="py-2.5 px-2 text-left uppercase tracking-wider text-[10px] sm:text-[11px] text-blue-950">
                    <i class="fas fa-calculator text-blue-600 mr-1.5"></i> Totales Generales (<?php echo e($totales['cantidad_movimientos']); ?> reg.)
                </td>
                <td class="py-2.5 px-1 text-right tabular-nums text-slate-900 font-black">
                    <?php echo e($totales['total_unidades']); ?>

                </td>
                <td class="py-2.5 px-1 text-right tabular-nums text-slate-400">—</td>
                <td class="py-2.5 px-1 text-right tabular-nums text-rose-600 font-black">
                    $<?php echo e(number_format($totales['total_costos'], 2)); ?>

                </td>
                <td class="py-2.5 px-1 text-right tabular-nums text-blue-700 font-black">
                    $<?php echo e(number_format($totales['total_ingresos'], 2)); ?>

                </td>
                <td class="py-2.5 px-1 text-right tabular-nums text-indigo-700 font-black">
                    $<?php echo e(number_format($totales['total_comisiones'], 2)); ?>

                </td>
                <td class="py-2.5 px-1 text-right tabular-nums text-emerald-700 font-black">
                    $<?php echo e(number_format($totales['total_ganancias'], 2)); ?>

                </td>
                <td class="py-2.5 px-2 text-slate-400 text-center">—</td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- PAGINACIÓN EN PANTALLA (SOLO PANTALLA) -->
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->query('modo') !== 'imprimir' && !request()->has('imprimir') && $movimientos instanceof \Illuminate\Pagination\LengthAwarePaginator && $movimientos->hasPages()): ?>
    <div class="no-print mt-4 py-3 px-4 sm:px-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="text-xs text-slate-500 font-medium">
            Mostrando <b class="text-slate-800 font-bold"><?php echo e($movimientos->firstItem()); ?></b> a <b class="text-slate-800 font-bold"><?php echo e($movimientos->lastItem()); ?></b> de <b class="text-slate-800 font-bold"><?php echo e($movimientos->total()); ?></b> registros
        </div>

        <nav class="flex items-center gap-1.5" aria-label="Navegación de páginas">
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($movimientos->onFirstPage()): ?>
                <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 text-slate-300 text-xs cursor-not-allowed select-none">
                    <i class="fas fa-chevron-left"></i>
                </span>
            <?php else: ?>
                <a href="<?php echo e($movimientos->previousPageUrl()); ?>" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 text-xs transition-colors cursor-pointer shadow-sm">
                    <i class="fas fa-chevron-left"></i>
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $movimientos->getUrlRange(max(1, $movimientos->currentPage() - 2), min($movimientos->lastPage(), $movimientos->currentPage() + 2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($page == $movimientos->currentPage()): ?>
                    <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-600 text-white text-xs font-black shadow-md shadow-blue-500/25">
                        <?php echo e($page); ?>

                    </span>
                <?php else: ?>
                    <a href="<?php echo e($url); ?>" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 text-xs font-bold transition-colors cursor-pointer">
                        <?php echo e($page); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($movimientos->hasMorePages()): ?>
                <a href="<?php echo e($movimientos->nextPageUrl()); ?>" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 text-xs transition-colors cursor-pointer shadow-sm">
                    <i class="fas fa-chevron-right"></i>
                </a>
            <?php else: ?>
                <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 text-slate-300 text-xs cursor-not-allowed select-none">
                    <i class="fas fa-chevron-right"></i>
                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </nav>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/admin/reportes/tabla-reportes-movimientos.blade.php ENDPATH**/ ?>