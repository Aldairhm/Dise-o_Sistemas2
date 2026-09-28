<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Comisiones de Vendedores — AXStore</title>

    <script>
        (function() {
            var t = localStorage.getItem('ax_theme') || 'system';
            if (t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/home.css', 'resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-slate-800 antialiased font-['Plus_Jakarta_Sans',sans-serif] bg-slate-50 flex flex-col">

    <header class="bg-white/85 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 transition-all duration-300">
        @include('componentsHome.header')
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1.5">
                    <i class="fas fa-hand-holding-dollar"></i>
                    <span>Módulo de Comisiones</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    {{ $isAdmin ? 'Gestión de Comisiones y Liquidaciones' : 'Mis Comisiones Ganadas' }}
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $isAdmin ? 'Control de pagos de comisiones, bonificaciones y liquidación con comprobantes.' : 'Consulta el estado y pagos de tus comisiones generadas por venta.' }}
                </p>
            </div>

            @if($isAdmin)
            <div class="flex items-center gap-3 flex-wrap">
                {{-- Botón Liquidar --}}
                <button type="button" onclick="openLiquidarModal()"
                        id="btnLiquidar"
                        class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-lg shadow-emerald-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-xs">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <span>LIQUIDAR PAGO</span>
                </button>
                {{-- Botón Registro manual / Bono --}}
                <button type="button" onclick="openCreateModal()"
                        id="btnNuevaComision"
                        class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm shadow-lg shadow-indigo-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-xs">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span>NUEVO BONO</span>
                </button>
            </div>
            @endif
        </div>

        {{-- KPI CARDS --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Histórico</span>
                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-sm">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <p class="text-2xl font-black text-slate-900 mt-3" id="kpiTotal">${{ number_format($stats['total'], 2) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-amber-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Por Pagar (Pendiente)</span>
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <p class="text-2xl font-black text-amber-700 mt-3" id="kpiPendiente">${{ number_format($stats['pendiente'], 2) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-emerald-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Pagadas</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <p class="text-2xl font-black text-emerald-700 mt-3" id="kpiPagada">${{ number_format($stats['pagada'], 2) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Canceladas</span>
                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-sm">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
                <p class="text-2xl font-black text-slate-900 mt-3" id="kpiCancelada">${{ number_format($stats['cancelada'], 2) }}</p>
            </div>
        </div>

        {{-- FILTROS --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs mb-6">
            <form id="filtrosForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-center">

                @if($isAdmin)
                {{-- Filtro vendedor --}}
                <div>
                    <select id="filtroVendedor" name="vendedor_id"
                            class="w-full py-2.5 px-3.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-xs sm:text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all">
                        <option value="">Todos los vendedores</option>
                        @foreach($vendedores as $v)
                            <option value="{{ $v->id }}" {{ request('vendedor_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->nombre_real }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Estado --}}
                <div>
                    <select id="filtroEstado" name="estado"
                            class="w-full py-2.5 px-3.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-xs sm:text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all">
                        <option value="todos">Todos los estados</option>
                        <option value="Pendiente" {{ request('estado') === 'Pendiente' ? 'selected' : '' }}>Pendiente (Por pagar)</option>
                        <option value="Pagada"    {{ request('estado') === 'Pagada'    ? 'selected' : '' }}>Pagada</option>
                        <option value="Cancelada" {{ request('estado') === 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                {{-- Método de Pago --}}
                <div>
                    <select id="filtroMetodoPago" name="metodo_pago"
                            class="w-full py-2.5 px-3.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-xs sm:text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all">
                        <option value="todos">Todos los métodos de pago</option>
                        <option value="Efectivo" {{ request('metodo_pago') === 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="Transferencia Bancaria" {{ request('metodo_pago') === 'Transferencia Bancaria' ? 'selected' : '' }}>Transferencia Bancaria</option>
                        <option value="Cheque" {{ request('metodo_pago') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="Billetera Digital" {{ request('metodo_pago') === 'Billetera Digital' ? 'selected' : '' }}>Billetera Digital</option>
                    </select>
                </div>

                {{-- Fechas --}}
                <div class="flex items-center gap-1.5">
                    <input type="date" id="filtroDesde" name="fecha_desde"
                           value="{{ request('fecha_desde') }}"
                           class="w-1/2 py-2 px-2 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-xs text-slate-700 focus:outline-none"
                           placeholder="Desde">
                    <span class="text-slate-400 text-xs">—</span>
                    <input type="date" id="filtroHasta" name="fecha_hasta"
                           value="{{ request('fecha_hasta') }}"
                           class="w-1/2 py-2 px-2 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-xs text-slate-700 focus:outline-none"
                           placeholder="Hasta">
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="aplicarFiltros()"
                            class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm rounded-xl transition-all duration-200 cursor-pointer text-center">
                        <i class="fas fa-filter mr-1"></i>Filtrar
                    </button>
                    <button type="button" onclick="limpiarFiltros()"
                            class="p-2.5 text-slate-400 hover:text-indigo-600 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer flex-shrink-0" title="Limpiar filtros">
                        <i class="fas fa-rotate-right text-sm"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- TABLA --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden relative" id="tableContainer">
            {{-- Loader --}}
            <div id="tableLoading" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-20 hidden">
                <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-2xl shadow-xl border border-slate-100 text-indigo-600 text-sm font-bold">
                    <i class="fas fa-spinner fa-spin text-lg"></i>
                    <span>Cargando comisiones...</span>
                </div>
            </div>

            <div id="tableWrapper">
                @include('comisiones.partials.table', compact('comisiones', 'isAdmin'))
            </div>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200/80 mt-12">
        @include('componentsHome.footer')
    </footer>

    {{-- ═══════════════════════════════════════════════════════════════════
         MODALES
    ═══════════════════════════════════════════════════════════════════════ --}}
    @include('comisiones.modals')

    {{-- ═══════════════════════════════════════════════════════════════════
         JS
    ═══════════════════════════════════════════════════════════════════════ --}}
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
        if (document.getElementById('filtroVendedor')) document.getElementById('filtroVendedor').value = '';
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
            const res = await fetch(`${ROUTES.pendientes}/${vendedorId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            loadingBox.classList.add('hidden');

            if (data.success && data.cantidad > 0) {
                document.getElementById('liquidarTotalMonto').textContent = '$' + formatNum(data.total);
                document.getElementById('liquidarCountBadge').textContent = `${data.cantidad} comisiones pendientes`;

                let html = '';
                data.comisiones.forEach(c => {
                    html += `
                        <label class="flex items-center justify-between p-2 bg-white rounded-lg border border-emerald-100 text-xs hover:bg-emerald-50/50 cursor-pointer">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <input type="checkbox" name="comisiones_ids[]" value="${c.id}" checked onchange="recalcularTotalSeleccionado()" class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="truncate font-medium text-slate-800">${c.concepto}</span>
                            </div>
                            <span class="font-extrabold text-emerald-700 ml-2 flex-shrink-0" data-monto="${c.monto}">$${formatNum(c.monto)}</span>
                        </label>
                    `;
                });
                lista.innerHTML = html;
                resumenBox.classList.remove('hidden');
                if (btnSubmit) btnSubmit.disabled = false;
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
        document.getElementById('liquidarTotalMonto').textContent = '$' + formatNum(total);
        document.getElementById('liquidarCountBadge').textContent = `${checkboxes.length} seleccionadas`;
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

    function openLiquidarModal() {
        document.getElementById('modalLiquidar').classList.remove('hidden');
        document.getElementById('formLiquidar').reset();
        document.getElementById('liquidarResumenBox').classList.add('hidden');
        document.getElementById('liquidarEmptyBox').classList.add('hidden');
    }

    function closeLiquidarModal() {
        document.getElementById('modalLiquidar').classList.add('hidden');
    }

    document.getElementById('formLiquidar')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const vendedorId = document.getElementById('liquidarVendedor').value;

        if (!vendedorId) {
            Swal.fire({ icon: 'warning', title: 'Selecciona un vendedor', showConfirmButton: false, timer: 1800 });
            return;
        }

        const formData = new FormData();
        formData.append('id_vendedor', vendedorId);
        formData.append('metodo_pago', document.getElementById('liquidarMetodoPago').value);
        formData.append('referencia_pago', document.getElementById('liquidarReferencia').value || '');
        formData.append('notas', document.getElementById('liquidarNotas').value || '');

        const comprobante = document.getElementById('liquidarComprobante').files[0];
        if (comprobante) {
            formData.append('comprobante_pago', comprobante);
        }

        const checkboxes = document.querySelectorAll('input[name="comisiones_ids[]"]:checked');
        checkboxes.forEach(cb => {
            formData.append('comisiones_ids[]', cb.value);
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
                Swal.fire({ icon: 'warning', title: 'Sin cambios', text: data.message });
            }
        } catch(e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo procesar la liquidación.' });
        }
    });

    // ── Crear manual / Bono (admin) ──────────────────────────────────────

    function openCreateModal() {
        document.getElementById('modalCreate').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('modalCreate').classList.add('hidden');
        document.getElementById('formCreate').reset();
    }

    function setConcepto(texto) {
        document.getElementById('createConcepto').value = texto;
    }

    document.getElementById('formCreate')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const body = {
            id_vendedor: document.getElementById('createVendedor').value,
            concepto:    document.getElementById('createConcepto').value,
            monto:       document.getElementById('createMonto').value,
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

</body>
</html>
