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
    const icons = {ventas:'🛒', comisiones:'💵', ayuda:'💬', auditorias:'📋', seguridad:'🛡', administracion:'⚙'};
    let timer, busy = false, stopped = false, delay = 30000;
    const close = () => { panel.hidden = true; toggle.setAttribute('aria-expanded', 'false'); };
    const sync = () => { channel?.postMessage('refresh'); localStorage.setItem('axstore-notifications-sync', String(Date.now())); };
    const announce = (text) => { status.textContent = text; status.hidden = !text; };
    function relative(iso) {
        const minutes = Math.max(0, Math.floor((Date.now() - Date.parse(iso)) / 60000));
        if (minutes < 1) return 'Hace un momento';
        if (minutes < 60) return `Hace ${minutes} min`;
        if (minutes < 1440) return `Hace ${Math.floor(minutes / 60)} h`;
        return new Date(iso).toLocaleString('es');
    }
    async function mark(url) {
        const response = await fetch(url, {method:'POST', credentials:'same-origin', headers:{Accept:'application/json', 'X-CSRF-TOKEN':csrf}});
        if (!response.ok || response.redirected) throw new Error('No se pudo guardar la lectura. Actualiza tu sesión e inténtalo otra vez.');
        sync();
    }
    function render(items) {
        list.replaceChildren();
        for (const item of items) {
            const li = document.createElement('li');
            li.className = `p-3 ${item.read ? '' : 'bg-blue-50'}`;
            const a = document.createElement('a');
            a.href = item.open; a.className = 'block text-sm';
            const title = document.createElement('strong');
            title.textContent = `${icons[item.category] || '🔔'} ${item.title}`;
            const message = document.createElement('p'); message.textContent = item.message; message.className = 'text-gray-600 mt-1';
            const time = document.createElement('p'); time.className = `text-xs mt-1 ${['alta', 'critica'].includes(item.priority) ? 'text-red-700 font-bold' : 'text-gray-600'}`;
            time.textContent = `${item.priority.toUpperCase()} · ${relative(item.date)}${item.read ? '' : ' · No leída'}`;
            time.title = new Date(item.date).toLocaleString('es');
            a.append(title, message, time); li.append(a);
            if (!item.read) {
                const button = document.createElement('button'); button.type = 'button'; button.className = 'text-xs text-blue-700 mt-2'; button.textContent = 'Marcar como leída';
                const readUrl = item.open.replace(/\/abrir$/, '/leer');
                button.addEventListener('click', async () => {
                    button.disabled = true;
                    try { await mark(readUrl); await refresh(); } catch (e) { announce(e.message); } finally { button.disabled = false; }
                });
                li.append(button);
                a.addEventListener('click', async (event) => {
                    if (event.ctrlKey || event.metaKey || event.shiftKey) return;
                    event.preventDefault();
                    try { await mark(readUrl); location.assign(item.open); } catch (e) { announce(e.message); }
                });
            }
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
