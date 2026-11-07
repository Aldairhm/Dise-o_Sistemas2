<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin): ?>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Vendedor</th>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Concepto / Venta</th>
                <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Comisión</th>
                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Método de Pago</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Notas</th>
                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $comisiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <tr class="hover:bg-slate-50/60 transition-colors" data-id="<?php echo e($c->id); ?>">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin): ?>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                            <?php echo e(strtoupper(substr($c->vendedor->nombre_real ?? '?', 0, 2))); ?>

                        </div>
                        <div>
                            <span class="font-bold text-slate-800 block text-xs"><?php echo e($c->vendedor->nombre_real ?? '—'); ?></span>
                            <span class="text-[10px] text-slate-400 capitalize"><?php echo e($c->vendedor->rol ?? 'vendedor'); ?></span>
                        </div>
                    </div>
                </td>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <td class="px-5 py-4 text-slate-600 whitespace-nowrap">
                    <span class="font-medium text-xs text-slate-800 block"><?php echo e($c->fecha_registro ? $c->fecha_registro->format('d/m/Y') : '—'); ?></span>
                    <span class="text-[10px] text-slate-400"><?php echo e($c->fecha_registro ? $c->fecha_registro->format('h:i A') : ''); ?></span>
                </td>

                <td class="px-5 py-4 text-slate-700">
                    <div class="flex flex-col gap-0.5">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->id_salida): ?>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md text-[11px] font-bold">
                                    <i class="fas fa-shopping-bag text-[10px]"></i> Salida #<?php echo e($c->id_salida); ?>

                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md text-[11px] font-bold">
                                    <i class="fas fa-award text-[10px]"></i> Bono
                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <span class="font-semibold text-xs text-slate-800">
                                <?php echo e($c->concepto ?? ($c->salida ? "Venta directa de {$c->salida->cantidad} unidad(es)" : "Comisión directa")); ?>

                            </span>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->salida && $c->salida->variante && $c->salida->variante->producto): ?>
                            <span class="text-[11px] text-slate-500 pl-1">
                                <?php echo e($c->salida->variante->producto->nombre); ?> - <?php echo e($c->salida->variante->nombre_variante); ?> (Total salida: $<?php echo e(number_format($c->salida->total, 2)); ?>)
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </td>

                <td class="px-5 py-4 text-right">
                    <span class="font-extrabold text-sm text-slate-900 block">$<?php echo e(number_format($c->monto, 2)); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->porcentaje && $c->porcentaje > 0): ?>
                        <span class="text-[10px] text-slate-400 font-medium">Ref: $<?php echo e(number_format($c->porcentaje, 2)); ?>/ud</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>

                <td class="px-5 py-4 text-center">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->estado === 'Pendiente'): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->es_liquidacion_bloqueada): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 shadow-2xs"
                                  title="<?php echo e($c->motivo_bloqueo_liquidacion); ?>">
                                <i class="fas fa-shield-halved text-amber-600 text-[10px]"></i>
                                En garantía
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Pendiente
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php elseif($c->estado === 'Pagada'): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i class="fas fa-check text-[10px]"></i>
                            Pagada
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                            <i class="fas fa-ban text-[10px]"></i>
                            Cancelada
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>

                <td class="px-5 py-4 text-slate-700 text-xs">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->estado === 'Pagada'): ?>
                        <div class="flex flex-col gap-1">
                            <span class="font-bold text-emerald-800 flex items-center gap-1.5">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->metodo_pago === 'Transferencia Bancaria'): ?>
                                    <i class="fas fa-university text-blue-600"></i>
                                <?php elseif($c->metodo_pago === 'Efectivo'): ?>
                                    <i class="fas fa-money-bill-wave text-emerald-600"></i>
                                <?php elseif($c->metodo_pago === 'Cheque'): ?>
                                    <i class="fas fa-money-check text-indigo-600"></i>
                                <?php else: ?>
                                    <i class="fas fa-wallet text-purple-600"></i>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php echo e($c->metodo_pago ?? 'Efectivo'); ?>

                            </span>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->referencia_pago): ?>
                                <span class="text-[10px] text-slate-500 font-mono">Ref: <?php echo e($c->referencia_pago); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->comprobante_url): ?>
                                <a href="<?php echo e($c->comprobante_url); ?>" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold border border-indigo-200 text-[11px] shadow-2xs transition-all w-fit hover:scale-[1.02]">
                                    <i class="fas fa-file-invoice-dollar text-indigo-600"></i>
                                    <span>Ver Comprobante</span>
                                    <i class="fas fa-arrow-up-right-from-square text-[9px] opacity-70"></i>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->fecha_liquidacion): ?>
                                <span class="text-[10px] text-slate-400">Pagado: <?php echo e($c->fecha_liquidacion->format('d/m/Y')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <span class="text-slate-400 text-xs italic">Por liquidar</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>

                <td class="px-5 py-4 text-slate-500 text-xs max-w-[160px] truncate" title="<?php echo e($c->notas); ?>">
                    <?php echo e($c->notas ?? '—'); ?>

                </td>

                <td class="px-5 py-4">
                    <div class="flex items-center justify-center gap-1.5">
                        
                        <button type="button"
                                onclick="verDetalleComision(<?php echo e($c->id); ?>)"
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 transition-colors cursor-pointer"
                                title="Ver detalle completo de la comisión">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin && $c->estado === 'Pendiente'): ?>
                            
                            <button type="button"
                                    onclick="openEditModal(<?php echo e($c->id); ?>, '<?php echo e($c->monto); ?>', '<?php echo e(addslashes($c->concepto ?? '')); ?>', '<?php echo e(addslashes($c->notas ?? '')); ?>')"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer"
                                    title="Editar monto/concepto">
                                <i class="fas fa-pen text-xs"></i>
                            </button>
                            
                            <button type="button"
                                    onclick="cancelarComision(<?php echo e($c->id); ?>)"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors cursor-pointer"
                                    title="Cancelar comisión">
                                <i class="fas fa-ban text-xs"></i>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <tr>
                <td colspan="<?php echo e($isAdmin ? 8 : 7); ?>" class="px-5 py-14 text-center">
                    <div class="flex flex-col items-center gap-3 text-slate-400">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl">
                            <i class="fas fa-coins"></i>
                        </div>
                        <p class="font-semibold text-slate-500">No hay comisiones registradas</p>
                        <p class="text-sm">Ajusta los filtros o realiza ventas para generar comisiones.</p>
                    </div>
                </td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($comisiones->hasPages()): ?>
<div class="px-5 py-4 border-t border-slate-100">
    <?php echo e($comisiones->links()); ?>

</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/comisiones/partials/table.blade.php ENDPATH**/ ?>