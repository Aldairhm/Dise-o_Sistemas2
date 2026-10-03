document.addEventListener("alpine:init", () => {
    Alpine.data("carritoVentas", (variantesIniciales = [], listaVendedores = [], currentUserId = null) => ({
        variantes: variantesIniciales,
        vendedores: listaVendedores,
        currentUserId: currentUserId,
        carrito: [],
        busqueda: "",
        descuentoGlobal: 0,
        metodoPago: "Efectivo",

        // Asignación de otro vendedor
        asignarOtroVendedor: false,
        busquedaVendedor: "",
        dropdownVendedoresAbierto: false,
        idVendedorAsignado: null,
        errorVendedor: false,

        modalTipoVentaAbierto: false,
        varianteSeleccionada: null,
        archivoComprobante: null,
        comprobantePreview: null,
        comprobanteNombre: "",
        comprobanteTamano: "",
        toast: {
            visible: false,
            mensaje: "",
            tipo: "warning", // 'warning', 'error', 'info', 'success'
            timeout: null,
        },

        init() {
            try {
                const stored = localStorage.getItem("ax_carrito");
                if (stored) {
                    const parsed = JSON.parse(stored);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        this.carrito = parsed;
                    }
                }
            } catch (e) {
                console.error("Error al cargar carrito desde localStorage:", e);
            }

            this.$watch("carrito", (nuevoCarrito) => {
                try {
                    localStorage.setItem("ax_carrito", JSON.stringify(nuevoCarrito));
                    window.dispatchEvent(new CustomEvent("ax-cart-updated", { detail: nuevoCarrito }));
                } catch (e) {}
            });

            this.$watch("metodoPago", (nuevoMetodo) => {
                if (nuevoMetodo !== "Transferencia Bancaria") {
                    this.removerComprobante();
                }
            });
        },

        /**
         * Muestra una notificación tipo Toast no intrusiva
         */
        mostrarToast(mensaje, tipo = "warning") {
            if (this.toast.timeout) {
                clearTimeout(this.toast.timeout);
            }
            this.toast.mensaje = mensaje;
            this.toast.tipo = tipo;
            this.toast.visible = true;

            this.toast.timeout = setTimeout(() => {
                this.toast.visible = false;
            }, 3500);
        },

        cerrarToast() {
            this.toast.visible = false;
            if (this.toast.timeout) {
                clearTimeout(this.toast.timeout);
            }
        },

        /**
         * Filtra la lista de vendedores dinámicamente según lo que escribe el usuario
         */
        get vendedoresFiltrados() {
            if (!this.vendedores || !Array.isArray(this.vendedores)) return [];
            if (!this.busquedaVendedor.trim()) {
                return this.vendedores;
            }
            const q = this.busquedaVendedor.toLowerCase().trim();
            return this.vendedores.filter((v) => {
                const nombre = (v.nombre_real || "").toLowerCase();
                const username = (v.username || "").toLowerCase();
                const rol = (v.rol || "").toLowerCase();
                return nombre.includes(q) || username.includes(q) || rol.includes(q);
            });
        },

        get vendedorSeleccionado() {
            if (!this.idVendedorAsignado) return null;
            return this.vendedores.find((v) => v.id === this.idVendedorAsignado) || null;
        },

        seleccionarVendedor(vend) {
            this.idVendedorAsignado = vend.id;
            this.busquedaVendedor = vend.nombre_real || vend.username;
            this.dropdownVendedoresAbierto = false;
            this.errorVendedor = false;
        },

        limpiarVendedor() {
            this.idVendedorAsignado = null;
            this.busquedaVendedor = "";
            this.dropdownVendedoresAbierto = true;
            this.errorVendedor = false;
        },

        /**
         * Filtra variantes en el catálogo en tiempo real
         */
        get variantesFiltradas() {
            if (!this.busqueda.trim()) {
                return this.variantes;
            }
            const q = this.busqueda.toLowerCase();
            return this.variantes.filter((v) => {
                const prod = (v.producto || "").toLowerCase();
                const variante = (v.variante || "").toLowerCase();
                const sku = (v.sku || "").toLowerCase();
                return prod.includes(q) || variante.includes(q) || sku.includes(q);
            });
        },

        /**
         * Intercepta el clic en 'Agregar' para preguntar si es venta en tienda o envío
         */
        solicitarTipoVenta(variante) {
            const stockDisponible = Number(variante.stock || 0);

            if (stockDisponible <= 0) {
                this.mostrarToast(
                    `El producto "${variante.producto}" está agotado en tienda.`,
                    "error"
                );
                return;
            }

            this.varianteSeleccionada = variante;
            this.modalTipoVentaAbierto = true;
        },

        cerrarModalTipoVenta() {
            this.modalTipoVentaAbierto = false;
            this.varianteSeleccionada = null;
        },

        seleccionarVentaTienda() {
            if (!this.varianteSeleccionada) return;
            const v = this.varianteSeleccionada;
            this.modalTipoVentaAbierto = false;
            this.varianteSeleccionada = null;
            this.agregarProducto(v);
        },

        seleccionarVentaEnvio() {
            if (!this.varianteSeleccionada) return;
            const variante = this.varianteSeleccionada;
            this.modalTipoVentaAbierto = false;
            this.varianteSeleccionada = null;

            // Agregar el producto al carrito
            this.agregarProducto(variante);

            this.mostrarToast(
                `"${variante.producto}" agregado. Puedes seguir agregando productos o pulsar "Despachar Pedido como Envío" al terminar.`,
                "info"
            );
        },

        abrirEntregaDesdeCarrito() {
            if (!this.carrito || this.carrito.length === 0) {
                this.mostrarToast("El carrito está vacío. Agrega productos primero.", "warning");
                return;
            }
            if (this.asignarOtroVendedor && !this.idVendedorAsignado) {
                this.errorVendedor = true;
                this.dropdownVendedoresAbierto = true;
                this.mostrarToast(
                    "Has marcado 'Otro vendedor'. Por favor selecciona el vendedor de la lista.",
                    "warning"
                );
                return;
            }
            window.dispatchEvent(new CustomEvent('abrir-modal-entrega', { 
                detail: {
                    items: this.carrito,
                    id_usuario: (this.asignarOtroVendedor && this.idVendedorAsignado) ? this.idVendedorAsignado : null,
                    nombre_vendedor: this.vendedorSeleccionado ? (this.vendedorSeleccionado.nombre_real || this.vendedorSeleccionado.username) : null
                }
            }));
        },

        /**
         * Agrega un producto/variante al carrito con validación de stock
         */
        agregarProducto(variante) {
            const stockDisponible = Number(variante.stock || 0);

            // 1. Validar si hay stock disponible en tienda
            if (stockDisponible <= 0) {
                this.mostrarToast(
                    `El producto "${variante.producto}" está agotado en tienda.`,
                    "error"
                );
                return;
            }

            const itemExistente = this.carrito.find((item) => item.id === variante.id);

            // 2. Si ya existe en el carrito, verificar si agregar 1 más excede el stock
            if (itemExistente) {
                if (itemExistente.cantidad + 1 > stockDisponible) {
                    this.mostrarToast(
                        `Stock máximo alcanzado para "${variante.producto}" (Disponible: ${stockDisponible} uds).`,
                        "warning"
                    );
                    return;
                }
                itemExistente.cantidad++;
                this.mostrarToast(
                    `Cantidad actualizada (${itemExistente.cantidad}/${stockDisponible} uds).`,
                    "info"
                );
            } else {
                // 3. Agregar como nuevo ítem al carrito
                this.carrito.push({
                    id: variante.id,
                    producto: variante.producto,
                    variante: variante.variante,
                    sku: variante.sku,
                    precio_venta: Number(variante.precio_venta || 0),
                    stock: stockDisponible,
                    imagen: variante.imagen || null,
                    cantidad: 1,
                    costo_extra: 0,
                });

                this.mostrarToast(
                    `"${variante.producto}" agregado al carrito.`,
                    "success"
                );
            }
        },

        /**
         * Incrementa la cantidad de un ítem validando stock
         */
        incrementar(item) {
            if (item.cantidad >= item.stock) {
                this.mostrarToast(
                    `No puedes agregar más de ${item.stock} unidades disponibles en tienda.`,
                    "warning"
                );
                return;
            }
            item.cantidad++;
        },

        /**
         * Decrementa la cantidad de un ítem con tope mínimo de 1
         */
        decrementar(item) {
            if (item.cantidad > 1) {
                item.cantidad--;
            }
        },

        /**
         * Valida el cambio manual desde el input numérico
         */
        actualizarCantidad(item, event) {
            let valor = parseInt(event.target.value, 10);

            if (isNaN(valor) || valor < 1) {
                item.cantidad = 1;
                event.target.value = 1;
                return;
            }

            if (valor > item.stock) {
                this.mostrarToast(
                    `Solo hay ${item.stock} unidades disponibles. Se ajustó al stock máximo.`,
                    "warning"
                );
                item.cantidad = item.stock;
                event.target.value = item.stock;
                return;
            }

            item.cantidad = valor;
        },

        /**
         * Actualiza y valida el costo extra ingresado por el vendedor para el producto
         */
        actualizarCostoExtra(item, event) {
            let valor = parseFloat(event.target.value);
            if (isNaN(valor) || valor < 0) {
                valor = 0;
            }
            item.costo_extra = valor;
        },

        /**
         * Elimina un ítem del carrito
         */
        eliminarItem(id) {
            const item = this.carrito.find((i) => i.id === id);
            this.carrito = this.carrito.filter((i) => i.id !== id);
            if (item) {
                this.mostrarToast(
                    `"${item.producto || item.variante || 'Producto'}" fue retirado del carrito.`,
                    "info"
                );
            }
        },

        /**
         * Vacía todo el carrito
         */
        vaciarCarrito() {
            if (this.carrito.length === 0) return;
            this.carrito = [];
            this.mostrarToast("El carrito ha sido vaciado.", "info");
        },

        // ─── CÁLCULOS MATEMÁTICOS EN TIEMPO REAL ───

        /**
         * Subtotal individual de una fila (Base + Costo Extra)
         */
        subtotalFila(item) {
            const base = (Number(item.cantidad) || 0) * (Number(item.precio_venta) || 0);
            const extra = Math.max(0, Number(item.costo_extra) || 0);
            return base + extra;
        },

        /**
         * Subtotal acumulado de productos base (sin extras)
         */
        get subtotalProductos() {
            return this.carrito.reduce(
                (acc, item) => acc + (Number(item.cantidad) || 0) * (Number(item.precio_venta) || 0),
                0
            );
        },

        /**
         * Total acumulado de costos extras aplicados a los productos
         */
        get totalCostosExtras() {
            return this.carrito.reduce(
                (acc, item) => acc + Math.max(0, Number(item.costo_extra) || 0),
                0
            );
        },

        /**
         * Subtotal general acumulado (Productos + Extras)
         */
        get subtotalGeneral() {
            return this.subtotalProductos + this.totalCostosExtras;
        },

        /**
         * Total del descuento aplicado
         */
        get totalDescuento() {
            const desc = Number(this.descuentoGlobal || 0);
            return Math.min(this.subtotalGeneral, Math.max(0, desc));
        },

        /**
         * Total definitivo a cobrar
         */
        get totalPagar() {
            return Math.max(0, this.subtotalGeneral - this.totalDescuento);
        },

        /**
         * Total de artículos / piezas en el carrito
         */
        get totalArticulos() {
            return this.carrito.reduce(
                (acc, item) => acc + Number(item.cantidad || 0),
                0
            );
        },

        /**
         * Formatea valores monetarios a USD ($0.00)
         */
        moneda(valor) {
            return new Intl.NumberFormat("es-SV", {
                style: "currency",
                currency: "USD",
                minimumFractionDigits: 2,
            }).format(Number(valor || 0));
        },

        /**
         * Maneja la selección del archivo de comprobante de transferencia bancaria
         */
        onComprobanteSeleccionado(event) {
            const file = event.target.files ? event.target.files[0] : null;
            if (!file) return;

            // Validar que sea un formato de imagen soportado
            const tiposValidos = ["image/jpeg", "image/png", "image/jpg", "image/webp"];
            if (!tiposValidos.includes(file.type)) {
                this.mostrarToast(
                    "El comprobante debe ser un archivo de imagen válido (JPG, PNG o WEBP).",
                    "error"
                );
                event.target.value = "";
                return;
            }

            // Validar tamaño máximo (5MB)
            const maxBytes = 5 * 1024 * 1024;
            if (file.size > maxBytes) {
                this.mostrarToast(
                    "La imagen del comprobante no debe superar los 5MB.",
                    "warning"
                );
                event.target.value = "";
                return;
            }

            this.archivoComprobante = file;
            this.comprobanteNombre = file.name;
            this.comprobanteTamano = (file.size / 1024).toFixed(1) + " KB";

            const reader = new FileReader();
            reader.onload = (e) => {
                this.comprobantePreview = e.target.result;
            };
            reader.readAsDataURL(file);

            this.mostrarToast("Comprobante adjuntado correctamente.", "success");
        },

        /**
         * Elimina el comprobante cargado
         */
        removerComprobante() {
            this.archivoComprobante = null;
            this.comprobantePreview = null;
            this.comprobanteNombre = "";
            this.comprobanteTamano = "";

            const input = document.querySelector('input[x-ref="comprobanteInput"]');
            if (input) {
                input.value = "";
            }
        },

        procesando: false,

        /**
         * Valida si la venta está lista para procesarse
         */
        puedeProcesar() {
            if (this.carrito.length === 0 || this.totalPagar <= 0) {
                return false;
            }
            if (this.metodoPago === "Transferencia Bancaria" && !this.archivoComprobante) {
                return false;
            }
            return true;
        },

        /**
         * Envía la venta al backend para ser procesada atómicamente con soporte multipart/FormData
         */
        async procesarVenta() {
            if (this.procesando) return;

            if (this.asignarOtroVendedor && !this.idVendedorAsignado) {
                this.errorVendedor = true;
                this.dropdownVendedoresAbierto = true;
                this.mostrarToast(
                    "Has marcado 'Otro vendedor'. Por favor selecciona el vendedor de la lista.",
                    "warning"
                );
                return;
            }

            if (this.metodoPago === "Transferencia Bancaria" && !this.archivoComprobante) {
                this.mostrarToast(
                    "Es obligatorio adjuntar el comprobante de la transferencia bancaria.",
                    "warning"
                );
                return;
            }

            if (!this.puedeProcesar()) return;

            this.procesando = true;
            const token = document.querySelector('meta[name="csrf-token"]')?.content;

            const formData = new FormData();
            formData.append("metodo_pago", this.metodoPago);
            formData.append("tipo_venta", "Tienda");
            formData.append("direccion_entrega", "Venta en mostrador / POS");
            formData.append("descuento", Number(this.descuentoGlobal || 0));

            // Si se asignó a otro vendedor, adjuntar id_usuario
            if (this.asignarOtroVendedor && this.idVendedorAsignado) {
                formData.append("id_usuario", this.idVendedorAsignado);
            }

            this.carrito.forEach((item, index) => {
                formData.append(`lineas[${index}][id_variante]`, item.id);
                formData.append(`lineas[${index}][cantidad]`, Number(item.cantidad));
                formData.append(`lineas[${index}][precio_unitario]`, Number(item.precio_venta));
                formData.append(`lineas[${index}][costo_extra]`, Math.max(0, Number(item.costo_extra) || 0));
            });

            if (this.metodoPago === "Transferencia Bancaria" && this.archivoComprobante) {
                formData.append("comprobante_pago", this.archivoComprobante);
            }

            try {
                const response = await fetch("/ventas", {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": token,
                    },
                    body: formData,
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || "Error al procesar la venta.");
                }

                // Descontar stock localmente en el catálogo para sincronía instantánea
                this.carrito.forEach((item) => {
                    const v = this.variantes.find((variante) => variante.id === item.id);
                    if (v) {
                        v.stock = Math.max(0, v.stock - item.cantidad);
                    }
                });

                this.mostrarToast(
                    `¡Venta #${data.id_venta} procesada exitosamente! Total: ${this.moneda(data.total)}`,
                    "success"
                );

                // Limpiar carrito, campos y comprobante
                this.carrito = [];
                this.descuentoGlobal = 0;
                this.removerComprobante();
                this.asignarOtroVendedor = false;
                this.idVendedorAsignado = null;
                this.busquedaVendedor = "";
                this.errorVendedor = false;
                this.dropdownVendedoresAbierto = false;
            } catch (error) {
                this.mostrarToast(error.message, "error");
            } finally {
                this.procesando = false;
            }
        },
    }));
});
