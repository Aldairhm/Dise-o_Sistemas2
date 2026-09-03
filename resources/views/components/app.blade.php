@props(['title' => 'AXStore ERP'])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title> 
    
    <!-- 1. Recuperamos la tipografía original (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- ESTA ES LA LÍNEA MÁGICA QUE FALTA -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @vite(['resources/css/home.css', 'resources/css/app.css', 'resources/js/app.js'])
</head>

<!-- 2. Aplicamos el mismo color de fondo (bg-gray-50) y forzamos la fuente Inter -->
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col" style="font-family: 'Inter', sans-serif;">
    
    <!-- 3. Recuperamos el contenedor original del Header (Fondo blanco, borde y blur) -->
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 transition-all duration-300">
        @include('componentsHome.header')
    </header>
    
    <main class="flex-grow p-6 w-full max-w-7xl mx-auto mt-4">
        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-gray-200 pt-16 pb-8 mt-auto">
        @include('componentsHome.footer')
    </footer>
    
</body>
</html>