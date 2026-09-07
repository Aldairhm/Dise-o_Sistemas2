<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Categorías — AXStore</title>
    <!-- Dark mode init -->
    <script>
        (function() {
            var t = localStorage.getItem('ax_theme') || 'system';
            if (t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SortableJS para Drag & Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/categorias.css', 'resources/js/categorias.js', 'resources/css/home.css', 'resources/css/app.css', 'resources/js/app.js'])
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
        window.APP_ROUTES = {
            index:        '{{ route('categorias.index') }}',
            store:        '{{ route('categorias.store') }}',
            update:       '{{ url('categorias') }}',
            toggleStatus: '{{ url('categorias') }}',
            reorder:      '{{ route('categorias.reorder') }}',
            export:       '{{ route('categorias.export') }}',
        };
    </script>
</body>
</html>
