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

        get variantesFiltradas() {
            const termino = this.busqueda.trim().toLowerCase();
            return this.variantes.filter((v) => {
                if (!termino) return true;
                return [v.producto, v.variante, v.sku].join(' ').toLowerCase().includes(termino);
            });
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