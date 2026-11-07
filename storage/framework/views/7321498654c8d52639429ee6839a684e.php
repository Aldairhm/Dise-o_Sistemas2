        <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-100 bg-white">
            <nav class="flex flex-col px-4 pt-2 pb-4 space-y-2">
                <a href="/home" class="text-sm font-semibold text-blue-600 flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-home w-5 text-center"></i> Inicio</a>
                <a href="<?php echo e(route('vendedor.dashboard')); ?>" class="text-sm font-medium text-gray-600 hover:text-indigo-600 flex items-center gap-2 p-2 rounded-lg hover:bg-indigo-50 transition-colors <?php echo e(request()->routeIs('vendedor.dashboard') ? 'text-indigo-600 font-bold bg-indigo-50' : ''); ?>"><i class="fas fa-chart-line w-5 text-center"></i> Mi Dashboard</a>
                <a href="<?php echo e(route('ventas.mis-ventas')); ?>" class="text-sm font-medium text-gray-600 hover:text-blue-600 flex items-center gap-2 p-2 rounded-lg hover:bg-blue-50 transition-colors <?php echo e(request()->routeIs('ventas.mis-ventas') ? 'text-blue-600 font-bold bg-blue-50' : ''); ?>"><i class="fas fa-boxes-packing w-5 text-center"></i> Mis Ventas</a>
                <a href="<?php echo e(route('comisiones.index')); ?>" class="text-sm font-medium text-gray-600 hover:text-indigo-600 flex items-center gap-2 p-2 rounded-lg hover:bg-indigo-50 transition-colors <?php echo e(request()->routeIs('comisiones.*') ? 'text-indigo-600 font-bold bg-indigo-50' : ''); ?>"><i class="fas fa-hand-holding-dollar w-5 text-center"></i> Mis Comisiones</a>
                <!-- Auth block removed from mobile menu because profile is now on the top navbar -->
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
    </script><?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/componentsHome/headerMovil-vendedor.blade.php ENDPATH**/ ?>