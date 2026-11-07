const DAY = 86_400_000;
const dateSelector = 'input[type="date"]:not([readonly])';
const monthLabel = new Intl.DateTimeFormat('es-SV', { month: 'long', year: 'numeric', timeZone: 'UTC' });
const shortMonth = new Intl.DateTimeFormat('es-SV', { month: 'short', timeZone: 'UTC' });
const dayLabel = new Intl.DateTimeFormat('es-SV', { dateStyle: 'full', timeZone: 'UTC' });

function makeDate(year, month, day = 1) {
    const date = new Date(0);
    date.setUTCFullYear(year, month, day);
    return date;
}

function fromValue(value) {
    if (!value) return null;
    const [year, month, day] = value.split('-').map(Number);
    const date = makeDate(year, month - 1, day);
    return Number.isFinite(+date) ? date : null;
}

function toValue(date) {
    if (!Number.isFinite(+date) || date.getUTCFullYear() < 1) return '';
    return [
        String(date.getUTCFullYear()).padStart(4, '0'),
        String(date.getUTCMonth() + 1).padStart(2, '0'),
        String(date.getUTCDate()).padStart(2, '0'),
    ].join('-');
}

function shiftMonth(date, offset) {
    const year = date.getUTCFullYear();
    const month = date.getUTCMonth() + offset;
    const lastDay = makeDate(year, month + 1, 0).getUTCDate();
    return makeDate(year, month, Math.min(date.getUTCDate(), lastDay));
}

function localToday() {
    const now = new Date();
    return makeDate(now.getFullYear(), now.getMonth(), now.getDate());
}

function setupDatePickers() {
    const panel = document.createElement('section');
    panel.id = 'ax-date-picker';
    panel.className = 'date-picker';
    panel.hidden = true;
    panel.setAttribute('role', 'dialog');
    panel.setAttribute('aria-label', 'Elegir fecha');
    panel.innerHTML = `
        <div class="date-picker__header">
            <button type="button" data-step="-1" aria-label="Mes anterior">
                <i class="fas fa-chevron-left" aria-hidden="true"></i>
            </button>
            <button type="button" data-month-toggle aria-label="Elegir mes y año" aria-controls="ax-date-months" aria-expanded="false"></button>
            <button type="button" data-step="1" aria-label="Mes siguiente">
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </button>
        </div>
        <div data-days>
            <div class="date-picker__week" aria-hidden="true">
                <span>Do</span><span>Lu</span><span>Ma</span><span>Mi</span><span>Ju</span><span>Vi</span><span>Sá</span>
            </div>
            <div class="date-picker__grid" role="group" aria-label="Días del mes"></div>
        </div>
        <div class="date-picker__months" id="ax-date-months" role="group" aria-label="Meses del año" hidden></div>
        <div class="date-picker__footer">
            <button type="button" data-clear>Borrar</button>
            <button type="button" data-today>Hoy</button>
        </div>`;
    document.body.append(panel);

    const heading = panel.querySelector('[data-month-toggle]');
    const days = panel.querySelector('[data-days]');
    const grid = panel.querySelector('.date-picker__grid');
    const months = panel.querySelector('.date-picker__months');
    const clear = panel.querySelector('[data-clear]');
    const todayButton = panel.querySelector('[data-today]');
    const steps = [...panel.querySelectorAll('[data-step]')];
    // Reuse the browser's min/max/step validation without changing the real field.
    const probe = document.createElement('input');
    probe.type = 'date';
    let active;
    let cursor;
    let choosingMonth = false;
    let nextId = 0;

    function canSelect(value) {
        probe.value = value;
        return Boolean(probe.value && probe.validity.valid);
    }

    function close(restoreFocus = false) {
        if (!active) return;
        const { trigger } = active;
        active = null;
        panel.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        if (restoreFocus) trigger.focus({ preventScroll: true });
    }

    function position() {
        if (!active) return;
        const anchor = active.input.getBoundingClientRect();
        const margin = 12;
        if (!anchor.width || anchor.bottom < 0 || anchor.top > window.innerHeight) {
            close();
            return;
        }
        const { width, height } = panel.getBoundingClientRect();
        const below = anchor.bottom + 8;
        const above = anchor.top - height - 8;
        const top = below + height > window.innerHeight - margin && above >= margin ? above : below;
        panel.style.left = `${Math.max(margin, Math.min(anchor.left, window.innerWidth - width - margin))}px`;
        panel.style.top = `${Math.max(margin, Math.min(top, window.innerHeight - height - margin))}px`;
    }

    function render(focusDay = false) {
        const { input } = active;
        const today = toValue(localToday());
        for (const attribute of ['min', 'max', 'step', 'value']) {
            const value = input.getAttribute(attribute);
            if (value === null) probe.removeAttribute(attribute);
            else probe.setAttribute(attribute, value);
        }
        const month = monthLabel.format(cursor);
        heading.textContent = choosingMonth ? String(cursor.getUTCFullYear()) : month[0].toUpperCase() + month.slice(1);
        heading.setAttribute('aria-label', choosingMonth ? 'Volver al calendario de días' : `Elegir mes y año, ${heading.textContent}`);
        heading.setAttribute('aria-expanded', String(choosingMonth));
        steps.forEach((button) => {
            const previous = Number(button.dataset.step) < 0;
            const unit = choosingMonth ? 'Año' : 'Mes';
            button.setAttribute('aria-label', `${unit} ${previous ? 'anterior' : 'siguiente'}`);
        });
        clear.disabled = input.required || !input.value;
        todayButton.disabled = !canSelect(today);
        days.hidden = choosingMonth;
        months.hidden = !choosingMonth;

        if (choosingMonth) {
            months.replaceChildren(...Array.from({ length: 12 }, (_, month) => {
                const date = makeDate(cursor.getUTCFullYear(), month);
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.month = String(month);
                button.textContent = shortMonth.format(date);
                button.setAttribute('aria-label', monthLabel.format(date));
                button.setAttribute('aria-pressed', String(month === cursor.getUTCMonth()));
                return button;
            }));
        } else {
            const first = makeDate(cursor.getUTCFullYear(), cursor.getUTCMonth());
            const start = +first - first.getUTCDay() * DAY;
            const last = makeDate(cursor.getUTCFullYear(), cursor.getUTCMonth() + 1, 0);
            const cells = Math.ceil((first.getUTCDay() + last.getUTCDate()) / 7) * 7;
            const focused = toValue(cursor);
            grid.replaceChildren(...Array.from({ length: cells }, (_, index) => {
                const date = new Date(start + index * DAY);
                const value = toValue(date);
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.day = value;
                button.textContent = value ? date.getUTCDate() : '';
                button.tabIndex = value === focused ? 0 : -1;
                if (value) button.setAttribute('aria-label', dayLabel.format(date));
                button.setAttribute('aria-pressed', String(Boolean(value && value === input.value)));
                if (value === today) button.setAttribute('aria-current', 'date');
                if (date.getUTCMonth() !== cursor.getUTCMonth()) button.classList.add('is-outside');
                if (!canSelect(value)) button.setAttribute('aria-disabled', 'true');
                return button;
            }));
            if (focusDay) grid.querySelector('[tabindex="0"]')?.focus({ preventScroll: true });
        }
        position();
    }

    function toggle(input, trigger, label) {
        if (input.matches(':disabled, [readonly]')) return;
        if (active?.input === input) {
            close(true);
            return;
        }
        close();
        active = { input, trigger };
        cursor = fromValue(input.value) || localToday();
        choosingMonth = false;
        panel.setAttribute('aria-label', `Elegir fecha · ${label}`);
        panel.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
        render(true);
    }

    function choose(value) {
        if (!active) return;
        const { input } = active;
        if (input.matches(':disabled, [readonly]') || (value ? !canSelect(value) : input.required)) return;
        const changed = input.value !== value;
        input.value = value;
        close(true);
        if (changed) {
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    panel.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (!active || !button) return;
        if (button.hasAttribute('data-step')) {
            const next = shiftMonth(cursor, Number(button.dataset.step) * (choosingMonth ? 12 : 1));
            if (toValue(next)) { cursor = next; render(); }
        } else if (button === heading) {
            choosingMonth = !choosingMonth;
            render(!choosingMonth);
            if (choosingMonth) months.querySelector('[aria-pressed="true"]')?.focus();
        } else if (button.hasAttribute('data-month')) {
            cursor = shiftMonth(cursor, Number(button.dataset.month) - cursor.getUTCMonth());
            choosingMonth = false;
            render(true);
        } else if (button.hasAttribute('data-day')) {
            if (button.dataset.day) choose(button.dataset.day);
        } else if (button === todayButton) {
            choose(toValue(localToday()));
        } else if (button === clear) {
            choose('');
        }
    });

    panel.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            event.stopPropagation();
            close(true);
            return;
        }
        if (event.key === 'Tab') {
            const buttons = [...panel.querySelectorAll('button:not(:disabled)')]
                .filter((button) => button.tabIndex >= 0 && button.getClientRects().length);
            const edge = event.shiftKey ? buttons[0] : buttons.at(-1);
            if (event.target === edge) {
                event.preventDefault();
                close(true);
            }
            return;
        }
        if (!event.target.hasAttribute('data-day')) return;
        const date = fromValue(event.target.dataset.day);
        if (!date) return;
        const offsets = { ArrowLeft: -1, ArrowRight: 1, ArrowUp: -7, ArrowDown: 7, Home: -date.getUTCDay(), End: 6 - date.getUTCDay() };
        let next;
        if (event.key in offsets) next = new Date(+date + offsets[event.key] * DAY);
        if (event.key === 'PageUp' || event.key === 'PageDown') {
            next = shiftMonth(date, (event.key === 'PageUp' ? -1 : 1) * (event.shiftKey ? 12 : 1));
        }
        if (!next || !toValue(next)) return;
        event.preventDefault();
        cursor = next;
        render(true);
    });

    function enhance(input) {
        if (input.classList.contains('date-control__input')) return;
        const adjacent = input.previousElementSibling;
        const label = input.labels?.[0] || (adjacent?.tagName === 'LABEL' && !adjacent.htmlFor ? adjacent : null);
        if (!input.id) input.id = `ax-date-${++nextId}`;
        if (label && !label.htmlFor && !label.contains(input)) label.htmlFor = input.id;
        const name = label?.textContent.replace(/\s+/g, ' ').trim() || input.getAttribute('aria-label') || 'Fecha';
        const host = document.createElement('span');
        host.className = 'date-control';
        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'date-control__trigger';
        trigger.setAttribute('aria-label', `Abrir calendario · ${name}`);
        trigger.setAttribute('aria-controls', panel.id);
        trigger.setAttribute('aria-haspopup', 'dialog');
        trigger.setAttribute('aria-expanded', 'false');
        trigger.innerHTML = '<i class="fas fa-calendar-days" aria-hidden="true"></i>';
        input.classList.add('date-control__input');
        input.before(host);
        host.append(input, trigger);
        trigger.addEventListener('click', () => toggle(input, trigger, name));
        label?.addEventListener('click', (event) => {
            if (host.contains(event.target) || input.matches(':disabled, [readonly]')) return;
            event.preventDefault();
            toggle(input, trigger, name);
        });
        input.addEventListener('keydown', (event) => {
            if (event.key === ' ' || (event.altKey && event.key === 'ArrowDown')) {
                event.preventDefault();
                toggle(input, trigger, name);
            }
        });
    }

    document.querySelectorAll(dateSelector).forEach(enhance);
    new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (!(node instanceof Element) || !node.isConnected) continue;
                if (node.matches(dateSelector)) enhance(node);
                node.querySelectorAll(dateSelector).forEach(enhance);
            }
        }
        if (active && (!active.input.isConnected || !active.input.getClientRects().length)) close();
    }).observe(document.body, { childList: true, subtree: true });
    const dismissOutside = (event) => {
        if (active && !panel.contains(event.target) && !active.input.parentElement?.contains(event.target)) close();
    };
    document.addEventListener('pointerdown', dismissOutside);
    document.addEventListener('focusin', dismissOutside);
    window.addEventListener('resize', position);
    window.addEventListener('scroll', position, { capture: true, passive: true });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupDatePickers, { once: true });
} else {
    setupDatePickers();
}
