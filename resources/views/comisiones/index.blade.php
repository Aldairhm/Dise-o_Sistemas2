<x-app title="Dashboard de Comisiones | AXStore">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="max-w-[1440px] mx-auto space-y-6">

        <!-- HEADER DEL DASHBOARD -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <i class="fas fa-hand-holding-dollar text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600 mb-0.5">Gestión Financiera</p>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                            {{ $isAdmin ? 'Dashboard de Comisiones' : 'Mis Comisiones' }}
                        </h1>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN RÁPIDA -->
            @if($isAdmin)
            <div class="flex items-center gap-3 flex-wrap">
                {{-- Botón Liquidar --}}
                <button type="button" onclick="openLiquidarModal()"
                        id="btnLiquidar"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <i class="fas fa-circle-check"></i>
                    <span>LIQUIDAR PAGO</span>
                </button>
                {{-- Botón Nuevo Bono --}}
                <button type="button" onclick="openCreateModal()"
                        id="btnNuevaComision"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-lg shadow-blue-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <i class="fas fa-plus"></i>
                    <span>NUEVO BONO</span>
                </button>
            </div>
            @endif
        </div>

        <!-- SUB-NAV / PESTAÑAS (SOLO COMISIONES) -->
        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones de comisiones">
            <a href="{{ route('comisiones.porSemana') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-calendar-week text-slate-400"></i>
                <span>Comisiones por Semana</span>
            </a>
            <a href="{{ route('comisiones.porVendedor') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-users-gear text-slate-400"></i>
                <span>Comisiones por Vendedor</span>
            </a>
            <a href="{{ route('comisiones.index') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-coins"></i>
                <span>Comisiones</span>
            </a>
            <a href="{{ route('comisiones.ajustes') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-arrows-rotate text-slate-400"></i>
                <span>Ajustes</span>
            </a>
        </nav>

        <!-- FILTROS GLOBALES -->
        <section class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
            <form id="filtrosForm" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-3.5 items-end">

                @if($isAdmin)
                {{-- SELECT VENDEDOR --}}
                <div class="lg:col-span-3">
                    <label for="filtroVendedor" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-user-tie text-slate-400 mr-1"></i> Vendedor
                    </label>
                    <select id="filtroVendedor" name="vendedor_id" data-searchable-vendedor
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <option value="">Todos los vendedores</option>
                        @foreach($vendedores as $v)
                            <option value="{{ $v->id }}" {{ request('vendedor_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->nombre_real }} ({{ $v->username }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- ESTADO --}}
                <div class="{{ $isAdmin ? 'lg:col-span-2' : 'lg:col-span-3' }}">
                    <label for="filtroEstado" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-tag text-slate-400 mr-1"></i> Estado
                    </label>
                    <select id="filtroEstado" name="estado"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <option value="todos">Todos los estados</option>
                        <option value="Pendiente" {{ request('estado') === 'Pendiente' ? 'selected' : '' }}>Pendiente (Por liquidar)</option>
                        <option value="Pagada"    {{ request('estado') === 'Pagada'    ? 'selected' : '' }}>Pagada</option>
                        <option value="Cancelada" {{ request('estado') === 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                {{-- MÉTODO DE PAGO --}}
                <div class="{{ $isAdmin ? 'lg:col-span-2' : 'lg:col-span-3' }}">
                    <label for="filtroMetodoPago" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-wallet text-slate-400 mr-1"></i> Método de Pago
                    </label>
                    <select id="filtroMetodoPago" name="metodo_pago"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <option value="todos">Todos los métodos</option>
                        <option value="Efectivo" {{ request('metodo_pago') === 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="Transferencia Bancaria" {{ request('metodo_pago') === 'Transferencia Bancaria' ? 'selected' : '' }}>Transferencia Bancaria</option>
                    </select>
                </div>

                {{-- FECHA DESDE --}}
                <div class="lg:col-span-2">
                    <label for="filtroDesde" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-calendar-day text-slate-400 mr-1"></i> Desde
                    </label>
                    <input type="date" id="filtroDesde" name="fecha_desde"
                           value="{{ request('fecha_desde') }}"
                           class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                </div>

                {{-- FECHA HASTA --}}
                <div class="lg:col-span-2">
                    <label for="filtroHasta" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        <i class="fas fa-calendar-check text-slate-400 mr-1"></i> Hasta
                    </label>
                    <input type="date" id="filtroHasta" name="fecha_hasta"
                           value="{{ request('fecha_hasta') }}"
                           class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                </div>

                {{-- BOTONES DE FILTRADO --}}
                <div class="lg:col-span-1 flex items-center gap-1.5">
                    <button type="button" onclick="aplicarFiltros()"
                            class="w-full rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-2.5 text-xs transition-all shadow-md shadow-blue-600/15 flex items-center justify-center gap-1 cursor-pointer"
                            title="Aplicar filtros">
                        <i class="fas fa-filter text-[11px]"></i>
                        <span>Filtrar</span>
                    </button>
                    <button type="button" onclick="limpiarFiltros()"
                            class="rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-500 p-2 text-xs transition-all cursor-pointer"
                            title="Restablecer">
                        <i class="fas fa-rotate-left"></i>
                    </button>
                </div>
            </form>
        </section>

        <!-- KPI CARDS (ESTILO VENTAS) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- TOTAL COMISIONES -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Total Comisiones</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight" id="kpiTotal">${{ number_format($stats['total'], 2) }}</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>{{ $comisiones->total() }} registros</span>
                    </p>
                </div>
            </div>

            <!-- POR PAGAR (PENDIENTE) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Por Pagar (Pendiente)</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight" id="kpiPendiente">${{ number_format($stats['pendiente'], 2) }}</h3>
                    <p class="text-xs font-semibold text-amber-600 mt-1">Por liquidar a vendedores</p>
                </div>
            </div>

            <!-- PAGADAS / LIQUIDADAS -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Pagadas / Liquidadas</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight" id="kpiPagada">${{ number_format($stats['pagada'], 2) }}</h3>
                    <p class="text-xs font-semibold text-emerald-600 mt-1">Total transferido / entregado</p>
                </div>
            </div>

            <!-- CANCELADAS -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Canceladas</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight" id="kpiCancelada">${{ number_format($stats['cancelada'], 2) }}</h3>
                    <p class="text-xs font-semibold text-slate-400 mt-1">Anuladas por devoluciones</p>
                </div>
            </div>
        </div>

        <!-- TABLA PRINCIPAL DE COMISIONES -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden relative" id="tableContainer">
            {{-- Loader --}}
            <div id="tableLoading" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-20 hidden">
                <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-2xl shadow-xl border border-slate-100 text-blue-600 text-sm font-bold">
                    <i class="fas fa-spinner fa-spin text-lg"></i>
                    <span>Cargando comisiones...</span>
                </div>
            </div>

            <div id="tableWrapper">
                @include('comisiones.partials.table', compact('comisiones', 'isAdmin'))
            </div>
        </div>

    </div>

    {{-- MODALES --}}
    @include('comisiones.modals')

    {{-- SCRIPTS --}}
    <script>
    const ROUTES = {
        index:      "{{ route('comisiones.index') }}",
        pendientes: "{{ url('comisiones/pendientes') }}",
        store:      @if($isAdmin) "{{ route('comisiones.store') }}" @else "null" @endif,
        update:     @if($isAdmin) "{{ url('comisiones') }}" @else "null" @endif,
        liquidar:   @if($isAdmin) "{{ route('comisiones.liquidar') }}" @else "null" @endif,
        cancelar:   @if($isAdmin) "{{ url('comisiones') }}" @else "null" @endif,
    };
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const IS_ADMIN = {{ $isAdmin ? 'true' : 'false' }};

    // ── Filtros AJAX ──────────────────────────────────────────────────────

    async function aplicarFiltros() {
        const vendedorFiltro = document.getElementById('filtroVendedor');
        if (vendedorFiltro?._searchPending) {
            vendedorFiltro._searchHelp?.classList.remove('hidden');
            vendedorFiltro._searchInput?.focus();
            return;
        }

        const loading = document.getElementById('tableLoading');
        loading.classList.remove('hidden');

        const params = new URLSearchParams();
        @if($isAdmin)
        const vendedor = document.getElementById('filtroVendedor')?.value;
        if (vendedor) params.set('vendedor_id', vendedor);
        @endif
        const estado = document.getElementById('filtroEstado')?.value;
        if (estado && estado !== 'todos') params.set('estado', estado);
        const metodoPago = document.getElementById('filtroMetodoPago')?.value;
        if (metodoPago && metodoPago !== 'todos') params.set('metodo_pago', metodoPago);
        const desde = document.getElementById('filtroDesde')?.value;
        if (desde) params.set('fecha_desde', desde);
        const hasta = document.getElementById('filtroHasta')?.value;
        if (hasta) params.set('fecha_hasta', hasta);

        try {
            const res = await fetch(`${ROUTES.index}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('tableWrapper').innerHTML = data.table;
                if (data.stats) {
                    document.getElementById('kpiTotal').textContent     = '$' + formatNum(data.stats.total);
                    document.getElementById('kpiPendiente').textContent = '$' + formatNum(data.stats.pendiente);
                    document.getElementById('kpiPagada').textContent    = '$' + formatNum(data.stats.pagada);
                    document.getElementById('kpiCancelada').textContent = '$' + formatNum(data.stats.cancelada);
                }
            }
        } catch(e) {
            console.error(e);
        } finally {
            loading.classList.add('hidden');
        }
    }

    function limpiarFiltros() {
        @if($isAdmin)
        if (document.getElementById('filtroVendedor')) {
            document.getElementById('filtroVendedor').value = '';
            document.getElementById('filtroVendedor').dispatchEvent(new Event('change', { bubbles: true }));
        }
        @endif
        document.getElementById('filtroEstado').value     = 'todos';
        document.getElementById('filtroMetodoPago').value = 'todos';
        document.getElementById('filtroDesde').value      = '';
        document.getElementById('filtroHasta').value      = '';
        aplicarFiltros();
    }

    function formatNum(n) {
        return parseFloat(n).toLocaleString('es-SV', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // ── Cargar pendientes dinámicos al seleccionar vendedor para liquidar ──

    async function cargarPendientesVendedor(vendedorId) {
        const resumenBox = document.getElementById('liquidarResumenBox');
        const emptyBox   = document.getElementById('liquidarEmptyBox');
        const loadingBox = document.getElementById('liquidarLoadingBox');
        const lista      = document.getElementById('liquidarDetalleLista');
        const btnSubmit  = document.getElementById('btnConfirmLiquidar');

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
                Swal.fire({ icon: 'success', title: '¡Actualizado!', text: data.message, timer: 2000, showConfirmButton: false });
                aplicarFiltros();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            }
        } catch(e) {
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
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body:    JSON.stringify({ _method: 'PATCH' }),
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire({ icon: 'success', title: '¡Cancelada!', text: data.message, timer: 2000, showConfirmButton: false });
                aplicarFiltros();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            }
        } catch(e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cancelar la comisión.' });
        }
    }

    // ── Liquidar con Método de Pago (admin) ────────────────────────────────

    function openLiquidarModal(vendedorId = null, bloquear = false, fechaDesde = '', fechaHasta = '') {
        const modal = document.getElementById('modalLiquidar');
        const form = document.getElementById('formLiquidar');
        if (!modal || !form) return;

        modal.classList.remove('hidden');
        form.reset();
        window._vendedorLiquidarBloqueado = false;

        const fDesde = fechaDesde || document.getElementById('filtroDesde')?.value || '';
        const fHasta = fechaHasta || document.getElementById('filtroHasta')?.value || '';

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
        if (vendedorId) {
            const item = document.querySelector(`#listaVendedoresLiquidar .vendedor-item[data-id="${vendedorId}"]`);
            if (item && typeof seleccionarVendedorLiquidar === 'function') {
                const nombre = item.dataset.displayName || '';
                const username = item.dataset.displayUser || '';
                const initials = item.dataset.initials || '';
                seleccionarVendedorLiquidar(vendedorId, nombre, username, initials, Boolean(bloquear));
            } else if (typeof cargarPendientesVendedor === 'function') {
                const inputHidden = document.getElementById('liquidarVendedor');
                if (inputHidden) inputHidden.value = vendedorId;
                cargarPendientesVendedor(vendedorId);
            }
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
            Swal.fire({
                icon: 'warning',
                title: 'Sin comisiones seleccionadas',
                text: 'Debes seleccionar al menos una comisión pendiente para proceder con el pago.',
                confirmButtonColor: '#059669',
            });
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
        document.getElementById('liquidarStep2')?.classList.add('hidden');
        document.getElementById('formLiquidar')?.classList.remove('hidden');
        // Reiniciar indicador
        if (document.getElementById('stepProgressLine')) document.getElementById('stepProgressLine').style.width = '0%';
        if (document.getElementById('stepIndicator2')) document.getElementById('stepIndicator2').classList.add('opacity-35');
        const s2 = document.getElementById('step2Circle');
        if (s2) { s2.classList.replace('bg-emerald-600', 'bg-slate-300'); }
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

        checkboxes.forEach(cb => {
            formData.append('comisiones_ids[]', cb.value);
        });

        const btnFinal = document.getElementById('btnFinalConfirmLiquidar');
        if (btnFinal) {
            btnFinal.disabled = true;
            btnFinal.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Procesando...</span>';
        }

        Swal.fire({
            title: 'Procesando pago...',
            text: 'Por favor espera un momento',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const res = await fetch(ROUTES.liquidar, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                body:    formData,
            });
            const data = await res.json();
            if (data.success) {
                closeLiquidarModal();
                Swal.fire({ icon: 'success', title: '¡Liquidación Completada!', text: data.message, timer: 2500, showConfirmButton: false });
                aplicarFiltros();
            } else {
                if (btnFinal) {
                    btnFinal.disabled = false;
                    btnFinal.innerHTML = '<i class="fas fa-check-circle"></i><span>Confirmar Pago</span>';
                }
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo procesar la liquidación.' });
            }
        } catch(e) {
            if (btnFinal) {
                btnFinal.disabled = false;
                btnFinal.innerHTML = '<i class="fas fa-check-circle"></i><span>Confirmar Pago</span>';
            }
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
                Swal.fire({ icon: 'success', title: '¡Registrado!', text: data.message, timer: 2000, showConfirmButton: false });
                aplicarFiltros();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Verifica los datos ingresados.' });
            }
        } catch(e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo registrar la comisión.' });
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
    </script>
    @if($isAdmin)
        @include('comisiones.partials.searchable-vendedor-select')
    @endif
</x-app>
