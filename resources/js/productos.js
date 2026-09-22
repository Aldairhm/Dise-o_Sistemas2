// resources/js/productos.js

document.addEventListener('DOMContentLoaded', () => {

    // ─── Referencias DOM ───────────────────────────────────────────
    const btnAgregar     = document.getElementById('btnAgregarVariante');
    const modalEl        = document.getElementById('modalVariante');
    const backdropEl     = document.getElementById('modalVarianteBackdrop');
    const panelEl        = document.getElementById('modalVariantePanel');
    const formVariante   = document.getElementById('formVariante');
    const tablaBody      = document.getElementById('tablaVariantes')?.querySelector('tbody');
    const dropZone       = document.getElementById('dropZoneImagenes');
    const fileInput      = document.getElementById('imagenesVarianteInput');
    const previewZone    = document.getElementById('previewImagenes');
    const errorBox       = document.getElementById('modalVarianteError');
    const errorMsg       = document.getElementById('modalVarianteErrorMsg');

    let archivosImagenes = [];

    // ─── Rutas y CSRF ─────────────────────────────────────────────
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const ROUTES    = window.PRODUCTOS_ROUTES || {};

    // ─── Helpers: Swal unificados ──────────────────────────────────
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2200,
        timerProgressBar: true,
        customClass: { popup: 'swal-axstore' },
    });

    function alertOk(msg) {
        Swal.fire({
            icon: 'success',
            title: '¡Operación exitosa!',
            text: msg,
            timer: 1800,
            showConfirmButton: false,
            customClass: { popup: 'swal-axstore' },
        });
    }

    function alertErr(msg) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: msg,
            confirmButtonColor: '#ef4444',
            customClass: { popup: 'swal-axstore' },
        });
    }

    async function confirmDanger(title, html) {
        return Swal.fire({
            title,
            html: `<p class="text-slate-600 text-sm">${html}</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: '<i class="fas fa-trash mr-1"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            customClass: { popup: 'swal-axstore' },
        });
    }

    // ─── SKU preview basado en nombre (igual que el servidor: VAR-NOM-###) ────
    function previewSkuVariante(nombre) {
        // Eliminar tildes y caracteres no-ASCII
        const ascii = nombre.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        // Solo letras, primeras 3 en mayúsculas
        const prefijo = ascii.replace(/[^a-zA-Z]/g, '').substring(0, 3).toUpperCase();
        if (!prefijo) return 'VAR-???-...';
        return `VAR-${prefijo}-###`;
    }

    // ─── Control del modal personalizado (sin Bootstrap) ──────────
    function openModal() {
        modalEl.classList.remove('hidden');
        // Forzar reflow para que la transición funcione
        modalEl.offsetHeight;
        modalEl.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
        // Preview SKU basado en nombre al abrir en modo "nuevo"
        const varianteId = document.getElementById('variante_id').value;
        if (!varianteId) {
            const nombre = document.getElementById('nombre_variante').value;
            document.getElementById('sku').value = previewSkuVariante(nombre);
        }
    }

    function closeModal() {
        modalEl.classList.remove('modal-open');
        document.body.style.overflow = '';
        setTimeout(() => modalEl.classList.add('hidden'), 300);
        hideModalError();
    }

    // Cerrar con botones y backdrop
    document.getElementById('btnCerrarModalVariante')?.addEventListener('click', closeModal);
    document.getElementById('btnCancelarVariante')?.addEventListener('click', closeModal);
    backdropEl?.addEventListener('click', closeModal);

    // Cerrar con Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modalEl.classList.contains('hidden')) closeModal();
    });

    // ─── Preview SKU en tiempo real mientras se escribe el nombre ──
    document.getElementById('nombre_variante')?.addEventListener('input', (e) => {
        const varianteId = document.getElementById('variante_id').value;
        // Solo actualizar preview en modo "nuevo" (no al editar)
        if (!varianteId) {
            document.getElementById('sku').value = previewSkuVariante(e.target.value);
        }
    });

    // ─── Cálculo de Precio Venta ───────────────────────────────────
    const costoInput    = document.getElementById('costo_promedio');
    const gananciaInput = document.getElementById('porcentaje_ganancia');
    const precioInput   = document.getElementById('precio_venta');
    const comisionEl    = document.getElementById('comision_producto');

    function calcularPrecio() {
        if (!costoInput || !gananciaInput || !precioInput) return;
        const costo    = parseFloat(costoInput.value)    || 0;
        const ganancia = parseFloat(gananciaInput.value) || 0;
        const comision = parseFloat(comisionEl?.value)   || 0;
        const precio   = (costo * (1 + (ganancia / 100))) + comision;
        precioInput.value = precio.toFixed(2);
    }

    // Solo ganancia es editable; al cambiarla recalcula el precio
    gananciaInput?.addEventListener('input', calcularPrecio);

    // ─── Error inline del modal ────────────────────────────────────
    function showModalError(msg) {
        errorMsg.textContent = msg;
        errorBox.classList.remove('hidden');
        errorBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideModalError() {
        errorBox?.classList.add('hidden');
    }

    // ─── Drag & Drop ───────────────────────────────────────────────
    if (dropZone && fileInput) {
        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });
        fileInput.addEventListener('change', (e) => handleFiles(e.target.files));
    }

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                archivosImagenes.push(file);
                renderPreview(file);
            } else {
                Toast.fire({ icon: 'warning', title: 'Solo se permiten imágenes.' });
            }
        });
    }

    function renderPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const container = document.createElement('div');
            container.className = 'preview-img-container is-new relative group rounded-lg overflow-hidden';
            container.style.width = '100px';
            container.style.height = '100px';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-full h-full object-cover';

            // Botón eliminar
            const btnRemove = document.createElement('button');
            btnRemove.className = 'absolute top-1 right-1 bg-red-500/90 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-20 shadow-sm hover:bg-red-600 text-xs';
            btnRemove.type = 'button';
            btnRemove.innerHTML = '&times;';
            btnRemove.onclick = (event) => {
                event.stopPropagation();
                const index = archivosImagenes.indexOf(file);
                if (index > -1) {
                    archivosImagenes.splice(index, 1);
                    container.remove();
                    // Si se elimina la principal, hacer la primera principal
                    let principalIdx = parseInt(document.getElementById('imagen_principal_index').value);
                    if (principalIdx === index) {
                        document.getElementById('imagen_principal_index').value = archivosImagenes.length > 0 ? 0 : 0;
                    } else if (principalIdx > index) {
                        document.getElementById('imagen_principal_index').value = principalIdx - 1;
                    }
                    actualizarPrincipalUI();
                }
            };

            // Botón estrella (Principal)
            const btnStar = document.createElement('button');
            btnStar.className = 'btn-star-img absolute bottom-1 left-1/2 transform -translate-x-1/2 bg-white/90 backdrop-blur rounded-full w-6 h-6 flex items-center justify-center text-slate-300 hover:text-yellow-500 transition-all z-20 shadow-sm text-xs cursor-pointer opacity-0 group-hover:opacity-100';
            btnStar.type = 'button';
            btnStar.innerHTML = '<i class="fas fa-star"></i>';
            btnStar.onclick = (event) => {
                event.stopPropagation();
                document.getElementById('imagen_principal_index').value = archivosImagenes.indexOf(file);
                document.getElementById('imagen_existente_principal_id').value = '';
                actualizarPrincipalUI();
            };

            container.appendChild(img);
            container.appendChild(btnRemove);
            container.appendChild(btnStar);
            previewZone.appendChild(container);

            // Si es la primera imagen subida y no estamos editando, hacerla principal por defecto
            const isEdit = document.getElementById('variante_id').value !== '';
            if (!isEdit && archivosImagenes.length === 1) {
                document.getElementById('imagen_principal_index').value = 0;
            }
            actualizarPrincipalUI();
        };
        reader.readAsDataURL(file);
    }

    function renderExistingImage(imgObj) {
        const container = document.createElement('div');
        container.className = 'preview-img-container is-existing relative group rounded-lg overflow-hidden';
        container.style.width = '100px';
        container.style.height = '100px';
        container.dataset.id = imgObj.id;

        const img = document.createElement('img');
        img.src = '/storage/' + imgObj.ruta_imagen;
        img.className = 'w-full h-full object-cover';

        // Botón eliminar
        const btnRemove = document.createElement('button');
        btnRemove.className = 'absolute top-1 right-1 bg-red-500/90 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-20 shadow-sm hover:bg-red-600 text-xs';
        btnRemove.type = 'button';
        btnRemove.innerHTML = '&times;';
        btnRemove.onclick = (event) => {
            event.stopPropagation();
            container.remove();
            
            // Agregar id a los eliminados
            const delContainer = document.getElementById('deletedImagesContainer');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'imagenes_eliminadas[]';
            input.value = imgObj.id;
            delContainer.appendChild(input);

            // Si se elimina la principal
            if (document.getElementById('imagen_existente_principal_id').value == imgObj.id) {
                document.getElementById('imagen_existente_principal_id').value = '';
            }
            actualizarPrincipalUI();
        };

        // Botón estrella (Principal)
        const btnStar = document.createElement('button');
        btnStar.className = 'btn-star-img absolute bottom-1 left-1/2 transform -translate-x-1/2 bg-white/90 backdrop-blur rounded-full w-6 h-6 flex items-center justify-center text-slate-300 hover:text-yellow-500 transition-all z-20 shadow-sm text-xs cursor-pointer opacity-0 group-hover:opacity-100';
        btnStar.type = 'button';
        btnStar.innerHTML = '<i class="fas fa-star"></i>';
        btnStar.onclick = (event) => {
            event.stopPropagation();
            document.getElementById('imagen_existente_principal_id').value = imgObj.id;
            document.getElementById('imagen_principal_index').value = '-1';
            actualizarPrincipalUI();
        };

        container.appendChild(img);
        container.appendChild(btnRemove);
        container.appendChild(btnStar);
        previewZone.appendChild(container);

        if (imgObj.es_principal) {
            document.getElementById('imagen_existente_principal_id').value = imgObj.id;
            document.getElementById('imagen_principal_index').value = '-1';
        }
        actualizarPrincipalUI();
    }

    function actualizarPrincipalUI() {
        const principalIndex = parseInt(document.getElementById('imagen_principal_index').value);
        const principalExistenteId = parseInt(document.getElementById('imagen_existente_principal_id').value);

        // Actualizar nuevas
        const newContainers = previewZone.querySelectorAll('.preview-img-container.is-new');
        newContainers.forEach((cont, idx) => {
            const isPrincipal = (idx === principalIndex && principalIndex >= 0);
            togglePrincipalStyles(cont, isPrincipal);
        });

        // Actualizar existentes
        const existingContainers = previewZone.querySelectorAll('.preview-img-container.is-existing');
        existingContainers.forEach((cont) => {
            const isPrincipal = (parseInt(cont.dataset.id) === principalExistenteId && principalExistenteId > 0);
            togglePrincipalStyles(cont, isPrincipal);
        });
    }

    function togglePrincipalStyles(cont, isPrincipal) {
        const starBtn = cont.querySelector('.btn-star-img');
        if (isPrincipal) {
            cont.classList.add('ring-2', 'ring-blue-500', 'ring-offset-1');
            if (starBtn) {
                starBtn.classList.remove('text-slate-300', 'opacity-0');
                starBtn.classList.add('text-yellow-500', 'scale-110', 'opacity-100');
                starBtn.parentElement.classList.remove('group-hover:opacity-100');
            }
        } else {
            cont.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-1');
            if (starBtn) {
                starBtn.classList.add('text-slate-300', 'opacity-0');
                starBtn.classList.remove('text-yellow-500', 'scale-110', 'opacity-100');
                starBtn.parentElement.classList.add('group-hover:opacity-100');
            }
        }
    }

    // ─── Utilidades del modal ──────────────────────────────────────
    function clearModal() {
        formVariante?.reset();
        document.getElementById('variante_id').value = '';
        document.getElementById('sku').value = '';
        document.getElementById('estado').value = '1';
        if (costoInput) costoInput.value = '0.00';
        if (gananciaInput) gananciaInput.value = '';
        if (precioInput) precioInput.value = '0.00';
        document.getElementById('imagen_principal_index').value = '-1';
        document.getElementById('imagen_existente_principal_id').value = '';
        document.getElementById('deletedImagesContainer').innerHTML = '';
        document.querySelectorAll('.atributo-input').forEach(input => input.value = ''); // Limpiar atributos
        archivosImagenes = [];
        if (previewZone) previewZone.innerHTML = '';
        document.getElementById('modalVarianteLabel').innerText = 'Nueva Variante';
        document.getElementById('modalVarianteSubtitle').innerText = 'Completa los campos para registrar la variante';
        document.getElementById('btnGuardarTexto').innerText = 'GUARDAR VARIANTE';
        hideModalError();
    }

    function fillModal(v, isDuplicar = false) {
        document.getElementById('nombre_variante').value = isDuplicar ? `${v.nombre_variante} (Copia)` : v.nombre_variante;
        document.getElementById('sku').value = isDuplicar ? previewSkuVariante(v.nombre_variante ?? '') : (v.sku ?? previewSkuVariante(v.nombre_variante ?? ''));

        document.getElementById('estado').value = isDuplicar ? '1' : (v.estado ?? '1');

        if (isDuplicar) {
            // Al duplicar: costo y precio se reinician (el costo lo asignan las compras)
            if (costoInput)    costoInput.value    = '0.00';
            if (gananciaInput) gananciaInput.value = v.porcentaje_ganancia ?? '';
            if (precioInput)   precioInput.value   = '0.00';
        } else {
            if (costoInput)    costoInput.value    = v.costo_promedio ?? '';
            if (gananciaInput) gananciaInput.value = v.porcentaje_ganancia ?? '';
            if (precioInput)   precioInput.value   = v.precio_venta ?? '';
        }

        // Llenar valores de atributos si existen
        document.querySelectorAll('.atributo-input').forEach(input => input.value = ''); // Limpiar todos
        if (v.valores && v.valores.length > 0) {
            v.valores.forEach(valorObj => {
                const input = document.getElementById(`atributo_${valorObj.id_atributo}`);
                if (input) {
                    input.value = valorObj.valor;
                }
            });
        }

        if (!isDuplicar) {
            document.getElementById('variante_id').value = v.id;
            document.getElementById('modalVarianteLabel').innerText = 'Editar Variante';
            document.getElementById('modalVarianteSubtitle').innerText = `Modificando: ${v.nombre_variante}`;
            document.getElementById('btnGuardarTexto').innerText = 'GUARDAR CAMBIOS';
            
            // Mostrar imágenes existentes si hay
            if (v.imagenes && v.imagenes.length > 0) {
                v.imagenes.forEach(img => {
                    renderExistingImage(img);
                });
            }
        } else {
            document.getElementById('modalVarianteLabel').innerText = 'Duplicar Variante';
            document.getElementById('modalVarianteSubtitle').innerText = 'Copia de: ' + v.nombre_variante;
            document.getElementById('btnGuardarTexto').innerText = 'GUARDAR VARIANTE';
        }
    }

    // ─── Abrir modal (Nueva) ───────────────────────────────────────
    btnAgregar?.addEventListener('click', () => {
        clearModal();
        openModal();
    });

    // ─── Submit: guardar / actualizar ─────────────────────────────
    formVariante?.addEventListener('submit', async (e) => {
        e.preventDefault();
        hideModalError();

        const varianteId = document.getElementById('variante_id').value;
        const isEdit     = varianteId !== '';
        const url        = isEdit ? `${ROUTES.updateVariante}/${varianteId}` : ROUTES.storeVariante;

        // Validaciones manuales para reemplazar las nativas de HTML
        const nombreVal = document.getElementById('nombre_variante').value.trim();
        const costoVal = costoInput ? costoInput.value.trim() : '0';
        const gananciaVal = gananciaInput ? gananciaInput.value.trim() : '0';

        if (!nombreVal) {
            showModalError('El nombre de la variante es obligatorio.');
            return;
        }

        if (!costoVal || isNaN(parseFloat(costoVal)) || parseFloat(costoVal) < 0) {
            showModalError('El costo promedio es inválido.');
            return;
        }

        if (!gananciaVal || parseFloat(gananciaVal) < 0) {
            showModalError('El porcentaje de ganancia es requerido y debe ser mayor o igual a 0.');
            return;
        }

        // Validación: debe haber al menos una imagen
        if (previewZone && previewZone.children.length === 0) {
            showModalError('Debes agregar al menos una imagen para la variante.');
            return;
        }

        // El SKU lo genera el servidor; limpiar el campo preview antes de enviar
        const skuInput = document.getElementById('sku');
        skuInput.value = ''; // El servidor asignará el SKU real

        const formData = new FormData(formVariante);
        archivosImagenes.forEach(f => formData.append('imagenes[]', f));
        if (isEdit) formData.append('_method', 'PUT');

        const btnGuardar = document.getElementById('btnGuardarVariante');
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...';

        try {
            const res  = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData,
            });
            const data = await res.json();

            if (res.ok && data.success) {
                closeModal();
                alertOk(data.message);
                setTimeout(() => location.reload(), 1900);
            } else {
                const msg = data.errors
                    ? Object.values(data.errors).flat().join(' · ')
                    : (data.message || 'Error de validación.');
                showModalError(msg);
            }
        } catch {
            showModalError('Error de red. Intenta de nuevo.');
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fas fa-check mr-2"></i><span id="btnGuardarTexto">GUARDAR VARIANTE</span>';
        }
    });

    // ─── Acciones delegadas en tabla (editar / duplicar / eliminar)──
    tablaBody?.addEventListener('click', async (e) => {
        const btnEliminar = e.target.closest('.btn-eliminar-variante');
        const btnEditar   = e.target.closest('.btn-editar-variante');
        const btnDuplicar = e.target.closest('.btn-duplicar-variante');

        // ELIMINAR
        if (btnEliminar) {
            const id     = btnEliminar.dataset.id;
            const tr     = btnEliminar.closest('tr');
            const nombre = tr.querySelector('td:nth-child(3)')?.innerText?.trim() ?? 'esta variante';

            const result = await confirmDanger(
                '¿Eliminar variante?',
                `Estás a punto de eliminar <strong class="text-slate-800">${nombre}</strong>. Esta acción no se puede deshacer.`
            );
            if (!result.isConfirmed) return;

            try {
                const res  = await fetch(`${ROUTES.getVariante}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    tr.remove();
                    Toast.fire({ icon: 'success', title: data.message });
                    if (!tablaBody.querySelector('tr[data-id]')) location.reload();
                } else {
                    alertErr(data.message || 'No se pudo eliminar.');
                }
            } catch {
                alertErr('Error de red. Intenta de nuevo.');
            }
        }

        // EDITAR o DUPLICAR
        if (btnEditar || btnDuplicar) {
            const id        = (btnEditar ?? btnDuplicar).dataset.id;
            const isDuplicar = !!btnDuplicar;

            try {
                const res  = await fetch(`${ROUTES.getVariante}/${id}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();

                if (data.success) {
                    clearModal();
                    fillModal(data.variante, isDuplicar);
                    openModal();
                } else {
                    alertErr('No se pudieron cargar los datos de la variante.');
                }
            } catch {
                alertErr('Error de red al cargar la variante.');
            }
        }
    });
});
