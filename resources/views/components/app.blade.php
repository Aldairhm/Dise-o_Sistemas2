@props(['title' => 'AXStore ERP'])

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    <!-- Dark mode init -->
    <script>
        (function() {
            var t = localStorage.getItem('ax_theme') || 'system';
            if (t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">

    <!-- 1. Recuperamos la tipografía original (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/home.css', 'resources/css/app.css', 'resources/js/app.js'])
</head>

<!-- 2. Aplicamos el mismo color de fondo (bg-gray-50) y forzamos la fuente Inter -->

<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col" style="font-family: 'Inter', sans-serif;">

    {{-- Banner de Notificación Permanente de MODO AUDITORÍA (Secure Impersonation) --}}
    @if(session()->has('auditoria_token'))
        <div class="bg-purple-900 text-white px-4 py-2.5 shadow-md sticky top-0 z-[60] border-b border-purple-800 flex flex-wrap items-center justify-between text-xs font-semibold gap-3">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></span>
                <span class="bg-purple-800 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider text-purple-200">
                    Modo Auditoría · Cuenta Supervisada
                </span>
                <span>
                    Estás auditando la cuenta de: <strong class="underline decoration-purple-400">{{ Auth::user()->nombre_real }}</strong> ({{ Auth::user()->username }})
                </span>
                @if(session('auditoria_solo_lectura', true))
                    <span class="bg-amber-500/30 text-amber-300 border border-amber-400/40 px-2 py-0.5 rounded text-[10px] font-black">
                        SOLO LECTURA
                    </span>
                @endif
            </div>

            <form method="POST" action="{{ route('superadmin.auditorias.finalizar') }}" class="inline">
                @csrf
                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition-colors shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-right-from-bracket"></i>
                    <span>Finalizar Auditoría y Restaurar</span>
                </button>
            </form>
        </div>
    @endif

    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 transition-all duration-300">
        @if(Auth::check() && Auth::user()->rol === 'vendedor')
            @include('componentsHome.header-vendedor')
        @else
            @include('componentsHome.header')
        @endif
    </header>

    <!-- SWEETALERT2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: 'Acción Denegada',
                    text: "{{ session('error') }}"
                });
            @endif

            @if($errors->any())
                Toast.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    html: `{!! implode('<br>', $errors->all()) !!}`
                });
            @endif
        });
    </script>

    <main class="flex-grow p-6 w-full max-w-7xl mx-auto mt-4">
        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-gray-200 pt-16 pb-8 mt-auto">
        @include('componentsHome.footer')
    </footer>

</body>

</html>


