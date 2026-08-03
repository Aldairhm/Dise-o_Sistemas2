<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AXStore - Nueva Variante</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Navbar -->
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="/variante" class="flex items-center gap-3 group">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="AXStore Logo" class="h-12 w-auto object-contain">
                    <div class="hidden sm:flex flex-col">
                        <span class="text-3xl font-black tracking-tighter text-gray-900 group-hover:text-blue-600 transition-colors">
                            AX<span class="text-blue-600">STORE</span>
                        </span>
                        <span class="text-[10px] uppercase tracking-widest text-gray-500 font-semibold mt-[-4px]">Tu tienda online</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="/variante" class="text-sm font-semibold text-blue-600 flex items-center gap-2"><i class="fas fa-home"></i> Inicio</a>
                    <a href="#" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors flex items-center gap-2"><i class="fas fa-shopping-bag"></i> Productos</a>
                    <a href="#" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors flex items-center gap-2"><i class="fas fa-folder-open"></i> Categorías</a>
                    <a href="/" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors flex items-center gap-2"><i class="fas fa-arrow-left"></i> Original</a>
                    
                    <!-- Dropdown de variantes -->
                    <div class="relative group">
                        <button class="text-sm font-bold text-blue-600 flex items-center gap-2">
                            <span>Variantes</span>
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </button>
                        <div class="absolute top-full left-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 flex flex-col py-2">
                            <a href="/variante" class="px-4 py-2 text-sm text-blue-600 font-bold bg-blue-50">Variante 2 (Clara)</a>
                            <a href="/variante-premium" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors">Variante 3 (Luxury)</a>
                            <a href="/variante-apple" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors">Variante 4 (Minimalista)</a>
                        </div>
                    </div>
                </nav>

                <!-- Icons -->
                <div class="flex items-center gap-4">
                    <button class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold h-4 w-4 rounded-full flex items-center justify-center">0</span>
                    </button>
                    <button class="p-2 text-gray-600 hover:text-blue-600 transition-colors hidden sm:block">
                        <i class="fas fa-user-circle text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32 pt-10 sm:pt-16 lg:pt-20 px-4 sm:px-6 lg:px-8">
                <main class="mx-auto max-w-7xl">
                    <div class="sm:text-center lg:text-left">
                        <span class="inline-block py-1 px-3 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold uppercase tracking-wider mb-4 border border-blue-100">
                            Nueva Colección 2026
                        </span>
                        <h1 class="text-4xl tracking-tight font-black text-gray-900 sm:text-5xl md:text-6xl mb-6">
                            <span class="block xl:inline">Calidad que marca la</span>
                            <span class="block text-blue-600 xl:inline">Diferencia.</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Descubre nuestra nueva línea de accesorios con diseños modernos, materiales de primera calidad y envíos a todo El Salvador.
                        </p>
                        <div class="mt-8 sm:mt-12 sm:flex sm:justify-center lg:justify-start gap-4">
                            <a href="#productos" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-white bg-blue-600 hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 md:py-4 md:text-lg sm:w-auto">
                                Ver Catálogo
                            </a>
                            <a href="#contacto" class="mt-3 w-full flex items-center justify-center px-8 py-3 border border-gray-300 text-base font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 md:py-4 md:text-lg sm:mt-0 sm:w-auto">
                                Contáctanos
                            </a>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 bg-blue-50/30 flex items-center justify-center p-10 lg:p-20">
            <img class="max-h-64 sm:max-h-80 md:max-h-96 lg:max-h-full object-contain drop-shadow-2xl hover:scale-105 transition-transform duration-700" src="{{ asset('assets/images/logo.png') }}" alt="AXStore Logo">
        </div>
    </section>

    <!-- Beneficios (Process) -->
    <section class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="p-4">
                    <div class="w-16 h-16 mx-auto bg-blue-600 rounded-2xl flex items-center justify-center mb-4 transform rotate-3">
                        <i class="fas fa-shopping-bag text-2xl text-white transform -rotate-3"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">1. Haz tu pedido</h3>
                    <p class="text-gray-400 text-sm">Elige tus productos favoritos y agrégalos al carrito de compras.</p>
                </div>
                <div class="p-4">
                    <div class="w-16 h-16 mx-auto bg-blue-600 rounded-2xl flex items-center justify-center mb-4 transform -rotate-3">
                        <i class="fas fa-truck-fast text-2xl text-white transform rotate-3"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">2. Lo enviamos</h3>
                    <p class="text-gray-400 text-sm">Preparamos tu paquete con seguridad y te contactamos.</p>
                </div>
                <div class="p-4">
                    <div class="w-16 h-16 mx-auto bg-blue-600 rounded-2xl flex items-center justify-center mb-4 transform rotate-3">
                        <i class="fas fa-box-open text-2xl text-white transform -rotate-3"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">3. Recíbelo</h3>
                    <p class="text-gray-400 text-sm">Recibe en la puerta de tu casa o punto de encuentro.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Productos Section -->
    <section id="productos" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-black text-gray-900 sm:text-4xl">Productos Destacados</h2>
                <p class="mt-4 text-xl text-gray-500">Encuentra los mejores accesorios al mejor precio.</p>
                <div class="w-24 h-1 bg-blue-600 mx-auto mt-6 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($products as $product)
                <!-- Tarjeta de Producto -->
                <div class="group bg-white rounded-3xl p-5 border border-gray-100 hover:border-blue-100 shadow-sm hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 relative flex flex-col">
                    
                    <!-- Imagen -->
                    <div class="relative rounded-2xl overflow-hidden bg-gray-50 mb-4 aspect-square flex items-center justify-center group-hover:bg-blue-50 transition-colors">
                        <img src="{{ asset('assets/images/' . ($product->imagen ?? 'default.png')) }}" alt="{{ $product->nombre }}" class="w-3/4 h-3/4 object-contain group-hover:scale-110 transition-transform duration-500">
                        
                        <!-- Badges -->
                        <div class="absolute top-3 left-3 flex flex-col gap-2">
                            <span class="bg-white/90 backdrop-blur text-xs font-bold px-3 py-1 rounded-full text-gray-800 shadow-sm">SKU: {{ $product->sku }}</span>
                        </div>
                    </div>

                    <!-- Detalles -->
                    <div class="flex flex-col flex-grow">
                        <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider mb-1">{{ $product->nombre_categoria ?? 'Accesorio' }}</span>
                        <h3 class="text-gray-900 font-bold text-lg leading-tight mb-2 line-clamp-2">{{ $product->nombre }}</h3>
                        
                        <!-- Stock info -->
                        <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                            <span class="flex items-center gap-1.5"><i class="fas fa-box text-green-500"></i> {{ $product->stock }} Disp.</span>
                            <span class="flex items-center gap-1.5"><i class="fas fa-clock text-orange-500"></i> {{ $product->reserva }} Res.</span>
                        </div>

                        <!-- Precio y Botones -->
                        <div class="mt-auto flex items-center justify-between">
                            <div class="text-2xl font-black text-gray-900">${{ number_format($product->precio_venta, 2) }}</div>
                            <div class="flex gap-2">
                                <button onclick="openVariantModal(this)"
                                        data-name="{{ $product->nombre }}"
                                        data-price="${{ number_format($product->precio_venta, 2) }}"
                                        data-category="{{ $product->nombre_categoria ?? 'Sin Categoría' }}"
                                        data-sku="{{ $product->sku }}"
                                        data-stock="{{ $product->stock }} un."
                                        data-reserva="{{ $product->reserva }} un."
                                        data-image="{{ asset('assets/images/' . ($product->imagen ?? 'default.png')) }}"
                                        data-description="{{ $product->nombre_producto_padre ?? 'No hay descripción disponible.' }}"
                                        class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-colors shadow-sm" title="Vista rápida">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="w-10 h-10 rounded-full bg-blue-600 text-white hover:bg-blue-700 shadow-md hover:shadow-lg hover:shadow-blue-600/30 flex items-center justify-center transition-all transform hover:-translate-y-1" title="Añadir al carrito">
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
                <a href="#" class="inline-flex items-center justify-center px-8 py-3 border-2 border-gray-200 text-base font-semibold rounded-full text-gray-700 bg-white hover:border-blue-600 hover:text-blue-600 transition-colors">
                    Ver todos los productos <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Información de Envío -->
    <section id="envios" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-black text-gray-900 sm:text-4xl">Información de Envíos</h2>
                <p class="mt-4 text-xl text-gray-500">Entregamos tu pedido de forma segura en todo El Salvador</p>
                <div class="w-24 h-1 bg-blue-600 mx-auto mt-6 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Local -->
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 hover:border-blue-200 transition-colors shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 text-gray-100 opacity-50 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-map-marker-alt text-9xl"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg shadow-blue-200">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Envío Local</h3>
                        <p class="text-gray-500 mb-6">San Salvador - Puntos Céntricos</p>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-gray-600">Costo</span>
                                <span class="text-2xl font-black text-blue-600">$3.00</span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-gray-600">Tiempo</span>
                                <span class="font-bold text-gray-900">Mismo día</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Horario</span>
                                <span class="font-bold text-gray-900">8:00 AM - 4:00 PM</span>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                                <p class="text-sm text-blue-800"><span class="font-bold">Puntos:</span> Gasolineras, Hospitales, Parques, Escuelas y Centros Comerciales.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Departamental -->
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 hover:border-blue-200 transition-colors shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 text-gray-100 opacity-50 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-truck text-9xl"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gray-900 text-white rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg shadow-gray-200">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Envío Departamental</h3>
                        <p class="text-gray-500 mb-6">Todo El Salvador - Puntos Céntricos</p>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-gray-600">Costo</span>
                                <span class="text-2xl font-black text-gray-900">$4.00</span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-gray-600">Tiempo</span>
                                <span class="font-bold text-gray-900">24 a 48 horas</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Cierre</span>
                                <span class="font-bold text-red-500">2:00 PM - 3:00 PM</span>
                            </div>
                        </div>

                        <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-exclamation-triangle text-orange-500 mt-1"></i>
                                <p class="text-sm text-orange-800"><span class="font-bold">Importante:</span> San Salvador Sur se considera envío departamental.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personalizado -->
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 hover:border-green-200 transition-colors shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 text-gray-100 opacity-50 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-home text-9xl"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-green-500 text-white rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg shadow-green-200">
                            <i class="fas fa-home"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Personalizado</h3>
                        <p class="text-gray-500 mb-6">Entrega a Domicilio Exacto</p>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-gray-600">Costo</span>
                                <span class="text-2xl font-black text-green-600">$5.00</span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-gray-600">Cobertura</span>
                                <span class="font-bold text-gray-900">Todo el país*</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Tipo</span>
                                <span class="font-bold text-gray-900">Puerta a puerta</span>
                            </div>
                        </div>

                        <div class="bg-green-50 border border-green-100 rounded-xl p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-star text-green-500 mt-1"></i>
                                <p class="text-sm text-green-800"><span class="font-bold">Premium:</span> Entregamos hasta la puerta de tu casa.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-20 bg-gray-50 border-t border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-black text-gray-900">Preguntas Frecuentes</h2>
                <div class="w-16 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
            </div>
            
            <div class="space-y-4">
                <!-- Pregunta 1 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden transition-all duration-300 shadow-sm hover:border-blue-300">
                    <button class="faq-button w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none" onclick="toggleFaq(this)">
                        <span class="font-bold text-gray-900">¿Cuál es la diferencia entre los tipos de envío?</span>
                        <i class="fas fa-chevron-down text-blue-600 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-4 text-gray-600 text-sm border-t border-gray-100 pt-4">
                        <p class="mb-2"><strong>Envío Local ($3):</strong> San Salvador, entrega en puntos céntricos el mismo día.</p>
                        <p class="mb-2"><strong>Envío Departamental ($4):</strong> Otros departamentos, entrega en puntos céntricos en 24-48h.</p>
                        <p><strong>Envío Personalizado ($5):</strong> Entrega a domicilio en toda dirección exacta de El Salvador (excepto cantones).</p>
                    </div>
                </div>

                <!-- Pregunta 2 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden transition-all duration-300 shadow-sm hover:border-blue-300">
                    <button class="faq-button w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none" onclick="toggleFaq(this)">
                        <span class="font-bold text-gray-900">¿Por qué no hacen envíos a cantones?</span>
                        <i class="fas fa-chevron-down text-blue-600 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-4 text-gray-600 text-sm border-t border-gray-100 pt-4">
                        <p>Los cantones generalmente tienen difícil acceso y las empresas de encomienda no ingresan a estas zonas. Por seguridad y logística, solo entregamos en zonas urbanas y puntos céntricos accesibles.</p>
                    </div>
                </div>

                <!-- Pregunta 3 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden transition-all duration-300 shadow-sm hover:border-blue-300">
                    <button class="faq-button w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none" onclick="toggleFaq(this)">
                        <span class="font-bold text-gray-900">¿Qué pasa si hago un pedido departamental el sábado?</span>
                        <i class="fas fa-chevron-down text-blue-600 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-4 text-gray-600 text-sm border-t border-gray-100 pt-4">
                        <p>Si realizas tu pedido departamental el sábado antes de la hora de cierre, se envia para dia lunes. Si lo haces después de la hora de cierre pasa para dia martes, ya que no laboramos domingos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contacto" class="bg-white border-t border-gray-200 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                    <span class="text-2xl font-black tracking-tighter text-gray-900 mb-4 block">
                        AX<span class="text-blue-600">STORE</span>
                    </span>
                    <p class="text-gray-500 mb-6 max-w-sm">
                        La mejor tienda online de accesorios en El Salvador. Calidad, garantía y envíos seguros a nivel nacional.
                    </p>
                    <div class="flex gap-4">
                        <a href="https://www.facebook.com/kidsparadise2024?locale=es_LA" target="_blank" class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/ax_storesv" target="_blank" class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-colors"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-bold text-gray-900 mb-4">Contacto</h4>
                    <ul class="space-y-3 text-gray-500 text-sm">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-blue-600"></i>
                            <span>Metrocentro San Salvador.<br>Local 3-5A, 3er nivel</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-blue-600"></i>
                            <span>+503 7888-7889</span>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold text-gray-900 mb-4">Horarios</h4>
                    <ul class="space-y-3 text-gray-500 text-sm">
                        <li class="flex items-center justify-between">
                            <span>Lunes - Sábado</span>
                            <span class="font-medium text-gray-900">8:00 AM - 4:00 PM</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Domingo</span>
                            <span class="font-medium text-red-500">Cerrado</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-400">&copy; 2026 AXStore. Todos los derechos reservados.</p>
                <div class="flex gap-4 text-sm text-gray-400">
                    <a href="#" class="hover:text-blue-600">Políticas de Privacidad</a>
                    <a href="#" class="hover:text-blue-600">Términos de Servicio</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modal Tailwind (Rediseño Total - Light Mode) -->
    <div id="variantModal" class="fixed inset-0 z-[100] hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeVariantModal()"></div>
        
        <!-- Modal Panel -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Contenedor Light Mode Minimalista -->
            <div class="relative bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl w-full border border-gray-100 flex flex-col md:flex-row">
                
                <!-- Botón Cerrar Flotante -->
                <button onclick="closeVariantModal()" class="absolute top-5 right-5 w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-gray-800 transition-all z-20 shadow-sm">
                    <i class="fas fa-times"></i>
                </button>

                <!-- Panel Izquierdo (Imagen) con fondo muy suave -->
                <div class="md:w-1/2 bg-gray-50/50 p-10 flex items-center justify-center min-h-[300px] relative overflow-hidden group border-r border-gray-100">
                    <div class="absolute inset-0 bg-blue-50 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <img id="v-modal-img" src="" alt="Product" class="max-h-[350px] object-contain drop-shadow-xl transform group-hover:scale-105 transition-transform duration-700 z-10 relative">
                </div>

                <!-- Panel Derecho (Info Light) -->
                <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-white">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span id="v-modal-category" class="text-blue-600 text-[10px] font-black uppercase tracking-widest mb-2 block">Categoría</span>
                            <h2 id="v-modal-name" class="text-3xl font-black text-gray-900 leading-tight">Nombre</h2>
                        </div>
                        <span id="v-modal-sku" class="bg-gray-100 text-gray-500 text-[10px] font-bold px-3 py-1.5 rounded-full border border-gray-200">SKU</span>
                    </div>
                    
                    <div id="v-modal-price" class="text-4xl font-light text-gray-900 mb-6 flex items-center">
                        <span class="text-blue-600 font-bold mr-1">$</span><span id="v-modal-price-val">0.00</span>
                    </div>
                    
                    <p id="v-modal-description" class="text-gray-500 mb-8 leading-relaxed text-sm">Descripción</p>

                    <!-- Tarjetas de Stock Estilo Minimalista -->
                    <div class="flex gap-4 mb-10">
                        <div class="flex-1 bg-white rounded-2xl p-4 border border-gray-100 shadow-sm shadow-gray-100/50 flex flex-col items-center justify-center text-center group hover:border-green-200 transition-colors">
                            <i class="fas fa-box text-green-500 mb-2 text-xl group-hover:scale-110 transition-transform"></i>
                            <p id="v-modal-stock" class="text-xl font-black text-gray-800">0</p>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Disp.</p>
                        </div>
                        <div class="flex-1 bg-white rounded-2xl p-4 border border-gray-100 shadow-sm shadow-gray-100/50 flex flex-col items-center justify-center text-center group hover:border-orange-200 transition-colors">
                            <i class="fas fa-clock text-orange-500 mb-2 text-xl group-hover:scale-110 transition-transform"></i>
                            <p id="v-modal-reserva" class="text-xl font-black text-gray-800">0</p>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Reserva</p>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex gap-3 mt-auto">
                        <button onclick="copyVariantKit()" class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold py-4 px-6 rounded-2xl transition-colors flex items-center justify-center gap-3 border border-gray-200 hover:border-gray-300">
                            <i class="fas fa-copy text-gray-400"></i> 
                            Copiar Info
                        </button>
                        <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-blue-600/30 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3">
                            <i class="fas fa-shopping-cart"></i>
                            Al Carrito
                        </button>
                    </div>
                    
                    <!-- Textarea oculta para poder copiar -->
                    <textarea id="v-modal-copy" class="hidden"></textarea>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleFaq(btn) {
            const content = btn.nextElementSibling;
            const icon = btn.querySelector('i');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        function openVariantModal(btn) {
            document.getElementById('v-modal-name').innerText = btn.getAttribute('data-name');
            // Quitamos el signo de dólar que venía del data-price para ponerlo en el HTML por separado
            let rawPrice = btn.getAttribute('data-price');
            document.getElementById('v-modal-price-val').innerText = rawPrice.replace('$', '');
            document.getElementById('v-modal-category').innerText = btn.getAttribute('data-category');
            document.getElementById('v-modal-sku').innerText = 'SKU: ' + btn.getAttribute('data-sku');
            document.getElementById('v-modal-stock').innerText = btn.getAttribute('data-stock').replace(' un.', '');
            document.getElementById('v-modal-reserva').innerText = btn.getAttribute('data-reserva').replace(' un.', '');
            document.getElementById('v-modal-img').src = btn.getAttribute('data-image');
            document.getElementById('v-modal-description').innerText = btn.getAttribute('data-description');
            
            let copyText = `*${btn.getAttribute('data-name')}*\n` +
                           `Categoría: ${btn.getAttribute('data-category')}\n` +
                           `PRECIO: ${rawPrice}\n` +
                           `SKU: ${btn.getAttribute('data-sku')}`;
            document.getElementById('v-modal-copy').value = copyText;
            
            document.getElementById('variantModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeVariantModal() {
            document.getElementById('variantModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function copyVariantKit() {
            const copyText = document.getElementById('v-modal-copy');
            // Como el textarea está hidden, navigator.clipboard funciona pero copyText.select() no.
            navigator.clipboard.writeText(copyText.value).then(() => {
                alert("¡Kit de Contenido copiado exitosamente al portapapeles!");
            });
        }
    </script>
</body>
</html>
