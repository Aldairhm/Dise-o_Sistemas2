<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo --> 
                 <a href="/home" class="flex items-center gap-3 group">
                    <img src="<?php echo e(asset('assets/images/logo.png')); ?>" alt="AXStore Logo" class="h-12 w-auto object-contain">
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
                    <a href="<?php echo e(route('vendedor.dashboard')); ?>" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors flex items-center gap-2 <?php echo e(request()->routeIs('vendedor.dashboard') ? 'text-indigo-600 font-bold' : ''); ?>"><i class="fas fa-chart-line"></i> Mi Dashboard</a>
                    <a href="<?php echo e(route('ventas.mis-ventas')); ?>" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors flex items-center gap-2 <?php echo e(request()->routeIs('ventas.mis-ventas') ? 'text-blue-600 font-bold' : ''); ?>"><i class="fas fa-boxes-packing"></i> Mis Ventas</a>
                    <a href="<?php echo e(route('comisiones.index')); ?>" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors flex items-center gap-2 <?php echo e(request()->routeIs('comisiones.*') ? 'text-indigo-600 font-bold' : ''); ?>"><i class="fas fa-hand-holding-dollar"></i> Mis Comisiones</a>
                                      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('ayuda.index')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:underline">
                            <i class="fas fa-circle-question" aria-hidden="true"></i> Ayuda
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </nav>

                 <!-- Icons -->
                <div class="flex items-center gap-4">
                    <?php echo $__env->make('notifications.bell', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
                    <?php echo $__env->make('componentsHome.cartDropdown', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <!-- Usuario Dropdown con Cerrar Sesión -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <div class="user-dropdown">
                        <button type="button" id="userMenuBtn" class="p-2 text-blue-600 hover:text-blue-700 transition-transform hover:scale-105 flex items-center gap-2 rounded-full focus:outline-none" title="Mi Cuenta">
                            <i class="fas fa-user-circle text-2xl text-blue-600"></i>
                        </button>
                        
                        <div class="user-dropdown-menu">
                            <div class="p-3 border-b border-gray-100 flex items-center gap-3 bg-gray-50/80">
                                <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    <?php echo e(strtoupper(substr(Auth::user()->nombre_real ?? Auth::user()->username ?? 'U', 0, 1))); ?>

                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-xs font-bold text-gray-800 truncate" title="<?php echo e(Auth::user()->nombre_real ?? Auth::user()->username); ?>">
                                        <?php echo e(Auth::user()->nombre_real ?? Auth::user()->username); ?>

                                    </p>
                                    <span class="inline-block text-[10px] font-semibold text-blue-600 uppercase tracking-wider">
                                        Rol: <?php echo e(Auth::user()->rol ?? 'Usuario'); ?>

                                    </span>
                                </div>
                            </div>
                            <div class="p-1.5 space-y-1">
                                <a href="<?php echo e(route('perfil.show')); ?>" class="w-full text-left px-3 py-2 text-xs font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg flex items-center gap-2 transition-colors">
                                    <i class="fas fa-user-gear text-blue-500"></i>
                                    <span>Configurar Perfil</span>
                                </a>
                                <form method="POST" action="<?php echo e(route('logout')); ?>" class="m-0" id="logoutFormVendedor">
                                    <?php echo csrf_field(); ?>
                                    <button type="button" onclick="confirmLogout(this.closest('form'))" class="w-full text-left px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50 rounded-lg flex items-center gap-2 transition-colors cursor-pointer">
                                        <i class="fas fa-arrow-right-from-bracket text-red-500"></i>
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </form>
                                <script>
                                    if (typeof confirmLogout === 'undefined') {
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
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="p-2 text-gray-600 hover:text-blue-600 transition-colors block" title="Iniciar Sesión">
                        <i class="fas fa-user-circle text-2xl"></i>
                    </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <!-- Mobile menu button -->
                    <button id="mobile-menu-btn" class="lg:hidden p-2 text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menu Móvil -->
        <?php echo $__env->make('componentsHome.headerMovil-vendedor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/componentsHome/header-vendedor.blade.php ENDPATH**/ ?>