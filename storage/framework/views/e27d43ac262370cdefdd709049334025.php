<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Informe Oficial de Movimientos y Flujo Financiero | AXStore']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Informe Oficial de Movimientos y Flujo Financiero | AXStore']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <div class="max-w-[1440px] mx-auto space-y-6" id="reporte-container" x-data="gestionReporteMovimientos()" :aria-busy="cargando">

        <!-- 1. BARRA DE ACCIÓN Y NAVEGACIÓN SUPERIOR (SOLO PANTALLA) -->
        <div class="no-print flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard General
                </a>
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center shadow-lg shadow-blue-600/25">
                        <i class="fas fa-file-invoice-dollar text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-black uppercase tracking-[0.18em] text-blue-600">Módulo de Auditoría</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800">Oficial AXStore</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Informe de Movimientos & Flujo</h1>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN PARA EXPORTAR / IMPRIMIR -->
            <div class="flex flex-wrap items-center gap-2.5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->query('modo') === 'imprimir'): ?>
                    <a href="<?php echo e(route('admin.reportes.movimientos', array_merge(request()->except('modo', 'page'), ['modo' => 'normal']))); ?>"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-sm transition-all">
                        <i class="fas fa-table-list text-slate-500"></i>
                        <span>Vista Paginada</span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('admin.reportes.movimientos', array_merge(request()->except('page'), ['modo' => 'imprimir']))); ?>"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-sm transition-all"
                        title="Carga todos los registros continuos para impresión">
                        <i class="fas fa-list-check text-blue-600"></i>
                        <span>Cargar Todo</span>
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <!-- BOTÓN EXPORTAR EXCEL XLSX ESTILIZADO CON GRÁFICOS -->
                <a href="<?php echo e(route('admin.reportes.movimientos.csv', request()->except('page'))); ?>"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                    title="Descargar libro de Microsoft Excel (.xlsx) con estilos corporativos, tarjetas de KPIs y gráficos nativos">
                    <i class="fas fa-file-excel text-base"></i>
                    <span>Exportar Excel (.xlsx)</span>
                </a>

                <button
                    type="button"
                    onclick="prepararEImprimir()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-lg shadow-blue-600/30 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <i class="fas fa-print"></i>
                    <span>Imprimir / PDF</span>
                </button>
            </div>
        </div>

        <!-- 2. SUB-NAV ADMINISTRATIVO (SOLO PANTALLA) -->
        <nav class="no-print flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones de administración">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-chart-pie text-slate-400"></i>
                <span>Dashboard General</span>
            </a>
            <a href="<?php echo e(route('admin.reportes.movimientos')); ?>" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Informe de Movimientos</span>
            </a>
            <a href="<?php echo e(route('comisiones.porVendedor')); ?>" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-hand-holding-dollar text-slate-400"></i>
                <span>Comisiones</span>
            </a>
            <a href="<?php echo e(route('compras.movimientos')); ?>" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-warehouse text-slate-400"></i>
                <span>Kardex Físico</span>
            </a>
        </nav>

        <!-- 3. BARRA DE FILTROS INTERACTIVA (SOLO PANTALLA) -->
        <section class="no-print bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
            <form @submit.prevent="aplicarFiltros(1)" class="space-y-4">
                <input type="hidden" name="modo" :value="filtros.modo">

                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <i class="fas fa-clock text-slate-400 mr-1"></i> Periodo:
                        </span>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" @click="cambiarPreset('hoy')"
                                :class="filtros.preset === 'hoy' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                Hoy
                            </button>
                            <button type="button" @click="cambiarPreset('ultimos_7_dias')"
                                :class="filtros.preset === 'ultimos_7_dias' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                7 Días
                            </button>
                            <button type="button" @click="cambiarPreset('este_mes')"
                                :class="filtros.preset === 'este_mes' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                Este mes
                            </button>
                            <button type="button" @click="cambiarPreset('mes_anterior')"
                                :class="filtros.preset === 'mes_anterior' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                Mes anterior
                            </button>
                            <button type="button" @click="cambiarPreset('todo')"
                                :class="filtros.preset === 'todo' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                Todo el historial
                            </button>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500 font-medium">
                        Periodo activo: <b class="text-slate-800" x-text="formatearFechaVisual(filtros.desde)"><?php echo e(\Carbon\Carbon::parse($filtros['desde'])->format('d/m/Y')); ?></b> al <b class="text-slate-800" x-text="formatearFechaVisual(filtros.hasta)"><?php echo e(\Carbon\Carbon::parse($filtros['hasta'])->format('d/m/Y')); ?></b>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
                    <div class="lg:col-span-3">
                        <label for="tipo" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            <i class="fas fa-tags text-slate-400 mr-1"></i> Tipo de Movimiento
                        </label>
                        <select
                            name="tipo"
                            id="tipo" data-filter-select data-select-label="Tipo de movimiento" data-select-icon="fa-tags"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                            <option value="todos" <?php echo e(($filtros['tipo'] ?? '') === 'todos' ? 'selected' : ''); ?>>Todos los movimientos</option>
                            <option data-icon="fa-cart-shopping" data-tone="blue" value="ventas" <?php echo e(($filtros['tipo'] ?? '') === 'ventas' ? 'selected' : ''); ?>>Solo Ventas / Salidas</option>
                            <option data-icon="fa-boxes-stacked" data-tone="emerald" value="compras" <?php echo e(($filtros['tipo'] ?? '') === 'compras' ? 'selected' : ''); ?>>Solo Compras / Entradas</option>
                            <option data-icon="fa-truck" data-tone="amber" value="transferencias" <?php echo e(($filtros['tipo'] ?? '') === 'transferencias' ? 'selected' : ''); ?>>Transferencias a Tienda</option>
                            <option data-icon="fa-rotate-left" data-tone="purple" value="devoluciones" <?php echo e(($filtros['tipo'] ?? '') === 'devoluciones' ? 'selected' : ''); ?>>Devoluciones</option>
                            <option data-icon="fa-hand-holding-dollar" data-tone="emerald" value="comisiones" <?php echo e(($filtros['tipo'] ?? '') === 'comisiones' ? 'selected' : ''); ?>>Comisiones de Vendedores</option>
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label for="desde" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">Desde</label>
                        <input
                            type="date"
                            name="desde"
                            id="desde"
                            x-model="filtros.desde"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <div class="lg:col-span-2">
                        <label for="hasta" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">Hasta</label>
                        <input
                            type="date"
                            name="hasta"
                            id="hasta"
                            x-model="filtros.hasta"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <div class="lg:col-span-3">
                        <label for="q" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">Buscar Producto / Folio / Cliente</label>
                        <input
                            type="text"
                            name="q"
                            id="q"
                            x-model="filtros.q"
                            @input.debounce.400ms="aplicarFiltros(1)"
                            placeholder="Ej: Cámara, VNT-00036..."
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <div class="lg:col-span-2 flex items-center gap-2">
                        <button
                            type="submit"
                            class="flex-1 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-3 text-xs transition-all shadow-md shadow-blue-600/15 flex items-center justify-center gap-1.5 cursor-pointer">
                            <i :class="cargando ? 'fas fa-spinner fa-spin' : 'fas fa-filter'"></i>
                            <span x-text="cargando ? 'Filtrando...' : 'Filtrar'"></span>
                        </button>
                        <button
                            type="button"
                            @click="restablecerFiltros()"
                            title="Restablecer"
                            class="inline-flex items-center justify-center px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold transition-all cursor-pointer">
                            <i class="fas fa-rotate-left"></i>
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <!-- ════════════════════════════════════════════════════════════════════════════ -->
        <!-- 4. DOCUMENTO FORMAL DE INFORME EJECUTIVO (DISEÑO CORPORATIVO AXSTORE)        -->
        <!-- ════════════════════════════════════════════════════════════════════════════ -->
        <article class="report-paper bg-white border border-slate-200 rounded-3xl shadow-sm p-6 sm:p-10 print:p-0 print:border-none print:shadow-none print:m-0 space-y-6 text-slate-800">

            <!-- BARRA SUPERIOR DECORATIVA DE MARCA (AZUL CORPORATIVO) -->
            <div class="h-2 w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-500 rounded-t-full print:h-2 print:rounded-none"></div>

            <!-- A. ENCABEZADO CORPORATIVO CON LOGO Y MEMBRETE -->
            <header class="report-header flex flex-col sm:flex-row items-start justify-between gap-6 pb-6 border-b-2 border-slate-200">
                <!-- Lado Izquierdo: Logotipo AXStore y datos fiscales/comerciales -->
                <div class="flex items-center gap-4">
                    <div class="logo-box p-2.5 bg-slate-50 border border-slate-200 rounded-2xl shadow-sm flex items-center justify-center shrink-0 print:bg-white print:border-slate-300">
                        <img src="<?php echo e(asset('assets/images/logo.png')); ?>" alt="AXStore Logo" class="h-14 sm:h-16 w-auto object-contain">
                    </div>
                    <div>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black tracking-tight text-slate-900 leading-none">AX<span class="text-blue-600">STORE</span></span>
                            <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">Oficial</span>
                        </div>
                        <p class="text-xs font-bold text-slate-700 tracking-wide mt-1">AXSTORE COMERCIO & LOGÍSTICA DIGITAL</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">San Salvador, El Salvador · Tu tienda online</p>
                        <p class="text-[10px] text-slate-400 font-mono">PBX: (503) 2200-0000 · soporte@axstore.com</p>
                    </div>
                </div>

                <!-- Lado Derecho: Tarjeta ejecutiva del informe con folio y metadatos -->
                <div class="w-full sm:w-auto bg-slate-50 rounded-2xl p-4 border border-slate-200/90 text-left sm:text-right shrink-0 print:bg-slate-50 print:border-slate-300 space-y-1.5 min-w-[270px]">
                    <div class="flex items-center justify-between sm:justify-end gap-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Tipo de Documento:</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-600 text-white uppercase tracking-wider">Auditoría Interna</span>
                    </div>

                    <h2 class="text-sm sm:text-base font-black text-slate-900 uppercase tracking-tight">
                        Informe de Movimientos & Flujo
                    </h2>

                    <div class="pt-2 border-t border-slate-200 text-[11px] text-slate-600 space-y-1">
                        <div class="flex justify-between gap-3">
                            <span class="text-slate-400">Folio:</span>
                            <span class="font-mono font-black text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200 text-[11px]"><?php echo e($folioReporte); ?></span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="text-slate-400">Emisión:</span>
                            <span class="font-bold text-slate-800"><?php echo e($fechaGeneracion); ?></span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="text-slate-400">Auditor:</span>
                            <span class="font-bold text-slate-800"><?php echo e($usuarioEmisor); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- B. RESUMEN DE PARÁMETROS Y ALCANCE DE CONSULTA -->
            <section class="bg-gradient-to-r from-slate-50 to-slate-100/60 rounded-2xl p-4 border border-slate-200 text-xs text-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-4 print:bg-slate-50 print:border-slate-300">
                <div class="border-l-2 border-blue-600 pl-3">
                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Período Auditado</span>
                    <span class="font-black text-slate-900 text-[12px]">
                        <span x-text="formatearFechaVisual(filtros.desde)"><?php echo e(\Carbon\Carbon::parse($filtros['desde'])->format('d/m/Y')); ?></span> — 
                        <span x-text="formatearFechaVisual(filtros.hasta)"><?php echo e(\Carbon\Carbon::parse($filtros['hasta'])->format('d/m/Y')); ?></span>
                    </span>
                </div>
                <div class="border-l-2 border-emerald-500 pl-3">
                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Tipo de Movimiento</span>
                    <span class="font-bold text-slate-800 capitalize" x-text="filtros.tipo.replace('_', ' ')"><?php echo e(str_replace('_', ' ', $filtros['tipo'] ?? 'Todos')); ?></span>
                </div>
                <div class="border-l-2 border-indigo-500 pl-3">
                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Vendedor / Usuario</span>
                    <span class="font-bold text-slate-800 truncate block">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($filtros['vendedor_id'])): ?>
                            <?php echo e($vendedores->firstWhere('id', $filtros['vendedor_id'])?->nombre_real ?? 'Asignado'); ?>

                        <?php else: ?>
                            Todos los vendedores
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </div>
                <div class="border-l-2 border-amber-500 pl-3">
                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Registros Procesados</span>
                    <span class="font-black text-slate-900 text-[12px]" x-text="totales.cantidad_movimientos + ' operaciones'"><?php echo e($totales['cantidad_movimientos']); ?> operaciones</span>
                </div>
            </section>

            <!-- C. BLOQUE EJECUTIVO DE LOS 4 KPIS FINANCIEROS (CON SKELETON EN CARGA) -->
            <section class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 print:gap-2.5">
                <!-- KPI 1: Ingresos -->
                <div class="relative overflow-hidden rounded-2xl border-2 border-blue-100 bg-gradient-to-b from-blue-50/40 to-white p-4 print:border-blue-300 print:bg-white shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-blue-700">1. Ingresos Totales</span>
                        <div class="w-6 h-6 rounded-md bg-blue-100 text-blue-600 flex items-center justify-center text-[10px]">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <template x-if="cargando">
                        <div class="space-y-2 mt-2">
                            <div class="h-7 w-28 rounded-md ax-skeleton-shimmer"></div>
                            <div class="h-3 w-20 rounded ax-skeleton-shimmer"></div>
                        </div>
                    </template>
                    <div x-show="!cargando">
                        <p class="mt-2 text-xl sm:text-2xl font-black text-slate-900 tabular-nums">
                            $<span x-text="formatearMoneda(kpis.ingresos.total)"><?php echo e(number_format($kpis['ingresos']['total'], 2)); ?></span>
                        </p>
                        <p class="mt-1 text-[11px] text-slate-500 font-medium">
                            <b class="text-slate-800" x-text="kpis.ingresos.ventas_count"><?php echo e($kpis['ingresos']['ventas_count']); ?></b> ventas concretadas
                        </p>
                    </div>
                </div>

                <!-- KPI 2: Costos COGS -->
                <div class="relative overflow-hidden rounded-2xl border-2 border-rose-100 bg-gradient-to-b from-rose-50/40 to-white p-4 print:border-rose-300 print:bg-white shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-rose-700">2. Costo de Ventas</span>
                        <div class="w-6 h-6 rounded-md bg-rose-100 text-rose-600 flex items-center justify-center text-[10px]">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>
                    </div>
                    <template x-if="cargando">
                        <div class="space-y-2 mt-2">
                            <div class="h-7 w-28 rounded-md ax-skeleton-shimmer"></div>
                            <div class="h-3 w-24 rounded ax-skeleton-shimmer"></div>
                        </div>
                    </template>
                    <div x-show="!cargando">
                        <p class="mt-2 text-xl sm:text-2xl font-black text-rose-600 tabular-nums">
                            $<span x-text="formatearMoneda(kpis.costos.cogs_periodo)"><?php echo e(number_format($kpis['costos']['cogs_periodo'], 2)); ?></span>
                        </p>
                        <p class="mt-1 text-[11px] text-slate-500 font-medium">
                            <b class="text-slate-800" x-text="kpis.costos.unidades_vendidas"><?php echo e($kpis['costos']['unidades_vendidas']); ?></b> unidades despachadas
                        </p>
                    </div>
                </div>

                <!-- KPI 3: Ganancia Neta -->
                <div class="relative overflow-hidden rounded-2xl border-2 border-emerald-100 bg-gradient-to-b from-emerald-50/40 to-white p-4 print:border-emerald-300 print:bg-white shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700">3. Ganancia Neta</span>
                        <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px]">
                            <i class="fas fa-arrow-trend-up"></i>
                        </div>
                    </div>
                    <template x-if="cargando">
                        <div class="space-y-2 mt-2">
                            <div class="h-7 w-28 rounded-md ax-skeleton-shimmer"></div>
                            <div class="h-3 w-20 rounded ax-skeleton-shimmer"></div>
                        </div>
                    </template>
                    <div x-show="!cargando">
                        <p class="mt-2 text-xl sm:text-2xl font-black text-emerald-600 tabular-nums">
                            $<span x-text="formatearMoneda(kpis.ganancias.neta)"><?php echo e(number_format($kpis['ganancias']['neta'], 2)); ?></span>
                        </p>
                        <p class="mt-1 text-[11px] text-emerald-700 font-bold">
                            Margen real: <span x-text="kpis.ganancias.margen_porcentaje"><?php echo e($kpis['ganancias']['margen_porcentaje']); ?></span>%
                        </p>
                    </div>
                </div>

                <!-- KPI 4: Comisiones -->
                <div class="relative overflow-hidden rounded-2xl border-2 border-indigo-100 bg-gradient-to-b from-indigo-50/40 to-white p-4 print:border-indigo-300 print:bg-white shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-indigo-700">4. Comisiones</span>
                        <div class="w-6 h-6 rounded-md bg-indigo-100 text-indigo-600 flex items-center justify-center text-[10px]">
                            <i class="fas fa-hand-holding-dollar"></i>
                        </div>
                    </div>
                    <template x-if="cargando">
                        <div class="space-y-2 mt-2">
                            <div class="h-7 w-28 rounded-md ax-skeleton-shimmer"></div>
                            <div class="h-3 w-20 rounded ax-skeleton-shimmer"></div>
                        </div>
                    </template>
                    <div x-show="!cargando">
                        <p class="mt-2 text-xl sm:text-2xl font-black text-indigo-700 tabular-nums">
                            $<span x-text="formatearMoneda(kpis.ganancias.comisiones_periodo)"><?php echo e(number_format($kpis['ganancias']['comisiones_periodo'], 2)); ?></span>
                        </p>
                        <p class="mt-1 text-[11px] text-slate-500 font-medium">
                            Pendiente: <b class="text-indigo-800">$<span x-text="formatearMoneda(kpis.comisiones_por_pagar.total_pendiente)"><?php echo e(number_format($kpis['comisiones_por_pagar']['total_pendiente'], 2)); ?></span></b>
                        </p>
                    </div>
                </div>
            </section>

            <!-- D. GRÁFICOS VISUALES EJECUTIVOS (ESTILO PDF / CORPORATIVO) -->
            <section class="grid grid-cols-1 lg:grid-cols-12 gap-5 print:grid-cols-12 print:gap-4 print:break-inside-avoid">
                <!-- GRÁFICO 1: COMPARATIVA FINANCIERA (INGRESOS VS COSTOS VS GANANCIAS) -->
                <div class="lg:col-span-7 print:col-span-7 bg-slate-50/80 rounded-2xl border border-slate-200 p-4 sm:p-5 print:bg-white print:border-slate-300">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3 pb-2.5 border-b border-slate-200">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Comparativa Financiera del Periodo</h3>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5">Ingresos Brutos vs. Costo de Ventas vs. Ganancias Netas</p>
                        </div>
                        <div class="flex items-center gap-2.5 text-[10px] font-bold">
                            <span class="inline-flex items-center gap-1 text-blue-600">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span> Ingresos
                            </span>
                            <span class="inline-flex items-center gap-1 text-rose-500">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span> Costos
                            </span>
                            <span class="inline-flex items-center gap-1 text-emerald-600">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Ganancias
                            </span>
                        </div>
                    </div>
                    <div class="relative h-[210px] w-full">
                        <!-- Skeleton shimmer para gráfico financiero -->
                        <div x-show="cargando" x-cloak class="absolute inset-0 z-10 bg-slate-50/80 rounded-xl">
                            <?php if (isset($component)) { $__componentOriginal76f46eec35e23eb7e56b93766fa47592 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal76f46eec35e23eb7e56b93766fa47592 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.skeleton-chart','data' => ['type' => 'line','height' => 'h-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('skeleton-chart'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'line','height' => 'h-full']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal76f46eec35e23eb7e56b93766fa47592)): ?>
<?php $attributes = $__attributesOriginal76f46eec35e23eb7e56b93766fa47592; ?>
<?php unset($__attributesOriginal76f46eec35e23eb7e56b93766fa47592); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal76f46eec35e23eb7e56b93766fa47592)): ?>
<?php $component = $__componentOriginal76f46eec35e23eb7e56b93766fa47592; ?>
<?php unset($__componentOriginal76f46eec35e23eb7e56b93766fa47592); ?>
<?php endif; ?>
                        </div>
                        <canvas id="chartReporteFinanciero" :class="{'opacity-0': cargando}"></canvas>
                    </div>
                </div>

                <!-- GRÁFICO 2: FLUJO DE UNIDADES Y OPERACIONES -->
                <div class="lg:col-span-5 print:col-span-5 bg-slate-50/80 rounded-2xl border border-slate-200 p-4 sm:p-5 print:bg-white print:border-slate-300">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3 pb-2.5 border-b border-slate-200">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Flujo de Stock & Comisiones</h3>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5">Entradas físicas vs. Despachos y Comisiones</p>
                        </div>
                        <div class="flex items-center gap-2.5 text-[10px] font-bold">
                            <span class="inline-flex items-center gap-1 text-emerald-600">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Entradas
                            </span>
                            <span class="inline-flex items-center gap-1 text-indigo-600">
                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span> Salidas
                            </span>
                        </div>
                    </div>
                    <div class="relative h-[210px] w-full">
                        <!-- Skeleton shimmer para gráfico de movimientos -->
                        <div x-show="cargando" x-cloak class="absolute inset-0 z-10 bg-slate-50/80 rounded-xl">
                            <?php if (isset($component)) { $__componentOriginal76f46eec35e23eb7e56b93766fa47592 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal76f46eec35e23eb7e56b93766fa47592 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.skeleton-chart','data' => ['type' => 'bar','height' => 'h-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('skeleton-chart'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'bar','height' => 'h-full']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal76f46eec35e23eb7e56b93766fa47592)): ?>
<?php $attributes = $__attributesOriginal76f46eec35e23eb7e56b93766fa47592; ?>
<?php unset($__attributesOriginal76f46eec35e23eb7e56b93766fa47592); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal76f46eec35e23eb7e56b93766fa47592)): ?>
<?php $component = $__componentOriginal76f46eec35e23eb7e56b93766fa47592; ?>
<?php unset($__componentOriginal76f46eec35e23eb7e56b93766fa47592); ?>
<?php endif; ?>
                        </div>
                        <canvas id="chartReporteMovimientos" :class="{'opacity-0': cargando}"></canvas>
                    </div>
                </div>
            </section>

            <!-- E. TABLA DETALLADA DE MOVIMIENTOS Y SKELETON -->
            <div>
                <!-- Skeleton Shimmer de Tabla de Movimientos (12 columnas) -->
                <div x-show="cargando" x-cloak class="border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-sm p-4">
                    <?php if (isset($component)) { $__componentOriginalb3a7e296665e2f58ea67cad97892cbe7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb3a7e296665e2f58ea67cad97892cbe7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.skeleton-table','data' => ['rows' => 8,'cols' => 12]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('skeleton-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rows' => 8,'cols' => 12]); ?>
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

                <!-- Contenedor de la Tabla AJAX Dinámica -->
                <div id="contenedorTablaReporteMovimientos" x-show="!cargando">
                    <?php echo $__env->make('admin.reportes.tabla-reportes-movimientos', [
                        'movimientos' => $movimientos,
                        'todosMovimientos' => $todosMovimientos,
                        'totales' => $totales
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>

            <!-- E. PIE DE PÁGINA FORMAL DEL INFORME CON SELLOS DE AUDITORÍA -->
            <footer id="report-footer" class="report-footer pt-6 border-t-2 border-slate-200 text-slate-500 text-[10px] space-y-4 print:pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 items-end">
                    <!-- Código y autenticidad -->
                    <div class="space-y-1">
                        <p class="font-bold text-slate-800 uppercase tracking-wider">Certificación de Datos</p>
                        <p class="text-[10px] leading-relaxed text-slate-500">Documento emitido conforme a las directrices contables de AXStore. La información refleja fielmente los movimientos físicos y financieros registrados.</p>
                        <!-- Código de barras simulado para estilo formal -->
                        <div class="pt-1 font-mono tracking-widest text-slate-400 text-[10px]">
                            ||||| | ||||| |||| | ||||||| | ||||
                        </div>
                    </div>

                    <!-- Cuadro de Firma y Sello -->
                    <div class="text-center">
                        <div class="w-48 mx-auto border-b-2 border-dashed border-slate-400 mb-1.5 h-10 flex items-end justify-center">
                            <span class="text-[9px] text-slate-400 uppercase tracking-wider mb-0.5">Sello de Control Interno</span>
                        </div>
                        <p class="font-black text-slate-800 uppercase tracking-wider text-[10px]"><?php echo e($usuarioEmisor); ?></p>
                        <p class="text-[9px] text-slate-500">Revisor / Auditor Autorizado</p>
                    </div>

                    <!-- Cláusula y Visto Bueno -->
                    <div class="text-left sm:text-right space-y-1">
                        <p class="font-bold text-slate-800 uppercase tracking-wider">Visto Bueno Gerencia</p>
                        <div class="w-48 ml-auto border-b-2 border-dashed border-slate-400 mb-1.5 h-10 flex items-end justify-center">
                            <span class="text-[9px] text-slate-400 uppercase tracking-wider mb-0.5">Firma de Dirección</span>
                        </div>
                        <p class="text-[9px] text-slate-400">Dirección de Operaciones</p>
                    </div>
                </div>

                <div class="pt-3 text-center text-slate-400 border-t border-slate-100 flex flex-wrap items-center justify-between text-[10px]">
                    <span class="font-semibold text-slate-600">AXStore ERP v2.0 · Subsistema de Control & Reportes Financieros</span>
                    <span class="font-mono text-slate-500">Folio: <?php echo e($folioReporte); ?> · Generado: <?php echo e($fechaGeneracion); ?></span>
                </div>
            </footer>

            <!-- BARRA FIJA AL PIE EN TODAS LAS PÁGINAS IMPRESAS (RUNNING FOOTER) -->
            <div class="report-running-footer">
                <span>AXStore ERP v2.0 · Subsistema de Control & Reportes Financieros</span>
                <span>Folio: <?php echo e($folioReporte); ?> · Generado: <?php echo e($fechaGeneracion); ?></span>
            </div>

        </article>
    </div>

    <!-- ESTILOS EXHAUSTIVOS DE IMPRESIÓN (@media print) -->
    <style>
        .report-running-footer {
            display: none;
        }

        @media print {
            /* 1. Márgenes y orientación HORIZONTAL para que las 12 columnas quepan sin cortes */
            @page {
                margin: 8mm 6mm 10mm 6mm;
                size: landscape;
            }

            body {
                background-color: #ffffff !important;
                color: #0f172a !important;
                font-family: 'Inter', system-ui, sans-serif !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* 2. Ocultar estrictamente el navbar superior del sistema, menús y botones */
            body > header,
            body > footer,
            header:not(.report-header),
            footer:not(.report-footer),
            nav,
            .no-print,
            .swal2-container,
            #mobile-menu,
            button,
            .btn {
                display: none !important;
            }

            /* 3. Asegurar que el membrete y encabezado del informe SIEMPRE se muestren */
            .report-header {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
                margin-bottom: 12px !important;
            }

            .logo-box {
                background-color: #ffffff !important;
                border: 1px solid #cbd5e1 !important;
            }

            .logo-box img {
                display: block !important;
                max-height: 55px !important;
                width: auto !important;
            }

            /* 4. Contenedores a ancho completo y layout flexible para empujar el pie de página */
            main {
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            #reporte-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .report-paper {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                display: flex !important;
                flex-direction: column !important;
                min-height: calc(100vh - 18mm) !important;
            }

            /* 5. Prevenir cortes feos a mitad de fila o bloque */
            tr, .print-break-inside-avoid, section {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            /* 5b. Columnas proporcionales y limpias para la tabla */
            table {
                table-layout: fixed !important;
                width: 100% !important;
                font-size: 8pt !important;
            }

            /* Asegurar que las celdas hagan salto de línea completo sin cortar texto */
            table th, table td {
                padding: 3.5px 3px !important;
                font-size: 7.5pt !important;
                line-height: 1.25 !important;
                vertical-align: top !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
            }

            table thead th {
                font-size: 7.5pt !important;
                letter-spacing: 0.02em !important;
                white-space: normal !important;
                word-break: break-word !important;
                vertical-align: middle !important;
            }

            /* El badge de tipo reducido */
            table tbody span[class*="rounded-full"] {
                font-size: 7.2pt !important;
                padding: 1px 3px !important;
                white-space: normal !important;
                display: inline-block !important;
                line-height: 1.1 !important;
            }

            /* 6. TABLA: Encabezados y totales corporativos armónicos (cero negro pesado) */
            table thead tr {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
                border-bottom: 2px solid #2563eb !important;
            }

            table thead th {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
                font-weight: 800 !important;
                border-bottom: 2px solid #2563eb !important;
            }

            /* Evita que los totales tfoot se repitan en la página 1 */
            table tfoot {
                display: table-row-group !important;
            }

            table tfoot tr {
                background-color: #eff6ff !important;
                color: #0f172a !important;
                border-top: 2px solid #2563eb !important;
                border-bottom: 2px solid #cbd5e1 !important;
            }

            table tfoot td {
                background-color: #eff6ff !important;
            }

            table th, table td {
                border-color: #e2e8f0 !important;
            }

            /* 7. Footer formal con firmas empujado al pie de la página */
            .report-footer {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
                border-top: 2px solid #e2e8f0 !important;
                padding-top: 14px !important;
                margin-top: auto !important;
                margin-bottom: 24px !important;
            }

            /* 8. Running footer fijo en la parte inferior de cada hoja */
            .report-running-footer {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                height: 20px !important;
                background: #ffffff !important;
                border-top: 1px solid #cbd5e1 !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                font-size: 9px !important;
                color: #64748b !important;
                padding: 0 4px !important;
                z-index: 9999 !important;
            }
        }
    </style>

    <script>
        /**
         * Componente reactivo Alpine.js para gestión del Informe de Movimientos
         */
        function gestionReporteMovimientos() {
            return {
                cargando: false,
                solicitudActual: 0,
                filtros: {
                    preset: '<?php echo e($filtros['preset'] ?? 'este_mes'); ?>',
                    desde: '<?php echo e($filtros['desde']); ?>',
                    hasta: '<?php echo e($filtros['hasta']); ?>',
                    tipo: '<?php echo e($filtros['tipo'] ?? 'todos'); ?>',
                    vendedor_id: '<?php echo e($filtros['vendedor_id'] ?? ''); ?>',
                    producto_id: '<?php echo e($filtros['producto_id'] ?? ''); ?>',
                    q: '<?php echo e($filtros['q'] ?? ''); ?>',
                    modo: '<?php echo e($filtros['modo'] ?? 'normal'); ?>',
                },
                kpis: <?php echo \Illuminate\Support\Js::from($kpis)->toHtml() ?>,
                totales: <?php echo \Illuminate\Support\Js::from($totales)->toHtml() ?>,

                formatearMoneda(valor) {
                    if (valor === null || valor === undefined) return '0.00';
                    return Number(valor).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                formatearFechaVisual(fechaIso) {
                    if (!fechaIso) return '';
                    const partes = fechaIso.split('-');
                    if (partes.length === 3) {
                        return `${partes[2]}/${partes[1]}/${partes[0]}`;
                    }
                    return fechaIso;
                },

                cambiarPreset(nuevoPreset) {
                    this.filtros.preset = nuevoPreset;
                    // Cuando se selecciona un preset predeterminado, limpiamos fechas fijas para que el backend las resuelva con precisión
                    this.filtros.desde = '';
                    this.filtros.hasta = '';
                    this.aplicarFiltros(1);
                },

                restablecerFiltros() {
                    this.filtros.preset = 'este_mes';
                    this.filtros.desde = '';
                    this.filtros.hasta = '';
                    this.filtros.tipo = 'todos';
                    this.filtros.vendedor_id = '';
                    this.filtros.producto_id = '';
                    this.filtros.q = '';
                    this.aplicarFiltros(1);
                },

                aplicarFiltros(pagina = 1) {
                    const solicitud = ++this.solicitudActual;
                    this.cargando = true;

                    const params = new URLSearchParams();
                    if (this.filtros.preset) params.append('preset', this.filtros.preset);
                    if (this.filtros.desde) params.append('desde', this.filtros.desde);
                    if (this.filtros.hasta) params.append('hasta', this.filtros.hasta);
                    if (this.filtros.tipo) params.append('tipo', this.filtros.tipo);
                    if (this.filtros.vendedor_id) params.append('vendedor_id', this.filtros.vendedor_id);
                    if (this.filtros.producto_id) params.append('producto_id', this.filtros.producto_id);
                    if (this.filtros.q) params.append('q', this.filtros.q);
                    if (this.filtros.modo) params.append('modo', this.filtros.modo);
                    if (pagina && pagina > 1) params.append('page', pagina);

                    const urlDestino = `<?php echo e(route('admin.reportes.movimientos')); ?>?${params.toString()}`;

                    return fetch(urlDestino, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok || response.redirected) throw new Error('Error al consultar movimientos');
                        return response.json();
                    })
                    .then(data => {
                        if (solicitud !== this.solicitudActual) return;
                        if (!data.success) throw new Error(data.message || 'No se pudieron actualizar los datos.');
                        if (data.success) {
                            // 1. Reemplazar HTML de la tabla
                            const contenedor = document.getElementById('contenedorTablaReporteMovimientos');
                            if (contenedor) {
                                contenedor.innerHTML = data.html;
                                this.conectarPaginacionAjax(contenedor);
                            }

                            // 2. Actualizar KPIs y Totales
                            if (data.kpis) this.kpis = data.kpis;
                            if (data.totales) this.totales = data.totales;

                            // 3. Si el backend resolvió las fechas del preset, actualizarlas reactivamente
                            if (data.rango) {
                                this.filtros.desde = data.rango.desde;
                                this.filtros.hasta = data.rango.hasta;
                            }

                            // 4. Actualizar Gráficos Chart.js
                            if (data.graficoComparativa || data.graficoMovimientos) {
                                actualizarGraficosReporte(data.graficoComparativa, data.graficoMovimientos);
                            }

                            // 5. Reflejar en la barra de URL sin recargar
                            window.history.pushState({}, '', urlDestino);
                        }
                    })
                    .catch(err => {
                        if (solicitud !== this.solicitudActual) return;
                        window.Swal?.fire({ icon: 'error', title: 'No se pudo actualizar', text: 'Inténtalo de nuevo. Se conservan los datos anteriores.' });
                        console.error('Error al filtrar movimientos:', err);
                    })
                    .finally(() => {
                        if (solicitud === this.solicitudActual) this.cargando = false;
                    });
                },

                conectarPaginacionAjax(contenedor) {
                    const links = contenedor.querySelectorAll('nav a');
                    links.forEach(link => {
                        link.addEventListener('click', (e) => {
                            e.preventDefault();
                            try {
                                const url = new URL(link.href);
                                const page = url.searchParams.get('page') || 1;
                                this.aplicarFiltros(page);
                            } catch (error) {
                                console.error('Error parseando link de página:', error);
                            }
                        });
                    });
                },

                init() {
                    const contenedor = document.getElementById('contenedorTablaReporteMovimientos');
                    if (contenedor) {
                        this.conectarPaginacionAjax(contenedor);
                    }
                }
            };
        }

        /**
         * Calcula el espaciado dinámico necesario para que el bloque de firmas y certificación
         * se posicione exactamente al pie de la página impresa, tanto si queda en la misma hoja
         * como si salta a una nueva página.
         */
        function ajustarPieImpresion() {
            const footer = document.getElementById('report-footer');
            const paper = document.querySelector('.report-paper');
            const table = paper ? paper.querySelector('table') : null;
            if (!footer || !table || !paper) return;

            footer.style.marginTop = '0px';

            const paperRect = paper.getBoundingClientRect();
            const tableRect = table.getBoundingClientRect();
            const tableBottom = tableRect.bottom - paperRect.top;
            const footerHeight = footer.offsetHeight || 135;

            const PAGE_HEIGHT = 720;
            const SAFETY_PADDING = 30;

            const posInCurrentPage = tableBottom % PAGE_HEIGHT;

            if (posInCurrentPage + footerHeight + SAFETY_PADDING <= PAGE_HEIGHT) {
                const spaceToBottom = PAGE_HEIGHT - posInCurrentPage - footerHeight - SAFETY_PADDING;
                footer.style.marginTop = Math.max(20, Math.floor(spaceToBottom)) + 'px';
            } else {
                const spaceToBottomNewPage = PAGE_HEIGHT - footerHeight - SAFETY_PADDING;
                footer.style.marginTop = Math.max(40, Math.floor(spaceToBottomNewPage)) + 'px';
            }
        }

        function prepararEImprimir() {
            ajustarPieImpresion();
            setTimeout(() => {
                window.print();
            }, 60);
        }

        window.addEventListener('beforeprint', ajustarPieImpresion);
        window.addEventListener('afterprint', () => {
            const footer = document.getElementById('report-footer');
            if (footer) footer.style.marginTop = '';
        });

        // Actualización dinámica de gráficos sin destruir canvas
        function actualizarGraficosReporte(compData, movData) {
            if (window.chartFinancieroInstance && compData) {
                window.chartFinancieroInstance.data.labels = compData.labels || [];
                if (window.chartFinancieroInstance.data.datasets[0]) {
                    window.chartFinancieroInstance.data.datasets[0].data = compData.ingresos || [];
                }
                if (window.chartFinancieroInstance.data.datasets[1]) {
                    window.chartFinancieroInstance.data.datasets[1].data = compData.costos || [];
                }
                if (window.chartFinancieroInstance.data.datasets[2]) {
                    window.chartFinancieroInstance.data.datasets[2].data = compData.ganancias || [];
                }
                window.chartFinancieroInstance.update();
            }

            if (window.chartMovimientosInstance && movData) {
                window.chartMovimientosInstance.data.labels = movData.labels || [];
                if (window.chartMovimientosInstance.data.datasets[0]) {
                    window.chartMovimientosInstance.data.datasets[0].data = movData.entradas || [];
                }
                if (window.chartMovimientosInstance.data.datasets[1]) {
                    window.chartMovimientosInstance.data.datasets[1].data = movData.salidas || [];
                }
                window.chartMovimientosInstance.update();
            }
        }

        // Inicialización de Gráficos Ejecutivos con Chart.js
        function inicializarGraficosReporte() {
            if (typeof Chart === 'undefined') return;

            const compData = <?php echo \Illuminate\Support\Js::from($graficoComparativa ?? ['labels' => [], 'ingresos' => [], 'costos' => [], 'ganancias' => []])->toHtml() ?>;
            const movData = <?php echo \Illuminate\Support\Js::from($graficoMovimientos ?? ['labels' => [], 'entradas' => [], 'salidas' => [], 'comisiones' => []])->toHtml() ?>;

            // 1. Gráfico Financiero (Ingresos vs Costos vs Ganancias)
            const ctxFin = document.getElementById('chartReporteFinanciero');
            if (ctxFin && compData.labels) {
                if (window.chartFinancieroInstance) {
                    window.chartFinancieroInstance.destroy();
                }
                window.chartFinancieroInstance = new Chart(ctxFin, {
                    type: 'line',
                    data: {
                        labels: compData.labels,
                        datasets: [
                            {
                                label: 'Ingresos ($)',
                                data: compData.ingresos,
                                borderColor: '#2563eb', // Blue 600
                                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                                fill: true,
                                tension: 0.3,
                                borderWidth: 2,
                                pointRadius: compData.labels.length > 25 ? 0 : 2.5,
                                pointHoverRadius: 4,
                            },
                            {
                                label: 'Costos ($)',
                                data: compData.costos,
                                borderColor: '#f43f5e', // Rose 500
                                backgroundColor: 'transparent',
                                borderDash: [3, 3],
                                tension: 0.3,
                                borderWidth: 1.8,
                                pointRadius: compData.labels.length > 25 ? 0 : 2,
                                pointHoverRadius: 4,
                            },
                            {
                                label: 'Ganancia Neta ($)',
                                data: compData.ganancias,
                                borderColor: '#10b981', // Emerald 500
                                backgroundColor: 'rgba(16, 185, 129, 0.08)',
                                fill: true,
                                tension: 0.3,
                                borderWidth: 2,
                                pointRadius: compData.labels.length > 25 ? 0 : 2.5,
                                pointHoverRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { family: 'Inter', size: 11, weight: 'bold' },
                                bodyFont: { family: 'Inter', size: 11 },
                                padding: 8,
                                cornerRadius: 6,
                                callbacks: {
                                    label: function(ctx) {
                                        return (ctx.dataset.label || '') + ': $' + Number(ctx.parsed.y).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { family: 'Inter', size: 9 },
                                    color: '#64748b',
                                    maxTicksLimit: 12,
                                }
                            },
                            y: {
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    font: { family: 'Inter', size: 9 },
                                    color: '#64748b',
                                    callback: function(val) { return '$' + val; }
                                }
                            }
                        }
                    }
                });
            }

            // 2. Gráfico de Flujo de Stock y Operaciones
            const ctxMov = document.getElementById('chartReporteMovimientos');
            if (ctxMov && movData.labels) {
                if (window.chartMovimientosInstance) {
                    window.chartMovimientosInstance.destroy();
                }
                window.chartMovimientosInstance = new Chart(ctxMov, {
                    type: 'bar',
                    data: {
                        labels: movData.labels,
                        datasets: [
                            {
                                label: 'Entradas (Uds)',
                                data: movData.entradas,
                                backgroundColor: '#10b981', // Emerald 500
                                borderRadius: 4,
                                barPercentage: 0.6,
                            },
                            {
                                label: 'Salidas (Uds)',
                                data: movData.salidas,
                                backgroundColor: '#6366f1', // Indigo 500
                                borderRadius: 4,
                                barPercentage: 0.6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { family: 'Inter', size: 11, weight: 'bold' },
                                bodyFont: { family: 'Inter', size: 11 },
                                padding: 8,
                                cornerRadius: 6,
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { family: 'Inter', size: 9 },
                                    color: '#64748b',
                                    maxTicksLimit: 12,
                                }
                            },
                            y: {
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    font: { family: 'Inter', size: 9 },
                                    color: '#64748b',
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', inicializarGraficosReporte);
        } else {
            inicializarGraficosReporte();
        }

        document.addEventListener('livewire:navigated', inicializarGraficosReporte);
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
<?php endif; ?>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/admin/reportes/movimientos.blade.php ENDPATH**/ ?>