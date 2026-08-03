// ============================================================
// PROTECCIÓN CONTRA DOBLE CARGA DEL SCRIPT
// ============================================================
if (window.catalogoInicializado) {
    console.warn("catalogo.js ya fue cargado. Ignorando segunda carga.");
} else {
    window.catalogoInicializado = true;

const ruta = "http://localhost/AXStore2026_1/app/views/assets/images/";
let allProducts = [];
let filteredProducts = [];
let isRegistrando = false; // FLAG ANTI-DUPLICACIÓN

$(document).ready(function () {
    cargarTodosLosProductos();
    setupEvents();
    setFechaHoraActual();
});

function setFechaHoraActual() {
    const ahora = new Date();
    const offset = ahora.getTimezoneOffset() * 60000;
    const localISOTime = new Date(ahora.getTime() - offset).toISOString();
    const fecha = localISOTime.split('T')[0];
    const hora  = ahora.getHours().toString().padStart(2,'0') + ':' +
                  ahora.getMinutes().toString().padStart(2,'0');
    $("#fecha_salida").val(fecha);
    $("#hora_salida").val(hora);
}

function cargarTodosLosProductos() {
    $.ajax({
        url: "app/controllers/productoController.php",
        method: "POST",
        dataType: "json",
        data: { accion: "obtenerTodosLosProductosConVariantes" },
        success: function (response) {
            if (response.status === "success") {
                allProducts = response.data;
                filteredProducts = allProducts;
                cargarCategorias();
                renderProducts(filteredProducts);
                updateResultCount();

                const urlParams = new URLSearchParams(window.location.search);
                const abrirSalidaId = urlParams.get('abrirSalida');
                if (abrirSalidaId) {
                    const productToOpen = allProducts.find(p => p.id == abrirSalidaId);
                    if (productToOpen) {
                        setTimeout(() => abrirModalSalida(productToOpen), 500);
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }
                }
            } else {
                showNoResults();
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la carga:", error);
            $("#product-grid").html('<div class="col-12 text-center text-danger">Error al conectar con la base de datos.</div>');
        },
    });
}

function cargarCategorias() {
    const categorias = [...new Set(allProducts.map(p => p.nombre_categoria))];
    const $categorySelect = $("#categoryFilter");
    const $categoryNav    = $("#catalogo-categories-nav");

    $categorySelect.html('<option value="all">Todas las Categorías</option>');
    $categoryNav.empty();
    $categoryNav.append('<div class="catalog-pill active" data-category="all">TODOS</div>');

    categorias.sort().forEach(categoria => {
        $categorySelect.append(`<option value="${categoria}">${categoria}</option>`);
        $categoryNav.append(`<div class="catalog-pill" data-category="${categoria}">${categoria}</div>`);
    });
}

function aplicarFiltros() {
    const searchTerm       = $("#searchInput").val().toLowerCase().trim();
    const selectedCategory = $("#categoryFilter").val();
    const selectedStatus   = $("#statusFilter").val();

    filteredProducts = allProducts.filter(product => {
        const matchesSearch = searchTerm === "" ||
            product.nombre.toLowerCase().includes(searchTerm) ||
            product.sku.toLowerCase().includes(searchTerm) ||
            product.nombre_producto_padre.toLowerCase().includes(searchTerm);
        const matchesCategory = selectedCategory === "all" || product.nombre_categoria === selectedCategory;
        const matchesStatus   = selectedStatus === "all"   || product.estado == selectedStatus;
        return matchesSearch && matchesCategory && matchesStatus;
    });

    renderProducts(filteredProducts);
    updateResultCount();
}

function renderProducts(productsList) {
    const $productGrid = $("#product-grid");
    const $noResults   = $("#noResults");
    $productGrid.empty();

    if (productsList.length === 0) { showNoResults(); return; }

    $noResults.addClass("d-none");
    $productGrid.removeClass("d-none");

    $.each(productsList, function (i, product) {
        
        // Convertimos a números reales para evitar errores de lectura
        const reservaNum = parseInt(product.reserva) || 0;
        const stockNum = parseInt(product.stock) || 0;

        const precioFormateado = Number(product.precio_venta).toFixed(2);
        const mainImg  = `${ruta}${product.imagen}`;
        const hoverImg = product.imagen_hover ? `${ruta}${product.imagen_hover}` : mainImg;
        
        // CORRECCIÓN: El botón solo se bloquea si NO hay stock en Tienda
        const disabledBtn = stockNum <= 0 ? 'disabled' : '';

        // --- LÓGICA DE CARTELES CORREGIDA ---
        let cartelesExtra = '';
        
        if (stockNum <= 0 && reservaNum <= 0) {
            // No hay ni en tienda ni en bodega
            cartelesExtra = '<span class="badge-premium badge-low-stock"><i class="fas fa-times-circle me-1"></i> Agotado</span>';
        } else if (stockNum <= 0 && reservaNum > 0) {
            // No hay en tienda, pero SÍ hay en bodega
            cartelesExtra = '<span class="badge-premium badge-top"><i class="fas fa-truck-loading me-1"></i> Esperando bodega</span>';
        }

        const badgesHtml = `
            ${cartelesExtra}
            <span class="badge-premium badge-stock"><i class="fas fa-box me-1"></i>Tienda: ${product.stock}</span>
            <span class="badge-premium badge-reserva"><i class="fas fa-clock me-1"></i>Bodega: ${product.reserva}</span>
        `;

        $productGrid.append(`
            <div class="col">
                <div class="card h-100 border-0 shadow-sm transition-hover product-card">
                    <div class="product-badge-container">${badgesHtml}</div>
                    <div class="product-quick-actions">
                        <button class="btn-action-premium btnQuickView" data-id="${product.id}" title="Vista Rápida">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action-premium btnPdfDownload" data-id="${product.id}" title="Descargar Ficha PDF">
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
                            <span class="d-block text-success fw-bold" style="font-size:0.75rem;">Comisión: $${parseFloat(product.comision).toFixed(2)}</span>
                        </div>
                        <p class="text-muted small mb-1">${product.nombre_producto_padre}</p>
                        <h5 class="card-title fw-bold text-dark mb-3">${product.nombre}</h5>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="h4 mb-0 fw-bold text-primary">$${precioFormateado}</span>
                                <small class="text-muted fw-bold">SKU: ${product.sku}</small>
                            </div>
                            <button class="btn btn-success btn-sm w-100 btnSalidaProducto py-2"
                                    data-id="${product.id}" data-nombre="${product.nombre}"
                                    data-sku="${product.sku}" data-precio="${product.precio_venta}"
                                    data-stock="${product.stock}" data-imagen="${product.imagen}"
                                    ${disabledBtn}>
                                <i class="fas fa-check-circle me-1"></i> REGISTRAR ENTREGA
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `);
    });
}

// PDF ticket por producto
$(document).on("click", ".btnPdfDownload", function(e) {
    e.preventDefault();
    e.stopPropagation();
    const product = allProducts.find(p => p.id == $(this).data("id"));
    if (product) descargarFichaProducto(product);
});

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

// Helpers de imagen
function getBase64ImageFromUrl(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.setAttribute("crossOrigin", "anonymous");
        img.onload = () => {
            const canvas = document.createElement("canvas");
            canvas.width = img.width; canvas.height = img.height;
            canvas.getContext("2d").drawImage(img, 0, 0);
            resolve(canvas.toDataURL("image/jpeg"));
        };
        img.onerror = e => reject(e);
        img.src = url;
    });
}

function getBase64ImageFromURL(url) {
    return new Promise(resolve => {
        const img = new Image();
        img.setAttribute("crossOrigin", "anonymous");
        img.onload = () => {
            const canvas = document.createElement("canvas");
            canvas.width = img.width; canvas.height = img.height;
            canvas.getContext("2d").drawImage(img, 0, 0);
            resolve(canvas.toDataURL("image/png"));
        };
        img.onerror = () => resolve(null);
        img.src = url;
    });
}

function showNoResults() {
    $("#product-grid").addClass("d-none");
    $("#noResults").removeClass("d-none");
}

function updateResultCount() {
    $("#resultCount").text(filteredProducts.length);
}

// ============================================================
// SETUP EVENTS
// ============================================================
function setupEvents() {

    $(document).on("click", ".catalog-pill", function() {
        $(".catalog-pill").removeClass("active");
        $(this).addClass("active");
        $("#categoryFilter").val($(this).data("category"));
        aplicarFiltros();
    });

    $("#searchInput").on("keyup", aplicarFiltros);

    $("#categoryFilter").on("change", function() {
        $(`.catalog-pill[data-category="${$(this).val()}"]`).click();
    });

    $("#statusFilter").on("change", aplicarFiltros);

    $("#clearSearch").on("click", function() {
        $("#searchInput").val("");
        $(".catalog-pill").first().click();
        aplicarFiltros();
    });

    $(document).on("click", ".btnSalidaProducto", function() {
        abrirModalSalida({
            id:     $(this).data("id"),
            nombre: $(this).data("nombre"),
            sku:    $(this).data("sku"),
            precio: parseFloat($(this).data("precio")),
            stock:  parseInt($(this).data("stock")),
            imagen: $(this).data("imagen")
        });
    });

    $("#cantidad_salida, #descuento_salida").on("input", calcularTotales);

    $(document).on("click", ".btnQuickView", function() {
        abrirQuickView($(this).data("id"));
    });

    $(document).on("click", ".quick-view-thumb", function() {
        $(".quick-view-thumb").removeClass("active");
        $(this).addClass("active");
        const src = $(this).find("img").attr("src");
        $("#qv-main-img").fadeOut(200, function() { $(this).attr("src", src).fadeIn(200); });
    });

    $(document).on("click", "#btn-copy-info", function() {
        navigator.clipboard.writeText($("#qv-copy-text").val()).then(() => {
            const $btn = $(this), orig = $btn.html();
            $btn.html('<i class="fas fa-check text-success"></i>');
            setTimeout(() => $btn.html(orig), 2000);
            Swal.fire({ toast:true, position:'top-end', icon:'success', title:'¡Copiado!', showConfirmButton:false, timer:1500 });
        });
    });

    $(document).on("click", "#btn-download-img", function() {
        const imgSrc = $("#qv-main-img").attr("src");
        if (!imgSrc || imgSrc.includes("default.png")) { Swal.fire("Aviso","No hay imagen válida.","info"); return; }
        const link = document.createElement("a");
        link.href = imgSrc;
        link.download = `AXStore_${imgSrc.split('/').pop()}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    $("#direccion").on("input", function() {
        const q = $(this).val().trim(), $btn = $("#verifyAddressBtn");
        if (q.length > 3) {
            $btn.attr("href", `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(q)}`).removeClass("d-none");
        } else {
            $btn.addClass("d-none");
        }
    });

    $("#modalSalidaProducto").on("show.bs.modal hidden.bs.modal", function() {
        $("#verifyAddressBtn").addClass("d-none").attr("href","#");
    });

    // ============================================================
    // Reset completo al cerrar el modal
    // ============================================================
    $("#modalSalidaProducto").on("hidden.bs.modal", function () {
        $("#formSalidaProducto")[0].reset();
        limpiarValidaciones();
        isRegistrando = false;
        $("#btnRegistrarSalida")
            .data("procesando", false)
            .prop("disabled", false)
            .html('<i class="fas fa-check me-1"></i> Registrar Entrega');
    });

    // Validaciones blur
    $("#cantidad_salida").on("blur", validarCantidadVsStock);
    $("#fecha_salida").on("blur change", function() {
        validarFechaSalida();
        if ($("#fecha_entrega").val()) validarFechaEntrega();
    });
    $("#hora_salida").on("blur", validarHoraSalida);
    $("#fecha_entrega").on("blur change", validarFechaEntrega);
    $("#direccion").on("blur", validarDireccion);
    $("#precio_envio").on("blur", validarPrecioEnvio);
    $("#costo_extra").on("blur", function() { validarNumeroDecimal("costo_extra","Costo extra",false); });

    // ============================================================
    // FIX DEFINITIVO ANTI-DUPLICADO:
    // 1. El form submit queda BLOQUEADO completamente
    // 2. El botón usa .off().on() para limpiar cualquier handler previo
    //    y manejar el click directamente sin pasar por el submit
    // ============================================================
    $("#formSalidaProducto").off("submit").on("submit", function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    });

    $("#btnRegistrarSalida").off("click").on("click", function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        registrarSalida();
    });

    $("#btnExportarPDF").on("click", generarPDFCatalogo);
}

// ============================================================
// MODAL DE SALIDA
// ============================================================
function abrirModalSalida(producto) {
    // Limpiar flags antes de abrir por si quedaron sucios
    isRegistrando = false;
    $("#btnRegistrarSalida")
        .data("procesando", false)
        .prop("disabled", false)
        .html('<i class="fas fa-check me-1"></i> Registrar Entrega');

    $("#id_variante_salida").val(producto.id);
    $("#precio_unitario_salida").val(producto.precio);
    $("#nombreProductoSalida").text(producto.nombre);
    $("#skuProductoSalida").text(producto.sku);
    $("#precioProductoSalida").text("$" + producto.precio.toFixed(2));
    $("#stockProductoSalida").text(producto.stock + " unidades");
    $("#imgProductoSalida").attr("src", ruta + producto.imagen);
    $("#cantidad_salida").attr("max", producto.stock).val(1);
    $("#descuento_salida").val("0.00");
    setFechaHoraActual();
    calcularTotales();
    limpiarValidaciones();

    // getOrCreateInstance evita acumular instancias Bootstrap
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalSalidaProducto')).show();
}

// ============================================================
// CALCULAR TOTALES
// ============================================================
function calcularTotales() {
    const cantidad       = parseFloat($("#cantidad_salida").val()) || 0;
    const precioUnitario = parseFloat($("#precio_unitario_salida").val()) || 0;
    const descuento      = parseFloat($("#descuento_salida").val()) || 0;
    const subtotal       = cantidad * precioUnitario;
    const total          = Math.max(0, subtotal - descuento);

    $("#subtotalSalida").text("$" + subtotal.toFixed(2));
    $("#descuentoSalidaPreview").text("-$" + descuento.toFixed(2));
    $("#totalSalida").text("$" + total.toFixed(2));

    if (descuento > subtotal && subtotal > 0) {
        mostrarError("descuento_salida", `El descuento no puede superar el subtotal ($${subtotal.toFixed(2)})`);
    } else {
        $("#descuento_salida").removeClass("is-invalid").siblings(".invalid-feedback").remove();
    }
}

// ============================================================
// VALIDACIONES
// ============================================================
function mostrarError(fieldId, message) {
    const $f = $("#" + fieldId);
    $f.removeClass("is-valid").addClass("is-invalid").siblings(".invalid-feedback").remove();
    $f.after(`<div class="invalid-feedback d-block">${message}</div>`);
}

function mostrarValido(fieldId) {
    $("#" + fieldId).removeClass("is-invalid").addClass("is-valid").siblings(".invalid-feedback").remove();
}

function limpiarValidaciones() {
    $("#formSalidaProducto").find(".is-invalid,.is-valid").removeClass("is-invalid is-valid");
    $("#formSalidaProducto").find(".invalid-feedback").remove();
}

function validarCantidadVsStock() {
    const cantidad = parseInt($("#cantidad_salida").val());
    const stock    = parseInt($("#stockProductoSalida").text());
    if (isNaN(cantidad) || cantidad <= 0) { mostrarError("cantidad_salida","Ingrese una cantidad válida"); return false; }
    if (cantidad > stock) { mostrarError("cantidad_salida",`Solo hay ${stock} unidades disponibles`); return false; }
    mostrarValido("cantidad_salida"); return true;
}

function validarFechaSalida() {
    const v = $("#fecha_salida").val();
    if (!v) { mostrarError("fecha_salida","La fecha de salida es obligatoria"); return false; }
    const hoy = new Date(); hoy.setHours(0,0,0,0);
    if (new Date(v+"T00:00:00") > hoy) { mostrarError("fecha_salida","La fecha de salida no puede ser futura"); return false; }
    mostrarValido("fecha_salida"); return true;
}

function validarFechaEntrega() {
    const fe = $("#fecha_entrega").val();
    if (!fe || !fe.trim()) { mostrarError("fecha_entrega","La fecha de entrega es obligatoria"); return false; }
    const fs = $("#fecha_salida").val();
    if (!fs) { mostrarError("fecha_entrega","Primero seleccione una fecha de salida"); return false; }
    if (new Date(fe+"T00:00:00") < new Date(fs+"T00:00:00")) {
        mostrarError("fecha_entrega","La fecha de entrega no puede ser anterior a la de salida"); return false;
    }
    mostrarValido("fecha_entrega"); return true;
}

function validarHoraSalida() {
    if (!$("#hora_salida").val()) { mostrarError("hora_salida","La hora de salida es obligatoria"); return false; }
    mostrarValido("hora_salida"); return true;
}

function validarNumeroDecimal(fieldId, fieldName, esObligatorio = false) {
    const raw = $("#"+fieldId).val().trim(), valor = parseFloat(raw);
    if (esObligatorio && (isNaN(valor) || raw === "")) { mostrarError(fieldId,`${fieldName} es obligatorio`); return false; }
    if (!esObligatorio && (isNaN(valor) || raw === "")) { mostrarValido(fieldId); return true; }
    if (isNaN(valor) || valor < 0) { mostrarError(fieldId,`${fieldName} debe ser un número válido ≥ 0`); return false; }
    mostrarValido(fieldId); return true;
}

function validarPrecioEnvio() {
    const raw = $("#precio_envio").val().trim(), valor = parseFloat(raw);
    if (isNaN(valor) || raw === "") { mostrarError("precio_envio","El precio de envío es obligatorio"); return false; }
    if (valor <= 0) { mostrarError("precio_envio","El precio de envío debe ser mayor a 0"); return false; }
    mostrarValido("precio_envio"); return true;
}

function validarDireccion() {
    const d = $("#direccion").val().trim();
    if (!d) { mostrarError("direccion","La dirección de entrega es obligatoria"); return false; }
    if (d.length < 10) { mostrarError("direccion","La dirección debe tener al menos 10 caracteres"); return false; }
    mostrarValido("direccion"); return true;
}

function validarFormularioCompleto() {
    let ok = true;
    limpiarValidaciones();
    ok = validarCantidadVsStock() && ok;
    ok = validarFechaSalida()     && ok;
    ok = validarHoraSalida()      && ok;

    const cantidad  = parseFloat($("#cantidad_salida").val()) || 0;
    const precio    = parseFloat($("#precio_unitario_salida").val()) || 0;
    const subtotal  = cantidad * precio;
    const descuento = parseFloat($("#descuento_salida").val()) || 0;
    if (descuento > subtotal) {
        mostrarError("descuento_salida",`El descuento ($${descuento.toFixed(2)}) no puede superar el subtotal ($${subtotal.toFixed(2)})`);
        ok = false;
    }

    if (!ok) { const $err = $("#formSalidaProducto .is-invalid").first(); if ($err.length) $err.focus(); }
    return ok;
}

// ============================================================
// REGISTRAR SALIDA — DOBLE BLOQUEO: flag JS + atributo DOM
// ============================================================
function registrarSalida() {
    const $btn = $("#btnRegistrarSalida");

    // Doble candado: variable JS + atributo del DOM
    if (isRegistrando || $btn.data("procesando") === true) {
        console.warn("Registro ya en proceso, ignorando llamada duplicada.");
        return;
    }

    if (!validarFormularioCompleto()) {
        Swal.fire({ icon:'warning', title:'Formulario Incompleto', text:'Corrija los errores señalados', confirmButtonColor:'#ffc107' });
        return;
    }

    // Activar ambos bloqueos de forma simultánea
    isRegistrando = true;
    $btn.data("procesando", true);
    $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin me-1"></i> Procesando...');

    const cantidad   = parseInt($("#cantidad_salida").val());
    const precioUnit = parseFloat($("#precio_unitario_salida").val());
    const descuento  = parseFloat($("#descuento_salida").val() || 0);

    const formData = new FormData();
    formData.append("accion",          "registrarSalida");
    formData.append("id_variante",     $("#id_variante_salida").val());
    formData.append("cantidad",        cantidad);
    formData.append("fecha_salida",    $("#fecha_salida").val());
    formData.append("hora_salida",     $("#hora_salida").val());
    formData.append("precio_unitario", precioUnit.toFixed(2));
    formData.append("descuento",       descuento.toFixed(2));

    $.ajax({
        url: "app/controllers/salidaController.php",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                Swal.fire({ icon:'success', title:'¡Salida Registrada!', text:response.message, confirmButtonColor:'#28a745' })
                .then(() => {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalSalidaProducto')).hide();
                    cargarTodosLosProductos();
                });
            } else {
                Swal.fire({ icon:'error', title:'Error', text:response.message, confirmButtonColor:'#dc3545' });
            }
        },
        error: function() {
            Swal.fire({ icon:'error', title:'Error de Conexión', text:'No se pudo conectar con el servidor', confirmButtonColor:'#dc3545' });
        },
        complete: function() {
            // Liberar siempre, pase lo que pase
            isRegistrando = false;
            $btn.data("procesando", false);
            $btn.prop("disabled", false).html('<i class="fas fa-check me-1"></i> Registrar Entrega');
        }
    });
}

// ============================================================
// QUICK VIEW
// ============================================================
function abrirQuickView(id) {
    $.ajax({
        url: "app/controllers/productoController.php",
        type: "POST",
        data: { accion: "obtenerDetalleQuickView", id: id },
        dataType: "json",
        beforeSend: function() {
            Swal.fire({ title:'Cargando...', allowOutsideClick:false, didOpen:() => { Swal.showLoading(); } });
        },
        success: function(response) {
            Swal.close();
            if (response.status !== "success") { Swal.fire("Error", response.message, "error"); return; }

            const data = response.data, v = data.variante;
            const nombrePadre    = v.nombre_producto_padre || "Producto";
            const nombreVariante = v.nombre || "";

            $("#qv-category").text(v.nombre_categoria);
            $("#qv-sku").text("SKU: " + v.sku);
            $("#qv-name").html(`<small class="text-muted d-block fs-6 mb-1">${nombrePadre}</small>${nombreVariante}`);
            $("#qv-price").text("$" + parseFloat(v.precio_venta).toFixed(2));
            $("#qv-stock").text(v.stock + " un.");
            $("#qv-reserva").text(v.reserva + " un.");
            $("#qv-description").text(v.descripcion || "Sin descripción.");

            const $gallery = $("#qv-gallery-thumbs").empty();
            const rutaImg  = "app/views/assets/images/";
            if (data.imagenes.length > 0) {
                $("#qv-main-img").attr("src", rutaImg + data.imagenes[0].ruta_imagen);
                data.imagenes.forEach((img, idx) => {
                    $gallery.append(`<div class="quick-view-thumb ${idx===0?'active':''}"><img src="${rutaImg}${img.ruta_imagen}"></div>`);
                });
            } else {
                $("#qv-main-img").attr("src", rutaImg + "default.png");
            }

            const $attr = $("#qv-attributes").empty();
            if (data.atributos.length > 0) {
                let html = '<div class="d-flex flex-wrap gap-2">';
                data.atributos.forEach(a => { html += `<span class="badge bg-light text-dark border">${a.nombre_atributo}: ${a.valor}</span>`; });
                $attr.html(html + '</div>');
            }

            let extraAttrs = "";
            (data.atributos || []).forEach(a => { extraAttrs += `🔹 ${a.nombre_atributo}: ${a.valor}\n`; });
            $("#qv-copy-text").val(
                `🛍️ *${nombrePadre}${nombreVariante ? ' - '+nombreVariante : ''}*\n` +
                `📁 Categoría: ${v.nombre_categoria}\n` +
                `💰 PRECIO: $${parseFloat(v.precio_venta).toFixed(2)}\n` +
                extraAttrs +
                `${v.descripcion ? '\n📝 *Descripción:*\n'+v.descripcion : ''}`
            );

            $("#btnSalidaFromQuick").data("id", id);
            $("#modalQuickView").modal("show");
        },
        error: function() { Swal.close(); Swal.fire("Error","No se pudo conectar","error"); }
    });
}

// ============================================================
// PDF CATÁLOGO VISUAL
// ============================================================
async function generarPDFCatalogo() {
    if (allProducts.length === 0) {
        Swal.fire({ icon:'warning', title:'Sin Datos', text:'No hay productos para generar el catálogo', confirmButtonColor:'#ffc107' });
        return;
    }

    Swal.fire({ title:'Generando Catálogo Visual', text:'Procesando imágenes...', allowOutsideClick:false, didOpen:() => { Swal.showLoading(); } });

    try {
        const byCategoria = {};
        allProducts.forEach(p => {
            const cat = p.nombre_categoria || "Sin Categoría";
            if (!byCategoria[cat]) byCategoria[cat] = [];
            byCategoria[cat].push(p);
        });

        const content = [
            { text:'CATÁLOGO VISUAL DE PRODUCTOS', style:'header' },
            { text:'Generado el: ' + new Date().toLocaleDateString(), style:'subheader' },
            { text:'\n' }
        ];

        for (const cat of Object.keys(byCategoria).sort()) {
            content.push({ text:cat.toUpperCase(), style:'categoryTitle' });
            const prods = byCategoria[cat].sort((a,b) => a.nombre.localeCompare(b.nombre));

            for (let i = 0; i < prods.length; i += 3) {
                const cols = [];
                for (const p of prods.slice(i, i+3)) {
                    const b64 = await getBase64ImageFromURL(p.imagen ? `${ruta}${p.imagen}` : `${ruta}default.png`);
                    cols.push({
                        stack: [
                            b64 ? { image:b64, width:100, height:100, alignment:'center', margin:[0,5,0,5] }
                                : { text:'\n(Sin imagen)\n', alignment:'center', fontSize:8, margin:[0,40,0,40] },
                            { text:p.nombre_categoria || cat, style:'prodCategory' },
                            { text:p.nombre,                  style:'prodName' },
                            { text:'$'+parseFloat(p.precio_venta).toFixed(2), style:'prodPrice' }
                        ],
                        width:'33%', margin:[5,5,5,15],
                        canvas:[{ type:'rect', x:0, y:0, w:160, h:175, r:5, lineWidth:0.5, lineColor:'#eee' }]
                    });
                }
                while (cols.length < 3) cols.push({ text:'', width:'33%' });
                content.push({ columns:cols, columnGap:10 });
            }
            content.push({ text:'\n', pageBreak:'after' });
        }

        if (content.length) delete content[content.length-1].pageBreak;

        window.pdfMake.createPdf({
            content,
            pageSize:'A4',
            pageMargins:[40,60,40,60],
            styles:{
                header:        { fontSize:24, bold:true, alignment:'center', color:'#dc3545', margin:[0,0,0,5] },
                subheader:     { fontSize:10, alignment:'center', color:'#666', margin:[0,0,0,30] },
                categoryTitle: { fontSize:16, bold:true, color:'#333', background:'#f4f4f4', margin:[0,20,0,15] },
                prodCategory:  { fontSize:7, bold:true, alignment:'center', color:'#666', margin:[0,2,0,2] },
                prodName:      { fontSize:9, bold:true, alignment:'center', margin:[0,2,0,2], color:'#222' },
                prodPrice:     { fontSize:11, bold:true, alignment:'center', color:'#dc3545', margin:[0,0,0,5] }
            },
            footer:(cur,total) => ({ text:`Página ${cur} de ${total}`, alignment:'center', fontSize:9, margin:[0,20,0,0], color:'#888' })
        }).download('Catalogo_Visual_'+new Date().toISOString().split('T')[0]+'.pdf');

        Swal.close();
        Swal.fire({ icon:'success', title:'¡Catálogo Creado!', timer:3000, showConfirmButton:false });

    } catch(error) {
        console.error("Error PDF:", error);
        Swal.close();
        Swal.fire({ icon:'error', title:'Error de Generación', confirmButtonColor:'#dc3545' });
    }
}

} 