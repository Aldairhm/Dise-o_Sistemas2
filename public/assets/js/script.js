document.addEventListener('DOMContentLoaded', function() {
    
    console.log('✓ Script.js cargado correctamente');
    
    // ==========================================
    // 1. LÓGICA DEL MENÚ MÓVIL MEJORADA
    // ==========================================
    
    const mobileMenuBtn = document.querySelector('.mobile-nav-toggle');
    const navmenu = document.getElementById('navmenu');
    const navLinks = document.querySelectorAll('.navmenu a');
    const mobileOverlay = document.getElementById('mobile-nav-overlay');
    const body = document.body;

    // [NUEVO] Referencias a los botones de WhatsApp y Usuario
    const whatsappToggleBtn = document.querySelector('.grupos-icon [data-bs-toggle="dropdown"]');
    const userTogleBtn = document.querySelector('.user-icon [data-bs-toggle="dropdown"]');
    
    if (mobileMenuBtn && navmenu) {
        const icon = mobileMenuBtn.querySelector('i');

        // [NUEVO] Función auxiliar para cerrar dropdowns de Bootstrap manualmente
        function cerrarDropdownBootstrap(selectorPadre) {
            const dropdownMenu = document.querySelector(selectorPadre + ' .dropdown-menu');
            const dropdownToggle = document.querySelector(selectorPadre + ' [data-bs-toggle="dropdown"]');
            
            if (dropdownMenu && dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
                if (dropdownToggle) {
                    dropdownToggle.classList.remove('show');
                    dropdownToggle.setAttribute('aria-expanded', 'false');
                }
            }
        }

        // Función para abrir menú
        function openMenu() {
            navmenu.classList.add('mobile-nav-active');
            if (mobileOverlay) mobileOverlay.classList.add('active');
            body.classList.add('mobile-nav-open');
            
            if (icon) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            }

            // [MODIFICADO] Cerrar Carrito si está abierto
            const cartModal = document.getElementById('cart-modal');
            if (cartModal) cartModal.style.display = 'none';

            // [NUEVO] Cerrar Dropdowns de Bootstrap si están abiertos
            cerrarDropdownBootstrap('.user-icon');   // Cierra usuario
            cerrarDropdownBootstrap('.grupos-icon'); // Cierra WhatsApp
        }

        // Función para cerrar menú
        function closeMenu() {
            navmenu.classList.remove('mobile-nav-active');
            if (mobileOverlay) mobileOverlay.classList.remove('active');
            body.classList.remove('mobile-nav-open');
            
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }

        // Toggle menu
        function toggleMenu(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            if (navmenu.classList.contains('mobile-nav-active')) {
                closeMenu();
            } else {
                openMenu();
            }
        }

        // Click en botón hamburguesa
        mobileMenuBtn.addEventListener('click', toggleMenu);

        // Click en overlay (cierra el menú)
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', function(e) {
                if (e.target === mobileOverlay) {
                    closeMenu();
                }
            });
        }

        // [NUEVO] Click en icono WhatsApp cierra el menú móvil
        if (whatsappToggleBtn) {
            whatsappToggleBtn.addEventListener('click', function() {
                if (navmenu.classList.contains('mobile-nav-active')) {
                    closeMenu();
                }
                // Opcional: Cerrar también usuario si quisieras exclusividad total entre iconos
                cerrarDropdownBootstrap('.user-icon');
            });
        }

        // [NUEVO] Click en icono Usuario cierra el menú móvil
        if (userTogleBtn) {
            userTogleBtn.addEventListener('click', function() {
                if (navmenu.classList.contains('mobile-nav-active')) {
                    closeMenu();
                }
                // Opcional: Cerrar también WhatsApp
                cerrarDropdownBootstrap('.grupos-icon');
            });
        }

        // Click en enlaces del menú
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Si el enlace no es un dropdown, cerramos el menú
                const parent = this.closest('li');
                if (!parent.classList.contains('dropdown')) {
                    if (window.innerWidth <= 1199 && navmenu.classList.contains('mobile-nav-active')) {
                        setTimeout(() => closeMenu(), 150);
                    }
                }
            });
        });

        // Manejar clicks en dropdowns del menú móvil
        const dropdowns = document.querySelectorAll('.navmenu .dropdown > a');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                if (window.innerWidth <= 1199) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const parent = this.parentElement;
                    const isActive = parent.classList.contains('dropdown-active');
                    
                    // Cerrar todos los dropdowns internos del menú
                    const allDropdowns = navmenu.querySelectorAll('.dropdown.dropdown-active');
                    allDropdowns.forEach(d => d.classList.remove('dropdown-active'));
                    
                    // Abrir el actual si no estaba abierto
                    if (!isActive) {
                        parent.classList.add('dropdown-active');
                    } else {
                        // Si ya estaba abierto, navegamos al enlace
                        window.location.href = this.href;
                    }
                }
            });
        });

        // Cierra menú al redimensionar ventana
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (window.innerWidth > 1199 && navmenu.classList.contains('mobile-nav-active')) {
                    closeMenu();
                }
            }, 250);
        });

        // ESC key para cerrar menú
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && navmenu.classList.contains('mobile-nav-active')) {
                closeMenu();
            }
        });
    }

    // ==========================================
    // 2. HEADER SCROLL EFFECT
    // ==========================================
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // ==========================================
    // 3. LÓGICA DE PRODUCTOS DINÁMICA (CORREGIDA)
    // ==========================================
    const productGrid = document.getElementById('product-grid');
    const categoriesContainer = document.getElementById('categories-container');
    let allProducts = [];

    if (productGrid && categoriesContainer) {
        cargarDatosHome();
    }

    function cargarDatosHome() {
        // 1. Cargar Productos primero para saber cuáles categorías son funcionales
        $.ajax({
            url: 'app/controllers/productoController.php',
            type: 'POST',
            data: { accion: 'obtenerTodosLosProductosConVariantes' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    allProducts = response.data;
                    
                    // Una vez tenemos los productos, cargamos las categorías
                    $.ajax({
                        url: 'app/controllers/productoController.php',
                        type: 'POST',
                        data: { accion: 'obtenerCategorias' },
                        dataType: 'json',
                        success: function(catResponse) {
                            if (catResponse.status === 'success') {
                                // Filtramos solo las categorías que tienen al menos un producto en allProducts
                                const functionalCategories = catResponse.data.filter(cat => 
                                    allProducts.some(p => p.nombre_categoria === cat.nombre)
                                );
                                renderizarCategorias(functionalCategories);
                            }
                        }
                    });

                    renderizarProductosFiltrados(allProducts);
                }
            },
            error: function(err) {
                console.error("Error al cargar productos:", err);
                productGrid.innerHTML = '<div class="col-12 text-center py-5"><p class="text-danger">Error al conectar con el servidor.</p></div>';
            }
        });
    }

    function renderizarCategorias(categorias) {
        // Renderizar el selector de la toolbar
        const $catFilter = $("#category-filter");
        $catFilter.find('option:not([value="all"])').remove();
        
        // Renderizar las Pills de navegación
        categoriesContainer.innerHTML = '<div class="catalog-pill active" data-category="all">Todos los Productos</div>';
        
        categorias.forEach(cat => {
            // Opción en el select
            const option = document.createElement('option');
            option.value = cat.nombre;
            option.textContent = cat.nombre.toUpperCase();
            $catFilter.append(option);

            // Pill de navegación
            const pill = document.createElement('div');
            pill.className = 'catalog-pill';
            pill.dataset.category = cat.nombre;
            pill.textContent = cat.nombre;
            categoriesContainer.appendChild(pill);
        });

        // Eventos para las pills
        document.querySelectorAll('.catalog-pill').forEach(pill => {
            pill.addEventListener('click', function() {
                const category = this.dataset.category;
                document.querySelectorAll('.catalog-pill').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                
                // Sincronizar con el select
                $catFilter.val(category);
                
                aplicarFiltros();
            });
        });

        // Evento para el select
        $catFilter.on('change', function() {
            const category = $(this).val();
            document.querySelectorAll('.catalog-pill').forEach(p => {
                p.classList.toggle('active', p.dataset.category === category);
            });
            aplicarFiltros();
        });
    }

    // Nueva función de filtrado unificada (igual al catálogo)
    function aplicarFiltros() {
        const busqueda = $("#product-search").val().toLowerCase();
        const categoria = $("#category-filter").val();

        const filtrados = allProducts.filter(p => {
            const matchBusqueda = p.nombre.toLowerCase().includes(busqueda) || 
                                 (p.sku && p.sku.toLowerCase().includes(busqueda));
            const matchCategoria = (categoria === 'all' || p.nombre_categoria === categoria);
            return matchBusqueda && matchCategoria;
        });

        renderizarProductosFiltrados(filtrados);
    }

    function renderizarProductosFiltrados(productos) {
        productGrid.innerHTML = '';
        
        if (productos.length === 0) {
            $("#noResults").removeClass("d-none");
            return;
        }

        $("#noResults").addClass("d-none");
        
        const rutaBase = "app/views/assets/images/";
        const maxId = Math.max(...allProducts.map(p => p.id), 0);
        const umbralNuevo = maxId - 12;

        productos.forEach((product, index) => {
            
            // Convertimos a números para evitar bugs en devoluciones
            const reservaNum = parseInt(product.reserva) || 0;
            const stockNum = parseInt(product.stock) || 0;

            let precioVenta = Number(product.precio_venta);
            let precioFormateado = precioVenta.toFixed(2);
            
            // --- LÓGICA DE CARTELES CORREGIDA ---
            let cartelesExtra = '';
            if (stockNum <= 0 && reservaNum <= 0) {
                cartelesExtra = '<span class="badge-premium badge-low-stock"><i class="fas fa-times-circle me-1"></i> Agotado</span>';
            } else if (stockNum <= 0 && reservaNum > 0) {
                cartelesExtra = '<span class="badge-premium badge-top"><i class="fas fa-truck-loading me-1"></i> Esperando bodega</span>';
            }

            // Etiquetas de Información: Stock, Reserva y Cartel Extra
            let badgesHtml = `
                ${cartelesExtra}
                <span class="badge-premium badge-stock"><i class="fas fa-box me-1"></i>Tienda: ${product.stock}</span>
                <span class="badge-premium badge-reserva"><i class="fas fa-clock me-1"></i>Bodega: ${product.reserva}</span>
            `;

            const mainImg = `${rutaBase}${product.imagen || 'default.png'}`;
            const hoverImg = product.imagen_hover ? `${rutaBase}${product.imagen_hover}` : mainImg;
            let stockColor = product.stock > 5 ? 'text-success' : product.stock > 0 ? 'text-warning' : 'text-danger';

            const col = document.createElement('div');
            col.className = 'swiper-slide animate__animated animate__fadeIn';
            
            col.innerHTML = `
                <div class="card h-100 border-0 shadow-sm transition-hover product-card">
                    <div class="product-badge-container">${badgesHtml}</div>
                    <div class="product-quick-actions">
                        <button class="btn-action-premium btnQuickViewHome" data-id="${product.id}" title="Vista Rápida">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action-premium btnPdfDownloadHome" data-id="${product.id}" title="Descargar Ficha PDF">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                    </div>
                    <div class="product-image-container">
                        <img src="${mainImg}" class="product-img-main" alt="${product.nombre}">
                        <img src="${hoverImg}" class="product-img-hover" alt="${product.nombre} hover">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-light text-dark border">${product.nombre_categoria}</span>
                            <div class="text-end">
                                <span class="d-block x-small text-success fw-bold" style="font-size: 0.75rem;">Comisión: $${parseFloat(product.comision).toFixed(2)}</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-1">${product.nombre_producto_padre || ''}</p>
                        <h5 class="card-title fw-bold text-dark mb-3">${product.nombre}</h5>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="h4 mb-0 text-primary fw-bold">$${precioFormateado}</span>
                                <span class="small text-muted">SKU: ${product.sku}</span>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            productGrid.appendChild(col);
        });

        // Listeners específicos para elementos dentro del grid
        $(".btnQuickViewHome").off("click").on("click", function() {
            abrirQuickView($(this).data("id"));
        });

        // Inicializar o actualizar Swiper
        inicializarSwiper();
    }

    let productSwiperInstance = null;
    function inicializarSwiper() {
        if (productSwiperInstance) {
            productSwiperInstance.destroy(true, true);
        }

        productSwiperInstance = new Swiper(".productSwiper", {
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
    }

    // Event listener para el botón PDF en Home (Delegado)
    $(document).on("click", ".btnPdfDownloadHome", function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const id = $(this).data("id");
        console.log("Home PDF Clicked for ID:", id);
        
        const product = allProducts.find(p => p.id == id);
        
        if (product) {
            console.log("Generating Home Ticket for:", product.nombre);
            descargarFichaProducto(product);
        } else {
            console.error("Home Product not found for ID:", id);
        }
    });

    $("#btn-refresh").on("click", function() {
        $("#product-search").val("");
        $("#category-filter").val("all").trigger("change");
        cargarDatosHome();
    });

    // Eventos de búsqueda
    $("#product-search").on("input", aplicarFiltros);

    function abrirQuickView(id) {
        $.ajax({
            url: "app/controllers/productoController.php",
            method: "POST",
            dataType: "json",
            data: { accion: "obtenerDetalleQuickView", id: id },
            beforeSend: function() {
                Swal.fire({ title: 'Cargando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            },
            success: function(response) {
                Swal.close();
                if (response.status === "success") {
                    const data = response.data;
                    const v = data.variante;
                    console.log("Quick View Data (Home):", v); // DEBUG
                    const ruta = "app/views/assets/images/";
                    
                    // [MEJORA] Mostrar nombre del Padre + Variante
                    const nombrePadre = v.nombre_producto_padre || "Producto";
                    const nombreVariante = v.nombre || "";

                    $("#qv-category").text(v.nombre_categoria);
                    $("#qv-sku").text("SKU: " + v.sku);
                    // Actualizamos el título para que sea descriptivo
                    $("#qv-name").html(`<small class="text-muted d-block fs-6 mb-1">${nombrePadre}</small>${nombreVariante}`);

                    $("#qv-price").text("$" + parseFloat(v.precio_venta).toFixed(2));
                    $("#qv-stock").text(v.stock + " unidades");
                    $("#qv-reserva").text(v.reserva + " un.");
                    $("#qv-description").text(v.descripcion || "Sin descripción disponible.");
                    
                    const $gallery = $("#qv-gallery-thumbs");
                    $gallery.empty();
                    if (data.imagenes.length > 0) {
                        $("#qv-main-img").attr("src", ruta + data.imagenes[0].ruta_imagen);
                        data.imagenes.forEach((img, index) => {
                            $gallery.append(`<div class="quick-view-thumb ${index === 0 ? 'active' : ''}"><img src="${ruta}${img.ruta_imagen}" alt="Thumb"></div>`);
                        });
                    } else {
                        $("#qv-main-img").attr("src", ruta + "default.png");
                    }
                    const $attrContainer = $("#qv-attributes");
                    $attrContainer.empty();
                    if (data.atributos && data.atributos.length > 0) {
                        let attrHtml = '<div class="row row-cols-2 g-2">';
                        data.atributos.forEach(attr => {
                            attrHtml += `<div class="col"><div class="p-2 border rounded bg-light small"><span class="text-muted">${attr.nombre_atributo}:</span> <strong class="text-dark">${attr.valor}</strong></div></div>`;
                        });
                        attrHtml += '</div>';
                        $attrContainer.append(attrHtml);
                    }
                    
                    // [NUEVO] Generar texto para copiar (Expandido)
                    let extraAttrs = "";
                    if (data.atributos && data.atributos.length > 0) {
                        data.atributos.forEach(attr => {
                            extraAttrs += `🔹 ${attr.nombre_atributo}: ${attr.valor}\n`;
                        });
                    }

                     const copyText = `🛍️ *${nombrePadre}${nombreVariante ? ' - ' + nombreVariante : ''}*\n` +
                                     `📁 Categoría: ${v.nombre_categoria}\n` +
                                     `💰 PRECIO: $${parseFloat(v.precio_venta).toFixed(2)}\n` +
                                     extraAttrs +
                                     `${v.descripcion ? '\n📝 *Descripción:*\n' + v.descripcion : ''}`;
                    $("#qv-copy-text").val(copyText);

                    $("#modalQuickView").modal("show");
                    $("#btnSalidaFromQuick").data("id", id);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'No se pudo cargar el detalle.' });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                console.error("Error QuickView:", error);
                Swal.fire({ icon: 'error', title: 'Error de Conexión', text: 'No se pudo conectar con el servidor.' });
            }
        });
    }



    // Manejador para miniaturas en el modal de Home (con efecto fade)
    $(document).on("click", ".quick-view-thumb", function() {
        $(".quick-view-thumb").removeClass("active");
        $(this).addClass("active");
        const newSrc = $(this).find("img").attr("src");
        $("#qv-main-img").fadeOut(200, function() {
            $(this).attr("src", newSrc).fadeIn(200);
        });
    });

    // [NUEVO] Botón Copiar Información (Home)
    $(document).on("click", "#btn-copy-info", function() {
        const text = $("#qv-copy-text").val();
        navigator.clipboard.writeText(text).then(() => {
            const $btn = $(this);
            const originalIcon = $btn.html();
            $btn.html('<i class="fas fa-check text-success"></i>');
            setTimeout(() => $btn.html(originalIcon), 2000);
            
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '¡Copiado al portapapeles!',
                showConfirmButton: false,
                timer: 1500
            });
        });
    });

    // [NUEVO] Botón Descargar Imagen Actual (Home)
    $(document).on("click", "#btn-download-img", function() {
        const imgSrc = $("#qv-main-img").attr("src");
        if (!imgSrc || imgSrc.includes("default.png")) {
            Swal.fire("Aviso", "No hay una imagen válida para descargar.", "info");
            return;
        }

        const link = document.createElement("a");
        link.href = imgSrc;
        const fileName = imgSrc.split('/').pop();
        link.download = `AXStore_${fileName}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // Acción desde Quick View (Registro de Salida)
    $(document).on("click", "#btnSalidaFromQuick", function() {
        const id = $(this).data("id");
        const product = allProducts.find(p => p.id == id);
        
        if (product) {
            // Cerrar Quick View antes de abrir Salida
            $("#modalQuickView").modal("hide");
            setTimeout(() => abrirModalSalida(product), 500);
        }
    });

    function abrirModalSalida(product) {
        console.log("Abriendo Modal Salida (Home) para:", product);
        
        // Llenar campos ocultos
        $("#id_variante_salida").val(product.id);
        $("#precio_unitario_salida").val(product.precio_venta);
        
        // Llenar vista previa
        $("#nombreProductoSalida").text(product.nombre);
        $("#skuProductoSalida").text(product.sku);
        $("#precioProductoSalida").text("$" + parseFloat(product.precio_venta).toFixed(2));
        $("#stockProductoSalida").text(product.stock + " un.");
        $("#imgProductoSalida").attr("src", "app/views/assets/images/" + (product.imagen || 'default.png'));
        
        // Reiniciar formulario
        $("#formSalidaProducto")[0].reset();
        
        // Establecer fecha y hora actual
        const ahora = new Date();
        const offset = ahora.getTimezoneOffset() * 60000;
        const localISO = new Date(ahora.getTime() - offset).toISOString();
        $("#fecha_salida").val(localISO.split('T')[0]);
        $("#hora_salida").val(ahora.getHours().toString().padStart(2, '0') + ':' + ahora.getMinutes().toString().padStart(2, '0'));
        
        calcularTotales();
        $("#modalSalidaProducto").modal("show");
    }

    function calcularTotales() {
        const cant = parseInt($("#cantidad_salida").val()) || 0;
        const precio = parseFloat($("#precio_unitario_salida").val()) || 0;
        const envio = parseFloat($("#costo_envio").val()) || 0;
        const desc = parseFloat($("#descuento_salida").val()) || 0;
        
        const subtotal = cant * precio;
        const total = subtotal + envio - desc;
        
        $("#subtotalSalida").text("$" + subtotal.toFixed(2));
        $("#descuentoSalidaPreview").text("-$" + desc.toFixed(2));
        $("#totalSalida").text("$" + Math.max(0, total).toFixed(2));
    }

    // Google Maps dinámico
    $("#direccion_entrega").on("input", function() {
        const query = $(this).val().trim();
        const $container = $("#mapLinkContainer");
        const $link = $("#googleMapsLink");
        
        if (query.length > 3) {
            $link.attr("href", `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}`);
            $container.fadeIn();
        } else {
            $container.fadeOut();
        }
    });

    // Calcular totales al cambiar valores
    $(document).on("input", "#cantidad_salida, #costo_envio, #descuento_salida", calcularTotales);

    // Registrar Salida desde Home
    $("#formSalidaProducto").on("submit", function(e) {
        e.preventDefault();
        
        const $btn = $("#btnRegistrarSalida");
        $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin me-1"></i> Procesando...');
        
        const formData = new FormData(this);
        formData.append("accion", "registrarSalida");
        
        $.ajax({
            url: "app/controllers/salidaController.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    Swal.fire("¡Éxito!", response.message, "success").then(() => {
                        $("#modalSalidaProducto").modal("hide");
                        cargarDatosHome(); // Recargar datos para actualizar stock
                    });
                } else {
                    Swal.fire("Error", response.message, "error");
                }
            },
            error: function() {
                Swal.fire("Error", "Error de conexión con el servidor.", "error");
            },
            complete: function() {
                $btn.prop("disabled", false).html('<i class="fas fa-check me-1"></i> Registrar Entrega');
            }
        });
    });

    // ==========================================
    // 4. ANIMACIONES AL SCROLL
    // ==========================================
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.product-card, .feature-box').forEach(el => observer.observe(el));


    // ==========================================
    // [MODIFICADO] LISTENER PARA CERRAR TODO AL ABRIR PERFIL
    // ==========================================
    const userToggleBtn = document.querySelector('.user-icon [data-bs-toggle="dropdown"]');
    if (userToggleBtn) {
        userToggleBtn.addEventListener('click', function() {
            // Cerrar Menú Móvil manualmente
            if (typeof navmenu !== 'undefined' && navmenu.classList.contains('mobile-nav-active')) {
                navmenu.classList.remove('mobile-nav-active');
                if (mobileOverlay) mobileOverlay.classList.remove('active');
                document.body.classList.remove('mobile-nav-open');
                const icon = document.querySelector('.mobile-nav-toggle i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
            
            // Cerrar Carrito
            const cartModal = document.getElementById('cart-modal');
            if (cartModal) cartModal.style.display = 'none';
        });
    }

});



// ==========================================
    // BLOQUEO DE INSPECTOR Y CLIC DERECHO
    // ==========================================
    
    // Deshabilitar clic derecho
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    }, false);

    // Deshabilitar teclas de acceso rápido comunes (F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U)
    document.addEventListener('keydown', function(e) {
        // F12
        if (e.key === 'F12' || e.keyCode === 123) {
            e.preventDefault();
            return false;
        }
        
        // Combinaciones con Ctrl+Shift (I, J, C)
        if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C' || e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67)) {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+U (Ver código fuente)
        if (e.ctrlKey && (e.key === 'U' || e.keyCode === 85)) {
            e.preventDefault();
            return false;
        }
    });


    //scrip para div de envios y preguntas frecuentes


    document.addEventListener('DOMContentLoaded', function() {
        const accordionButtons = document.querySelectorAll('.accordion-button');
        accordionButtons.forEach(button => {
            button.addEventListener('click', function() {
                accordionButtons.forEach(btn => {
                    btn.closest('.accordion-item').classList.remove('border-luxury');
                });
                
                setTimeout(() => {
                    if (!this.classList.contains('collapsed')) {
                        this.closest('.accordion-item').classList.add('border-luxury');
                    }
                }, 100);
            });
        });
    });

    

// Función para descargar PDF tipo TICKET
async function descargarFichaProducto(product) {
    console.log("Iniciando descargarFichaProducto (Home)...");
    try {
        const { jsPDF } = window.jspdf;
        if (!jsPDF) throw new Error("jsPDF no está cargado correctamente.");

        // Formato Ticket (80mm x 150mm aprox)
        const doc = new jsPDF({
            unit: 'mm',
            format: [80, 160]
        });
    
    const pageWidth = 80;
    const margin = 5;
    const availableWidth = pageWidth - (margin * 2);
    
    // Header Ticket
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.text("AXStore", pageWidth / 2, 10, { align: "center" });
    
    doc.setFontSize(8);
    doc.setFont("helvetica", "normal");
    doc.text("******************************************", pageWidth / 2, 14, { align: "center" });
    doc.text("FICHA DE PRODUCTO", pageWidth / 2, 18, { align: "center" });
    doc.text("******************************************", pageWidth / 2, 22, { align: "center" });
    
    // Imagen del Producto (Centrada)
    let yPos = 25;
    try {
        const imgUrl = "app/views/assets/images/" + (product.imagen || 'default.png');
        const imgData = await getBase64ImageFromUrl(imgUrl);
        const imgSize = 50; 
        const xImg = (pageWidth - imgSize) / 2;
        doc.addImage(imgData, "JPEG", xImg, yPos, imgSize, imgSize);
        yPos += imgSize + 5;
    } catch (err) {
        console.error("Error cargando imagen para PDF:", err);
        yPos += 5;
    }
    
    // Datos del Producto
    doc.setFontSize(10);
    doc.setFont("helvetica", "bold");
    const splitTitle = doc.splitTextToSize(product.nombre.toUpperCase(), availableWidth);
    doc.text(splitTitle, pageWidth / 2, yPos, { align: "center" });
    yPos += (splitTitle.length * 5) + 2;
    
    doc.setFontSize(8);
    doc.setFont("helvetica", "normal");
    doc.text(`CAT: ${product.nombre_categoria}`, pageWidth / 2, yPos, { align: "center" });
    yPos += 4;
    doc.text(`SKU: ${product.sku}`, pageWidth / 2, yPos, { align: "center" });
    yPos += 8;
    
    // Precio (Grande)
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.text(`PRECIO: $${parseFloat(product.precio_venta).toFixed(2)}`, pageWidth / 2, yPos, { align: "center" });
    yPos += 10;
    
    // Línea divisoria
    doc.setFontSize(8);
    doc.text("------------------------------------------", pageWidth / 2, yPos, { align: "center" });
    yPos += 5;
    
    // Logo 
    try {
            const logoUrl = "app/views/assets/images/logo.png"; 
            const logoData = await getBase64ImageFromUrl(logoUrl);

            const logoW = 50; // ancho del logo en mm
            const logoH = 30; // alto del logo en mm 
            const xLogo = (pageWidth - logoW) / 2;

            doc.addImage(logoData, "PNG", xLogo, yPos, logoW, logoH);
            yPos += logoH + 5;
        } catch (err) {
            // Si falla la carga del logo, poner texto de respaldo
            console.warn("No se pudo cargar el logo, usando texto de respaldo:", err);
            doc.setFontSize(9);
            doc.setFont("helvetica", "bold");
            doc.text("AX STORE", pageWidth / 2, yPos + 5, { align: "center" });
            yPos += 15;
        }

    
    // Footer
    doc.setFontSize(7);
    doc.text("¡GRACIAS POR SU PREFERENCIA!", pageWidth / 2, yPos, { align: "center" });
    yPos += 4;
    doc.text(new Date().toLocaleString(), pageWidth / 2, yPos, { align: "center" });
    
    console.log("Guardando PDF (Home)...");
    doc.save(`Ticket_${product.sku}.pdf`);
    console.log("PDF guardado con éxito (Home).");

    } catch (error) {
        console.error("Error fatal generando PDF (Home):", error);
        Swal.fire({
            icon: 'error',
            title: 'Error al generar PDF',
            text: 'Hubo un problema al crear el ticket térmico.'
        });
    }
}

// Helper para imagen
function getBase64ImageFromUrl(url) {
    return new Promise((resolve, reject) => {
        var img = new Image();
        img.setAttribute("crossOrigin", "anonymous");
        img.onload = () => {
            var canvas = document.createElement("canvas");
            canvas.width = img.width;
            canvas.height = img.height;
            var ctx = canvas.getContext("2d");
            ctx.drawImage(img, 0, 0);
            var dataURL = canvas.toDataURL("image/jpeg");
            resolve(dataURL);
        };
        img.onerror = error => reject(error);
        img.src = url;
    });
}

// Estilos para notificaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }
`;
document.head.appendChild(style);