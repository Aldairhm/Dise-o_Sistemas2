<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Producto — AXStore</title>
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
    @vite(['resources/css/home.css', 'resources/css/productos.css', 'resources/css/app.css', 'resources/js/app.js', 'resources/js/productos.js'])
</head>
<body class="min-h-screen text-slate-800 antialiased font-['Plus_Jakarta_Sans',sans-serif] bg-slate-50 flex flex-col"
      data-producto-id="{{ $producto->id }}">

    <header class="bg-white/85 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 transition-all duration-300">
        @include('componentsHome.header')
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- BREADCRUMB --}}
        <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-6">
            <i class="fas fa-boxes-stacked"></i>
            <a href="{{ route('productos.index') }}" class="hover:underline">Productos</a>
            <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            <span class="text-slate-400 font-medium normal-case truncate max-w-[200px]">{{ $producto->nombre }}</span>
        </div>

        {{-- FLASH SUCCESS → SweetAlert --}}
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

        {{-- ERRORES DE VALIDACIÓN --}}
        @if($errors->any())
        <div class="mb-6 px-5 py-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm">
            <p class="font-bold mb-2 flex items-center gap-2"><i class="fas fa-circle-exclamation"></i> Corrige los siguientes errores:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        {{-- INFORMACIÓN GENERAL DEL PRODUCTO --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-8 mb-6">
            <h2 class="text-base font-black text-slate-900 mb-5 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                Información General
            </h2>
            <form action="{{ route('productos.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="nombre" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">
                            Nombre del Producto <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                               value="{{ old('nombre', $producto->nombre) }}">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">
                            SKU del Producto
                        </label>
                        <input type="text" value="{{ $producto->sku }}" disabled
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-500 bg-slate-50 cursor-not-allowed">
                    </div>
                    <div>
                        <label for="marca" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">
                            Marca
                        </label>
                        <input type="text" name="marca" id="marca" maxlength="100"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all @error('marca') border-red-400 bg-red-50 @enderror"
                               value="{{ old('marca', $producto->marca) }}">
                    </div>
                    <div>
                        <label for="id_categoria" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">
                            Categoría <span class="text-red-500">*</span>
                        </label>
                        <select name="id_categoria" id="id_categoria" required
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                            <option value="">Selecciona una categoría...</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ old('id_categoria', $producto->id_categoria) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="imagen_principal" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Imagen Principal (Opcional)</label>
                    <div class="flex items-center gap-4">
                        @if($producto->imagen_principal)
                            <img src="{{ asset('storage/' . $producto->imagen_principal) }}" alt="Imagen actual" class="w-16 h-16 rounded-lg object-cover border border-slate-200 shadow-sm">
                        @endif
                        <input type="file" name="imagen_principal" id="imagen_principal" accept="image/*"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <div class="mb-6">
                    <label for="descripcion" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="3"
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all resize-none">{{ old('descripcion', $producto->descripcion) }}</textarea>
                </div>

                <div class="mb-6">
                    <label for="comision" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">
                        Comisión (General) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative w-full sm:w-1/3">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                        <input type="number" step="0.01" min="0" name="comision" id="comision" required
                               class="w-full rounded-xl border border-slate-200 pl-8 pr-4 py-2.5 text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all @error('comision') border-red-400 bg-red-50 @enderror"
                               value="{{ old('comision', $producto->comision) }}">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Estado</label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="estado" value="1" {{ old('estado', $producto->estado) == 1 ? 'checked' : '' }} class="accent-blue-600 w-4 h-4">
                                <span class="text-sm font-semibold text-slate-700">Activo</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="estado" value="0" {{ old('estado', $producto->estado) == 0 ? 'checked' : '' }} class="accent-red-500 w-4 h-4">
                                <span class="text-sm font-semibold text-slate-700">Inactivo</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Atributos Dinámicos -->
                <div class="mb-8 p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">
                        <i class="fas fa-tags text-blue-500 mr-1"></i> Atributos del Producto (Opcional)
                    </label>
                    <p class="text-xs text-slate-500 mb-4">Selecciona los atributos que definirán las variantes de este producto.</p>
                    
                    @php
                        $productoAtributosIds = $producto->atributos->pluck('id')->toArray();
                    @endphp
                    
                    <div id="atributos-checkbox-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-4">
                        @if($atributos->count() > 0)
                            @foreach($atributos as $atributo)
                                <label class="flex items-center gap-2 p-3 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all group">
                                    <input type="checkbox" name="atributos[]" value="{{ $atributo->id }}" 
                                           {{ in_array($atributo->id, $productoAtributosIds) ? 'checked' : '' }}
                                           class="accent-blue-600 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                                    <span class="text-sm font-semibold text-slate-700 flex-1">{{ $atributo->nombre }}</span>
                                    <button type="button" class="text-slate-400 hover:text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity" onclick="editarAtributo(this, event)">
                                        <i class="fas fa-pencil"></i>
                                    </button>
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

                <div class="flex flex-col sm:flex-row justify-end items-start sm:items-center gap-4">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-extrabold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-500/25 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-save"></i>
                        Actualizar Producto
                    </button>
                </div>
            </form>
        </div>

        {{-- GESTIÓN DE VARIANTES --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-layer-group text-blue-500"></i>
                        Gestión de Variantes
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">SKU, precio, stock e imágenes por cada combinación del producto.</p>
                </div>
                
                <div class="flex items-center gap-3 flex-wrap">
                    <!-- Tabla / Tarjetas -->
                    <div class="flex items-center bg-slate-100 rounded-xl p-1 gap-1">
                        <button type="button" id="btnViewTableVar"
                                onclick="setViewVar('table')"
                                class="var-view-btn w-9 h-9 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors cursor-pointer"
                                title="Vista de tabla">
                            <i class="fas fa-table-list text-sm"></i>
                        </button>
                        <button type="button" id="btnViewCardsVar"
                                onclick="setViewVar('cards')"
                                class="var-view-btn w-9 h-9 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors cursor-pointer"
                                title="Vista de tarjetas">
                            <i class="fas fa-grip text-sm"></i>
                        </button>
                    </div>

                    <button type="button" id="btnAgregarVariante"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-lg shadow-emerald-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] whitespace-nowrap">
                        <i class="fas fa-plus text-xs"></i>
                        Agregar Variante
                    </button>
                </div>
            </div>

            <!-- BUSQUEDA Y FILTROS VARIANTES -->
            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <!-- Campo de Busqueda -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-search text-sm"></i>
                    </div>
                    <input type="text" 
                           id="searchVarInput" 
                           placeholder="Buscar por SKU o nombre de variante..." 
                           class="w-full pl-10 pr-10 py-2 bg-white hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-600 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all"
                           oninput="applyFiltersVar()">
                    <button type="button" 
                            id="clearSearchVarBtn" 
                            onclick="clearSearchVar()" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 hidden cursor-pointer">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <!-- Filtro por Estado -->
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:inline">Estado:</span>
                        <select id="statusVarFilter" 
                                onchange="applyFiltersVar()" 
                                class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none cursor-pointer">
                            <option value="todos">Todos</option>
                            <option value="1">Activos</option>
                            <option value="0">Inactivos</option>
                        </select>
                    </div>

                    <!-- Boton Restablecer -->
                    <button type="button" 
                            onclick="resetAllFiltersVar()" 
                            class="p-2 text-slate-400 hover:text-blue-600 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer"
                            title="Restablecer filtros">
                        <i class="fas fa-rotate-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Contenedor Principal (Tabla o Tarjetas) -->
            <div id="variantsContainer" class="relative">
                <!-- Vista Tabla -->
                <div id="variantsTableWrapper">
                    @include('productos.partials.table_variantes', ['producto' => $producto])
                </div>

                <!-- Vista Tarjetas -->
                <div id="variantsCardsWrapper" class="hidden">
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 bg-slate-50/30" id="variantsCardsGrid">
                        @include('productos.partials.cards_variantes', ['producto' => $producto])
                    </div>
                </div>
            </div>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200/80 mt-12">
        @include('componentsHome.footer')
    </footer>

    @include('productos.modal-variante')

    <script>
        window.PRODUCTOS_ROUTES = {
            storeVariante:  "{{ route('variantes.store', $producto->id) }}",
            getVariante:    "{{ url('variantes') }}",
            updateVariante: "{{ url('variantes') }}",
        };

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

        // ====== Lógica de Vistas y Filtros de Variantes ======
        let currentViewVar = localStorage.getItem('ax_variantes_view') || 'table';

        document.addEventListener('DOMContentLoaded', () => {
            setViewVar(currentViewVar, false);
        });

        function setViewVar(view, save = true) {
            currentViewVar = view;
            if (save) localStorage.setItem('ax_variantes_view', view);

            const btnTable = document.getElementById('btnViewTableVar');
            const btnCards = document.getElementById('btnViewCardsVar');
            const wrapperTable = document.getElementById('variantsTableWrapper');
            const wrapperCards = document.getElementById('variantsCardsWrapper');

            // Estilos botones
            document.querySelectorAll('.var-view-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                btn.classList.add('text-slate-400');
            });

            if (view === 'table') {
                btnTable.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                btnTable.classList.remove('text-slate-400');
                wrapperTable.classList.remove('hidden');
                wrapperCards.classList.add('hidden');
            } else {
                btnCards.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                btnCards.classList.remove('text-slate-400');
                wrapperTable.classList.add('hidden');
                wrapperCards.classList.remove('hidden');
            }
        }

        function applyFiltersVar() {
            const searchInput = document.getElementById('searchVarInput');
            const clearBtn = document.getElementById('clearSearchVarBtn');
            const query = searchInput.value.toLowerCase().trim();
            const status = document.getElementById('statusVarFilter').value;

            // Mostrar/ocultar boton X
            if (query.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }

            // Filtrar tabla
            let visibleRows = 0;
            document.querySelectorAll('.variante-row').forEach(row => {
                const matchQuery = row.dataset.nombre.includes(query) || row.dataset.sku.includes(query);
                const matchStatus = status === 'todos' || row.dataset.estado === status;

                if (matchQuery && matchStatus) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            const noResultsTable = document.getElementById('table-no-results-variantes');
            const emptyRowTable = document.getElementById('rowNoVariantes'); // El mensaje original si no hay ninguna variante
            if (noResultsTable) {
                noResultsTable.style.display = (visibleRows === 0 && !emptyRowTable) ? '' : 'none';
            }

            // Filtrar tarjetas
            let visibleCards = 0;
            document.querySelectorAll('.variante-card').forEach(card => {
                const matchQuery = card.dataset.nombre.includes(query) || card.dataset.sku.includes(query);
                const matchStatus = status === 'todos' || card.dataset.estado === status;

                if (matchQuery && matchStatus) {
                    card.style.display = '';
                    visibleCards++;
                } else {
                    card.style.display = 'none';
                }
            });

            const noResultsCards = document.getElementById('cards-no-results-variantes');
            const emptyCardsMsg = document.getElementById('rowNoVariantesCards');
            if (noResultsCards) {
                if (visibleCards === 0 && !emptyCardsMsg) {
                    noResultsCards.classList.remove('hidden');
                    noResultsCards.classList.add('flex');
                } else {
                    noResultsCards.classList.add('hidden');
                    noResultsCards.classList.remove('flex');
                }
            }
        }

        function clearSearchVar() {
            document.getElementById('searchVarInput').value = '';
            applyFiltersVar();
        }

        function resetAllFiltersVar() {
            document.getElementById('searchVarInput').value = '';
            document.getElementById('statusVarFilter').value = 'todos';
            applyFiltersVar();
        }
    </script>
</body>
</html>
