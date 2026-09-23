document.addEventListener('alpine:init', () => {
    Alpine.data('transferenciaBodega', (variantesIniciales = []) => ({
        variantes: variantesIniciales,
        busqueda: '',
        varianteSeleccionada: null,
        cantidad: 1,
        observacion: '',
        enviando: false,
        mensaje: '',
        error: '',
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
                this.variantes = result.data || []; 
            } catch (error) {
                console.error("Error buscando variantes", error);
            } finally {
                this.buscando = false;
            }
        },

        get variantesFiltradas() {
            return this.variantes;
        },

        seleccionar(variante) {
            if (variante.reserva <= 0) return;
            this.varianteSeleccionada = variante;
            this.cantidad = 1;
            this.busqueda = '';
        },

        limpiarSeleccion() {
            this.varianteSeleccionada = null;
        },

        async enviar() {
            if (!this.varianteSeleccionada || this.cantidad < 1 || this.cantidad > this.varianteSeleccionada.reserva) {
                return;
            }

            this.enviando = true;
            this.mensaje = '';
            this.error = '';

            try {
                const url = document.querySelector('meta[name="transferencia-tienda-url"]')?.content;
                const token = document.querySelector('meta[name="csrf-token"]')?.content;

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({
                        id_variante: this.varianteSeleccionada.id,
                        cantidad: this.cantidad,
                        observacion: this.observacion,
                    }),
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || Object.values(data.errors || {}).flat().join(' ') || 'No se pudo completar la transferencia.');
                }

                this.mensaje = data.message;
                window.setTimeout(() => window.location.reload(), 900);
            } catch (err) {
                this.error = err.message;
            } finally {
                this.enviando = false;
            }
        },
    }));
});