        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-black text-gray-900 sm:text-4xl">Productos Destacados</h2>
                <p class="mt-4 text-xl text-gray-500">Encuentra los mejores accesorios al mejor precio.</p>
                <div class="w-24 h-1 bg-blue-600 mx-auto mt-6 rounded-full"></div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                @forelse($products as $product)
                <!-- Tarjeta de Producto -->
                <div class="group bg-white rounded-2xl sm:rounded-3xl p-3 sm:p-5 border border-gray-100 hover:border-blue-100 shadow-sm hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 relative flex flex-col">
                    
                    <!-- Imagen -->
                    <div class="relative rounded-xl sm:rounded-2xl overflow-hidden bg-gray-50 mb-3 sm:mb-4 aspect-square flex items-center justify-center group-hover:bg-blue-50 transition-colors">
                        <img src="{{ asset('assets/images/' . ($product->imagen ?? 'default.png')) }}" alt="{{ $product->nombre }}" class="w-3/4 h-3/4 object-contain group-hover:scale-110 transition-transform duration-500">
                        
                        <!-- Badges -->
                        <div class="absolute top-2 left-2 sm:top-3 sm:left-3 flex flex-col gap-2">
                            <span class="bg-white/90 backdrop-blur text-[10px] sm:text-xs font-bold px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-gray-800 shadow-sm">SKU: {{ $product->sku }}</span>
                        </div>
                    </div>

                    <!-- Detalles -->
                    <div class="flex flex-col flex-grow">
                        <span class="text-[10px] sm:text-[11px] font-bold text-blue-600 uppercase tracking-wider mb-1">{{ $product->nombre_categoria ?? 'Accesorio' }}</span>
                        <h3 class="text-gray-900 font-bold text-sm sm:text-lg leading-tight mb-2 line-clamp-2">{{ $product->nombre }}</h3>
                        
                        <!-- Stock info -->
                        <div class="flex items-center gap-2 sm:gap-4 text-xs sm:text-sm text-gray-500 mb-3 sm:mb-4 flex-wrap">
                            <span class="flex items-center gap-1 sm:gap-1.5"><i class="fas fa-box text-green-500 text-[10px] sm:text-xs"></i> <span class="hidden sm:inline">{{ $product->stock }} Disp.</span><span class="sm:hidden">{{ $product->stock }}</span></span>
                            <span class="flex items-center gap-1 sm:gap-1.5"><i class="fas fa-clock text-orange-500 text-[10px] sm:text-xs"></i> <span class="hidden sm:inline">{{ $product->reserva }} Res.</span><span class="sm:hidden">{{ $product->reserva }}</span></span>
                        </div>

                        <!-- Precio y Botones -->
                        <div class="mt-auto flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-0">
                            <div class="text-lg sm:text-2xl font-black text-gray-900">${{ number_format($product->precio_venta, 2) }}</div>
                            <div class="flex gap-1.5 sm:gap-2">
                                <button onclick="openModal(this)"
                                        data-name="{{ $product->nombre }}"
                                        data-price="${{ number_format($product->precio_venta, 2) }}"
                                        data-category="{{ $product->nombre_categoria ?? 'Sin Categoría' }}"
                                        data-sku="{{ $product->sku }}"
                                        data-stock="{{ $product->stock }} un."
                                        data-reserva="{{ $product->reserva }} un."
                                        data-image="{{ asset('assets/images/' . ($product->imagen ?? 'default.png')) }}"
                                        data-description="{{ $product->nombre_producto_padre ?? 'No hay descripción disponible.' }}"
                                        class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-colors shadow-sm text-xs sm:text-base" title="Vista rápida">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-600 text-white hover:bg-blue-700 shadow-md hover:shadow-lg hover:shadow-blue-600/30 flex items-center justify-center transition-all transform hover:-translate-y-1 text-xs sm:text-base" title="Añadir al carrito">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-10 text-gray-500">
                    No hay productos disponibles.
                </div>
                @endforelse
            </div>
            
            <div class="mt-12 text-center">
                <a href="#productos" class="inline-flex items-center justify-center px-8 py-3 border-2 border-gray-200 text-base font-semibold rounded-full text-gray-700 bg-white hover:border-blue-600 hover:text-blue-600 transition-colors">
                    Ver todos los productos <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>