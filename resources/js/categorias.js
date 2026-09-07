        const ROUTES = {
            index: window.APP_ROUTES.index,
            store: window.APP_ROUTES.store,
            update: id => `${window.APP_ROUTES.update}/${id}`,
            toggleStatus: id => `${window.APP_ROUTES.toggleStatus}/${id}/toggle-status`,
            reorder: window.APP_ROUTES.reorder,
            export: window.APP_ROUTES.export,
        };

        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let currentView   = localStorage.getItem('cat_view')   || 'table';
        let currentEstado = localStorage.getItem('cat_estado') || 'todas';
        let sortableTable = null;
        let sortableCards = null;

        function setView(view) {
            currentView = view;
            localStorage.setItem('cat_view', view);

            const tw = document.getElementById('tableWrapper');
            const cw = document.getElementById('cardsWrapper');
            const bt = document.getElementById('btnViewTable');
            const bc = document.getElementById('btnViewCards');

            if (view === 'table') {
                tw.classList.remove('hidden'); cw.classList.add('hidden');
                bt.classList.add('active');    bc.classList.remove('active');
            } else {
                tw.classList.add('hidden');    cw.classList.remove('hidden');
                bc.classList.add('active');    bt.classList.remove('active');
            }
            filterItems();
        }

        function setEstado(estado) {
            currentEstado = estado;
            localStorage.setItem('cat_estado', estado);

            const tabs = { todas: 'tabTodas', activas: 'tabActivas', inactivos: 'tabInactivos' };
            const cls  = { todas: 'active-all', activas: 'active-act', inactivos: 'active-ina' };

            Object.entries(tabs).forEach(([key, id]) => {
                const el = document.getElementById(id);
                if(el) {
                    el.classList.remove('active-all', 'active-act', 'active-ina');
                    if (key === estado) el.classList.add(cls[key]);
                }
            });
            
            // Actualizar ruta del botón exportar
            const btnExport = document.getElementById('btnExport');
            if(btnExport && ROUTES.export) {
                btnExport.href = `${ROUTES.export}?estado=${estado}`;
            }

            refreshContent();
        }

        function initSortable() {
            const tbody = document.getElementById('categoriasBody');
            const grid  = document.getElementById('cardsWrapper');
            
            const options = {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                onEnd: function () {
                    saveOrder();
                }
            };

            if (tbody) sortableTable = new Sortable(tbody, options);
            if (grid)  sortableCards = new Sortable(grid, options);
        }

        async function saveOrder() {
            // Obtenemos el nuevo orden desde la vista actual
            const container = currentView === 'table' ? '#categoriasBody .categoria-row' : '#cardsWrapper .categoria-card';
            const order = Array.from(document.querySelectorAll(container)).map(el => el.dataset.id);
            
            if (order.length === 0) return;

            try {
                const res = await fetch(ROUTES.reorder, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({ order })
                });
                
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Error guardando el orden');
                
                // Mostrar un pequeño toast (sin interrumpir)
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
                Toast.fire({ icon: 'success', title: 'Orden guardado' });

            } catch (err) {
                console.error(err);
                Swal.fire({ customClass: { popup: 'swal-axstore' }, icon: 'error', title: 'Error', text: 'No se pudo guardar el orden.' });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            setView(currentView);
            setEstado(currentEstado);
            initSortable();
        });

        function openCreateModal() {
            const icon = document.getElementById('modalHeaderIcon');
            if (icon) icon.className = 'fas fa-plus-circle';
            const title = document.getElementById('modalTitle');
            if (title) title.textContent = 'Nueva Categoría';
            const sub = document.getElementById('modalSubtitle');
            if (sub) sub.textContent = 'Completa los campos para registrar una categoría';
            const btn = document.getElementById('btnSubmitText');
            if (btn) btn.textContent = 'GUARDAR CATEGORÍA';

            document.getElementById('modalMethod').value      = 'POST';
            document.getElementById('categoriaId').value      = '';
            document.getElementById('inputNombre').value      = '';
            document.getElementById('inputDescripcion').value = '';

            const pName = document.getElementById('previewName');
            if (pName) pName.textContent = 'Nueva Categoría';
            const pDesc = document.getElementById('previewDesc');
            if (pDesc) pDesc.textContent = '---';

            clearErrors();
            selectColor('#3b82f6', '#eff6ff');
            selectIcon('fa-tag');
            showModal();
        }

        function openEditModal(id, nombre, descripcion, color, icono) {
            const icon = document.getElementById('modalHeaderIcon');
            if (icon) icon.className = 'fas fa-edit';
            const title = document.getElementById('modalTitle');
            if (title) title.textContent = 'Editar Categoría';
            const sub = document.getElementById('modalSubtitle');
            if (sub) sub.textContent = 'Modifica los datos de la categoría';
            const btn = document.getElementById('btnSubmitText');
            if (btn) btn.textContent = 'GUARDAR CAMBIOS';

            document.getElementById('modalMethod').value      = 'PUT';
            document.getElementById('categoriaId').value      = id;
            document.getElementById('inputNombre').value      = nombre;
            document.getElementById('inputDescripcion').value = descripcion;

            const pName = document.getElementById('previewName');
            if (pName) pName.textContent = nombre || 'Nueva Categoría';
            const pDesc = document.getElementById('previewDesc');
            if (pDesc) pDesc.textContent = descripcion || '---';

            clearErrors();
            
            const foundColor = (window.COLOR_PALETTE ?? []).find(c => c.hex === color);
            selectColor(color, foundColor?.light ?? '#eff6ff');
            
            const foundIcon = (window.ICON_PALETTE ?? []).includes(icono) ? icono : 'fa-tag';
            selectIcon(foundIcon);
            
            showModal();
        }

        function showModal() {
            const modal = document.getElementById('categoriaModal');
            const bd    = document.getElementById('modalBackdrop');
            const panel = document.getElementById('modalPanel');
            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                bd.classList.remove('opacity-0'); bd.classList.add('opacity-100');
                panel.classList.remove('scale-95','opacity-0'); panel.classList.add('scale-100','opacity-100');
            });
            setTimeout(() => document.getElementById('inputNombre').focus(), 250);
        }

        function closeModal() {
            const modal = document.getElementById('categoriaModal');
            const bd    = document.getElementById('modalBackdrop');
            const panel = document.getElementById('modalPanel');
            bd.classList.remove('opacity-100'); bd.classList.add('opacity-0');
            panel.classList.remove('scale-100','opacity-100'); panel.classList.add('scale-95','opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 250);
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        document.getElementById('categoriaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();

            const id     = document.getElementById('categoriaId').value;
            const method = document.getElementById('modalMethod').value;
            const url    = id ? ROUTES.update(id) : ROUTES.store;
            const body   = new FormData();

            body.append('_token',      CSRF);
            body.append('nombre',      document.getElementById('inputNombre').value.trim());
            body.append('descripcion', document.getElementById('inputDescripcion').value.trim());
            body.append('color',       document.getElementById('inputColor').value);
            body.append('icono',       document.getElementById('inputIcono').value);
            if (method === 'PUT') body.append('_method', 'PUT');

            const btn  = document.getElementById('btnSubmitModal');
            const span = document.getElementById('btnSubmitText');
            btn.disabled = true; span.textContent = 'Guardando...';

            try {
                const res  = await fetch(url, { method: 'POST', body });
                const data = await res.json();

                if (res.ok && data.success) {
                    closeModal();
                    await refreshContent();
                    Swal.fire({ customClass: { popup: 'swal-axstore' }, icon:'success', title:'¡Listo!', text:data.message, timer:2000, showConfirmButton:false, timerProgressBar:true });
                } else if (res.status === 422) {
                    showErrors(data.errors ?? {});
                } else {
                    Swal.fire({ customClass: { popup: 'swal-axstore' }, icon:'error', title:'Error', text:data.message ?? 'Error inesperado.' });
                }
            } catch {
                Swal.fire({ customClass: { popup: 'swal-axstore' }, icon:'error', title:'Error de red', text:'No se pudo conectar.' });
            } finally {
                btn.disabled = false;
                span.textContent = id ? 'GUARDAR CAMBIOS' : 'GUARDAR CATEGORÍA';
            }
        });

        async function toggleStatus(id, nombre, estadoActivo) {
            const actionText = estadoActivo ? 'activar' : 'inactivar';
            const actionIcon = estadoActivo ? 'fa-check' : 'fa-ban';
            const btnColor   = estadoActivo ? '#22c55e' : '#f97316';
            const htmlText   = estadoActivo 
                ? `La categoría <strong class="text-slate-800">${nombre}</strong> volverá a estar visible.` 
                : `La categoría <strong class="text-slate-800">${nombre}</strong> se ocultará temporalmente.`;

            const result = await Swal.fire({ customClass: { popup: 'swal-axstore' },
                title: `¿${actionText.charAt(0).toUpperCase() + actionText.slice(1)} categoría?`,
                html: `<p class="text-slate-600 text-sm">${htmlText}</p>`,
                icon: 'warning',
                iconColor: btnColor,
                showCancelButton: true,
                confirmButtonColor: btnColor,
                cancelButtonColor: '#94a3b8',
                confirmButtonText: `<i class="fas ${actionIcon} mr-1"></i> ${actionText.charAt(0).toUpperCase() + actionText.slice(1)}`,
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
            });

            if (!result.isConfirmed) return;

            try {
                const res  = await fetch(ROUTES.toggleStatus(id), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `_token=${CSRF}&_method=PATCH`,
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    await refreshContent();
                    Swal.fire({ customClass: { popup: 'swal-axstore' }, icon:'success', title:'¡Hecho!', text:data.message, timer:2000, showConfirmButton:false, timerProgressBar:true });
                } else {
                    Swal.fire({ customClass: { popup: 'swal-axstore' }, icon:'error', title:'Error', text:data.message ?? `No se pudo ${actionText}.` });
                }
            } catch {
                Swal.fire({ customClass: { popup: 'swal-axstore' }, icon:'error', title:'Error de red', text:'No se pudo conectar.' });
            }
        }

        let searchTimeout;

        function onSearchInput() {
            const val = document.getElementById('searchInput').value;
            document.getElementById('clearSearchBtn').classList.toggle('hidden', !val);
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterItems, 200);
        }

        function filterItems() {
            const q = document.getElementById('searchInput').value.toLowerCase().trim();
            document.querySelectorAll('.categoria-row').forEach(r => {
                r.style.display = (!q || r.dataset.nombre.includes(q) || r.dataset.desc.includes(q)) ? '' : 'none';
            });
            document.querySelectorAll('.categoria-card').forEach(c => {
                const m = !q || c.dataset.nombre.includes(q) || c.dataset.desc.includes(q);
                c.style.display = m ? '' : 'none';
            });
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('clearSearchBtn').classList.add('hidden');
            document.querySelectorAll('.categoria-row, .categoria-card').forEach(el => el.style.display = '');
        }

        async function refreshContent() {
            document.getElementById('tableWrapper').classList.add('hidden');
            document.getElementById('cardsWrapper').classList.add('hidden');
            document.getElementById('tableLoading').classList.remove('hidden');

            try {
                const url  = `${ROUTES.index}?estado=${currentEstado}`;
                const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();

                document.getElementById('tableWrapper').innerHTML = data.table;
                document.getElementById('cardsWrapper').innerHTML = data.cards;

                if (data.stats) {
                    document.getElementById('kpiTotal').textContent     = data.stats.total;
                    document.getElementById('kpiActivas').textContent   = data.stats.activas;
                    document.getElementById('kpiInactivos').textContent = data.stats.inactivos;
                }
                
                // Re-inicializar drag&drop sobre los nuevos elementos del DOM
                initSortable();
            } finally {
                document.getElementById('tableLoading').classList.add('hidden');
                setView(currentView);
            }
        }

        function showErrors(errors) {
            Object.entries(errors).forEach(([field, msgs]) => {
                const el = document.getElementById('error' + field.charAt(0).toUpperCase() + field.slice(1));
                if (el) { el.textContent = msgs[0]; el.classList.remove('hidden'); }
            });
        }

        function clearErrors() {
            ['errorNombre','errorDescripcion'].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.textContent = ''; el.classList.add('hidden'); }
            });
        }
        
window.setView = setView;
window.setEstado = setEstado;
window.initSortable = initSortable;
window.saveOrder = saveOrder;
window.openCreateModal = openCreateModal;
window.openEditModal = openEditModal;
window.showModal = showModal;
window.closeModal = closeModal;
window.toggleStatus = toggleStatus;
window.onSearchInput = onSearchInput;
window.filterItems = filterItems;
window.clearSearch = clearSearch;
window.refreshContent = refreshContent;
window.showErrors = showErrors;
window.clearErrors = clearErrors;
