<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AXStore — Diseño Minimalista</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }

        /* Navbar blur estilo iOS */
        .glass-nav {
            background: rgba(242,242,247,0.82);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
        }

        /* Marquee */
        .marquee-track {
            display: flex; gap: 60px; white-space: nowrap;
            animation: marquee 22s linear infinite;
        }
        @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }

        /* Dropdown */
        .apple-dropdown { position: relative; }
        .apple-dropdown-menu {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 200px; background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px); border: 1px solid rgba(0,0,0,0.06);
            border-radius: 16px; box-shadow: 0 8px 40px rgba(0,0,0,0.12);
            overflow: hidden; opacity: 0; visibility: hidden;
            transform: translateY(-8px); transition: all 0.25s ease;
        }
        .apple-dropdown:hover .apple-dropdown-menu {
            opacity: 1; visibility: visible; transform: translateY(0);
        }
        .apple-dropdown-menu a {
            display: block; padding: 11px 16px;
            font-size: 12px; color: #3A3A3C; text-decoration: none;
            transition: background 0.15s;
        }
        .apple-dropdown-menu a:hover { background: rgba(0,0,0,0.04); }
        .apple-dropdown-menu a.current { color: #007AFF; font-weight: 600; }

        /* FAQ accordion */
        .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }
        .faq-answer.open { max-height: 200px; }
        .faq-chevron { transition: transform 0.35s, color 0.2s; }
        .faq-btn.open .faq-chevron { transform: rotate(180deg); color: #007AFF; }

        /* Modal */
        #appleModal { display: none; }
        #appleModal.open { display: flex; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 antialiased overflow-x-hidden" style="background:#F2F2F7;">

    <!-- ══════════ HEADER ══════════ -->
    <header class="glass-nav fixed w-full top-0 z-50" style="border-bottom:1px solid rgba(0,0,0,0.07); height:52px; display:flex; align-items:center;">
        <div class="max-w-6xl mx-auto px-6 w-full flex items-center justify-between">

            <!-- Logo -->
            <a href="/variante-apple" class="no-underline" style="font-size:18px; font-weight:700; letter-spacing:-0.5px; color:#1C1C1E;">
                AX<span style="color:#007AFF;">Store</span>
            </a>

            <!-- Centro: Nav Links -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="/variante-apple" style="font-size:12px; color:#3A3A3C; text-decoration:none; transition:color 0.2s;" class="hover:text-blue-600">Inicio</a>
                <a href="#productos" style="font-size:12px; color:#3A3A3C; text-decoration:none; transition:color 0.2s;" class="hover:text-blue-600">Productos</a>
                <a href="#envios" style="font-size:12px; color:#3A3A3C; text-decoration:none; transition:color 0.2s;" class="hover:text-blue-600">Envíos</a>
                <div class="apple-dropdown">
                    <button style="font-size:12px; font-weight:500; color:#007AFF; background:none; border:none; cursor:pointer; display:flex; align-items:center; gap:4px;">
                        Versiones <i class="fas fa-chevron-down" style="font-size:9px;"></i>
                    </button>
                    <div class="apple-dropdown-menu">
                        <a href="/">1. Original</a>
                        <a href="/variante">2. Clara</a>
                        <a href="/variante-premium">3. Luxury Oscura</a>
                        <a href="/variante-apple" class="current">4. Minimalista (Actual)</a>
                    </div>
                </div>
            </nav>

            <!-- Iconos -->
            <div class="flex items-center gap-5">
                <button style="background:none; border:none; cursor:pointer; font-size:13px; color:#3A3A3C; transition:color 0.2s;" class="hover:text-blue-600"><i class="fas fa-search"></i></button>
                <button style="background:none; border:none; cursor:pointer; font-size:13px; color:#3A3A3C; transition:color 0.2s;" class="hover:text-blue-600"><i class="fas fa-shopping-bag"></i></button>
            </div>
        </div>
    </header>

    <!-- ══════════ HERO ══════════ -->
    <section style="padding-top:52px; background:#FFFFFF;">
        <div class="max-w-6xl mx-auto px-6 py-20 grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            <!-- Texto -->
            <div>
                <span class="block mb-5" style="font-size:11px; font-weight:600; letter-spacing:0.06em; text-transform:uppercase; color:#007AFF;">Nueva Colección · 2026</span>
                <h1 style="font-size:clamp(44px,7vw,76px); font-weight:800; letter-spacing:-2.5px; line-height:0.92; color:#1C1C1E; margin-bottom:24px;">
                    Accesorios<br><span style="font-weight:200; color:#8E8E93;">para brillar.</span>
                </h1>
                <p style="font-size:17px; font-weight:300; color:#8E8E93; line-height:1.65; max-width:400px; margin-bottom:36px;">
                    Diseño sin igual, calidad que se siente. Descubre piezas únicas pensadas para destacar.
                </p>
                <div class="flex gap-3 flex-wrap">
                    <a href="#productos" style="background:#007AFF; color:#fff; padding:14px 28px; border-radius:980px; font-size:14px; font-weight:600; text-decoration:none; transition:all 0.25s;" class="hover:bg-blue-700">Comprar ahora</a>
                    <a href="#envios" style="color:#007AFF; font-size:14px; font-weight:500; text-decoration:none; padding:14px 0; display:flex; align-items:center; gap:6px; transition:opacity 0.2s;" class="hover:opacity-60">
                        Conocer más <i class="fas fa-chevron-right" style="font-size:10px;"></i>
                    </a>
                </div>
            </div>
            <!-- Logo -->
            <div class="flex items-center justify-center">
                <img src="{{ asset('assets/images/logo.png') }}" alt="AXStore"
                    style="max-width:360px; width:100%; object-fit:contain; filter:drop-shadow(0 24px 48px rgba(0,0,0,0.12)); transition:transform 0.5s ease;"
                    onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
            </div>
        </div>
    </section>

    <!-- ══════════ MARQUEE ══════════ -->
    <div style="background:#007AFF; padding:13px 0; overflow:hidden;">
        <div class="marquee-track">
            <span style="font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:rgba(255,255,255,0.8); flex-shrink:0;">Nueva Colección</span>
            <span style="color:rgba(255,255,255,0.35); font-size:6px; flex-shrink:0;">●</span>
            <span style="font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:rgba(255,255,255,0.8); flex-shrink:0;">Envío Nacional</span>
            <span style="color:rgba(255,255,255,0.35); font-size:6px; flex-shrink:0;">●</span>
            <span style="font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:rgba(255,255,255,0.8); flex-shrink:0;">Calidad Garantizada</span>
            <span style="color:rgba(255,255,255,0.35); font-size:6px; flex-shrink:0;">●</span>
            <span style="font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:rgba(255,255,255,0.8); flex-shrink:0;">Accesorios Premium</span>
            <span style="color:rgba(255,255,255,0.35); font-size:6px; flex-shrink:0;">●</span>
            <span style="font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:rgba(255,255,255,0.8); flex-shrink:0;">AXStore 2026</span>
            <span style="color:rgba(255,255,255,0.35); font-size:6px; flex-shrink:0;">●</span>
            <!-- Duplicado para loop continuo -->
            <span style="font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:rgba(255,255,255,0.8); flex-shrink:0;">Nueva Colección</span>
            <span style="color:rgba(255,255,255,0.35); font-size:6px; flex-shrink:0;">●</span>
            <span style="font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:rgba(255,255,255,0.8); flex-shrink:0;">Envío Nacional</span>
            <span style="color:rgba(255,255,255,0.35); font-size:6px; flex-shrink:0;">●</span>
            <span style="font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:rgba(255,255,255,0.8); flex-shrink:0;">Calidad Garantizada</span>
            <span style="color:rgba(255,255,255,0.35); font-size:6px; flex-shrink:0;">●</span>
            <span style="font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:rgba(255,255,255,0.8); flex-shrink:0;">Accesorios Premium</span>
            <span style="color:rgba(255,255,255,0.35); font-size:6px; flex-shrink:0;">●</span>
            <span style="font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:rgba(255,255,255,0.8); flex-shrink:0;">AXStore 2026</span>
        </div>
    </div>

    <!-- ══════════ PRODUCTOS ══════════ -->
    <section id="productos" style="background:#F2F2F7; padding:80px 0;">
        <div class="max-w-6xl mx-auto px-6">
            <!-- Header -->
            <div class="flex justify-between items-end mb-10">
                <h2 style="font-size:28px; font-weight:700; letter-spacing:-0.5px; color:#1C1C1E;">
                    Productos <span style="font-weight:200; color:#8E8E93;">destacados.</span>
                </h2>
                <a href="#" style="font-size:13px; font-weight:500; color:#007AFF; text-decoration:none;">Ver todos <i class="fas fa-arrow-right" style="font-size:11px;"></i></a>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($products as $product)
                <div class="cursor-pointer overflow-hidden"
                    style="background:#FFFFFF; border-radius:24px; box-shadow:0 2px 12px rgba(0,0,0,0.05); transition:box-shadow 0.3s, transform 0.3s;"
                    onclick="openAppleModal(this)"
                    onmouseover="this.style.boxShadow='0 16px 40px rgba(0,0,0,0.12)'; this.style.transform='translateY(-4px)'"
                    onmouseout="this.style.boxShadow='0 2px 12px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'"
                    data-name="{{ $product->nombre }}"
                    data-price="${{ number_format($product->precio_venta, 2) }}"
                    data-image="{{ asset('assets/images/' . ($product->imagen ?? 'default.png')) }}"
                    data-sku="{{ $product->sku }}"
                    data-stock="{{ $product->stock }}"
                    data-reserva="{{ $product->reserva }}"
                    data-category="{{ $product->nombre_categoria ?? 'General' }}"
                    data-description="{{ $product->nombre_producto_padre ?? 'Descripción no disponible.' }}">

                    <!-- Imagen -->
                    <div style="background:#F2F2F7; aspect-ratio:1/1; display:flex; align-items:center; justify-content:center; padding:20px; overflow:hidden;">
                        <img src="{{ asset('assets/images/' . ($product->imagen ?? 'default.png')) }}" alt="{{ $product->nombre }}"
                            style="max-width:100%; max-height:100%; object-fit:contain; transition:transform 0.5s ease;"
                            onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
                    </div>

                    <!-- Info -->
                    <div style="padding:20px 20px 24px;">
                        <div style="font-size:11px; font-weight:600; color:#007AFF; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:5px;">{{ $product->nombre_categoria ?? 'General' }}</div>
                        <div style="font-size:16px; font-weight:600; color:#1C1C1E; line-height:1.3; margin-bottom:4px;">{{ $product->nombre }}</div>
                        <div style="font-size:14px; color:#3A3A3C; margin-bottom:16px;">${{ number_format($product->precio_venta, 2) }}</div>
                        <div class="flex gap-2">
                            <button style="flex:1; background:#007AFF; color:#fff; border:none; border-radius:980px; padding:10px 14px; font-size:13px; font-weight:600; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#0056CC'" onmouseout="this.style.background='#007AFF'">Ver detalles</button>
                            <button style="background:#F2F2F7; color:#3A3A3C; border:none; border-radius:980px; padding:10px 14px; font-size:13px; font-weight:600; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#E5E5EA'" onmouseout="this.style.background='#F2F2F7'"><i class="fas fa-shopping-bag"></i></button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ══════════ ENVÍOS (BENTO) ══════════ -->
    <section id="envios" style="background:#FFFFFF; padding:80px 0;">
        <div class="max-w-6xl mx-auto px-6">
            <div style="margin-bottom:40px;">
                <h2 style="font-size:28px; font-weight:700; letter-spacing:-0.5px; color:#1C1C1E;">Entrega rápida y segura.</h2>
                <p style="font-size:15px; color:#8E8E93; margin-top:8px;">Elige la opción que mejor se adapte a tus necesidades.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3" style="grid-auto-rows: auto;">

                <!-- Departamental (destaca, ocupa 2 cols) -->
                <div class="md:col-span-2 flex items-center gap-10 p-10 rounded-3xl" style="background:#1C1C1E; border-radius:24px;">
                    <div style="flex:1;">
                        <span style="display:inline-block; background:#007AFF; color:#fff; font-size:10px; font-weight:700; letter-spacing:0.05em; text-transform:uppercase; padding:4px 12px; border-radius:980px; margin-bottom:12px;">Más elegido</span>
                        <h3 style="font-size:28px; font-weight:700; letter-spacing:-0.5px; color:#fff; margin-bottom:8px;">Envío Departamental</h3>
                        <p style="font-size:14px; color:rgba(255,255,255,0.5); line-height:1.6; margin-bottom:16px;">Llega a cualquier parte del país con seguimiento y empaque protegido.</p>
                        <div style="font-size:40px; font-weight:700; letter-spacing:-1px; color:#fff;">$3.50</div>
                    </div>
                    <div style="width:80px; height:80px; border-radius:24px; background:rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:center; font-size:36px; color:rgba(255,255,255,0.5); flex-shrink:0;">
                        <i class="fas fa-truck"></i>
                    </div>
                </div>

                <!-- Local -->
                <div class="p-8 rounded-3xl" style="background:#F2F2F7; border-radius:24px;">
                    <div style="width:48px; height:48px; border-radius:14px; background:rgba(255,159,10,0.12); color:#FF9F0A; display:flex; align-items:center; justify-content:center; font-size:20px; margin-bottom:16px;"><i class="fas fa-motorcycle"></i></div>
                    <h3 style="font-size:20px; font-weight:700; color:#1C1C1E; margin-bottom:6px;">Envío Local</h3>
                    <p style="font-size:14px; color:#8E8E93; line-height:1.6; margin-bottom:14px;">Mismo día en el área metropolitana. De 8AM a 4PM.</p>
                    <div style="font-size:28px; font-weight:700; color:#1C1C1E;">$2.00</div>
                </div>

                <!-- Especial -->
                <div class="p-8 rounded-3xl" style="background:#F2F2F7; border-radius:24px;">
                    <div style="width:48px; height:48px; border-radius:14px; background:rgba(52,199,89,0.12); color:#34C759; display:flex; align-items:center; justify-content:center; font-size:20px; margin-bottom:16px;"><i class="fas fa-gift"></i></div>
                    <h3 style="font-size:20px; font-weight:700; color:#1C1C1E; margin-bottom:6px;">Entrega Especial</h3>
                    <p style="font-size:14px; color:#8E8E93; line-height:1.6;">Empaque premium con nota personalizada para regalos únicos.</p>
                </div>

                <!-- Horario -->
                <div class="p-8 rounded-3xl md:col-span-2" style="background:rgba(0,122,255,0.07); border:1px solid rgba(0,122,255,0.15); border-radius:24px;">
                    <div style="width:48px; height:48px; border-radius:14px; background:rgba(0,122,255,0.12); color:#007AFF; display:flex; align-items:center; justify-content:center; font-size:20px; margin-bottom:16px;"><i class="fas fa-clock"></i></div>
                    <h3 style="font-size:20px; font-weight:700; color:#007AFF; margin-bottom:6px;">Entrega el mismo día</h3>
                    <p style="font-size:14px; color:#3A3A3C;">Realiza tu pedido antes de las 2:00 PM y recíbelo hoy mismo en tu puerta.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- ══════════ FAQ ══════════ -->
    <section id="faq" style="background:#F2F2F7; padding:80px 0;">
        <div class="max-w-3xl mx-auto px-6">
            <h2 style="font-size:28px; font-weight:700; letter-spacing:-0.5px; color:#1C1C1E; margin-bottom:32px;">Preguntas frecuentes.</h2>
            <div style="background:#FFFFFF; border-radius:24px; overflow:hidden;">

                <div style="border-bottom:1px solid #F2F2F7;">
                    <button class="faq-btn w-full flex justify-between items-center px-6 py-5 bg-transparent border-0 cursor-pointer text-left hover:bg-gray-50 transition-colors" onclick="toggleFaqApple(this)">
                        <span style="font-size:15px; font-weight:600; color:#1C1C1E;">¿Cuentan con garantía?</span>
                        <i class="fas fa-chevron-down faq-chevron" style="font-size:11px; color:#8E8E93; flex-shrink:0; margin-left:16px;"></i>
                    </button>
                    <div class="faq-answer"><p style="padding:0 24px 20px; font-size:14px; color:#8E8E93; line-height:1.7;">Todos nuestros artículos tienen garantía directa de 12 meses contra defectos de fábrica.</p></div>
                </div>

                <div style="border-bottom:1px solid #F2F2F7;">
                    <button class="faq-btn w-full flex justify-between items-center px-6 py-5 bg-transparent border-0 cursor-pointer text-left hover:bg-gray-50 transition-colors" onclick="toggleFaqApple(this)">
                        <span style="font-size:15px; font-weight:600; color:#1C1C1E;">¿Cuánto tarda el envío departamental?</span>
                        <i class="fas fa-chevron-down faq-chevron" style="font-size:11px; color:#8E8E93; flex-shrink:0; margin-left:16px;"></i>
                    </button>
                    <div class="faq-answer"><p style="padding:0 24px 20px; font-size:14px; color:#8E8E93; line-height:1.7;">Normalmente de 2 a 4 días hábiles dependiendo de la ruta.</p></div>
                </div>

                <div style="border-bottom:1px solid #F2F2F7;">
                    <button class="faq-btn w-full flex justify-between items-center px-6 py-5 bg-transparent border-0 cursor-pointer text-left hover:bg-gray-50 transition-colors" onclick="toggleFaqApple(this)">
                        <span style="font-size:15px; font-weight:600; color:#1C1C1E;">¿Puedo hacer devoluciones?</span>
                        <i class="fas fa-chevron-down faq-chevron" style="font-size:11px; color:#8E8E93; flex-shrink:0; margin-left:16px;"></i>
                    </button>
                    <div class="faq-answer"><p style="padding:0 24px 20px; font-size:14px; color:#8E8E93; line-height:1.7;">Tienes hasta 14 días para devolver productos en su empaque original sin señales de uso.</p></div>
                </div>

                <div>
                    <button class="faq-btn w-full flex justify-between items-center px-6 py-5 bg-transparent border-0 cursor-pointer text-left hover:bg-gray-50 transition-colors" onclick="toggleFaqApple(this)">
                        <span style="font-size:15px; font-weight:600; color:#1C1C1E;">¿Qué métodos de pago aceptan?</span>
                        <i class="fas fa-chevron-down faq-chevron" style="font-size:11px; color:#8E8E93; flex-shrink:0; margin-left:16px;"></i>
                    </button>
                    <div class="faq-answer"><p style="padding:0 24px 20px; font-size:14px; color:#8E8E93; line-height:1.7;">Aceptamos tarjetas de crédito/débito, transferencias bancarias y pagos digitales.</p></div>
                </div>

            </div>
        </div>
    </section>

    <!-- ══════════ FOOTER ══════════ -->
    <footer style="background:#FFFFFF; border-top:1px solid rgba(0,0,0,0.07); padding:48px 24px 32px;">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center flex-wrap gap-5 mb-8">
                <a href="/variante-apple" style="font-size:20px; font-weight:700; color:#1C1C1E; text-decoration:none;">AX<span style="color:#007AFF;">Store</span></a>
                <nav class="flex gap-6 flex-wrap">
                    <a href="#" style="font-size:13px; color:#8E8E93; text-decoration:none; transition:color 0.2s;" class="hover:text-blue-600">Productos</a>
                    <a href="#" style="font-size:13px; color:#8E8E93; text-decoration:none; transition:color 0.2s;" class="hover:text-blue-600">Envíos</a>
                    <a href="#" style="font-size:13px; color:#8E8E93; text-decoration:none; transition:color 0.2s;" class="hover:text-blue-600">Preguntas</a>
                    <a href="#" style="font-size:13px; color:#8E8E93; text-decoration:none; transition:color 0.2s;" class="hover:text-blue-600">Contacto</a>
                </nav>
            </div>
            <div style="border-top:1px solid rgba(0,0,0,0.07); padding-top:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <span style="font-size:12px; color:#8E8E93;">Copyright © 2026 AXStore. Todos los derechos reservados.</span>
                <div class="flex gap-4">
                    <a href="#" style="font-size:16px; color:#8E8E93; text-decoration:none; transition:color 0.2s;" class="hover:text-blue-600"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="font-size:16px; color:#8E8E93; text-decoration:none; transition:color 0.2s;" class="hover:text-blue-600"><i class="fab fa-facebook"></i></a>
                    <a href="#" style="font-size:16px; color:#8E8E93; text-decoration:none; transition:color 0.2s;" class="hover:text-blue-600"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ══════════ MODAL ══════════ -->
    <div id="appleModal" class="fixed inset-0 z-[200] items-center justify-center p-4"
        style="background:rgba(0,0,0,0.4); backdrop-filter:blur(20px) saturate(180%);">
        <div style="width:100%; max-width:820px; background:rgba(255,255,255,0.88); backdrop-filter:blur(40px); border-radius:28px; overflow:hidden; display:flex; box-shadow:0 30px 80px rgba(0,0,0,0.2); max-height:88vh;">

            <!-- Imagen -->
            <div style="width:42%; flex-shrink:0; background:#F2F2F7; display:flex; align-items:center; justify-content:center; padding:28px;">
                <img id="a-modal-img" src="" alt="Producto" style="max-width:100%; max-height:100%; object-fit:contain;">
            </div>

            <!-- Info -->
            <div style="flex:1; padding:36px 32px; display:flex; flex-direction:column; overflow-y:auto;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px;">
                    <span id="a-modal-category" style="font-size:11px; font-weight:700; color:#007AFF; text-transform:uppercase; letter-spacing:0.05em;"></span>
                    <button onclick="closeAppleModal()" style="width:30px; height:30px; border-radius:50%; background:#F2F2F7; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#8E8E93; font-size:11px; flex-shrink:0; transition:background 0.2s;" onmouseover="this.style.background='#E5E5EA'" onmouseout="this.style.background='#F2F2F7'"><i class="fas fa-times"></i></button>
                </div>
                <h2 id="a-modal-name" style="font-size:26px; font-weight:700; letter-spacing:-0.5px; color:#1C1C1E; margin-bottom:4px; line-height:1.2;"></h2>
                <div id="a-modal-sku" style="font-size:11px; color:#8E8E93; margin-bottom:20px;"></div>
                <div id="a-modal-price" style="font-size:36px; font-weight:700; letter-spacing:-1px; color:#1C1C1E; margin-bottom:4px;"></div>
                <div style="font-size:11px; color:#8E8E93; margin-bottom:18px;">Precio de venta</div>
                <div style="height:1px; background:rgba(0,0,0,0.06); margin-bottom:18px;"></div>
                <p id="a-modal-description" style="font-size:14px; color:#8E8E93; line-height:1.7; flex:1; margin-bottom:20px;"></p>

                <!-- Stats -->
                <div class="flex gap-5 mb-6">
                    <div style="flex:1; background:#F2F2F7; border-radius:14px; padding:14px 16px; text-align:center;">
                        <div style="font-size:10px; font-weight:600; color:#8E8E93; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Stock</div>
                        <div id="a-modal-stock" style="font-size:22px; font-weight:700; color:#1C1C1E;"></div>
                    </div>
                    <div style="flex:1; background:#F2F2F7; border-radius:14px; padding:14px 16px; text-align:center;">
                        <div style="font-size:10px; font-weight:600; color:#8E8E93; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Reserva</div>
                        <div id="a-modal-reserva" style="font-size:22px; font-weight:700; color:#1C1C1E;"></div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-2">
                    <button style="flex:1; background:#007AFF; color:#fff; border:none; border-radius:14px; padding:14px; font-size:14px; font-weight:600; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#0056CC'" onmouseout="this.style.background='#007AFF'">
                        <i class="fas fa-shopping-bag" style="margin-right:8px;"></i>Agregar a la Bolsa
                    </button>
                    <button onclick="copyAppleKit()" style="flex:1; background:#F2F2F7; color:#3A3A3C; border:none; border-radius:14px; padding:14px; font-size:14px; font-weight:600; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#E5E5EA'" onmouseout="this.style.background='#F2F2F7'">
                        <i class="fas fa-copy" style="margin-right:8px;"></i>Copiar
                    </button>
                </div>
                <textarea id="a-modal-copy" style="position:absolute; left:-9999px;"></textarea>
            </div>
        </div>
    </div>

    <script>
        function openAppleModal(card) {
            const d = card.dataset;
            document.getElementById('a-modal-name').innerText        = d.name;
            document.getElementById('a-modal-price').innerText       = d.price;
            document.getElementById('a-modal-category').innerText    = d.category;
            document.getElementById('a-modal-sku').innerText         = 'SKU: ' + d.sku;
            document.getElementById('a-modal-stock').innerText       = (d.stock||'0').replace(' un.','');
            document.getElementById('a-modal-reserva').innerText     = (d.reserva||'0').replace(' un.','');
            document.getElementById('a-modal-img').src               = d.image;
            document.getElementById('a-modal-description').innerText = d.description;
            document.getElementById('a-modal-copy').value = `Producto: ${d.name}\nPrecio: ${d.price}\nCategoría: ${d.category}\nSKU: ${d.sku}`;
            document.getElementById('appleModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeAppleModal() {
            document.getElementById('appleModal').classList.remove('open');
            document.body.style.overflow = '';
        }
        document.getElementById('appleModal').addEventListener('click', function(e) {
            if (e.target === this) closeAppleModal();
        });
        function copyAppleKit() {
            navigator.clipboard.writeText(document.getElementById('a-modal-copy').value)
                .then(() => alert('Información copiada al portapapeles.'));
        }
        function toggleFaqApple(btn) {
            const answer = btn.nextElementSibling;
            const open   = answer.classList.contains('open');
            document.querySelectorAll('.faq-answer.open').forEach(a => {
                a.classList.remove('open');
                a.previousElementSibling.classList.remove('open');
            });
            if (!open) { answer.classList.add('open'); btn.classList.add('open'); }
        }
    </script>
</body>
</html>
