// Keep the select as the form control and as the visible value, including values set by Alpine.
const selector = 'select:not([data-searchable-vendedor]):not([multiple])';
const normalize = (text) => text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase();

function icon(name, tone = 'blue') {
    const element = document.createElement('i');
    element.className = `fas ${name} filter-select__icon filter-select__icon--${tone}`;
    element.setAttribute('aria-hidden', 'true');
    return element;
}

function initFilterSelects() {
    const menu = document.createElement('div');
    menu.className = 'filter-select__menu';
    menu.hidden = true;
    menu.innerHTML = `
        <div class="filter-select__search">
            <i class="fas fa-magnifying-glass filter-select__icon filter-select__icon--muted" aria-hidden="true"></i>
            <input type="search" role="combobox" aria-autocomplete="list" aria-controls="ax-select-options" autocomplete="off">
        </div>
        <div id="ax-select-options" class="filter-select__options" role="listbox"></div>
        <p class="filter-select__empty" role="status" hidden>No se encontraron resultados.</p>`;
    document.body.append(menu);

    const searchBox = menu.querySelector('.filter-select__search');
    const search = searchBox.querySelector('input');
    const list = menu.querySelector('[role="listbox"]');
    const empty = menu.querySelector('.filter-select__empty');
    const controls = new WeakMap();
    let active;
    let options = [];
    let visible = [];
    let current = -1;
    let sequence = 0;
    let refreshFrame;

    function decoration(select, option) {
        const isDefault = !option || option.value === '' || option.value === 'todos';
        return icon(option?.dataset.icon || (isDefault ? 'fa-layer-group' : select.dataset.selectIcon || 'fa-list-ul'),
            option?.dataset.tone || (isDefault ? 'muted' : 'blue'));
    }

    function close(restoreFocus = false) {
        if (!active) return;
        const { host, toggle } = active;
        active = null;
        menu.hidden = true;
        host.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.removeAttribute('aria-activedescendant');
        search.setAttribute('aria-expanded', 'false');
        search.removeAttribute('aria-activedescendant');
        if (restoreFocus) toggle.focus({ preventScroll: true });
    }

    function sync(control) {
        const { select, host, toggle, leading, error, label } = control;
        const option = select.selectedOptions[0];
        const value = option?.label.trim();
        const nextIcon = decoration(select, option);
        if (leading.firstElementChild?.className !== nextIcon.className) leading.replaceChildren(nextIcon);
        toggle.setAttribute('aria-label', value ? `${label}: ${value}` : label);
        toggle.setAttribute('aria-required', String(select.required));
        toggle.disabled = select.matches(':disabled');
        host.hidden = select.hidden || select.classList.contains('hidden') || select.style.display === 'none';
        if (select.validity.valid && !error.hidden) {
            error.hidden = true;
            toggle.removeAttribute('aria-invalid');
        }
        if (active === control && (toggle.disabled || host.hidden)) close();
    }

    function position() {
        if (!active) return;
        const anchor = active.toggle.getBoundingClientRect();
        const viewport = document.documentElement;
        if (!anchor.width || anchor.bottom < 0 || anchor.top > viewport.clientHeight) {
            close();
            return;
        }

        const margin = 12;
        const gap = 6;
        const width = Math.min(Math.max(anchor.width, 200), viewport.clientWidth - margin * 2);
        menu.style.width = `${width}px`;
        menu.style.left = `${Math.max(margin, Math.min(anchor.left, viewport.clientWidth - width - margin))}px`;
        const below = viewport.clientHeight - anchor.bottom - gap - margin;
        const above = anchor.top - gap - margin;
        list.style.removeProperty('max-height');
        const fullHeight = menu.getBoundingClientRect().height;
        const flip = fullHeight > below && above > below;
        const available = flip ? above : below;
        if (fullHeight > available) {
            const chromeHeight = fullHeight - list.getBoundingClientRect().height;
            list.style.maxHeight = `${Math.max(32, available - chromeHeight)}px`;
        }
        const height = menu.getBoundingClientRect().height;
        const top = flip ? anchor.top - gap - height : anchor.bottom + gap;
        menu.style.top = `${Math.max(margin, Math.min(top, viewport.clientHeight - height - margin))}px`;
    }

    function activate(index) {
        current = index;
        for (const { row } of options) row.removeAttribute('data-active');
        const row = visible[index]?.row;
        const controller = searchBox.hidden ? active.toggle : search;
        if (!row) {
            controller.removeAttribute('aria-activedescendant');
            return;
        }
        row.dataset.active = '';
        controller.setAttribute('aria-activedescendant', row.id);
        const rowRect = row.getBoundingClientRect();
        const listRect = list.getBoundingClientRect();
        if (rowRect.top < listRect.top) list.scrollTop += rowRect.top - listRect.top;
        else if (rowRect.bottom > listRect.bottom) list.scrollTop += rowRect.bottom - listRect.bottom;
    }

    function filter(preferSelection = false) {
        const query = normalize(searchBox.hidden ? '' : search.value.trim());
        for (const { row, option, text } of options) {
            row.hidden = option.hidden || option.parentElement.hidden || !text.includes(query);
        }
        visible = options.filter(({ row }) => !row.hidden && !row.disabled);
        empty.hidden = options.some(({ row }) => !row.hidden);
        position();
        if (!active) return;
        const selected = preferSelection ? visible.findIndex(({ option }) => option.selected) : -1;
        activate(visible.length ? Math.max(0, selected) : -1);
    }

    function render() {
        const { select, label } = active;
        list.setAttribute('aria-label', label);
        const searchLabel = select.dataset.selectSearch || 'Buscar opción...';
        searchBox.hidden = !select.hasAttribute('data-select-search') && select.options.length < 10;
        search.placeholder = searchLabel;
        search.setAttribute('aria-label', searchLabel);
        search.setAttribute('aria-expanded', String(!searchBox.hidden));
        const groups = new Map();
        list.replaceChildren();
        options = [...select.options].map((option, index) => {
            const row = document.createElement('button');
            row.type = 'button';
            row.tabIndex = -1;
            row.id = `ax-select-option-${index}`;
            row.className = 'filter-select__option';
            row.dataset.index = index;
            row.setAttribute('role', 'option');
            row.setAttribute('aria-selected', String(option.selected));
            row.disabled = option.disabled || option.parentElement.disabled === true;
            const caption = document.createElement('span');
            caption.className = 'filter-select__option-label';
            caption.textContent = option.label.trim();
            const check = icon('fa-check');
            check.classList.add('filter-select__check');
            row.append(decoration(select, option), caption, check);

            const group = option.parentElement;
            if (group.tagName === 'OPTGROUP' && !groups.has(group)) {
                const section = document.createElement('div');
                section.className = 'filter-select__group';
                section.setAttribute('role', 'group');
                section.setAttribute('aria-label', group.label);
                const heading = document.createElement('div');
                heading.className = 'filter-select__group-heading';
                heading.textContent = group.label;
                section.append(heading);
                groups.set(group, section);
                list.append(section);
            }
            (groups.get(group) || list).append(row);
            return { row, option, text: normalize(`${option.label} ${option.dataset.search || ''}`) };
        });
        filter(true);
    }

    function open(control) {
        close();
        sync(control);
        if (control.toggle.disabled || control.host.hidden) return;

        active = control;
        const portal = control.select.closest('dialog, [role="dialog"]') || document.body;
        if (menu.parentElement !== portal) portal.append(menu);
        menu.hidden = false;
        control.host.classList.add('is-open');
        control.toggle.setAttribute('aria-expanded', 'true');
        search.value = '';
        render();
        if (active && !searchBox.hidden) search.focus({ preventScroll: true });
    }

    function choose(entry) {
        if (!active || !entry || entry.row.disabled || entry.row.hidden) return;
        const control = active;
        const { select } = control;
        const index = [...select.options].indexOf(entry.option);
        if (index === -1 || select.matches(':disabled')) return;
        const changed = select.selectedIndex !== index;
        select.selectedIndex = index;
        close(true);
        if (changed) {
            select.dispatchEvent(new Event('input', { bubbles: true }));
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }
        sync(control);
    }

    function keyboard(event, control = active) {
        const opened = active === control;
        if (event.key === 'Tab' && opened) {
            close(true);
        } else if (event.key === 'Escape' && opened) {
            event.preventDefault();
            event.stopPropagation();
            close(true);
        } else if (['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) {
            if (!opened && !['ArrowDown', 'ArrowUp'].includes(event.key)) return;
            event.preventDefault();
            if (!opened) {
                open(control);
                return;
            }
            const next = event.key === 'Home' ? 0 : event.key === 'End' ? visible.length - 1
                : current + (event.key === 'ArrowDown' ? 1 : -1);
            activate(visible.length ? Math.max(0, Math.min(visible.length - 1, next)) : -1);
        } else if (event.key === 'Enter' || (event.key === ' ' && event.target === control.toggle)) {
            event.preventDefault();
            if (opened) choose(visible[current]);
            else open(control);
        }
    }

    const watchOptions = new MutationObserver((records) => {
        const changed = new Set(records.map(({ target }) => {
            const element = target instanceof Element ? target : target.parentElement;
            return controls.get(element?.closest('select'));
        }));
        for (const control of changed) {
            if (!control) continue;
            sync(control);
            if (active === control) render();
        }
    });

    function enhance(select) {
        if (controls.has(select) || select.size > 1) return;
        const preceding = select.previousElementSibling;
        const labels = [...select.labels];
        if (!labels.length && preceding?.tagName === 'LABEL' && !preceding.htmlFor) labels.push(preceding);
        const label = select.dataset.selectLabel || select.getAttribute('aria-label')
            || labels[0]?.textContent.trim().replace(/\s+/g, ' ').replace(/\s*\*$/, '')
            || select.name.replace(/[_-]/g, ' ') || 'Seleccionar';
        const id = `ax-select-${++sequence}`;
        const host = document.createElement('div');
        host.className = 'filter-select';
        const field = document.createElement('div');
        field.className = 'filter-select__field';
        const toggle = document.createElement('button');
        toggle.id = id;
        toggle.type = 'button';
        toggle.className = 'filter-select__toggle';
        toggle.setAttribute('role', 'combobox');
        toggle.setAttribute('aria-haspopup', 'listbox');
        toggle.setAttribute('aria-controls', list.id);
        toggle.setAttribute('aria-expanded', 'false');
        const leading = document.createElement('span');
        const arrow = icon('fa-chevron-down', 'muted');
        arrow.classList.add('filter-select__arrow');
        toggle.append(leading, arrow);
        const error = document.createElement('p');
        error.id = `${id}-error`;
        error.className = 'filter-select__error';
        error.setAttribute('role', 'alert');
        error.hidden = true;
        toggle.setAttribute('aria-describedby', [select.getAttribute('aria-describedby'), error.id].filter(Boolean).join(' '));

        const control = { select, host, toggle, leading, error, label };
        controls.set(select, control);
        host.append(field, error);
        select.before(host);
        field.append(select, toggle);
        select.classList.add('filter-select__native');
        select.tabIndex = -1;
        select.setAttribute('aria-hidden', 'true');
        labels.forEach((associated) => { associated.htmlFor = id; });
        sync(control);

        toggle.addEventListener('focus', () => sync(control));
        toggle.addEventListener('click', () => active === control ? close() : open(control));
        toggle.addEventListener('keydown', (event) => keyboard(event, control));
        select.addEventListener('focus', () => toggle.focus());
        select.addEventListener('change', () => sync(control));
        select.addEventListener('filter-select:refresh', () => {
            sync(control);
            if (active === control) close();
        });
        select.addEventListener('invalid', (event) => {
            event.preventDefault();
            error.textContent = select.validationMessage;
            error.hidden = false;
            toggle.setAttribute('aria-invalid', 'true');
            const firstInvalid = select.form && [...select.form.elements].find((field) => field.willValidate && !field.validity.valid);
            if (!firstInvalid || firstInvalid === select) toggle.focus();
        });
        watchOptions.observe(select, { subtree: true, childList: true, characterData: true, attributes: true,
            attributeFilter: ['disabled', 'hidden', 'selected', 'label', 'value', 'required', 'style', 'class'] });
    }

    function scan(root) {
        if (root instanceof Element && root.matches(selector)) enhance(root);
        root.querySelectorAll(selector).forEach(enhance);
    }

    function refreshAfterInteraction() {
        if (refreshFrame) return;
        refreshFrame = requestAnimationFrame(() => {
            refreshFrame = null;
            document.querySelectorAll('.filter-select__native').forEach((select) => {
                const control = controls.get(select);
                if (control) sync(control);
            });
            position();
        });
    }

    scan(document);
    new MutationObserver((records) => {
        for (const { addedNodes } of records) {
            for (const node of addedNodes) {
                if (node instanceof Element && node.isConnected && !menu.contains(node)) scan(node);
            }
        }
        if (active && (!active.select.isConnected || !active.toggle.getClientRects().length)) close();
    }).observe(document.body, { childList: true, subtree: true });
    menu.addEventListener('click', (event) => {
        // The floating list can sit outside a modal's content box.
        event.stopPropagation();
        const row = event.target.closest('[data-index]');
        if (row) choose(options[Number(row.dataset.index)]);
    });
    search.addEventListener('input', () => { if (active) filter(); });
    search.addEventListener('keydown', (event) => { if (active) keyboard(event); });
    const dismissOutside = (event) => {
        if (active && !active.host.contains(event.target) && !menu.contains(event.target)) close();
    };
    document.addEventListener('pointerdown', dismissOutside);
    document.addEventListener('focusin', dismissOutside);
    for (const event of ['click', 'change', 'reset']) document.addEventListener(event, refreshAfterInteraction);
    window.addEventListener('pageshow', refreshAfterInteraction);
    window.addEventListener('resize', position);
    window.addEventListener('scroll', position, { capture: true, passive: true });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFilterSelects, { once: true });
} else {
    initFilterSelects();
}
