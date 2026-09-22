<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Atributos — AXStore</title>
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
    @vite(['resources/css/home.css', 'resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-slate-800 antialiased font-['Plus_Jakarta_Sans',sans-serif] bg-slate-50 flex flex-col">

    <header class="bg-white/85 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 transition-all duration-300">
        @include('componentsHome.header')
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- ENCABEZADO -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1.5">
                    <i class="fas fa-tags"></i>
                    <span>Módulo de Catálogo</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Gestión de Atributos
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Administra los atributos dinámicos (Talla, Color, etc.) para los productos.
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <!-- Botón agregar -->
                <button type="button" onclick="openModal('create')"
                        class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-lg shadow-blue-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-xs">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span>NUEVO ATRIBUTO</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                <ul class="list-disc list-inside text-sm font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- TABLA -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200/80">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nombre del Atributo</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider w-24">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($atributos as $atributo)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-500">
                                #{{ $atributo->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">
                                {{ $atributo->nombre }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="openModal('edit', {{ $atributo->id }}, '{{ $atributo->nombre }}')" class="p-2 w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors flex items-center justify-center cursor-pointer" title="Editar">
                                        <i class="fas fa-pen text-xs"></i>
                                    </button>
                                    <form action="{{ route('atributos.destroy', $atributo->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas eliminar este atributo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors flex items-center justify-center cursor-pointer" title="Eliminar">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500 text-sm">
                                <i class="fas fa-tags text-3xl mb-3 text-slate-300 block"></i>
                                No hay atributos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200/80 mt-12">
        @include('componentsHome.footer')
    </footer>

    <!-- MODAL -->
    <div id="modalAtributo" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-md border border-slate-100">
                    <div class="bg-blue-600 px-6 py-4 flex items-center justify-between">
                        <h3 class="text-white font-bold flex items-center gap-2 text-base" id="modalTitle">Nuevo Atributo</h3>
                        <button type="button" onclick="closeModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all cursor-pointer">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form id="formAtributo" method="POST" action="{{ route('atributos.store') }}" class="p-6">
                        @csrf
                        <div id="methodContainer"></div>
                        
                        <div class="mb-5">
                            <label for="nombre" class="block text-sm font-bold text-slate-700 mb-1.5">
                                Nombre del Atributo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nombre" id="nombreInput" required placeholder="Ej: Talla, Color, Material"
                                   class="w-full px-4 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all shadow-sm">
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl font-black text-sm text-white bg-slate-900 hover:bg-slate-800 shadow-lg shadow-slate-900/20 transition-all hover:scale-[1.02] cursor-pointer">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(mode, id = null, nombre = '') {
            const modal = document.getElementById('modalAtributo');
            const form = document.getElementById('formAtributo');
            const methodContainer = document.getElementById('methodContainer');
            const title = document.getElementById('modalTitle');
            const input = document.getElementById('nombreInput');

            modal.classList.remove('hidden');
            
            if (mode === 'edit') {
                title.innerHTML = '<i class="fas fa-pen"></i> Editar Atributo';
                form.action = `/atributos/${id}`;
                methodContainer.innerHTML = '@method("PUT")';
                input.value = nombre;
            } else {
                title.innerHTML = '<i class="fas fa-tag"></i> Nuevo Atributo';
                form.action = "{{ route('atributos.store') }}";
                methodContainer.innerHTML = '';
                input.value = '';
            }
        }

        function closeModal() {
            document.getElementById('modalAtributo').classList.add('hidden');
        }
    </script>
</body>
</html>
