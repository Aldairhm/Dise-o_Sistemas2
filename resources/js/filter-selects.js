// Only the filters explicitly marked in the four requested screens are enhanced.
// The original select remains the form control and continues emitting native changes.
export function initFilterSelects(scope = document) {
    const controls = [];
    const normalize = (text) => text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase();
    const icon = (name, tone = 'blue') => {
        const element = document.createElement('i');
        element.className = `fas ${name} filter-select__icon filter-select__icon--${tone}`;
        element.setAttribute('aria-hidden', 'true');
        return element;
    };

    scope.querySelectorAll('select[data-filter-select]').forEach((select) => {
        if (select.dataset.filterSelectReady) return;
        const label = select.dataset.selectLabel || select.labels?.[0]?.textContent.trim() || 'Seleccionar';
        const searchable = select.hasAttribute('data-select-search');
        const host = document.createElement('div');
        host.className = 'filter-select';
        host.dataset.filterSelectHost = '';
        const toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.id = `${select.id}-control`;
        toggle.className = 'filter-select__toggle';
        toggle.setAttribute('aria-label', label);
        toggle.setAttribute('aria-haspopup', 'listbox');
        toggle.setAttribute('aria-expanded', 'false');
        if (!searchable) toggle.setAttribute('role', 'combobox');
        const selectedIcon = document.createElement('span');
        const selectedLabel = document.createElement('span');
        selectedLabel.className = 'filter-select__label';
        const arrow = icon('fa-chevron-down', 'muted');
        arrow.classList.add('filter-select__arrow');
        toggle.append(selectedIcon, selectedLabel, arrow);

        const menu = document.createElement('div');
        menu.className = 'filter-select__menu';
        menu.hidden = true;
        const list = document.createElement('div');
        list.id = `${select.id}-options`;
        list.className = 'filter-select__options';
        list.setAttribute('role', 'listbox');
        list.setAttribute('aria-label', label);
        toggle.setAttribute('aria-controls', list.id);
        let search;
        if (searchable) {
            const searchBox = document.createElement('div');
            searchBox.className = 'filter-select__search';
            search = document.createElement('input');
            search.type = 'search';
            search.autocomplete = 'off';
            search.placeholder = select.dataset.selectSearch || 'Buscar...';
            search.setAttribute('aria-label', search.placeholder);
            search.setAttribute('role', 'combobox');
            search.setAttribute('aria-autocomplete', 'list');
            search.setAttribute('aria-controls', list.id);
            search.setAttribute('aria-expanded', 'false');
            searchBox.append(icon('fa-magnifying-glass', 'muted'), search);
            menu.append(searchBox);
        }

        const options = Array.from(select.options).map((option, index) => {
            const row = document.createElement('button');
            row.type = 'button';
            row.tabIndex = -1;
            row.id = `${select.id}-option-${index}`;
            row.className = 'filter-select__option';
            row.setAttribute('role', 'option');
            row.disabled = option.disabled || option.parentElement.disabled === true;
            const isDefault = option.value === '' || option.value === 'todos';
            const iconName = option.dataset.icon || (isDefault ? (searchable ? 'fa-users' : 'fa-layer-group') : select.dataset.selectIcon || 'fa-tag');
            const tone = option.dataset.tone || (isDefault ? 'muted' : 'blue');
            const text = document.createElement('span');
            text.className = 'filter-select__option-label';
            text.textContent = option.label.trim();
            const check = icon('fa-check');
            check.classList.add('filter-select__check');
            row.append(icon(iconName, tone), text, check);
            list.append(row);
            return { row, option, index, iconName, tone, text: normalize(`${option.label} ${option.dataset.search || ''}`) };
        });
        const empty = document.createElement('p');
        empty.className = 'filter-select__empty';
        empty.textContent = 'No se encontraron resultados.';
        empty.hidden = true;
        menu.append(list, empty);

        let active = -1;
        let visible = options.filter(({ row }) => !row.disabled);
        function close(focus = false) {
            menu.hidden = true;
            host.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
            search?.setAttribute('aria-expanded', 'false');
            toggle.removeAttribute('aria-activedescendant');
            search?.removeAttribute('aria-activedescendant');
            if (focus) toggle.focus();
        }
        function sync() {
            const selected = options.find(({ index }) => index === select.selectedIndex);
            selectedIcon.replaceChildren(icon(selected?.iconName || 'fa-layer-group', selected?.tone || 'muted'));
            selectedLabel.textContent = selected?.option.label.trim() || 'Seleccionar';
            toggle.title = selectedLabel.textContent;
            toggle.setAttribute('aria-label', `${label}: ${selectedLabel.textContent}`);
            toggle.disabled = select.disabled;
            options.forEach(({ row, index }) => row.setAttribute('aria-selected', String(index === select.selectedIndex)));
        }
        function activate(index) {
            active = index;
            options.forEach(({ row }) => row.removeAttribute('data-active'));
            const row = visible[index]?.row;
            if (!row) {
                toggle.removeAttribute('aria-activedescendant');
                search?.removeAttribute('aria-activedescendant');
                return;
            }
            row.dataset.active = '';
            (search || toggle).setAttribute('aria-activedescendant', row.id);
            const rowRect = row.getBoundingClientRect();
            const listRect = list.getBoundingClientRect();
            if (rowRect.top < listRect.top) list.scrollTop += rowRect.top - listRect.top;
            else if (rowRect.bottom > listRect.bottom) list.scrollTop += rowRect.bottom - listRect.bottom;
        }
        function filter() {
            const query = normalize(search?.value.trim() || '');
            options.forEach(({ row, text, option }) => { row.hidden = option.hidden || !text.includes(query); });
            visible = options.filter(({ row }) => !row.hidden && !row.disabled);
            empty.hidden = visible.length > 0;
            activate(visible.length ? 0 : -1);
        }
        function positionMenu() {
            // Align the popup inside the viewport, including the last filter on mobile.
            menu.style.left = '0px';
            menu.style.top = 'calc(100% + 6px)';
            menu.style.bottom = 'auto';
            const rect = menu.getBoundingClientRect();
            if (rect.right > window.innerWidth - 12) menu.style.left = `${window.innerWidth - 12 - rect.right}px`;
            const available = window.innerHeight - toggle.getBoundingClientRect().bottom - 18;
            if (available < rect.height && toggle.getBoundingClientRect().top > rect.height + 12) {
                menu.style.top = 'auto';
                menu.style.bottom = 'calc(100% + 6px)';
            }
        }
        function open() {
            controls.forEach((control) => control.close());
            sync();
            if (search) search.value = '';
            menu.hidden = false;
            host.classList.add('is-open');
            toggle.setAttribute('aria-expanded', 'true');
            search?.setAttribute('aria-expanded', 'true');
            filter();
            positionMenu();
            const selected = visible.findIndex(({ index }) => index === select.selectedIndex);
            activate(selected >= 0 ? selected : 0);
            search?.focus();
        }
        function choose(entry) {
            if (!entry || entry.row.disabled) return;
            select.selectedIndex = entry.index;
            sync();
            close(true);
            select.dispatchEvent(new Event('input', { bubbles: true }));
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }
        function keyboard(event) {
            if (event.key === 'Tab') { close(); return; }
            if (event.key === 'Escape' && !menu.hidden) {
                event.preventDefault();
                event.stopPropagation();
                close(true);
            } else if (['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) {
                event.preventDefault();
                if (menu.hidden) { open(); return; }
                const next = event.key === 'Home' ? 0 : event.key === 'End' ? visible.length - 1 : active + (event.key === 'ArrowDown' ? 1 : -1);
                activate(Math.max(0, Math.min(visible.length - 1, next)));
            } else if (event.key === 'Enter' || (event.key === ' ' && event.target === toggle)) {
                event.preventDefault();
                if (menu.hidden) open(); else choose(visible[active]);
            }
        }
        toggle.addEventListener('click', () => menu.hidden ? open() : close());
        toggle.addEventListener('keydown', keyboard);
        search?.addEventListener('input', filter);
        search?.addEventListener('keydown', keyboard);
        options.forEach((entry) => entry.row.addEventListener('click', () => choose(entry)));
        select.addEventListener('change', sync);
        select.addEventListener('filter-select:refresh', () => { sync(); close(); });
        select.form?.addEventListener('reset', () => setTimeout(() => { sync(); close(); }, 0));
        window.addEventListener('pageshow', sync);
        document.addEventListener('click', (event) => { if (!host.contains(event.target)) close(); });
        window.addEventListener('resize', () => { if (!menu.hidden) positionMenu(); });

        const labels = Array.from(select.labels || []);
        select.before(host);
        host.append(toggle, menu, select);
        // No name/id/value changes: GET filters, FormData and existing AJAX handlers keep working.
        select.hidden = true;
        select.tabIndex = -1;
        select.setAttribute('aria-hidden', 'true');
        labels.forEach((label) => { label.htmlFor = toggle.id; });
        select.dataset.filterSelectReady = 'true';
        sync();
        controls.push({ close });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initFilterSelects(), { once: true });
} else {
    initFilterSelects();
}
