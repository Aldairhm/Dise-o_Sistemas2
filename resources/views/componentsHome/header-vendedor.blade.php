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

                 <!-- Desktop Menu -->
                  <nav class="hidden lg:flex items-center gap-8">
                    <a href="/home" class="text-sm font-semibold text-blue-600 flex items-center gap-2"><i class="fas fa-home"></i> Inicio</a>
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

                    <button class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold h-4 w-4 rounded-full flex items-center justify-center">0</span>
                    </button>
                    <!-- Usuario Dropdown con Cerrar Sesión -->
                    @auth
                    <div class="user-dropdown">
                        <button type="button" id="userMenuBtn" class="p-2 text-blue-600 hover:text-blue-700 transition-transform hover:scale-105 hidden sm:flex items-center gap-2 rounded-full focus:outline-none" title="Mi Cuenta">
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
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50 rounded-lg flex items-center gap-2 transition-colors cursor-pointer">
                                        <i class="fas fa-arrow-right-from-bracket text-red-500"></i>
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="p-2 text-gray-600 hover:text-blue-600 transition-colors hidden sm:block" title="Iniciar Sesión">
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
        @include('componentsHome.headerMovil-vendedor')