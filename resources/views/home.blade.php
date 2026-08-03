<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0b5ee1">
    
    <!-- Íconos y Web App -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/logo.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">

    <title>AXStore - Online</title>
    
    <!-- CSS Assets -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/card.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/enviosHome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/headerstyle.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ss.css') }}" />
</head>

<body class="bg-light">
    <!-- HEADER REPLICADO -->
    <header id="header" class="header">
        <div class="branding d-flex align-items-center">
            <div class="container position-relative d-flex align-items-center justify-content-between">
                <a class="logo d-flex align-items-center" href="#">
                    <div class="logo-wrapper">
                        <h1 class="mb-0">AX<span>STORE</span></h1>
                        <p class="logo-tagline mb-0">Tu tienda online</p>
                    </div>
                </a>

                <nav id="navmenu" class="navmenu">
                    <ul>
                        <li><a href="#" class="active"><i class="fas fa-home me-1"></i>Inicio</a></li>
                        <li><a href="#"><i class="fas fa-shopping-bag me-1"></i>Productos</a></li>
                        <li><a href="#"><i class="fas fa-folder-open me-1"></i>Categorías</a></li>
                        <li><a href="#"><i class="fas fa-clipboard-list me-1"></i>Catálogo</a></li>
                        <li class="dropdown"><a href="#" class="text-primary fw-bold"><span>Variantes</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                            <ul>
                                <li><a href="/variante">Variante 2 (Clara)</a></li>
                                <li><a href="/variante-premium">Variante 3 (Luxury)</a></li>
                                <li><a href="/variante-apple">Variante 4 (Minimalista)</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>

                <div class="header-actions d-flex align-items-center gap-3">
                    <button class="mobile-nav-toggle" aria-label="Toggle navigation menu">
                        <i class="fas fa-bars"></i>
                    </button>

                    <!-- Icono Carrito (Estático) -->
                    <div class="cart-icon position-relative" role="button" tabindex="0" aria-label="Ver carrito">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count position-absolute">0</span>
                    </div>

                    <!-- Icono Usuario (Estático) -->
                    <div class="user-icon dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="fas fa-user-circle fa-lg"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <!-- HERO SECTION -->
        <section id="inicio" class="hero d-flex align-items-center justify-content-center text-center position-relative vh-100 text-white">
            <div class="hero-overlay position-absolute w-100 h-100"></div>
            <div class="container position-relative hero-content">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-8">
                        <h1 class="display-2 display-md-1 font-luxury mb-4 text-uppercase hero-title">
                            Todo lo que necesitas
                        </h1>
                        <p class="lead mb-5 hero-subtitle">
                            Variedad, calidad y confianza
                        </p>
                        <a href="#categorias" class="btn btn-outline-light btn-lg rounded-0 px-5 py-3 hero-button">
                            VER COLECCIÓN
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- CATÁLOGO -->
        <section class="catalog-header py-5 bg-white">
            <div class="container">
                <!-- Toolbar estático -->
                <div class="smart-toolbar-container mb-4">
                    <div class="smart-toolbar shadow-sm">
                        <div class="search-focus">
                            <i class="fas fa-search"></i>
                            <input type="text" id="product-search" placeholder="¿Qué estás buscando hoy?" disabled>
                        </div>
                        <div class="toolbar-divider d-none d-md-block"></div>
                        <div class="pill-select-wrapper d-none d-md-block">
                            <select id="category-filter" class="pill-select" disabled>
                                <option value="all">TODAS LAS CATEGORÍAS</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="categorias" class="category-segment-container mb-5">
                    <div class="d-flex align-items-center justify-content-between mb-3 px-2">
                        <h5 class="fw-bold mb-0 text-uppercase letter-spacing-1 small">Colecciones</h5>
                        <div class="category-divider-line flex-grow-1 ms-3"></div>
                    </div>
                    <div class="category-pill-wrapper" id="categories-container">
                        <div class="catalog-pill active" data-category="all">Todos los Productos</div>
                    </div>
                </div>

                <!-- SWIPER PRODUCTOS (BLOQUE ELOQUENT A BLADE) -->
                <div class="product-carousel-container mb-5">
                    <div class="swiper productSwiper">
                        <div class="swiper-wrapper" id="product-grid">
                            
                            @forelse($products as $product)
                                <div class="swiper-slide animate__animated animate__fadeIn">
                                    <div class="card h-100 border-0 shadow-sm transition-hover product-card">
                                        <div class="product-badge-container">
                                            @if($product->stock <= 0 && $product->reserva <= 0)
                                                <span class="badge-premium badge-low-stock"><i class="fas fa-times-circle me-1"></i> Agotado</span>
                                            @elseif($product->stock <= 0 && $product->reserva > 0)
                                                <span class="badge-premium badge-top"><i class="fas fa-truck-loading me-1"></i> Esperando bodega</span>
                                            @endif
                                            <span class="badge-premium badge-stock"><i class="fas fa-box me-1"></i>Tienda: {{ $product->stock }}</span>
                                            <span class="badge-premium badge-reserva"><i class="fas fa-clock me-1"></i>Bodega: {{ $product->reserva }}</span>
                                        </div>
                                        
                                        <div class="product-quick-actions">
                                            <button type="button" class="btn-action-premium border-0" style="background: transparent;" title="Vista Rápida"
                                                data-bs-toggle="modal" data-bs-target="#modalQuickView"
                                                data-name="{{ $product->nombre }}"
                                                data-price="${{ number_format($product->precio_venta, 2) }}"
                                                data-category="{{ $product->nombre_categoria ?? 'Sin Categoría' }}"
                                                data-sku="{{ $product->sku }}"
                                                data-stock="{{ $product->stock }} un."
                                                data-reserva="{{ $product->reserva }} un."
                                                data-image="{{ asset('assets/images/' . ($product->imagen ?? 'default.png')) }}"
                                                data-description="{{ $product->nombre_producto_padre ?? 'No hay descripción disponible.' }}"
                                                onclick="openQuickView(this)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="#" class="btn-action-premium" title="Descargar Ficha PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </div>
                                        
                                        <div class="product-image-container">
                                            <img src="{{ asset('assets/images/' . ($product->imagen ?? 'default.png')) }}" class="product-img-main" alt="{{ $product->nombre }}">
                                            <img src="{{ asset('assets/images/' . ($product->imagen_hover ?? $product->imagen ?? 'default.png')) }}" class="product-img-hover" alt="{{ $product->nombre }} hover">
                                        </div>
                                        
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span class="badge bg-light text-dark border">{{ $product->nombre_categoria ?? 'Sin Categoría' }}</span>
                                                <div class="text-end">
                                                    <span class="d-block x-small text-success fw-bold" style="font-size: 0.75rem;">Comisión: ${{ number_format($product->comision ?? 0, 2) }}</span>
                                                </div>
                                            </div>
                                            <p class="text-muted small mb-1">{{ $product->nombre_producto_padre ?? '' }}</p>
                                            <h5 class="card-title fw-bold text-dark mb-3">{{ $product->nombre }}</h5>
                                            <div class="mt-auto">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="h4 mb-0 text-primary fw-bold">${{ number_format($product->precio_venta, 2) }}</span>
                                                    <span class="small text-muted">SKU: {{ $product->sku }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <h3 class="fw-bold">No hay productos disponibles</h3>
                                </div>
                            @endforelse

                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="nosotros" class="about py-5 bg-luxury text-white">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-12">
                        <h2 class="section-title text-white">
                            Calidad que Marca la Diferencia
                        </h2>
                        <p class="text-white-50 mb-4">
                            En nuestra tienda seleccionamos cuidadosamente productos para el hogar, herramientas y accesorios de auto y moto que combinan funcionalidad, durabilidad y buen diseño. </p>
                        <p class="text-white-50">
                            Trabajamos con proveedores confiables para garantizar artículos de calidad que se adapten a tus necesidades del dia a dia.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECCIÓN DE ENVÍOS ACTUALIZADA -->
        <section class="shipping-info py-5 bg-white" id="envios">
            <div class="container">
                <!-- Título Principal -->
                <div class="text-center mb-5">
                    <h2 class="section-title mb-3">Información de Envíos</h2>
                    <p class="text-muted">Entregamos tu pedido de forma segura en toda El Salvador</p>
                </div>

                <!-- Cards de Tipos de Envío -->
                <div class="row g-4 mb-5">
                    <!-- Envío Local -->
                    <div class="col-lg-4">
                        <div class="shipping-card h-100 p-4 bg-light border-0 shadow-sm position-relative overflow-hidden">
                            <div class="shipping-icon-bg position-absolute opacity-10">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="position-relative">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="shipping-icon bg-luxury text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; flex-shrink: 0;">
                                        <i class="fas fa-map-marker-alt fa-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="h4 font-luxury mb-1">Envío Local</h3>
                                        <p class="text-muted small mb-0">San Salvador - Puntos Céntricos</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Costo de envío</span>
                                        <span class="h4 text-luxury mb-0 font-luxury">$3.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Tiempo de entrega</span>
                                        <span class="fw-semibold">Mismo día</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Horario</span>
                                        <span class="fw-semibold">8:00 AM - 4:00 PM</span>
                                    </div>
                                </div>

                                <div class="alert alert-info border-0 bg-white shadow-sm mb-3" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-info-circle text-info mt-1 me-2"></i>
                                        <small class="mb-0">
                                            <strong>Puntos de entrega:</strong> Gasolineras, Hospitales, Parques, Escuelas y Centros Comerciales.
                                        </small>
                                    </div>
                                </div>

                                <!-- Zonas de Entrega -->
                                <div class="zones-container mb-3">
                                    <p class="small fw-bold text-uppercase text-muted mb-2">Zonas de cobertura:</p>
                                    
                                    <div class="zone-detail mb-3 p-3 bg-white border">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-sun text-warning me-2"></i>
                                            <h5 class="h6 mb-0 font-luxury">Zona Occidente - Ruta Matutina</h5>
                                        </div>
                                        <p class="small text-muted mb-2">
                                            <i class="fas fa-map-pin me-1"></i>
                                            Santa Tecla, Merliot, Santa Elena, Escalón, San Marcos, Olímpica, Constitución, Centro S.S.
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center small">
                                            <span class="badge bg-luxury text-white">Entrega: Mañana</span>
                                            <span class="text-danger fw-bold">
                                                <i class="fas fa-clock me-1"></i>Cierre: 11:00 AM
                                            </span>
                                        </div>
                                    </div>

                                    <div class="zone-detail p-3 bg-white border">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-moon text-primary me-2"></i>
                                            <h5 class="h6 mb-0 font-luxury">Zona Oriente - Ruta Vespertina</h5>
                                        </div>
                                        <p class="small text-muted mb-2">
                                            <i class="fas fa-map-pin me-1"></i>
                                            Centro S.S., Mejicanos, Apopa, Soyapango, Ilopango, San Martín
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center small">
                                            <span class="badge bg-dark text-white">Entrega: Tarde</span>
                                            <span class="text-danger fw-bold">
                                                <i class="fas fa-clock me-1"></i>Cierre: 2:00 PM
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Envío Departamental -->
                    <div class="col-lg-4">
                        <div class="shipping-card h-100 p-4 bg-light border-0 shadow-sm position-relative overflow-hidden">
                            <div class="shipping-icon-bg position-absolute opacity-10">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="position-relative">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="shipping-icon bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; flex-shrink: 0;">
                                        <i class="fas fa-truck fa-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="h4 font-luxury mb-1">Envío Departamental</h3>
                                        <p class="text-muted small mb-0">Todo El Salvador - Puntos Céntricos</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Costo de envío</span>
                                        <span class="h4 text-luxury mb-0 font-luxury">$4.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Tiempo de entrega</span>
                                        <span class="fw-semibold">24 a 48 horas</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Horario</span>
                                        <span class="fw-semibold">8:00 AM - 4:00 PM</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Cierre pedidos</span>
                                        <span class="text-danger fw-bold">2:00 PM - 3:00 PM</span>
                                    </div>
                                </div>

                                <div class="alert alert-warning border-0 bg-white shadow-sm mb-3" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-exclamation-triangle text-warning mt-1 me-2"></i>
                                        <small class="mb-0">
                                            <strong>Importante:</strong> San Salvador Sur se considera envío departamental. Incluye puntos céntricos como gasolineras, hospitales, parques y plazas.
                                        </small>
                                    </div>
                                </div>

                                <div class="alert alert-danger border-0 bg-white shadow-sm mb-3" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-calendar-times text-danger mt-1 me-2"></i>
                                        <small class="mb-0">
                                            <strong>No laboramos domingos:</strong> Pedidos hechos sábados después de hora de cierre se entregan el martes.
                                        </small>
                                    </div>
                                </div>

                                <!-- Departamentos -->
                                <div class="departments-container">
                                    <p class="small fw-bold text-uppercase text-muted mb-2">Departamentos:</p>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Ahuachapán</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Santa Ana</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Sonsonate</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">La Libertad</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Chalatenango</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Cuscatlán</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">La Paz</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Cabañas</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">San Vicente</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Usulután</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">San Miguel</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Morazán</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">La Unión</span>
                                        <span class="badge bg-luxury text-white px-2 py-1 small">SS Sur</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NUEVO: Envío Personalizado a Domicilio -->
                    <div class="col-lg-4">
                        <div class="shipping-card h-100 p-4 bg-light border-0 shadow-sm position-relative overflow-hidden">
                            <div class="shipping-icon-bg position-absolute opacity-10">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="position-relative">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="shipping-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; flex-shrink: 0;">
                                        <i class="fas fa-home fa-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="h4 font-luxury mb-1">Envío Personalizado</h3>
                                        <p class="text-muted small mb-0">Entrega a Domicilio</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Costo de envío</span>
                                        <span class="h4 text-success mb-0 font-luxury">$5.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Cobertura</span>
                                        <span class="fw-semibold">Todo El Salvador</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Tiempo</span>
                                        <span class="fw-semibold">Mismo día / 24-48h</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Tipo de entrega</span>
                                        <span class="fw-semibold">Dirección exacta</span>
                                    </div>
                                </div>

                                <div class="alert alert-success border-0 bg-white shadow-sm mb-3" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-star text-success mt-1 me-2"></i>
                                        <small class="mb-0">
                                            <strong>Servicio premium:</strong> Entregamos hasta la puerta de tu casa tanto en San Salvador como en otros departamentos.
                                        </small>
                                    </div>
                                </div>

                                <div class="alert alert-warning border-0 bg-white shadow-sm mb-3" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-exclamation-circle text-warning mt-1 me-2"></i>
                                        <small class="mb-0">
                                            <strong>Restricción:</strong> No realizamos envíos a cantones por difícil acceso y limitaciones de encomienda.
                                        </small>
                                    </div>
                                </div>

                                <div class="alert alert-danger border-0 bg-white shadow-sm mb-0" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-calendar-times text-danger mt-1 me-2"></i>
                                        <small class="mb-0">
                                            <strong>Domingos:</strong> No realizamos ningún tipo de envío los domingos.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Proceso de Entrega -->
                <div class="delivery-process bg-luxury text-white p-5 mb-5">
                    <div class="row align-items-center">
                        <div class="col-lg-4 text-center text-lg-start mb-4 mb-lg-0">
                            <i class="fas fa-route fa-3x mb-3 text-gold"></i>
                            <h3 class="h4 font-luxury mb-2">Proceso de Entrega</h3>
                            <p class="text-white-50 small mb-0">Así llega tu pedido hasta ti</p>
                        </div>
                        <div class="col-lg-8">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-white bg-opacity-10 h-100">
                                        <div class="step-number bg-gold text-dark rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-2" style="width: 40px; height: 40px;">1</div>
                                        <h5 class="h6 font-luxury mb-1">Confirmas tu pedido</h5>
                                        <p class="text-white-50 small mb-0">Antes del horario de cierre</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-white bg-opacity-10 h-100">
                                        <div class="step-number bg-gold text-dark rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-2" style="width: 40px; height: 40px;">2</div>
                                        <h5 class="h6 font-luxury mb-1">Preparamos el envío</h5>
                                        <p class="text-white-50 small mb-0">Organizamos la ruta</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-white bg-opacity-10 h-100">
                                        <div class="step-number bg-gold text-dark rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-2" style="width: 40px; height: 40px;">3</div>
                                        <h5 class="h6 font-luxury mb-1">Lo entregamos</h5>
                                        <p class="text-white-50 small mb-0">En tu punto o domicilio</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preguntas Frecuentes -->
                <div class="faq-section">
                    <h3 class="h4 font-luxury text-center mb-4">Preguntas Frecuentes</h3>
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <div class="accordion accordion-flush" id="shippingFAQ">
                                <!-- FAQ 1 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Cuál es la diferencia entre los tres tipos de envío?
                                        </button>
                                    </h2>
                                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            <strong>Envío Local ($3):</strong> San Salvador, entrega en puntos céntricos el mismo día.<br>
                                            <strong>Envío Departamental ($4):</strong> Otros departamentos, entrega en puntos céntricos en 24-48h.<br>
                                            <strong>Envío Personalizado ($5):</strong> Entrega a domicilio en toda dirección exacta de El Salvador (excepto cantones).
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 2 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Por qué no hacen envíos a cantones?
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            Los cantones generalmente tienen difícil acceso y las empresas de encomienda no ingresan a estas zonas. Por seguridad y logística, solo entregamos en zonas urbanas y puntos céntricos accesibles.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 3 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Qué pasa si hago un pedido departamental el sábado?
                                        </button>
                                    </h2>
                                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            Si realizas tu pedido departamental el sábado antes de la hora de cierre, se envia para dia lunes. Si lo haces después de la hora de cierre pasa para dia martes, ya que no laboramos domingos.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 4 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Qué son "puntos céntricos" en envíos departamentales?
                                        </button>
                                    </h2>
                                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            Lugares de fácil acceso y conocidos como: gasolineras principales (Shell, Texaco, Puma), hospitales públicos, parques centrales, plazas comerciales, escuelas o colegios reconocidos. Te ayudamos a coordinar el punto más cercano a ti.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 5 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Hasta qué hora puedo hacer pedidos para envío local en San Salvador?
                                        </button>
                                    </h2>
                                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            Depende de tu zona:<br>
                                            <strong>Zona Occidente:</strong> Hasta las 11:00 AM<br>
                                            <strong>Zona Oriente:</strong> Hasta las 2:00 PM<br>
                                            Pedidos después de estos horarios se procesan para el día siguiente.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 6 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Me pueden confirmar la hora exacta de entrega?
                                        </button>
                                    </h2>
                                    <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            No manejamos horas exactas de entrega. Nuestro horario general es de 8:00 AM a 4:00 PM. Te contactamos cuando el pedido esté en camino para coordinar mejor.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 7 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿El envío personalizado a domicilio cubre todo El Salvador?
                                        </button>
                                    </h2>
                                    <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            Sí, el envío personalizado de $5.00 cubre todo El Salvador con entrega a domicilio, EXCEPTO cantones. Entregamos en zonas urbanas de todos los departamentos directamente en tu dirección.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 8 -->
                                <div class="accordion-item border-0 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Por qué San Salvador Sur es envío departamental?
                                        </button>
                                    </h2>
                                    <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            Por la distancia y logística de rutas, zonas como Planes de Renderos, Panchimalco, San Marcos y otras del sur se consideran envío departamental. El costo es de $4.00 en puntos céntricos o $5.00 a domicilio.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Final -->
                <div class="text-center mt-5 pt-4 border-top">
                    <h4 class="font-luxury mb-3">¿Tienes más preguntas?</h4>
                    <p class="text-muted mb-4">Estamos aquí para ayudarte</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="https://chat.whatsapp.com/JMjwo6P73evJ7vzYllO3Nm?mode=hq2tswa" class="btn btn-success btn-lg rounded-0 px-4" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>Contactar por WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Modal de Quick View (Vista Rápida) Shared -->
    <div class="modal fade modal-quickview" id="modalQuickView" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body p-0">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-index-10" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="row g-0">
                        <!-- Galería Izquierda -->
                        <div class="col-lg-7">
                            <div class="quickview-img-large text-center bg-light p-4 h-100 d-flex align-items-center justify-content-center">
                                <img id="qv-main-img" src="" alt="Vista Rápida" class="img-fluid" style="max-height: 500px; object-fit: contain;">
                            </div>
                        </div>
                        <!-- Información Derecha -->
                        <div class="col-lg-5 bg-white p-4 p-md-5">
                            <div class="mb-2">
                                <span id="qv-category" class="badge bg-light text-dark border">Categoría</span>
                                <span id="qv-sku" class="badge bg-dark ms-2">SKU</span>
                            </div>
                            <h2 id="qv-name" class="fw-bold mb-3">Nombre del Producto</h2>
                            <h3 id="qv-price" class="text-primary fw-bold mb-4">$0.00</h3>
                            
                            <div class="mb-4">
                                <h6 class="fw-bold text-muted small text-uppercase mb-2">Descripción</h6>
                                <p id="qv-description" class="text-muted lh-lg small">No hay descripción disponible.</p>
                            </div>

                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <div class="bg-light p-3 rounded d-flex align-items-center h-100">
                                        <i class="fas fa-warehouse text-success me-3 fs-4"></i>
                                        <div>
                                            <small class="text-muted d-block">Existencias</small>
                                            <strong id="qv-stock" class="fs-6">0 un.</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light p-3 rounded d-flex align-items-center h-100">
                                        <i class="fas fa-hand-holding-usd text-warning me-3 fs-4"></i>
                                        <div>
                                            <small class="text-muted d-block">Reserva</small>
                                            <strong id="qv-reserva" class="fs-6">0 un.</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kit de Contenido (Redes Sociales) -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark small text-uppercase mb-3 d-flex align-items-center">
                                    <i class="fas fa-bullhorn text-primary me-2"></i>Kit de Contenido
                                </h6>
                                <div class="position-relative">
                                    <textarea id="qv-copy-text" class="form-control bg-light border-0 small mb-2 p-3" rows="4" readonly style="font-size: 0.85rem; resize: none;"></textarea>
                                    <button id="btn-copy-info" onclick="copyKitContent()" class="btn btn-sm btn-dark position-absolute top-0 end-0 m-2 opacity-75 hover-opacity-100" title="Copiar al portapapeles">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <div class="d-grid mt-2">
                                    <a id="btn-download-img" href="#" download="imagen.jpg" class="btn btn-outline-primary btn-sm rounded-pill py-2 fw-bold">
                                        <i class="fas fa-image me-2"></i>DESCARGAR IMAGEN ACTUAL
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer id="contacto">
        <div class="container">
            <div class="footer-section">
                <h3>Contacto</h3>
                <p><i class="fas fa-map-marker-alt"></i>Metro Centró San Salvador. Sobre la calle Los Sisimiles, local 3-5A 3er nivel</p>
                <p><i class="fas fa-phone"></i> +503 7888-7889</p>
            </div>
            <div class="footer-section">
                <h3>Horario</h3>
                <p>Lunes - Sabado: 8am - 4pm</p>
                <p>Domingo: Cerrado</p>
            </div>
            <div class="footer-section">
                <h3>Síguenos</h3>
                <div class="social-icons">
                    <a href="https://www.instagram.com/ax_storesv" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.facebook.com/kidsparadise2024?locale=es_LA" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook"></i></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2026 AXStore. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- SCRIPTS MÍNIMOS REQUERIDOS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicialización básica del carrusel estático
            new Swiper(".productSwiper", {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: false,
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                    dynamicBullets: true
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                breakpoints: {
                    576: { slidesPerView: 2 },
                    992: { slidesPerView: 3 },
                    1200: { slidesPerView: 4 },
                }
            });
        });

        // Quick View Function
        function openQuickView(btn) {
            document.getElementById('qv-name').innerText = btn.getAttribute('data-name');
            document.getElementById('qv-price').innerText = btn.getAttribute('data-price');
            document.getElementById('qv-category').innerText = btn.getAttribute('data-category');
            document.getElementById('qv-sku').innerText = 'SKU: ' + btn.getAttribute('data-sku');
            document.getElementById('qv-stock').innerText = btn.getAttribute('data-stock');
            document.getElementById('qv-reserva').innerText = btn.getAttribute('data-reserva');
            document.getElementById('qv-main-img').src = btn.getAttribute('data-image');
            document.getElementById('qv-description').innerText = btn.getAttribute('data-description');
            
            // Set Download Link
            const downloadBtn = document.getElementById('btn-download-img');
            downloadBtn.href = btn.getAttribute('data-image');
            downloadBtn.download = btn.getAttribute('data-sku') + '.jpg';

            // Kit de contenido
            let copyText = `*${btn.getAttribute('data-name')}*\n` +
                           `Categoría: ${btn.getAttribute('data-category')}\n` +
                           `PRECIO: ${btn.getAttribute('data-price')}\n` +
                           `SKU: ${btn.getAttribute('data-sku')}`;
            document.getElementById('qv-copy-text').value = copyText;
        }

        // Copy Kit Content
        function copyKitContent() {
            const copyText = document.getElementById('qv-copy-text');
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            navigator.clipboard.writeText(copyText.value).then(() => {
                alert("¡Texto copiado!");
            });
        }
    </script>
</body>
</html>
