const ruta = "http://localhost/AXStore2026_1/app/views/assets/images/";
let allSalidas = [];
let filteredSalidas = [];
let currentPage = 1;
const itemsPerPage = 9;
let currentSalidaDetail = null; // Para almacenar la salida abierta en el modal

$(document).ready(function () {
    cargarTodasLasSalidas();
    setupEvents();
    setFechasIniciales();
});

// Establecer fechas iniciales (últimos 30 días)
function setFechasIniciales() {
    const hoy = new Date();
    const hace30Dias = new Date();
    hace30Dias.setDate(hoy.getDate() - 30);

    const offset = hoy.getTimezoneOffset() * 60000;
    const fechaHasta = new Date(hoy.getTime() - offset).toISOString().split('T')[0];
    const fechaDesde = new Date(hace30Dias.getTime() - offset).toISOString().split('T')[0];

    $("#fechaHasta").val(fechaHasta);
    $("#fechaDesde").val(fechaDesde);
}

// Cargar todas las salidas
function cargarTodasLasSalidas() {
    $.ajax({
        url: "app/controllers/salidaController.php",
        method: "POST",
        dataType: "json",
        data: { accion: "obtenerTodasLasSalidas" },
        success: function (response) {
            if (response.status === "success") {
                allSalidas = response.data;
                filteredSalidas = allSalidas;
                aplicarFiltros();
                calcularEstadisticas();
            } else {
                console.error("Error al cargar salidas");
                showNoResults();
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la carga:", error);
            console.error("Respuesta del servidor:", xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo cargar el historial de salidas',
                confirmButtonColor: '#dc3545'
            });
        },
    });
}

// Calcular estadísticas (Solo entregadas)
function calcularEstadisticas() {
    // El usuario solo quiere trabajar con Entregados
    const entregas = allSalidas.filter(s => s.estado === 'Entregado');

    const total = entregas.length;
    const montoTotal = entregas.reduce((sum, s) => sum + parseFloat(s.total || 0), 0);
    const unidadesTotales = entregas.reduce((sum, s) => sum + parseInt(s.cantidad || 0), 0);

    // Entregas de hoy
    const offset = new Date().getTimezoneOffset() * 60000;
    const hoy = new Date(new Date().getTime() - offset).toISOString().split('T')[0];
    const salidasHoy = entregas.filter(s => s.fecha_salida === hoy).length;

    $("#totalSalidas").text(total);
    $("#montoTotal").text("$" + montoTotal.toFixed(2));
    $("#unidadesTotales").text(unidadesTotales);
    $("#salidasHoy").text(salidasHoy);
}

// Aplicar filtros
function aplicarFiltros() {
    const searchTerm = $("#searchInput").val().toLowerCase().trim();
    const fechaDesde = $("#fechaDesde").val();
    const fechaHasta = $("#fechaHasta").val();
    const ordenar = $("#ordenar").val();

    // Forzar estado 'Entregado' ya que se eliminó la pestaña de cancelados
    const activeStatus = "Entregado";

    filteredSalidas = allSalidas.filter(salida => {
        const estado = salida.estado || 'Pendiente';
        const matchesSearch = searchTerm === "" ||
            salida.sku.toLowerCase().includes(searchTerm) ||
            salida.nombre_producto.toLowerCase().includes(searchTerm) ||
            (salida.observaciones && salida.observaciones.toLowerCase().includes(searchTerm));

        const matchesFechaDesde = !fechaDesde || salida.fecha_salida >= fechaDesde;
        const matchesFechaHasta = !fechaHasta || salida.fecha_salida <= fechaHasta;

        // Solo mostrar entregados
        const matchesTab = (estado === 'Entregado');

        return matchesSearch && matchesFechaDesde && matchesFechaHasta && matchesTab;
    });

    // Ordenar
    switch (ordenar) {
        case 'fecha_desc':
            filteredSalidas.sort((a, b) => {
                const dateA = new Date(a.fecha_salida + ' ' + a.hora_salida);
                const dateB = new Date(b.fecha_salida + ' ' + b.hora_salida);
                return dateB - dateA;
            });
            break;
        case 'fecha_asc':
            filteredSalidas.sort((a, b) => {
                const dateA = new Date(a.fecha_salida + ' ' + a.hora_salida);
                const dateB = new Date(b.fecha_salida + ' ' + b.hora_salida);
                return dateA - dateB;
            });
            break;
        case 'monto_desc':
            filteredSalidas.sort((a, b) => parseFloat(b.total) - parseFloat(a.total));
            break;
        case 'monto_asc':
            filteredSalidas.sort((a, b) => parseFloat(a.total) - parseFloat(b.total));
            break;
    }

    currentPage = 1;
    renderSalidas();
    updateResultCount();
}

// Renderizar salidas con paginación
function renderSalidas() {
    const $salidasGrid = $("#salidas-grid");
    const $noResults = $("#noResults");

    $salidasGrid.empty();

    if (filteredSalidas.length === 0) {
        showNoResults();
        $("#paginationContainer").addClass("d-none");
        return;
    }

    $noResults.addClass("d-none");
    $salidasGrid.removeClass("d-none");

    // Calcular índices para paginación
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedSalidas = filteredSalidas.slice(startIndex, endIndex);

    $.each(paginatedSalidas, function (i, salida) {
        const card = crearCardSalida(salida);
        $salidasGrid.append(card);
    });

    renderPagination();
}

// Obtener badge de estado
// Obtener badge de estado (con lógica de vencimiento)
function getBadgeEstado(estado, fechaEntrega = null) {
    // Ya no hay lógica de vencimiento porque todo es entregado
    let badgeEstado = '';
    switch (estado) {
        case 'Entregado':
            badgeEstado = `<span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3"><i class="fas fa-check-circle me-1"></i>Entregado</span>`;
            break;
        case 'Cancelado':
            badgeEstado = `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3"><i class="fas fa-ban me-1"></i>Cancelado</span>`;
            break;
        case 'En camino':
            badgeEstado = `<span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle rounded-pill px-3"><i class="fas fa-truck me-1"></i>En camino</span>`;
            break;
        case 'Pendiente':
            badgeEstado = `<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill px-3"><i class="fas fa-clock me-1"></i>Pendiente</span>`;
            break;
        default:
            badgeEstado = `<span class="badge bg-info bg-opacity-10 text-info border border-info-subtle rounded-pill px-3">${estado}</span>`;
    }
    return badgeEstado;
}

// Verificar si puede devolver (frontend)
function puedeDevolver(salida) {
    const estado = salida.estado || 'Pendiente';

    // No puede devolver si ya está cancelado
    if (estado === 'Cancelado') {
        return {
            puede: false,
            motivo: 'Salida cancelada'
        };
    }

    // Validar plazo de 7 días
    if (salida.fecha_salida) {
        const fechaSalida = new Date(salida.fecha_salida + 'T00:00:00');
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        const diffTime = hoy - fechaSalida;
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays > 7) {
            return {
                puede: false,
                motivo: `Plazo vencido (${diffDays} días)`,
                diasTranscurridos: diffDays
            };
        }

        return {
            puede: true,
            motivo: 'Devolución permitida',
            diasRestantes: 7 - diffDays
        };
    }

    return {
        puede: true,
        motivo: 'Devolución permitida',
        diasRestantes: 7
    };
}

// Crear card de salida
function crearCardSalida(salida) {
    const total = parseFloat(salida.total);
    const subtotal = parseFloat(salida.subtotal);
    const descuento = parseFloat(salida.descuento || 0);

    // Formatear fecha y hora
    const fechaSalida = new Date(salida.fecha_salida + ' ' + salida.hora_salida);
    const fechaFormateada = fechaSalida.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
    const horaFormateada = salida.hora_salida;

    // Manejar imagen
    const imagenSrc = salida.imagen ? `${ruta}${salida.imagen}` : `${ruta}default.png`;

    // Estado de la salida
    const estado = salida.estado || 'Pendiente';
    const badgeEstado = getBadgeEstado(estado);

    // Info de cancelación/devolución para la card
    let infoExtraCard = '';
    if (estado === 'Cancelado' && salida.fecha_cancelacion) {
        const fCanc = new Date(salida.fecha_cancelacion);
        infoExtraCard = `
            <div class="mt-2 py-1 px-2 bg-danger bg-opacity-10 border border-danger-subtle rounded small">
                <p class="mb-0 text-danger" style="font-size: 0.7rem;">
                    <i class="fas fa-undo-alt me-1"></i><strong>Devuelto el:</strong> ${fCanc.toLocaleDateString()}
                </p>
                ${salida.observaciones ? `<p class="mb-0 text-muted" style="font-size: 0.65rem;">${salida.observaciones}</p>` : ''}
            </div>
        `;
    } else if (salida.observaciones && salida.observaciones.toLowerCase().includes('devolución')) {
        // Para devoluciones parciales que siguen 'Entregado'
        infoExtraCard = `
            <div class="mt-2 py-1 px-2 bg-warning bg-opacity-10 border border-warning-subtle rounded small">
                <p class="mb-0 text-warning-emphasis" style="font-size: 0.7rem;">
                    <i class="fas fa-exclamation-circle me-1"></i><strong>Nota:</strong> ${salida.observaciones}
                </p>
            </div>
        `;
    }

    // Verificar si puede devolver
    const validacionDevolucion = puedeDevolver(salida);
    const puedeDevol = validacionDevolucion.puede;

    return `
        <div class="col-md-6 col-lg-4">
            <div class="card card-salida h-100 border-0 shadow-sm">
                <div class="card-body">
                    <!-- Header con fecha y estado -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-danger">ID: ${salida.id}</span>
                            ${badgeEstado}
                            <p class="text-muted small mb-0 mt-1">
                                <i class="far fa-calendar me-1"></i>${fechaFormateada}
                                <i class="far fa-clock ms-2 me-1"></i>${horaFormateada}
                            </p>
                            ${infoExtraCard}
                        </div>
                        <button class="btn btn-sm btn-outline-primary btnVerDetalle" 
                                data-id="${salida.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <!-- Producto -->
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <img src="${imagenSrc}" 
                                 class="rounded border" 
                                 style="width: 60px; height: 60px; object-fit: cover;" 
                                 alt="${salida.nombre_producto}"
                                 onerror="this.src='${ruta}default.png'">
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1">${salida.nombre_producto}</h6>
                            <p class="text-muted small mb-0">SKU: ${salida.sku}</p>
                            <span class="badge bg-secondary mt-1">${salida.cantidad} unidades</span>
                        </div>
                    </div>

                    <!-- Detalles financieros -->
                    <div class="border-top pt-3">
                        <div class="info-item">
                            <span class="text-muted small">Subtotal:</span>
                            <strong class="small">$${subtotal.toFixed(2)}</strong>
                        </div>
                        ${descuento > 0 ? `
                        <div class="info-item">
                            <span class="text-muted small">Descuento:</span>
                            <strong class="small text-danger">-$${descuento.toFixed(2)}</strong>
                        </div>
                        ` : ''}
                        <div class="info-item pt-2">
                            <span class="fw-bold">TOTAL:</span>
                            <strong class="text-danger fs-5">$${total.toFixed(2)}</strong>
                        </div>
                    </div>

                    <!-- Botón de devolución -->
                    ${puedeDevol ? `
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-danger w-100 btnDevolverSalida" 
                                data-id="${salida.id}"
                                data-cantidad="${salida.cantidad}"
                                data-variante="${salida.id_variante}"
                                data-nombre="${salida.nombre_producto}">
                            <i class="fas fa-undo me-1"></i>Devolver Stock
                        </button>
                    </div>
                    ` : estado !== 'Cancelado' ? `
                    <div class="mt-3">
                        <button class="btn btn-sm btn-secondary w-100" disabled>
                            <i class="fas fa-ban me-1"></i>${validacionDevolucion.motivo}
                        </button>
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

// Renderizar paginación
function renderPagination() {
    const totalPages = Math.ceil(filteredSalidas.length / itemsPerPage);
    const $pagination = $("#pagination");
    const $paginationContainer = $("#paginationContainer");

    $pagination.empty();

    if (totalPages <= 1) {
        $paginationContainer.addClass("d-none");
        return;
    }

    $paginationContainer.removeClass("d-none");

    // Botón anterior
    $pagination.append(`
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `);

    // Números de página
    for (let i = 1; i <= totalPages; i++) {
        if (
            i === 1 ||
            i === totalPages ||
            (i >= currentPage - 1 && i <= currentPage + 1)
        ) {
            $pagination.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            $pagination.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
        }
    }

    // Botón siguiente
    $pagination.append(`
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `);
}

// Ver detalle de salida
function verDetalleSalida(id) {
    $.ajax({
        url: "app/controllers/salidaController.php",
        method: "POST",
        dataType: "json",
        data: {
            accion: "obtenerDetalleSalida",
            id: id
        },
        success: function (response) {
            if (response.status === "success") {
                mostrarModalDetalle(response.data);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar el detalle',
                confirmButtonColor: '#dc3545'
            });
        }
    });
}

// Mostrar modal con detalle completo
function mostrarModalDetalle(salida) {
    currentSalidaDetail = salida; // Guardar para impresión
    const total = parseFloat(salida.total);
    const subtotal = parseFloat(salida.subtotal);
    const descuento = parseFloat(salida.descuento || 0);
    const precioUnitario = parseFloat(salida.precio_unitario);

    const fechaSalida = new Date(salida.fecha_salida + ' ' + salida.hora_salida);
    const fechaFormateada = fechaSalida.toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    // Manejar imagen
    const imagenSrc = salida.imagen ? `${ruta}${salida.imagen}` : `${ruta}default.png`;

    const estado = salida.estado || 'Pendiente';
    const badgeEstado = getBadgeEstado(estado, salida.fecha_entrega);

    // Información de devolución: Validar plazo de 7 días
    const validacion = puedeDevolver(salida);
    let infoDevolucion = '';
    if (estado === 'Cancelado') {
        const fechaCanc = salida.fecha_cancelacion ? new Date(salida.fecha_cancelacion).toLocaleDateString('es-ES', {
            year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
        }) : 'No registrada';

        infoDevolucion = `
            <div class="alert alert-danger mb-4 shadow-sm border-0">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="fas fa-ban fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Salida Cancelada / Devuelta</h6>
                        <p class="mb-1 small"><strong>Fecha:</strong> ${fechaCanc}</p>
                        ${salida.observaciones ? `<p class="mb-0 small"><strong>Motivo:</strong> ${salida.observaciones}</p>` : ''}
                    </div>
                </div>
            </div>
        `;
    }
    else if (!validacion.puede) {
        infoDevolucion = `
            <div class="alert alert-warning mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Plazo Vencido:</strong> ${validacion.motivo}. La devolución ya no es posible.
            </div>
        `;
    } else {
        infoDevolucion = `
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Devolución disponible:</strong> Puede procesar devoluciones parciales o totales. Le quedan <strong>${validacion.diasRestantes} días</strong> de plazo.
            </div>
        `;
    }

    const contenido = `
        <div class="row">
            <!-- Columna izquierda: Producto -->
            <div class="col-md-5 border-end">
                <h6 class="fw-bold text-muted mb-3">PRODUCTO</h6>
                
                <div class="text-center mb-3">
                    <img src="${imagenSrc}" 
                         class="img-fluid rounded border" 
                         style="max-height: 250px; object-fit: contain;" 
                         alt="${salida.nombre_producto}"
                         onerror="this.src='${ruta}default.png'">
                </div>

                <div class="bg-light p-3 rounded">
                    <h5 class="fw-bold mb-3">${salida.nombre_producto}</h5>
                    <p class="mb-2">
                        <strong>SKU:</strong> 
                        <span class="badge bg-dark">${salida.sku}</span>
                    </p>
                    ${salida.nombre_categoria ? `
                    <p class="mb-2">
                        <strong>Categoría:</strong> 
                        ${salida.nombre_categoria}
                    </p>
                    ` : ''}
                    <p class="mb-2">
                        <strong>Precio Unitario:</strong> 
                        <span class="text-primary fw-bold">$${precioUnitario.toFixed(2)}</span>
                    </p>
                    <p class="mb-2">
                        <strong>Cantidad:</strong> 
                        <span class="badge bg-danger fs-6">${salida.cantidad} unidades</span>
                    </p>
                    <p class="mb-0">
                        <strong>Stock Actual:</strong> 
                        <span class="badge bg-success">${salida.stock_actual || 0} un.</span>
                    </p>
                </div>
            </div>

            <!-- Columna derecha: Detalles de salida -->
            <div class="col-md-7">
                <h6 class="fw-bold text-muted mb-3">DETALLES DE LA SALIDA</h6>

                ${infoDevolucion}

                <!-- Historial de Devoluciones (NUEVO) -->
                ${salida.historial_devoluciones && salida.historial_devoluciones.length > 0 ? `
                <div class="mb-4">
                    <h6 class="fw-bold small mb-2 text-danger"><i class="fas fa-history me-1"></i>HISTORIAL DE CAMBIOS / DEVOLUCIONES</h6>
                    <div class="list-group list-group-flush border rounded shadow-sm overflow-hidden">
                        ${salida.historial_devoluciones.map(dev => {
        const fDev = new Date(dev.fecha_registro);
        return `
                                <div class="list-group-item list-group-item-action py-2">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill">
                                            -${dev.cantidad} un.
                                        </span>
                                        <small class="text-muted"><i class="far fa-clock me-1"></i>${fDev.toLocaleString('es-ES')}</small>
                                    </div>
                                    <p class="mb-0 small mt-1 text-secondary">${dev.motivo || 'Sin motivo registrado'}</p>
                                </div>
                            `;
    }).join('')}
                    </div>
                </div>
                ` : ''}

                <div class="mb-4">
                    <div class="bg-danger bg-opacity-10 p-3 rounded mb-3">
                        <p class="mb-2">
                            <i class="fas fa-hashtag me-2 text-danger"></i>
                            <strong>ID de Salida:</strong> #${salida.id}
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-flag me-2 text-danger"></i>
                            <strong>Estado:</strong> ${badgeEstado}
                        </p>
                        <p class="mb-2">
                            <i class="far fa-calendar-alt me-2 text-danger"></i>
                            <strong>Fecha de Salida:</strong> ${fechaFormateada}
                        </p>
                        <p class="mb-2">
                            <i class="far fa-clock me-2 text-danger"></i>
                            <strong>Hora:</strong> ${salida.hora_salida}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-user me-2 text-danger"></i>
                            <strong>Registrado por:</strong> ${salida.usuario || 'N/A'}
                        </p>
                    </div>

                </div>

                <!-- Resumen financiero -->
                <div class="border rounded p-3 bg-light">
                    <h6 class="fw-bold mb-3">Resumen Financiero</h6>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal (${salida.cantidad} × $${precioUnitario.toFixed(2)}):</span>
                        <strong>$${subtotal.toFixed(2)}</strong>
                    </div>
                    
                    ${descuento > 0 ? `
                    <div class="d-flex justify-content-between mb-2">
                        <span>Descuento:</span>
                        <strong class="text-danger">-$${descuento.toFixed(2)}</strong>
                    </div>
                    ` : ''}
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold fs-5">TOTAL:</span>
                        <strong class="text-danger fs-4">$${total.toFixed(2)}</strong>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="mt-4 pt-3 border-top">
                    ${estado === 'Entregado' ? `
                    <p class="text-success fw-bold text-center mb-3">
                        <i class="fas fa-check-circle me-1"></i> Esta salida ha sido entregada
                    </p>
                    ` : ''}

                    ${estado !== 'Cancelado' ? `
                    <button class="btn btn-outline-danger w-100 btnDevolverSalida" 
                            data-id="${salida.id}"
                            data-cantidad="${salida.cantidad}"
                            data-variante="${salida.id_variante}"
                            data-nombre="${salida.nombre_producto}">
                        <i class="fas fa-undo me-2"></i>Devolver Stock (Parcial o Total)
                    </button>
                    ` : `
                    <div class="alert alert-danger text-center mb-0">
                        <i class="fas fa-ban me-2"></i>Salida Cancelada
                    </div>
                    `}
                </div>
            </div>
        </div>
    `;

    $("#detalleContent").html(contenido);

    // El footer ya no necesita inyección dinámica para acciones principales,
    // pero podemos limpiarlo para evitar duplicados residuales.
    $("#modalDetalleSalida .modal-footer .btnDevolverSalida, #modalDetalleSalida .modal-footer .alert-devolucion").remove();

    const modalElement = document.getElementById('modalDetalleSalida');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

// Devolver salida (cambiar estado a Cancelado y restaurar stock)
function devolverSalida(id, cantidadOriginal, idVariante, nombreProducto) {
    // Usar Swal con input para pedir cantidad
    Swal.fire({
        title: 'Procesar Devolución',
        html: `
            <p>Producto: <strong>${nombreProducto}</strong></p>
            <p>Cantidad vendida original: <strong>${cantidadOriginal}</strong></p>
            <p class="mb-2">Ingrese la cantidad a devolver al stock:</p>
            <input type="number" id="cantidadDevolver" class="swal2-input" min="1" max="${cantidadOriginal}" value="${cantidadOriginal}">
            <div class="mt-3">
                <label class="form-label fw-bold small">Motivo de la Devolución</label>
                <textarea id="motivoDevolucion" class="form-control" rows="2" placeholder="Ej: Defecto de fábrica, cambio de talle..."></textarea>
            </div>
            <p class="text-muted small mt-2">
                <i class="fas fa-info-circle"></i> Si devuelve todo (${cantidadOriginal}), la salida se cancelará.<br>
                Si devuelve menos, será una devolución parcial.
            </p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Procesar Devolución',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const cantidad = Swal.getPopup().querySelector('#cantidadDevolver').value;
            const motivo = Swal.getPopup().querySelector('#motivoDevolucion').value || 'Devolución solicitada por usuario';

            if (!cantidad || cantidad <= 0) {
                Swal.showValidationMessage('Debe ingresar una cantidad válida');
            } else if (parseInt(cantidad) > parseInt(cantidadOriginal)) {
                Swal.showValidationMessage(`No puede devolver más de lo vendido (${cantidadOriginal})`);
            }
            return { cantidad: cantidad, motivo: motivo };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const cantidadDevolver = result.value.cantidad;
            const motivo = result.value.motivo;
            procesarDevolucion(id, cantidadDevolver, motivo);
        }
    });
}

// Procesar la devolución
function procesarDevolucion(id, cantidad, motivo = "") {
    $.ajax({
        url: "app/controllers/salidaController.php",
        method: "POST",
        dataType: "json",
        data: {
            accion: "devolverSalida",
            id: id,
            motivo: motivo,
            cantidad: cantidad
        },
        success: function (response) {
            if (response.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Devolución Exitosa',
                    html: `
                        <p>${response.message}</p>
                        <p class="text-muted small">Stock actualizado: ${response.stock_actualizado} unidades</p>
                    `,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    cargarTodasLasSalidas(); // Recargar datos
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo procesar la devolución',
                confirmButtonColor: '#dc3545'
            });
        }
    });
}

// Mostrar mensaje cuando no hay resultados
function showNoResults() {
    $("#salidas-grid").addClass("d-none");
    $("#noResults").removeClass("d-none");
    $("#paginationContainer").addClass("d-none");
}

// Actualizar contador
// Actualizar contador
function updateResultCount() {
    $("#resultCount").text(filteredSalidas.length);
    // Siempre mostrar como 'entregas' ya no hay pestaña de cancelados
    const container = $("#resultCount").parent();
    if (container.length) {
        container.html(`<span id="resultCount">${filteredSalidas.length}</span> entregas encontradas`);
    }
}

//Reporte de salidas a PDF
async function exportarSalidasPDF() {
    if (filteredSalidas.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin Datos',
            text: 'No hay salidas para exportar con los filtros actuales',
            confirmButtonColor: '#ffc107'
        });
        return;
    }

    Swal.fire({
        title: 'Generando Reporte PDF',
        text: 'Por favor espere...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        // ─── Métricas ────────────────────────────────────────────────────────
        const totalMonto = filteredSalidas.reduce((sum, s) => sum + parseFloat(s.total || 0), 0);
        const totalDescuento = filteredSalidas.reduce((sum, s) => sum + parseFloat(s.descuento || 0), 0);
        const totalUnidades = filteredSalidas.reduce((sum, s) => sum + parseInt(s.cantidad || 0), 0);
        const subtotalBruto = totalMonto + totalDescuento;
        const fechaDesc = `Desde: ${$("#fechaDesde").val() || 'Inicio'}   Hasta: ${$("#fechaHasta").val() || 'Hoy'}`;
        const busqueda = $("#searchInput").val() || 'Ninguna';
        const fechaArchivo = new Date().toISOString().split('T')[0];

        // ─── Helpers ─────────────────────────────────────────────────────────
        const cell = (text, opts = {}) => ({
            text: String(text ?? 'N/A'),
            fontSize: 8,
            ...opts
        });

        const headerCell = (text) => ({
            text,
            style: 'tableHeader'
        });

        const colorEstado = (estado) => {
            const mapa = {
                'Entregado': '#28a745',
                'Cancelado': '#dc3545',
                'En camino': '#fd7e14',
                'Pendiente': '#ffc107'
            };
            return mapa[estado] || '#6c757d';
        };

        // ─── Filas de datos ──────────────────────────────────────────────────
        const filasDatos = filteredSalidas.map((s) => [
            cell(s.id, { alignment: 'center', color: '#555' }),
            cell(s.fecha_salida, { alignment: 'center' }),
            cell(s.nombre_producto),
            cell(s.sku, { alignment: 'center', fontSize: 7 }),
            cell(s.cantidad, { alignment: 'center' }),
            cell('$' + parseFloat(s.precio_unitario).toFixed(2), { alignment: 'right' }),
            cell('$' + parseFloat(s.descuento || 0).toFixed(2), { alignment: 'right', color: '#dc3545' }),
            cell('$' + parseFloat(s.total).toFixed(2), { alignment: 'right', bold: true }),
            {
                text: (s.estado || 'Pendiente').toUpperCase(),
                fontSize: 7,
                bold: true,
                alignment: 'center',
                color: colorEstado(s.estado)
            },
            cell(s.usuario || 'N/A')
        ]);

        // ─── Fila de totales ─────────────────────────────────────────────────
        const filaTotales = [
            { text: '', fontSize: 8 },
            { text: '', fontSize: 8 },
            { text: `TOTALES (${filteredSalidas.length} registros)`, fontSize: 8, bold: true, colSpan: 2, alignment: 'right' },
            {},
            { text: String(totalUnidades), fontSize: 8, bold: true, alignment: 'center' },
            { text: '', fontSize: 8 },
            { text: '-$' + totalDescuento.toFixed(2), fontSize: 8, bold: true, alignment: 'right', color: '#dc3545' },
            { text: '$' + totalMonto.toFixed(2), fontSize: 9, bold: true, alignment: 'right', color: '#0b5ee1' },
            { text: '', fontSize: 8 },
            { text: '', fontSize: 8 }
        ];

        // ─── Documento ───────────────────────────────────────────────────────
        const docDefinition = {
            pageSize: 'A4',
            pageOrientation: 'landscape',
            pageMargins: [30, 45, 30, 40],

            header: (currentPage, pageCount) => ({
                columns: [
                    { text: 'AX STORE - Reporte de Historial de Salidas', fontSize: 8, color: '#999', margin: [30, 15, 0, 0] },
                    { text: `Pág. ${currentPage} / ${pageCount}`, fontSize: 8, color: '#999', alignment: 'right', margin: [0, 15, 30, 0] }
                ]
            }),

            footer: () => ({
                text: `Generado el ${new Date().toLocaleString()} — AX STORE`,
                alignment: 'center',
                fontSize: 7,
                color: '#bbb',
                margin: [0, 10, 0, 0]
            }),

            content: [
                // ─── Título ──────────────────────────────────────────────────
                {
                    columns: [
                        { text: 'REPORTE DE SALIDAS', style: 'mainHeader' },
                        {
                            stack: [
                                { text: 'Fecha de Generación', fontSize: 7, color: '#999' },
                                { text: new Date().toLocaleString(), fontSize: 9, bold: true }
                            ],
                            alignment: 'right',
                            margin: [0, 4, 0, 0]
                        }
                    ]
                },
                { canvas: [{ type: 'line', x1: 0, y1: 4, x2: 781, y2: 4, lineWidth: 2, lineColor: '#0b5ee1' }] },
                { text: '\n' },

                // ─── Resumen en 2 columnas (sin canvas separador) ─────────────────────
                {
                    columns: [
                        {
                            stack: [
                                { text: 'FILTROS APLICADOS', style: 'sectionTitle' },
                                { text: `Rango de fechas: ${fechaDesc}`, fontSize: 8 },
                                { text: `Búsqueda activa: ${busqueda}`, fontSize: 8 }
                            ],
                            width: '*'
                        },
                        {
                            stack: [
                                { text: 'RESUMEN GENERAL', style: 'sectionTitle', alignment: 'right' },
                                { text: `Registros: ${filteredSalidas.length}   |   Unidades: ${totalUnidades}`, alignment: 'right', fontSize: 8 },
                                { text: `Subtotal bruto: $${subtotalBruto.toFixed(2)}`, alignment: 'right', fontSize: 8, color: '#555' },
                                { text: `Descuentos aplicados: -$${totalDescuento.toFixed(2)}`, alignment: 'right', fontSize: 8, color: '#dc3545' },
                               // { canvas: [{ type: 'line', x1: 0, y1: 3, x2: 200, y2: 3, lineWidth: 0.5, lineColor: '#ccc' }], margin: [0, 2, 0, 2] },
                                { text: `Total Neto: $${totalMonto.toFixed(2)}`, alignment: 'right', fontSize: 13, bold: true, color: '#0b5ee1' }
                            ],
                            width: '*',
                            margin: [20, 0, 0, 0]
                        }
                    ]
                },
                { text: '\n' },

                // ─── Tabla principal ──────────────────────────────────────────
                {
                    table: {
                        headerRows: 1,
                        widths: [30, 55, '*', 50, 35, 52, 55, 58, 58, 55],
                        body: [
                            [
                                headerCell('ID'),
                                headerCell('FECHA'),
                                headerCell('PRODUCTO'),
                                headerCell('SKU'),
                                headerCell('CANT'),
                                headerCell('P. UNIT'),
                                headerCell('DESCUENTO'),
                                headerCell('TOTAL'),
                                headerCell('ESTADO'),
                                headerCell('USUARIO')
                            ],
                            ...filasDatos,
                            filaTotales
                        ]
                    },
                    layout: {
                        fillColor: (rowIndex, node) => {
                            if (rowIndex === 0) return null;
                            if (rowIndex === node.table.body.length - 1) return '#e8f0fe'; // fila totales
                            return rowIndex % 2 === 0 ? '#f8f9fa' : null;
                        },
                        hLineWidth: (i, node) => (i === 0 || i === node.table.body.length) ? 1 : 0.2,
                        vLineWidth: () => 0,
                        hLineColor: () => '#d0d7e2',
                        paddingTop: () => 4,
                        paddingBottom: () => 4
                    }
                }
            ],

            styles: {
                mainHeader: {
                    fontSize: 20,
                    bold: true,
                    color: '#0b5ee1',
                    margin: [0, 0, 0, 4]
                },
                sectionTitle: {
                    fontSize: 9,
                    bold: true,
                    color: '#333',
                    margin: [0, 0, 0, 4]
                },
                tableHeader: {
                    bold: true,
                    fontSize: 8,
                    color: 'white',
                    fillColor: '#0b5ee1',
                    alignment: 'center',
                    margin: [0, 3, 0, 3]
                }
            }
        };

        window.pdfMake.createPdf(docDefinition).download(`Reporte_Salidas_${fechaArchivo}.pdf`);
        Swal.close();

        Swal.fire({
            icon: 'success',
            title: 'Reporte Generado',
            text: 'El archivo PDF ha sido descargado correctamente.',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });

    } catch (error) {
        console.error("Error al generar PDF:", error);
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo generar el reporte PDF' });
    }
}
// Cambiar estado con confirmación
function cambiarEstadoSalida(id, nuevoEstado, accionNombre) {
    Swal.fire({
        title: `¿${accionNombre}?`,
        html: `
            <p>La salida pasará a estado <strong>"${nuevoEstado}"</strong></p>
            <div class="mt-3 text-start">
                <label class="form-label small fw-bold">Nota / Observación (opcional):</label>
                <textarea id="notaEstado" class="form-control" rows="2" placeholder="Ej: No se encontró al cliente, pedido duplicado..."></textarea>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            return Swal.getPopup().querySelector('#notaEstado').value;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const motivo = result.value || '';
            $.ajax({
                url: "app/controllers/salidaController.php",
                method: "POST",
                dataType: "json",
                data: {
                    accion: "cambiarEstado",
                    id: id,
                    nuevo_estado: nuevoEstado,
                    motivo: motivo // Pasamos el motivo
                },
                success: function (response) {
                    if (response.status === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Actualizado!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            cargarTodasLasSalidas();
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modalDetalleSalida'));
                            if (modal) modal.hide();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
                }
            });
        }
    });
}

// Configurar eventos
function setupEvents() {
    // Filtros
    $("#searchInput").on("keyup", aplicarFiltros);
    $("#fechaDesde, #fechaHasta, #ordenar").on("change", aplicarFiltros);

    // Limpiar filtros
    $("#btnLimpiarFiltros").on("click", function () {
        $("#searchInput").val("");
        setFechasIniciales();
        $("#ordenar").val("fecha_desc");
        aplicarFiltros();
    });

    // Estadísticas interactivas
    $("#totalSalidas").closest(".stat-card").on("click", function () {
        $("#searchInput").val("");
        setFechasIniciales();
        $("#ordenar").val("fecha_desc");
        aplicarFiltros();
        Swal.fire({ icon: 'info', title: 'Filtros Reiniciados', text: 'Viendo todas las salidas', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
    });

    $("#montoTotal").closest(".stat-card").on("click", function () {
        $("#ordenar").val("monto_desc");
        aplicarFiltros();
        Swal.fire({ icon: 'info', title: 'Ordenado por Monto', text: 'Filtrando por mayores ingresos', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
    });

    $("#salidasHoy").closest(".stat-card").on("click", function () {
        const hoy = new Date().toISOString().split('T')[0];
        $("#fechaDesde, #fechaHasta").val(hoy);
        $("#ordenar").val("fecha_desc");
        aplicarFiltros();
        Swal.fire({ icon: 'info', title: 'Filtrado: Hoy', text: 'Viendo solo las salidas de este día', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
    });

    // Exportar
    $("#btnExportar").on("click", exportarSalidasPDF);

    // Cambio de pestaña
    $('#historyTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        currentPage = 1;
        aplicarFiltros();
    });

    // Ver detalle
    $(document).on("click", ".btnVerDetalle", function () {
        const id = $(this).data("id");
        verDetalleSalida(id);
    });

    // Acción de botones de estado (Despachar, Entregar)
    $(document).on("click", ".btnCambiarEstado", function () {
        const id = $(this).data("id");
        const nuevoEstado = $(this).data("nuevo-estado");
        const accionNombre = $(this).data("accion");

        cambiarEstadoSalida(id, nuevoEstado, accionNombre);
    });

    // Devolver salida
    $(document).on("click", ".btnDevolverSalida", function () {
        const id = $(this).data("id");
        const cantidad = $(this).data("cantidad");
        const idVariante = $(this).data("variante");
        const nombreProducto = $(this).data("nombre");
        const fechaEntrega = $(this).data("fecha-entrega");
        devolverSalida(id, cantidad, idVariante, nombreProducto, fechaEntrega);
    });

    // Paginación
    $(document).on("click", ".pagination .page-link", function (e) {
        e.preventDefault();
        const page = parseInt($(this).data("page"));
        if (page && page !== currentPage) {
            currentPage = page;
            renderSalidas();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Imprimir detalle (Ticket PDF)
    $("#btnImprimirDetalle").on("click", function () {
        if (currentSalidaDetail) {
            generarPDFTicket(currentSalidaDetail);
        } else {
            window.print();
        }
    });
}

/**
 * Helper para convertir una URL de imagen a Base64
 */
function getBase64ImageFromURL(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.setAttribute("crossOrigin", "anonymous");
        img.onload = () => {
            const canvas = document.createElement("canvas");
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext("2d");
            ctx.drawImage(img, 0, 0);
            const dataURL = canvas.toDataURL("image/png");
            resolve(dataURL);
        };
        img.onerror = (error) => {
            resolve(null);
        };
        img.src = url;
    });
}

/**
 * Genera un Ticket PDF compacto (layout de 80mm) con pdfmake
 */
async function generarPDFTicket(salida) {
    Swal.fire({
        title: 'Generando Ticket',
        text: 'Por favor espere...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const imgUrl = salida.imagen ? `${ruta}${salida.imagen}` : `${ruta}default.png`;
        const base64Img = await getBase64ImageFromURL(imgUrl);

        // Logo de la empresa (puedes cambiar la ruta)
        const logoUrl = `${ruta}logo.png`;
        const base64Logo = await getBase64ImageFromURL(logoUrl);

        const docDefinition = {
            pageSize: { width: 226.77, height: 'auto' },
            pageMargins: [10, 10, 10, 10],
            content: [
                { text: 'AX STORE', style: 'storeName' },
                { text: 'Comprobante de Salida', style: 'ticketTitle' },
                { canvas: [{ type: 'line', x1: 0, y1: 5, x2: 206.77, y2: 5, lineWidth: 0.5 }] },
                { text: '\n' },
                {
                    columns: [
                        { text: 'ID Salida:', bold: true, fontSize: 8 },
                        { text: '#' + salida.id, alignment: 'right', fontSize: 8 }
                    ]
                },
                {
                    columns: [
                        { text: 'Fecha:', bold: true, fontSize: 8 },
                        { text: salida.fecha_salida, alignment: 'right', fontSize: 8 }
                    ]
                },
                {
                    columns: [
                        { text: 'Hora:', bold: true, fontSize: 8 },
                        { text: salida.hora_salida, alignment: 'right', fontSize: 8 }
                    ]
                },
                {
                    columns: [
                        { text: 'Estado:', bold: true, fontSize: 8 },
                        { text: (salida.estado || 'Pendiente').toUpperCase(), alignment: 'right', fontSize: 8, color: '#dc3545' }
                    ]
                },
                { text: '\n' },
                { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 206.77, y2: 0, lineWidth: 0.5 }] },
                { text: 'PRODUCTO', style: 'sectionHeader' },
                base64Img ? {
                    image: base64Img,
                    width: 60,
                    alignment: 'center',
                    margin: [0, 5, 0, 5]
                } : {},
                { text: salida.nombre_producto, bold: true, fontSize: 9, alignment: 'center' },
                { text: 'SKU: ' + salida.sku, fontSize: 7, alignment: 'center', color: '#666' },
                { text: '\n' },
                {
                    table: {
                        widths: ['*', 'auto'],
                        body: [
                            [
                                { text: 'Cant x Precio', fontSize: 8 },
                                { text: salida.cantidad + ' x $' + parseFloat(salida.precio_unitario).toFixed(2), alignment: 'right', fontSize: 8 }
                            ],
                            [
                                { text: 'Descuento', fontSize: 8 },
                                { text: '$' + parseFloat(salida.descuento || 0).toFixed(2), alignment: 'right', fontSize: 8 }
                            ],
                            [
                                { text: 'Subtotal', bold: true, fontSize: 8 },
                                { text: '$' + parseFloat(salida.subtotal).toFixed(2), alignment: 'right', bold: true, fontSize: 8 }
                            ]
                        ]
                    },
                    layout: 'noBorders'
                },
                { text: '\n' },

                // ✅ SECCIÓN: TOTAL A PAGAR (antes era Envío/Entrega)
                { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 206.77, y2: 0, lineWidth: 1 }] },
                { text: 'TOTAL A PAGAR', style: 'sectionHeader' },
                {
                    table: {
                        widths: ['*', 'auto'],
                        body: [
                            [
                                { text: 'Total', style: 'totalLabel' },
                                { text: '$' + parseFloat(salida.total).toFixed(2), style: 'totalValue' }
                            ]
                        ]
                    },
                    layout: 'noBorders',
                    margin: [0, 5, 0, 5]
                },
                { text: '\n' },

                // ✅ SECCIÓN: LOGO DE LA EMPRESA (antes era el Total)
                { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 206.77, y2: 0, lineWidth: 0.5 }] },
                base64Logo ? {
                    image: base64Logo,
                    width: 80,
                    alignment: 'center',
                    margin: [0, 10, 0, 10]
                } : { text: 'AX STORE', alignment: 'center', fontSize: 14, bold: true, margin: [0, 10, 0, 10] },

                { text: '¡Gracias por su preferencia!', alignment: 'center', fontSize: 8, italic: true },
                { text: 'AX STORE', alignment: 'center', fontSize: 7, margin: [0, 10, 0, 0], color: '#aaa' }
            ],
            styles: {
                storeName: { fontSize: 16, bold: true, alignment: 'center', margin: [0, 0, 0, 2] },
                ticketTitle: { fontSize: 10, alignment: 'center', color: '#666' },
                sectionHeader: { fontSize: 8, bold: true, margin: [0, 10, 0, 5], color: '#333' },
                totalLabel: { fontSize: 10, bold: true, margin: [0, 5, 0, 0] },
                totalValue: { fontSize: 12, bold: true, alignment: 'right', color: '#dc3545' }
            }
        };

        window.pdfMake.createPdf(docDefinition).download('Ticket_Salida_' + salida.id + '.pdf');
        Swal.close();

    } catch (error) {
        console.error("Error al generar ticket:", error);
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo generar el ticket PDF' });
    }
}

