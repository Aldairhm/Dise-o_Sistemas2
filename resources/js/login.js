function togglePassVisibility() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (!passwordInput || !eyeIcon) return;

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}

function prepareLoginField() {
    const form = document.getElementById('loginForm');
    const visibleField = document.getElementById('login_field');
    const usernameField = document.getElementById('username');

    if (!form || !visibleField || !usernameField) return;

    visibleField.addEventListener('focus', () => {
        visibleField.removeAttribute('readonly');
    }, { once: true });

    const syncUsername = () => {
        usernameField.value = visibleField.value;
    };

    visibleField.addEventListener('input', syncUsername);
    form.addEventListener('submit', syncUsername);
}

document.addEventListener('DOMContentLoaded', prepareLoginField);
window.togglePassVisibility = togglePassVisibility;
