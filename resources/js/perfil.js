const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function applyTheme(theme) {
    const html = document.documentElement;
    if (theme === 'dark') {
        html.classList.add('dark');
    } else if (theme === 'light') {
        html.classList.remove('dark');
    } else {
        // 'system' — seguir la preferencia del SO
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
    }
}

function setTheme(theme) {
    localStorage.setItem('ax_theme', theme);
    applyTheme(theme);
    // Actualizar botones
    document.querySelectorAll('.theme-option').forEach(btn => {
        btn.classList.toggle('selected', btn.dataset.theme === theme);
    });
    Toast.fire({ icon: 'success', title: 'Tema actualizado' });
}

// Exponer para uso global 
window.applyTheme = applyTheme;
window.setTheme = setTheme;


function switchTab(tabId) {
    document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.profile-tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById(`tab-btn-${tabId}`).classList.add('active');
    document.getElementById(`tab-${tabId}`).classList.add('active');
}

// VALIDACION TELEFONO 
const TEL_REGEX = /^[267]\d{3}-\d{4}$/;

function formatTelefono(input) {
    let val = input.value.replace(/\D/g, '');
    if (val.length > 4) {
        val = val.slice(0, 4) + '-' + val.slice(4, 8);
    }
    input.value = val;
    validateTelefono(input);
}

function validateTelefono(input) {
    const val = input.value.trim();
    const msgEl = document.getElementById('telefono-msg');
    const iconEl = document.getElementById('telefono-icon');

    if (!val) {
        // Vacio (campo opcional)
        input.classList.remove('error', 'success', 'text-red-500', 'text-emerald-500', 'bg-red-50', 'bg-emerald-50', 'focus:border-red-500', 'focus:ring-red-500/20', 'focus:border-emerald-500', 'focus:ring-emerald-500/20', 'focus:border-blue-500', 'focus:ring-blue-500/20');
        input.classList.add('text-gray-900', 'bg-white');
        if (msgEl) msgEl.textContent = 'Formato: XXXX-XXXX (ej: 7890-1234)';
        if (iconEl) iconEl.innerHTML = '';
        return true;
    }

    if (TEL_REGEX.test(val)) {
        input.classList.remove('error', 'text-red-500', 'text-gray-900', 'bg-red-50', 'bg-white', 'focus:border-blue-500', 'focus:ring-blue-500/20', 'focus:border-red-500', 'focus:ring-red-500/20');
        input.classList.add('success', 'text-emerald-500', 'bg-emerald-50', 'focus:border-emerald-500', 'focus:ring-emerald-500/20');
        if (msgEl) { msgEl.textContent = 'Número válido ✓'; msgEl.className = 'text-xs text-emerald-600 mt-1'; }
        if (iconEl) iconEl.innerHTML = '<i class="fas fa-check text-emerald-500"></i>';
        return true;
    } else {
        input.classList.remove('success', 'text-emerald-500', 'text-gray-900', 'bg-emerald-50', 'bg-white', 'focus:border-blue-500', 'focus:ring-blue-500/20', 'focus:border-emerald-500', 'focus:ring-emerald-500/20');
        input.classList.add('error', 'text-red-500', 'bg-red-50', 'focus:border-red-500', 'focus:ring-red-500/20');
        let errorMsg = 'Formato incorrecto. Debe iniciar con 2, 6 o 7 y tener la forma XXXX-XXXX';
        if (/^[267]/.test(val.replace(/\D/g, ''))) {
            errorMsg = 'El formato debe ser XXXX-XXXX (8 dígitos).';
        }
        if (msgEl) { msgEl.textContent = errorMsg; msgEl.className = 'text-xs text-red-500 mt-1'; }
        if (iconEl) iconEl.innerHTML = '<i class="fas fa-triangle-exclamation text-red-500"></i>';
        return false;
    }
}

// FORTALEZA CONTRASEÑA

function checkPasswordStrength(input, fillId, labelId) {
    const val = input.value;
    const fill = document.getElementById(fillId);
    const label = document.getElementById(labelId);
    if (!fill || !label) return;

    if (!val) {
        fill.className = 'strength-fill';
        label.textContent = '';
        return;
    }

    let score = 0;
    if (val.length >= 8) score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    fill.className = 'strength-fill';
    if (score <= 1) { fill.classList.add('weak'); label.textContent = 'Débil'; label.className = 'text-xs font-bold text-red-500'; }
    else if (score === 2) { fill.classList.add('fair'); label.textContent = 'Regular'; label.className = 'text-xs font-bold text-amber-500'; }
    else if (score === 3 || score === 4) { fill.classList.add('good'); label.textContent = 'Buena'; label.className = 'text-xs font-bold text-blue-500'; }
    else { fill.classList.add('strong'); label.textContent = 'Muy fuerte'; label.className = 'text-xs font-bold text-emerald-500'; }
}

function togglePwdVisibility(inputId, iconId) {
    const inp = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!inp || !icon) return;
    const isPass = inp.type === 'password';
    inp.type = isPass ? 'text' : 'password';
    icon.className = isPass ? 'fas fa-eye-slash' : 'fas fa-eye';
}

// GUARDAR INFORMACION PERSONAL

async function savePersonalInfo(event) {
    event.preventDefault();

    const telInput = document.getElementById('perfil_telefono');
    if (telInput && !validateTelefono(telInput)) {
        let val = telInput.value.replace(/\D/g, '');
        let hint = /^[267]/.test(val) ? 'Complete los 8 dígitos.' : 'El primer dígito debe ser <strong>2, 6 o 7</strong>.';
        Swal.fire({ customClass: { popup: 'swal-axstore' },
            icon: 'warning',
            title: 'Teléfono inválido',
            html: `<p style="color:#475569;font-size:0.92rem;">El número de teléfono debe tener el formato de El Salvador:<br><strong>XXXX-XXXX</strong> (ej: 7890-1234).<br>${hint}</p>`,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#2563eb',
        });
        return;
    }

    const form = document.getElementById('form-personal');
    const data = new FormData(form);
    data.append('_method', 'PUT');

    const btn = document.getElementById('btn-save-personal');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Guardando...';

    try {
        const res = await fetch('/perfil', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: data });
        const json = await res.json();

        if (json.success) {
            Toast.fire({ icon: 'success', title: json.message });
            // Actualizar avatar/nombre 
            const nameEl = document.getElementById('perfil-header-name');
            if (nameEl) nameEl.textContent = document.getElementById('perfil_nombre').value;
        } else {
            const errors = json.errors ? Object.values(json.errors).flat().join('<br>') : json.message;
            Swal.fire({ customClass: { popup: 'swal-axstore' }, icon: 'error', title: 'Error al guardar', html: `<p style="font-size:0.9rem;color:#475569;">${errors}</p>`, confirmButtonColor: '#ef4444' });
        }
    } catch (e) {
        Toast.fire({ icon: 'error', title: 'Error de conexión al guardar el perfil.' });
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save mr-1.5"></i> Guardar Cambios';
    }
}

// CAMBIAR CONTRASEÑA

async function savePassword(event) {
    event.preventDefault();

    const newPwd = document.getElementById('new_password').value;
    const confPwd = document.getElementById('confirm_password').value;

    if (newPwd !== confPwd) {
        Swal.fire({ customClass: { popup: 'swal-axstore' }, icon: 'warning', title: 'Las contraseñas no coinciden', text: 'La nueva contraseña y su confirmación deben ser iguales.', confirmButtonColor: '#f59e0b' });
        return;
    }

    const btn = document.getElementById('btn-save-password');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Cambiando...';

    try {
        const form = document.getElementById('form-password');
        const data = new FormData(form);
        data.append('_method', 'PUT');

        const res = await fetch('/perfil/password', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: data });
        const json = await res.json();

        if (json.success) {
            Toast.fire({ icon: 'success', title: json.message });
            form.reset();
            document.getElementById('pwd-strength-fill').className = 'strength-fill';
            document.getElementById('pwd-strength-label').textContent = '';
        } else {
            const errors = json.errors ? Object.values(json.errors).flat().join('<br>') : json.message;
            Swal.fire({ customClass: { popup: 'swal-axstore' }, icon: 'error', title: 'Error', html: `<p style="font-size:0.9rem;color:#475569;">${errors}</p>`, confirmButtonColor: '#ef4444' });
        }
    } catch (e) {
        Toast.fire({ icon: 'error', title: 'Error de conexión.' });
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-lock mr-1.5"></i> Cambiar Contraseña';
    }
}

// TOAST NOTIFICATION

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});


// INICIALIZACION

document.addEventListener('DOMContentLoaded', () => {
    // Aplicar tema guardado
    const saved = localStorage.getItem('ax_theme') ?? 'system';
    applyTheme(saved);

    // Marcar boton de tema activo
    document.querySelectorAll('.theme-option').forEach(btn => {
        btn.classList.toggle('selected', btn.dataset.theme === saved);
    });

    // Auto-formatear telefono
    const telInput = document.getElementById('perfil_telefono');
    if (telInput) {
        telInput.addEventListener('input', () => formatTelefono(telInput));
    }
});

// ─── Exports globales ───
window.switchTab = switchTab;
window.setTheme = setTheme;
window.formatTelefono = formatTelefono;
window.validateTelefono = validateTelefono;
window.checkPasswordStrength = checkPasswordStrength;
window.togglePwdVisibility = togglePwdVisibility;
window.savePersonalInfo = savePersonalInfo;
window.savePassword = savePassword;
