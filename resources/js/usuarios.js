const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let searchTimeout = null;
let currentView = localStorage.getItem('usr_view') ?? 'table';

// ─── TOGGLE VISTA TABLA / TARJETAS ───
function setView(view) {
    currentView = view;
    localStorage.setItem('usr_view', view);

    const tw = document.getElementById('usersTableWrapper');
    const cw = document.getElementById('usersCardsWrapper');
    const bt = document.getElementById('btnViewTable');
    const bc = document.getElementById('btnViewCards');

    if (view === 'table') {
        tw.classList.remove('hidden'); cw.classList.add('hidden');
        bt.classList.add('usr-view-active'); bc.classList.remove('usr-view-active');
    } else {
        tw.classList.add('hidden'); cw.classList.remove('hidden');
        bc.classList.add('usr-view-active'); bt.classList.remove('usr-view-active');
    }
}

// Inicializar vista guardada al cargar
document.addEventListener('DOMContentLoaded', () => setView(currentView));

// ─── GESTION DEL MODAL ───
function openCreateUserModal() {
    const form = document.getElementById('userForm');
    form.reset();
    document.getElementById('userId').value = '';
    
    // Limpiar validacion visual del telefono
    const inputTelefono = document.getElementById('input_telefono');
    if(inputTelefono) {
        formatTelefonoInput(inputTelefono);
    }

    document.getElementById('_method').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Registrar Nuevo Usuario';
    document.getElementById('btnSubmitText').textContent = 'REGISTRAR USUARIO';
    document.getElementById('modalHeaderIcon').className = 'fas fa-circle-check';

    // Contraseña obligatoria al crear
    document.getElementById('input_password').required = true;
    document.getElementById('input_password_confirmation').required = true;
    document.getElementById('pwdRequiredIndicator').style.display = 'inline';
    document.getElementById('pwdConfRequiredIndicator').style.display = 'inline';
    document.getElementById('pwdHelpText').textContent = 'Mínimo 8 caracteres (Campo obligatorio)';

    hideModalAlert();
    updatePreviewCard();

    document.getElementById('userModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('input_nombre_real').focus(), 100);
}

function openEditUserModal(user) {
    document.getElementById('userId').value = user.id;
    document.getElementById('_method').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Modificar Usuario #' + user.id;
    document.getElementById('btnSubmitText').textContent = 'GUARDAR CAMBIOS';
    document.getElementById('modalHeaderIcon').className = 'fas fa-pen-to-square';

    document.getElementById('input_nombre_real').value = user.nombre_real || '';
    document.getElementById('input_telefono').value = user.telefono || '';
    document.getElementById('input_username').value = user.username || '';
    document.getElementById('input_rol').value = user.rol || 'vendedor';
    document.getElementById('input_estado').value = user.estado !== undefined ? user.estado : '1';
    
    // Validar visualmente el telefono
    formatTelefonoInput(document.getElementById('input_telefono'));

    // Contraseña opcional al editar
    const pwdInput = document.getElementById('input_password');
    const pwdConfInput = document.getElementById('input_password_confirmation');
    pwdInput.value = '';
    pwdConfInput.value = '';
    pwdInput.required = false;
    pwdConfInput.required = false;
    document.getElementById('pwdRequiredIndicator').style.display = 'none';
    document.getElementById('pwdConfRequiredIndicator').style.display = 'none';
    document.getElementById('pwdHelpText').textContent = 'Dejar en blanco para conservar la contraseña actual';

    hideModalAlert();
    updatePreviewCard();

    document.getElementById('userModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Previsualizacion
function updatePreviewCard() {
    const name = document.getElementById('input_nombre_real').value.trim() || 'Nuevo Usuario';
    const telefono = document.getElementById('input_telefono').value.trim();
    const username = document.getElementById('input_username').value.trim() || 'usuario@axstore.com';
    const role = document.getElementById('input_rol').value;
    const status = document.getElementById('input_estado').value;

    document.getElementById('previewName').textContent = name;
    document.getElementById('previewUsername').textContent = username;
    document.getElementById('previewInitial').textContent = name.charAt(0).toUpperCase() || 'U';

    // Rol
    const roleBadge = document.getElementById('previewRoleBadge');
    const roleText = document.getElementById('previewRoleText');
    if (role === 'admin') {
        roleBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-100 text-purple-700 border border-purple-200';
        roleText.textContent = 'Administrador';
        document.getElementById('previewAvatar').className = 'w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center text-4xl font-black shadow-lg shadow-purple-500/25 mb-4 transition-transform hover:scale-105';
    } else {
        roleBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 border border-blue-200';
        roleText.textContent = 'Vendedor';
        document.getElementById('previewAvatar').className = 'w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-500 text-white flex items-center justify-center text-4xl font-black shadow-lg shadow-blue-500/25 mb-4 transition-transform hover:scale-105';
    }

    // Telefono
    const phoneBadge = document.getElementById('previewPhoneBadge');
    const phoneText = document.getElementById('previewPhoneText');
    if (telefono) {
        phoneBadge.style.display = 'inline-flex';
        phoneText.textContent = telefono;
    } else {
        phoneBadge.style.display = 'none';
        phoneText.textContent = '';
    }

    // Estado
    const statusBadge = document.getElementById('previewStatusBadge');
    const statusText = document.getElementById('previewStatusText');
    if (status == 1) {
        statusBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200';
        statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span><span>Habilitado</span>';
    } else {
        statusBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200';
        statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span><span>Deshabilitado</span>';
    }
}

// Validacion de Telefono El Salvador
function formatTelefonoInput(input) {
    let val = input.value.replace(/\D/g, '');
    if (val.length > 4) {
        val = val.slice(0, 4) + '-' + val.slice(4, 8);
    }
    input.value = val;
    
    const msgEl = document.getElementById('modal-telefono-msg');
    const iconEl = document.getElementById('modal-telefono-icon');
    
    if (!val) {
        input.classList.remove('border-rose-500', 'border-emerald-500', 'text-rose-500', 'text-emerald-500', 'bg-rose-50', 'bg-emerald-50', 'focus:border-rose-600', 'focus:ring-rose-600/20', 'focus:border-emerald-600', 'focus:ring-emerald-600/20');
        input.classList.add('text-gray-900', 'bg-white', 'focus:border-blue-600', 'focus:ring-blue-600/20');
        if (msgEl) msgEl.innerHTML = 'Formato: XXXX-XXXX (ej: 7890-1234)';
        if (iconEl) iconEl.innerHTML = '';
        return true;
    }
    
    if (/^[267]\d{3}-\d{4}$/.test(val)) {
        input.classList.remove('border-rose-500', 'text-rose-500', 'text-gray-900', 'bg-rose-50', 'bg-white', 'focus:border-blue-600', 'focus:ring-blue-600/20', 'focus:border-rose-600', 'focus:ring-rose-600/20');
        input.classList.add('border-emerald-500', 'text-emerald-500', 'bg-emerald-50', 'focus:border-emerald-600', 'focus:ring-emerald-600/20');
        if (msgEl) msgEl.innerHTML = '<span class="text-emerald-500">Número válido ✓</span>';
        if (iconEl) iconEl.innerHTML = '<i class="fas fa-check text-emerald-500"></i>';
        return true;
    } else {
        input.classList.remove('border-emerald-500', 'text-emerald-500', 'text-gray-900', 'bg-emerald-50', 'bg-white', 'focus:border-blue-600', 'focus:ring-blue-600/20', 'focus:border-emerald-600', 'focus:ring-emerald-600/20');
        input.classList.add('border-rose-500', 'text-rose-500', 'bg-rose-50', 'focus:border-rose-600', 'focus:ring-rose-600/20');
        let errorMsg = 'Formato incorrecto. Inicia con 2, 6 o 7.';
        if (/^[267]/.test(val.replace(/\D/g, ''))) {
            errorMsg = 'Debe completar los 8 números.';
        }
        if (msgEl) msgEl.innerHTML = `<span class="text-rose-500">${errorMsg}</span>`;
        if (iconEl) iconEl.innerHTML = '<i class="fas fa-triangle-exclamation text-rose-500"></i>';
        return false;
    }
}

function toggleModalPassword(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const eye = document.getElementById(eyeId);
    if (input.type === 'password') {
        input.type = 'text';
        eye.className = 'fas fa-eye-slash text-blue-600';
    } else {
        input.type = 'password';
        eye.className = 'fas fa-eye text-gray-400';
    }
}

function showModalAlert(msg) {
    const alert = document.getElementById('modalAlertError');
    document.getElementById('modalAlertErrorText').innerHTML = msg;
    alert.classList.remove('hidden');
}

function hideModalAlert() {
    document.getElementById('modalAlertError').classList.add('hidden');
}

// ─── ENVIO DEL FORMULARIO ───
async function handleUserSubmit(event) {
    event.preventDefault();
    hideModalAlert();

    const userId = document.getElementById('userId').value;
    const isEdit = Boolean(userId);
    const url = isEdit ? `/usuarios/${userId}` : '/usuarios';
    const method = isEdit ? 'PUT' : 'POST';

    const submitBtn = document.getElementById('btnSubmitModal');
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-75');

    const payload = {
        _token: csrfToken,
        nombre_real: document.getElementById('input_nombre_real').value,
        telefono: document.getElementById('input_telefono').value,
        username: document.getElementById('input_username').value,
        rol: document.getElementById('input_rol').value,
        estado: document.getElementById('input_estado').value,
    };
    
    if (payload.telefono && !/^[267]\d{3}-\d{4}$/.test(payload.telefono)) {
        showModalAlert('El teléfono ingresado no tiene un formato válido para El Salvador (XXXX-XXXX).');
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-75');
        return;
    }

    const pwd = document.getElementById('input_password').value;
    const pwdConf = document.getElementById('input_password_confirmation').value;
    if (pwd || !isEdit) {
        payload.password = pwd;
        payload.password_confirmation = pwdConf;
    }

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (!response.ok) {
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat().join('<br>');
                showModalAlert(errorMessages);
            } else {
                showModalAlert(data.message || 'Error al procesar la solicitud.');
            }
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75');
            return;
        }

        closeUserModal();
        Swal.fire({
            icon: 'success',
            title: '¡Operación Exitosa!',
            text: data.message,
            timer: 1800,
            showConfirmButton: false,
            customClass: { popup: 'swal-axstore' }
        });

        // Recargar tabla
        applyFilters();

    } catch (err) {
        console.error(err);
        showModalAlert('Ocurrió un error inesperado al conectar con el servidor.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-75');
    }
}

// ─── BUSQUEDA Y FILTRADO EN TIEMPO REAL ───
function onSearchInput() {
    const val = document.getElementById('searchInput').value.trim();
    const clearBtn = document.getElementById('clearSearchBtn');
    if (val) {
        clearBtn.classList.remove('hidden');
    } else {
        clearBtn.classList.add('hidden');
    }

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 350);
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearchBtn').classList.add('hidden');
    applyFilters();
}

function resetAllFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearchBtn').classList.add('hidden');
    document.getElementById('roleFilter').value = 'todos';
    document.getElementById('statusFilter').value = 'todos';
    applyFilters();
}

async function applyFilters() {
    const search = document.getElementById('searchInput').value.trim();
    const rol = document.getElementById('roleFilter').value;
    const estado = document.getElementById('statusFilter').value;

    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (rol !== 'todos') params.append('rol', rol);
    if (estado !== 'todos') params.append('estado', estado);

    const loader = document.getElementById('tableLoading');
    loader.classList.remove('hidden');

    try {
        const response = await fetch(`/usuarios?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();
        if (data.success) {
            document.getElementById('usersTableWrapper').innerHTML = data.html;
            document.getElementById('usersCardsGrid').innerHTML = data.cards ?? '';

            // Actualizar KPIs
            if (data.stats) {
                document.getElementById('kpiTotal').textContent = data.stats.total;
                document.getElementById('kpiAdmins').textContent = data.stats.admins;
                document.getElementById('kpiVendedores').textContent = data.stats.vendedores;
                document.getElementById('kpiActivos').textContent = data.stats.activos;
            }
        }
    } catch (err) {
        console.error('Error al filtrar usuarios:', err);
    } finally {
        loader.classList.add('hidden');
    }
}

// ─── ALTERNAR ESTADO ───
async function toggleUserStatus(userId, btnElement) {
    const currentState = parseInt(btnElement.dataset.current);
    const estadoActivo = (currentState === 0);
    const nombre = btnElement.dataset.name || 'el usuario';

    const actionText = estadoActivo ? 'habilitar' : 'deshabilitar';
    const actionIcon = estadoActivo ? 'fa-check' : 'fa-ban';
    const btnColor   = estadoActivo ? '#22c55e' : '#f97316';
    const htmlText   = estadoActivo 
        ? `El usuario <strong class="text-slate-800">${nombre}</strong> volverá a tener acceso al sistema.` 
        : `El usuario <strong class="text-slate-800">${nombre}</strong> perderá temporalmente el acceso al sistema.`;

    const result = await Swal.fire({ customClass: { popup: 'swal-axstore' },
        title: `¿${actionText.charAt(0).toUpperCase() + actionText.slice(1)} usuario?`,
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
        const response = await fetch(`/usuarios/${userId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (!response.ok) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'No se pudo cambiar el estado.',
                customClass: { popup: 'swal-axstore' }
            });
            return;
        }

        // Notificacion flotante rapida
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
        Toast.fire({
            icon: 'success',
            title: data.message
        });

        applyFilters();

    } catch (err) {
        console.error(err);
    }
}

// ─── ELIMINAR USUARIO (Soft Delete) ───
function confirmDeleteUser(userId, userName) {
    Swal.fire({
        title: '¿Eliminar Usuario?',
        html: `¿Estás seguro de que deseas eliminar al usuario <b>${userName}</b> de la lista? El registro se conservará en la base de datos.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#0f172a',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'swal-axstore'
        }
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`/usuarios/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo eliminar el usuario.',
                        customClass: { popup: 'swal-axstore' }
                    });
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: '¡Removido!',
                    text: data.message,
                    timer: 1800,
                    showConfirmButton: false,
                    customClass: { popup: 'swal-axstore' }
                });

                applyFilters();

            } catch (err) {
                console.error(err);
            }
        }
    });
}

// ─── PAGINACION ───
document.getElementById('usersTableWrapper').addEventListener('click', function (e) {
    const link = e.target.closest('.pagination-link');
    if (!link) return;
    e.preventDefault();

    const url = new URL(link.href);
    const page = url.searchParams.get('page');
    if (!page) return;

    const search = document.getElementById('searchInput').value.trim();
    const rol = document.getElementById('roleFilter').value;
    const estado = document.getElementById('statusFilter').value;

    const params = new URLSearchParams();
    params.append('page', page);
    if (search) params.append('search', search);
    if (rol !== 'todos') params.append('rol', rol);
    if (estado !== 'todos') params.append('estado', estado);

    const loader = document.getElementById('tableLoading');
    loader.classList.remove('hidden');

    fetch(`/usuarios?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('usersTableWrapper').innerHTML = data.html;
                document.getElementById('usersCardsGrid').innerHTML = data.cards ?? '';
                if (data.stats) {
                    document.getElementById('kpiTotal').textContent = data.stats.total;
                    document.getElementById('kpiAdmins').textContent = data.stats.admins;
                    document.getElementById('kpiVendedores').textContent = data.stats.vendedores;
                    document.getElementById('kpiActivos').textContent = data.stats.activos;
                }
            }
        })
        .catch(err => console.error('Error al paginar:', err))
        .finally(() => loader.classList.add('hidden'));
});


window.openCreateUserModal = openCreateUserModal;
window.openEditUserModal = openEditUserModal;
window.closeUserModal = closeUserModal;
window.updatePreviewCard = updatePreviewCard;
window.toggleModalPassword = toggleModalPassword;
window.handleUserSubmit = handleUserSubmit;
window.onSearchInput = onSearchInput;
window.clearSearch = clearSearch;
window.resetAllFilters = resetAllFilters;
window.applyFilters = applyFilters;
window.toggleUserStatus = toggleUserStatus;
window.confirmDeleteUser = confirmDeleteUser;
window.setView = setView;
window.formatTelefonoInput = formatTelefonoInput;