<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Devoluciones de clientes | AXStore']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Devoluciones de clientes | AXStore']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="mx-auto max-w-[1440px] space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="<?php echo e(route('ventas.index')); ?>" class="mb-4 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600"><i class="fas fa-arrow-left"></i> Volver a ventas</a>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600">Módulo de Ventas</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">Devoluciones de clientes</h1>
                <p class="mt-2 text-sm text-slate-500">Consulta los retornos registrados desde ventas entregadas.</p>
            </div>
            <div class="flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 shadow-sm"><i class="fas fa-rotate-left text-blue-600"></i><span class="text-sm font-bold text-blue-800"><?php echo e($devoluciones->total()); ?> registros</span></div>
        </div>

        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones de ventas">
            <a href="<?php echo e(route('ventas.index')); ?>" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-chart-line mr-2 text-slate-400"></i>Dashboard Histórico</a>
            <a href="<?php echo e(route('ventas.create')); ?>" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-cash-register mr-2 text-slate-400"></i>Terminal de Ventas (Carrito)</a>
            <a href="<?php echo e(route('ventas.pedidos')); ?>" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-boxes-packing mr-2 text-slate-400"></i>Control de Envíos y Estados</a>
            <a href="<?php echo e(route('ventas.devoluciones')); ?>" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm"><i class="fas fa-rotate-left mr-2"></i>Devoluciones de clientes</a>
        </nav>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4"><h2 class="text-base font-black text-slate-900">Historial de devoluciones de clientes</h2><p class="mt-1 text-xs text-slate-500">Solo se muestran devoluciones cuyo origen es una venta.</p></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-white text-[10px] font-black uppercase tracking-widest text-slate-500"><tr><th class="px-5 py-4">Ticket / fecha</th><th class="px-5 py-4">Venta</th><th class="px-5 py-4">Resolución</th><th class="px-5 py-4 text-right">Impacto financiero</th><th class="px-5 py-4">Motivo</th><th class="px-5 py-4 text-right">Evidencia</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $devoluciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $devolucion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php $resolucion = ucfirst(str_replace('_', ' ', $devolucion->tipo_resolucion ?? 'Sin resolución')); ?>
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-5 py-4"><p class="font-mono text-xs font-black text-slate-800">DEV-<?php echo e(str_pad($devolucion->id, 5, '0', STR_PAD_LEFT)); ?></p><p class="mt-1 text-xs text-slate-500"><?php echo e(optional($devolucion->created_at)->format('d/m/Y H:i')); ?></p></td>
                                <td class="px-5 py-4"><span class="inline-flex rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-bold text-blue-700"><i class="fas fa-receipt mr-1.5"></i>Venta #<?php echo e($devolucion->origen_id); ?></span></td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-bold capitalize text-slate-700"><?php echo e($resolucion); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($devolucion->detalles_json['modalidad_entrega_cambio'] ?? null) === 'envio'): ?>
                                            <span class="inline-flex items-center gap-1 rounded-md border border-teal-200 bg-teal-50 px-2 py-0.5 text-[10px] font-bold text-teal-800" title="Despacho programado por envío">
                                                <i class="fas fa-truck-fast text-[9px]"></i> Reenvío a domicilio
                                            </span>
                                        <?php elseif(($devolucion->detalles_json['modalidad_entrega_cambio'] ?? null) === 'tienda' && str_contains($devolucion->tipo_resolucion ?? '', 'cambio')): ?>
                                            <span class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700" title="Entregado en tienda física">
                                                <i class="fas fa-store text-[9px]"></i> Cambio en tienda
                                            </span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($devolucion->detalles_json['nueva_venta_id'])): ?>
                                                <a href="<?php echo e(route('ventas.show', $devolucion->detalles_json['nueva_venta_id'])); ?>" class="inline-flex items-center gap-1 rounded-md border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-800 hover:bg-emerald-100 transition-colors" title="Ver nueva venta de reemplazo">
                                                    <i class="fas fa-arrow-up-right-from-square text-[8px]"></i> Nueva Venta #<?php echo e($devolucion->detalles_json['nueva_venta_id']); ?>

                                                </a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-right font-black <?php echo e((float) $devolucion->monto_reembolsado > 0 ? 'text-red-600' : 'text-slate-500'); ?>">$<?php echo e(number_format((float) $devolucion->monto_reembolsado, 2)); ?></td>
                                <td class="max-w-[300px] px-5 py-4"><span class="block truncate text-xs text-slate-600" title="<?php echo e($devolucion->motivo); ?>"><?php echo e(\Illuminate\Support\Str::limit($devolucion->motivo, 60)); ?></span></td>
                                <td class="px-5 py-4 text-right"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($devolucion->comprobante): ?><a href="<?php echo e(str_starts_with($devolucion->comprobante, 'http') ? $devolucion->comprobante : \Illuminate\Support\Facades\Storage::disk('s3')->url($devolucion->comprobante)); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 hover:bg-blue-600 hover:text-white"><i class="fas fa-paperclip"></i> Ver evidencia</a><?php else: ?><span class="text-xs text-slate-400">Sin evidencia</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr><td colspan="6" class="px-5 py-16 text-center"><i class="fas fa-folder-open mb-3 block text-2xl text-slate-300"></i><p class="text-sm font-bold text-slate-700">No hay devoluciones de clientes</p><p class="mt-1 text-xs text-slate-400">Las devoluciones creadas desde el historial de ventas aparecerán aquí.</p></td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($devoluciones->hasPages()): ?><div class="border-t border-slate-100 bg-slate-50/50 p-4"><?php echo e($devoluciones->links()); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>
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
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/ventas/devoluciones.blade.php ENDPATH**/ ?>