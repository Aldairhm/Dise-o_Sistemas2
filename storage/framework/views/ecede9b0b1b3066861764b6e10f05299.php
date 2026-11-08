<div id="contenedorListaVendedores" class="space-y-4">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $reporte; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php
            $vendedor = $item['vendedor'];
            $comisiones = $item['comisiones'];
            $hasPendiente = $item['total_pendiente'] > 0;
        ?>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-200 hover:border-slate-300"
            id="card-vendedor-<?php echo e($vendedor->id); ?>">

            <!-- HEADER DE LA TARJETA DEL VENDEDOR -->
            <div
                class="p-5 sm:p-6 bg-white flex flex-col lg:flex-row lg:items-center justify-between gap-5 border-b border-slate-100">

                <!-- INFORMACIÓN DEL VENDEDOR -->
                <div class="flex items-center gap-4">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-lg flex items-center justify-center shadow-md shadow-blue-500/20 flex-shrink-0">
                        <?php echo e(strtoupper(substr($vendedor->nombre_real ?: $vendedor->username, 0, 2))); ?>

                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black text-slate-900">
                                <?php echo e($vendedor->nombre_real ?: $vendedor->username); ?></h3>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasPendiente): ?>
                                <span
                                    class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black uppercase tracking-wider">
                                    Pendiente
                                </span>
                            <?php else: ?>
                                <span
                                    class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-wider">
                                    Al día
                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">
                            <?php echo e($vendedor->username); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendedor->telefono): ?>
                                &bull; <?php echo e($vendedor->telefono); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            &bull; <span class="text-slate-500"><?php echo e($item['total_ventas']); ?> comisiones en el
                                período</span>
                        </p>
                    </div>
                </div>

                <!-- RESUMEN DE CIFRAS -->
                <div
                    class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-100 flex-1 max-w-xl">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total
                            Generado</span>
                        <span
                            class="text-base font-black text-slate-900">$<?php echo e(number_format($item['total_comisiones'], 2)); ?></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block">Por
                            Liquidar</span>
                        <span
                            class="text-base font-black text-amber-700">$<?php echo e(number_format($item['total_pendiente'], 2)); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($item['saldo_en_garantia'] ?? 0) > 0): ?>
                            <span class="block text-[10px] font-semibold text-amber-600 truncate"
                                title="$<?php echo e(number_format($item['saldo_en_garantia'], 2)); ?> en período de garantía de devolución">
                                <i class="fas fa-shield-halved text-[9px]"></i>
                                $<?php echo e(number_format($item['saldo_en_garantia'], 2)); ?> en garantía
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Total
                            Pagado</span>
                        <span
                            class="text-base font-black text-emerald-700">$<?php echo e(number_format($item['total_pagada'], 2)); ?></span>
                    </div>
                    <div>
                        <span
                            class="text-[10px] font-bold uppercase tracking-wider text-rose-500 block">Canceladas</span>
                        <span
                            class="text-base font-black text-slate-600">$<?php echo e(number_format($item['total_cancelada'], 2)); ?></span>
                    </div>
                </div>

                <!-- BOTONES DE ACCIÓN (SOLO ADMIN) -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin): ?>
                    <div class="flex items-center gap-2 self-end lg:self-center">
                        <?php
                            $montoLiquidable = $item['saldo_liquidable'] ?? 0;
                            $montoGarantia = $item['saldo_en_garantia'] ?? 0;
                            $montoHistorico = $item['saldo_liquidable_total'] ?? 0;
                            $nombreVendedorSeguro = addslashes($vendedor->nombre_real ?: $vendedor->username);
                            $usernameVendedorSeguro = addslashes($vendedor->username);
                        ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($montoLiquidable > 0): ?>
                            <button type="button"
                                onclick="openLiquidarModal(<?php echo e($vendedor->id); ?>, '<?php echo e($nombreVendedorSeguro); ?>', '<?php echo e($usernameVendedorSeguro); ?>', '<?php echo e($desde); ?>', '<?php echo e($hasta); ?>')"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                                title="Liquidar comisiones de este período">
                                <i class="fas fa-circle-check"></i>
                                <span>Liquidar Período ($<?php echo e(number_format($montoLiquidable, 2)); ?>)</span>
                            </button>
                        <?php elseif($montoGarantia > 0): ?>
                            <button type="button"
                                onclick="openLiquidarModal(<?php echo e($vendedor->id); ?>, '<?php echo e($nombreVendedorSeguro); ?>', '<?php echo e($usernameVendedorSeguro); ?>', '<?php echo e($desde); ?>', '<?php echo e($hasta); ?>')"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-black text-xs shadow-sm transition-all cursor-pointer"
                                title="Comisiones en período de garantía de devolución (aún no liquidables)">
                                <i class="fas fa-shield-halved text-amber-600"></i>
                                <span>En garantía ($<?php echo e(number_format($montoGarantia, 2)); ?>)</span>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($montoLiquidable <= 0 && $montoHistorico > 0): ?>
                            <button type="button"
                                onclick="openLiquidarModal(<?php echo e($vendedor->id); ?>, '<?php echo e($nombreVendedorSeguro); ?>', '<?php echo e($usernameVendedorSeguro); ?>', '', '')"
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors cursor-pointer"
                                title="Liquidar todas las comisiones pendientes históricas disponibles de este vendedor">
                                <i class="fas fa-clock-rotate-left text-slate-500"></i>
                                <span>Histórico ($<?php echo e(number_format($montoHistorico, 2)); ?>)</span>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <!-- TABLA DE DETALLE / HISTORIAL -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                            <th class="px-5 py-3">Fecha y Hora</th>
                            <th class="px-4 py-3">Concepto / Venta</th>
                            <th class="px-4 py-3 text-right">Comisión</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3">Método de Pago</th>
                            <th class="px-4 py-3">Notas</th>
                            <th class="px-5 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $comisiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="hover:bg-slate-50/60 transition-colors <?php echo e($c->monto < 0 ? 'bg-red-50/40' : ''); ?>">
                                
                                <td class="px-5 py-3 font-semibold text-slate-700 whitespace-nowrap">
                                    <?php echo e($c->fecha_registro ? $c->fecha_registro->format('d/m/Y H:i') : '—'); ?>

                                </td>

                                
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->id_salida): ?>
                                            <div
                                                class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                                                <i class="fas fa-receipt"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800">
                                                    <?php echo e($c->salida->variante->producto->nombre ?? 'Venta #' . $c->id_salida); ?>

                                                </p>
                                                <p class="text-[10px] text-slate-400">Salida #<?php echo e($c->id_salida); ?>

                                                    &bull; <?php echo e($c->salida->cantidad ?? 1); ?> uds</p>
                                            </div>
                                        <?php else: ?>
                                            <div
                                                class="w-6 h-6 rounded-lg <?php echo e($c->monto < 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'); ?> flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                                                <i class="fas <?php echo e($c->monto < 0 ? 'fa-arrow-down' : 'fa-gift'); ?>"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800">
                                                    <?php echo e($c->concepto ?? 'Ajuste / Bono'); ?></p>
                                                <p class="text-[10px] text-slate-400">Ajuste manual de saldo</p>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>

                                
                                <td
                                    class="px-4 py-3 text-right font-black whitespace-nowrap <?php echo e($c->monto < 0 ? 'text-red-600' : 'text-slate-900'); ?>">
                                    <?php echo e($c->monto < 0 ? '-$' . number_format(abs($c->monto), 2) : '$' . number_format($c->monto, 2)); ?>

                                </td>

                                
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <?php
                                        $badgeColor = match ($c->estado) {
                                            'Pagada' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Cancelada' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                                        };
                                        $badgeIcon = match ($c->estado) {
                                            'Pagada' => 'fa-circle-check',
                                            'Cancelada' => 'fa-ban',
                                            default => 'fa-clock',
                                        };
                                    ?>
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border <?php echo e($badgeColor); ?>">
                                        <i class="fas <?php echo e($badgeIcon); ?> text-[9px]"></i>
                                        <span><?php echo e($c->estado); ?></span>
                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->estado === 'Pendiente' && $c->es_liquidacion_bloqueada): ?>
                                        <span
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200 mt-1 block"
                                            title="<?php echo e($c->motivo_bloqueo_liquidacion); ?>">
                                            <i class="fas fa-shield-halved text-[8px]"></i> En garantía
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="px-4 py-3 text-slate-600 text-[11px]">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->estado === 'Pagada'): ?>
                                        <div class="space-y-1.5">
                                            <span class="font-bold text-emerald-700 flex items-center gap-1.5 text-xs">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->metodo_pago === 'Transferencia Bancaria'): ?>
                                                    <i class="fas fa-university text-blue-600"></i>
                                                <?php elseif($c->metodo_pago === 'Efectivo'): ?>
                                                    <i class="fas fa-money-bill-wave text-emerald-600"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-money-bill-transfer text-emerald-600"></i>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php echo e($c->metodo_pago ?? 'Efectivo'); ?>

                                            </span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->referencia_pago): ?>
                                                <span class="text-[10px] text-slate-500 font-mono block">Ref:
                                                    <?php echo e($c->referencia_pago); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->comprobante_url): ?>
                                                <a href="<?php echo e($c->comprobante_url); ?>" target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold border border-indigo-200 text-[11px] shadow-2xs transition-all hover:scale-[1.02] cursor-pointer w-fit">
                                                    <i class="fas fa-file-invoice-dollar text-indigo-600"></i>
                                                    <span>Ver Comprobante</span>
                                                    <i class="fas fa-arrow-up-right-from-square text-[9px] opacity-70"></i>
                                                </a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic">Por liquidar</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="px-4 py-3 text-slate-500 text-[11px] max-w-[150px] truncate"
                                    title="<?php echo e($c->notas); ?>">
                                    <?php echo e($c->notas ?? '—'); ?>

                                </td>

                                
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        
                                        <button type="button"
                                            onclick="verDetalleComision(<?php echo e($c->id); ?>)"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 transition-colors cursor-pointer"
                                            title="Ver Detalle Completo">
                                            <i class="fas fa-eye text-[11px]"></i>
                                        </button>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin && $c->estado === 'Pendiente'): ?>
                                            
                                            <button type="button"
                                                onclick="openEditModal(<?php echo e($c->id); ?>, <?php echo e($c->monto); ?>, '<?php echo e(addslashes($c->concepto ?? '')); ?>', '<?php echo e(addslashes($c->notas ?? '')); ?>')"
                                                class="w-7 h-7 rounded-lg flex items-center justify-center text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition-colors cursor-pointer"
                                                title="Editar Monto / Concepto">
                                                <i class="fas fa-pen-to-square text-[11px]"></i>
                                            </button>

                                            
                                            <button type="button" onclick="cancelarComision(<?php echo e($c->id); ?>)"
                                                class="w-7 h-7 rounded-lg flex items-center justify-center text-red-600 hover:text-red-800 hover:bg-red-50 transition-colors cursor-pointer"
                                                title="Cancelar Comisión">
                                                <i class="fas fa-ban text-[11px]"></i>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="7" class="px-5 py-6 text-center text-slate-400">
                                    Sin registros de comisiones para este vendedor en el período.
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center text-slate-400 shadow-sm">
            <i class="fas fa-inbox text-4xl mb-3 text-slate-300"></i>
            <h3 class="text-base font-bold text-slate-700">Sin datos para mostrar</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">No se encontraron comisiones para los filtros
                seleccionados en este período.</p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/comisiones/tabla-por-vendedor.blade.php ENDPATH**/ ?>