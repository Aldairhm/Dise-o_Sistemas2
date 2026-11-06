<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    
    <div class="text-center mb-14">
        <p class="text-xs font-black text-blue-600 uppercase tracking-widest mb-3 flex items-center justify-center gap-2">
            <i class="fas fa-fire"></i> Más Populares
        </p>
        <h2 class="text-3xl font-black text-gray-900 sm:text-4xl">Productos Destacados</h2>
        <p class="mt-4 text-base text-gray-500 max-w-xl mx-auto">Encuentra los mejores accesorios al mejor precio, ordenados por disponibilidad.</p>
        <div class="w-20 h-1 bg-blue-600 mx-auto mt-5 rounded-full"></div>
    </div>

    
    <div class="max-w-4xl mx-auto mb-10">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($categorias) && $categorias->isNotEmpty()): ?>
        <div class="flex flex-wrap justify-center gap-3 mb-6">
            <button class="filter-btn active px-6 py-2 rounded-full text-sm font-bold bg-blue-600 text-white shadow-md shadow-blue-500/30 transition-all transform hover:-translate-y-1" data-category="all">
                Todas
            </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <button class="filter-btn px-6 py-2 rounded-full text-sm font-bold bg-white text-slate-500 border border-slate-200 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition-all transform hover:-translate-y-1 shadow-sm" data-category="<?php echo e($cat->id); ?>">
                <?php echo e($cat->nombre); ?>

            </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="relative max-w-lg mx-auto transform transition-all focus-within:scale-[1.02]">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-search"></i>
            </div>
            <input type="text" id="search-home-products" 
                   class="w-full pl-11 pr-11 py-3.5 bg-white border border-slate-200 rounded-2xl text-sm font-medium text-slate-800 placeholder:text-slate-400 shadow-sm focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all"
                   placeholder="¿Qué estás buscando hoy?">
            <button type="button" id="clear-search-home" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-600 hidden transition-colors cursor-pointer">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->isNotEmpty()): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-8">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php
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
                    'id'      => $v->id,
                    'nombre'  => $v->nombre_variante,
                    'precio'  => $v->precio_venta,
                    'stock'   => $v->stock,
                    'reserva' => $v->reserva,
                    'sku'     => $v->sku,
                    'imgUrl'  => $vImgUrl,
                    'imagenes'=> $vImgsUrls,
                    'valores' => $v->valores->map(fn($val) => [
                        'atributo' => $val->atributo?->nombre ?? '',
                        'valor'    => $val->valor,
                    ])->values()->toArray(),
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

            // El producto se marca Agotado solo si NINGUNA variante tiene stock ni reserva
            $todoAgotado = $variantesActivas->every(fn($v) => $v->stock == 0 && $v->reserva == 0);
        ?>

        <div class="product-card group bg-white rounded-3xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-transparent hover:border-blue-100 transition-all duration-300 flex flex-col overflow-hidden hover:-translate-y-1" 
             data-category-id="<?php echo e($prod->id_categoria); ?>"
             data-name="<?php echo e(strtolower($prod->nombre)); ?>">

            
            <div class="relative bg-slate-50/70 p-4 m-3 rounded-2xl flex items-center justify-center h-[260px] overflow-hidden group-hover:bg-blue-50/30 transition-colors">
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prod->sku): ?>
                <div class="absolute top-3 left-3 bg-white text-gray-800 text-[11px] font-bold px-3 py-1.5 rounded-full shadow-sm z-10">
                    SKU: <span class="font-medium text-gray-500"><?php echo e($prod->sku); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($todoAgotado && $variantesActivas->isNotEmpty()): ?>
                <div class="absolute top-3 right-3 z-10 flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-500 text-white text-[10px] font-black uppercase tracking-wider shadow-md shadow-red-500/30">
                    <i class="fas fa-ban text-[9px]"></i> Agotado
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prodImgUrl): ?>
                    <img src="<?php echo e($prodImgUrl); ?>" alt="<?php echo e($prod->nombre); ?>"
                         class="w-full h-full object-cover rounded-xl transform group-hover:scale-105 transition-transform duration-500">
                <?php else: ?>
                    <div class="flex flex-col items-center gap-2 text-gray-300">
                        <i class="fas fa-image text-4xl"></i>
                        <span class="text-xs font-semibold">Sin imagen</span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="px-6 pb-6 pt-2 flex flex-col flex-1">
                
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[11px] font-black text-blue-600 uppercase tracking-wider">
                        <?php echo e($catNombre); ?>

                    </p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prod->marca): ?>
                        <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md uppercase tracking-wider border border-orange-200">
                            <?php echo e($prod->marca); ?>

                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <h3 class="text-lg font-bold text-gray-900 leading-tight mb-3 line-clamp-2">
                    <?php echo e($prod->nombre); ?>

                </h3>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prod->atributos->count() > 0): ?>
                <div class="mb-2">
                    <div class="flex flex-wrap gap-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $prod->atributos->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[9.5px] font-bold bg-indigo-50/80 text-indigo-600 border border-indigo-100/50 uppercase tracking-wide">
                                <i class="fas fa-tag mr-1 text-[8px] opacity-60"></i><?php echo e(Str::limit($attr->nombre, 15)); ?>

                            </span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prod->atributos->count() > 3): ?>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[9.5px] font-bold bg-slate-50 text-slate-500 border border-slate-200">
                                +<?php echo e($prod->atributos->count() - 3); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="mb-4">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Variantes</p>
                    <div class="flex flex-wrap gap-1.5">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = array_slice($variantesData, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $vStock   = $vData['stock']   ?? 0;
                                $vReserva = $vData['reserva'] ?? 0;
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vStock == 0 && $vReserva == 0): ?>
                                
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-50 border border-red-200 text-[10px] font-bold text-red-500">
                                    <i class="fas fa-ban text-[8px]"></i>
                                    <?php echo e(Str::limit($vData['nombre'], 13)); ?>

                                </span>
                            <?php elseif($vStock == 0 && $vReserva > 0): ?>
                                
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 border border-amber-300 text-[10px] font-bold text-amber-700">
                                    <i class="fas fa-warehouse text-[8px]"></i>
                                    <?php echo e(Str::limit($vData['nombre'], 13)); ?>

                                </span>
                            <?php else: ?>
                                
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-50 border border-gray-200 text-[10px] font-medium text-gray-600">
                                    <?php echo e(Str::limit($vData['nombre'], 15)); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <span class="text-[10px] text-gray-400">Sin variantes</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($variantesData) > 3): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-50 border border-gray-200 text-[10px] font-bold text-blue-600">
                                +<?php echo e(count($variantesData) - 3); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="flex items-center justify-between mt-auto">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Comisión</p>
                        <p class="text-2xl font-black text-gray-900 flex items-start leading-none">
                            <span class="text-base font-bold text-blue-600 mt-0.5 mr-0.5">$</span><?php echo e(number_format($prod->comision, 2)); ?>

                        </p>
                    </div>

                    
                    <button type="button"
                            onclick="openModal(this)"
                            data-name="<?php echo e($prod->nombre); ?>"
                            data-price="<?php echo e(number_format($precio, 2)); ?>"
                            data-category="<?php echo e($catNombre); ?>"
                            data-marca="<?php echo e($prod->marca ?? ''); ?>"
                            data-sku="<?php echo e($sku); ?>"
                            data-stock="<?php echo e($stock); ?>"
                            data-reserva="<?php echo e($reserva); ?>"
                            data-image="<?php echo e($imgUrl ?? ''); ?>"
                            data-images="<?php echo e(json_encode($imagenesUrls)); ?>"
                            data-variants="<?php echo e(json_encode($variantesData)); ?>"
                            data-description="<?php echo e($prod->descripcion ?? 'No hay descripción disponible.'); ?>"
                            data-atributos="<?php echo e(json_encode($prod->atributos->map(fn($a) => $a->nombre)->values())); ?>"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-blue-600 text-slate-700 hover:text-white font-bold text-xs transition-all shadow-sm hover:shadow-md hover:shadow-blue-600/20 active:scale-95 group cursor-pointer"
                            title="Ver detalles de <?php echo e($prod->nombre); ?>">
                        <i class="fas fa-eye text-slate-400 group-hover:text-white transition-colors text-xs"></i>
                        <span class="tracking-wide">Ver</span>
                    </button>
                </div>
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    <?php else: ?>
    
    <div class="text-center py-16 text-gray-400">
        <i class="fas fa-boxes-stacked text-5xl mb-4 block opacity-30"></i>
        <p class="text-lg font-semibold">No hay productos activos disponibles aún.</p>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
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
    const searchInput = document.getElementById('search-home-products');
    const clearSearchBtn = document.getElementById('clear-search-home');
    
    let currentCategory = 'all';
    let searchQuery = '';
    let isExpanded = false;
    const ITEMS_PER_PAGE = 4; // Límite inicial de productos a mostrar (1 fila)

    // Elemento para mostrar "No hay resultados"
    const gridContainer = document.querySelector('.grid');
    let noResultsMsg = document.createElement('div');
    noResultsMsg.className = 'col-span-full text-center py-12 text-slate-400 hidden';
    noResultsMsg.innerHTML = `
        <i class="fas fa-search text-4xl mb-4 block opacity-30"></i>
        <p class="text-lg font-semibold">No se encontraron productos.</p>
        <p class="text-sm mt-1">Intenta con otra búsqueda o categoría.</p>
    `;
    if (gridContainer) {
        gridContainer.appendChild(noResultsMsg);
    }

    function renderProducts() {
        if (!gridContainer) return;

        // 1. Filtrar por categoría y búsqueda
        const filtered = productCards.filter(card => {
            const matchCat = currentCategory === 'all' || card.getAttribute('data-category-id') === currentCategory;
            const matchName = card.getAttribute('data-name').includes(searchQuery);
            return matchCat && matchName;
        });

        // 2. Ocultar todos
        productCards.forEach(card => card.style.display = 'none');

        // 3. Mostrar los correspondientes
        const itemsToShow = isExpanded ? filtered.length : Math.min(filtered.length, ITEMS_PER_PAGE);
        for(let i = 0; i < itemsToShow; i++) {
            filtered[i].style.display = 'flex';
        }

        // 4. Mostrar mensaje si no hay resultados
        if (filtered.length === 0) {
            noResultsMsg.classList.remove('hidden');
        } else {
            noResultsMsg.classList.add('hidden');
        }

        // 5. Lógica del botón Ver Todos
        if (btnVerTodos && btnContainer) {
            if (filtered.length <= ITEMS_PER_PAGE) {
                // Si son muy pocos o cero, ocultar el botón
                btnContainer.style.display = 'none';
            } else {
                btnContainer.style.display = 'block';
                if (isExpanded) {
                    btnVerTodos.innerHTML = 'Ver menos <i class="fas fa-arrow-up ml-1"></i>';
                } else {
                    btnVerTodos.innerHTML = `Ver todos (${filtered.length}) <i class="fas fa-arrow-down ml-1"></i>`;
                }
            }
        }
    }

    // Eventos Filtros de Categoría
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

    // Eventos Buscador
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.toLowerCase().trim();
            
            if (searchQuery.length > 0) {
                clearSearchBtn.classList.remove('hidden');
            } else {
                clearSearchBtn.classList.add('hidden');
            }
            
            isExpanded = true; // Si busca, mostrar todos los resultados filtrados sin colapsar (opcional, pero buena UX)
            if (searchQuery === '') isExpanded = false; // Si borra, volver a 4

            renderProducts();
        });
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', () => {
            searchInput.value = '';
            searchQuery = '';
            clearSearchBtn.classList.add('hidden');
            isExpanded = false;
            renderProducts();
        });
    }

    // Evento Ver Todos
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
</script><?php /**PATH C:\laragon\www\diseno\resources\views/componentsHome/productSection.blade.php ENDPATH**/ ?>