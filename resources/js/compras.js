document.addEventListener("alpine:init", () => {
    Alpine.data("registroCompra", (proveedoresIniciales = [], variantesIniciales = []) => ({
        proveedores: proveedoresIniciales,
        variantes: variantesIniciales,
        proveedorId: "",
        fecha: new Date().toISOString().slice(0, 10),
        referencia: "",
        observaciones: "",
        busqueda: "",
        lineas: [],
        guardado: false,
        buscando: false,
        timeoutBusqueda: null,

        init() {
            this.$watch('busqueda', (value) => {
                clearTimeout(this.timeoutBusqueda);
                this.timeoutBusqueda = setTimeout(() => {
                    this.buscarEnServidor(value);
                }, 300);
            });
        },

        async buscarEnServidor(termino) {
            this.buscando = true;
            try {
                const response = await fetch(`/compras/buscar-variantes?q=${encodeURIComponent(termino)}`);
                const result = await response.json();
                // Si es un paginador de Laravel, los datos están en result.data
                this.variantes = result.data || []; 
            } catch (error) {
                console.error("Error buscando variantes", error);
            } finally {
                this.buscando = false;
            }
        },

        get variantesDisponibles() {
            return this.variantes.filter((variante) => {
                return !this.lineas.some((linea) => linea.id === variante.id);
            });
        },

        get proveedorSeleccionado() {
            return this.proveedores.find((proveedor) => String(proveedor.id) === String(this.proveedorId));
        },

        get subtotal() {
            return this.lineas.reduce((total, linea) => total + this.costoLinea(linea), 0);
        },

        get unidades() {
            return this.lineas.reduce((total, linea) => total + Number(linea.cantidad || 0), 0);
        },



        agregarVariante(variante) {
            this.lineas.push({
                ...variante,
                cantidad: 1,
                costo: 0,
            });
            this.busqueda = "";
        },

        quitarLinea(id) {
            this.lineas = this.lineas.filter((linea) => linea.id !== id);
        },

        costoLinea(linea) {
            return Math.max(0, Number(linea.cantidad || 0)) * Math.max(0, Number(linea.costo || 0));
        },



        moneda(valor) {
            return new Intl.NumberFormat("es-SV", { style: "currency", currency: "USD" }).format(Number(valor || 0));
        },

        formularioValido() {
            return this.proveedorId && this.fecha && this.lineas.length > 0 && this.lineas.every((linea) => Number(linea.cantidad) > 0 && Number(linea.costo) >= 0);
        },

        async guardarCompra() {
            if (!this.formularioValido()) return;

            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const payload = {
                id_proveedor: this.proveedorId,
                fecha_compra: this.fecha,
                referencia: this.referencia || null,
                observaciones: this.observaciones || null,
                lineas: this.lineas.map((linea) => ({
                    id_variante: linea.id,
                    cantidad: Number(linea.cantidad),
                    costo: Number(linea.costo),
                })),
            };

            try {
                const storeUrl = document.querySelector('meta[name="compras-store-url"]')?.content;
                const response = await fetch(storeUrl, {
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
                    throw new Error(data.message || "No se pudo registrar la compra.");
                }

                this.guardado = true;
                this.lineas = [];
                window.setTimeout(() => { this.guardado = false; }, 4500);
            } catch (error) {
                window.alert(error.message);
            }
        },
    }));
});