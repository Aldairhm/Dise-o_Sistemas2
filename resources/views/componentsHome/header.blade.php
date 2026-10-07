<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo --> 
                 <a href="/home" class="flex items-center gap-3 group">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="AXStore Logo" class="h-12 w-auto object-contain">
                    <div class="hidden sm:flex flex-col">
                        <span class="text-3xl font-black tracking-tighter text-gray-900 group-hover:text-blue-600 transition-colors">
                            AX<span class="text-blue-600">STORE</span>
                        </span>
                        <span class="text-[10px] uppercase tracking-widest text-gray-500 font-semibold mt-[-4px]">Tu tienda online</span>
                    </div>
                 </a> 

                 <!-- Desktop Menu Grouped -->
                 <nav class="hidden lg:flex items-center gap-7">
                    <!-- Inicio -->
                    <a href="/home" class="text-sm flex items-center gap-2 py-2 transition-colors {{ request()->routeIs('home*') || request()->is('/') ? 'text-blue-600 font-bold' : 'text-gray-600 hover:text-blue-600 font-medium' }}">
                        <i class="fas fa-home"></i>
                        <span>Inicio</span>
                    </a>

                    @if(Auth::check() && Auth::user()->rol === 'admin')
                    <!-- 1. Grupo Ventas -->
                    <div class="nav-dropdown">
                        <button type="button" class="text-sm flex items-center gap-1.5 py-2 transition-colors focus:outline-none cursor-pointer {{ request()->routeIs('ventas.*') || request()->routeIs('devoluciones.*') ? 'text-blue-600 font-bold' : 'text-gray-600 hover:text-blue-600 font-medium' }}">
                            <i class="fas fa-cash-register"></i>
                            <span>Ventas</span>
                            <i class="fas fa-chevron-down text-[10px] ml-0.5 nav-chevron opacity-70"></i>
                        </button>
                        <div class="nav-dropdown-menu">
                            <div class="px-3.5 py-2.5 border-b border-gray-100 flex items-center justify-between bg-slate-50/70">
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Gestión Comercial</span>
                                <span class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Ventas</span>
                            </div>
                            <div class="p-1.5 space-y-1">
                                <!-- Mis Ventas (sacado del perfil y puesto en la agrupación) -->
                                <a href="{{ route('ventas.mis-ventas') }}" class="flex items-center gap-3 p-2 rounded-xl transition-all duration-150 {{ request()->routeIs('ventas.mis-ventas') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50/60 hover:text-blue-600' }}">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-boxes-packing text-sm"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold leading-tight">Mis Ventas</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Historial y seguimiento de pedidos</span>
                                    </div>
                                </a>

                                <!-- Historial General / Dashboard -->
                                <a href="{{ route('ventas.index') }}" class="flex items-center gap-3 p-2 rounded-xl transition-all duration-150 {{ request()->routeIs('ventas.index') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50/60 hover:text-blue-600' }}">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-chart-line text-sm"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold leading-tight">Historial General</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Dashboard histórico y reportes</span>
                                    </div>
                                </a>

                                <!-- Control de Envíos y Estados -->
                                <a href="{{ route('ventas.pedidos') }}" class="flex items-center gap-3 p-2 rounded-xl transition-all duration-150 {{ request()->routeIs('ventas.pedidos') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50/60 hover:text-blue-600' }}">
                                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-truck-fast text-sm"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold leading-tight">Control de Envíos</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Estados de ruta y despacho</span>
                                    </div>
                                </a>

                                <!-- Terminal POS / Carrito -->
                                <a href="{{ route('ventas.create') }}" class="flex items-center gap-3 p-2 rounded-xl transition-all duration-150 {{ request()->routeIs('ventas.create') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50/60 hover:text-blue-600' }}">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-cash-register text-sm"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold leading-tight">Terminal POS / Carrito</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Registrar nueva orden o venta</span>
                                    </div>
                                </a>

                                <!-- Devoluciones y Cambios -->
                                <a href="{{ route('ventas.devoluciones') }}" class="flex items-center gap-3 p-2 rounded-xl transition-all duration-150 {{ request()->routeIs('ventas.devoluciones') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50/60 hover:text-blue-600' }}">
                                    <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-rotate-left text-sm"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold leading-tight">Devoluciones de Clientes</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Historial de garantías y retornos</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Grupo Inventario -->
                    <div class="nav-dropdown">
                        <button type="button" class="text-sm flex items-center gap-1.5 py-2 transition-colors focus:outline-none cursor-pointer {{ request()->routeIs('productos.*') || request()->routeIs('categorias.*') || request()->routeIs('compras.*') || request()->is('proveedores*') || request()->routeIs('proveedores.*') ? 'text-blue-600 font-bold' : 'text-gray-600 hover:text-blue-600 font-medium' }}">
                            <i class="fas fa-boxes-stacked"></i>
                            <span>Inventario</span>
                            <i class="fas fa-chevron-down text-[10px] ml-0.5 nav-chevron opacity-70"></i>
                        </button>
                        <div class="nav-dropdown-menu">
                            <div class="px-3.5 py-2.5 border-b border-gray-100 flex items-center justify-between bg-slate-50/70">
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Catálogo & Stock</span>
                                <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Almacén</span>
                            </div>
                            <div class="p-1.5 space-y-1">
                                <!-- Productos -->
                                <a href="{{ route('productos.index') }}" class="flex items-center gap-3 p-2 rounded-xl transition-all duration-150 {{ request()->routeIs('productos.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50/60 hover:text-blue-600' }}">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-shopping-bag text-sm"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold leading-tight">Productos</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Catálogo, variantes y stock</span>
                                    </div>
                                </a>

                                <!-- Categorías -->
                                <a href="{{ route('categorias.index') }}" class="flex items-center gap-3 p-2 rounded-xl transition-all duration-150 {{ request()->routeIs('categorias.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50/60 hover:text-blue-600' }}">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-folder-open text-sm"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold leading-tight">Categorías</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Organización y clasificación</span>
                                    </div>
                                </a>

                                <!-- Compras -->
                                <a href="{{ route('compras.create') }}" class="flex items-center gap-3 p-2 rounded-xl transition-all duration-150 {{ request()->routeIs('compras.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50/60 hover:text-blue-600' }}">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-cart-plus text-sm"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold leading-tight">Compras</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Abastecimiento a proveedores</span>
                                    </div>
                                </a>

                                <!-- Proveedores -->
                                <a href="{{ url('/proveedores') }}" class="flex items-center gap-3 p-2 rounded-xl transition-all duration-150 {{ request()->is('proveedores*') || request()->routeIs('proveedores.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50/60 hover:text-blue-600' }}">
                                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-truck text-sm"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold leading-tight">Proveedores</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Directorio y catálogos</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Grupo Administración -->
                    <div class="nav-dropdown">
                        <button type="button" class="text-sm flex items-center gap-1.5 py-2 transition-colors focus:outline-none cursor-pointer {{ request()->routeIs('usuarios.*') || request()->routeIs('comisiones.*') ? 'text-blue-600 font-bold' : 'text-gray-600 hover:text-blue-600 font-medium' }}">
                            <i class="fas fa-shield-halved"></i>
                            <span>Administración</span>
                            <i class="fas fa-chevron-down text-[10px] ml-0.5 nav-chevron opacity-70"></i>
                        </button>
                        <div class="nav-dropdown-menu">
                            <div class="px-3.5 py-2.5 border-b border-gray-100 flex items-center justify-between bg-slate-50/70">
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Control & Personal</span>
                                <span class="text-[10px] font-bold bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">Admin</span>
                            </div>
                            <div class="p-1.5 space-y-1">
                                <!-- Usuarios -->
                                <a href="{{ route('usuarios.index') }}" class="flex items-center gap-3 p-2 rounded-xl transition-all duration-150 {{ request()->routeIs('usuarios.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50/60 hover:text-blue-600' }}">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-users-gear text-sm"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold leading-tight">Usuarios</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Roles y control de cuentas</span>
                                    </div>
                                </a>

                                <!-- Comisiones -->
                                <a href="{{ route('comisiones.porVendedor') }}" class="flex items-center gap-3 p-2 rounded-xl transition-all duration-150 {{ request()->routeIs('comisiones.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-700 hover:bg-indigo-50/60 hover:text-indigo-600' }}">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-hand-holding-dollar text-sm"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold leading-tight">Comisiones</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Cálculo y liquidaciones</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @elseif(Auth::check() && Auth::user()->rol === 'vendedor')
                    <a href="{{ route('ventas.mis-ventas') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors flex items-center gap-2 {{ request()->routeIs('ventas.mis-ventas') ? 'text-blue-600 font-bold' : '' }}">
                        <i class="fas fa-boxes-packing"></i>
                        <span>Mis Ventas</span>
                    </a>
                    <a href="{{ route('comisiones.index') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors flex items-center gap-2 {{ request()->routeIs('comisiones.*') ? 'text-indigo-600 font-bold' : '' }}">
                        <i class="fas fa-hand-holding-dollar"></i>
                        <span>Mis Comisiones</span>
                    </a>
                    @else
                    <a href="{{ route('productos.index') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors flex items-center gap-2">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Productos</span>
                    </a>
                    @endif
                </nav>

                 <!-- Icons -->
                <div class="flex items-center gap-4">
                    <!-- WhatsApp Dropdown -->
                    <div class="wa-dropdown">
                        <a href="#" class="wa-trigger" title="Grupos de WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <div class="wa-dropdown-menu">
                            <div class="wa-dropdown-header">
                                <i class="fab fa-whatsapp"></i> Grupos WhatsApp
                            </div>
                            <div class="wa-dropdown-list">
                                <a href="#" class="wa-dropdown-item hover:bg-green-300 transition-colors">
                                    <div class="wa-icon-box wa-bg-green"><i class="fas fa-shopping-cart"></i></div>
                                    <div class="wa-item-content">
                                        <span class="wa-item-title">Ventas Online</span>
                                        <span class="wa-item-subtitle">Realiza tus pedidos</span>
                                    </div>
                                </a>
                                <a href="#" class="wa-dropdown-item hover:bg-blue-300 transition-colors">
                                    <div class="wa-icon-box wa-bg-blue"><i class="fas fa-truck"></i></div>
                                    <div class="wa-item-content">
                                        <span class="wa-item-title">Entregas San Salvador</span>
                                        <span class="wa-item-subtitle">Seguimiento SS</span>
                                    </div>
                                </a>
                                <a href="#" class="wa-dropdown-item hover:bg-purple-300 transition-colors">
                                    <div class="wa-icon-box wa-bg-purple"><i class="fas fa-truck-fast"></i></div>
                                    <div class="wa-item-content">
                                        <span class="wa-item-title">Entregas Departamentales</span>
                                        <span class="wa-item-subtitle">Envíos a todo el país</span>
                                    </div>
                                </a>
                                <a href="#" class="wa-dropdown-item hover:bg-yellow-300 transition-colors">
                                    <div class="wa-icon-box wa-bg-yellow"><i class="fas fa-question"></i></div>
                                    <div class="wa-item-content">
                                        <span class="wa-item-title">Consultas</span>
                                        <span class="wa-item-subtitle">Resuelve tus dudas</span>
                                    </div>
                                </a>
                                <a href="#" class="wa-dropdown-item hover:bg-pink-300 transition-colors">
                                    <div class="wa-icon-box wa-bg-pink"><i class="fas fa-camera"></i></div>
                                    <div class="wa-item-content">
                                        <span class="wa-item-title">Fotos de Paquetes</span>
                                        <span class="wa-item-subtitle">Evidencias de entrega</span>
                                    </div>
                                </a>
                                <a href="#" class="wa-dropdown-item hover:bg-orange-300 transition-colors">
                                    <div class="wa-icon-box wa-bg-orange"><i class="fas fa-undo"></i></div>
                                    <div class="wa-item-content">
                                        <span class="wa-item-title">Devoluciones</span>
                                        <span class="wa-item-subtitle">Gestión de retornos</span>
                                    </div>
                                </a>
                                <a href="#" class="wa-dropdown-item hover:bg-red-300 transition-colors">
                                    <div class="wa-icon-box wa-bg-red"><i class="fas fa-exclamation-triangle"></i></div>
                                    <div class="wa-item-content">
                                        <span class="wa-item-title">Producto Agotado</span>
                                        <span class="wa-item-subtitle">Reportar sin stock</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bolsa de compras estilo Apple -->
                    @include('componentsHome.cartDropdown')
                    <!-- Usuario Dropdown con Cerrar Sesión -->
                    @auth
                    <div class="user-dropdown">
                        <button type="button" id="userMenuBtn" class="p-2 text-blue-600 hover:text-blue-700 transition-transform hover:scale-105 flex items-center gap-2 rounded-full focus:outline-none" title="Mi Cuenta">
                            <i class="fas fa-user-circle text-2xl text-blue-600"></i>
                        </button>
                        
                        <div class="user-dropdown-menu">
                            <div class="p-3 border-b border-gray-100 flex items-center gap-3 bg-gray-50/80">
                                <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr(Auth::user()->nombre_real ?? Auth::user()->username ?? 'U', 0, 1)) }}
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-xs font-bold text-gray-800 truncate" title="{{ Auth::user()->nombre_real ?? Auth::user()->username }}">
                                        {{ Auth::user()->nombre_real ?? Auth::user()->username }}
                                    </p>
                                    <span class="inline-block text-[10px] font-semibold text-blue-600 uppercase tracking-wider">
                                        Rol: {{ Auth::user()->rol ?? 'Usuario' }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-1.5 space-y-1">
                                <a href="{{ route('perfil.show') }}" class="w-full text-left px-3 py-2 text-xs font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg flex items-center gap-2 transition-colors">
                                    <i class="fas fa-user-gear text-blue-500"></i>
                                    <span>Configurar Perfil</span>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="m-0" id="logoutForm">
                                    @csrf
                                    <button type="button" onclick="confirmLogout(this.closest('form'))" class="w-full text-left px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50 rounded-lg flex items-center gap-2 transition-colors cursor-pointer">
                                        <i class="fas fa-arrow-right-from-bracket text-red-500"></i>
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </form>
                                <script>
                                    function confirmLogout(form) {
                                        Swal.fire({
                                            title: 'Cerrando sesión...',
                                            html: 'Por favor, espera un momento.',
                                            timer: 1500,
                                            timerProgressBar: true,
                                            allowOutsideClick: false,
                                            customClass: { popup: 'swal-axstore' },
                                            didOpen: () => {
                                                Swal.showLoading();
                                            },
                                            willClose: () => {
                                                form.submit();
                                            }
                                        });
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="p-2 text-gray-600 hover:text-blue-600 transition-colors block" title="Iniciar Sesión">
                        <i class="fas fa-user-circle text-2xl"></i>
                    </a>
                    @endauth
                    <!-- Mobile menu button -->
                    <button id="mobile-menu-btn" class="lg:hidden p-2 text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menu Móvil -->
        @include('componentsHome.headerMovil')

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const navDropdowns = document.querySelectorAll('.nav-dropdown');
                navDropdowns.forEach(dd => {
                    const btn = dd.querySelector('button');
                    if (btn) {
                        btn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            navDropdowns.forEach(other => {
                                if (other !== dd) other.classList.remove('active');
                            });
                            dd.classList.toggle('active');
                        });
                    }
                });

                document.addEventListener('click', () => {
                    navDropdowns.forEach(dd => dd.classList.remove('active'));
                });
            });
        </script>