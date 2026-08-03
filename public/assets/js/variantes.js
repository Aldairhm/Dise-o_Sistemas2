const ruta = "http://localhost/AXStore2026_1/app/views/assets/images/";

let idProducto;
$(document).ready(function () {
  // 1. CONFIGURACIÓN
  idProducto = $("#variantes-container").data("id");
  console.log("ID del producto:", idProducto);
  init();

  function init() {
    cargarVariantesGrid(idProducto);
    setupEvents();
  }

  //manejo del botón editar variante
  $(document).on("click", ".btnEditarVariante", function () {
    const idVariante = $(this).data("id");
    $("#modalEditarVariante").modal("show");
    console.log("Editar variante ID:", idVariante);
    prepararEdicionVariante(idVariante);
  });

  //envio de datos para editar alguna variante
  $("#formEditarVariante").on("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append("accion", "editarVariante");

    $.ajax({
      url: "app/controllers/productoController.php",
      method: "POST",
      dataType: "json",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.status === "success") {
          mostrarExito(response.message);
          $("#modalEditarVariante").modal("hide");
          cargarVariantesGrid(idProducto);
        } else {
          mostrarError(response.message);
        }
      },
    });
  });

  // Escuchamos cuando el input de archivos cambia
  $("#inputGaleria").on("change", function () {
    // Revisamos si seleccionaron al menos una foto
    if (this.files && this.files.length > 0) {
      // Si hay fotos, le quitamos el "escondite"
      $("#btnGuardarGaleria").removeClass("d-none");
      console.log("Fotos seleccionadas: " + this.files.length);
    } else {
      // Si al final no eligieron nada, lo volvemos a esconder
      $("#btnGuardarGaleria").addClass("d-none");
    }
  });

  //manejo de registro para nuevas imagenes
  $("#btnGuardarGaleria").on("click", function () {
    // 1. CORRECCIÓN: Accedemos al elemento nativo [0] para sacar los archivos
    const inputFotos = $("#inputGaleria")[0];
    const archivos = inputFotos.files;

    if (archivos.length === 0) {
      // Supongo que esta función la tenés definida
      mostrarError("No has seleccionado ningún archivo.");
      return;
    }

    // Preparamos el FormData con el formulario
    const formData = new FormData($("#formSubirGaleria")[0]);
    formData.append("accion", "subirGaleriaVariante");

    $.ajax({
      url: "app/controllers/productoController.php",
      method: "POST",
      data: formData,
      dataType: "json",
      // 2. CORRECCIÓN VITAL: Sin esto, los archivos no se envían
      contentType: false,
      processData: false,
      beforeSend: function () {
        // Bloqueamos el botón por seguridad (UX)
        $("#btnGuardarGaleria")
          .prop("disabled", true)
          .html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
      },
      success: function (response) {
        if (response.status === "success") {
          mostrarExito(response.message);
          // Limpiamos el input y escondemos el botón
          $("#inputGaleria").val("");
          $("#btnGuardarGaleria").addClass("d-none");

          // Refrescamos la galería
          cargarFotosExistentes(response.idRefresh);
        } else {
          mostrarError(response.message);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("Error técnico: ", textStatus, errorThrown);
        mostrarError("Error de conexión con el servidor.");
      },
      complete: function () {
        // Rehabilitamos el botón
        $("#btnGuardarGaleria")
          .prop("disabled", false)
          .text("Subir Seleccionadas");
      },
    });
  });

  // 5. EVENTOS (MENÚ Y FILTROS)
  function setupEvents() {
    // Menú móvil
    $(".mobile-menu-btn").on("click", function () {
      const $nav = $(".main-nav");
      $nav.toggleClass("active");
      $(this).html(
        $nav.hasClass("active")
          ? '<i class="fas fa-times"></i>'
          : '<i class="fas fa-bars"></i>',
      );
    });

    // Filtrado por categoría
    $(".category-btn").on("click", function () {
      $(".category-btn").removeClass("active");
      $(this).addClass("active");

      const category = $(this).data("category");
      const filtered =
        category === "all"
          ? allProducts
          : allProducts.filter((p) => p.categoria === category);

      renderProducts(filtered);
    });
  }
});

//cargaremos los datos de la variante
function prepararEdicionVariante(idVariante) {
  $.ajax({
    url: "app/controllers/productoController.php",
    method: "POST",
    dataType: "json",
    data: { accion: "obtenerVariantePorId", id: idVariante },
    success: function (response) {
      if (response.status === "success") {
        const variante = response.data.variante;
        $("#id_variante_edit").val(variante.id);
        $("#id_producto").val(variante.id_producto);
        $("#nombre_variante_edit").val(variante.nombre);
        $("#precio_venta_edit").val(variante.precio_venta);
        $("#stock_actual_edit").val(variante.stock);
        $("#sku_edit").val(variante.sku);
        $("#stock_minimo_edit").val(variante.reserva);
        $("#comision_edit").val(variante.comision);
        $("#imgPrevEdit").attr("src", ruta + variante.imagen);

        //pintar los atributos que trae
        const atributos = response.data.atributos;
        const contenedor = $("#contenedorAtributosEdit");
        contenedor.empty();

        if (atributos && atributos.length > 0) {
          $.each(atributos, function (i, attr) {
            const inputAttr = `
            <div class="mb-2">
                <label class="form-label small fw-bold text-muted mb-0">${attr.nombre_atributo}</label>
                <input type="text" 
                       name="atributo_valor[${attr.id_atributo}]" 
                       class="form-control form-control-sm border-secondary input-atributo-edit" 
                       value="${attr.valor}" 
                       data-nombre="${attr.nombre_atributo}"
                       placeholder="Valor para ${attr.nombre_atributo}">
            </div>`;
            contenedor.append(inputAttr);
          });
        } else {
          contenedor.append(
            '<div class="text-center text-muted small py-3">Sin atributos definidos</div>',
          );
        }
      }
    },
  });
}

// 3. PETICIÓN AJAX AL CONTROLADOR (MVC) PARA RENDERIZAR LOS PRODUCTOS
window.cargarVariantesGrid = function (id) {
  let allProducts = [];
  const $productGrid = $("#product-grid");

  if (!id || id == 0) {
    console.warn("ID no válido para cargar variantes.");
    return;
  }

  $.ajax({
    url: "app/controllers/productoController.php",
    method: "POST",
    dataType: "json",
    data: { accion: "variantes", id: id },
    success: function (response) {
      if (response.status === "success") {
        allProducts = response.data;
        renderProducts(allProducts);
      } else {
        console.error("El servidor respondió pero con error");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error en la carga:", error);
      $productGrid.html(
        '<div class="col-12 text-center text-danger">Error al conectar con la base de datos.</div>',
      );
    },
  });
};

//funcion para mostrar las fotos del producto
function cargarFotosExistentes(id) {
  $.ajax({
    url: "app/controllers/productoController.php",
    method: "POST",
    data: { id: id, accion: "obtenerGaleria" },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        pintarFotosGrid(response.data);
      } else {
        mostrarError(response.message);
      }
    },
  });
}

//pintar las fotos de la galeria en cada variante
function pintarFotosGrid(fotos) {
  //capturamos el grid como un objeto y quitamos lo que hay
  const $grid = $("#gridGaleria");
  $grid.empty();

  if (fotos.length == 0) {
    $grid.append(
      '<p class="text-center w-100">No hay imagenes para mostrar.</p>',
    );
    return;
  }

  $("#contadorFotos").text(fotos.length);

  $.each(fotos, function (i, foto) {
    // Ejemplo de cómo se vería el item de la galería
    let itemGaleria = `
    <div class="col-6 col-sm-4 col-md-3">
        <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border: 1px solid #eee !important;">
            
            <div class="position-relative" style="height: 140px; background: #fdfdfd; display: flex; align-items: center; justify-content: center;">
                ${foto.es_principal == 1 ? '<span class="badge bg-primary position-absolute top-0 start-0 m-2 shadow-sm" style="font-size: 0.6rem; z-index: 10;"><i class="bi bi-star-fill"></i> PORTADA</span>' : ""}
                
                <img src="${ruta}${foto.ruta_imagen}" 
                     class="w-100 h-100" 
                     style="object-fit: contain; padding: 10px;">
            </div>

            <div class="card-footer p-2 bg-white d-flex justify-content-center gap-2 border-top-0">
                <button class="btn ${foto.es_principal == 1 ? "btn-primary" : "btn-outline-primary"} btn-circulo-gal" 
                        onclick="hacerPrincipal(${foto.id_variante}, ${foto.id})"
                        ${foto.es_principal == 1 ? "disabled" : ""}
                        title="Principal">
                    <i class="bi bi-star-fill"></i>
                </button>
                
                <button class="btn btn-outline-danger btn-circulo-gal" 
                        onclick="confirmarEliminarFoto(${foto.id},${foto.id_variante})"
                        title="Eliminar">
                    <i class="bi bi-trash3-fill"></i>
                </button>
            </div>
        </div>
    </div>
`;
    $grid.append(itemGaleria);
  });
}

//funcion para cambiar la foto de portada de una variante
function hacerPrincipal(idVariante, id) {
  Swal.fire({
    title: "¿Cambiar foto de Portada?",
    text: "Se cambiara la foto de portada y se colocara esta..",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, cambiar ",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // Limpiamos borradores para evitar el error de simetría que vimos antes
      $.ajax({
        url: "app/controllers/productoController.php",
        method: "POST",
        data: { id_variante: idVariante, id: id, accion: "cambiarPortada" },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            mostrarExito("Imagen cambiada correctamente");
            cargarFotosExistentes(idVariante);
            cargarVariantesGrid(idProducto);
          }
        },
      });
    }
  });
}

//funcion para la eliminacion de alguna foto de la galeria
function confirmarEliminarFoto(id, idV) {
  Swal.fire({
    title: "¿Eliminar esta Imagen?",
    text: "Se eliminara desde la BD y la carpeta de archivos...",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // Limpiamos borradores para evitar el error de simetría que vimos antes
      $.ajax({
        url: "app/controllers/productoController.php",
        method: "POST",
        data: { id: id, accion: "eliminarFoto" },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            mostrarExito("Imagen eliminada correctamente");
            cargarFotosExistentes(idV);

            if (response.refreshGrid) {
              console.log("Cambiando portada en el grid principal...");
              cargarVariantesGrid(idProducto);
            }
          }
        },
      });
    }
  });
}

// Abrir el modal de galería desde la card o el editor
function abrirGaleria(id, nombre) {
  $("#id_variante_galeria").val(id);
  $("#nombreVarianteModal").text(nombre);
  $("#inputGaleria").val("");
  $("#modalGaleriaVariante").modal("show");

  // Aquí podrías hacer un AJAX para cargar las fotos ya existentes de la BD
  cargarFotosExistentes(id);
}

// Función para abrir el modal desde la tarjeta del producto
function abrirModalStock(id, nombre, reserva, stock) {
  // 1. Cargar datos en el Modal
  $("#stockNombreProducto").text(nombre);

  // Para el formulario de Compra
  $("#idProductoCompra").val(id);

  // Para el formulario de Traslado
  $("#idProductoTraslado").val(id);
  $("#displayBodega").text(reserva);
  $("#displayTienda").text(stock);
  $("#maxBodega").val(reserva); // Guardamos el límite para validar

  // Limpiar inputs anteriores
  $('input[name="cantidad"]').val("");
  $("#errorTraslado").addClass("d-none");

  // Resetear a la primera pestaña
  var firstTabEl = document.querySelector("#pills-compra-tab");
  var firstTab = new bootstrap.Tab(firstTabEl);
  firstTab.show();

  // 2. Mostrar el Modal
  $("#modalStock").modal("show");
}

// Función auxiliar para el botón "Todo"
function moverTodo() {
  let max = $("#maxBodega").val();
  $("#inputTraslado").val(max);
}

// Validación en tiempo real del traslado
$("#inputTraslado").on("input", function () {
  let valor = parseInt($(this).val()) || 0;
  let max = parseInt($("#maxBodega").val()) || 0;

  if (valor > max) {
    $("#errorTraslado").removeClass("d-none");
    $('button[type="submit"]', "#formTraslado").prop("disabled", true);
  } else {
    $("#errorTraslado").addClass("d-none");
    $('button[type="submit"]', "#formTraslado").prop("disabled", false);
  }
});

// Manejo del Envío (AJAX) - Ejemplo genérico
// Tendrás que adaptar la URL a tu controlador real
$("#formCompra, #formTraslado").on("submit", function (e) {
  e.preventDefault();

  let formData = new FormData(this);
  // Agregamos la opción para tu controlador
  formData.append("accion", "registrarMovimiento");

  $.ajax({
    url: "app/controllers/productoController.php", // Ajusta tu ruta
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      // Aquí recargas la tabla o muestras alerta de éxito
      $("#modalStock").modal("hide");

      // Swal.fire de éxito...
      mostrarExito(response.message);

      // Recargar productos para ver los cambios
      cargarVariantesGrid(idProducto);

      //cerramos el modal
      $("#modalStock").modal("show");
    },
  });
});

// 4. RENDERIZADO CON JQUERY
function renderProducts(productsList) {
  const $productGrid = $("#product-grid");
  $productGrid.empty();

  if (productsList.length === 0) {
    $productGrid.append(
      '<p class="text-center w-100">No hay variantes para mostrar.</p>',
    );
    return;
  }

  $.each(productsList, function (i, product) {
    let precioVenta = Number(product.precio_venta);
    let precioFormateado = precioVenta.toFixed(2);

    // Lógica de Stock: bg-danger para reserva (stock crítico) y bg-success para normal
    let stockClass = product.stock < 6 ? "bg-danger" : "bg-success";
    let stockText = product.stock > 0 ? `${product.stock} un.` : "Agotado";

    const card = `
    <div class="col">
        <div class="card h-100 border-0 shadow-sm card-variante">
            <span class="badge ${stockClass} position-absolute top-0 start-0 m-3 shadow-sm">
                ${stockText}
            </span>

            <span class="badge bg-secondary position-absolute top-0 end-0 m-3 shadow-sm" title="En Bodega">
                <i class="bi bi-building"></i> ${product.reserva || 0}
            </span>

            <div class="p-3 bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                <img src="${ruta}${product.imagen}" 
                     class="img-fluid" 
                     style="max-height: 100%; object-fit: contain;" 
                     alt="${product.nombre}">
            </div>

            <div class="card-body d-flex flex-column p-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="badge bg-light text-dark border-0 small text-uppercase" style="font-size: 0.65rem;">
                        ${product.nombre_categoria}
                    </span>
                </div>
                
                <h5 class="card-title fw-bold text-dark mb-1" style="font-size: 1rem;">${product.nombre}</h5>
                <p class="text-muted small mb-3">Ref: ${product.nombre_producto_padre}</p>
                
                <div class="mt-auto">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="h5 mb-0 fw-bold text-primary">$${precioFormateado}</span>
                        </div>
                        
                        <div class="btn-group">
                            <button class="btn btn-outline-success btn-sm rounded-pill px-3 me-1" 
                                    onclick="abrirModalStock(${product.id}, '${product.nombre}', ${product.reserva || 0}, ${product.stock})"
                                    title="Gestionar Entradas y Traslados">
                                <i class="bi bi-box-seam"></i>
                            </button>

                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 me-1" 
                                    onclick="abrirGaleria(${product.id}, '${product.nombre}')"
                                    title="Gestionar Fotos">
                                <i class="bi bi-images"></i>
                            </button>
                            
                            <button class="btn btn-dark btn-sm rounded-pill px-3 btnEditarVariante" 
                                    data-id="${product.id}"
                                    title="Editar Detalles">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
`;
    $productGrid.append(card);
  });
}

function mostrarExito(m) {
  Swal.fire({
    title: "¡Éxito!",
    text: m,
    icon: "success",
    confirmButtonColor: "#333",
  });
}

function mostrarError(m) {
  Swal.fire({
    title: "Error",
    text: m,
    icon: "error",
    confirmButtonColor: "#333",
  });
}
