@auth
@if(!session()->has('auditoria_token'))
<div class="notification-bell relative" data-notification-bell data-feed="{{ route('notifications.feed', [], false) }}" data-read-all="{{ route('notifications.read-all', [], false) }}">
    <button type="button" class="relative p-2 rounded-xl text-gray-600 hover:bg-blue-50" aria-label="Notificaciones" aria-expanded="false" aria-controls="notification-panel" data-bell-toggle>
        <i class="fas fa-bell" aria-hidden="true"></i>
        <span data-bell-count hidden class="absolute -top-1 -right-1 rounded-full bg-red-600 text-white text-xs px-1.5" aria-live="polite"></span>
    </button>
    <section id="notification-panel" hidden data-bell-panel aria-label="Notificaciones recientes" class="absolute right-0 mt-3 rounded-2xl border border-gray-200 bg-white shadow-xl text-gray-800" style="width:min(360px,calc(100vw - 24px));max-height:75vh;overflow:auto;z-index:90">
        <div class="p-4 border-b border-gray-100">
            <strong>Notificaciones</strong>
            <button type="button" data-bell-read-all class="block text-sm text-blue-700 mt-2">Marcar todas como leídas</button>
        </div>
        <p data-bell-status role="status" class="p-3 text-sm text-gray-500">Cargando notificaciones…</p>
        <ul data-bell-items></ul>
        <a href="{{ route('notifications.index') }}" class="block border-t border-gray-100 p-3 text-center text-sm font-bold text-blue-700">Ver todas las notificaciones</a>
    </section>
</div>
@once
<style>
.notification-bell [data-bell-items]:not(:empty) { display: grid; gap: 10px; padding: 12px; }
.notification-bell .notification-preview {
    min-width: 0;
    padding: 12px;
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 12px;
    background: var(--bg-card, #fff);
    box-shadow: 0 2px 5px rgb(15 23 42 / 5%);
    overflow-wrap: anywhere;
    transition: box-shadow 150ms ease, border-color 150ms ease;
}
.notification-bell .notification-preview:hover,
.notification-bell .notification-preview:focus-within { border-color: #93c5fd; box-shadow: 0 3px 10px rgb(37 99 235 / 12%); }
.notification-bell .notification-preview.is-unread { border-color: #bfdbfe; border-left: 3px solid #3b82f6; background: #eff6ff; }
.notification-bell .notification-preview strong { line-height: 1.4; }
.notification-bell .notification-preview button {
    padding: 5px 9px;
    border: 1px solid var(--border, #dbeafe);
    border-radius: 8px;
    background: var(--bg-card, #fff);
    color: #2563eb;
}
html.dark .notification-bell .notification-preview.is-unread { border-color: #1e40af; border-left-color: #60a5fa; background: #172554; }
html.dark .notification-bell .notification-preview button { color: #93c5fd; }
@media (max-width: 640px) {
    .notification-bell [data-bell-panel] { position: fixed; left: 12px; right: 12px; top: 80px; width: auto !important; margin-top: 8px; }
}
</style>
<script src="{{ asset('js/notification-center.js') }}?v={{ filemtime(public_path('js/notification-center.js')) }}" defer></script>
@endonce
@endif
@endauth
