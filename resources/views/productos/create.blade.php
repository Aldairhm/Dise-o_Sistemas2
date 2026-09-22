<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nuevo Producto — AXStore</title>
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
    @vite(['resources/css/home.css', 'resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-slate-800 antialiased font-['Plus_Jakarta_Sans',sans-serif] bg-slate-50 flex flex-col">

    <header class="bg-white/85 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 transition-all duration-300">
        @include('componentsHome.header')
    </header>

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- BREADCRUMB --}}
        <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-6">
            <i class="fas fa-boxes-stacked"></i>
            <a href="{{ route('productos.index') }}" class="hover:underline">Productos</a>
            <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            <span class="text-slate-400 font-medium normal-case">Nuevo producto</span>
        </div>

        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Crear Nuevo Producto</h1>
            <p class="text-sm text-slate-500 mt-1">
                Paso 1 de 2: Completa los datos básicos. Las variantes se añaden en el siguiente paso.
            </p>
        </div>

        {{-- ERRORES DE VALIDACIÓN --}}
        @if($errors->any())
        <div class="mb-6 px-5 py-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm">
            <p class="font-bold mb-2 flex items-center gap-2"><i class="fas fa-circle-exclamation"></i> Por favor corrige los siguientes errores:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- FORMULARIO --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
            <form action="{{ route('productos.store') }}" method="POST" class="p-6 md:p-8" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="nombre" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">
                            Nombre del Producto <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all @error('nombre') border-red-400 bg-red-50 @enderror"
                               placeholder="Ej: Camiseta Premium" value="{{ old('nombre') }}">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">
                            SKU del Producto
                        </label>
                        <input type="text" disabled
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-500 bg-slate-50 cursor-not-allowed"
                               placeholder="Se generará automáticamente">
                    </div>

                    <div>
                        <label for="marca" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">
                            Marca
                        </label>
                        <input type="text" name="marca" id="marca"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all @error('marca') border-red-400 bg-red-50 @enderror"
                               placeholder="Ej: Nike" maxlength="100" value="{{ old('marca') }}">
                    </div>

                    <div>
                        <label for="id_categoria" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">
                            Categoría <span class="text-red-500">*</span>
                        </label>
                        <select name="id_categoria" id="id_categoria"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all @error('id_categoria') border-red-400 bg-red-50 @enderror">
                            <option value="">Selecciona una categoría...</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ old('id_categoria') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="descripcion" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="4"
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all resize-none"
                              placeholder="Describe el producto brevemente...">{{ old('descripcion') }}</textarea>
                </div>

                <div class="mb-6">
                    <label for="comision" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">
                        Comisión (General) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative w-full sm:w-1/3">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                        <input type="number" step="0.01" min="0" name="comision" id="comision"
                               class="w-full rounded-xl border border-slate-200 pl-8 pr-4 py-2.5 text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all @error('comision') border-red-400 bg-red-50 @enderror"
                               value="{{ old('comision', 0) }}">
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Estado</label>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="estado" value="1" {{ old('estado', '1') == '1' ? 'checked' : '' }} class="accent-blue-600 w-4 h-4">
                            <span class="text-sm font-semibold text-slate-700">Activo</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="estado" value="0" {{ old('estado') == '0' ? 'checked' : '' }} class="accent-red-500 w-4 h-4">
                            <span class="text-sm font-semibold text-slate-700">Inactivo</span>
                        </label>
                    </div>
                </div>

                <!-- Atributos Dinámicos -->
                <div class="mb-8 p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">
                        <i class="fas fa-tags text-blue-500 mr-1"></i> Atributos del Producto (Opcional)
                    </label>
                    <p class="text-xs text-slate-500 mb-4">Selecciona los atributos que definirán las variantes de este producto (ej. si vas a tener variantes por Color y Talla, selecciona ambos).</p>
                    
                    <div id="atributos-checkbox-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-4">
                        @if($atributos->count() > 0)
                            @foreach($atributos as $atributo)
                                <label class="flex items-center gap-2 p-3 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all">
                                    <input type="checkbox" name="atributos[]" value="{{ $atributo->id }}" 
                                           class="accent-blue-600 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                                    <span class="text-sm font-semibold text-slate-700">{{ $atributo->nombre }}</span>
                                </label>
                            @endforeach
                        @else
                            <div id="no-atributos-msg" class="text-sm text-slate-500 italic col-span-full">No hay atributos creados. Usa el campo de abajo para crear uno.</div>
                        @endif
                    </div>

                    <!-- Agregar Atributo Rápido -->
                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-slate-200">
                        <input type="text" id="nuevo_atributo_nombre" class="w-full max-w-xs rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all shadow-sm" placeholder="Ej: Material, Talla...">
                        <button type="button" id="btn_crear_atributo" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm">
                            <i class="fas fa-plus mr-1"></i> Añadir
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="imagen_principal" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Imagen Principal (Opcional)</label>
                    <input type="file" name="imagen_principal" id="imagen_principal" accept="image/*"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('productos.index') }}"
                       class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-extrabold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-500/25 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-arrow-right"></i>
                        Guardar y Agregar Variantes
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200/80 mt-12">
        @include('componentsHome.footer')
    </footer>

    <script>
        document.getElementById('btn_crear_atributo').addEventListener('click', () => {
            const input = document.getElementById('nuevo_atributo_nombre');
            const nombre = input.value.trim();
            if (!nombre) return;

            const container = document.getElementById('atributos-checkbox-container');
            const noMsg = document.getElementById('no-atributos-msg');
            if (noMsg) noMsg.remove();

            const lbl = document.createElement('label');
            lbl.className = 'flex items-center gap-2 p-3 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all group';
            lbl.innerHTML = `
                <input type="checkbox" name="nuevos_atributos[]" value="${nombre}" checked
                       class="accent-blue-600 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                <span class="text-sm font-semibold text-slate-700 flex-1">${nombre}</span>
                <button type="button" class="text-slate-400 hover:text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity" onclick="editarAtributo(this, event)">
                    <i class="fas fa-pencil"></i>
                </button>
            `;
            container.appendChild(lbl);
            input.value = '';
        });

        window.editarAtributo = function(btn, event) {
            event.preventDefault();
            event.stopPropagation();

            const label = btn.closest('label');
            const span = label.querySelector('span');
            const checkbox = label.querySelector('input[type="checkbox"]');
            const oldText = span.innerText;

            const input = document.createElement('input');
            input.type = 'text';
            input.value = oldText;
            input.className = 'text-sm font-semibold text-slate-800 bg-white border border-blue-400 rounded px-1 flex-1 focus:outline-none focus:ring-2 focus:ring-blue-500';
            
            span.replaceWith(input);
            btn.style.display = 'none';
            input.focus();

            input.addEventListener('click', (e) => e.stopPropagation());

            const saveEdit = () => {
                const newText = input.value.trim();
                if (!newText || newText === oldText) {
                    input.replaceWith(span);
                    btn.style.display = '';
                    return;
                }

                span.innerText = newText;
                input.replaceWith(span);
                btn.style.display = '';

                if (checkbox.name === 'atributos[]') {
                    checkbox.name = 'nuevos_atributos[]';
                }
                checkbox.value = newText;
                checkbox.checked = true;
            };

            input.addEventListener('blur', saveEdit);
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    saveEdit();
                }
            });
        };
    </script>
</body>
</html>
