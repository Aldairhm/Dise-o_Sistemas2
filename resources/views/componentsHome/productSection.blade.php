<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Encabezado --}}
    <div class="text-center mb-14">
        <p class="text-xs font-black text-blue-600 uppercase tracking-widest mb-3 flex items-center justify-center gap-2">
            <i class="fas fa-fire"></i> Más Populares
        </p>
        <h2 class="text-3xl font-black text-gray-900 sm:text-4xl">Productos Destacados</h2>
        <p class="mt-4 text-base text-gray-500 max-w-xl mx-auto">Encuentra los mejores accesorios al mejor precio, ordenados por disponibilidad.</p>
        <div class="w-20 h-1 bg-blue-600 mx-auto mt-5 rounded-full"></div>
    </div>

    {{-- Filtros de Categoría --}}
    @if(isset($categorias) && $categorias->isNotEmpty())
    <div class="flex flex-wrap justify-center gap-3 mb-10">
        <button class="filter-btn active px-6 py-2 rounded-full text-sm font-bold bg-blue-600 text-white shadow-md shadow-blue-500/30 transition-all transform hover:-translate-y-1" data-category="all">
            Todas
        </button>
        @foreach($categorias as $cat)
        <button class="filter-btn px-6 py-2 rounded-full text-sm font-bold bg-white text-slate-500 border border-slate-200 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition-all transform hover:-translate-y-1 shadow-sm" data-category="{{ $cat->id }}">
            {{ $cat->nombre }}
        </button>
        @endforeach
    </div>
    @endif

    {{-- Grid de Tarjetas --}}
    @if($products->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-8">
        @foreach($products as $prod)
        @php
            $variantesActivas = $prod->variantes->where('estado', 1)->values();
            $variante = $variantesActivas->first();
            $imgUrl   = null;
            $imagenesUrls = [];
            $variantesData = [];
            
            $prodImgUrl = $prod->imagen_principal ? asset('storage/' . $prod->imagen_principal) : null;
            
            foreach($variantesActivas as $v) {
                $vImg = $v->imagenes->where('es_principal', 1)->first() ?? $v->imagenes->first();
                $vImgUrl = $vImg ? asset('storage/' . $vImg->ruta_imagen) : $prodImgUrl;
                $vImgsUrls = [];
                foreach ($v->imagenes->sortByDesc('es_principal') as $img) {
                    $vImgsUrls[] = asset('storage/' . $img->ruta_imagen);
                }
                if (empty($vImgsUrls) && $prodImgUrl) {
                    $vImgsUrls[] = $prodImgUrl;
                }
                
                $variantesData[] = [
                    'id' => $v->id,
                    'nombre' => $v->nombre_variante,
                    'precio' => $v->precio_venta,
                    'stock' => $v->stock,
                    'reserva' => $v->reserva,
                    'sku' => $v->sku,
                    'imgUrl' => $vImgUrl,
                    'imagenes' => $vImgsUrls
                ];
            }

            if ($variante && count($variantesData) > 0) {
                $imgUrl = $variantesData[0]['imgUrl'];
                $imagenesUrls = $variantesData[0]['imagenes'];
            } else {
                $imgUrl = $prodImgUrl;
                $imagenesUrls = $prodImgUrl ? [$prodImgUrl] : [];
            }
            $precio   = $variante?->precio_venta ?? 0;
            $stock    = $variante?->stock ?? 0;
            $reserva  = $variante?->reserva ?? 0;
            $sku      = $variante?->sku ?? '';
            $catNombre = $prod->categoria?->nombre ?? 'Sin categoría';
        @endphp

        <div class="product-card group bg-white rounded-3xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-transparent hover:border-blue-100 transition-all duration-300 flex flex-col overflow-hidden hover:-translate-y-1" data-category-id="{{ $prod->id_categoria }}">

            {{-- Imagen --}}
            <div class="relative bg-slate-50/70 p-4 m-3 rounded-2xl flex items-center justify-center h-[260px] overflow-hidden group-hover:bg-blue-50/30 transition-colors">
                {{-- Badge SKU --}}
                @if($prod->sku)
                <div class="absolute top-3 left-3 bg-white text-gray-800 text-[11px] font-bold px-3 py-1.5 rounded-full shadow-sm z-10">
                    SKU: <span class="font-medium text-gray-500">{{ $prod->sku }}</span>
                </div>
                @endif

                @if($prodImgUrl)
                    <img src="{{ $prodImgUrl }}" alt="{{ $prod->nombre }}"
                         class="w-full h-full object-cover rounded-xl transform group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="flex flex-col items-center gap-2 text-gray-300">
                        <i class="fas fa-image text-4xl"></i>
                        <span class="text-xs font-semibold">Sin imagen</span>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="px-6 pb-6 pt-2 flex flex-col flex-1">
                {{-- Categoría y Marca --}}
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[11px] font-black text-blue-600 uppercase tracking-wider">
                        {{ $catNombre }}
                    </p>
                    @if($prod->marca)
                        <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md uppercase tracking-wider border border-orange-200">
                            {{ $prod->marca }}
                        </span>
                    @endif
                </div>

                {{-- Nombre --}}
                <h3 class="text-lg font-bold text-gray-900 leading-tight mb-3 line-clamp-2">
                    {{ $prod->nombre }}
                </h3>

                {{-- Variantes disponibles (Pills mini) --}}
                <div class="mb-4">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Variantes</p>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse(array_slice($variantesData, 0, 3) as $vData)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-50 border border-gray-200 text-[10px] font-medium text-gray-600">
                                {{ Str::limit($vData['nombre'], 15) }}
                            </span>
                        @empty
                            <span class="text-[10px] text-gray-400">Sin variantes</span>
                        @endforelse
                        @if(count($variantesData) > 3)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-50 border border-gray-200 text-[10px] font-bold text-blue-600">
                                +{{ count($variantesData) - 3 }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Comisión + Botones --}}
                <div class="flex items-center justify-between mt-auto">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Comisión</p>
                        <p class="text-2xl font-black text-gray-900 flex items-start leading-none">
                            <span class="text-base font-bold text-blue-600 mt-0.5 mr-0.5">$</span>{{ number_format($prod->comision, 2) }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Ver detalles --}}
                        <button type="button"
                                onclick="openModal(this)"
                                data-name="{{ $prod->nombre }}"
                                data-price="{{ number_format($precio, 2) }}"
                                data-category="{{ $catNombre }}"
                                data-marca="{{ $prod->marca ?? '' }}"
                                data-sku="{{ $sku }}"
                                data-stock="{{ $stock }}"
                                data-reserva="{{ $reserva }}"
                                data-image="{{ $imgUrl ?? '' }}"
                                data-images="{{ json_encode($imagenesUrls) }}"
                                data-variants="{{ json_encode($variantesData) }}"
                                data-description="{{ $prod->descripcion ?? 'No hay descripción disponible.' }}"
                                class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 text-gray-600 flex items-center justify-center transition-colors"
                                title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>

                        {{-- Agregar al carrito --}}
                        <button type="button"
                                class="w-10 h-10 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center shadow-lg shadow-blue-500/30 transition-all hover:scale-105"
                                title="Agregar al carrito">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @else
    {{-- Empty state --}}
    <div class="text-center py-16 text-gray-400">
        <i class="fas fa-boxes-stacked text-5xl mb-4 block opacity-30"></i>
        <p class="text-lg font-semibold">No hay productos activos disponibles aún.</p>
    </div>
    @endif

    {{-- Botón ver todos --}}
    <div class="mt-12 text-center" id="btn-ver-todos-container">
        <button type="button" id="btn-ver-todos"
           class="inline-flex items-center justify-center gap-2 px-8 py-3 border-2 border-slate-200 text-base font-semibold rounded-full text-slate-700 bg-white hover:border-blue-600 hover:text-blue-600 transition-all hover:shadow-md">
            Ver todos los productos <i class="fas fa-arrow-down ml-1"></i>
        </button>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const productCards = Array.from(document.querySelectorAll('.product-card'));
    const btnVerTodos = document.getElementById('btn-ver-todos');
    const btnContainer = document.getElementById('btn-ver-todos-container');
    
    let currentCategory = 'all';
    let isExpanded = false;
    const ITEMS_PER_PAGE = 4; // Límite inicial de productos a mostrar (1 fila)

    function renderProducts() {
        // 1. Filtrar por categoría
        const filtered = productCards.filter(card => {
            return currentCategory === 'all' || card.getAttribute('data-category-id') === currentCategory;
        });

        // 2. Ocultar todos
        productCards.forEach(card => card.style.display = 'none');

        // 3. Mostrar los correspondientes
        const itemsToShow = isExpanded ? filtered.length : Math.min(filtered.length, ITEMS_PER_PAGE);
        for(let i = 0; i < itemsToShow; i++) {
            filtered[i].style.display = 'flex';
        }

        // 4. Lógica del botón Ver Todos
        if (btnVerTodos && btnContainer) {
            if (filtered.length <= ITEMS_PER_PAGE) {
                // Si son muy pocos, ocultar el botón
                btnContainer.style.display = 'none';
            } else {
                btnContainer.style.display = 'block';
                if (isExpanded) {
                    btnVerTodos.innerHTML = 'Ver menos <i class="fas fa-arrow-up ml-1"></i>';
                } else {
                    btnVerTodos.innerHTML = 'Ver todos los productos <i class="fas fa-arrow-down ml-1"></i>';
                }
            }
        }
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => {
                b.classList.remove('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-500/30');
                b.classList.add('bg-white', 'text-slate-500', 'border-slate-200');
            });
            btn.classList.add('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-500/30');
            btn.classList.remove('bg-white', 'text-slate-500', 'border-slate-200');

            currentCategory = btn.getAttribute('data-category');
            isExpanded = false; // Al cambiar categoría, colapsamos
            renderProducts();
        });
    });

    if (btnVerTodos) {
        btnVerTodos.addEventListener('click', (e) => {
            e.preventDefault();
            isExpanded = !isExpanded;
            renderProducts();
        });
    }

    // Inicializar la vista
    renderProducts();
});
</script>