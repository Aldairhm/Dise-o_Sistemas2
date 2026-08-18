        <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-100 bg-white">
            <nav class="flex flex-col px-4 pt-2 pb-4 space-y-2">
                <a href="/home" class="text-sm font-semibold text-blue-600 flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-home w-5 text-center"></i> Inicio</a>
                <a href="#productos" class="text-sm font-medium text-gray-600 hover:text-blue-600 flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-shopping-bag w-5 text-center"></i> Productos</a>
                <a href="#entregas" class="text-sm font-medium text-gray-600 hover:text-blue-600 flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-truck w-5 text-center"></i> Entregas</a>
                <a href="#categorias" class="text-sm font-medium text-gray-600 hover:text-blue-600 flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-folder-open w-5 text-center"></i> Categorías</a>
                <a href="#" class="text-sm font-medium text-gray-600 hover:text-blue-600 flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors sm:hidden"><i class="fas fa-user-circle w-5 text-center"></i> Mi Cuenta</a>
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