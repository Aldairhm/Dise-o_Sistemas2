/**
 * AXStore 2026 - Módulo Maestro de Gestión de Productos
 * Lógica: Registro Incremental, Matriz EAV y Protección Contable
 */

// Evita que Bootstrap 5 bloquee el enfoque del teclado en SweetAlert2
document.addEventListener("focusin", (e) => {
  if (e.target.closest(".swal2-container")) {
    e.stopImmediatePropagation();
  }
});

var tabla;
let tieneVentasGlobal = false; // Estado para controlar la edición del esquema

$(document).ready(function () {
  // 1. Configuración de DataTable (Vista General)
  tabla = $("#tablaProductos").DataTable({
    ajax: {
        url: "app/controllers/productoController.php",
        type: "POST",
        data: { accion: "cargarProductos" },
    },
    // Desactivamos el responsive nativo para usar nuestras Cards CSS
    responsive: false, 
    autoWidth: false,
    dom: '<"d-flex justify-content-between align-items-center px-2 mb-3"lf>rt<"d-flex justify-content-between align-items-center px-2 mt-4"ip>',
    language: { url: "app/ajax/idioma.json" },
    
    // Inyectamos los labels para que el CSS sepa qué nombre poner en el móvil
    columnDefs: [
        { targets: 0, createdCell: function (td) { $(td).attr('data-label', 'Producto'); } },
        { targets: 1, createdCell: function (td) { $(td).attr('data-label', 'Categoría'); } },
        { targets: 2, createdCell: function (td) { $(td).attr('data-label', 'Descripción'); } },
        { targets: 3, createdCell: function (td) { $(td).attr('data-label', 'Estado'); } },
        { targets: 4, createdCell: function (td) { $(td).attr('data-label', 'Acciones'); },}
    ],
    
    order: [[0, "asc"]],
    drawCallback: function () {
        $(".dataTables_paginate > .paginate_button").addClass("btn btn-sm");
    },
});

  cargarCategorias();

  // Disparador de Expansión Automática (vía URL ?id=X)
  const contenedor = $("#variantes-container");
  const idProductoUrl = contenedor.data("id");
  $('#modalProducto').on('show.bs.modal', function (event) {
    const boton = $(event.relatedTarget); // El botón que abrió el modal
    const modo = boton.data('modo'); // 'variante' o 'nuevo'

    if (modo === 'variante') {
        // --- CASO VARIANTE: Llenamos con datos del padre ---
        prepararExpansion(idProductoUrl);
    } else {
        // --- CASO PRODUCTO NUEVO: Limpieza total ---
        $(this).find('form')[0].reset();
        $('#id_producto').val('0'); // Importante para que PHP sepa que es INSERT
        $('#modalProductoLabel').text('Registrar Nuevo Producto');
    }
});

  // --- BLOQUE 1: VALIDACIONES DE ENTRADA (Originales) ---
  $(document).on("keydown", "input[type='number']", function (e) {
    if (e.key === "-" || e.key === "e" || e.key === "E") e.preventDefault();
  });

  $(document).on(
    "change",
    "input[name='v_precio_compra[]'], input[name='v_precio_venta[]']",
    function () {
      const fila = $(this).closest("tr");
      const pCompra =
        parseFloat(fila.find("input[name='v_precio_compra[]']").val()) || 0;
      const pVenta =
        parseFloat(fila.find("input[name='v_precio_venta[]']").val()) || 0;

      if (pVenta > 0 && pVenta < pCompra) {
        mostrarError(
          "El precio de venta no puede ser menor al de compra. ¡Estarías perdiendo dinero!",
        );
        fila.find("input[name='v_precio_venta[]']").val(pCompra.toFixed(2));
      }
    },
  );

  $(document).on("change", "input[name='v_stock_actual[]']", function () {
    let valor = parseInt($(this).val());
    if (isNaN(valor) || valor < 1) {
      $(this).val(1);
      mostrarError("El stock inicial debe ser al menos 1 unidad.");
    }
  });
  
  // --- BLOQUE 2: GESTIÓN DE ATRIBUTOS Y ESQUEMA ---
  $("#btnAgregarFilaAtributo").click(function () {
    // 1. Blindaje de Integridad: Si hay ventas, el esquema es inmutable
    if (tieneVentasGlobal) {
      mostrarError(
        "No puedes agregar atributos: Este producto ya posee historial de ventas.",
      );
      return;
    }

    const idProd = $("#id_producto").val();

    // 2. Lógica de Decisión: ¿Nuevo o Expansión?
    if (idProd == 0) {
      // Producto nuevo: Agregar directamente sin molestar al usuario
      ejecutarAgregarFila();
    } else {
      // Producto existente: PEDIR CONFIRMACIÓN
      Swal.fire({
        title: "¿Agregar nuevo atributo?",
        text: "Al agregar un atributo, el esquema cambiará. Las variantes existentes recibirán el valor 'N/A' y los borradores actuales en la tabla se limpiarán para mantener la simetría.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, ampliar contrato",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          // Limpiamos borradores para evitar el error de simetría que vimos antes
          $("#cuerpoVariantes").empty();
          ejecutarAgregarFila();
          mostrarExito("Nuevo atributo añadido al contrato del producto.");
        }
      });
    }
  });

  /**
   * Función Auxiliar: Contiene la lógica técnica de inserción de la fila
   * Evita repetir este bloque de código en los condicionales.
   */
  function ejecutarAgregarFila() {
    $("#contenedorAtributos p").remove();

    const htmlFila = `
        <div class="row g-2 mb-3 fila-atributo p-2 align-items-end border-bottom">
            <div class="col-5">
                <label class="form-label small fw-bold text-dark">Atributo</label>
                <select name="atributo_id[]" class="form-select form-select-sm selectAtributo">
                    <option value="">Cargando...</option>
                </select>
            </div>
            <div class="col-5">
                <label class="form-label small fw-bold text-dark">Valor(es)</label>
                <input type="text" name="atributo_valor[]" class="form-control form-control-sm input-valor-variante" placeholder="Ej: L, M, XL">
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-quitar-atributo btn-sm btn-outline-danger w-100 p-0 d-flex justify-content-center align-items-center" style="height: 31px;">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>`;

    const $nuevaFila = $(htmlFila);
    $("#contenedorAtributos").append($nuevaFila);
    cargarAtributos($nuevaFila.find(".selectAtributo"));
  }

  $(document).on("click", ".btn-quitar-atributo", function () {
    const fila = $(this).closest(".fila-atributo");
    const idProd = $("#id_producto").val();

    if (tieneVentasGlobal) {
      mostrarError(
        "No puedes quitar atributos: Este producto ya posee historial de ventas.",
      );
      return;
    }

    // Si es un registro nuevo (ID 0), borrar sin preguntar
    if (idProd == 0) {
      fila.remove();
      return;
    } else {
      Swal.fire({
        title: "¿Eliminar atributo del producto?",
        text: "Esta acción quitará este atributo de TODAS las variantes existentes. Los datos guardados anteriormente para este atributo se perderán.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar de todo el producto",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          fila.remove();
          // Limpiamos la tabla de variantes generadas porque el esquema cambió
          $("#cuerpoVariantes").empty();
          generarMatrizUnificada();
          mostrarExito(
            "Esquema actualizado. Recordá guardar los cambios para aplicar.",
          );
          if ($("#contenedorAtributos").children().length === 0) {
            $("#contenedorAtributos").html(
              '<p class="text-muted small text-center pt-4">Asigne atributos para generar variantes.</p>',
            );
          }
        }
      });
    }
  });

  // Crear nuevo atributo en BD
  $("#btnNuevoAtributo").on("click", function () {
    if (tieneVentasGlobal) return;
    Swal.fire({
      title: "Nuevo Atributo",
      input: "text",
      inputLabel: "Nombre (ej: Talla, Color)",
      showCancelButton: true,
      confirmButtonText: "Guardar",
      inputValidator: (value) => {
        if (!value) return "¡Escribe el nombre!";
      },
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "app/controllers/productoController.php",
          { nombre: result.value, accion: "crearAtributo" },
          function (res) {
            if (res.status === "success") {
              mostrarExito("Atributo creado.");
              $(".selectAtributo").append(
                $("<option>", { value: res.id, text: result.value }),
              );
            } else {
              mostrarError(res.message);
            }
          },
          "json",
        );
      }
    });
  });

  // --- BLOQUE 3: GENERACIÓN DE MATRIZ UNIFICADA ---
  $("#btnGenerarMatriz").on("click", function () {
    //datos del producto padre
    const nombre = $("#nombre").val();
    const descripcion = $("#descripcion").val();
    
    if(nombre==='' || descripcion===''){
      mostrarError("Debe llenar los datos del producto antes")
      return;
    }
    generarMatrizUnificada();
  });

  // Envío del formulario
  $("#formProducto").on("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const idProd = $("#id_producto").val();
    formData.append(
      "accion",
      idProd > 0 ? "agregarVariantesIncremental" : "registrarProductoCompleto",
    );

    $.ajax({
      url: "app/controllers/productoController.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (res) {
        if (res.status === "success") {
          mostrarExito(res.message);
          $("#modalProducto").modal("hide");
          tabla.ajax.reload();
          if (typeof cargarVariantesGrid === "function") {
            console.log("Recargando grid para ID:", idProd);
            cargarVariantesGrid(idProd);
          }
        } else {
          mostrarError(res.message);
        }
      },
    });
  });
});

/**
 * Función Maestra: Genera variantes nuevas respetando el contrato de atributos.
 */
function generarMatrizUnificada() {
  const nombreProducto = $("#nombre").val().trim();
  const cuerpo = $("#cuerpoVariantes");

  cuerpo.empty();

  // 1. RECOPILACIÓN DE ATRIBUTOS
  let atributosData = [];
  let idsSet = new Set();
  let errorIncompleto = false;
  let errorDuplicado = false;

  $(".fila-atributo").each(function () {
    const idAttr = $(this).find(".selectAtributo").val();
    const nombreAttr = $(this).find(".selectAtributo option:selected").text();
    const valorInput = $(this).find(".input-valor-variante").val().trim();

    if (!idAttr || !valorInput) { errorIncompleto = true; return false; }
    if (idsSet.has(idAttr)) { errorDuplicado = true; return false; }
    idsSet.add(idAttr);

    let valoresUnicos = [...new Set(valorInput.split(",").map(v => v.trim()).filter(v => v !== ""))];

    atributosData.push({ id: idAttr, nombre: nombreAttr, valores: valoresUnicos });
  });

  if (errorIncompleto || errorDuplicado || atributosData.length === 0) {
    mostrarError("Atributos: No puedes seleccionar 1 atributo en un mismo select o completa los valores de estos")
    return;
  }
  
  // 2. GENERACIÓN DE COMBINACIONES
  const valoresParaCombinar = atributosData.map((a) => a.valores);
  let combinaciones = valoresParaCombinar.length > 1
      ? obtenerCombinaciones(valoresParaCombinar)
      : valoresParaCombinar[0].map((v) => [v]);

  // 3. RENDERIZADO SIMPLIFICADO
  let contadorNuevas = 0;
  combinaciones.forEach((combo) => {
    const arrayCombo = Array.isArray(combo) ? combo : [combo];
    
    // El SKU ahora es un simple placeholder, el servidor generará el real
    const skuPlaceholder = "AUTOGENERADO";

    const labelVariante = arrayCombo
      .map((val, idx) => `${atributosData[idx].nombre}: ${val}`)
      .join(" / ");

    let mapaValores = {};
    arrayCombo.forEach((val, idx) => {
      mapaValores[atributosData[idx].id] = val;
    });

    const nuevaFila = `
        <tr class="animate__animated animate__fadeIn">
            <td class="ps-3">
                <input type="hidden" name="v_id[]" value="0">
                <small class="text-muted d-block">${nombreProducto}</small>
                <span class="fw-bold">${labelVariante}</span>
                <input type="hidden" name="v_nombre[]" value="${labelVariante}">
                <input type="hidden" name="v_valores_json[]" value='${JSON.stringify(mapaValores)}'>
            </td>
            <td>
                <input type="text" name="v_sku[]" class="form-control form-control-sm bg-light text-center fw-bold text-primary" value="${skuPlaceholder}" readonly>
            </td>
            <td><input type="file" name="v_foto[]" class="form-control form-control-sm" accept="image/*"></td>
            <td><input type="number" name="v_precio_venta[]" class="form-control form-control-sm" placeholder="0.00" step="0.01" required></td>
            <td><input type="number" name="v_stock_actual[]" class="form-control form-control-sm" value="1" required></td>
            <td><input type="number" name="v_stock_minimo[]" class="form-control form-control-sm" value="1" required></td>
            <td><input type="number" name="v_comision[]" class="form-control form-control-sm" value="0" step="0.01" required></td>
            <td class="text-center">
                <button type="button" class="btn btn-link text-danger p-0 btn-quitar-variante">
                    <i class="fas fa-times-circle"></i>
                </button>
            </td>
        </tr>`;
    cuerpo.append(nuevaFila);
    contadorNuevas++;
  });

  mostrarExito(`Se generaron ${contadorNuevas} combinaciones listas para configurar.`);
}

// Función que dispararás desde el botón de la tabla (ej: onclick="abrirEditar(12)")
function abrirEditarProducto(id) {
    $.ajax({
        url: "app/controllers/productoController.php",
        type: "POST",
        data: { 
            accion: "obtener_uno", 
            id: id 
        },
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                const producto = response.data; // Note: In 'obtener_uno', response.data IS the product object directly, not {variante: ...}
                
                // Llenamos los campos del modal con los datos recibidos
                $("#id_producto_edit").val(producto.id);
                // [NUEVO] Mostramos el nombre completo (Padre + Variante) si es posible, o solo el nombre
                const nombreCompleto = producto.nombre_producto_padre 
                                     ? `${producto.nombre_producto_padre} - ${producto.nombre}` 
                                     : producto.nombre;
                
                $("#nombre_edit").val(nombreCompleto);
                $("#id_categoria_edit").val(producto.id_categoria);
                $("#estado_edit").val(producto.estado);
                $("#descripcion_edit").val(producto.descripcion);

                // Mostramos el modal de edición
                $("#modalProductoEdicion").modal("show");
            } else {
                Swal.fire("Error", response.message, "error");
            }
        },
        error: function() {
            Swal.fire("Error", "No se pudo conectar con el servidor", "error");
        }
    });
}

$("#formProductoEdicion").on("submit", function(e) {
    e.preventDefault(); // Evitamos que la página se recargue

    // Creamos un objeto FormData para manejar los datos del formulario
    let datos = new FormData(this);
    datos.append("accion", "editarProducto"); // Inyectamos la acción para el controlador

    $.ajax({
        url: "app/controllers/productoController.php",
        type: "POST",
        data: datos,
        processData: false, // Necesario para FormData
        contentType: false, // Necesario para FormData
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });

                $("#modalProductoEdicion").modal("hide"); // Cerramos el modal
                
                // Suponiendo que tu DataTable se llama 'tabla'
                if (typeof tabla !== 'undefined') {
                    tabla.ajax.reload(null, false); // Recargamos la tabla sin perder la posición
                }
            } else {
                // Manejamos errores como "Nombre ya existe" o "Sin cambios"
                Swal.fire("Atención", response.message, "warning");
            }
        },
        error: function(jqXHR) {
            // Manejo de errores de servidor o validaciones de PHP (ej: el throw Exception)
            let msg = "Error al procesar la solicitud";
            if(jqXHR.responseJSON && jqXHR.responseJSON.message) {
                msg = jqXHR.responseJSON.message;
            }
            Swal.fire("Error", msg, "error");
        }
    });
});

// --- BLOQUE 4: FUNCIONES GLOBALES DE SOPORTE ---
function prepararExpansion(id) {
  $("#cuerpoVariantes").empty(); // REGLA: Tabla vacía al inicio (Carga Incremental)
  $("#id_producto").val(id);
  console.log("✅ Detalle de producto cargado para expansión.");

  $.ajax({
    url: "app/controllers/productoController.php",
    type: "POST",
    data: { accion: "obtenerDetalleParaExpansion", id: id },
    dataType: "json",
    success: function (res) {
      if (res.status === "success") {
        tieneVentasGlobal = res.tieneVentas;
        const producto= res.data.producto;
        $("#nombre").val(producto.nombre).prop("readonly", true);
        $("#id_categoria").val(producto.id_categoria).prop("disabled", true);
        $("#descripcion").val(producto.descripcion).prop("readonly", true);
        $("#estado").val(producto.estado).prop("disabled", true);
        renderizarAtributosContrato(res.data.atributos, res.tieneVentas);
      } else {
        mostrarError(res.message);
      }
    },
  });
}

function renderizarAtributosContrato(atributos, tieneVentas) {
  const contenedor = $("#contenedorAtributos");
  contenedor.empty();

  // Si tiene ventas, bloqueamos botones de "Añadir Atributo"
  if (tieneVentas) {
    $("#btnNuevoAtributo, #btnAgregarFilaAtributo")
      .prop("disabled", true)
      .addClass("opacity-50");
  }

  atributos.forEach((attr) => {
    const btnEliminar = tieneVentas
      ? `<span class="badge bg-light text-muted border w-100 py-2"><i class="fas fa-lock"></i></span>`
      : `<button type="button" class="btn btn-quitar-atributo btn-sm btn-outline-danger w-100 p-0 d-flex justify-content-center align-items-center" style="height: 31px;">
            <i class="fas fa-trash-alt"></i>
        </button>`;

    const html = `
            <div class="row g-2 mb-3 fila-atributo p-2 align-items-end border-bottom">
                <div class="col-5">
                    <label class="form-label small fw-bold">Atributo</label>
                    <select name="atributo_id[]" class="form-select form-select-sm selectAtributo" disabled></select>
                    <input type="hidden" name="atributo_id[]" value="${attr.id_atributo}">
                </div>
                <div class="col-5">
                    <label class="form-label small fw-bold text-primary">Nuevos Valores</label>
                    <input type="text" name="atributo_valor[]" class="form-control form-control-sm input-valor-variante" placeholder="Rojo, Azul">
                </div>
                <div class="col-2">${btnEliminar}</div>
            </div>`;
    const $fila = $(html);
    contenedor.append($fila);
    cargarAtributos($fila.find(".selectAtributo"), attr.id_atributo);
  });
}

// Auxiliares finales
function obtenerCombinaciones(arrays) {
  return arrays.reduce((a, b) => a.flatMap((d) => b.map((e) => [d, e].flat())));
}

function generarPrefijo(texto) {
  return texto.trim().substring(0, 3).toUpperCase().replace(/\s+/g, "");
}

function cargarCategorias() {
  $.post(
    "app/controllers/productoController.php",
    { accion: "obtenerCategorias" },
    function (res) {
      if (res.status === "success") {
        let opt = '<option value="">Seleccione...</option>';
        res.data.forEach((i) => {
          opt += `<option value="${i.id}">${i.nombre}</option>`;
        });
        $("#id_categoria").html(opt);
        $("#id_categoria_edit").html(opt);
      }
    },
    "json",
  );
}

function cargarAtributos(select, idPre) {
  $.post(
    "app/controllers/productoController.php",
    { accion: "obtenerAtributos" },
    function (res) {
      if (res.status === "success") {
        let opt = '<option value="">Seleccione...</option>';
        res.data.forEach((i) => {
          let s = idPre == i.id ? "selected" : "";
          opt += `<option value="${i.id}" ${s}>${i.nombre}</option>`;
        });
        select.html(opt);
      }
    },
    "json",
  );
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
