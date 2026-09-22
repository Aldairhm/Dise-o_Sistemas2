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
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-extrabold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-500/25 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-save"></i>
                        Actualizar Producto
                    </button>
                </div>
            </form>
        </div>

        {{-- GESTIÓN DE VARIANTES --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-layer-group text-blue-500"></i>
                        Gestión de Variantes
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">SKU, precio, stock e imágenes por cada combinación del producto.</p>
                </div>
                <button type="button" id="btnAgregarVariante"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-lg shadow-emerald-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] whitespace-nowrap">
                    <i class="fas fa-plus text-xs"></i>
                    Agregar Variante
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="tablaVariantes">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80">
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Imagen</th>
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">SKU</th>
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Variante</th>
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Precio</th>
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Stock</th>
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Reserva</th>
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Estado</th>
                            <th class="text-right px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($producto->variantes as $variante)
                        <tr class="hover:bg-slate-50/60 transition-colors" data-id="{{ $variante->id }}">
                            <td class="px-6 py-3">
                                @php
                                    $imgP = $variante->imagenes->where('es_principal', 1)->first() ?? $variante->imagenes->first();
                                @endphp
                                @if($imgP)
                                    <img src="{{ asset('storage/' . $imgP->ruta_imagen) }}" alt="img"
                                         class="w-10 h-10 rounded-lg object-cover border border-slate-200">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300">
                                        <i class="fas fa-image text-sm"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-3 font-mono text-xs text-slate-500">{{ $variante->sku ?? '—' }}</td>
                            <td class="px-6 py-3 font-semibold text-slate-800">{{ $variante->nombre_variante }}</td>
                            <td class="px-6 py-3 font-bold text-slate-800">${{ number_format($variante->precio_venta, 2) }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full
                                    {{ $variante->stock > 10 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                     : ($variante->stock > 0  ? 'bg-amber-50 text-amber-700 border border-amber-200'
                                                               : 'bg-red-50 text-red-600 border border-red-200') }}">
                                    {{ $variante->stock }}
                                </span>
                            </td>
                            <td class="px-6 py-3 hidden md:table-cell text-slate-500 text-xs">{{ $variante->reserva }}</td>
                            <td class="px-6 py-3">
                                @if($variante->estado == 1)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-2.5 py-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 bg-red-50 border border-red-200 rounded-full px-2.5 py-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button"
                                            class="btn-duplicar-variante p-2 text-xs font-bold text-violet-600 bg-violet-50 hover:bg-violet-100 border border-violet-200 rounded-lg transition-colors"
                                            data-id="{{ $variante->id }}" title="Duplicar">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button type="button"
                                            class="btn-editar-variante p-2 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors"
                                            data-id="{{ $variante->id }}" title="Editar">
                                        <i class="fas fa-pencil"></i>
                                    </button>
                                    <button type="button"
                                            class="btn-eliminar-variante p-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors"
                                            data-id="{{ $variante->id }}" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="rowNoVariantes">
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <i class="fas fa-layer-group text-3xl mb-3 block opacity-30"></i>
                                <p class="font-semibold text-sm">Este producto aún no tiene variantes.</p>
                                <p class="text-xs mt-1">Usa el botón <strong>"Agregar Variante"</strong> para comenzar.</p>
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

    @include('productos.modal-variante')

    <script>
        window.PRODUCTOS_ROUTES = {
            storeVariante:  "{{ route('variantes.store', $producto->id) }}",
            getVariante:    "{{ url('variantes') }}",
            updateVariante: "{{ url('variantes') }}",
        };
    </script>

</body>
</html>
