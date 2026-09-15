document.addEventListener("alpine:init", () => {
    Alpine.data("registroCompra", (proveedoresIniciales = [], variantesIniciales = []) => ({
        proveedores: proveedoresIniciales,
        variantes: variantesIniciales,
        proveedorId: "",
        fecha: new Date().toISOString().slice(0, 10),
        referencia: "",
        observaciones: "",
        busqueda: "",
        comisionTipo: "porcentaje",
        margenTipo: "porcentaje",
        comisionValor: 5,
        margenValor: 30,
        lineas: [],
        guardado: false,

        get variantesDisponibles() {
            const termino = this.busqueda.trim().toLowerCase();
            return this.variantes.filter((variante) => {
                const yaAgregada = this.lineas.some((linea) => linea.id === variante.id);
                const coincide = !termino || [variante.producto, variante.variante, variante.sku]
                    .join(" ")
                    .toLowerCase()
                    .includes(termino);
                return !yaAgregada && coincide;
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

        get totalComision() {
            return this.lineas.reduce((total, linea) => total + this.comisionLinea(linea), 0);
        },

        get totalMargen() {
            return this.lineas.reduce((total, linea) => total + this.margenLinea(linea), 0);
        },

        get totalCompra() {
            return this.subtotal;
        },

        get precioVentaTotal() {
            return this.lineas.reduce((total, linea) => total + this.precioVenta(linea), 0);
        },

        agregarVariante(variante) {
            this.lineas.push({
                ...variante,
                cantidad: 1,
                costo: 0,
                personalizado: false,
                comisionTipo: this.comisionTipo,
                comisionValor: this.comisionValor,
                margenTipo: this.margenTipo,
                margenValor: this.margenValor,
            });
            this.busqueda = "";
        },

        alternarPersonalizacion(linea) {
            linea.personalizado = !linea.personalizado;
            if (linea.personalizado) {
                linea.comisionTipo = this.comisionTipo;
                linea.comisionValor = this.comisionValor;
                linea.margenTipo = this.margenTipo;
                linea.margenValor = this.margenValor;
            }
        },

        quitarLinea(id) {
            this.lineas = this.lineas.filter((linea) => linea.id !== id);
        },

        costoLinea(linea) {
            return Math.max(0, Number(linea.cantidad || 0)) * Math.max(0, Number(linea.costo || 0));
        },

        porcentajeOValor(valor, tipo, base) {
            const cantidad = Math.max(0, Number(valor || 0));
            return tipo === "porcentaje" ? base * (cantidad / 100) : cantidad;
        },

        comisionLinea(linea) {
            const tipo = linea.personalizado ? linea.comisionTipo : this.comisionTipo;
            const valor = linea.personalizado ? linea.comisionValor : this.comisionValor;
            return this.porcentajeOValor(valor, tipo, this.costoLinea(linea));
        },

        margenLinea(linea) {
            const base = this.costoLinea(linea) + this.comisionLinea(linea);
            const tipo = linea.personalizado ? linea.margenTipo : this.margenTipo;
            const valor = linea.personalizado ? linea.margenValor : this.margenValor;
            return this.porcentajeOValor(valor, tipo, base);
        },

        precioVenta(linea) {
            return this.costoLinea(linea) + this.comisionLinea(linea) + this.margenLinea(linea);
        },

        moneda(valor) {
            return new Intl.NumberFormat("es-SV", { style: "currency", currency: "USD" }).format(Number(valor || 0));
        },

        porcentaje(valor) {
            return `${Number(valor || 0).toFixed(2)}%`;
        },

        formularioValido() {
            return this.proveedorId && this.fecha && this.lineas.length > 0 && this.lineas.every((linea) => Number(linea.cantidad) > 0 && Number(linea.costo) >= 0);
        },

        guardarDemo() {
            if (!this.formularioValido()) return;
            this.guardado = true;
            window.setTimeout(() => { this.guardado = false; }, 4500);
        },
    }));
});