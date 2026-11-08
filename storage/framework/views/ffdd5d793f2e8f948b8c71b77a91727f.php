<div id="contenedorListaSemanas" class="space-y-4 scroll-mt-6" aria-live="polite">
    <div id="filtroKpiAnuncio" class="hidden rounded-xl border border-blue-100 bg-blue-50 px-4 py-2.5 text-xs font-bold text-blue-700 flex items-center justify-between gap-3" role="status">
        <span id="filtroKpiAnuncioTexto"></span>
        <button type="button" onclick="aplicarFiltroKpiSemanal('all')" class="text-[11px] underline font-extrabold hover:text-blue-900 cursor-pointer">
            Quitar enfoque
        </button>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $reporteSemanas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <?php
        $comisiones = $sem['comisiones'];
        $vendedoresSemana = $sem['vendedores'];
        $hasPendiente = $sem['total_pendiente'] > 0;
        $fechaDesdeSemana = $sem['inicio_semana']->format('Y-m-d');
        $fechaHastaSemana = $sem['fin_semana']->format('Y-m-d');

        $nombresMeses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        $mesInicio = $nombresMeses[$sem['inicio_semana']->month] ?? '';
        $mesFin = $nombresMeses[$sem['fin_semana']->month] ?? '';
        $vendedoresTexto = strtolower($vendedoresSemana->map(fn($v) => ($v['vendedor']->nombre_real ?? '') . ' ' . ($v['vendedor']->username ?? ''))->implode(' '));
        $searchableText = "semana {$sem['semana_numero']} sem {$sem['semana_numero']} {$sem['semana_numero']} {$sem['year']} {$sem['inicio_semana']->format('d/m/Y')} {$sem['fin_semana']->format('d/m/Y')} {$mesInicio} {$mesFin} {$vendedoresTexto}";
    ?>
    <div id="semana-<?php echo e($sem['key']); ?>"
         data-week-card
         data-week-key="<?php echo e($sem['key']); ?>"
         data-week-number="<?php echo e($sem['semana_numero']); ?>"
         data-week-year="<?php echo e($sem['year']); ?>"
         data-week-searchable="<?php echo e($searchableText); ?>"
         data-week-current="<?php echo e($sem['es_semana_actual'] ? '1' : '0'); ?>"
         data-week-has-pending="<?php echo e($sem['total_pendiente'] > 0 ? '1' : '0'); ?>"
         data-week-has-paid="<?php echo e($comisiones->where('estado', 'Pagada')->isNotEmpty() ? '1' : '0'); ?>"
         class="bg-white border <?php echo e($sem['es_semana_actual'] ? 'border-blue-400 ring-2 ring-blue-500/10' : 'border-slate-200'); ?> rounded-2xl shadow-sm overflow-hidden transition-all duration-200 hover:border-slate-300">
        
        <!-- HEADER DE LA TARJETA DE LA SEMANA (INTERACTIVO / CLICKABLE) -->
        <div onclick="toggleSemana('<?php echo e($sem['key']); ?>')"
             role="button"
             tabindex="0"
             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleSemana('<?php echo e($sem['key']); ?>');}"
             class="p-4 sm:p-5 bg-white flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 cursor-pointer select-none hover:bg-slate-50/70 transition-colors group">
            
            <!-- INFORMACIÓN DE LA SEMANA -->
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-12 h-12 rounded-2xl <?php echo e($sem['es_semana_actual'] ? 'bg-gradient-to-tr from-blue-600 to-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700'); ?> font-black text-center flex flex-col items-center justify-center flex-shrink-0 transition-transform group-hover:scale-105">
                    <span class="text-[9px] uppercase tracking-wider <?php echo e($sem['es_semana_actual'] ? 'text-blue-100' : 'text-slate-400'); ?> leading-none">SEM</span>
                    <span class="text-lg leading-tight"><?php echo e($sem['semana_numero']); ?></span>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-base font-black text-slate-900 tracking-tight">
                            Semana <?php echo e($sem['semana_numero']); ?>

                        </h3>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sem['es_semana_actual']): ?>
                            <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-black uppercase tracking-wider flex items-center gap-1 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                En curso
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sem['total_pendiente'] <= 0 && $sem['total_comisiones'] > 0): ?>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-wider">
                                <i class="fas fa-check text-[9px]"></i> Liquidada
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5 flex items-center gap-1.5 flex-wrap">
                        <i class="fas fa-calendar-week text-slate-400 text-[11px]"></i>
                        <span><?php echo e($sem['inicio_semana']->format('d/m/Y')); ?></span>
                        <span class="text-slate-300">—</span>
                        <span><?php echo e($sem['fin_semana']->format('d/m/Y')); ?></span>
                        <span class="text-slate-300 hidden sm:inline">·</span>
                        <span class="text-slate-400 font-medium text-[11px] hidden sm:inline">
                            <?php echo e($vendedoresSemana->count()); ?> <?php echo e($vendedoresSemana->count() === 1 ? 'vendedor' : 'vendedores'); ?>

                        </span>
                    </p>
                </div>
            </div>

            <!-- RESUMEN DE CIFRAS DE LA SEMANA -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 bg-slate-50/90 p-3 rounded-xl border border-slate-100 flex-1 max-w-xl">
                <div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Total Semana</span>
                    <span class="text-sm sm:text-base font-black text-slate-900">$<?php echo e(number_format($sem['total_comisiones'], 2)); ?></span>
                </div>
                <div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-600 block">Por Liquidar</span>
                    <span class="text-sm sm:text-base font-black text-amber-700">$<?php echo e(number_format($sem['total_pendiente'], 2)); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($sem['saldo_en_garantia'] ?? 0) > 0): ?>
                        <span class="block text-[10px] font-semibold text-amber-600 truncate" title="$<?php echo e(number_format($sem['saldo_en_garantia'], 2)); ?> en período de garantía de devolución">
                            <i class="fas fa-shield-halved text-[9px]"></i> $<?php echo e(number_format($sem['saldo_en_garantia'], 2)); ?> en garantía
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-600 block">Pagado</span>
                    <span class="text-sm sm:text-base font-black text-emerald-700">$<?php echo e(number_format($sem['total_pagada'], 2)); ?></span>
                </div>
                <div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Ventas</span>
                    <span class="text-sm sm:text-base font-black text-slate-700"><?php echo e($sem['total_ventas']); ?></span>
                </div>
            </div>

            <!-- BOTÓN ACCIÓN DE DESPLIEGUE / COLAPSO -->
            <div class="flex items-center gap-2 self-end lg:self-center flex-shrink-0" onclick="event.stopPropagation()">
                <button type="button"
                        onclick="toggleSemana('<?php echo e($sem['key']); ?>')"
                        id="btn-toggle-semana-<?php echo e($sem['key']); ?>"
                        aria-expanded="<?php echo e($sem['es_semana_actual'] ? 'true' : 'false'); ?>"
                        class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 text-slate-700 text-xs font-black transition-all cursor-pointer shadow-2xs group-hover:border-blue-300 group-hover:text-blue-600">
                    <span id="btn-text-semana-<?php echo e($sem['key']); ?>"><?php echo e($sem['es_semana_actual'] ? 'Plegar' : 'Desplegar'); ?></span>
                    <i id="chevron-semana-<?php echo e($sem['key']); ?>" class="fas fa-chevron-down text-[11px] transition-transform duration-300 <?php echo e($sem['es_semana_actual'] ? 'rotate-180' : ''); ?>"></i>
                </button>
            </div>

        </div>

        <!-- CUERPO DE LA SEMANA (COLAPSABLE / DESPLEGABLE) -->
        <div id="semana-body-<?php echo e($sem['key']); ?>" class="<?php echo e($sem['es_semana_actual'] ? '' : 'hidden'); ?> bg-slate-50/40 p-4 sm:p-5 border-t border-slate-100 space-y-4 transition-all">
            
            <div class="space-y-2">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                            <i class="fas fa-users text-slate-400"></i>
                            <span>Vendedores en esta semana</span>
                            <span class="px-1.5 py-0.2 rounded-md bg-slate-200/70 text-slate-600 text-[10px] font-black"><?php echo e($vendedoresSemana->count()); ?></span>
                        </h4>
                    </div>

                    <div class="relative w-full sm:w-64">
                            <input type="text"
                                   placeholder="Buscar vendedor..."
                                   oninput="buscarEnCarruselSemana('<?php echo e($sem['key']); ?>', this.value)"
                                   class="w-full pl-9 pr-3 py-2 text-xs font-medium rounded-lg border border-slate-200 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendedoresSemana->isNotEmpty()): ?>
                    <div class="space-y-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $vendedoresSemana; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vItem['vendedor']): ?>
                        <?php
                            $vId = $vItem['vendedor']->id;
                            $vNombre = $vItem['vendedor']->nombre_real ?: $vItem['vendedor']->username;
                            $vUsername = $vItem['vendedor']->username ?? '';
                            $detalleId = 'desglose-' . $sem['key'] . '-' . $vId;
                        ?>
                        <div data-vendedor-card="<?php echo e($vId); ?>"
                             data-vendedor-has-pending="<?php echo e($vItem['pendiente'] > 0 ? '1' : '0'); ?>"
                             data-vendedor-has-paid="<?php echo e($vItem['comisiones']->where('estado', 'Pagada')->isNotEmpty() ? '1' : '0'); ?>"
                             data-vendedor-nombre="<?php echo e(strtolower($vNombre)); ?>"
                             data-vendedor-username="<?php echo e(strtolower($vUsername)); ?>"
                             class="vendedor-card-<?php echo e($sem['key']); ?> space-y-2">
                          <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs grid grid-cols-1 lg:grid-cols-[minmax(210px,1.2fr)_minmax(340px,2fr)_auto] items-center gap-4 transition-all duration-200">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm font-black flex-shrink-0 shadow-sm">
                                    <?php echo e(strtoupper(substr($vNombre, 0, 2))); ?>

                                </div>
                                <div class="min-w-0">
                                    <p class="font-black text-sm text-slate-900 truncate"><?php echo e($vNombre); ?></p>
                                    <p class="text-[11px] text-slate-500 truncate"><?php echo e($vUsername); ?></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 rounded-xl border border-slate-100 px-3.5 py-3">
                                <div>
                                    <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Ganado semana</span>
                                    <span class="text-sm font-black text-slate-900">$<?php echo e(number_format($vItem['total'], 2)); ?></span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-black uppercase tracking-wider text-amber-600">Por liquidar</span>
                                    <span class="text-sm font-black text-amber-700">$<?php echo e(number_format($vItem['pendiente'], 2)); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($vItem['saldo_en_garantia'] ?? 0) > 0): ?>
                                        <span class="block text-[10px] font-semibold text-amber-600" title="En período de garantía de devolución">
                                            <i class="fas fa-shield-halved text-[9px]"></i> $<?php echo e(number_format($vItem['saldo_en_garantia'], 2)); ?> en garantía
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-black uppercase tracking-wider text-emerald-600">Pagado</span>
                                    <span class="text-sm font-black text-emerald-700">$<?php echo e(number_format($vItem['pagada'], 2)); ?></span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Ventas</span>
                                    <span class="text-sm font-black text-slate-700"><?php echo e($vItem['ventas']); ?></span>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin): ?>
                                    <?php
                                        $montoLiquidable = $vItem['saldo_liquidable'] ?? 0;
                                        $montoGarantia   = $vItem['saldo_en_garantia'] ?? 0;
                                        $montoHistorico  = $vItem['saldo_liquidable_total'] ?? 0;
                                    ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($montoLiquidable > 0): ?>
                                        <button type="button"
                                                onclick="abrirLiquidacionSemanal(<?php echo e($vId); ?>, '<?php echo e(addslashes($vNombre)); ?>', '<?php echo e(addslashes($vUsername)); ?>', '<?php echo e($fechaDesdeSemana); ?>', '<?php echo e($fechaHastaSemana); ?>')"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-extrabold transition-colors cursor-pointer whitespace-nowrap shadow-xs"
                                                title="Liquidar comisiones elegibles para este vendedor en esta semana">
                                            <i class="fas fa-circle-check"></i>
                                            <span>Liquidar ($<?php echo e(number_format($montoLiquidable, 2)); ?>)</span>
                                        </button>
                                    <?php elseif($montoGarantia > 0): ?>
                                        <button type="button"
                                                onclick="abrirLiquidacionSemanal(<?php echo e($vId); ?>, '<?php echo e(addslashes($vNombre)); ?>', '<?php echo e(addslashes($vUsername)); ?>', '<?php echo e($fechaDesdeSemana); ?>', '<?php echo e($fechaHastaSemana); ?>')"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 text-[11px] font-extrabold transition-colors cursor-pointer whitespace-nowrap shadow-xs"
                                                title="Comisiones en período de garantía de devolución (aún no liquidables)">
                                            <i class="fas fa-shield-halved text-amber-600"></i>
                                            <span>En garantía ($<?php echo e(number_format($montoGarantia, 2)); ?>)</span>
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($montoLiquidable <= 0 && $montoHistorico > 0): ?>
                                        <button type="button"
                                                onclick="abrirLiquidacionSemanal(<?php echo e($vId); ?>, '<?php echo e(addslashes($vNombre)); ?>', '<?php echo e(addslashes($vUsername)); ?>', '', '')"
                                                class="inline-flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition-colors cursor-pointer whitespace-nowrap"
                                                title="Ver y liquidar comisiones pendientes disponibles de otros periodos">
                                            <i class="fas fa-clock-rotate-left text-slate-500"></i>
                                            <span>Histórico ($<?php echo e(number_format($montoHistorico, 2)); ?>)</span>
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <button type="button"
                                        onclick="toggleDesgloseVendedor('<?php echo e($detalleId); ?>')"
                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition-colors cursor-pointer whitespace-nowrap">
                                    <i class="fas fa-list-ul text-slate-500"></i>
                                    <span>Desglose (<?php echo e($vItem['cantidad']); ?>)</span>
                                    <i id="icon-<?php echo e($detalleId); ?>" class="fas fa-chevron-down text-[9px] transition-transform"></i>
                                </button>
                            </div>
                          </div>

                          <div id="<?php echo e($detalleId); ?>" class="hidden overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-2xs">
                            <table class="w-full min-w-[850px] text-xs">
                              <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-bold">
                                  <th class="px-4 py-3 text-left">Fecha y Hora</th>
                                  <th class="px-4 py-3 text-left">Concepto / Venta</th>
                                  <th class="px-4 py-3 text-right">Comisión</th>
                                  <th class="px-4 py-3 text-center">Estado</th>
                                  <th class="px-4 py-3 text-left">Detalle de Pago</th>
                                  <th class="px-4 py-3 text-left">Notas</th>
                                  <th class="px-4 py-3 text-center">Acciones</th>
                                </tr>
                              </thead>
                              <tbody class="divide-y divide-slate-100">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $vItem['comisiones']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                  <tr class="hover:bg-slate-50 transition-colors <?php echo e($c->monto < 0 ? 'bg-red-50/40' : ''); ?>">
                                    <td class="px-4 py-3 text-slate-600 font-semibold whitespace-nowrap">
                                      <?php echo e($c->fecha_registro ? $c->fecha_registro->format('d/m/Y H:i') : '—'); ?>

                                    </td>
                                    <td class="px-4 py-3">
                                      <div class="flex items-center gap-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->id_salida): ?>
                                          <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                                            <i class="fas fa-receipt"></i>
                                          </div>
                                          <div>
                                            <p class="font-bold text-slate-800"><?php echo e($c->salida->variante->producto->nombre ?? 'Venta #' . $c->id_salida); ?></p>
                                            <p class="text-[10px] text-slate-400">Salida #<?php echo e($c->id_salida); ?> &bull; <?php echo e($c->salida->cantidad ?? 1); ?> uds</p>
                                          </div>
                                        <?php else: ?>
                                          <div class="w-6 h-6 rounded-lg <?php echo e($c->monto < 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'); ?> flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                                            <i class="fas <?php echo e($c->monto < 0 ? 'fa-arrow-down' : 'fa-gift'); ?>"></i>
                                          </div>
                                          <div>
                                            <p class="font-bold text-slate-800"><?php echo e($c->concepto ?? 'Ajuste / Bono'); ?></p>
                                            <p class="text-[10px] text-slate-400">Ajuste manual de saldo</p>
                                          </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                      </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-black whitespace-nowrap <?php echo e($c->monto < 0 ? 'text-red-600' : 'text-slate-900'); ?>">
                                      <?php echo e($c->monto < 0 ? '-$' . number_format(abs($c->monto), 2) : '$' . number_format($c->monto, 2)); ?>

                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                      <?php
                                        $badgeColor = match($c->estado) {
                                            'Pagada'    => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Cancelada' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            default     => 'bg-amber-50 text-amber-700 border-amber-200',
                                        };
                                        $badgeIcon = match($c->estado) {
                                            'Pagada'    => 'fa-circle-check',
                                            'Cancelada' => 'fa-ban',
                                            default     => 'fa-clock',
                                        };
                                      ?>
                                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border <?php echo e($badgeColor); ?>">
                                        <i class="fas <?php echo e($badgeIcon); ?> text-[9px]"></i>
                                        <span><?php echo e($c->estado); ?></span>
                                      </span>
                                      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->estado === 'Pendiente' && $c->es_liquidacion_bloqueada): ?>
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200 mt-0.5 block" title="<?php echo e($c->motivo_bloqueo_liquidacion); ?>">
                                          <i class="fas fa-shield-halved text-[8px]"></i> En garantía
                                        </span>
                                      <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-[11px]">
                                      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->estado === 'Pagada'): ?>
                                        <div class="space-y-1">
                                          <span class="font-bold text-emerald-700 flex items-center gap-1">
                                            <i class="fas fa-check-circle text-xs"></i>
                                            <?php echo e($c->metodo_pago ?? 'Efectivo'); ?>

                                          </span>
                                          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->referencia_pago): ?>
                                            <span class="text-[10px] text-slate-500 font-mono block">Ref: <?php echo e($c->referencia_pago); ?></span>
                                          <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->comprobante_url): ?>
                                            <a href="<?php echo e($c->comprobante_url); ?>" target="_blank"
                                               class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                              <i class="fas fa-file-invoice-dollar"></i> Comprobante
                                            </a>
                                          <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                      <?php else: ?>
                                        <span class="text-slate-400 italic">Por liquidar</span>
                                      <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500 text-[11px] max-w-[150px] truncate" title="<?php echo e($c->notas); ?>">
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
                                          <button type="button"
                                                  onclick="cancelarComision(<?php echo e($c->id); ?>)"
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
                                    <td colspan="7" class="px-4 py-4 text-center text-slate-400">Sin comisiones</td>
                                  </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-white p-6 rounded-xl border border-dashed border-slate-200 text-center text-slate-400">
                        <i class="fas fa-users-slash text-2xl mb-1 text-slate-300"></i>
                        <p class="text-xs font-semibold">No se encontraron comisiones para los vendedores en esta semana.</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

        </div>

    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center text-slate-400 shadow-sm">
        <i class="fas fa-calendar-xmark text-4xl mb-3 text-slate-300"></i>
        <h3 class="text-base font-bold text-slate-700">No hay semanas registradas</h3>
        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">No se encontraron comisiones ni cortes semanales para el año y vendedor seleccionados.</p>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/comisiones/tabla-por-semana.blade.php ENDPATH**/ ?>