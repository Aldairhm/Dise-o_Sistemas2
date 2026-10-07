        <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-100 bg-white">
            <nav class="flex flex-col px-4 pt-2 pb-4 space-y-2">
                <a href="/home" class="text-sm font-semibold text-blue-600 flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-home w-5 text-center"></i> Inicio</a>
                <a href="{{ route('ventas.mis-ventas') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 flex items-center gap-2 p-2 rounded-lg hover:bg-blue-50 transition-colors {{ request()->routeIs('ventas.mis-ventas') ? 'text-blue-600 font-bold bg-blue-50' : '' }}"><i class="fas fa-boxes-packing w-5 text-center"></i> Mis Ventas</a>
                <a href="{{ route('comisiones.index') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 flex items-center gap-2 p-2 rounded-lg hover:bg-indigo-50 transition-colors {{ request()->routeIs('comisiones.*') ? 'text-indigo-600 font-bold bg-indigo-50' : '' }}"><i class="fas fa-hand-holding-dollar w-5 text-center"></i> Mis Comisiones</a>
                <!-- Auth block removed from mobile menu because profile is now on the top navbar -->
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
    </script>