<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Categorías — AXStore</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SortableJS para Drag & Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/home.css', 'resources/css/usuarios.css', 'resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes cardFadeIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .view-btn.active {
            background-color: #3b82f6;
            color: white;
            box-shadow: 0 4px 12px -2px #3b82f640;
        }
        .view-btn:not(.active) {
            background-color: #f1f5f9;
            color: #64748b;
        }
        .view-btn:not(.active):hover {
            background-color: #e2e8f0;
            color: #334155;
        }

        /* Filtro de estado */
        .estado-tab {
            padding: 0.375rem 0.875rem;
            border-radius: 0.625rem;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            border: 1.5px solid transparent;
        }
        .estado-tab.active-all    { background:#eff6ff; color:#2563eb; border-color:#bfdbfe; }
        .estado-tab.active-act    { background:#f0fdf4; color:#16a34a; border-color:#bbf7d0; }
        .estado-tab.active-ina    { background:#f1f5f9; color:#64748b; border-color:#e2e8f0; }
        .estado-tab:not(.active-all):not(.active-act):not(.active-ina) {
            background:#f8fafc; color:#64748b;
        }
        .estado-tab:hover:not(.active-all):not(.active-act):not(.active-ina) {
            background:#f1f5f9;
        }

        #cardsWrapper {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            padding: 1.25rem;
        }
        @media (min-width: 640px)  { #cardsWrapper { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1024px) { #cardsWrapper { grid-template-columns: repeat(4, 1fr); } }

        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: skeleton-shimmer 1.4s infinite;
            border-radius: 0.75rem;
        }
        @keyframes skeleton-shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        .sortable-ghost {
            opacity: 0.4;
            background-color: #f8fafc;
        }
        .drag-handle {
            cursor: grab;
        }
        .drag-handle:active {
            cursor: grabbing;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased font-['Plus_Jakarta_Sans',sans-serif] bg-slate-50 flex flex-col">

    <header class="bg-white/85 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 transition-all duration-300">
        @include('componentsHome.header')
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- ENCABEZADO -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1.5">
                    <i class="fas fa-tag"></i>
                    <span>Módulo de Catálogo</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Gestión de Categorías
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Administra las categorías del catálogo de productos de AXStore.
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <!-- Exportar PDF -->
                <a href="{{ route('categorias.export', ['estado' => $estado]) }}" id="btnExport"
                   class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-bold text-sm transition-colors duration-200 shadow-xs cursor-pointer border border-red-200">
                    <i class="fas fa-file-pdf text-red-600"></i>
                    <span class="hidden sm:inline">Exportar PDF</span>
                </a>

                <!-- Toggle tabla / cards -->
                <div class="flex items-center bg-slate-100 rounded-xl p-1 gap-1">
                    <button id="btnViewTable" type="button" onclick="setView('table')"
                            class="view-btn w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 cursor-pointer" title="Vista tabla">
                        <i class="fas fa-list text-sm"></i>
                    </button>
                    <button id="btnViewCards" type="button" onclick="setView('cards')"
                            class="view-btn w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 cursor-pointer" title="Vista tarjetas">
                        <i class="fas fa-grip text-sm"></i>
                    </button>
                </div>

                <!-- Botón agregar -->
                <button type="button" onclick="openCreateModal()"
                        class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-lg shadow-blue-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                        id="btnOpenAddModal">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-xs">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span>AGREGAR</span>
                </button>
            </div>
        </div>

        <!-- MÉTRICAS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 mb-8">
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-blue-300 transition-colors cursor-pointer" onclick="setEstado('todas')">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Categorías</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                        <i class="fas fa-tag"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-3" id="kpiTotal">{{ $stats['total'] }}</p>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-emerald-300 transition-colors cursor-pointer" onclick="setEstado('activas')">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Activas</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-3" id="kpiActivas">{{ $stats['activas'] }}</p>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-slate-400 transition-colors cursor-pointer" onclick="setEstado('inactivos')">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Inactivas</span>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-base">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-3" id="kpiInactivos">{{ $stats['inactivos'] }}</p>
            </div>
        </div>

        <!-- BÚSQUEDA + FILTROS -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs mb-6">
            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-search text-sm"></i>
                    </div>
                    <input type="text" id="searchInput"
                           placeholder="Buscar categoría por nombre o descripción..."
                           class="w-full pl-10 pr-10 py-2.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 focus:border-blue-600 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all"
                           oninput="onSearchInput()">
                    <button type="button" id="clearSearchBtn" onclick="clearSearch()"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 hidden cursor-pointer">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:inline">Ver:</span>
                    <button type="button" id="tabTodas"     onclick="setEstado('todas')"     class="estado-tab">Todas</button>
                    <button type="button" id="tabActivas"   onclick="setEstado('activas')"   class="estado-tab">Activas</button>
                    <button type="button" id="tabInactivos" onclick="setEstado('inactivos')" class="estado-tab">Inactivas</button>
                </div>
                <button type="button" onclick="clearSearch()" class="p-2 text-slate-400 hover:text-blue-600 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer flex-shrink-0" title="Limpiar">
                    <i class="fas fa-rotate-right text-xs"></i>
                </button>
            </div>
        </div>

        <!-- TABLA / CARDS -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden relative" id="tableContainer">
            <div id="tableLoading" class="hidden p-5">
                <!-- Skeletons -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @for($i=0;$i<8;$i++)
                    <div class="rounded-2xl overflow-hidden border border-slate-100">
                        <div class="skeleton h-28"></div>
                        <div class="p-4 space-y-2">
                            <div class="skeleton h-4 w-3/4 rounded"></div>
                            <div class="skeleton h-3 w-full rounded"></div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            <div id="tableWrapper">
                @include('categorias.partials.table', ['categorias' => $categorias, 'estado' => $estado])
            </div>
            <div id="cardsWrapper" class="hidden">
                @include('categorias.partials.cards', ['categorias' => $categorias, 'estado' => $estado])
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200/80 mt-12">
        @include('componentsHome.footer')
    </footer>

    @include('categorias.modal-categoria')

    <script>
        const ROUTES = {
            index:        '{{ route('categorias.index') }}',
            store:        '{{ route('categorias.store') }}',
            update:       (id) => `/categorias/${id}`,
            toggleStatus: (id) => `/categorias/${id}/toggle-status`,
            reorder:      '{{ route('categorias.reorder') }}',
            export:       '{{ route('categorias.export') }}',
        };
        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let currentView   = localStorage.getItem('cat_view')   ?? 'table';
        let currentEstado = localStorage.getItem('cat_estado') ?? '{{ $estado }}';
        
        let sortableTable, sortableCards;

        function setView(view) {
            currentView = view;
            localStorage.setItem('cat_view', view);

            const tw = document.getElementById('tableWrapper');
            const cw = document.getElementById('cardsWrapper');
            const bt = document.getElementById('btnViewTable');
            const bc = document.getElementById('btnViewCards');

            if (view === 'table') {
                tw.classList.remove('hidden'); cw.classList.add('hidden');
                bt.classList.add('active');    bc.classList.remove('active');
            } else {
                tw.classList.add('hidden');    cw.classList.remove('hidden');
                bc.classList.add('active');    bt.classList.remove('active');
            }
            filterItems();
        }

        function setEstado(estado) {
            currentEstado = estado;
            localStorage.setItem('cat_estado', estado);

            const tabs = { todas: 'tabTodas', activas: 'tabActivas', inactivos: 'tabInactivos' };
            const cls  = { todas: 'active-all', activas: 'active-act', inactivos: 'active-ina' };

            Object.entries(tabs).forEach(([key, id]) => {
                const el = document.getElementById(id);
                el.classList.remove('active-all', 'active-act', 'active-ina');
                if (key === estado) el.classList.add(cls[key]);
            });
            
            // Actualizar ruta del botón exportar
            document.getElementById('btnExport').href = `${ROUTES.export}?estado=${estado}`;

            refreshContent();
        }

        function initSortable() {
            const tbody = document.getElementById('categoriasBody');
            const grid  = document.getElementById('cardsWrapper');
            
            const options = {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                onEnd: function () {
                    saveOrder();
                }
            };

            if (tbody) sortableTable = new Sortable(tbody, options);
            if (grid)  sortableCards = new Sortable(grid, options);
        }

        async function saveOrder() {
            // Obtenemos el nuevo orden desde la vista actual
            const container = currentView === 'table' ? '#categoriasBody .categoria-row' : '#cardsWrapper .categoria-card';
            const order = Array.from(document.querySelectorAll(container)).map(el => el.dataset.id);
            
            if (order.length === 0) return;

            try {
                const res = await fetch(ROUTES.reorder, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({ order })
                });
                
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Error guardando el orden');
                
                // Mostrar un pequeño toast (sin interrumpir)
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 2000
                });
                Toast.fire({ icon: 'success', title: 'Orden guardado' });

            } catch (err) {
                console.error(err);
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar el orden.' });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            setView(currentView);
            setEstado(currentEstado);
            initSortable();
        });

        function openCreateModal() {
            const icon = document.getElementById('modalHeaderIcon');
            if (icon) icon.className = 'fas fa-plus-circle';
            const title = document.getElementById('modalTitle');
            if (title) title.textContent = 'Nueva Categoría';
            const sub = document.getElementById('modalSubtitle');
            if (sub) sub.textContent = 'Completa los campos para registrar una categoría';
            const btn = document.getElementById('btnSubmitText');
            if (btn) btn.textContent = 'GUARDAR CATEGORÍA';

            document.getElementById('modalMethod').value      = 'POST';
            document.getElementById('categoriaId').value      = '';
            document.getElementById('inputNombre').value      = '';
            document.getElementById('inputDescripcion').value = '';

            const pName = document.getElementById('previewName');
            if (pName) pName.textContent = 'Nueva Categoría';
            const pDesc = document.getElementById('previewDesc');
            if (pDesc) pDesc.textContent = '---';

            clearErrors();
            selectColor('#3b82f6', '#eff6ff');
            selectIcon('fa-tag');
            showModal();
        }

        function openEditModal(id, nombre, descripcion, color, icono) {
            const icon = document.getElementById('modalHeaderIcon');
            if (icon) icon.className = 'fas fa-edit';
            const title = document.getElementById('modalTitle');
            if (title) title.textContent = 'Editar Categoría';
            const sub = document.getElementById('modalSubtitle');
            if (sub) sub.textContent = 'Modifica los datos de la categoría';
            const btn = document.getElementById('btnSubmitText');
            if (btn) btn.textContent = 'GUARDAR CAMBIOS';

            document.getElementById('modalMethod').value      = 'PUT';
            document.getElementById('categoriaId').value      = id;
            document.getElementById('inputNombre').value      = nombre;
            document.getElementById('inputDescripcion').value = descripcion;

            const pName = document.getElementById('previewName');
            if (pName) pName.textContent = nombre || 'Nueva Categoría';
            const pDesc = document.getElementById('previewDesc');
            if (pDesc) pDesc.textContent = descripcion || '---';

            clearErrors();
            
            const foundColor = (window.COLOR_PALETTE ?? []).find(c => c.hex === color);
            selectColor(color, foundColor?.light ?? '#eff6ff');
            
            const foundIcon = (window.ICON_PALETTE ?? []).includes(icono) ? icono : 'fa-tag';
            selectIcon(foundIcon);
            
            showModal();
        }

        function showModal() {
            const modal = document.getElementById('categoriaModal');
            const bd    = document.getElementById('modalBackdrop');
            const panel = document.getElementById('modalPanel');
            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                bd.classList.remove('opacity-0'); bd.classList.add('opacity-100');
                panel.classList.remove('scale-95','opacity-0'); panel.classList.add('scale-100','opacity-100');
            });
            setTimeout(() => document.getElementById('inputNombre').focus(), 250);
        }

        function closeModal() {
            const modal = document.getElementById('categoriaModal');
            const bd    = document.getElementById('modalBackdrop');
            const panel = document.getElementById('modalPanel');
            bd.classList.remove('opacity-100'); bd.classList.add('opacity-0');
            panel.classList.remove('scale-100','opacity-100'); panel.classList.add('scale-95','opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 250);
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        document.getElementById('categoriaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();

            const id     = document.getElementById('categoriaId').value;
            const method = document.getElementById('modalMethod').value;
            const url    = id ? ROUTES.update(id) : ROUTES.store;
            const body   = new FormData();

            body.append('_token',      CSRF);
            body.append('nombre',      document.getElementById('inputNombre').value.trim());
            body.append('descripcion', document.getElementById('inputDescripcion').value.trim());
            body.append('color',       document.getElementById('inputColor').value);
            body.append('icono',       document.getElementById('inputIcono').value);
            if (method === 'PUT') body.append('_method', 'PUT');

            const btn  = document.getElementById('btnSubmitModal');
            const span = document.getElementById('btnSubmitText');
            btn.disabled = true; span.textContent = 'Guardando...';

            try {
                const res  = await fetch(url, { method: 'POST', body });
                const data = await res.json();

                if (res.ok && data.success) {
                    closeModal();
                    await refreshContent();
                    Swal.fire({ icon:'success', title:'¡Listo!', text:data.message, timer:2000, showConfirmButton:false, timerProgressBar:true });
                } else if (res.status === 422) {
                    showErrors(data.errors ?? {});
                } else {
                    Swal.fire({ icon:'error', title:'Error', text:data.message ?? 'Error inesperado.' });
                }
            } catch {
                Swal.fire({ icon:'error', title:'Error de red', text:'No se pudo conectar.' });
            } finally {
                btn.disabled = false;
                span.textContent = id ? 'GUARDAR CAMBIOS' : 'GUARDAR CATEGORÍA';
            }
        });

        async function toggleStatus(id, nombre, estadoActivo) {
            const actionText = estadoActivo ? 'activar' : 'inactivar';
            const actionIcon = estadoActivo ? 'fa-check' : 'fa-ban';
            const btnColor   = estadoActivo ? '#22c55e' : '#f97316';
            const htmlText   = estadoActivo 
                ? `La categoría <strong class="text-slate-800">${nombre}</strong> volverá a estar visible.` 
                : `La categoría <strong class="text-slate-800">${nombre}</strong> se ocultará temporalmente.`;

            const result = await Swal.fire({
                title: `¿${actionText.charAt(0).toUpperCase() + actionText.slice(1)} categoría?`,
                html: `<p class="text-slate-600 text-sm">${htmlText}</p>`,
                icon: 'warning',
                iconColor: btnColor,
                showCancelButton: true,
                confirmButtonColor: btnColor,
                cancelButtonColor: '#94a3b8',
                confirmButtonText: `<i class="fas ${actionIcon} mr-1"></i> ${actionText.charAt(0).toUpperCase() + actionText.slice(1)}`,
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
            });

            if (!result.isConfirmed) return;

            try {
                const res  = await fetch(ROUTES.toggleStatus(id), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `_token=${CSRF}&_method=PATCH`,
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    await refreshContent();
                    Swal.fire({ icon:'success', title:'¡Hecho!', text:data.message, timer:2000, showConfirmButton:false, timerProgressBar:true });
                } else {
                    Swal.fire({ icon:'error', title:'Error', text:data.message ?? `No se pudo ${actionText}.` });
                }
            } catch {
                Swal.fire({ icon:'error', title:'Error de red', text:'No se pudo conectar.' });
            }
        }

        let searchTimeout;

        function onSearchInput() {
            const val = document.getElementById('searchInput').value;
            document.getElementById('clearSearchBtn').classList.toggle('hidden', !val);
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterItems, 200);
        }

        function filterItems() {
            const q = document.getElementById('searchInput').value.toLowerCase().trim();
            document.querySelectorAll('.categoria-row').forEach(r => {
                r.style.display = (!q || r.dataset.nombre.includes(q) || r.dataset.desc.includes(q)) ? '' : 'none';
            });
            document.querySelectorAll('.categoria-card').forEach(c => {
                const m = !q || c.dataset.nombre.includes(q) || c.dataset.desc.includes(q);
                c.style.display = m ? '' : 'none';
            });
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('clearSearchBtn').classList.add('hidden');
            document.querySelectorAll('.categoria-row, .categoria-card').forEach(el => el.style.display = '');
        }

        async function refreshContent() {
            document.getElementById('tableWrapper').classList.add('hidden');
            document.getElementById('cardsWrapper').classList.add('hidden');
            document.getElementById('tableLoading').classList.remove('hidden');

            try {
                const url  = `${ROUTES.index}?estado=${currentEstado}`;
                const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();

                document.getElementById('tableWrapper').innerHTML = data.table;
                document.getElementById('cardsWrapper').innerHTML = data.cards;

                if (data.stats) {
                    document.getElementById('kpiTotal').textContent     = data.stats.total;
                    document.getElementById('kpiActivas').textContent   = data.stats.activas;
                    document.getElementById('kpiInactivos').textContent = data.stats.inactivos;
                }
                
                // Re-inicializar drag&drop sobre los nuevos elementos del DOM
                initSortable();
            } finally {
                document.getElementById('tableLoading').classList.add('hidden');
                setView(currentView);
            }
        }

        function showErrors(errors) {
            Object.entries(errors).forEach(([field, msgs]) => {
                const el = document.getElementById('error' + field.charAt(0).toUpperCase() + field.slice(1));
                if (el) { el.textContent = msgs[0]; el.classList.remove('hidden'); }
            });
        }

        function clearErrors() {
            ['errorNombre','errorDescripcion'].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.textContent = ''; el.classList.add('hidden'); }
            });
        }
    </script>
</body>
</html>
