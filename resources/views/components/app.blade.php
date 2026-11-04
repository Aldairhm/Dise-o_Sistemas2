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
        @auth
            @php
                $helpEntry = config('ayuda_contextual')[request()->route()?->getName()] ?? null;
                $helpText = is_array($helpEntry) ? ($helpEntry[auth()->user()->rol] ?? null) : $helpEntry;
                $helpGuide = is_array($helpEntry) ? ($helpEntry['guide'] ?? null) : null;
            @endphp
            @if ($helpText)
                <details class="mb-4 rounded-lg border border-blue-200 bg-white px-3 py-2 text-sm text-slate-800">
                    <summary class="cursor-pointer font-semibold text-blue-700">¿Necesitas ayuda?</summary>
                    <p class="mt-2">{{ $helpText }}
                        @if ($helpGuide)
                            <a href="{{ route('ayuda.index', ['tema' => $helpGuide]) }}" target="_blank" rel="noopener"
                               class="font-semibold text-blue-700 underline">Ver guía (nueva pestaña)</a>
                        @endif
                    </p>
                </details>
            @endif
        @endauth

        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-gray-200 pt-16 pb-8 mt-auto">
        @include('componentsHome.footer')
    </footer>

</body>

</html>

