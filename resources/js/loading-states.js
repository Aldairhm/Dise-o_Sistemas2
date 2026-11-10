// Native navigation and custom requests share the same reversible button state.
const buttons = new Map();
const forms = new Map();

function restoreAttribute(element, name, value) {
    if (value === null) element.removeAttribute(name);
    else element.setAttribute(name, value);
}

export function setButton(button, busy, label = 'Procesando...', { disable = true } = {}) {
    if (!button) return false;
    if (!busy) {
        const previous = buttons.get(button);
        if (!previous) return false;
        if (button instanceof HTMLInputElement) button.value = previous.content;
        else button.innerHTML = previous.content;
        button.disabled = previous.disabled;
        button.style.minWidth = previous.minWidth;
        restoreAttribute(button, 'aria-busy', previous.ariaBusy);
        restoreAttribute(button, 'aria-disabled', previous.ariaDisabled);
        restoreAttribute(button, 'data-ax-button-busy', previous.busyAttribute);
        buttons.delete(button);
        return true;
    }
    if (buttons.has(button) || button.disabled) return false;
    buttons.set(button, {
        content: button instanceof HTMLInputElement ? button.value : button.innerHTML,
        disabled: button.disabled,
        minWidth: button.style.minWidth,
        ariaBusy: button.getAttribute('aria-busy'),
        ariaDisabled: button.getAttribute('aria-disabled'),
        busyAttribute: button.getAttribute('data-ax-button-busy'),
    });
    button.style.minWidth = `${button.getBoundingClientRect().width}px`;
    button.disabled = disable;
    button.setAttribute('aria-busy', 'true');
    button.setAttribute('aria-disabled', 'true');
    button.setAttribute('data-ax-button-busy', '');
    if (button instanceof HTMLInputElement) {
        button.value = label;
    } else {
        const content = document.createElement('span');
        content.className = 'inline-flex items-center justify-center gap-2';
        content.innerHTML = '<svg class="ax-spinner" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".25"/><path d="M12 3a9 9 0 0 1 9 9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';
        content.append(document.createTextNode(label));
        button.replaceChildren(content);
    }
    return true;
}

export function beginForm(form, button = null) {
    if (!form || forms.has(form)) return false;
    button ??= form.querySelector('button[type="submit"], input[type="submit"], button:not([type])');
    if (button?.disabled) return false;
    const previous = form.getAttribute('aria-busy');
    const label = button?.dataset.loadingLabel || form.dataset.loadingLabel || 'Procesando...';
    // Disabling a named submitter here would remove its value from the native request.
    if (button && !setButton(button, true, label, { disable: false })) return false;
    const regions = [];
    for (const [selector, hidden] of [[form.dataset.loadingTarget, true], [form.dataset.loadingSkeleton, false]]) {
        if (!selector) continue;
        for (const element of document.querySelectorAll(selector)) {
            regions.push({ element, hidden: element.classList.contains('hidden'), ariaBusy: element.getAttribute('aria-busy') });
            element.classList.toggle('hidden', hidden);
            element.setAttribute('aria-busy', 'true');
        }
    }
    forms.set(form, { button, ariaBusy: previous, regions });
    form.setAttribute('aria-busy', 'true');
    return true;
}

export function resetForm(form) {
    const previous = forms.get(form);
    if (!previous) return;
    setButton(previous.button, false);
    for (const region of previous.regions) {
        region.element.classList.toggle('hidden', region.hidden);
        restoreAttribute(region.element, 'aria-busy', region.ariaBusy);
    }
    restoreAttribute(form, 'aria-busy', previous.ariaBusy);
    forms.delete(form);
}

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-loading-form]')) return;
    if (forms.has(form)) {
        event.preventDefault();
        return;
    }
    if (event.defaultPrevented || form.method === 'dialog' || event.submitter?.formMethod === 'dialog') return;
    beginForm(form, event.submitter);
});

// Back/forward cache restores the DOM, including any spinner shown before leaving.
window.addEventListener('pageshow', () => {
    for (const form of forms.keys()) resetForm(form);
    for (const button of buttons.keys()) setButton(button, false);
});

window.AxLoading = { setButton, beginForm, resetForm };
