document.addEventListener("alpine:init", () => {
    Alpine.data("carritoVentas", (variantesIniciales = []) => ({
        variantes: variantesIniciales,
        carrito: [],
        busqueda: "",
        descuentoGlobal: 0,
        metodoPago: "Efectivo",
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
                    `"${item.variante || item.producto}" fue retirado del carrito.`,
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

        procesando: false,

        /**
         * Valida si la venta está lista para procesarse
         */
        puedeProcesar() {
            return this.carrito.length > 0 && this.totalPagar > 0;
        },

        /**
         * Envía la venta al backend para ser procesada atómicamente
         */
        async procesarVenta() {
            if (!this.puedeProcesar() || this.procesando) return;

            this.procesando = true;
            const token = document.querySelector('meta[name="csrf-token"]')?.content;

            const payload = {
                metodo_pago: this.metodoPago,
                descuento: Number(this.descuentoGlobal || 0),
                lineas: this.carrito.map((item) => ({
                    id_variante: item.id,
                    cantidad: Number(item.cantidad),
                    precio_unitario: Number(item.precio_venta),
                    costo_extra: Math.max(0, Number(item.costo_extra) || 0),
                })),
            };

            try {
                const response = await fetch("/ventas", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": token,
                    },
                    body: JSON.stringify(payload),
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

                // Limpiar carrito y campos
                this.carrito = [];
                this.descuentoGlobal = 0;
            } catch (error) {
                this.mostrarToast(error.message, "error");
            } finally {
                this.procesando = false;
            }
        },
    }));
});
