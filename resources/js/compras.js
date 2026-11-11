document.addEventListener("alpine:init", () => {
    Alpine.data("registroCompra", (proveedoresIniciales, catalogoInicial) => ({
        proveedores: proveedoresIniciales,
        variantes: catalogoInicial.data,
        totalVariantes: catalogoInicial.total,
        paginaVariantes: catalogoInicial.current_page,
        ultimaPaginaVariantes: catalogoInicial.last_page,
        terminoVariantes: '',
        proveedorId: "",
        fecha: new Date().toISOString().slice(0, 10),
        referencia: "",
        observaciones: "",
        busqueda: "",
        lineas: [],
        guardado: false,
        guardando: false,
        buscando: false,
        timeoutBusqueda: null,
        solicitudBusqueda: 0,
        errorBusqueda: '',

        init() {
            this.$watch('busqueda', (value) => {
                clearTimeout(this.timeoutBusqueda);
                this.solicitudBusqueda++;
                this.buscando = true;
                this.timeoutBusqueda = setTimeout(() => {
                    this.buscarEnServidor(value);
                }, 300);
            });
        },

        async buscarEnServidor(termino, pagina = 1) {
            const solicitud = ++this.solicitudBusqueda;
            this.buscando = true;
            this.errorBusqueda = '';
            try {
                const response = await fetch(`/compras/buscar-variantes?q=${encodeURIComponent(termino)}&page=${pagina}`);
                if (!response.ok) throw new Error('No se pudieron cargar las variantes.');
                const result = await response.json();
                if (solicitud !== this.solicitudBusqueda) return;
                this.variantes = pagina === 1 ? result.data : [...this.variantes, ...result.data];
                this.totalVariantes = result.total;
                this.paginaVariantes = result.current_page;
                this.ultimaPaginaVariantes = result.last_page;
                this.terminoVariantes = termino;
            } catch (error) {
                if (solicitud === this.solicitudBusqueda) this.errorBusqueda = error.message;
            } finally {
                if (solicitud === this.solicitudBusqueda) this.buscando = false;
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
            if (this.guardando || !this.formularioValido()) return;
            this.guardando = true;

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
            } finally {
                this.guardando = false;
            }
        },
    }));
});
