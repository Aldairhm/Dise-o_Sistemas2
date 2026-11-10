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
        solicitudBusqueda: 0,
        errorBusqueda: '',

        init() {
            this.$watch('busqueda', (value) => {
                clearTimeout(this.timeoutBusqueda);
                this.timeoutBusqueda = setTimeout(() => {
                    this.buscarEnServidor(value);
                }, 300);
            });
        },

        async buscarEnServidor(termino) {
            const solicitud = ++this.solicitudBusqueda;
            this.buscando = true;
            this.errorBusqueda = '';
            try {
                const response = await fetch(`/compras/buscar-variantes?q=${encodeURIComponent(termino)}`);
                if (!response.ok) throw new Error('No se pudieron cargar las variantes.');
                const result = await response.json();
                if (solicitud !== this.solicitudBusqueda) return;
                this.variantes = result.data || []; 
            } catch (error) {
                if (solicitud === this.solicitudBusqueda) this.errorBusqueda = error.message;
            } finally {
                if (solicitud === this.solicitudBusqueda) this.buscando = false;
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
            if (this.enviando || !this.varianteSeleccionada || this.cantidad < 1 || this.cantidad > this.varianteSeleccionada.reserva) {
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
                // Actualizar existencias localmente sin recargar la página
                if (data.movimiento && data.movimiento.reserva_nueva !== undefined) {
                    this.varianteSeleccionada.reserva = data.movimiento.reserva_nueva;
                    const vEnLista = this.variantes.find(v => v.id === this.varianteSeleccionada.id);
                    if (vEnLista) {
                        vEnLista.reserva = data.movimiento.reserva_nueva;
                    }
                }
                this.limpiarSeleccion();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Transferencia Exitosa!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } catch (err) {
                this.error = err.message;
            } finally {
                this.enviando = false;
            }
        },
    }));
});