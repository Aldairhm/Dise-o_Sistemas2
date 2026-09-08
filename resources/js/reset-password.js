function toggleVisibility(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(eyeId);

    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

window.toggleVisibility = toggleVisibility;

const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('password_confirmation');
const strengthLabel = document.getElementById('strengthLabel');
const bars = [
    document.getElementById('bar1'),
    document.getElementById('bar2'),
    document.getElementById('bar3'),
    document.getElementById('bar4')
];

const ruleLength = document.getElementById('rule-length');
const ruleCase = document.getElementById('rule-case');
const ruleNumber = document.getElementById('rule-number');
const ruleSymbol = document.getElementById('rule-symbol');

const matchFeedback = document.getElementById('matchFeedback');
const matchIcon = document.getElementById('matchIcon');
const matchText = document.getElementById('matchText');

function updateRule(element, isValid) {
    const icon = element.querySelector('i');
    if (isValid) {
        element.classList.add('valid');
        icon.className = 'fas fa-circle-check';
    } else {
        element.classList.remove('valid');
        icon.className = 'fas fa-circle-xmark';
    }
}

const strengthMeterBox = document.getElementById('strengthMeterBox');

function evaluatePassword() {
    const val = passwordInput.value;

    // Ocultar si está vacío
    if (val.length === 0) {
        strengthMeterBox.style.display = 'none';
        checkMatch();
        return;
    }

    // Mostrar el recuadro tan pronto como haya texto
    strengthMeterBox.style.display = 'block';

    const hasLength = val.length >= 8;
    const hasCase = /[a-z]/.test(val) && /[A-Z]/.test(val);
    const hasNumber = /[0-9]/.test(val);
    const hasSymbol = /[^A-Za-z0-9]/.test(val);

    updateRule(ruleLength, hasLength);
    updateRule(ruleCase, hasCase);
    updateRule(ruleNumber, hasNumber);
    updateRule(ruleSymbol, hasSymbol);

    let score = 0;
    if (hasLength) score++;
    if (hasCase) score++;
    if (hasNumber) score++;
    if (hasSymbol) score++;

    // Resetear barras
    bars.forEach(b => {
        b.style.backgroundColor = 'rgba(255, 255, 255, 0.08)';
    });

    if (score === 1) {
        strengthLabel.textContent = 'Muy débil';
        strengthLabel.style.color = '#ef4444';
        bars[0].style.backgroundColor = '#ef4444';
    } else if (score === 2) {
        strengthLabel.textContent = 'Débil';
        strengthLabel.style.color = '#f97316';
        bars[0].style.backgroundColor = '#f97316';
        bars[1].style.backgroundColor = '#f97316';
    } else if (score === 3) {
        strengthLabel.textContent = 'Buena';
        strengthLabel.style.color = '#eab308';
        bars[0].style.backgroundColor = '#eab308';
        bars[1].style.backgroundColor = '#eab308';
        bars[2].style.backgroundColor = '#eab308';
    } else if (score === 4) {
        strengthLabel.textContent = 'Excelente y segura';
        strengthLabel.style.color = '#10b981';
        bars.forEach(b => b.style.backgroundColor = '#10b981');
    }

    checkMatch();
}

function checkMatch() {
    const pass = passwordInput.value;
    const conf = confirmInput.value;

    if (!conf) {
        matchFeedback.className = 'match-feedback';
        matchFeedback.style.display = 'none';
        return;
    }

    matchFeedback.style.display = 'flex';
    if (pass === conf) {
        matchFeedback.className = 'match-feedback match';
        matchIcon.className = 'fas fa-circle-check';
        matchText.textContent = 'Las contraseñas coinciden perfectamente';
    } else {
        matchFeedback.className = 'match-feedback no-match';
        matchIcon.className = 'fas fa-circle-xmark';
        matchText.textContent = 'Las contraseñas no coinciden todavía';
    }
}

passwordInput.addEventListener('input', evaluatePassword);
confirmInput.addEventListener('input', checkMatch);