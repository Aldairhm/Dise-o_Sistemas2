<script>
    (() => {
        const normalize = (value) => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase();

        document.querySelectorAll('select[data-searchable-vendedor]').forEach((select) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'relative w-full';
            select.parentNode.insertBefore(wrapper, select);
            wrapper.appendChild(select);
            select.classList.add('hidden');
            select.tabIndex = -1;
            select.setAttribute('aria-hidden', 'true');

            const input = document.createElement('input');
            input.type = 'text';
            input.autocomplete = 'off';
            input.placeholder = 'Buscar por nombre o usuario...';
            input.setAttribute('role', 'combobox');
            input.setAttribute('aria-autocomplete', 'list');
            input.setAttribute('aria-expanded', 'false');
            input.setAttribute('aria-controls', `${select.id}-opciones`);
            input.className = 'w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 pl-9 pr-10 text-xs font-semibold text-slate-700 transition placeholder:font-normal placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10';

            const searchId = `${select.id}-buscar`;
            input.id = searchId;
            const label = document.querySelector(`label[for="${select.id}"]`);
            if (label) label.htmlFor = searchId;

            const searchIcon = document.createElement('i');
            searchIcon.className = 'fas fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[11px] text-slate-400';
            searchIcon.setAttribute('aria-hidden', 'true');

            const clearButton = document.createElement('button');
            clearButton.type = 'button';
            clearButton.className = 'absolute right-2.5 top-1/2 flex h-6 w-6 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-200 hover:text-slate-700';
            clearButton.setAttribute('aria-label', 'Mostrar todos los vendedores');
            clearButton.title = 'Mostrar todos';
            clearButton.innerHTML = '<i class="fas fa-xmark text-xs" aria-hidden="true"></i>';

            const dropdown = document.createElement('div');
            dropdown.id = `${select.id}-opciones`;
            dropdown.setAttribute('role', 'listbox');
            dropdown.className = 'absolute z-30 mt-2 hidden max-h-64 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl shadow-slate-900/10';

            const emptyMessage = document.createElement('p');
            emptyMessage.className = 'hidden px-3 py-5 text-center text-xs text-slate-500';
            emptyMessage.textContent = 'No se encontraron vendedores con esa búsqueda.';

            const helpMessage = document.createElement('p');
            helpMessage.className = 'hidden mt-1 text-[10px] font-semibold text-rose-600';
            helpMessage.textContent = 'Elige un vendedor de la lista para aplicar el filtro.';

            const optionButtons = Array.from(select.options).map((option, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.id = `${select.id}-opcion-${index}`;
                button.setAttribute('role', 'option');
                button.dataset.value = option.value;
                button.dataset.label = option.textContent.trim();
                button.dataset.search = normalize(option.textContent.trim());
                button.setAttribute('aria-selected', option.selected ? 'true' : 'false');
                button.className = 'flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-left text-xs text-slate-700 transition hover:bg-blue-50 hover:text-blue-700';
                button.disabled = option.disabled;

                const content = document.createElement('span');
                content.className = 'flex min-w-0 items-center gap-2';
                const avatar = document.createElement('span');
                avatar.className = 'flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[9px] font-black text-blue-700';
                avatar.textContent = option.value ? option.textContent.trim().slice(0, 2).toUpperCase() : '';
                if (!option.value) {
                    avatar.className = 'flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-slate-100 text-[10px] text-slate-500';
                    avatar.innerHTML = '<i class="fas fa-users" aria-hidden="true"></i>';
                }

                const text = document.createElement('span');
                text.className = 'min-w-0 truncate font-bold';
                text.textContent = option.textContent.trim();
                content.append(avatar, text);

                const check = document.createElement('i');
                check.className = `fas fa-check ml-2 text-blue-600${option.selected ? '' : ' invisible'}`;
                check.dataset.optionCheck = '';
                check.setAttribute('aria-hidden', 'true');
                button.append(content, check);
                dropdown.appendChild(button);
                return button;
            });
            dropdown.appendChild(emptyMessage);

            wrapper.insertBefore(searchIcon, select);
            wrapper.insertBefore(input, select);
            wrapper.insertBefore(clearButton, select);
            wrapper.insertBefore(dropdown, select);
            wrapper.insertBefore(helpMessage, select);

            let activeIndex = -1;
            let pendingSearch = false;
            const selectedOption = () => select.options[select.selectedIndex];
            const visibleOptions = () => optionButtons.filter((button) => !button.classList.contains('hidden') && !button.disabled);
            select._searchPending = false;
            select._searchInput = input;
            select._searchHelp = helpMessage;

            function closeDropdown() {
                dropdown.classList.add('hidden');
                input.setAttribute('aria-expanded', 'false');
                input.removeAttribute('aria-activedescendant');
                activeIndex = -1;
                optionButtons.forEach((button) => button.classList.remove('bg-blue-50'));
            }

            function openDropdown() {
                dropdown.classList.remove('hidden');
                input.setAttribute('aria-expanded', 'true');
            }

            function updateSelected() {
                const selected = selectedOption();
                input.value = selected ? selected.textContent.trim() : '';
                pendingSearch = false;
                select._searchPending = false;
                helpMessage.classList.add('hidden');
                optionButtons.forEach((button) => {
                    const isSelected = button.dataset.value === select.value;
                    button.setAttribute('aria-selected', isSelected ? 'true' : 'false');
                    button.querySelector('[data-option-check]')?.classList.toggle('invisible', !isSelected);
                });
            }

            function filterOptions() {
                const query = normalize(input.value.trim());
                let visibleCount = 0;
                optionButtons.forEach((button) => {
                    const matches = button.dataset.search.includes(query);
                    button.classList.toggle('hidden', !matches);
                    if (matches) visibleCount++;
                });
                emptyMessage.classList.toggle('hidden', visibleCount > 0);
                activeIndex = -1;
                openDropdown();
            }

            function choose(button) {
                select.value = button.dataset.value;
                updateSelected();
                closeDropdown();
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }

            input.addEventListener('focus', () => {
                if (!pendingSearch) input.select();
                openDropdown();
            });
            input.addEventListener('input', () => {
                pendingSearch = true;
                select._searchPending = true;
                helpMessage.classList.add('hidden');
                filterOptions();
            });
            input.addEventListener('keydown', (event) => {
                const visible = visibleOptions();
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                    event.preventDefault();
                    openDropdown();
                    if (!visible.length) return;
                    const step = event.key === 'ArrowDown' ? 1 : -1;
                    activeIndex = Math.max(0, Math.min(visible.length - 1, activeIndex + step));
                    visible.forEach((button, index) => button.classList.toggle('bg-blue-50', index === activeIndex));
                    visible[activeIndex]?.scrollIntoView({ block: 'nearest' });
                    if (visible[activeIndex]) input.setAttribute('aria-activedescendant', visible[activeIndex].id);
                } else if (event.key === 'Enter' && !dropdown.classList.contains('hidden')) {
                    event.preventDefault();
                    const option = activeIndex >= 0 ? visible[activeIndex] : (visible.length === 1 ? visible[0] : null);
                    if (option) choose(option);
                } else if (event.key === 'Escape') {
                    updateSelected();
                    closeDropdown();
                }
            });

            optionButtons.forEach((button) => button.addEventListener('click', () => choose(button)));
            clearButton.addEventListener('click', () => {
                const allOption = optionButtons.find((button) => button.dataset.value === '');
                if (allOption) choose(allOption);
                input.focus();
            });
            select.addEventListener('change', updateSelected);
            document.addEventListener('click', (event) => {
                if (!wrapper.contains(event.target)) {
                    const clickedSubmit = select.form
                        && event.target.closest('button[type="submit"], input[type="submit"]');
                    if (pendingSearch && !clickedSubmit) updateSelected();
                    closeDropdown();
                }
            });

            select.form?.addEventListener('submit', (event) => {
                if (pendingSearch) {
                    event.preventDefault();
                    helpMessage.classList.remove('hidden');
                    openDropdown();
                    input.focus();
                }
            });

            updateSelected();
        });
    })();
</script>
