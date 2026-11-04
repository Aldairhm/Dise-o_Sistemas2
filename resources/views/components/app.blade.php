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

    {{-- Barra Sutil de MODO AUDITORÍA (Estilo nativo AXStore) --}}
    @if(session()->has('auditoria_token'))
        <div class="bg-slate-900 text-slate-200 border-b border-slate-800 text-xs py-2 px-4 shadow-sm relative z-[60]">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2.5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                        Modo Auditoría
                    </span>
                    <span class="text-slate-300 text-xs">
                        Supervisando cuenta de: <strong class="text-white font-bold">{{ Auth::user()->nombre_real }}</strong> <span class="text-slate-400 font-mono text-[11px]">({{ Auth::user()->username }})</span>
                    </span>
                    @if(session('auditoria_solo_lectura', true))
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-300 border border-amber-500/20">
                            Solo Lectura
                        </span>
                    @endif
                </div>

                <form method="POST" action="{{ route('superadmin.auditorias.finalizar') }}" class="shrink-0 w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto justify-center inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-800 hover:bg-rose-600 hover:text-white text-slate-300 border border-slate-700 hover:border-rose-600 font-bold text-xs transition-all shadow-sm cursor-pointer">
                        <i class="fas fa-arrow-right-from-bracket text-[11px]"></i>
                        <span>Salir de Auditoría</span>
                    </button>
                </form>
            </div>
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


