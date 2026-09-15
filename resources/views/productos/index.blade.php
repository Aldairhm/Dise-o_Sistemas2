<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Productos — AXStore</title>
    <script>
        (function() {
            var t = localStorage.getItem('ax_theme') || 'system';
            if (t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/home.css', 'resources/css/productos.css', 'resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-slate-800 antialiased font-['Plus_Jakarta_Sans',sans-serif] bg-slate-50 flex flex-col">

    <header class="bg-white/85 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 transition-all duration-300">
        @include('componentsHome.header')
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- ENCABEZADO DE SECCIÓN --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1.5">
                    <i class="fas fa-boxes-stacked"></i>
                    <span>Módulo de Inventario</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Gestión de Productos
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Consulta y administra el catálogo de productos de AXStore.
                </p>
            </div>

            @if(Auth::user()->rol === 'admin')
            <div>
                <a href="{{ route('productos.create') }}"
                   class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-lg shadow-blue-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-xs">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span>NUEVO PRODUCTO</span>
                </a>
            </div>
            @endif
        </div>

        {{-- FLASH SESSION (SweetAlert automático al volver de store/update) --}}
        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación exitosa!',
                    text: @json(session('success')),
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'swal-axstore' },
                });
            });
        </script>
        @endif

        {{-- TABLA DE PRODUCTOS --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80">
                            <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">#</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Nombre</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:table-cell">Categoría</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Variantes</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Estado</th>
                            @if(Auth::user()->rol === 'admin')
                            <th class="text-right px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($productos as $prod)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4 text-slate-400 font-mono text-xs">{{ $prod->id }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $prod->nombre }}</td>
                            <td class="px-6 py-4 hidden sm:table-cell text-slate-500 text-xs">{{ $prod->categoria->nombre ?? '—' }}</td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 bg-slate-100 rounded-full px-2.5 py-1">
                                    <i class="fas fa-layer-group text-xs"></i>
                                    {{ $prod->variantes_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($prod->estado == 1)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-2.5 py-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 bg-red-50 border border-red-200 rounded-full px-2.5 py-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactivo
                                    </span>
                                @endif
                            </td>
                            @if(Auth::user()->rol === 'admin')
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('productos.edit', $prod->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors">
                                    <i class="fas fa-pencil text-xs"></i> Editar / Variantes
                                </a>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ Auth::user()->rol === 'admin' ? 6 : 5 }}" class="text-center py-16 text-slate-400">
                                <i class="fas fa-boxes-stacked text-4xl mb-3 block opacity-30"></i>
                                <p class="font-semibold text-sm">No hay productos registrados.</p>
                                @if(Auth::user()->rol === 'admin')
                                <a href="{{ route('productos.create') }}" class="mt-3 inline-block text-sm font-bold text-blue-600 hover:underline">
                                    + Crear el primero
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200/80 mt-12">
        @include('componentsHome.footer')
    </footer>

</body>
</html>
