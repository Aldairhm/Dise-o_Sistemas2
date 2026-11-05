(() => {
    'use strict';
    const root = document.querySelector('[data-notification-bell]');
    if (!root) return;
    const toggle = root.querySelector('[data-bell-toggle]');
    const panel = root.querySelector('[data-bell-panel]');
    const count = root.querySelector('[data-bell-count]');
    const status = root.querySelector('[data-bell-status]');
    const list = root.querySelector('[data-bell-items]');
    const all = root.querySelector('[data-bell-read-all]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const channel = typeof BroadcastChannel !== 'undefined' ? new BroadcastChannel('axstore-notifications') : null;

    const categories = {
        ventas: { label: 'Ventas', icon: 'fa-cart-shopping', color: '#2563eb', bg: '#eff6ff', text: '#1d4ed8' },
        comisiones: { label: 'Comisiones', icon: 'fa-hand-holding-dollar', color: '#059669', bg: '#ecfdf5', text: '#047857' },
        inventario: { label: 'Inventario', icon: 'fa-warehouse', color: '#d97706', bg: '#fffbeb', text: '#92400e' },
        ayuda: { label: 'Ayuda', icon: 'fa-circle-question', color: '#0284c7', bg: '#f0f9ff', text: '#0369a1' },
        auditorias: { label: 'Auditorías', icon: 'fa-clipboard-check', color: '#7c3aed', bg: '#f5f3ff', text: '#6d28d9' },
        seguridad: { label: 'Seguridad', icon: 'fa-shield-halved', color: '#d97706', bg: '#fffbeb', text: '#b45309' },
        administracion: { label: 'Administración', icon: 'fa-gear', color: '#475569', bg: '#f1f5f9', text: '#334155' }
    };

    const priorities = {
        informativa: { label: 'Informativa', bg: '#f1f5f9', text: '#475569' },
        importante: { label: 'Importante', bg: '#dbeafe', text: '#1e40af' },
        alta: { label: 'Alta', bg: '#fef3c7', text: '#92400e' },
        critica: { label: 'Crítica', bg: '#fee2e2', text: '#991b1b' }
    };

    let timer, busy = false, stopped = false, delay = 30000;
    const close = () => { panel.hidden = true; toggle.setAttribute('aria-expanded', 'false'); };
    const sync = () => { channel?.postMessage('refresh'); localStorage.setItem('axstore-notifications-sync', String(Date.now())); };
    const announce = (text) => { status.textContent = text; status.hidden = !text; };

    function relative(iso) {
        const minutes = Math.max(0, Math.floor((Date.now() - Date.parse(iso)) / 60000));
        if (minutes < 1) return 'Hace un momento';
        if (minutes < 60) return `Hace ${minutes} min`;
        if (minutes < 1440) return `Hace ${Math.floor(minutes / 60)} h`;
        return new Date(iso).toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
    }

    async function mark(url) {
        const response = await fetch(url, {method:'POST', credentials:'same-origin', headers:{Accept:'application/json', 'X-CSRF-TOKEN':csrf}});
        if (!response.ok || response.redirected) throw new Error('No se pudo guardar la lectura. Actualiza tu sesión e inténtalo otra vez.');
        sync();
    }

    function render(items) {
        list.replaceChildren();
        for (const item of items) {
            const cat = categories[item.category] || { label: 'General', icon: 'fa-circle-info', color: '#2563eb', bg: '#eff6ff', text: '#1d4ed8' };
            const prio = priorities[item.priority] || { label: item.priority || 'Info', bg: '#f1f5f9', text: '#475569' };
            const readUrl = item.open.replace(/\/abrir$/, '/leer');

            const li = document.createElement('li');
            li.className = `notification-preview${item.read ? '' : ' is-unread'}`;

            // 1. Cabecera (Icono + Categoría a la izq | Fecha + Indicador a la der)
            const header = document.createElement('div');
            header.className = 'flex items-center justify-between gap-2';

            const catBox = document.createElement('div');
            catBox.className = 'flex items-center gap-1.5 min-w-0';

            const iconSpan = document.createElement('span');
            iconSpan.className = 'flex items-center justify-center w-6 h-6 rounded-lg text-xs shrink-0';
            iconSpan.style.backgroundColor = cat.bg;
            iconSpan.style.color = cat.color;
            const icon = document.createElement('i');
            icon.className = `fas ${cat.icon}`;
            icon.setAttribute('aria-hidden', 'true');
            iconSpan.append(icon);

            const catLabel = document.createElement('span');
            catLabel.className = 'text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded shrink-0';
            catLabel.style.backgroundColor = cat.bg;
            catLabel.style.color = cat.text;
            catLabel.textContent = cat.label;

            catBox.append(iconSpan, catLabel);

            const metaRight = document.createElement('div');
            metaRight.className = 'flex items-center gap-2 shrink-0 text-slate-500';

            const timeSpan = document.createElement('span');
            timeSpan.className = 'text-[11px] flex items-center gap-1';
            timeSpan.title = new Date(item.date).toLocaleString('es');
            const clockIcon = document.createElement('i');
            clockIcon.className = 'far fa-clock text-[10px] text-slate-400';
            clockIcon.setAttribute('aria-hidden', 'true');
            timeSpan.append(clockIcon, document.createTextNode(relative(item.date)));

            metaRight.append(timeSpan);

            if (!item.read) {
                const unreadBadge = document.createElement('span');
                unreadBadge.className = 'text-[9px] font-extrabold uppercase px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-700 shrink-0';
                unreadBadge.textContent = 'Nueva';
                metaRight.append(unreadBadge);
            }

            header.append(catBox, metaRight);

            // 2. Cuerpo del mensaje (Título + Descripción clicables)
            const bodyLink = document.createElement('a');
            bodyLink.href = item.open;
            bodyLink.className = 'group block min-w-0 text-left py-0.5';

            const title = document.createElement('h4');
            title.className = 'text-xs font-bold text-slate-900 group-hover:text-blue-600 transition leading-snug';
            title.textContent = item.title;

            const msg = document.createElement('p');
            msg.className = 'text-xs text-slate-600 mt-1 leading-relaxed break-words';
            msg.textContent = item.message;

            bodyLink.append(title, msg);

            // 3. Pie de la tarjeta (Badge de Prioridad + Acciones)
            const footer = document.createElement('div');
            footer.className = 'flex items-center justify-between gap-2 pt-2 border-t border-slate-100 text-[11px] mt-0.5';

            const prioBadge = document.createElement('span');
            prioBadge.className = 'text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-full';
            prioBadge.style.backgroundColor = prio.bg;
            prioBadge.style.color = prio.text;
            prioBadge.textContent = prio.label;

            const actionsBox = document.createElement('div');
            actionsBox.className = 'flex items-center gap-1.5';

            if (!item.read) {
                const readBtn = document.createElement('button');
                readBtn.type = 'button';
                readBtn.className = 'text-[11px] font-semibold text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2 py-1 rounded transition flex items-center gap-1';
                const checkIcon = document.createElement('i');
                checkIcon.className = 'fas fa-check text-[10px]';
                readBtn.append(checkIcon, document.createTextNode('Marcar leída'));

                readBtn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    readBtn.disabled = true;
                    try { await mark(readUrl); await refresh(); } catch (err) { announce(err.message); } finally { readBtn.disabled = false; }
                });

                actionsBox.append(readBtn);

                bodyLink.addEventListener('click', async (event) => {
                    if (event.ctrlKey || event.metaKey || event.shiftKey) return;
                    event.preventDefault();
                    try { await mark(readUrl); location.assign(item.open); } catch (err) { announce(err.message); }
                });
            }

            const detailLink = document.createElement('a');
            detailLink.href = item.open;
            detailLink.className = 'text-slate-400 hover:text-blue-600 p-1 rounded transition flex items-center gap-1 text-[11px] font-medium';
            detailLink.title = 'Ver detalle';
            const arrowIcon = document.createElement('i');
            arrowIcon.className = 'fas fa-chevron-right text-[9px]';
            detailLink.append(document.createTextNode('Ver'), arrowIcon);

            if (!item.read) {
                detailLink.addEventListener('click', async (event) => {
                    if (event.ctrlKey || event.metaKey || event.shiftKey) return;
                    event.preventDefault();
                    try { await mark(readUrl); location.assign(item.open); } catch (err) { announce(err.message); }
                });
            }

            actionsBox.append(detailLink);
            footer.append(prioBadge, actionsBox);

            li.append(header, bodyLink, footer);
            list.append(li);
        }
    }

    async function refresh() {
        clearTimeout(timer);
        if (stopped || busy || document.hidden) return;
        busy = true;
        try {
            const response = await fetch(root.dataset.feed, {credentials:'same-origin', cache:'no-store', headers:{Accept:'application/json'}});
            if (response.redirected || [401,403,419].includes(response.status)) {
                stopped = true; list.replaceChildren(); count.hidden = true; announce('La sesión cambió o terminó. Actualiza la página para continuar.'); return;
            }
            if (!response.ok) throw new Error();
            const data = await response.json();
            count.textContent = data.unread > 99 ? '99+' : String(data.unread); count.hidden = !data.unread;
            toggle.setAttribute('aria-label', `Notificaciones: ${data.unread} sin leer`);
            all.disabled = !data.unread;
            render(data.items); announce(data.items.length ? '' : 'No tienes notificaciones recientes.'); delay = 30000;
        } catch (_) {
            delay = Math.min(delay * 2, 300000); announce('No se pudieron actualizar los avisos. Reintentaremos automáticamente.');
        } finally {
            busy = false;
            if (!stopped) timer = setTimeout(refresh, delay);
        }
    }

    toggle.addEventListener('click', () => { panel.hidden = !panel.hidden; toggle.setAttribute('aria-expanded', String(!panel.hidden)); if (!panel.hidden) refresh(); });
    document.addEventListener('click', (e) => { if (!root.contains(e.target)) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !panel.hidden) { close(); toggle.focus(); } });
    all.addEventListener('click', async () => {
        all.disabled = true;
        try { await mark(root.dataset.readAll); await refresh(); } catch (e) { announce(e.message); all.disabled = false; }
    });
    document.addEventListener('visibilitychange', () => { if (document.hidden) clearTimeout(timer); else refresh(); });
    window.addEventListener('online', refresh);
    window.addEventListener('pageshow', refresh);
    window.addEventListener('storage', (event) => { if (event.key === 'axstore-notifications-sync') refresh(); });
    if (channel) channel.onmessage = () => refresh();
    refresh();
})();
