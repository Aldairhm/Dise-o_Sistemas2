<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Productos — AXStore</title>
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
    @vite(['resources/css/home.css', 'resources/css/productos.css', 'resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-slate-800 antialiased font-['Plus_Jakarta_Sans',sans-serif] bg-slate-50 flex flex-col">

    <header class="bg-white/85 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 transition-all duration-300">
        @include('componentsHome.header')
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- ENCABEZADO DE SECCIÓN --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1.5">
                    <i class="fas fa-boxes-stacked"></i>
                    <span>Módulo de Inventario</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Gestión de Productos
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Consulta y administra el catálogo de productos de AXStore.
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <!-- Tabla / Tarjetas -->
                <div class="flex items-center bg-slate-100 rounded-xl p-1 gap-1">
                    <button type="button" id="btnViewTable"
                            onclick="setView('table')"
                            class="prod-view-btn w-9 h-9 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors cursor-pointer"
                            title="Vista de tabla">
                        <i class="fas fa-table-list text-sm"></i>
                    </button>
                    <button type="button" id="btnViewCards"
                            onclick="setView('cards')"
                            class="prod-view-btn w-9 h-9 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors cursor-pointer"
                            title="Vista de tarjetas">
                        <i class="fas fa-grip text-sm"></i>
                    </button>
                </div>

            @if(Auth::user()->rol === 'admin')
                <a href="{{ route('productos.create') }}"
                   class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-lg shadow-blue-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-xs">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span>NUEVO PRODUCTO</span>
                </a>
            @endif
            </div>
        </div>

        {{-- FLASH SESSION (SweetAlert automático al volver de store/update) --}}
        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación exitosa!',
                    text: @json(session('success')),
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'swal-axstore' },
                });
            });
        </script>
        @endif

        @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'error',
                    title: '¡Acción no permitida!',
                    text: @json(session('error')),
                    confirmButtonText: 'Entendido',
                    customClass: { popup: 'swal-axstore' },
                });
            });
        </script>
        @endif


        <!-- BUSQUEDA Y FILTROS -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs mb-6">
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">

                <!-- Campo de Busqueda -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-search text-sm"></i>
                    </div>
                    <input type="text" 
                           id="searchInput" 
                           placeholder="Buscar producto por nombre..." 
                           class="w-full pl-10 pr-10 py-2.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 focus:border-blue-600 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all"
                           oninput="applyFilters()">
                    <button type="button" 
                            id="clearSearchBtn" 
                            onclick="clearSearch()" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 hidden cursor-pointer">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <!-- Filtros Dropdowns Personalizados -->
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Filtro por Categoría --}}
                    <div class="relative" data-custom-producto-select="categoria">
                        <label class="mb-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-tight">
                            <i class="fas fa-layer-group text-blue-600 text-xs"></i>
                            <span>Categoría</span>
                        </label>
                        <input type="hidden" id="categoriaFilter" value="todos">
                        <button type="button" class="h-10 min-w-[150px] max-w-[210px] rounded-xl border border-slate-200 bg-slate-50/90 px-3.5 text-xs font-bold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 flex items-center justify-between gap-2 cursor-pointer text-left" data-toggle aria-expanded="false">
                            <span class="flex items-center gap-2 truncate">
                                <i class="fas fa-border-all text-slate-400" data-icon></i>
                                <span class="truncate" data-label>Todas</span>
                            </span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" data-arrow></i>
                        </button>
                        <div class="hidden absolute left-0 top-full z-50 mt-1.5 w-full min-w-[180px] rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xl max-h-64 overflow-y-auto" data-menu>
                            <div class="space-y-0.5">
                                <button type="button" data-option data-value="todos" data-label="Todas" data-icon="fas fa-border-all text-slate-400"
                                    class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left bg-blue-50 text-blue-700">
                                    <span class="flex items-center gap-2 truncate">
                                        <i class="fas fa-border-all text-slate-400"></i>
                                        <span>Todas</span>
                                    </span>
                                    <i class="fas fa-check text-blue-600 text-[10px]" data-check></i>
                                </button>
                                @foreach($categorias as $cat)
                                    <button type="button" data-option data-value="{{ strtolower($cat->nombre) }}" data-label="{{ $cat->nombre }}" data-icon="fas fa-tag text-blue-500"
                                        class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                        <span class="flex items-center gap-2 truncate">
                                            <i class="fas fa-tag text-blue-500"></i>
                                            <span>{{ $cat->nombre }}</span>
                                        </span>
                                        <i class="fas fa-check text-blue-600 text-[10px] hidden" data-check></i>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Filtro por Estado --}}
                    <div class="relative" data-custom-producto-select="estado">
                        <label class="mb-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-tight">
                            <i class="fas fa-toggle-on text-blue-600 text-xs"></i>
                            <span>Estado</span>
                        </label>
                        <input type="hidden" id="statusFilter" value="todos">
                        <button type="button" class="h-10 min-w-[130px] rounded-xl border border-slate-200 bg-slate-50/90 px-3.5 text-xs font-bold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 flex items-center justify-between gap-2 cursor-pointer text-left" data-toggle aria-expanded="false">
                            <span class="flex items-center gap-2 truncate">
                                <i class="fas fa-layer-group text-slate-400" data-icon></i>
                                <span class="truncate" data-label>Todos</span>
                            </span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" data-arrow></i>
                        </button>
                        <div class="hidden absolute left-0 top-full z-50 mt-1.5 w-full min-w-[160px] rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xl" data-menu>
                            <div class="space-y-0.5">
                                <button type="button" data-option data-value="todos" data-label="Todos" data-icon="fas fa-layer-group text-slate-400"
                                    class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left bg-blue-50 text-blue-700">
                                    <span class="flex items-center gap-2 truncate">
                                        <i class="fas fa-layer-group text-slate-400"></i>
                                        <span>Todos</span>
                                    </span>
                                    <i class="fas fa-check text-blue-600 text-[10px]" data-check></i>
                                </button>
                                <button type="button" data-option data-value="1" data-label="Activos" data-icon="fas fa-circle-check text-emerald-500"
                                    class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                    <span class="flex items-center gap-2 truncate">
                                        <i class="fas fa-circle-check text-emerald-500"></i>
                                        <span>Activos</span>
                                    </span>
                                    <i class="fas fa-check text-blue-600 text-[10px] hidden" data-check></i>
                                </button>
                                <button type="button" data-option data-value="0" data-label="Inactivos" data-icon="fas fa-ban text-rose-500"
                                    class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                    <span class="flex items-center gap-2 truncate">
                                        <i class="fas fa-ban text-rose-500"></i>
                                        <span>Inactivos</span>
                                    </span>
                                    <i class="fas fa-check text-blue-600 text-[10px] hidden" data-check></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Botón Restablecer --}}
                    <div class="flex items-end">
                        <button type="button" 
                                onclick="resetAllFilters()" 
                                class="h-10 rounded-xl border border-slate-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-slate-500 px-3 text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm"
                                title="Restablecer filtros">
                            <i class="fas fa-rotate-left text-xs"></i>
                            <span class="hidden sm:inline">Limpiar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTENEDOR DE PRODUCTOS --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden relative" id="productsContainer">
            <!-- Skeleton Shimmer de Tabla de Productos -->
            <div id="tableSkeletonProductos" class="hidden p-5">
                <x-skeleton-table :rows="6" :cols="7" :avatarCol="1" />
            </div>

            <!-- Vista Tabla -->
            <div id="productsTableWrapper">
                @include('productos.partials.table', ['productos' => $productos])
            </div>

            <!-- Vista Tarjetas -->
            <div id="productsCardsWrapper" class="hidden">
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="productsCardsGrid">
                    @include('productos.partials.cards', ['productos' => $productos])
                </div>
            </div>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200/80 mt-12">
        @include('componentsHome.footer')
    </footer>

<script>
    let currentView = localStorage.getItem('ax_productos_view') || 'table';

    document.addEventListener('DOMContentLoaded', () => {
        setView(currentView, false);
    });

    function setView(view, save = true) {
        currentView = view;
        if (save) localStorage.setItem('ax_productos_view', view);

        const btnTable = document.getElementById('btnViewTable');
        const btnCards = document.getElementById('btnViewCards');
        const wrapperTable = document.getElementById('productsTableWrapper');
        const wrapperCards = document.getElementById('productsCardsWrapper');
        const mainContainer = document.getElementById('productsContainer');

        // Estilos botones
        document.querySelectorAll('.prod-view-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
            btn.classList.add('text-slate-400');
        });

        if (view === 'table') {
            btnTable.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
            btnTable.classList.remove('text-slate-400');
            wrapperTable.classList.remove('hidden');
            wrapperCards.classList.add('hidden');
            mainContainer.classList.remove('bg-transparent', 'border-transparent', 'shadow-none');
            mainContainer.classList.add('bg-white', 'border-slate-200/80', 'shadow-xs');
        } else {
            btnCards.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
            btnCards.classList.remove('text-slate-400');
            wrapperTable.classList.add('hidden');
            wrapperCards.classList.remove('hidden');
            mainContainer.classList.add('bg-transparent', 'border-transparent', 'shadow-none');
            mainContainer.classList.remove('bg-white', 'border-slate-200/80', 'shadow-xs');
        }
    }

    let filterDebounceTimer = null;

    function applyFilters() {
        const searchInput = document.getElementById('searchInput');
        const clearBtn = document.getElementById('clearSearchBtn');
        const query = (searchInput.value || '').trim().toLowerCase();
        const categoria = document.getElementById('categoriaFilter').value.trim().toLowerCase();
        const status = document.getElementById('statusFilter').value;

        // Mostrar/ocultar boton X
        if (query.length > 0) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }

        // Mostrar skeleton sutil de transición
        const skeleton = document.getElementById('tableSkeletonProductos');
        const tableWrapper = document.getElementById('productsTableWrapper');
        const cardsWrapper = document.getElementById('productsCardsWrapper');

        if (skeleton) {
            skeleton.classList.remove('hidden');
            if (tableWrapper) tableWrapper.classList.add('hidden');
            if (cardsWrapper) cardsWrapper.classList.add('hidden');
        }

        clearTimeout(filterDebounceTimer);
        filterDebounceTimer = setTimeout(() => {
            filtrarLocalmente(query, categoria, status);

            if (skeleton) {
                skeleton.classList.add('hidden');
                setView(currentView, false);
            }
        }, 120);
    }

    function filtrarLocalmente(query, categoria, status) {
        // Filtrar tabla
        let visibleRows = 0;
        document.querySelectorAll('.producto-row').forEach(row => {
            const rowNombre = (row.dataset.nombre || '').toLowerCase();
            const rowCat = (row.dataset.categoria || '').toLowerCase().trim();
            const rowEstado = String(row.dataset.estado);

            const matchName = !query || rowNombre.includes(query);
            const matchCat = categoria === 'todos' || rowCat === categoria || rowCat.includes(categoria);
            const matchStatus = status === 'todos' || rowEstado === String(status);

            if (matchName && matchCat && matchStatus) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });
        
        const noResultsTable = document.getElementById('table-no-results');
        const emptyRowTable = document.querySelector('.empty-row');
        if (noResultsTable) {
            noResultsTable.style.display = (visibleRows === 0 && !emptyRowTable) ? '' : 'none';
        }

        // Filtrar tarjetas
        let visibleCards = 0;
        document.querySelectorAll('.producto-card').forEach(card => {
            const cardNombre = (card.dataset.nombre || '').toLowerCase();
            const cardCat = (card.dataset.categoria || '').toLowerCase().trim();
            const cardEstado = String(card.dataset.estado);

            const matchName = !query || cardNombre.includes(query);
            const matchCat = categoria === 'todos' || cardCat === categoria || cardCat.includes(categoria);
            const matchStatus = status === 'todos' || cardEstado === String(status);

            if (matchName && matchCat && matchStatus) {
                card.style.display = '';
                visibleCards++;
            } else {
                card.style.display = 'none';
            }
        });

        const noResultsCards = document.getElementById('cards-no-results');
        const emptyCardsMsg = document.querySelector('.empty-cards');
        if (noResultsCards) {
            if (visibleCards === 0 && !emptyCardsMsg) {
                noResultsCards.classList.remove('hidden');
                noResultsCards.classList.add('flex');
            } else {
                noResultsCards.classList.add('hidden');
                noResultsCards.classList.remove('flex');
            }
        }
    }

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        applyFilters();
    }

    function resetAllFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('categoriaFilter').value = 'todos';
        document.getElementById('statusFilter').value = 'todos';
        if (window.resetProductoCustomSelects) {
            window.resetProductoCustomSelects();
        }
        applyFilters();
    }

    function confirmarEliminar(id, nombre) {
        Swal.fire({
            title: '¿Eliminar producto?',
            html: `Estás a punto de eliminar <strong>"${nombre}"</strong> y todas sus variantes. Esta acción no se puede deshacer.`,
            icon: 'warning',
            iconColor: '#f97316',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            reverseButtons: true,
            customClass: {
                popup:         'swal-axstore',
                title:         'swal-title-custom',
                htmlContainer: 'swal-html-custom',
                confirmButton: 'swal-btn-danger',
                cancelButton:  'swal-btn-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-delete-' + id).submit();
            }
        });
    }

    // ─── Activar / Desactivar Producto ───────────────────────────
    const productsContainer = document.getElementById('productsContainer');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        customClass: { popup: 'swal-axstore' },
    });

    async function toggleProductoStatus(id, nombre, estadoActual) {
        const nuevoEstado = (estadoActual === 1) ? 0 : 1;
        const actionText = nuevoEstado === 1 ? 'activar' : 'desactivar';
        const actionIcon = nuevoEstado === 1 ? 'fa-check' : 'fa-ban';
        const btnColor   = nuevoEstado === 1 ? '#10b981' : '#f43f5e';
        const htmlText   = nuevoEstado === 1
            ? `El producto <strong class="text-slate-800">${nombre}</strong> volverá a estar disponible y visible en el catálogo.`
            : `El producto <strong class="text-slate-800">${nombre}</strong> quedará inactivo y se ocultará del catálogo.`;

        const result = await Swal.fire({
            customClass: { popup: 'swal-axstore' },
            title: `¿${actionText.charAt(0).toUpperCase() + actionText.slice(1)} producto?`,
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
            const res = await fetch(`/productos/${id}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();

            if (res.ok && data.success) {
                Toast.fire({ icon: 'success', title: data.message });

                const nuevoEst = parseInt(data.nuevo_estado);

                // Actualizar fila en tabla
                const tr = document.querySelector(`tr.producto-row[data-id="${id}"]`);
                if (tr) {
                    tr.dataset.estado = nuevoEst;
                    const btn = tr.querySelector('.btn-toggle-estado-producto');
                    if (btn) {
                        btn.dataset.estado = nuevoEst;
                        btn.title = `Clic para ${nuevoEst === 1 ? 'desactivar' : 'activar'} producto`;
                        if (nuevoEst === 1) {
                            btn.className = 'btn-toggle-estado-producto inline-flex items-center gap-1.5 text-xs font-bold rounded-full px-2.5 py-1 transition-all hover:scale-105 active:scale-95 cursor-pointer border text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border-emerald-200';
                            btn.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> <span class="estado-label">Activo</span>`;
                        } else {
                            btn.className = 'btn-toggle-estado-producto inline-flex items-center gap-1.5 text-xs font-bold rounded-full px-2.5 py-1 transition-all hover:scale-105 active:scale-95 cursor-pointer border text-red-600 bg-red-50 hover:bg-red-100 border-red-200';
                            btn.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> <span class="estado-label">Inactivo</span>`;
                        }
                    }
                }

                // Actualizar tarjeta en grid
                const card = document.querySelector(`.producto-card[data-id="${id}"]`);
                if (card) {
                    card.dataset.estado = nuevoEst;
                    card.style.borderTopColor = nuevoEst === 1 ? '#10b981' : '#ef4444';
                    const imgContainer = card.querySelector('.w-16.h-16');
                    const titleEl = card.querySelector('h3');
                    const btn = card.querySelector('.btn-toggle-estado-producto');

                    if (nuevoEst === 1) {
                        card.classList.remove('opacity-70');
                        imgContainer?.classList.remove('grayscale');
                        titleEl?.classList.remove('text-slate-400');
                        if (btn) {
                            btn.dataset.estado = 1;
                            btn.title = 'Clic para desactivar producto';
                            btn.className = 'btn-toggle-estado-producto inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-100 cursor-pointer transition-transform hover:scale-105 active:scale-95';
                            btn.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> <span class="estado-label">Activo</span>`;
                        }
                    } else {
                        card.classList.add('opacity-70');
                        imgContainer?.classList.add('grayscale');
                        titleEl?.classList.add('text-slate-400');
                        if (btn) {
                            btn.dataset.estado = 0;
                            btn.title = 'Clic para activar producto';
                            btn.className = 'btn-toggle-estado-producto inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 hover:bg-red-100 border border-red-100 cursor-pointer transition-transform hover:scale-105 active:scale-95';
                            btn.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> <span class="estado-label">Inactivo</span>`;
                        }
                    }
                }

                // Re-filtrar si hay un filtro de estado aplicado
                applyFilters();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo cambiar el estado del producto.',
                    customClass: { popup: 'swal-axstore' }
                });
            }
        } catch {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de red al intentar cambiar el estado.',
                customClass: { popup: 'swal-axstore' }
            });
        }
    }

    productsContainer?.addEventListener('click', async (e) => {
        const btnToggle = e.target.closest('.btn-toggle-estado-producto');
        if (btnToggle) {
            const id = btnToggle.dataset.id;
            const nombre = btnToggle.dataset.nombre || 'este producto';
            const estadoActual = parseInt(btnToggle.dataset.estado ?? '1');
            await toggleProductoStatus(id, nombre, estadoActual);
        }
    });

    // Custom Selects para Categoría y Estado
    function initProductoCustomSelects() {
        const containers = document.querySelectorAll('[data-custom-producto-select]');
        
        containers.forEach(container => {
            const toggle = container.querySelector('[data-toggle]');
            const menu = container.querySelector('[data-menu]');
            const arrow = container.querySelector('[data-arrow]');
            const labelSpan = container.querySelector('[data-label]');
            const iconEl = container.querySelector('[data-icon]');
            const hiddenInput = container.querySelector('input[type="hidden"]');
            const options = container.querySelectorAll('[data-option]');

            function openMenu() {
                document.querySelectorAll('[data-custom-producto-select] [data-menu]').forEach(m => {
                    if (m !== menu) {
                        m.classList.add('hidden');
                        const p = m.closest('[data-custom-producto-select]');
                        p?.querySelector('[data-arrow]')?.classList.remove('rotate-180');
                        p?.querySelector('[data-toggle]')?.setAttribute('aria-expanded', 'false');
                    }
                });
                menu.classList.remove('hidden');
                arrow?.classList.add('rotate-180');
                toggle.setAttribute('aria-expanded', 'true');
            }

            function closeMenu() {
                menu.classList.add('hidden');
                arrow?.classList.remove('rotate-180');
                toggle.setAttribute('aria-expanded', 'false');
            }

            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                if (menu.classList.contains('hidden')) {
                    openMenu();
                } else {
                    closeMenu();
                }
            });

            options.forEach(opt => {
                opt.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const val = opt.dataset.value;
                    const lbl = opt.dataset.label;
                    const icn = opt.dataset.icon;

                    hiddenInput.value = val;
                    if (labelSpan) labelSpan.textContent = lbl;
                    if (iconEl && icn) iconEl.className = icn;

                    options.forEach(o => {
                        const isSelected = o === opt;
                        o.classList.toggle('bg-blue-50', isSelected);
                        o.classList.toggle('text-blue-700', isSelected);
                        o.classList.toggle('text-slate-700', !isSelected);
                        const check = o.querySelector('[data-check]');
                        if (check) check.classList.toggle('hidden', !isSelected);
                    });

                    closeMenu();
                    applyFilters();
                });
            });
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('[data-custom-producto-select]')) {
                document.querySelectorAll('[data-custom-producto-select]').forEach(container => {
                    container.querySelector('[data-menu]')?.classList.add('hidden');
                    container.querySelector('[data-arrow]')?.classList.remove('rotate-180');
                    container.querySelector('[data-toggle]')?.setAttribute('aria-expanded', 'false');
                });
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('[data-custom-producto-select]').forEach(container => {
                    container.querySelector('[data-menu]')?.classList.add('hidden');
                    container.querySelector('[data-arrow]')?.classList.remove('rotate-180');
                    container.querySelector('[data-toggle]')?.setAttribute('aria-expanded', 'false');
                });
            }
        });

        window.resetProductoCustomSelects = function() {
            containers.forEach(container => {
                const hiddenInput = container.querySelector('input[type="hidden"]');
                const defaultOption = container.querySelector('[data-option][data-value="todos"]');
                if (defaultOption && hiddenInput) {
                    hiddenInput.value = 'todos';
                    const labelSpan = container.querySelector('[data-label]');
                    const iconEl = container.querySelector('[data-icon]');
                    if (labelSpan) labelSpan.textContent = defaultOption.dataset.label;
                    if (iconEl && defaultOption.dataset.icon) iconEl.className = defaultOption.dataset.icon;

                    const options = container.querySelectorAll('[data-option]');
                    options.forEach(o => {
                        const isDefault = o === defaultOption;
                        o.classList.toggle('bg-blue-50', isDefault);
                        o.classList.toggle('text-blue-700', isDefault);
                        o.classList.toggle('text-slate-700', !isDefault);
                        const check = o.querySelector('[data-check]');
                        if (check) check.classList.toggle('hidden', !isDefault);
                    });
                }
                container.querySelector('[data-menu]')?.classList.add('hidden');
                container.querySelector('[data-arrow]')?.classList.remove('rotate-180');
                container.querySelector('[data-toggle]')?.setAttribute('aria-expanded', 'false');
            });
        };
    }

    document.addEventListener('DOMContentLoaded', initProductoCustomSelects);
</script>

<style>
    .swal-axstore {
        border-radius: 1rem !important;
        padding: 2rem !important;
        font-family: inherit !important;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.18) !important;
    }
    .swal-title-custom {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-top: 0.5rem !important;
    }
    .swal-html-custom {
        font-size: 0.9rem !important;
        color: #64748b !important;
        line-height: 1.6 !important;
    }
    .swal-btn-danger {
        border-radius: 0.6rem !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        padding: 0.55rem 1.25rem !important;
        box-shadow: none !important;
    }
    .swal-btn-cancel {
        border-radius: 0.6rem !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        padding: 0.55rem 1.25rem !important;
        box-shadow: none !important;
    }
</style>

</body>
</html>
