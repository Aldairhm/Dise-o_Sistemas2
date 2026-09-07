        <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-100 bg-white">
            <nav class="flex flex-col px-4 pt-2 pb-4 space-y-2">
                <a href="/home" class="text-sm font-semibold text-blue-600 flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-home w-5 text-center"></i> Inicio</a>
                 @auth
                <div class="pt-2 border-t border-gray-100">
                    <div class="flex items-center gap-2.5 p-2 bg-gray-50 rounded-lg mb-1.5">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr(Auth::user()->nombre_real ?? Auth::user()->username ?? 'U', 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-bold text-gray-800 truncate">{{ Auth::user()->nombre_real ?? Auth::user()->username }}</p>
                            <span class="text-[10px] text-blue-600 font-semibold uppercase">{{ Auth::user()->rol ?? 'Usuario' }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="w-full text-left text-sm font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2 p-2 rounded-lg transition-colors cursor-pointer">
                            <i class="fas fa-arrow-right-from-bracket w-5 text-center text-red-500"></i>
                            <span>Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
                @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-arrow-right-to-bracket w-5 text-center"></i> Iniciar Sesión</a>
                @endauth
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