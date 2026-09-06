<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Usuarios — AXStore</title>
    <!-- Dark mode init -->
    <script>
        (function() {
            var t = localStorage.getItem('ax_theme') || 'system';
            if (t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/home.css', 'resources/css/usuarios.css', 'resources/css/app.css', 'resources/js/app.js', 'resources/js/usuarios.js'])
</head>
<body class="min-h-screen text-slate-800 antialiased font-['Plus_Jakarta_Sans',sans-serif] bg-slate-50 flex flex-col">

    <!-- ─── HEADER ─── -->
    <header class="bg-white/85 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 transition-all duration-300">
        @include('componentsHome.header')
    </header>

    <!-- ─── CONTENIDO PRINCIPAL ─── -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- ENCABEZADO DE SECCION Y BREADCRUMB -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1.5">
                    <i class="fas fa-users-gear"></i>
                    <span>Módulo de Control y Seguridad</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                    Gestión de Usuarios
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Administra las cuentas de acceso, roles y estados operativos del personal de AXStore.
                </p>
            </div>

            <!-- Boton -->
            <div class="flex items-center gap-3">
                <button type="button" 
                        onclick="openCreateUserModal()" 
                        class="btn-add-user inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-lg shadow-blue-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                        id="btnOpenAddModal">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-xs">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span>AGREGAR</span>
                </button>
            </div>
        </div>

        <!--  TARJETAS DE METRICAS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
            <!-- Total Usuarios -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-blue-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Usuarios</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-3" id="kpiTotal">{{ $stats['total'] }}</p>
                <span class="text-[11px] text-slate-400 font-medium mt-1 block">Registrados en el sistema</span>
            </div>

            <!-- Administradores -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-purple-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-purple-600 uppercase tracking-wider">Administradores</span>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-3" id="kpiAdmins">{{ $stats['admins'] }}</p>
                <span class="text-[11px] text-slate-400 font-medium mt-1 block">Control y privilegios totales</span>
            </div>

            <!-- Vendedores -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-cyan-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-cyan-600 uppercase tracking-wider">Vendedores</span>
                    <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-base">
                        <i class="fas fa-tags"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-3" id="kpiVendedores">{{ $stats['vendedores'] }}</p>
                <span class="text-[11px] text-slate-400 font-medium mt-1 block">Gestión de catálogo y ventas</span>
            </div>

            <!-- Activos -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-emerald-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Cuentas Activas</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-3" id="kpiActivos">{{ $stats['activos'] }}</p>
                <span class="text-[11px] text-emerald-600 font-bold mt-1 block">Con acceso habilitado</span>
            </div>
        </div>

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
                           placeholder="Buscar usuario por nombre o correo..." 
                           class="w-full pl-10 pr-10 py-2.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 focus:border-blue-600 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all"
                           oninput="onSearchInput()">
                    <button type="button" 
                            id="clearSearchBtn" 
                            onclick="clearSearch()" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 hidden cursor-pointer">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <!-- Filtros Dropdowns -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Filtro por Rol -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:inline">Rol:</span>
                        <select id="roleFilter" 
                                onchange="applyFilters()" 
                                class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none cursor-pointer">
                            <option value="todos">Todos los Roles</option>
                            <option value="admin">Administradores</option>
                            <option value="vendedor">Vendedores</option>
                        </select>
                    </div>

                    <!-- Filtro por Estado -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:inline">Estado:</span>
                        <select id="statusFilter" 
                                onchange="applyFilters()" 
                                class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none cursor-pointer">
                            <option value="todos">Todos los Estados</option>
                            <option value="1">Activos</option>
                            <option value="0">Inactivos</option>
                        </select>
                    </div>

                    <!-- Boton Restablecer -->
                    <button type="button" 
                            onclick="resetAllFilters()" 
                            class="p-2 text-slate-400 hover:text-blue-600 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer"
                            title="Restablecer filtros">
                        <i class="fas fa-rotate-right text-xs"></i>
                    </button>

                    <!-- Tabla / Tarjetas -->
                    <div class="flex items-center bg-slate-100 rounded-xl p-1 gap-1">
                        <button type="button" id="btnViewTable"
                                onclick="setView('table')"
                                class="usr-view-btn p-2 rounded-lg text-slate-400 hover:text-slate-700 transition-colors cursor-pointer"
                                title="Vista de tabla">
                            <i class="fas fa-table-list text-sm"></i>
                        </button>
                        <button type="button" id="btnViewCards"
                                onclick="setView('cards')"
                                class="usr-view-btn p-2 rounded-lg text-slate-400 hover:text-slate-700 transition-colors cursor-pointer"
                                title="Vista de tarjetas">
                            <i class="fas fa-grip text-sm"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- CONTENEDOR DE LA TABLA / CARDS -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden relative" id="tableContainer">
            <!-- Loader de busqueda AJAX -->
            <div id="tableLoading" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-20 hidden">
                <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-2xl shadow-xl border border-slate-100 text-blue-600 text-sm font-bold">
                    <i class="fas fa-spinner fa-spin text-lg"></i>
                    <span>Buscando usuarios...</span>
                </div>
            </div>

            <!-- Vista Tabla -->
            <div id="usersTableWrapper">
                @include('usuarios.partials.table', ['users' => $users])
            </div>

            <!-- Vista Tarjetas -->
            <div id="usersCardsWrapper" class="hidden">
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="usersCardsGrid">
                    @include('usuarios.partials.cards', ['users' => $users])
                </div>
            </div>
        </div>

    </main>

    <!-- ─── FOOTER ─── -->
    <footer class="bg-white border-t border-slate-200/80 mt-12">
        @include('componentsHome.footer')
    </footer>

    <!-- ─── MODAL DE USUARIOS ─── -->
    @include('usuarios.modal-usuario')

    
</body>
</html>
