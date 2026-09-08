// 1. Configuramos el Toast globalmente para que flote en la esquina superior derecha
const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    },
});

document.addEventListener("alpine:init", () => {
    Alpine.data("gestionProveedores", (proveedoresIniciales = []) => ({
        proveedores: proveedoresIniciales,
        openModal: false,
        loading: false,
        errors: {},
        editando: false,
        archivo: null,
        busqueda: "",
        paginaActual: 1,
        porPagina: 6,

        get proveedoresFiltrados() {
            if (this.busqueda === "") {
                return this.proveedores;
            }

            // Función auxiliar para quitar tildes/acentos y pasar a minúsculas
            const normalizar = (texto) => {
                if (!texto) return "";
                return texto
                    .toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "");
            };

            const b = normalizar(this.busqueda);

            return this.proveedores.filter(
                (p) =>
                    normalizar(p.nombre).includes(b) ||
                    normalizar(p.correo).includes(b),
            );
        },

        // 2. Calcular cuántas páginas hay en total según los filtrados
        get totalPaginas() {
            return (
                Math.ceil(this.proveedoresFiltrados.length / this.porPagina) ||
                1
            );
        },

        // 3. Cortar el arreglo para mostrar solo los 6 correspondientes a la página actual
        get proveedoresPaginados() {
            const inicio = (this.paginaActual - 1) * this.porPagina;
            const fin = inicio + this.porPagina;
            return this.proveedoresFiltrados.slice(inicio, fin);
        },

        // 4. Función para cambiar de página
        irPagina(pagina) {
            if (pagina >= 1 && pagina <= this.totalPaginas) {
                this.paginaActual = pagina;
            }
        },

        form: {
            nombre: "",
            correo: "",
            telefono: "",
            direccion: "",
            catalogo: "",
        },

        // ---------- FORMATO DE TELÉFONO EN VIVO ----------
        formatearTelefono(event) {
            let soloNumeros = event.target.value.replace(/\D/g, "");
            soloNumeros = soloNumeros.slice(0, 8);
            if (soloNumeros.length > 4) {
                soloNumeros =
                    soloNumeros.slice(0, 4) + " " + soloNumeros.slice(4);
            }
            this.form.telefono = soloNumeros;
            if (this.errors.telefono) delete this.errors.telefono;
        },

        // ---------- VALIDACIONES ----------
        telefonoValido() {
            if (!this.form.telefono) return true;
            return /^\d{4} \d{4}$/.test(this.form.telefono);
        },

        correoValido() {
            if (!this.form.correo) return true;
            return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(this.form.correo);
        },

        validarCorreo() {
            if (this.form.correo && !this.correoValido()) {
                this.errors.correo = [
                    "El correo electrónico no tiene un formato válido.",
                ];
            } else {
                delete this.errors.correo;
            }
        },

        formularioValido() {
            this.errors = {};
            if (!this.form.nombre.trim())
                this.errors.nombre = [
                    "El nombre de la empresa es obligatorio.",
                ];
            if (this.form.correo && !this.correoValido())
                this.errors.correo = [
                    "El correo electrónico no tiene un formato válido.",
                ];
            if (this.form.telefono && !this.telefonoValido())
                this.errors.telefono = [
                    "El teléfono debe tener el formato 0000 0000.",
                ];

            return Object.keys(this.errors).length === 0;
        },

        abrirCrear() {
            this.editando = false;
            this.limpiarFormulario();
            this.openModal = true;
        },

        abrirEditar(proveedor) {
            this.editando = proveedor.id;
            this.errors = {};
            this.archivo = null;
            this.form = {
                nombre: proveedor.nombre || "",
                correo: proveedor.correo || "",
                telefono: proveedor.telefono || "",
                direccion: proveedor.direccion || "",
            };
            this.openModal = true;
        },

        async guardarProveedor() {
            if (!this.formularioValido()) return;

            this.loading = true;
            this.errors = {};

            let formData = new FormData();
            formData.append("nombre", this.form.nombre);
            formData.append("correo", this.form.correo || "");
            formData.append("telefono", this.form.telefono || "");
            formData.append("direccion", this.form.direccion || "");

            if (this.archivo) {
                formData.append("archivo", this.archivo);
            }

            try {
                let response;
                if (this.editando) {
                    //formData.append('_method', 'PUT');
                    response = await axios.post(
                        `/proveedores/${this.editando}`,
                        formData,
                        {
                            headers: { "Content-Type": "multipart/form-data" },
                        },
                    );
                    let index = this.proveedores.findIndex(
                        (p) => p.id === this.editando,
                    );
                    if (index !== -1)
                        this.proveedores[index] = response.data.proveedor;
                } else {
                    response = await axios.post("/proveedores", formData, {
                        headers: { "Content-Type": "multipart/form-data" },
                    });
                    this.proveedores.unshift(response.data.proveedor);
                }

                this.openModal = false;
                this.limpiarFormulario();

                // 2. Toast de Éxito al guardar/editar
                Toast.fire({
                    icon: "success",
                    title: response.data.message,
                });
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    console.error("Error del servidor:", error);
                    // 3. Toast de Error general
                    Toast.fire({
                        icon: "error",
                        title: "Ocurrió un error inesperado al guardar.",
                    });
                }
            } finally {
                this.loading = false;
            }
        },

        async cambiarEstado(proveedor) {
            let esActivo = !proveedor.deleted_at;
            let accion = esActivo ? "deshabilitar" : "habilitar";
            let colorBoton = esActivo ? "#d33" : "#10b981"; // Rojo para deshabilitar, Verde para habilitar

            // 4. Reemplazamos el confirm() nativo por un Modal de SweetAlert
            let confirmacion = await Swal.fire({ customClass: { popup: 'swal-axstore' },
                title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} proveedor?`,
                text: `Estás a punto de ${accion} a "${proveedor.nombre}"`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: colorBoton,
                cancelButtonColor: "#6b7280",
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: "Cancelar",
            });

            if (!confirmacion.isConfirmed) return;

            try {
                let response = esActivo
                    ? await axios.delete(`/proveedores/${proveedor.id}`)
                    : await axios.post(`/proveedores/${proveedor.id}/restore`);

                let index = this.proveedores.findIndex(
                    (p) => p.id === proveedor.id,
                );
                if (index !== -1)
                    this.proveedores[index] = response.data.proveedor;

                // 5. Toast de Éxito al cambiar estado
                Toast.fire({
                    icon: "success",
                    title: response.data.message,
                });
            } catch (error) {
                console.error("Error al cambiar estado:", error);
                Toast.fire({
                    icon: "error",
                    title: "No se pudo cambiar el estado del proveedor.",
                });
            }
        },

        limpiarFormulario() {
            this.form = {
                nombre: "",
                correo: "",
                telefono: "",
                direccion: "",
                catalogo: "",
            };
            this.archivo = null;
            this.errors = {};
            this.editando = false;
        },
    }));

    Alpine.data(
        "perfilProveedor",
        (proveedorInicial, catalogosIniciales, historialInicial) => ({
            // 1. Inicializamos los datos que inyecta Laravel con Js::from()
            proveedor: proveedorInicial,
            catalogos: catalogosIniciales,
            historial: historialInicial,

            // 2. Variables de estado para el modal y el formulario
            modalCatalogo: false,
            archivoSeleccionado: null,

            formCatalogo: {
                nombre_referencia: "",
                tipo: "enlace", // Valor por defecto
                ruta_destino: "",
            },

            // 3. Funciones de la interfaz
            abrirModalCatalogo() {
                // Limpiamos el formulario antes de abrir
                this.formCatalogo = {
                    nombre_referencia: "",
                    tipo: "enlace",
                    ruta_destino: "",
                };
                this.archivoSeleccionado = null;
                this.modalCatalogo = true;
            },

            // 4. Lógica para guardar (Soporta archivos y texto gracias a FormData)
            async guardarCatalogo() {
                if (!this.formCatalogo.nombre_referencia) {
                    Toast.fire({
                        icon: "warning",
                        title: "El nombre es obligatorio",
                    });
                    return;
                }

                // Usamos FormData para poder enviar el PDF/Excel si se seleccionó uno
                let formData = new FormData();
                formData.append("id_proveedor", this.proveedor.id);
                formData.append(
                    "nombre_referencia",
                    this.formCatalogo.nombre_referencia,
                );
                formData.append("tipo", this.formCatalogo.tipo);

                if (this.formCatalogo.tipo === "enlace") {
                    formData.append(
                        "ruta_destino",
                        this.formCatalogo.ruta_destino,
                    );
                } else if (
                    this.formCatalogo.tipo === "archivo" &&
                    this.archivoSeleccionado
                ) {
                    formData.append("archivo", this.archivoSeleccionado);
                } else {
                    Toast.fire({
                        icon: "error",
                        title: "Debes adjuntar un archivo",
                    });
                    return;
                }

                try {
                    // Ajusta esta ruta a como la tengas en tu web.php
                    const response = await axios.post(
                        "/proveedores/catalogos",
                        formData,
                        {
                            headers: {
                                "Content-Type": "multipart/form-data",
                                Accept: "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                            },
                        },
                    );

                    console.log("ÉXITO REAL. Datos:", response.data);

                    // Agregamos el nuevo catálogo al arreglo para que aparezca al instante
                    this.catalogos.push(response.data.catalogo);

                    this.modalCatalogo = false;
                    Toast.fire({ icon: "success", title: "Recurso guardado" });
                } catch (error) {
                    console.error(error);
                    Toast.fire({
                        icon: "error",
                        title: "Error al guardar el recurso",
                    });
                }
            },

            // 5. Lógica para eliminar
            async eliminarCatalogo(id) {
                Swal.fire({ customClass: { popup: 'swal-axstore' },
                    title: "¿Eliminar recurso?",
                    text: "Esta acción no se puede deshacer.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Sí, eliminar",
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            // Ajusta esta ruta a tu backend
                            await axios.delete(`/proveedores/catalogos/${id}`);

                            // Sacamos el elemento eliminado de la lista visual
                            this.catalogos = this.catalogos.filter(
                                (c) => c.id !== id,
                            );

                            Toast.fire({
                                icon: "success",
                                title: "Recurso eliminado",
                            });
                        } catch (error) {
                            Toast.fire({
                                icon: "error",
                                title: "No se pudo eliminar",
                            });
                        }
                    }
                });
            },
        }),
    );
});
