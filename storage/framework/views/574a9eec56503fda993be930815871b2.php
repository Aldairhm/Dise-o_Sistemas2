<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Comisiones por Vendedor | AXStore']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Comisiones por Vendedor | AXStore']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php
        if ($periodo === 'mes') {
            $fechaDesdeFiltro = \Carbon\Carbon::createFromDate($anio, $mes, 1)->startOfMonth()->toDateString();
            $fechaHastaFiltro = \Carbon\Carbon::createFromDate($anio, $mes, 1)->endOfMonth()->toDateString();
        } else {
            $fechaDesdeFiltro = $desde ?? '';
            $fechaHastaFiltro = $hasta ?? '';
        }
    ?>
    <input type="hidden" id="filtroFechaDesdeActiva" value="<?php echo e($fechaDesdeFiltro); ?>">
    <input type="hidden" id="filtroFechaHastaActiva" value="<?php echo e($fechaHastaFiltro); ?>">

    <div class="max-w-[1440px] mx-auto space-y-6">

        <!-- HEADER DEL DASHBOARD -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="<?php echo e(route('home')); ?>"
                    class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3.5">
                    <div
                        class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-600/20">
                        <i class="fas fa-users-gear text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-indigo-600 mb-0.5">Analítica de
                            Rendimiento</p>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                            Comisiones por Vendedor
                        </h1>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN RÁPIDA -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin): ?>
                <div class="flex items-center gap-3 flex-wrap">
                    <button type="button" onclick="openLiquidarModal(null, '', '', '<?php echo e($fechaDesdeFiltro); ?>', '<?php echo e($fechaHastaFiltro); ?>')"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                        <i class="fas fa-circle-check"></i>
                        <span>LIQUIDAR PAGO</span>
                    </button>
                    <button type="button" onclick="openCreateModal()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-lg shadow-blue-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                        <i class="fas fa-plus"></i>
                        <span>NUEVO BONO</span>
                    </button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- SUB-NAV / PESTAÑAS (SOLO COMISIONES) -->
        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm"
            aria-label="Secciones de comisiones">
            <a href="<?php echo e(route('comisiones.porSemana')); ?>"
                class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-calendar-week text-slate-400"></i>
                <span>Comisiones por Semana</span>
            </a>
            <a href="<?php echo e(route('comisiones.porVendedor')); ?>"
                class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-users-gear"></i>
                <span>Comisiones por Vendedor</span>
            </a>
            <a href="<?php echo e(route('comisiones.index')); ?>"
                class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-coins text-slate-400"></i>
                <span>Comisiones</span>
            </a>
            <a href="<?php echo e(route('comisiones.ajustes')); ?>"
                class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-arrows-rotate text-slate-400"></i>
                <span>Ajustes</span>
            </a>
        </nav>

        <!-- SELECTOR DE MODO: VISTA MENSUAL VS HISTORIAL COMPLETO -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl border border-slate-200/80 w-fit">
                <a href="<?php echo e(route('comisiones.porVendedor', array_merge(request()->except(['periodo', 'mes', 'anio', 'fecha_desde', 'fecha_hasta']), ['periodo' => 'mes', 'mes' => now()->month, 'anio' => now()->year]))); ?>"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2 <?php echo e($periodo === 'mes' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900'); ?>">
                    <i
                        class="fas fa-calendar-alt text-[11px] <?php echo e($periodo === 'mes' ? 'text-blue-600' : 'text-slate-400'); ?>"></i>
                    <span>Resumen por Mes</span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodo === 'mes'): ?>
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </a>
                <a href="<?php echo e(route('comisiones.porVendedor', array_merge(request()->except(['periodo', 'mes', 'anio']), ['periodo' => 'historico']))); ?>"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2 <?php echo e($periodo === 'historico' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900'); ?>">
                    <i
                        class="fas fa-clock-rotate-left text-[11px] <?php echo e($periodo === 'historico' ? 'text-blue-600' : 'text-slate-400'); ?>"></i>
                    <span>Historial de Todos</span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodo === 'historico'): ?>
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </a>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodo === 'mes'): ?>
                
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="<?php echo e(route('comisiones.porVendedor', array_merge(request()->all(), ['periodo' => 'mes', 'mes' => $mesAnterior->month, 'anio' => $mesAnterior->year]))); ?>"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition-all"
                        title="Mes anterior (<?php echo e($nombresMeses[$mesAnterior->month]); ?>)">
                        <i class="fas fa-chevron-left text-[10px]"></i>
                        <span class="hidden sm:inline"><?php echo e($nombresMeses[$mesAnterior->month]); ?></span>
                    </a>

                    <div
                        class="px-3 py-1 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 font-black text-xs flex items-center gap-1.5">
                        <i class="fas fa-calendar-check text-blue-600"></i>
                        <span><?php echo e($mesNombre); ?> <?php echo e($anio); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($esMesActual): ?>
                            <span
                                class="px-1.5 py-0.2 rounded-md bg-blue-600 text-white text-[9px] font-black uppercase tracking-wider">Mes
                                Actual</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <a href="<?php echo e(route('comisiones.porVendedor', array_merge(request()->all(), ['periodo' => 'mes', 'mes' => $mesSiguiente->month, 'anio' => $mesSiguiente->year]))); ?>"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition-all"
                        title="Mes siguiente (<?php echo e($nombresMeses[$mesSiguiente->month]); ?>)">
                        <span class="hidden sm:inline"><?php echo e($nombresMeses[$mesSiguiente->month]); ?></span>
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </a>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$esMesActual): ?>
                        <a href="<?php echo e(route('comisiones.porVendedor', array_merge(request()->except(['mes', 'anio']), ['periodo' => 'mes', 'mes' => now()->month, 'anio' => now()->year]))); ?>"
                            class="px-2.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all flex items-center gap-1 shadow-2xs"
                            title="Ir al mes actual">
                            <i class="fas fa-rotate-left text-[10px]"></i>
                            <span>Mes Actual</span>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="text-xs font-semibold text-slate-500 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Visualizando acumulado histórico de todos los tiempos</span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- FILTROS GLOBALES -->
        <section class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-sm mb-6 transition-all hover:shadow-md">
            <form method="GET" action="<?php echo e(route('comisiones.porVendedor')); ?>"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3.5 items-end">
                <input type="hidden" name="periodo" value="<?php echo e($periodo); ?>">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin): ?>
                    
                    <div class="sm:col-span-2 md:col-span-4">
                        <label for="q" class="mb-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-tight">
                            <i class="fas fa-magnifying-glass text-blue-600 text-xs"></i>
                            <span>Buscar Vendedor</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="q" id="q" value="<?php echo e($busqueda ?? ''); ?>"
                                placeholder="Nombre o correo del vendedor..."
                                class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50/90 pl-9 pr-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                            <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodo === 'mes'): ?>
                    
                    <div class="sm:col-span-1 <?php echo e($isAdmin ? 'md:col-span-3' : 'md:col-span-5'); ?> relative" data-custom-vendedor-select="mes">
                        <label class="mb-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-tight">
                            <i class="fas fa-calendar-alt text-blue-600 text-xs"></i>
                            <span>Mes</span>
                        </label>
                        <?php
                            $curMesNombre = $nombresMeses[(int)$mes] ?? 'Mes';
                        ?>
                        <input type="hidden" id="mes" name="mes" value="<?php echo e((int)$mes); ?>">
                        <button type="button" class="h-10 w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50/90 px-3.5 text-xs font-bold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 flex items-center justify-between gap-2 cursor-pointer text-left" data-toggle aria-expanded="false">
                            <span class="flex items-center gap-2 truncate">
                                <i class="fas fa-calendar-alt text-blue-600" data-icon></i>
                                <span class="truncate" data-label><?php echo e($curMesNombre); ?></span>
                            </span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" data-arrow></i>
                        </button>
                        <div class="hidden absolute left-0 top-full z-50 mt-1.5 w-full min-w-[190px] rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xl max-h-64 overflow-y-auto" data-menu>
                            <div class="space-y-0.5">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $nombresMeses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mNum => $mNombre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php
                                        $isSelected = ((int) $mes === $mNum);
                                        $isActual = ((int) now()->month === $mNum && (int) now()->year === (int) $anio);
                                    ?>
                                    <button type="button" data-option data-value="<?php echo e($mNum); ?>" data-label="<?php echo e($mNombre); ?>" data-icon="fas fa-calendar-alt text-blue-600"
                                        class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left <?php echo e($isSelected ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900'); ?>">
                                        <span class="flex items-center gap-2 truncate">
                                            <i class="fas fa-calendar-day <?php echo e($isActual ? 'text-amber-500' : 'text-slate-400'); ?>"></i>
                                            <span><?php echo e($mNombre); ?></span>
                                        </span>
                                        <div class="flex items-center gap-1.5">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActual): ?>
                                                <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">Actual</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <i class="fas fa-check text-blue-600 text-[10px] <?php echo e($isSelected ? '' : 'hidden'); ?>" data-check></i>
                                        </div>
                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="sm:col-span-1 <?php echo e($isAdmin ? 'md:col-span-3' : 'md:col-span-5'); ?> relative" data-custom-vendedor-select="anio">
                        <label class="mb-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-tight">
                            <i class="fas fa-calendar text-blue-600 text-xs"></i>
                            <span>Año</span>
                        </label>
                        <input type="hidden" id="anio" name="anio" value="<?php echo e((int)$anio); ?>">
                        <button type="button" class="h-10 w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50/90 px-3.5 text-xs font-bold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 flex items-center justify-between gap-2 cursor-pointer text-left" data-toggle aria-expanded="false">
                            <span class="flex items-center gap-2 truncate">
                                <i class="fas fa-calendar text-blue-600" data-icon></i>
                                <span class="truncate" data-label>Año <?php echo e($anio); ?></span>
                            </span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" data-arrow></i>
                        </button>
                        <div class="hidden absolute left-0 top-full z-50 mt-1.5 w-full min-w-[170px] rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xl" data-menu>
                            <div class="space-y-0.5">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($y = now()->year; $y >= now()->year - 3; $y--): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php $isSelected = ((int)$anio === $y); ?>
                                    <button type="button" data-option data-value="<?php echo e($y); ?>" data-label="Año <?php echo e($y); ?>" data-icon="fas fa-calendar text-blue-600"
                                        class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left <?php echo e($isSelected ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900'); ?>">
                                        <span class="flex items-center gap-2">
                                            <i class="fas fa-calendar text-blue-500"></i>
                                            <span>Año <?php echo e($y); ?></span>
                                        </span>
                                        <i class="fas fa-check text-blue-600 text-[10px] <?php echo e($isSelected ? '' : 'hidden'); ?>" data-check></i>
                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    
                    <div class="<?php echo e($isAdmin ? 'md:col-span-3' : 'md:col-span-5'); ?>">
                        <label for="fecha_desde" class="mb-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-tight">
                            <i class="fas fa-calendar-day text-blue-600 text-xs"></i> <span>Desde</span>
                        </label>
                        <input type="date" name="fecha_desde" id="fecha_desde" value="<?php echo e($desde); ?>"
                            class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50/90 px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                    </div>

                    
                    <div class="<?php echo e($isAdmin ? 'md:col-span-3' : 'md:col-span-5'); ?>">
                        <label for="fecha_hasta" class="mb-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-tight">
                            <i class="fas fa-calendar-check text-blue-600 text-xs"></i> <span>Hasta</span>
                        </label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" value="<?php echo e($hasta); ?>"
                            class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50/90 px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="md:col-span-2 flex items-center gap-2">
                    <button type="submit"
                        class="h-10 w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 text-xs transition-all shadow-md shadow-blue-600/20 active:scale-95 flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fas fa-filter text-xs"></i>
                        <span>Filtrar</span>
                    </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($desde || $hasta || $busqueda || ($periodo === 'mes' && (!$esMesActual || $anio != now()->year))): ?>
                        <a href="<?php echo e(route('comisiones.porVendedor', ['periodo' => $periodo])); ?>"
                            class="h-10 rounded-xl border border-slate-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-slate-500 px-3 text-xs transition-all flex items-center justify-center"
                            title="Restablecer filtros">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </form>
        </section>

        <!-- KPI CARDS GLOBALES -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- TOTAL GENERADO -->
            <div
                class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">
                        <?php echo e($periodo === 'mes' ? "Total {$mesNombre} {$anio}" : "Total Histórico"); ?>

                    </span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                        <i class="fas fa-vault"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">
                        $<?php echo e(number_format($kpis['total'], 2)); ?></h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span><?php echo e($kpis['vendedores']); ?> vendedores activos en periodo</span>
                    </p>
                </div>
            </div>

            <!-- POR PAGAR (PENDIENTE) -->
            <div
                class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">
                        <?php echo e($periodo === 'mes' ? "Por Liquidar ({$mesNombre})" : "Por Liquidar (Histórico)"); ?>

                    </span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-amber-700 tracking-tight">
                        $<?php echo e(number_format($kpis['pendiente'], 2)); ?></h3>
                    <p class="text-xs font-semibold text-amber-600 mt-1">
                        <?php echo e($periodo === 'mes' ? "Saldo pendiente de {$mesNombre}" : "Saldo pendiente acumulado"); ?>

                    </p>
                </div>
            </div>

            <!-- PAGADAS / LIQUIDADAS -->
            <div
                class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">
                        <?php echo e($periodo === 'mes' ? "Liquidado ({$mesNombre})" : "Total Liquidado"); ?>

                    </span>
                    <div
                        class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-emerald-700 tracking-tight">
                        $<?php echo e(number_format($kpis['pagada'], 2)); ?></h3>
                    <p class="text-xs font-semibold text-emerald-600 mt-1">Total transferido / entregado</p>
                </div>
            </div>

            <!-- CANCELADAS -->
            <div
                class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">
                        <?php echo e($periodo === 'mes' ? "Canceladas ({$mesNombre})" : "Canceladas"); ?>

                    </span>
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">
                        $<?php echo e(number_format($kpis['cancelada'], 2)); ?></h3>
                    <p class="text-xs font-semibold text-slate-400 mt-1">Por ventas anuladas</p>
                </div>
            </div>
        </div>

        <!-- LISTA DE VENDEDORES CON HISTORIAL Y DESGLOSE -->
        <div class="space-y-4">
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
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h2 class="text-lg font-black text-slate-900 tracking-tight">
                                        <?php echo e($vendedor->nombre_real ?: $vendedor->username); ?></h2>
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider <?php echo e($vendedor->rol === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'); ?>">
                                        <?php echo e($vendedor->rol); ?>

                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium mt-0.5 flex items-center gap-2">
                                    <span><i
                                            class="fas fa-envelope text-slate-400 mr-1"></i><?php echo e($vendedor->username); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendedor->telefono): ?>
                                        <span><i class="fas fa-phone text-slate-400 mr-1"></i><?php echo e($vendedor->telefono); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <!-- RESUMEN DE CIFRAS DEL VENDEDOR -->
                        <div
                            class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50/80 p-3.5 rounded-2xl border border-slate-100 flex-1 max-w-2xl">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                                    <?php echo e($periodo === 'mes' ? "Ganado ({$mesNombre})" : "Total Ganado"); ?>

                                </span>
                                <span
                                    class="text-base font-black text-slate-900">$<?php echo e(number_format($item['total_comisiones'], 2)); ?></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block">Por
                                    Liquidar</span>
                                <span
                                    class="text-base font-black text-amber-700">$<?php echo e(number_format($item['total_pendiente'], 2)); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($item['saldo_garantia_total'] ?? 0) > 0): ?>
                                    <span class="text-[9px] text-amber-800 font-semibold block leading-tight mt-0.5"
                                        title="En período de garantía de devolución (aún no liquidable)">
                                        <i class="fas fa-shield-halved text-[8px] text-amber-600"></i> En garantía: $<?php echo e(number_format($item['saldo_garantia_total'], 2)); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodo === 'mes' && $item['saldo_pendiente_total'] > $item['total_pendiente']): ?>
                                    <span class="text-[9px] text-slate-500 font-semibold block leading-tight mt-0.5"
                                        title="Incluye pendientes de meses anteriores">
                                        Total acum: $<?php echo e(number_format($item['saldo_pendiente_total'], 2)); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div>
                                <span
                                    class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Pagado</span>
                                <span
                                    class="text-base font-black text-emerald-700">$<?php echo e(number_format($item['total_pagada'], 2)); ?></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                                    <?php echo e($periodo === 'mes' ? "Ventas en {$mesNombre}" : "Ventas/Registros"); ?>

                                </span>
                                <span class="text-base font-black text-slate-700"><?php echo e($item['total_ventas']); ?></span>
                            </div>
                        </div>

                        <!-- BOTONES DE ACCIÓN DE ESTE VENDEDOR -->
                        <div class="flex items-center gap-2 flex-wrap justify-end">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin && ($hasPendiente || ($item['saldo_pendiente_total'] ?? 0) > 0)): ?>
                                <?php
                                    if ($periodo === 'mes') {
                                        $montoLiquidable = $item['saldo_liquidable'] ?? 0;
                                        $montoGarantia   = $item['saldo_en_garantia'] ?? 0;
                                    } else {
                                        $montoLiquidable = $item['saldo_liquidable_total'] ?? 0;
                                        $montoGarantia   = $item['saldo_garantia_total'] ?? 0;
                                    }
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($montoLiquidable > 0): ?>
                                    <button type="button" onclick="liquidarDirectoVendedor(<?php echo e($vendedor->id); ?>, '<?php echo e(addslashes($vendedor->nombre_real)); ?>', '<?php echo e(addslashes($vendedor->username)); ?>', '<?php echo e($fechaDesdeFiltro); ?>', '<?php echo e($fechaHastaFiltro); ?>')"
                                        class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all cursor-pointer flex items-center gap-1.5"
                                        title="Liquidar comisiones elegibles para este vendedor en el período">
                                        <i class="fas fa-circle-check"></i>
                                        <span>Liquidar ($<?php echo e(number_format($montoLiquidable, 2)); ?>)</span>
                                    </button>
                                <?php elseif($montoGarantia > 0): ?>
                                    <button type="button" onclick="liquidarDirectoVendedor(<?php echo e($vendedor->id); ?>, '<?php echo e(addslashes($vendedor->nombre_real)); ?>', '<?php echo e(addslashes($vendedor->username)); ?>', '<?php echo e($fechaDesdeFiltro); ?>', '<?php echo e($fechaHastaFiltro); ?>')"
                                        class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5"
                                        title="Comisiones en período de garantía de devolución (aún no liquidables)">
                                        <i class="fas fa-shield-halved text-amber-600"></i>
                                        <span>En garantía ($<?php echo e(number_format($montoGarantia, 2)); ?>)</span>
                                    </button>
                                <?php elseif(($item['saldo_liquidable_total'] ?? 0) > 0): ?>
                                    <button type="button" onclick="liquidarDirectoVendedor(<?php echo e($vendedor->id); ?>, '<?php echo e(addslashes($vendedor->nombre_real)); ?>', '<?php echo e(addslashes($vendedor->username)); ?>', '', '')"
                                        class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5"
                                        title="Ver y liquidar comisiones pendientes de otros periodos">
                                        <i class="fas fa-clock-rotate-left text-slate-500"></i>
                                        <span>Histórico ($<?php echo e(number_format($item['saldo_liquidable_total'], 2)); ?>)</span>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <button type="button" onclick="toggleDesgloseVendedor(<?php echo e($vendedor->id); ?>)"
                                class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5">
                                <i class="fas fa-list-ul text-slate-500"></i>
                                <span>Desglose
                                    <?php echo e($periodo === 'mes' ? "({$mesNombre}: {$comisiones->count()})" : "({$comisiones->count()})"); ?></span>
                                <i class="fas fa-chevron-down text-[10px] transition-transform duration-200"
                                    id="icon-chevron-<?php echo e($vendedor->id); ?>"></i>
                            </button>
                        </div>
                    </div>

                    <!-- DESGLOSE EXPANDIBLE DE TODAS LAS VENTAS / COMISIONES -->
                    <div id="desglose-vendedor-<?php echo e($vendedor->id); ?>"
                        class="hidden bg-slate-50/40 p-4 sm:p-5 border-t border-slate-100">
                        <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-2xs">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr
                                        class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-bold">
                                        <th class="px-4 py-3 text-left">Fecha y Hora</th>
                                        <th class="px-4 py-3 text-left">Concepto / Detalle de la Venta</th>
                                        <th class="px-4 py-3 text-right">Comisión</th>
                                        <th class="px-4 py-3 text-center">Estado</th>
                                        <th class="px-4 py-3 text-left">Detalle de Pago</th>
                                        <th class="px-4 py-3 text-left">Notas</th>
                                        <th class="px-4 py-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $comisiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <tr class="hover:bg-slate-50/60 transition-colors">

                                            
                                            <td class="px-4 py-3 whitespace-nowrap text-slate-600">
                                                <span
                                                    class="font-bold text-slate-800 block"><?php echo e($c->fecha_registro ? $c->fecha_registro->format('d/m/Y') : '—'); ?></span>
                                                <span
                                                    class="text-[10px] text-slate-400"><?php echo e($c->fecha_registro ? $c->fecha_registro->format('h:i A') : ''); ?></span>
                                            </td>

                                            
                                            <td class="px-4 py-3 text-slate-700">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->id_salida): ?>
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md text-[10px] font-bold">
                                                            <i class="fas fa-shopping-bag"></i> Salida #<?php echo e($c->id_salida); ?>

                                                        </span>
                                                    <?php else: ?>
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md text-[10px] font-bold">
                                                            <i class="fas fa-award"></i> Bono
                                                        </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <span class="font-semibold text-slate-800">
                                                        <?php echo e($c->concepto ?? ($c->salida ? "Venta directa ({$c->salida->cantidad} uds)" : "Comisión directa")); ?>

                                                    </span>
                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->salida && $c->salida->variante && $c->salida->variante->producto): ?>
                                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                                        <?php echo e($c->salida->variante->producto->nombre); ?> -
                                                        <?php echo e($c->salida->variante->nombre_variante); ?> | Total salida:
                                                        $<?php echo e(number_format($c->salida->total, 2)); ?>

                                                    </p>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>

                                            
                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                <span
                                                    class="font-black text-sm text-slate-900 block">$<?php echo e(number_format($c->monto, 2)); ?></span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->porcentaje && $c->porcentaje > 0): ?>
                                                    <span class="text-[10px] text-slate-400">Ref:
                                                        $<?php echo e(number_format($c->porcentaje, 2)); ?>/ud</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>

                                            
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->estado === 'Pendiente'): ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c->es_liquidacion_bloqueada): ?>
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300 shadow-2xs"
                                                            title="<?php echo e($c->motivo_bloqueo_liquidacion); ?>">
                                                            <i class="fas fa-shield-halved text-[9px] text-amber-600"></i>
                                                            En garantía
                                                        </span>
                                                    <?php else: ?>
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                            Liquidación pendiente
                                                        </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php elseif($c->estado === 'Pagada'): ?>
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <i class="fas fa-check text-[9px]"></i>
                                                        Pagada
                                                    </span>
                                                <?php else: ?>
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                        <i class="fas fa-ban text-[9px]"></i>
                                                        Cancelada
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
                                                            onclick="openEditModal(<?php echo e($c->id); ?>, '<?php echo e($c->monto); ?>', '<?php echo e(addslashes($c->concepto ?? '')); ?>', '<?php echo e(addslashes($c->notas ?? '')); ?>')"
                                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer"
                                                            title="Editar">
                                                            <i class="fas fa-pen text-[10px]"></i>
                                                        </button>
                                                        <button type="button" onclick="cancelarComision(<?php echo e($c->id); ?>)"
                                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors cursor-pointer"
                                                            title="Cancelar">
                                                            <i class="fas fa-ban text-[10px]"></i>
                                                        </button>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <tr>
                                            <td colspan="7" class="px-4 py-6 text-center text-slate-400">
                                                No hay registros de comisiones para este vendedor en el rango seleccionado.
                                            </td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400 shadow-sm">
                    <div
                        class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl mx-auto mb-3 text-slate-400">
                        <i class="fas fa-users-slash"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700">No se encontraron vendedores</h3>
                    <p class="text-xs text-slate-400 mt-1">Ajusta los filtros de búsqueda o fechas.</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

    </div>

    
    <?php echo $__env->make('comisiones.modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <script>
        const ROUTES = {
            index: "<?php echo e(route('comisiones.index')); ?>",
            pendientes: "<?php echo e(url('comisiones/pendientes')); ?>",
            store: <?php if($isAdmin): ?> "<?php echo e(route('comisiones.store')); ?>" <?php else: ?>"null" <?php endif; ?>,
            update: <?php if($isAdmin): ?> "<?php echo e(url('comisiones')); ?>" <?php else: ?>"null" <?php endif; ?>,
            liquidar: <?php if($isAdmin): ?> "<?php echo e(route('comisiones.liquidar')); ?>" <?php else: ?>"null" <?php endif; ?>,
            cancelar: <?php if($isAdmin): ?> "<?php echo e(url('comisiones')); ?>" <?php else: ?>"null" <?php endif; ?>,
        };
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        function toggleDesgloseVendedor(id) {
            const desglose = document.getElementById(`desglose-vendedor-${id}`);
            const chevron = document.getElementById(`icon-chevron-${id}`);
            if (!desglose) return;

            desglose.classList.toggle('hidden');
            if (chevron) {
                chevron.classList.toggle('rotate-180');
            }
        }

        function liquidarDirectoVendedor(vendedorId, nombreVendedor = '', usernameVendedor = '', fechaDesde = '', fechaHasta = '') {
            openLiquidarModal(vendedorId, nombreVendedor, usernameVendedor, fechaDesde, fechaHasta, true);
        }

        function formatNum(n) {
            return parseFloat(n).toLocaleString('es-SV', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // ── Cargar pendientes dinámicos al seleccionar vendedor para liquidar ──

        async function cargarPendientesVendedor(vendedorId) {
            const resumenBox = document.getElementById('liquidarResumenBox');
            const emptyBox = document.getElementById('liquidarEmptyBox');
            const loadingBox = document.getElementById('liquidarLoadingBox');
            const lista = document.getElementById('liquidarDetalleLista');
            const btnSubmit = document.getElementById('btnConfirmLiquidar');

            resumenBox.classList.add('hidden');
            emptyBox.classList.add('hidden');
            lista.classList.add('hidden');
            lista.innerHTML = '';

            if (!vendedorId) return;

            loadingBox.classList.remove('hidden');

            try {
                const params = new URLSearchParams();
                const fechaDesde = document.getElementById('liquidarFechaDesde')?.value;
                const fechaHasta = document.getElementById('liquidarFechaHasta')?.value;
                if (fechaDesde && fechaHasta) {
                    params.set('fecha_desde', fechaDesde);
                    params.set('fecha_hasta', fechaHasta);
                }
                const queryString = params.toString();
                const res = await fetch(`${ROUTES.pendientes}/${vendedorId}${queryString ? `?${queryString}` : ''}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                loadingBox.classList.add('hidden');

                if (data.success && (data.cantidad > 0 || (data.comisiones && data.comisiones.length > 0))) {
                    const totalLiquidable = Number(data.total || 0);
                    const totalEl = document.getElementById('liquidarTotalMonto');
                    if (totalLiquidable < 0) {
                        totalEl.textContent = '-$' + formatNum(Math.abs(totalLiquidable));
                        totalEl.className = 'text-2xl font-black text-rose-600 mt-0.5';
                    } else if (totalLiquidable === 0 && data.cantidad > 0) {
                        totalEl.textContent = '$0.00';
                        totalEl.className = 'text-2xl font-black text-indigo-600 mt-0.5';
                    } else {
                        totalEl.textContent = '$' + formatNum(totalLiquidable);
                        totalEl.className = 'text-2xl font-black text-emerald-700 mt-0.5';
                    }

                    const countTexto = data.cantidad_bloqueada > 0
                        ? `${data.cantidad} liquidable(s) · ${data.cantidad_bloqueada} en garantía`
                        : `${data.cantidad} comisiones pendientes`;
                    document.getElementById('liquidarCountBadge').textContent = countTexto;

                    let html = '';
                    if (data.cantidad_bloqueada > 0) {
                        html += `
                        <div class="p-2 mb-2 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-[11px] flex items-center gap-1.5">
                            <i class="fas fa-shield-halved text-amber-600 flex-shrink-0"></i>
                            <span><strong>${data.cantidad_bloqueada} comisión(es)</strong> ($${formatNum(data.total_bloqueado)}) están en garantía de devolución y no pueden ser liquidadas aún.</span>
                        </div>
                        `;
                    }

                    data.comisiones.forEach(c => {
                        const monto = Number(c.monto);
                        const esNegativo = monto < 0;
                        const esBloqueada = Boolean(c.bloqueada_garantia);
                        const montoTexto = esNegativo ? '-$' + formatNum(Math.abs(monto)) : '$' + formatNum(monto);

                        if (esBloqueada) {
                            html += `
                            <div class="flex items-center justify-between p-2.5 bg-amber-50/70 rounded-xl border border-amber-200/90 text-xs">
                                <div class="flex items-center gap-2 overflow-hidden">
                                    <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center flex-shrink-0 text-[10px]" title="En período de garantía de devolución">
                                        <i class="fas fa-shield-halved"></i>
                                    </span>
                                    <div class="truncate">
                                        <span class="truncate font-semibold text-slate-800 block">${c.concepto}</span>
                                        <span class="text-[10px] font-bold text-amber-800 flex items-center gap-1">
                                            ${c.motivo_bloqueo || 'Período de garantía activo'} · No liquidable
                                        </span>
                                    </div>
                                </div>
                                <span class="font-extrabold text-amber-800 ml-2 flex-shrink-0" title="En garantía">${montoTexto}</span>
                            </div>
                            `;
                        } else {
                            html += `
                            <label class="flex items-center justify-between p-2 bg-white rounded-lg border ${esNegativo ? 'border-rose-200 bg-rose-50/40 hover:bg-rose-50/70' : 'border-emerald-100 hover:bg-emerald-50/50'} cursor-pointer text-xs transition-colors">
                                <div class="flex items-center gap-2 overflow-hidden">
                                    <input type="checkbox" name="comisiones_ids[]" value="${c.id}" checked onchange="recalcularTotalSeleccionado()" class="rounded ${esNegativo ? 'border-rose-300 text-rose-600 focus:ring-rose-500' : 'border-emerald-300 text-emerald-600 focus:ring-emerald-500'} cursor-pointer">
                                    <span class="truncate font-medium text-slate-800">${c.concepto}${esNegativo ? ' · Deducción' : ''}</span>
                                </div>
                                <span class="font-extrabold ${esNegativo ? 'text-rose-700' : 'text-emerald-700'} ml-2 flex-shrink-0" data-monto="${monto}">${montoTexto}</span>
                            </label>
                            `;
                        }
                    });
                    lista.innerHTML = html;
                    resumenBox.classList.remove('hidden');

                    if (typeof recalcularTotalSeleccionado === 'function') {
                        recalcularTotalSeleccionado();
                    }
                } else {
                    emptyBox.classList.remove('hidden');
                    if (btnSubmit) btnSubmit.disabled = true;
                }
            } catch (e) {
                loadingBox.classList.add('hidden');
                console.error(e);
            }
        }

        function toggleDetallePendientes() {
            const lista = document.getElementById('liquidarDetalleLista');
            lista.classList.toggle('hidden');
        }

        function recalcularTotalSeleccionado() {
            const checkboxes = document.querySelectorAll('input[name="comisiones_ids[]"]:checked');
            let total = 0;
            checkboxes.forEach(cb => {
                const montoEl = cb.closest('label')?.querySelector('[data-monto]');
                if (montoEl) {
                    total += parseFloat(montoEl.dataset.monto || 0);
                }
            });

            total = Math.round(total * 100) / 100;
            const totalEl = document.getElementById('liquidarTotalMonto');
            const btnSubmit = document.getElementById('btnConfirmLiquidar');
            const metodoSelect = document.getElementById('liquidarMetodoPago');
            const optCompensacion = document.getElementById('optCompensacionSaldo');

            if (total < 0) {
                totalEl.textContent = '-$' + formatNum(Math.abs(total));
                totalEl.className = 'text-2xl font-black text-rose-600 mt-0.5';
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.title = 'El saldo neto no puede ser negativo.';
                }
                if (optCompensacion) optCompensacion.classList.add('hidden');
            } else if (total === 0) {
                totalEl.textContent = '$0.00';
                totalEl.className = 'text-2xl font-black text-indigo-600 mt-0.5';
                if (checkboxes.length > 0) {
                    if (btnSubmit) {
                        btnSubmit.disabled = false;
                        btnSubmit.title = 'Liquidación por compensación mutua de saldos ($0.00)';
                    }
                    if (optCompensacion) {
                        optCompensacion.classList.remove('hidden');
                        optCompensacion.selected = true;
                        if (typeof handleMetodoPagoLiquidar === 'function') {
                            handleMetodoPagoLiquidar('Compensación de Saldo');
                        }
                    }
                } else {
                    if (btnSubmit) btnSubmit.disabled = true;
                }
            } else {
                totalEl.textContent = '$' + formatNum(total);
                totalEl.className = 'text-2xl font-black text-emerald-700 mt-0.5';
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.title = '';
                }
                if (optCompensacion) {
                    optCompensacion.classList.add('hidden');
                    if (metodoSelect && metodoSelect.value === 'Compensación de Saldo') {
                        metodoSelect.value = 'Efectivo';
                        if (typeof handleMetodoPagoLiquidar === 'function') {
                            handleMetodoPagoLiquidar('Efectivo');
                        }
                    }
                }
            }

            document.getElementById('liquidarCountBadge').textContent = `${checkboxes.length} seleccionada${checkboxes.length !== 1 ? 's' : ''}`;
        }

        // ── Editar comisión (admin) ───────────────────────────────────────────

        function openEditModal(id, monto, concepto, notas) {
            document.getElementById('editComisionId').value = id;
            document.getElementById('editMonto').value = monto;
            document.getElementById('editConcepto').value = concepto || '';
            document.getElementById('editNotas').value = notas || '';
            document.getElementById('modalEdit').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('modalEdit').classList.add('hidden');
        }

        document.getElementById('formEdit')?.addEventListener('submit', async function (e) {
            e.preventDefault();
            const id = document.getElementById('editComisionId').value;
            const body = {
                monto: document.getElementById('editMonto').value,
                concepto: document.getElementById('editConcepto').value,
                notas: document.getElementById('editNotas').value,
                _method: 'PUT',
            };

            try {
                const res = await fetch(`${ROUTES.update}/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(body),
                });
                const data = await res.json();
                if (data.success) {
                    closeEditModal();
                    Swal.fire({ icon: 'success', title: '¡Actualizado!', text: data.message, timer: 2000, showConfirmButton: false });
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo actualizar la comisión.' });
            }
        });

        // ── Cancelar comisión (admin) ─────────────────────────────────────────

        async function cancelarComision(id) {
            const result = await Swal.fire({
                icon: 'warning',
                title: '¿Cancelar comisión?',
                text: 'Esta acción cambiará el estado a Cancelada. No se puede revertir.',
                showCancelButton: true,
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No',
                confirmButtonColor: '#ef4444',
            });

            if (!result.isConfirmed) return;

            try {
                const res = await fetch(`${ROUTES.cancelar}/${id}/cancelar`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ _method: 'PATCH' }),
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: '¡Cancelada!', text: data.message, timer: 2000, showConfirmButton: false });
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cancelar la comisión.' });
            }
        }

        // ── Liquidar con Método de Pago (admin) ────────────────────────────────

        function openLiquidarModal(vendedorId = null, nombreVendedor = '', usernameVendedor = '', fechaDesde = '', fechaHasta = '', bloquear = false) {
            const modal = document.getElementById('modalLiquidar');
            const form = document.getElementById('formLiquidar');
            if (!modal || !form) return;

            modal.classList.remove('hidden');
            form.reset();
            window._vendedorLiquidarBloqueado = false;

            const fDesde = fechaDesde || document.getElementById('filtroFechaDesdeActiva')?.value || '';
            const fHasta = fechaHasta || document.getElementById('filtroFechaHastaActiva')?.value || '';

            if (document.getElementById('liquidarFechaDesde')) document.getElementById('liquidarFechaDesde').value = fDesde;
            if (document.getElementById('liquidarFechaHasta')) document.getElementById('liquidarFechaHasta').value = fHasta;

            const alcance = document.getElementById('liquidarAlcance');
            if (alcance) {
                if (fDesde && fHasta) {
                    alcance.textContent = `Pago limitado del ${fDesde} al ${fHasta}, inclusive.`;
                    alcance.classList.remove('hidden');
                } else {
                    alcance.textContent = '';
                    alcance.classList.add('hidden');
                }
            }

            if (typeof deseleccionarVendedorLiquidar === 'function') {
                deseleccionarVendedorLiquidar();
            }
            if (typeof handleMetodoPagoLiquidar === 'function') {
                handleMetodoPagoLiquidar('Efectivo');
            }
            if (vendedorId && typeof seleccionarVendedorLiquidar === 'function') {
                const initials = (nombreVendedor || usernameVendedor || 'VN').substring(0, 2).toUpperCase();
                seleccionarVendedorLiquidar(vendedorId, nombreVendedor || usernameVendedor, usernameVendedor, initials, Boolean(bloquear));
            } else if (vendedorId && typeof cargarPendientesVendedor === 'function') {
                const inputHidden = document.getElementById('liquidarVendedor');
                if (inputHidden) inputHidden.value = vendedorId;
                cargarPendientesVendedor(vendedorId);
            }
        }

        function closeLiquidarModal() {
            window._vendedorLiquidarBloqueado = false;
            document.getElementById('modalLiquidar').classList.add('hidden');
            if (document.getElementById('liquidarFechaDesde')) document.getElementById('liquidarFechaDesde').value = '';
            if (document.getElementById('liquidarFechaHasta')) document.getElementById('liquidarFechaHasta').value = '';
            const alcance = document.getElementById('liquidarAlcance');
            if (alcance) {
                alcance.textContent = '';
                alcance.classList.add('hidden');
            }
            // Reset step wizard
            document.getElementById('liquidarStep2')?.classList.add('hidden');
            document.getElementById('formLiquidar')?.classList.remove('hidden');
            if (document.getElementById('stepProgressLine')) document.getElementById('stepProgressLine').style.width = '0%';
            if (document.getElementById('stepIndicator2')) document.getElementById('stepIndicator2').classList.add('opacity-35');
            const s2 = document.getElementById('step2Circle');
            if (s2) { s2.classList.replace('bg-emerald-600', 'bg-slate-300'); }
        }

        document.getElementById('formLiquidar')?.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (typeof clearAllFieldErrors === 'function') clearAllFieldErrors('formLiquidar');

            const vendedorId = document.getElementById('liquidarVendedor').value;
            let valid = true;

            if (!vendedorId) {
                if (typeof showFieldError === 'function') showFieldError('liquidarVendedor', 'Debes seleccionar un vendedor para continuar.');
                document.getElementById('liquidarVendedor-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                valid = false;
            }

            const metodoPago = document.getElementById('liquidarMetodoPago').value;
            const comprobante = document.getElementById('liquidarComprobante').files[0];
            if (metodoPago === 'Transferencia Bancaria') {
                if (!comprobante) {
                    if (typeof showFieldError === 'function') showFieldError('liquidarComprobante', 'El comprobante es obligatorio para transferencias bancarias.');
                    document.getElementById('liquidarComprobante-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    valid = false;
                } else {
                    const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
                    const fileExt = comprobante.name.split('.').pop().toLowerCase();
                    if (!allowedExtensions.includes(fileExt)) {
                        if (typeof showFieldError === 'function') showFieldError('liquidarComprobante', 'El comprobante debe ser una imagen (JPG, PNG, WEBP) o un documento PDF.');
                        valid = false;
                    } else if (comprobante.size > 5 * 1024 * 1024) {
                        if (typeof showFieldError === 'function') showFieldError('liquidarComprobante', 'El comprobante no debe superar el límite de 5MB.');
                        valid = false;
                    }
                }
            }

            if (!valid) return;

            const checkboxes = document.querySelectorAll('input[name="comisiones_ids[]"]:checked');
            if (checkboxes.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Sin comisiones seleccionadas', text: 'Debes seleccionar al menos una comisión pendiente para proceder con el pago.', confirmButtonColor: '#059669' });
                return;
            }

            // — Pasar a Step 2: poblar el resumen —
            const nombre = document.getElementById('selectedVendedorNombre')?.textContent || '—';
            const username = document.getElementById('selectedVendedorUsername')?.textContent || '—';
            const avatar = document.getElementById('selectedVendedorAvatar')?.textContent || '??';
            const total = document.getElementById('liquidarTotalMonto')?.textContent || '$0.00';
            const referencia = document.getElementById('liquidarReferencia')?.value || '';
            const notas = document.getElementById('liquidarNotas')?.value || '';

            // Encabezado vendedor
            document.getElementById('reviewVendedorAvatar').textContent = avatar.trim();
            document.getElementById('reviewVendedorNombre').textContent = nombre;
            document.getElementById('reviewVendedorUser').textContent = username;
            document.getElementById('reviewTotal').textContent = total;

            // Conteo
            document.getElementById('reviewCantidad').textContent = `${checkboxes.length} comisión${checkboxes.length !== 1 ? 'es' : ''}`;

            // Método
            let metodoBadge = 'Efectivo';
            if (metodoPago === 'Transferencia Bancaria') {
                metodoBadge = 'Transferencia Bancaria';
            } else if (metodoPago === 'Compensación de Saldo') {
                metodoBadge = 'Compensación de Saldo (Neto $0.00)';
            }
            document.getElementById('reviewMetodo').textContent = metodoBadge;

            // Referencia
            const refRow = document.getElementById('reviewReferenciaRow');
            if (referencia) {
                document.getElementById('reviewReferencia').textContent = referencia;
                refRow.classList.remove('hidden');
            } else { refRow.classList.add('hidden'); }

            // Comprobante
            const compRow = document.getElementById('reviewComprobanteRow');
            if (comprobante) {
                document.getElementById('reviewComprobanteNombre').textContent = comprobante.name;
                compRow.classList.remove('hidden');
            } else { compRow.classList.add('hidden'); }

            // Notas
            const notasRow = document.getElementById('reviewNotasRow');
            if (notas.trim()) {
                document.getElementById('reviewNotas').textContent = notas;
                notasRow.classList.remove('hidden');
            } else { notasRow.classList.add('hidden'); }

            // Lista de comisiones
            const listaReview = document.getElementById('reviewListaComisiones');
            let html = '';
            checkboxes.forEach(cb => {
                const label = cb.closest('label');
                const concepto = label?.querySelector('.truncate')?.textContent?.trim() || `Comisión #${cb.value}`;
                const montoEl = label?.querySelector('[data-monto]');
                const val = montoEl ? parseFloat(montoEl.dataset.monto) : 0;
                const isNeg = val < 0;
                const monto = (isNeg ? '-$' : '$') + formatNum(Math.abs(val));
                const colorClass = isNeg ? 'text-rose-600' : 'text-emerald-700';
                html += `<div class="flex items-center justify-between px-3 py-2 bg-white rounded-xl border border-slate-100 text-xs">
                <span class="text-slate-700 font-medium truncate flex-1 pr-2">${concepto}</span>
                <span class="font-extrabold ${colorClass} flex-shrink-0">${monto}</span>
            </div>`;
            });
            listaReview.innerHTML = html;

            // Activar indicador de paso 2
            document.getElementById('stepProgressLine').style.width = '100%';
            document.getElementById('stepIndicator2').classList.remove('opacity-35');
            document.getElementById('step2Circle').classList.replace('bg-slate-300', 'bg-emerald-600');

            // Mostrar Step 2, ocultar Step 1
            document.getElementById('formLiquidar').classList.add('hidden');
            document.getElementById('liquidarStep2').classList.remove('hidden');
        });

        function volverStep1Liquidar() {
            document.getElementById('liquidarStep2').classList.add('hidden');
            document.getElementById('formLiquidar').classList.remove('hidden');
            // Reiniciar indicador
            document.getElementById('stepProgressLine').style.width = '0%';
            document.getElementById('stepIndicator2').classList.add('opacity-35');
            document.getElementById('step2Circle').classList.replace('bg-emerald-600', 'bg-slate-300');
        }

        async function ejecutarLiquidacion() {
            const vendedorId = document.getElementById('liquidarVendedor').value;
            const metodoPago = document.getElementById('liquidarMetodoPago').value;
            const comprobante = document.getElementById('liquidarComprobante').files[0];
            const checkboxes = document.querySelectorAll('input[name="comisiones_ids[]"]:checked');

            const formData = new FormData();
            formData.append('id_vendedor', vendedorId);
            formData.append('metodo_pago', metodoPago);
            formData.append('referencia_pago', document.getElementById('liquidarReferencia').value || '');
            formData.append('notas', document.getElementById('liquidarNotas').value || '');
            if (metodoPago === 'Transferencia Bancaria' && comprobante) {
                formData.append('comprobante_pago', comprobante);
            }
            const fechaDesde = document.getElementById('liquidarFechaDesde')?.value;
            const fechaHasta = document.getElementById('liquidarFechaHasta')?.value;
            if (fechaDesde) formData.append('fecha_desde', fechaDesde);
            if (fechaHasta) formData.append('fecha_hasta', fechaHasta);
            checkboxes.forEach(cb => formData.append('comisiones_ids[]', cb.value));

            document.getElementById('btnFinalConfirmLiquidar').disabled = true;
            document.getElementById('btnFinalConfirmLiquidar').innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Procesando...</span>';

            Swal.fire({ title: 'Procesando pago...', text: 'Por favor espera un momento', allowOutsideClick: false, allowEscapeKey: false, didOpen: () => Swal.showLoading() });

            try {
                const res = await fetch(ROUTES.liquidar, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData,
                });
                const data = await res.json();
                if (data.success) {
                    closeLiquidarModal();
                    Swal.fire({ icon: 'success', title: '¡Liquidación Completada!', text: data.message, timer: 2500, showConfirmButton: false });
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    document.getElementById('btnFinalConfirmLiquidar').disabled = false;
                    document.getElementById('btnFinalConfirmLiquidar').innerHTML = '<i class="fas fa-check-circle"></i><span>Confirmar Pago</span>';
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo procesar la liquidación.' });
                }
            } catch (e) {
                document.getElementById('btnFinalConfirmLiquidar').disabled = false;
                document.getElementById('btnFinalConfirmLiquidar').innerHTML = '<i class="fas fa-check-circle"></i><span>Confirmar Pago</span>';
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo procesar la liquidación.' });
            }
        }

        // ── Crear manual / Bono (admin) ──────────────────────────────────────

        function openCreateModal() {
            document.getElementById('modalCreate').classList.remove('hidden');
            document.getElementById('formCreate').reset();
            if (typeof deseleccionarVendedorCreate === 'function') {
                deseleccionarVendedorCreate();
            }
        }

        function closeCreateModal() {
            document.getElementById('modalCreate').classList.add('hidden');
            document.getElementById('formCreate').reset();
            if (typeof deseleccionarVendedorCreate === 'function') {
                deseleccionarVendedorCreate();
            }
        }

        function setConcepto(texto) {
            document.getElementById('createConcepto').value = texto;
        }

        document.getElementById('formCreate')?.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (typeof clearAllFieldErrors === 'function') clearAllFieldErrors('formCreate');

            const idVendedor = document.getElementById('createVendedor').value;
            const concepto = document.getElementById('createConcepto').value.trim();
            const monto = parseFloat(document.getElementById('createMonto').value);

            let valid = true;
            if (!idVendedor) {
                if (typeof showFieldError === 'function') showFieldError('createVendedor', 'Debes seleccionar un vendedor para asignar el bono.');
                valid = false;
            }
            if (!concepto) {
                if (typeof showFieldError === 'function') showFieldError('createConcepto', 'El concepto del bono es obligatorio.');
                valid = false;
            }
            if (isNaN(monto) || monto <= 0) {
                if (typeof showFieldError === 'function') showFieldError('createMonto', 'El monto del bono es obligatorio y debe ser mayor a $0.');
                valid = false;
            }
            if (!valid) return;

            const body = {
                id_vendedor: idVendedor,
                concepto: concepto,
                monto: monto,
                notas: document.getElementById('createNotas').value || null,
            };

            try {
                const res = await fetch(ROUTES.store, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(body),
                });
                const data = await res.json();
                if (data.success) {
                    closeCreateModal();
                    Swal.fire({ icon: 'success', title: '¡Registrado!', text: data.message, timer: 2000, showConfirmButton: false });
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Verifica los datos ingresados.' });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo registrar la comisión.' });
            }
        });

        // ── Cerrar modales con ESC ────────────────────────────────────────────
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeEditModal();
                closeLiquidarModal();
                closeCreateModal();
                if (typeof closeDetalleModal === 'function') closeDetalleModal();
            }
        });

        // ── Custom Dropdowns para Comisiones por Vendedor ──────────────────────
        (() => {
            function initVendedorSelects() {
                const dropdowns = document.querySelectorAll('[data-custom-vendedor-select]');
                if (!dropdowns.length) return;

                function closeAll() {
                    dropdowns.forEach(dd => {
                        const menu = dd.querySelector('[data-menu]');
                        const arrow = dd.querySelector('[data-arrow]');
                        const toggle = dd.querySelector('[data-toggle]');
                        if (menu) menu.classList.add('hidden');
                        if (arrow) arrow.classList.remove('rotate-180');
                        if (toggle) toggle.setAttribute('aria-expanded', 'false');
                    });
                }

                dropdowns.forEach(dd => {
                    const toggle = dd.querySelector('[data-toggle]');
                    const menu = dd.querySelector('[data-menu]');
                    const arrow = dd.querySelector('[data-arrow]');
                    const input = dd.querySelector('input[type="hidden"]');
                    const label = dd.querySelector('[data-label]');
                    const icon = dd.querySelector('[data-icon]');
                    const options = dd.querySelectorAll('[data-option]');

                    if (!toggle || !menu) return;

                    toggle.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const isHidden = menu.classList.contains('hidden');
                        closeAll();
                        if (isHidden) {
                            menu.classList.remove('hidden');
                            if (arrow) arrow.classList.add('rotate-180');
                            toggle.setAttribute('aria-expanded', 'true');
                        }
                    });

                    options.forEach(opt => {
                        opt.addEventListener('click', (e) => {
                            e.stopPropagation();
                            const val = opt.dataset.value;
                            const text = opt.dataset.label;
                            const iconClass = opt.dataset.icon;

                            if (input) input.value = val;
                            if (label) label.textContent = text;
                            if (icon && iconClass) icon.className = iconClass;

                            options.forEach(o => {
                                const check = o.querySelector('[data-check]');
                                if (o === opt) {
                                    o.classList.add('bg-blue-50', 'text-blue-700');
                                    o.classList.remove('text-slate-700');
                                    if (check) check.classList.remove('hidden');
                                } else {
                                    o.classList.remove('bg-blue-50', 'text-blue-700');
                                    o.classList.add('text-slate-700');
                                    if (check) check.classList.add('hidden');
                                }
                            });

                            closeAll();
                        });
                    });
                });

                document.addEventListener('click', () => closeAll());
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') closeAll();
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initVendedorSelects);
            } else {
                initVendedorSelects();
            }
        })();
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7ae6b45c011e855a5545a671a7f3568e)): ?>
<?php $attributes = $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e; ?>
<?php unset($__attributesOriginal7ae6b45c011e855a5545a671a7f3568e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7ae6b45c011e855a5545a671a7f3568e)): ?>
<?php $component = $__componentOriginal7ae6b45c011e855a5545a671a7f3568e; ?>
<?php unset($__componentOriginal7ae6b45c011e855a5545a671a7f3568e); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\diseno\resources\views/comisiones/por_vendedor.blade.php ENDPATH**/ ?>