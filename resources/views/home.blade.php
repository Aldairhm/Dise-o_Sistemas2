<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AXStore | Inicio</title>
    <!-- Dark mode init -->
    <script>
        (function() {
            var t = localStorage.getItem('ax_theme') || 'system';
            if (t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/home.css', 'resources/js/app.js','resources/css/app.css'])

</head>
<body class="bg-gray-50 text-gray-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Navbar-->
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 transition-all duration-300">
        @include('componentsHome.header')  
    </header>

    <!-- Hero Section -->
    <section class="relative bg-white overflow-hidden">
        @include('componentsHome.hero')  
    </section>

    <!-- Beneficios -->
    <section class="bg-gray-900 text-white py-16 sm:py-20 relative overflow-hidden">
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-gray-700 to-transparent"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-12 sm:mb-16">
                <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Compra en 3 pasos</h2>
                <p class="mt-4 text-lg text-gray-400 max-w-2xl mx-auto">Un proceso rápido y seguro para que disfrutes de tus accesorios.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-12 sm:gap-8 text-center">
                <div class="p-6 rounded-2xl bg-gray-800/50 border border-gray-700/50 hover:bg-gray-800 transition-colors duration-300 group">
                    <div class="w-20 h-20 mx-auto bg-blue-600/20 rounded-2xl flex items-center justify-center mb-6 transform group-hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center transform rotate-3 group-hover:rotate-6 transition-transform duration-300">
                            <i class="fas fa-shopping-bag text-2xl text-white transform -rotate-3 group-hover:-rotate-6 transition-transform duration-300"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">1. Haz tu pedido</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Elige tus productos favoritos de nuestro catálogo y agrégalos al carrito de compras.</p>
                </div>
                <div class="p-6 rounded-2xl bg-gray-800/50 border border-gray-700/50 hover:bg-gray-800 transition-colors duration-300 group">
                    <div class="w-20 h-20 mx-auto bg-blue-600/20 rounded-2xl flex items-center justify-center mb-6 transform group-hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center transform -rotate-3 group-hover:-rotate-6 transition-transform duration-300">
                            <i class="fas fa-truck-fast text-2xl text-white transform rotate-3 group-hover:rotate-6 transition-transform duration-300"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">2. Lo enviamos</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Preparamos tu paquete con seguridad y te contactamos para confirmar el envío.</p>
                </div>
                <div class="p-6 rounded-2xl bg-gray-800/50 border border-gray-700/50 hover:bg-gray-800 transition-colors duration-300 group sm:col-span-2 lg:col-span-1 sm:w-1/2 lg:w-full sm:mx-auto">
                    <div class="w-20 h-20 mx-auto bg-blue-600/20 rounded-2xl flex items-center justify-center mb-6 transform group-hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center transform rotate-3 group-hover:rotate-6 transition-transform duration-300">
                            <i class="fas fa-box-open text-2xl text-white transform -rotate-3 group-hover:-rotate-6 transition-transform duration-300"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">3. Recíbelo</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Recibe tu compra directamente en la puerta de tu casa o en un punto de encuentro.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Productos Section -->
    <section id="productos" class="py-20 bg-gray-50">
        @include('componentsHome.productSection')
    </section>

     <!-- Información de Envío -->
    <section id="envios" class="py-20 bg-white">
        @include('componentsHome.infoEnvios')
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-20 bg-gray-50 border-t border-gray-200">
        @include('componentsHome.preguntas')
    </section>

    <!-- Footer -->
    <footer id="contacto" class="bg-white border-t border-gray-200 pt-16 pb-8">
        @include('componentsHome.footer')
    </footer>

    <!-- Modal de Producto -->
    @include('componentsHome.modalViewProduct')


</body>
</html>