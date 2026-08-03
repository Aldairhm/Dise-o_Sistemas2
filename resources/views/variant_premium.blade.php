<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AXStore Maison — Luxury</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; background: #0A0A0A; }
        .font-luxury { font-family: 'Cormorant Garamond', serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0A0A0A; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #C9A96E; }

        /* Hero scroll indicator */
        .scroll-line {
            width: 1px; height: 60px;
            background: linear-gradient(to bottom, transparent, #C9A96E);
            animation: scrollAnim 2s ease-in-out infinite;
        }
        @keyframes scrollAnim {
            0%   { transform: scaleY(0); transform-origin: top; }
            49%  { transform: scaleY(1); transform-origin: top; }
            50%  { transform: scaleY(1); transform-origin: bottom; }
            100% { transform: scaleY(0); transform-origin: bottom; }
        }

        /* Product overlay reveal */
        .product-info-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.88) 0%, rgba(0,0,0,0.2) 60%, transparent 100%);
            display: flex; flex-direction: column; justify-content: flex-end;
            padding: 28px 24px;
            transition: background 0.5s;
        }
        .product-card:hover .product-info-overlay {
            background: linear-gradient(to top, rgba(0,0,0,0.95) 30%, rgba(0,0,0,0.3) 100%);
        }

        /* Dropdown */
        .nav-dropdown { position: relative; }
        .nav-dropdown-menu {
            position: absolute; top: calc(100% + 16px); right: 0;
            min-width: 220px; background: #111111;
            border: 1px solid rgba(255,255,255,0.07);
            opacity: 0; visibility: hidden; transform: translateY(-8px);
            transition: all 0.3s ease;
        }
        .nav-dropdown:hover .nav-dropdown-menu {
            opacity: 1; visibility: visible; transform: translateY(0);
        }

        /* FAQ */
        .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.5s cubic-bezier(0.4,0,0.2,1); }
        .faq-answer.open { max-height: 200px; }
        .faq-icon { transition: transform 0.4s, color 0.3s; }
        .faq-btn.open .faq-icon { transform: rotate(45deg); color: #C9A96E; }

        /* Modal */
        #premiumModal { display: none; }
        #premiumModal.open { display: flex; }
        .modal-img-panel img { transition: transform 0.8s ease; }
        .modal-img-panel:hover img { transform: scale(1.04); }
    </style>
</head>
<body class="antialiased overflow-x-hidden text-stone-300" style="background:#0A0A0A; color:#B8B8B8;">

    <!-- ═══ HEADER ═══ -->
    <header class="fixed w-full z-50 top-0" style="background:rgba(10,10,10,0.85); backdrop-filter:blur(20px); border-bottom:1px solid rgba(255,255,255,0.05);">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <div class="flex items-center justify-between h-24">

                <!-- Nav Left -->
                <nav class="hidden md:flex items-center gap-10 w-1/3">
                    <a href="/variante-premium" style="font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#C9A96E;" class="hover:opacity-70 transition-opacity">Inicio</a>
                    <a href="#collection" style="font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#888;" class="hover:text-white transition-colors">Colección</a>
                </nav>

                <!-- Logo Central -->
                <a href="/variante-premium" class="flex justify-center w-1/3 group no-underline text-center">
                    <div>
                        <span class="font-luxury block" style="font-size:30px; letter-spacing:0.25em; color:#fff; font-weight:300; text-transform:uppercase; transition:color 0.3s;">Axstore</span>
                        <div style="height:1px; width:40px; background:#C9A96E; margin:8px auto 4px;"></div>
                        <span style="font-size:9px; letter-spacing:0.4em; text-transform:uppercase; color:#555;">Maison</span>
                    </div>
                </a>

                <!-- Nav Right -->
                <div class="flex items-center justify-end gap-6 w-1/3">
                    <div class="nav-dropdown hidden md:block">
                        <button style="font-size:11px; letter-spacing:0.18em; text-transform:uppercase; color:#888; background:none; border:none; cursor:pointer; display:flex; align-items:center; gap:6px;" class="hover:text-white transition-colors">
                            Versiones <i class="fas fa-chevron-down" style="font-size:8px;"></i>
                        </button>
                        <div class="nav-dropdown-menu">
                            <a href="/" style="display:block; padding:14px 24px; font-size:10px; letter-spacing:0.15em; text-transform:uppercase; color:#666; text-decoration:none; border-bottom:1px solid rgba(255,255,255,0.04);" class="hover:text-white transition-colors">1. Original</a>
                            <a href="/variante" style="display:block; padding:14px 24px; font-size:10px; letter-spacing:0.15em; text-transform:uppercase; color:#666; text-decoration:none; border-bottom:1px solid rgba(255,255,255,0.04);" class="hover:text-white transition-colors">2. Clara</a>
                            <a href="/variante-premium" style="display:block; padding:14px 24px; font-size:10px; letter-spacing:0.15em; text-transform:uppercase; color:#C9A96E; text-decoration:none; border-bottom:1px solid rgba(255,255,255,0.04); background:rgba(201,169,110,0.05);">3. Luxury (Actual)</a>
                            <a href="/variante-apple" style="display:block; padding:14px 24px; font-size:10px; letter-spacing:0.15em; text-transform:uppercase; color:#666; text-decoration:none;" class="hover:text-white transition-colors">4. Minimalista</a>
                        </div>
                    </div>
                    <button style="background:none; border:none; cursor:pointer; color:#666; font-size:14px;" class="hover:text-yellow-500 transition-colors"><i class="fas fa-search"></i></button>
                    <button style="background:none; border:none; cursor:pointer; color:#666; font-size:14px; position:relative;" class="hover:text-yellow-500 transition-colors">
                        <i class="fas fa-shopping-bag"></i>
                        <span style="position:absolute; top:-6px; right:-8px; background:#C9A96E; color:#0A0A0A; font-size:9px; font-weight:700; width:16px; height:16px; border-radius:50%; display:flex; align-items:center; justify-content:center;">0</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- ═══ HERO ═══ -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden" style="padding-top:96px;">
        <!-- Logo como fondo fantasma -->
        <div class="absolute inset-0 flex items-center justify-center" style="z-index:0;">
            <img src="{{ asset('assets/images/logo.png') }}" alt="" style="width:55%; max-width:580px; opacity:0.04; filter:grayscale(1); pointer-events:none;">
        </div>
        <!-- Radial gradient para enfocar el texto -->
        <div class="absolute inset-0" style="background:radial-gradient(ellipse at center, transparent 10%, #0A0A0A 75%); z-index:1;"></div>

        <div class="relative text-center px-6 max-w-4xl mx-auto" style="z-index:2;">
            <span class="block mb-8" style="font-size:10px; letter-spacing:0.5em; text-transform:uppercase; color:#C9A96E; font-weight:600;">Exclusividad · Elegancia · 2026</span>
            <h1 class="font-luxury" style="font-size:clamp(64px,10vw,130px); font-weight:300; color:#fff; line-height:0.88; margin-bottom:40px; letter-spacing:-0.01em;">
                El Arte<br><span style="font-style:italic; color:rgba(255,255,255,0.35); font-weight:300;">de Regalar</span>
            </h1>
            <p style="font-size:15px; font-weight:300; color:#666; line-height:2; letter-spacing:0.04em; max-width:460px; margin:0 auto 50px;">
                Piezas únicas, diseñadas para quienes aprecian la distinción y la belleza que perdura.
            </p>
            <a href="#collection" style="display:inline-block; border:1px solid #C9A96E; color:#C9A96E; padding:16px 52px; font-size:10px; letter-spacing:0.3em; text-transform:uppercase; text-decoration:none; transition:all 0.5s;" class="hover:bg-amber-600 hover:border-amber-600 hover:text-black">
                Explorar Colección
            </a>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-3" style="z-index:2;">
            <div class="scroll-line"></div>
            <span style="font-size:9px; letter-spacing:0.3em; text-transform:uppercase; color:#444;">Scroll</span>
        </div>
    </section>

    <!-- ═══ COLECCIÓN ═══ -->
    <section id="collection" style="background:#0A0A0A; padding:160px 0;">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <!-- Header -->
            <div class="text-center mb-24">
                <span class="block mb-4" style="font-size:9px; letter-spacing:0.45em; text-transform:uppercase; color:#C9A96E;">Temporada 2026</span>
                <h2 class="font-luxury" style="font-size:clamp(36px,4vw,56px); font-weight:300; color:#fff; margin-bottom:16px;">La Colección</h2>
                <div style="width:60px; height:1px; background:rgba(201,169,110,0.4); margin:0 auto;"></div>
            </div>

            <!-- Grid tipo Galería Full-Bleed -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3" style="gap:2px;">
                @foreach($products as $product)
                <div class="product-card relative cursor-pointer overflow-hidden group"
                    onclick="openPremiumModal(this)"
                    data-name="{{ $product->nombre }}"
                    data-price="${{ number_format($product->precio_venta, 2) }}"
                    data-image="{{ asset('assets/images/' . ($product->imagen ?? 'default.png')) }}"
                    data-sku="{{ $product->sku }}"
                    data-stock="{{ $product->stock }}"
                    data-reserva="{{ $product->reserva }}"
                    data-category="{{ $product->nombre_categoria ?? 'General' }}"
                    data-description="{{ $product->nombre_producto_padre ?? 'Pieza exclusiva de nuestra colección.' }}">

                    <!-- Imagen -->
                    <div class="w-full overflow-hidden" style="aspect-ratio:3/4; background:#111;">
                        <img src="{{ asset('assets/images/' . ($product->imagen ?? 'default.png')) }}" alt="{{ $product->nombre }}"
                            class="w-full h-full object-cover transition-all duration-1000 ease-in-out group-hover:scale-110"
                            style="filter:brightness(0.82);">
                    </div>

                    <!-- Overlay con info -->
                    <div class="product-info-overlay">
                        <span class="block mb-2 opacity-0 group-hover:opacity-100 transition-all duration-300" style="font-size:9px; letter-spacing:0.35em; text-transform:uppercase; color:#C9A96E; transform:translateY(8px);" data-hover-up>{{ $product->nombre_categoria ?? 'General' }}</span>
                        <h3 class="font-luxury mb-1" style="font-size:22px; font-weight:300; color:#fff; line-height:1.2;">{{ $product->nombre }}</h3>
                        <p class="mb-4" style="font-size:13px; color:rgba(255,255,255,0.45); letter-spacing:0.08em;">${{ number_format($product->precio_venta, 2) }}</p>
                        <span class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300" style="font-size:9px; letter-spacing:0.3em; text-transform:uppercase; color:#C9A96E;">
                            Descubrir <span style="display:inline-block; width:40px; height:1px; background:#C9A96E; vertical-align:middle;"></span>
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ═══ ENVÍOS ═══ -->
    <section style="background:#111111; border-top:1px solid rgba(255,255,255,0.04); border-bottom:1px solid rgba(255,255,255,0.04); padding:120px 0;">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <div class="text-center mb-16">
                <span class="block mb-4" style="font-size:9px; letter-spacing:0.4em; text-transform:uppercase; color:#C9A96E;">Logística Premium</span>
                <h2 class="font-luxury" style="font-size:clamp(30px,3vw,48px); font-weight:300; color:#fff;">Entrega Excepcional</h2>
                <div style="width:60px; height:1px; background:rgba(201,169,110,0.4); margin:16px auto 0;"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3" style="gap:1px; background:rgba(255,255,255,0.04);">
                <!-- Local -->
                <div class="text-center group transition-all duration-300 cursor-default hover:bg-stone-900" style="background:#111111; padding:56px 36px;">
                    <div style="width:56px; height:56px; border:1px solid rgba(201,169,110,0.3); display:flex; align-items:center; justify-content:center; margin:0 auto 32px; color:#C9A96E; font-size:20px; transition:all 0.4s;" class="group-hover:border-yellow-600 group-hover:bg-yellow-900/10">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <h3 class="font-luxury mb-3" style="font-size:24px; font-weight:300; color:#fff;">Envío Local</h3>
                    <p style="font-size:13px; font-weight:300; color:#666; line-height:1.8; margin-bottom:28px;">Entrega el mismo día en puntos céntricos del área metropolitana.</p>
                    <span style="font-size:11px; letter-spacing:0.2em; color:#C9A96E; text-transform:uppercase;">$2.00</span>
                </div>

                <!-- Departamental -->
                <div class="text-center group transition-all duration-300 cursor-default hover:bg-stone-900 relative overflow-hidden" style="background:#111111; padding:56px 36px;">
                    <div style="position:absolute; top:0; right:0; background:#C9A96E; color:#0A0A0A; font-size:8px; font-weight:700; letter-spacing:0.15em; text-transform:uppercase; padding:6px 12px;">Preferido</div>
                    <div style="width:56px; height:56px; border:1px solid rgba(201,169,110,0.3); display:flex; align-items:center; justify-content:center; margin:0 auto 32px; color:#C9A96E; font-size:20px; transition:all 0.4s;" class="group-hover:border-yellow-600 group-hover:bg-yellow-900/10">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3 class="font-luxury mb-3" style="font-size:24px; font-weight:300; color:#fff;">Departamental</h3>
                    <p style="font-size:13px; font-weight:300; color:#666; line-height:1.8; margin-bottom:28px;">Cobertura nacional con embalaje de seguridad y seguimiento en tiempo real.</p>
                    <span style="font-size:11px; letter-spacing:0.2em; color:#C9A96E; text-transform:uppercase;">$3.50</span>
                </div>

                <!-- Especial -->
                <div class="text-center group transition-all duration-300 cursor-default hover:bg-stone-900" style="background:#111111; padding:56px 36px;">
                    <div style="width:56px; height:56px; border:1px solid rgba(201,169,110,0.3); display:flex; align-items:center; justify-content:center; margin:0 auto 32px; color:#C9A96E; font-size:20px; transition:all 0.4s;" class="group-hover:border-yellow-600 group-hover:bg-yellow-900/10">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h3 class="font-luxury mb-3" style="font-size:24px; font-weight:300; color:#fff;">Entrega Especial</h3>
                    <p style="font-size:13px; font-weight:300; color:#666; line-height:1.8; margin-bottom:28px;">Servicio personalizado con empaque premium y nota manuscrita para ocasiones únicas.</p>
                    <span style="font-size:11px; letter-spacing:0.2em; color:#C9A96E; text-transform:uppercase;">A convenir</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ FAQ ═══ -->
    <section style="background:#0A0A0A; padding:120px 0;">
        <div class="max-w-3xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="block mb-4" style="font-size:9px; letter-spacing:0.4em; text-transform:uppercase; color:#C9A96E;">Soporte</span>
                <h2 class="font-luxury" style="font-size:clamp(30px,3vw,44px); font-weight:300; color:#fff;">Preguntas Frecuentes</h2>
                <div style="width:60px; height:1px; background:rgba(201,169,110,0.4); margin:16px auto 0;"></div>
            </div>
            <div>
                <div style="border-bottom:1px solid rgba(255,255,255,0.06);">
                    <button class="faq-btn w-full flex justify-between items-center py-7 text-left bg-transparent border-0 cursor-pointer hover:text-amber-500 transition-colors" onclick="toggleFaqPremium(this)">
                        <span class="font-luxury" style="font-size:20px; font-weight:300; color:#fff;">¿Cuentan con garantía en sus piezas?</span>
                        <i class="fas fa-plus faq-icon" style="font-size:11px; color:#555; flex-shrink:0; margin-left:20px;"></i>
                    </button>
                    <div class="faq-answer"><p style="font-size:14px; font-weight:300; color:#666; line-height:2; padding-bottom:28px;">Todas nuestras piezas incluyen certificado de autenticidad y garantía de 12 meses contra defectos de fabricación.</p></div>
                </div>
                <div style="border-bottom:1px solid rgba(255,255,255,0.06);">
                    <button class="faq-btn w-full flex justify-between items-center py-7 text-left bg-transparent border-0 cursor-pointer hover:text-amber-500 transition-colors" onclick="toggleFaqPremium(this)">
                        <span class="font-luxury" style="font-size:20px; font-weight:300; color:#fff;">¿Puedo solicitar empaque de regalo?</span>
                        <i class="fas fa-plus faq-icon" style="font-size:11px; color:#555; flex-shrink:0; margin-left:20px;"></i>
                    </button>
                    <div class="faq-answer"><p style="font-size:14px; font-weight:300; color:#666; line-height:2; padding-bottom:28px;">Por supuesto. Cada pedido puede incluir una caja premium con nota personalizada sin costo adicional.</p></div>
                </div>
                <div style="border-bottom:1px solid rgba(255,255,255,0.06);">
                    <button class="faq-btn w-full flex justify-between items-center py-7 text-left bg-transparent border-0 cursor-pointer hover:text-amber-500 transition-colors" onclick="toggleFaqPremium(this)">
                        <span class="font-luxury" style="font-size:20px; font-weight:300; color:#fff;">¿Cuánto tarda el envío local?</span>
                        <i class="fas fa-plus faq-icon" style="font-size:11px; color:#555; flex-shrink:0; margin-left:20px;"></i>
                    </button>
                    <div class="faq-answer"><p style="font-size:14px; font-weight:300; color:#666; line-height:2; padding-bottom:28px;">El envío local se realiza el mismo día para pedidos antes de las 2:00 PM, en horario de 8:00 AM a 4:00 PM.</p></div>
                </div>
                <div style="border-bottom:1px solid rgba(255,255,255,0.06);">
                    <button class="faq-btn w-full flex justify-between items-center py-7 text-left bg-transparent border-0 cursor-pointer hover:text-amber-500 transition-colors" onclick="toggleFaqPremium(this)">
                        <span class="font-luxury" style="font-size:20px; font-weight:300; color:#fff;">¿Qué métodos de pago aceptan?</span>
                        <i class="fas fa-plus faq-icon" style="font-size:11px; color:#555; flex-shrink:0; margin-left:20px;"></i>
                    </button>
                    <div class="faq-answer"><p style="font-size:14px; font-weight:300; color:#666; line-height:2; padding-bottom:28px;">Aceptamos tarjetas de crédito/débito internacionales, transferencias bancarias y pagos digitales certificados.</p></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ FOOTER ═══ -->
    <footer style="background:#050505; border-top:1px solid rgba(255,255,255,0.04); padding:80px 0;">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <span class="font-luxury block mb-1" style="font-size:34px; font-weight:300; letter-spacing:0.25em; color:#fff; text-transform:uppercase;">Axstore</span>
            <div style="width:40px; height:1px; background:#C9A96E; margin:12px auto;"></div>
            <p style="font-size:10px; letter-spacing:0.2em; text-transform:uppercase; color:#444; margin-bottom:36px;">© 2026 AXStore Maison — Todos los derechos reservados.</p>
            <div class="flex justify-center gap-6">
                <a href="#" style="color:#333; font-size:16px; text-decoration:none; transition:color 0.3s;" class="hover:text-amber-500"><i class="fab fa-instagram"></i></a>
                <a href="#" style="color:#333; font-size:16px; text-decoration:none; transition:color 0.3s;" class="hover:text-amber-500"><i class="fab fa-facebook-f"></i></a>
                <a href="#" style="color:#333; font-size:16px; text-decoration:none; transition:color 0.3s;" class="hover:text-amber-500"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </footer>

    <!-- ═══ MODAL ═══ -->
    <div id="premiumModal" class="fixed inset-0 z-[200] items-center justify-center p-4 lg:p-12" style="background:rgba(0,0,0,0.92); backdrop-filter:blur(8px);">
        <div class="relative w-full max-w-5xl flex flex-col md:flex-row overflow-hidden" style="background:#111111; border:1px solid rgba(255,255,255,0.06); max-height:90vh;">
            <!-- Cerrar -->
            <button onclick="closePremiumModal()" class="absolute top-5 right-5 z-10 flex items-center gap-2 bg-transparent border-0 cursor-pointer hover:text-white transition-colors" style="color:#555; font-size:11px; letter-spacing:0.2em; text-transform:uppercase;">
                Cerrar <i class="fas fa-times"></i>
            </button>

            <!-- Panel Imagen -->
            <div class="modal-img-panel md:w-1/2 overflow-hidden" style="background:#0D0D0D; min-height:300px; display:flex; align-items:center; justify-content:center;">
                <img id="p-modal-img" src="" alt="Producto" class="w-full h-full object-cover" style="max-height:70vh;">
            </div>

            <!-- Panel Info -->
            <div class="md:w-1/2 flex flex-col justify-center overflow-y-auto" style="padding:56px 48px;">
                <span id="p-modal-category" class="block mb-4" style="font-size:9px; letter-spacing:0.4em; text-transform:uppercase; color:#C9A96E;"></span>
                <h2 id="p-modal-name" class="font-luxury mb-2" style="font-size:clamp(28px,3vw,44px); font-weight:300; color:#fff; line-height:1.1;"></h2>
                <div id="p-modal-sku" class="mb-8" style="font-size:10px; letter-spacing:0.2em; color:#555;"></div>
                <div style="height:1px; background:rgba(255,255,255,0.06); margin-bottom:28px;"></div>
                <div id="p-modal-price" class="font-luxury mb-2" style="font-size:42px; font-weight:300; color:#fff; letter-spacing:0.03em;"></div>
                <div class="mb-8" style="font-size:9px; letter-spacing:0.3em; text-transform:uppercase; color:#555;">Precio de venta</div>
                <p id="p-modal-description" class="mb-10" style="font-size:13px; font-weight:300; color:#666; line-height:1.9;"></p>
                <!-- Stats -->
                <div class="flex gap-10 mb-10">
                    <div>
                        <div class="mb-1" style="font-size:8px; letter-spacing:0.3em; text-transform:uppercase; color:#444;">Disponibles</div>
                        <div id="p-modal-stock" class="font-luxury" style="font-size:30px; color:#fff; font-weight:300;"></div>
                    </div>
                    <div>
                        <div class="mb-1" style="font-size:8px; letter-spacing:0.3em; text-transform:uppercase; color:#444;">En Reserva</div>
                        <div id="p-modal-reserva" class="font-luxury" style="font-size:30px; color:#fff; font-weight:300;"></div>
                    </div>
                </div>
                <!-- Acciones -->
                <div class="flex gap-3">
                    <button class="flex-1 border-0 cursor-pointer transition-colors" style="background:#C9A96E; color:#0A0A0A; padding:16px 24px; font-size:10px; letter-spacing:0.3em; text-transform:uppercase; font-weight:700;" onmouseover="this.style.background='#E8C98A'" onmouseout="this.style.background='#C9A96E'">
                        <i class="fas fa-shopping-bag" style="margin-right:8px;"></i>Al Carrito
                    </button>
                    <button onclick="copyPremiumKit()" class="flex-1 cursor-pointer transition-colors" style="background:transparent; color:#aaa; padding:16px 24px; font-size:10px; letter-spacing:0.3em; text-transform:uppercase; border:1px solid rgba(255,255,255,0.12);" onmouseover="this.style.borderColor='#fff';this.style.color='#fff'" onmouseout="this.style.borderColor='rgba(255,255,255,0.12)';this.style.color='#aaa'">
                        <i class="fas fa-copy" style="margin-right:8px;"></i>Copiar
                    </button>
                </div>
                <textarea id="p-modal-copy" style="position:absolute; left:-9999px;"></textarea>
            </div>
        </div>
    </div>

    <script>
        function openPremiumModal(card) {
            const d = card.dataset;
            document.getElementById('p-modal-name').innerText        = d.name;
            document.getElementById('p-modal-price').innerText       = d.price;
            document.getElementById('p-modal-category').innerText    = d.category;
            document.getElementById('p-modal-sku').innerText         = 'REF · ' + d.sku;
            document.getElementById('p-modal-stock').innerText       = (d.stock||'0').replace(' un.','');
            document.getElementById('p-modal-reserva').innerText     = (d.reserva||'0').replace(' un.','');
            document.getElementById('p-modal-img').src               = d.image;
            document.getElementById('p-modal-description').innerText = d.description;
            document.getElementById('p-modal-copy').value = `AXSTORE MAISON\nPieza: ${d.name}\nColección: ${d.category}\nValor: ${d.price}\nRef: ${d.sku}`;
            document.getElementById('premiumModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closePremiumModal() {
            document.getElementById('premiumModal').classList.remove('open');
            document.body.style.overflow = '';
        }
        document.getElementById('premiumModal').addEventListener('click', function(e){
            if (e.target === this) closePremiumModal();
        });
        function copyPremiumKit() {
            navigator.clipboard.writeText(document.getElementById('p-modal-copy').value)
                .then(() => alert('Detalles copiados al portapapeles.'));
        }
        function toggleFaqPremium(btn) {
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
