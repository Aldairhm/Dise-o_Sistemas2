<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Comisiones por Semana | AXStore']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Comisiones por Semana | AXStore']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="max-w-[1440px] mx-auto space-y-6">

        <!-- HEADER DEL DASHBOARD -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <i class="fas fa-calendar-week text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600 mb-0.5">Cortes Periódicos</p>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                            Comisiones por Semana
                        </h1>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN RÁPIDA -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin): ?>
            <div class="flex items-center gap-3 flex-wrap">
                <button type="button" onclick="openLiquidarModal()"
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
        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones de comisiones">
            <a href="<?php echo e(route('comisiones.porSemana')); ?>" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-calendar-week"></i>
                <span>Comisiones por Semana</span>
            </a>
            <a href="<?php echo e(route('comisiones.porVendedor')); ?>" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-users-gear text-slate-400"></i>
                <span>Comisiones por Vendedor</span>
            </a>
            <a href="<?php echo e(route('comisiones.index')); ?>" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-coins text-slate-400"></i>
                <span>Comisiones</span>
            </a>
            <a href="<?php echo e(route('comisiones.ajustes')); ?>" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-arrows-rotate text-slate-400"></i>
                <span>Ajustes</span>
            </a>
        </nav>

        <!-- FILTROS GLOBALES DE SEMANAS -->
        <section class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-sm mb-6 transition-all hover:shadow-md">
            <form id="formFiltroSemana" data-loading-form data-loading-label="Filtrando..." data-loading-target="#listaSemanas" data-loading-skeleton="#skeletonSemanas" method="GET" action="<?php echo e(route('comisiones.porSemana')); ?>" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3.5 items-end">

                
                <div class="sm:col-span-1 md:col-span-3 relative" data-custom-semana-select="anio">
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

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin): ?>
                
                <div class="sm:col-span-1 md:col-span-6">
                    <label for="vendedor_id" class="mb-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-tight">
                        <i class="fas fa-user-tie text-blue-600 text-xs"></i>
                        <span>Vendedor</span>
                    </label>
                    <select id="vendedor_id" name="vendedor_id" data-searchable-vendedor
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <option value="">Todos los vendedores</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $vendedores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($v->id); ?>" <?php echo e((string)$vendedorId === (string)$v->id ? 'selected' : ''); ?>>
                                <?php echo e($v->nombre_real); ?> (<?php echo e($v->username); ?>)
                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="sm:col-span-2 md:col-span-3 flex items-center gap-2">
                    <button id="btnFiltrarSemana" type="submit"
                            class="h-10 w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 text-xs transition-all shadow-md shadow-blue-600/20 active:scale-95 flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fas fa-filter text-xs"></i>
                        <span>Filtrar</span>
                    </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendedorId || $anio != now()->year): ?>
                    <a href="<?php echo e(route('comisiones.porSemana')); ?>"
                       class="h-10 rounded-xl border border-slate-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-slate-500 px-3 text-xs transition-all flex items-center justify-center"
                       title="Restablecer filtros">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </form>
        </section>

        <!-- KPI CARDS GLOBALES DE SEMANAS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" aria-label="Resumen de comisiones semanales">
            <!-- TOTAL GENERADO -->
            <div role="button" tabindex="0" data-weekly-kpi="all" aria-pressed="false" title="Ver todas las semanas y vendedores" class="weekly-kpi-card text-left bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between transition-all hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Total Año <?php echo e($anio); ?></span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">$<?php echo e(number_format($kpis['total'], 2)); ?></h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span><?php echo e($kpis['semanas']); ?> semanas con actividad</span>
                    </p>
                </div>
            </div>

            <!-- POR PAGAR (PENDIENTE) -->
            <div role="button" tabindex="0" data-weekly-kpi="pending" aria-pressed="false" title="Filtrar comisiones pendientes y ver su desglose" class="weekly-kpi-card text-left bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between transition-all hover:-translate-y-0.5 hover:border-amber-300 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-amber-500/20">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Por Liquidar</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-amber-700 tracking-tight">$<?php echo e(number_format($kpis['pendiente'], 2)); ?></h3>
                    <p class="text-xs font-semibold text-amber-600 mt-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($kpis['saldo_en_garantia'] ?? 0) > 0): ?>
                            <span title="$<?php echo e(number_format($kpis['saldo_en_garantia'], 2)); ?> en garantía de devolución">
                                $<?php echo e(number_format($kpis['saldo_liquidable'], 2)); ?> liquidable · <i class="fas fa-shield-halved text-[10px]"></i> $<?php echo e(number_format($kpis['saldo_en_garantia'], 2)); ?> en garantía
                            </span>
                        <?php else: ?>
                            <span>Saldo semanal pendiente</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                </div>
            </div>

            <!-- PAGADAS / LIQUIDADAS -->
            <div role="button" tabindex="0" data-weekly-kpi="paid" aria-pressed="false" title="Filtrar comisiones pagadas y ver su desglose" class="weekly-kpi-card text-left bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between transition-all hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-emerald-500/20">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Total Liquidado</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-emerald-700 tracking-tight">$<?php echo e(number_format($kpis['pagada'], 2)); ?></h3>
                    <p class="text-xs font-semibold text-emerald-600 mt-1">Comisiones semanales pagadas</p>
                </div>
            </div>

            <!-- ESTADO SEMANA ACTUAL -->
            <div role="button" tabindex="0" data-weekly-kpi="current" aria-pressed="false" title="Ir a la semana actual y abrir su desglose" class="weekly-kpi-card text-left bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between transition-all hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-indigo-500/20">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Semana En Curso</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                        <i class="fas fa-stopwatch"></i>
                    </div>
                </div>
                <div>
                    <?php
                        $semanaActualData = $reporteSemanas->firstWhere('es_semana_actual', true);
                    ?>
                    <h3 class="text-3xl font-black text-indigo-700 tracking-tight">
                        $<?php echo e(number_format($semanaActualData['total_comisiones'] ?? 0, 2)); ?>

                    </h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Semana <?php echo e(now()->weekOfYear); ?> en curso</p>
                </div>
            </div>
        </div>

        <!-- BARRA DE BÚSQUEDA Y CONTROL DE SEMANAS (INDEPENDIENTES - TODAS VISIBLES) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex flex-col xl:flex-row xl:items-center justify-between gap-3.5">
            <!-- BUSCADOR RÁPIDO Y SELECTOR DIRECTO -->
            <div class="flex flex-col sm:flex-row items-center gap-2.5 flex-1 max-w-2xl">
                <!-- SELECTOR DIRECTO DE SEMANA (DESPLAZA Y DESPLIEGA SIN OCULTAR NINGUNA) -->
                <div class="relative w-full sm:w-72 flex-shrink-0" data-custom-semana-select="directo">
                    <button type="button" class="h-10 w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50/90 px-3.5 text-xs font-bold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 flex items-center justify-between gap-2 cursor-pointer text-left" data-toggle aria-expanded="false">
                        <span class="flex items-center gap-2 truncate">
                            <i class="fas fa-calendar-week text-blue-600" data-icon></i>
                            <span class="truncate" data-label>Ir a semana...</span>
                        </span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" data-arrow></i>
                    </button>
                    <div class="hidden absolute left-0 top-full z-50 mt-1.5 w-full min-w-[240px] rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xl max-h-64 overflow-y-auto" data-menu>
                        <div class="space-y-0.5">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $reporteSemanas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semOpt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <button type="button" data-option data-value="<?php echo e($semOpt['key']); ?>" data-label="Sem <?php echo e($semOpt['semana_numero']); ?> (<?php echo e($semOpt['inicio_semana']->format('d/m')); ?> - <?php echo e($semOpt['fin_semana']->format('d/m')); ?>)" data-icon="fas fa-calendar-week text-blue-600"
                                    class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                    <span class="flex items-center gap-2 truncate">
                                        <i class="fas <?php echo e($semOpt['es_semana_actual'] ? 'fa-star text-amber-500' : 'fa-calendar-week text-blue-500'); ?>"></i>
                                        <span>Sem <?php echo e($semOpt['semana_numero']); ?> (<?php echo e($semOpt['inicio_semana']->format('d/m')); ?> - <?php echo e($semOpt['fin_semana']->format('d/m')); ?>)</span>
                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($semOpt['es_semana_actual']): ?>
                                        <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">Actual</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- BUSCADOR EN VIVO (UBICA Y DESPLIEGA LA COINCIDENCIA SIN OCULTAR LAS DEMÁS) -->
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-search text-xs"></i>
                    </div>
                    <input type="text"
                           id="buscadorSemanasInput"
                           oninput="buscarYDesplegarSemana(this.value)"
                           placeholder="Buscar semana (ej: 40, Sep, Oct, Ana Plasma...)"
                           class="w-full pl-9 pr-9 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                    <button type="button"
                            id="btnLimpiarBusquedaSemanas"
                            onclick="limpiarBuscadorSemanas()"
                            class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors cursor-pointer"
                            title="Limpiar búsqueda">
                        <i class="fas fa-circle-xmark text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- CONTROLES RÁPIDOS: CONTADOR DE DESPLEGADAS / EXPANDIR / COLAPSAR -->
            <div class="flex items-center gap-2 flex-wrap justify-between xl:justify-end">
                <span id="contadorSemanas" class="text-[11px] font-bold text-slate-500 mr-1" title="Semanas desplegadas de forma independiente">
                    <?php echo e($reporteSemanas->count()); ?> <?php echo e($reporteSemanas->count() === 1 ? 'semana' : 'semanas'); ?>

                </span>
                <div class="inline-flex rounded-xl border border-slate-200 bg-slate-50 p-1 gap-1">
                    <button type="button"
                            onclick="expandirTodasSemanas()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold shadow-2xs transition-all cursor-pointer"
                            title="Desplegar todas las semanas">
                        <i class="fas fa-angles-down text-[10px] text-blue-600"></i>
                        <span>Desplegar todas</span>
                    </button>
                    <button type="button"
                            onclick="colapsarTodasSemanas()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-slate-200/60 text-slate-600 text-xs font-bold transition-all cursor-pointer"
                            title="Plegar todas las semanas">
                        <i class="fas fa-angles-up text-[10px] text-slate-400"></i>
                        <span>Plegar todas</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- LISTA DE SEMANAS CON CORTES Y DESGLOSE -->
        <div id="skeletonSemanas" class="hidden">
            <?php if (isset($component)) { $__componentOriginalb3a7e296665e2f58ea67cad97892cbe7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb3a7e296665e2f58ea67cad97892cbe7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.skeleton-table','data' => ['rows' => 4,'cols' => 6]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('skeleton-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rows' => 4,'cols' => 6]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb3a7e296665e2f58ea67cad97892cbe7)): ?>
<?php $attributes = $__attributesOriginalb3a7e296665e2f58ea67cad97892cbe7; ?>
<?php unset($__attributesOriginalb3a7e296665e2f58ea67cad97892cbe7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb3a7e296665e2f58ea67cad97892cbe7)): ?>
<?php $component = $__componentOriginalb3a7e296665e2f58ea67cad97892cbe7; ?>
<?php unset($__componentOriginalb3a7e296665e2f58ea67cad97892cbe7); ?>
<?php endif; ?>
        </div>
        <div id="listaSemanas" class="space-y-4 scroll-mt-6" aria-live="polite">
            <?php echo $__env->make('comisiones.tabla-por-semana', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

    </div>
    
    <?php echo $__env->make('comisiones.modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <script>
    const ROUTES = {
        index:      "<?php echo e(route('comisiones.index')); ?>",
        pendientes: "<?php echo e(url('comisiones/pendientes')); ?>",
        store:      <?php if($isAdmin): ?> "<?php echo e(route('comisiones.store')); ?>" <?php else: ?> "null" <?php endif; ?>,
        update:     <?php if($isAdmin): ?> "<?php echo e(url('comisiones')); ?>" <?php else: ?> "null" <?php endif; ?>,
        liquidar:   <?php if($isAdmin): ?> "<?php echo e(route('comisiones.liquidar')); ?>" <?php else: ?> "null" <?php endif; ?>,
        cancelar:   <?php if($isAdmin): ?> "<?php echo e(url('comisiones')); ?>" <?php else: ?> "null" <?php endif; ?>,
    };
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let filtroKpiSemanal = 'all';

    // ── Filtro AJAX y Recarga Dinámica de Semanas ──

    async function aplicarFiltroSemanasAjax() {
        const solicitud = iniciarSolicitudComision('tabla');
        const form = document.getElementById('formFiltroSemana');
        if (!form) return;
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        const skeleton = document.getElementById('skeletonSemanas');
        const contenedor = document.getElementById('listaSemanas');
        const btnFiltrar = document.getElementById('btnFiltrarSemana');
        if (btnFiltrar) {
            btnFiltrar.disabled = true;
            btnFiltrar.innerHTML = '<i class="fas fa-spinner fa-spin text-[11px]"></i> <span>Filtrando...</span>';
        }
        if (skeleton && contenedor) {
            skeleton.classList.remove('hidden');
            contenedor.classList.add('hidden');
        }

        try {
            const url = `${form.action}?${params.toString()}`;
            const res = await fetch(url, {
                signal: solicitud.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) throw new Error('No se pudo cargar la lista de comisiones.');
            if (solicitud.vigente()) {
                const data = await res.json();
                if (!solicitud.vigente()) return;
                if (data.html && contenedor) {
                    contenedor.innerHTML = data.html;
                }
                if (data.kpis) {
                    actualizarKpisSemanas(data.kpis);
                }
                const btnReset = document.getElementById('btnResetSemanaFiltros');
                if (btnReset) {
                    const anio = document.getElementById('anio')?.value;
                    const vId = document.getElementById('vendedor_id')?.value;
                    const esActual = (anio == new Date().getFullYear() && !vId);
                    btnReset.classList.toggle('hidden', esActual);
                }
                actualizarContadorSemanas();
            }
        } catch (err) {
            if (!solicitud.vigente()) return;
            console.error('Error al filtrar semanas:', err);
            Swal.fire({ icon: 'error', title: 'No se pudo cargar la información', text: 'Intenta de nuevo. Se conservaron los datos anteriores.' });
        } finally {
            if (!solicitud.vigente()) return;
            if (btnFiltrar) {
                btnFiltrar.disabled = false;
                btnFiltrar.innerHTML = '<i class="fas fa-filter text-[11px]"></i> <span>Filtrar</span>';
            }
            if (skeleton && contenedor) {
                skeleton.classList.add('hidden');
                contenedor.classList.remove('hidden');
            }
        }
    }

    function limpiarFiltrosSemanasAjax() {
        const anio = document.getElementById('anio');
        if (anio) anio.value = new Date().getFullYear();
        const vSelect = document.getElementById('vendedor_id');
        if (vSelect) {
            vSelect.value = '';
            if (typeof updateSelected === 'function') updateSelected();
        }
        aplicarFiltroSemanasAjax();
    }

    function actualizarKpisSemanas(kpis) {
        // Los KPIs de semanas se refrescan reactivamente
        const elTotal = document.querySelector('[data-weekly-kpi="all"] h3');
        if (elTotal) elTotal.textContent = `$${formatNum(kpis.total || 0)}`;
        const elPend = document.querySelector('[data-weekly-kpi="pending"] h3');
        if (elPend) elPend.textContent = `$${formatNum(kpis.pendiente || 0)}`;
        const elPag = document.querySelector('[data-weekly-kpi="paid"] h3');
        if (elPag) elPag.textContent = `$${formatNum(kpis.pagada || 0)}`;
    }

    // ── Funciones de Control y Despliegue Independiente de Semanas ──

    function toggleSemana(semanaKey, forceState = null) {
        const body = document.getElementById(`semana-body-${semanaKey}`);
        const chevron = document.getElementById(`chevron-semana-${semanaKey}`);
        const btnText = document.getElementById(`btn-text-semana-${semanaKey}`);
        const btnToggle = document.getElementById(`btn-toggle-semana-${semanaKey}`);
        if (!body) return;

        // Cada semana es 100% independiente: desplegar o replegar una jamás afecta o cierra otra
        const willOpen = forceState !== null ? forceState : body.classList.contains('hidden');

        if (willOpen) {
            body.classList.remove('hidden');
            chevron?.classList.add('rotate-180');
            if (btnText) btnText.textContent = 'Plegar';
            btnToggle?.setAttribute('aria-expanded', 'true');
        } else {
            body.classList.add('hidden');
            chevron?.classList.remove('rotate-180');
            if (btnText) btnText.textContent = 'Desplegar';
            btnToggle?.setAttribute('aria-expanded', 'false');
        }

        actualizarContadorSemanas();
    }

    function actualizarContadorSemanas() {
        const cards = document.querySelectorAll('[data-week-card]');
        const total = cards.length;
        const abiertas = document.querySelectorAll('[data-week-card] [id^="semana-body-"]:not(.hidden)').length;
        const contador = document.getElementById('contadorSemanas');
        if (contador) {
            contador.textContent = `${abiertas} de ${total} ${total === 1 ? 'semana desplegada' : 'semanas desplegadas'}`;
        }
    }

    function irASemana(semanaKey) {
        if (!semanaKey) return;
        const card = document.getElementById(`semana-${semanaKey}`);
        if (!card) return;

        // Asegurar que la semana esté visible
        card.classList.remove('hidden');

        // Desplegar esta semana exclusivamente, de forma independiente
        toggleSemana(semanaKey, true);

        // Desplazamiento suave al centro de la pantalla
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Resaltar con destello visual temporal y estilo premium
        card.classList.remove('semana-highlight-search', 'semana-flash-anim');
        void card.offsetWidth; // forzar reflow para reiniciar la animación
        card.classList.add('semana-highlight-search', 'semana-flash-anim');
        setTimeout(() => {
            card.classList.remove('semana-highlight-search', 'semana-flash-anim');
        }, 2400);

        const sel = document.getElementById('selectorDirectoSemana');
        if (sel) sel.value = semanaKey;
    }

    function buscarYDesplegarSemana(query) {
        const q = (query || '').trim().toLowerCase();
        const btnLimpiar = document.getElementById('btnLimpiarBusquedaSemanas');
        if (btnLimpiar) btnLimpiar.classList.toggle('hidden', q === '');

        const cards = Array.from(document.querySelectorAll('[data-week-card]'));

        if (!q) {
            cards.forEach(card => {
                card.classList.remove('semana-highlight-search', 'semana-flash-anim');
            });
            actualizarContadorSemanas();
            return;
        }

        let primerMatch = null;
        let coincidencias = 0;

        cards.forEach(card => {
            // NUNCA OCULTAR LAS SEMANAS: TODAS PERMANECEN VISIBLES EN LA LISTA
            card.classList.remove('hidden');

            const searchable = (card.dataset.weekSearchable || '').toLowerCase();
            const coincide = searchable.includes(q);

            if (coincide) {
                coincidencias++;
                card.classList.add('semana-highlight-search');
                // Desplegar la semana coincidente de forma independiente
                toggleSemana(card.dataset.weekKey, true);
                if (!primerMatch) primerMatch = card;
            } else {
                card.classList.remove('semana-highlight-search', 'semana-flash-anim');
            }
        });

        if (primerMatch) {
            primerMatch.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        const contador = document.getElementById('contadorSemanas');
        if (contador) {
            contador.textContent = `${coincidencias} coincidencia${coincidencias !== 1 ? 's' : ''} (todas visibles)`;
        }
    }

    function limpiarBuscadorSemanas() {
        const input = document.getElementById('buscadorSemanasInput');
        if (input) {
            input.value = '';
            buscarYDesplegarSemana('');
            input.focus();
        }
    }

    function expandirTodasSemanas() {
        document.querySelectorAll('[data-week-card]').forEach(card => {
            toggleSemana(card.dataset.weekKey, true);
        });
    }

    function colapsarTodasSemanas() {
        document.querySelectorAll('[data-week-card]').forEach(card => {
            toggleSemana(card.dataset.weekKey, false);
        });
    }

    function toggleDesgloseVendedor(detalleId) {
        const desglose = document.getElementById(detalleId);
        const chevron = document.getElementById(`icon-${detalleId}`);
        if (!desglose) return;

        desglose.classList.toggle('hidden');
        if (chevron) chevron.classList.toggle('rotate-180');
    }

    function abrirLiquidacionSemanal(vendedorId, nombreVendedor, usernameVendedor = '', fechaDesde = '', fechaHasta = '') {
        if (!vendedorId) return;
        if (typeof openLiquidarModal === 'function') {
            openLiquidarModal(vendedorId, nombreVendedor, usernameVendedor, fechaDesde, fechaHasta, true);
        }
    }

    function formatNum(n) {
        return parseFloat(n).toLocaleString('es-SV', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function buscarEnCarruselSemana(semanaKey, query) {
        const q = query.trim().toLowerCase();
        const cards = document.querySelectorAll(`.vendedor-card-${semanaKey}`);
        let visibles = 0;
        cards.forEach(card => {
            const nombre = card.dataset.vendedorNombre || '';
            const username = card.dataset.vendedorUsername || '';
            const coincideNombre = !q || nombre.includes(q) || username.includes(q);
            const coincideFiltro = filtroKpiSemanal === 'all'
                || filtroKpiSemanal === 'current'
                || card.dataset.vendedorHasPending === '1' && filtroKpiSemanal === 'pending'
                || card.dataset.vendedorHasPaid === '1' && filtroKpiSemanal === 'paid';
            if (coincideNombre && coincideFiltro) {
                card.classList.remove('hidden');
                visibles++;
            } else {
                card.classList.add('hidden');
            }
        });

        const noMatchesEl = document.getElementById(`carrusel-empty-${semanaKey}`);
        if (noMatchesEl) {
            if (visibles === 0) {
                noMatchesEl.classList.remove('hidden');
            } else {
                noMatchesEl.classList.add('hidden');
            }
        }
    }

    function aplicarFiltroKpiSemanal(filtro) {
        const listaSemanas = document.getElementById('listaSemanas');
        const aviso = document.getElementById('filtroKpiAnuncio');
        const avisoTexto = document.getElementById('filtroKpiAnuncioTexto');
        const tarjetasKpi = document.querySelectorAll('[data-weekly-kpi]');
        const semanaActual = document.querySelector('[data-week-card][data-week-current="1"]');

        if (filtro === 'current' && !semanaActual && Number(document.getElementById('anio')?.value) !== new Date().getFullYear()) {
            const selectorAnio = document.getElementById('anio');
            selectorAnio.value = new Date().getFullYear();
            selectorAnio.form.requestSubmit();
            return;
        }

        filtroKpiSemanal = filtroKpiSemanal === filtro && filtro !== 'all' ? 'all' : filtro;

        // Limpiar resaltes previos de KPI pero NUNCA OCULTAR SEMANAS
        const cards = document.querySelectorAll('[data-week-card]');
        cards.forEach(card => {
            card.classList.remove('hidden');
            card.classList.remove('semana-highlight-search', 'semana-highlight-pending', 'semana-highlight-paid', 'semana-highlight-current', 'semana-flash-anim');
        });

        let primeraSemanaMatch = null;

        if (filtroKpiSemanal === 'current') {
            if (semanaActual) {
                toggleSemana(semanaActual.dataset.weekKey, true);
                semanaActual.classList.add('semana-highlight-current', 'semana-flash-anim');
                primeraSemanaMatch = semanaActual;
            }
        } else if (filtroKpiSemanal === 'pending') {
            cards.forEach(card => {
                if (card.dataset.weekHasPending === '1') {
                    toggleSemana(card.dataset.weekKey, true);
                    card.classList.add('semana-highlight-pending', 'semana-flash-anim');
                    if (!primeraSemanaMatch) primeraSemanaMatch = card;
                }
            });
        } else if (filtroKpiSemanal === 'paid') {
            cards.forEach(card => {
                if (card.dataset.weekHasPaid === '1') {
                    toggleSemana(card.dataset.weekKey, true);
                    card.classList.add('semana-highlight-paid', 'semana-flash-anim');
                    if (!primeraSemanaMatch) primeraSemanaMatch = card;
                }
            });
        }

        // Actualizar estados visuales de las tarjetas KPI
        tarjetasKpi.forEach((tarjeta) => {
            const activa = tarjeta.dataset.weeklyKpi === filtroKpiSemanal;
            tarjeta.setAttribute('aria-pressed', activa ? 'true' : 'false');
            tarjeta.classList.toggle('ring-2', activa);
            tarjeta.classList.toggle('ring-blue-300', activa && tarjeta.dataset.weeklyKpi === 'all');
            tarjeta.classList.toggle('ring-amber-300', activa && tarjeta.dataset.weeklyKpi === 'pending');
            tarjeta.classList.toggle('ring-emerald-300', activa && tarjeta.dataset.weeklyKpi === 'paid');
            tarjeta.classList.toggle('ring-indigo-300', activa && tarjeta.dataset.weeklyKpi === 'current');
        });

        const descripciones = {
            all: '',
            pending: 'Enfocando y desplegando semanas con saldo por liquidar. Las demás semanas permanecen visibles e independientes abajo.',
            paid: 'Enfocando y desplegando semanas con comisiones pagadas. Las demás semanas permanecen visibles e independientes abajo.',
            current: 'Enfocando y desplegando la semana en curso. Las demás semanas permanecen visibles e independientes abajo.'
        };

        if (filtroKpiSemanal !== 'all' && descripciones[filtroKpiSemanal]) {
            if (avisoTexto) avisoTexto.textContent = descripciones[filtroKpiSemanal];
            aviso?.classList.remove('hidden');
            if (primeraSemanaMatch) {
                primeraSemanaMatch.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            aviso?.classList.add('hidden');
        }

        actualizarContadorSemanas();
    }

    document.querySelectorAll('[data-weekly-kpi]').forEach((tarjeta) => {
        const activate = () => aplicarFiltroKpiSemanal(tarjeta.dataset.weeklyKpi);
        tarjeta.addEventListener('click', activate);
        tarjeta.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                activate();
            }
        });
    });

    const filtroVendedorSemana = document.querySelector('select[data-searchable-vendedor]');
    filtroVendedorSemana?.addEventListener('change', () => filtroVendedorSemana.form.requestSubmit());

    // Inicializar contador de semanas desplegadas
    actualizarContadorSemanas();

    // ── Cargar pendientes dinámicos al seleccionar vendedor para liquidar ──

    async function cargarPendientesVendedor(vendedorId) {
        const solicitud = iniciarSolicitudComision('pendientes');
        const resumenBox = document.getElementById('liquidarResumenBox');
        const emptyBox   = document.getElementById('liquidarEmptyBox');
        const loadingBox = document.getElementById('liquidarLoadingBox');
        const lista      = document.getElementById('liquidarDetalleLista');
        const btnSubmit  = document.getElementById('btnConfirmLiquidar');

        resumenBox.classList.add('hidden');
        emptyBox.classList.add('hidden');
        lista.classList.add('hidden');
        lista.innerHTML = '';

        loadingBox.classList.add('hidden');
        if (btnSubmit) btnSubmit.disabled = true;
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
                signal: solicitud.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error('No se pudieron cargar las comisiones pendientes.');
            const data = await res.json();
            if (!solicitud.vigente()) return;

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
                    <div class="p-2.5 mb-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px] space-y-1.5">
                        <div class="flex items-center gap-1.5 font-bold">
                            <i class="fas fa-shield-halved text-amber-600 flex-shrink-0"></i>
                            <span>${data.cantidad_bloqueada} comisión(es) ($${formatNum(data.total_bloqueado)}) están en garantía de devolución y no pueden ser liquidadas aún.</span>
                        </div>
                        ${fechaDesde && fechaHasta ? `
                        <div class="pt-1.5 border-t border-amber-200/60 flex items-center justify-between gap-2 flex-wrap">
                            <span class="text-amber-800 text-[10px]">¿Deseas ver pendientes acumuladas de todos los periodos?</span>
                            <button type="button" onclick="quitarFiltroFechasLiquidar()" class="px-2.5 py-1 rounded-lg bg-amber-200 hover:bg-amber-300 text-amber-950 font-black text-[10px] cursor-pointer transition-colors shadow-2xs">
                                <i class="fas fa-filter-circle-xmark mr-1"></i> Quitar filtro de semana
                            </button>
                        </div>
                        ` : ''}
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
            if (!solicitud.vigente() || e.name === 'AbortError') return;
            loadingBox.classList.add('hidden');
            console.error(e);
            Swal.fire({ icon: 'error', title: 'Error al cargar', text: 'No se pudieron cargar las comisiones pendientes. Vuelve a seleccionar el vendedor para reintentar.' });
        } finally {
            if (solicitud.vigente()) loadingBox.classList.add('hidden');
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
        if (total < 0) {
            totalEl.textContent = '-$' + formatNum(Math.abs(total));
            totalEl.className = 'text-2xl font-black text-rose-600 mt-0.5';
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.title = 'El saldo neto no puede ser negativo.';
            }
        } else if (total === 0) {
            totalEl.textContent = '$0.00';
            totalEl.className = 'text-2xl font-black text-indigo-600 mt-0.5';
            if (btnSubmit) {
                btnSubmit.disabled = checkboxes.length === 0;
                btnSubmit.title = '';
            }
        } else {
            totalEl.textContent = '$' + formatNum(total);
            totalEl.className = 'text-2xl font-black text-emerald-700 mt-0.5';
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.title = '';
            }
        }

        document.getElementById('liquidarCountBadge').textContent = `${checkboxes.length} seleccionada${checkboxes.length !== 1 ? 's' : ''}`;
    }

    // ── Editar comisión (admin) ───────────────────────────────────────────

    function openEditModal(id, monto, concepto, notas) {
        document.getElementById('editComisionId').value = id;
        document.getElementById('editMonto').value      = monto;
        document.getElementById('editConcepto').value   = concepto || '';
        document.getElementById('editNotas').value      = notas || '';
        document.getElementById('modalEdit').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modalEdit').classList.add('hidden');
    }

    document.getElementById('formEdit')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const finalizarCarga = iniciarCargaFormularioComision(this);
        if (!finalizarCarga) return;
        const id = document.getElementById('editComisionId').value;
        const body = {
            monto:    document.getElementById('editMonto').value,
            concepto: document.getElementById('editConcepto').value,
            notas:    document.getElementById('editNotas').value,
            _method:  'PUT',
        };

        try {
            const res = await fetch(`${ROUTES.update}/${id}`, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body:    JSON.stringify(body),
            });
            const data = await res.json();
            if (data.success) {
                closeEditModal();
                Swal.fire({ icon: 'success', title: '¡Actualizado!', text: data.message, timer: 1500, showConfirmButton: false });
                aplicarFiltroSemanasAjax();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            }
        } catch(e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo actualizar la comisión.' });
        } finally {
            finalizarCarga();
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
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body:    JSON.stringify({ _method: 'PATCH' }),
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire({ icon: 'success', title: '¡Cancelada!', text: data.message, timer: 1500, showConfirmButton: false });
                aplicarFiltroSemanasAjax();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            }
        } catch(e) {
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
        document.getElementById('liquidarFechaDesde').value = fechaDesde;
        document.getElementById('liquidarFechaHasta').value = fechaHasta;
        const alcance = document.getElementById('liquidarAlcance');
        if (alcance) {
            if (fechaDesde && fechaHasta) {
                alcance.textContent = `Pago limitado del ${fechaDesde} al ${fechaHasta}, inclusive.`;
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
        }
    }

    function quitarFiltroFechasLiquidar() {
        const inputDesde = document.getElementById('liquidarFechaDesde');
        const inputHasta = document.getElementById('liquidarFechaHasta');
        if (inputDesde) inputDesde.value = '';
        if (inputHasta) inputHasta.value = '';
        const alcance = document.getElementById('liquidarAlcance');
        if (alcance) {
            alcance.textContent = '';
            alcance.classList.add('hidden');
        }
        const vendedorId = document.getElementById('liquidarVendedor')?.value;
        if (vendedorId) {
            cargarPendientesVendedor(vendedorId);
        }
    }

    window.openLiquidarModal = openLiquidarModal;
    window.closeLiquidarModal = closeLiquidarModal;
    window.abrirLiquidacionSemanal = abrirLiquidacionSemanal;
    window.quitarFiltroFechasLiquidar = quitarFiltroFechasLiquidar;

    function closeLiquidarModal() {
        cancelarSolicitudComision('pendientes');
        document.getElementById('liquidarLoadingBox')?.classList.add('hidden');
        const siguiente = document.getElementById('btnConfirmLiquidar');
        if (siguiente) siguiente.disabled = true;
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

    document.getElementById('formLiquidar')?.addEventListener('submit', async function(e) {
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
        const nombre   = document.getElementById('selectedVendedorNombre')?.textContent || '—';
        const username = document.getElementById('selectedVendedorUsername')?.textContent || '—';
        const avatar   = document.getElementById('selectedVendedorAvatar')?.textContent  || '??';
        const total    = document.getElementById('liquidarTotalMonto')?.textContent       || '$0.00';
        const referencia = document.getElementById('liquidarReferencia')?.value || '';
        const notas      = document.getElementById('liquidarNotas')?.value || '';

        // Encabezado vendedor
        document.getElementById('reviewVendedorAvatar').textContent = avatar.trim();
        document.getElementById('reviewVendedorNombre').textContent = nombre;
        document.getElementById('reviewVendedorUser').textContent   = username;
        document.getElementById('reviewTotal').textContent          = total;

        // Conteo
        document.getElementById('reviewCantidad').textContent = `${checkboxes.length} comisión${checkboxes.length !== 1 ? 'es' : ''}`;

        // Método
        const metodoBadge = metodoPago === 'Transferencia Bancaria' ? 'Transferencia Bancaria' : 'Efectivo';
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
            const montoEl  = label?.querySelector('[data-monto]');
            const val      = montoEl ? parseFloat(montoEl.dataset.monto) : 0;
            const isNeg    = val < 0;
            const monto    = (isNeg ? '-$' : '$') + formatNum(Math.abs(val));
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
        if (document.getElementById('btnFinalConfirmLiquidar')?.disabled) return;
        const vendedorId = document.getElementById('liquidarVendedor').value;
        const metodoPago = document.getElementById('liquidarMetodoPago').value;
        const comprobante = document.getElementById('liquidarComprobante').files[0];
        const checkboxes = document.querySelectorAll('input[name="comisiones_ids[]"]:checked');

        const formData = new FormData();
        formData.append('id_vendedor', vendedorId);
        formData.append('metodo_pago', metodoPago);
        const fechaDesde = document.getElementById('liquidarFechaDesde')?.value;
        const fechaHasta = document.getElementById('liquidarFechaHasta')?.value;
        if (fechaDesde && fechaHasta) {
            formData.append('fecha_desde', fechaDesde);
            formData.append('fecha_hasta', fechaHasta);
        }
        formData.append('referencia_pago', document.getElementById('liquidarReferencia').value || '');
        formData.append('notas', document.getElementById('liquidarNotas').value || '');
        if (metodoPago === 'Transferencia Bancaria' && comprobante) {
            formData.append('comprobante_pago', comprobante);
        }
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
                Swal.fire({ icon: 'success', title: '¡Liquidación Completada!', text: data.message, timer: 2000, showConfirmButton: false });
                aplicarFiltroSemanasAjax();
            } else {
                document.getElementById('btnFinalConfirmLiquidar').disabled = false;
                document.getElementById('btnFinalConfirmLiquidar').innerHTML = '<i class="fas fa-check-circle"></i><span>Confirmar Pago</span>';
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo procesar la liquidación.' });
            }
        } catch(e) {
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

    document.getElementById('formCreate')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (typeof clearAllFieldErrors === 'function') clearAllFieldErrors('formCreate');

        const idVendedor = document.getElementById('createVendedor').value;
        const concepto   = document.getElementById('createConcepto').value.trim();
        const monto      = parseFloat(document.getElementById('createMonto').value);

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

        const finalizarCarga = iniciarCargaFormularioComision(this);
        if (!finalizarCarga) return;
        const body = {
            id_vendedor: idVendedor,
            concepto:    concepto,
            monto:       monto,
            notas:       document.getElementById('createNotas').value || null,
        };

        try {
            const res = await fetch(ROUTES.store, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body:    JSON.stringify(body),
            });
            const data = await res.json();
            if (data.success) {
                closeCreateModal();
                Swal.fire({ icon: 'success', title: '¡Registrado!', text: data.message, timer: 1500, showConfirmButton: false });
                aplicarFiltroSemanasAjax();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Verifica los datos ingresados.' });
            }
        } catch(e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo registrar la comisión.' });
        } finally {
            finalizarCarga();
        }
    });

    // ── Cerrar modales con ESC ────────────────────────────────────────────
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeEditModal();
            closeLiquidarModal();
            closeCreateModal();
        }
    });

    // ── Custom Dropdowns para Comisiones por Semana ────────────────────────
    (() => {
        function initSemanaSelects() {
            const dropdowns = document.querySelectorAll('[data-custom-semana-select]');
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
                const type = dd.dataset.customSemanaSelect;

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

                        if (type === 'directo' && typeof irASemana === 'function' && val) {
                            irASemana(val);
                        }
                    });
                });
            });

            document.addEventListener('click', () => closeAll());
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeAll();
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSemanaSelects);
        } else {
            initSemanaSelects();
        }
    })();
    </script>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin): ?>
        <?php echo $__env->make('comisiones.partials.searchable-vendedor-select', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/comisiones/por_semana.blade.php ENDPATH**/ ?>