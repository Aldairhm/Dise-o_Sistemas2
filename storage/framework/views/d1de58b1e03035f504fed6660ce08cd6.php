        <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-100 bg-white max-h-[80vh] overflow-y-auto">
            <nav class="flex flex-col px-4 pt-3 pb-6 space-y-4">
                <!-- Inicio -->
                <a href="/home" class="text-sm font-bold flex items-center gap-2.5 p-2.5 rounded-xl transition-colors <?php echo e(request()->routeIs('home*') || request()->is('/') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50'); ?>">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-home text-xs"></i>
                    </div>
                    <span>Inicio</span>
                </a>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::check() && Auth::user()->isSuperAdmin()): ?>
                <!-- Sección Exclusiva Super Administrador (Móvil) -->
                <div class="space-y-1">
                    <div class="px-2 pb-1 text-[11px] font-black uppercase tracking-wider text-indigo-600 flex items-center gap-1.5">
                        <i class="fas fa-shield-halved text-[10px]"></i>
                        <span>Gobernanza Super Admin</span>
                    </div>
                    <div class="grid grid-cols-1 gap-1 pl-1">
                        <a href="<?php echo e(route('superadmin.dashboard')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('superadmin.dashboard') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-gauge-high text-[11px]"></i>
                            </div>
                            <span>Panel de Control</span>
                        </a>
                        <a href="<?php echo e(route('superadmin.usuarios')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('superadmin.usuarios') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-users-gear text-[11px]"></i>
                            </div>
                            <span>Cuentas & Auditoría</span>
                        </a>
                        <a href="<?php echo e(route('superadmin.tickets')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('superadmin.tickets') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-headset text-[11px]"></i>
                            </div>
                            <span>Centro de Dudas</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::check() && (Auth::user()->rol === 'admin' || Auth::user()->isSuperAdmin())): ?>
                <!-- Sección Ventas -->
                <div class="space-y-1">
                    <div class="px-2 pb-1 text-[11px] font-black uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                        <i class="fas fa-cash-register text-[10px] text-blue-500"></i>
                        <span>Gestión de Ventas</span>
                    </div>
                    <div class="grid grid-cols-1 gap-1 pl-1">
                        <!-- Mis Ventas -->
                        <a href="<?php echo e(route('ventas.mis-ventas')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('ventas.mis-ventas') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-boxes-packing text-[11px]"></i>
                            </div>
                            <span>Mis Ventas</span>
                        </a>

                        <!-- Historial General -->
                        <a href="<?php echo e(route('ventas.index')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('ventas.index') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-chart-line text-[11px]"></i>
                            </div>
                            <span>Historial General</span>
                        </a>

                        <!-- Control de Envíos -->
                        <a href="<?php echo e(route('ventas.pedidos')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('ventas.pedidos') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-truck-fast text-[11px]"></i>
                            </div>
                            <span>Control de Envíos</span>
                        </a>

                        <!-- Terminal POS -->
                        <a href="<?php echo e(route('ventas.create')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('ventas.create') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-cash-register text-[11px]"></i>
                            </div>
                            <span>Terminal POS / Carrito</span>
                        </a>

                        <!-- Devoluciones -->
                        <a href="<?php echo e(route('ventas.devoluciones')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('ventas.devoluciones') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-rotate-left text-[11px]"></i>
                            </div>
                            <span>Devoluciones de Clientes</span>
                        </a>
                    </div>
                </div>

                <!-- Sección Inventario -->
                <div class="space-y-1">
                    <div class="px-2 pb-1 text-[11px] font-black uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                        <i class="fas fa-boxes-stacked text-[10px] text-emerald-500"></i>
                        <span>Inventario y Stock</span>
                    </div>
                    <div class="grid grid-cols-1 gap-1 pl-1">
                        <!-- Productos -->
                        <a href="<?php echo e(route('productos.index')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('productos.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-shopping-bag text-[11px]"></i>
                            </div>
                            <span>Productos</span>
                        </a>

                        <!-- Categorías -->
                        <a href="<?php echo e(route('categorias.index')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('categorias.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-folder-open text-[11px]"></i>
                            </div>
                            <span>Categorías</span>
                        </a>

                        <!-- Compras -->
                        <a href="<?php echo e(route('compras.create')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('compras.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-cart-plus text-[11px]"></i>
                            </div>
                            <span>Compras</span>
                        </a>

                        <!-- Proveedores -->
                        <a href="<?php echo e(url('/proveedores')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->is('proveedores*') || request()->routeIs('proveedores.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-truck text-[11px]"></i>
                            </div>
                            <span>Proveedores</span>
                        </a>
                    </div>
                </div>

                <!-- Sección Administración -->
                <div class="space-y-1">
                    <div class="px-2 pb-1 text-[11px] font-black uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                        <i class="fas fa-shield-halved text-[10px] text-purple-500"></i>
                        <span>Administración</span>
                    </div>
                    <div class="grid grid-cols-1 gap-1 pl-1">
                        <!-- Dashboard Administrativo -->
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-chart-pie text-[11px]"></i>
                            </div>
                            <span>Dashboard General</span>
                        </a>

                        <!-- Reportes de Movimientos -->
                        <a href="<?php echo e(route('admin.reportes.movimientos')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('admin.reportes.movimientos') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-file-invoice-dollar text-[11px]"></i>
                            </div>
                            <span>Reporte de Movimientos</span>
                        </a>

                        <!-- Usuarios -->
                        <a href="<?php echo e(route('usuarios.index')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('usuarios.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-users-gear text-[11px]"></i>
                            </div>
                            <span>Usuarios</span>
                        </a>

                        <!-- Comisiones -->
                        <a href="<?php echo e(route('comisiones.porVendedor')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('comisiones.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-hand-holding-dollar text-[11px]"></i>
                            </div>
                            <span>Comisiones</span>
                        </a>

                        <!-- Bitácora -->
                        <a href="<?php echo e(route('bitacora.index')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('bitacora.*') ? 'bg-teal-50 text-teal-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-teal-100 text-teal-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-clipboard-list text-[11px]"></i>
                            </div>
                            <span>Bitácora del Sistema</span>
                        </a>

                        <!-- Respaldos BD -->
                        <a href="<?php echo e(route('backups.index')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('backups.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <div class="w-6 h-6 rounded bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-database text-[11px]"></i>
                            </div>
                            <span>Respaldos de Base de Datos</span>
                        </a>
                    </div>
                </div>
                <?php elseif(Auth::check() && Auth::user()->rol === 'vendedor'): ?>
                <a href="<?php echo e(route('ventas.mis-ventas')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg text-gray-700 hover:bg-gray-50 <?php echo e(request()->routeIs('ventas.mis-ventas') ? 'bg-blue-50 text-blue-700 font-bold' : ''); ?>">
                    <div class="w-6 h-6 rounded bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-boxes-packing text-[11px]"></i>
                    </div>
                    <span>Mis Ventas</span>
                </a>
                <a href="<?php echo e(route('comisiones.index')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg text-gray-700 hover:bg-gray-50">
                    <div class="w-6 h-6 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-hand-holding-dollar text-[11px]"></i>
                    </div>
                    <span>Mis Comisiones</span>
                </a>
                <?php else: ?>
                <a href="<?php echo e(route('productos.index')); ?>" class="text-xs font-semibold flex items-center gap-2.5 p-2 rounded-lg text-gray-700 hover:bg-gray-50">
                    <div class="w-6 h-6 rounded bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-shopping-bag text-[11px]"></i>
                    </div>
                    <span>Productos</span>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('ayuda.index')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:underline">
                            <i class="fas fa-circle-question" aria-hidden="true"></i> Ayuda
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </nav>
        </div>
            
<!-- Script Menu Móvil -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const icon = btn.querySelector('i');

            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
                if (menu.classList.contains('hidden')) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                } else {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                }
            });
        });
    </script><?php /**PATH C:\laragon\www\diseno\resources\views/componentsHome/headerMovil.blade.php ENDPATH**/ ?>