@auth
@if(!session()->has('auditoria_token'))
<div class="notification-bell relative" data-notification-bell data-feed="{{ route('notifications.feed', [], false) }}" data-read-all="{{ route('notifications.read-all', [], false) }}">
    <button type="button" class="relative p-2 rounded-xl text-gray-600 hover:bg-blue-50 transition" aria-label="Notificaciones" aria-expanded="false" aria-controls="notification-panel" data-bell-toggle>
        <i class="fas fa-bell text-lg" aria-hidden="true"></i>
        <span data-bell-count hidden class="absolute -top-1 -right-1 rounded-full bg-red-600 text-white text-[11px] font-bold px-1.5 py-0.2 shadow-sm" aria-live="polite"></span>
    </button>
    <section id="notification-panel" hidden data-bell-panel aria-label="Notificaciones recientes" class="absolute right-0 mt-3 rounded-2xl border border-gray-200 bg-white shadow-2xl text-gray-800" style="width:min(400px,calc(100vw - 24px));max-height:78vh;overflow:auto;z-index:90">
        <div class="p-3.5 px-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
            <div class="flex items-center gap-2">
                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-50 text-blue-600 text-xs">
                    <i class="fas fa-bell" aria-hidden="true"></i>
                </span>
                <strong class="text-sm font-bold text-gray-900">Notificaciones</strong>
            </div>
            <button type="button" data-bell-read-all class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">Marcar todas como leídas</button>
        </div>
        <p data-bell-status role="status" class="p-4 text-center text-xs text-gray-500">Cargando notificaciones…</p>
        <ul data-bell-items></ul>
        <a href="{{ route('notifications.index') }}" class="block border-t border-gray-100 p-3 text-center text-xs font-bold text-blue-700 hover:bg-blue-50 transition sticky bottom-0 bg-white">Ver todas las notificaciones</a>
    </section>
</div>
@once
<style>
.notification-bell [data-bell-items]:not(:empty) { display: flex; flex-direction: column; gap: 10px; padding: 12px; }
.notification-bell .notification-preview {
    min-width: 0;
    padding: 12px 14px;
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 14px;
    background: var(--bg-card, #fff);
    box-shadow: 0 1px 3px rgb(15 23 42 / 4%);
    overflow-wrap: anywhere;
    transition: all 150ms ease;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.notification-bell .notification-preview:hover {
    border-color: #93c5fd;
    box-shadow: 0 4px 12px rgb(37 99 235 / 10%);
}
.notification-bell .notification-preview.is-unread {
    border-color: #bfdbfe;
    border-left: 3.5px solid #2563eb;
    background: #f8faff;
}
html.dark .notification-bell .notification-preview {
    background: #1e293b;
    border-color: #334155;
    color: #f1f5f9;
}
html.dark .notification-bell .notification-preview.is-unread {
    background: #0f172a;
    border-color: #1e3a8a;
    border-left-color: #3b82f6;
}
html.dark .notification-bell .notification-preview .border-slate-100,
html.dark .notification-bell .notification-preview .border-gray-100 {
    border-color: #334155;
}
@media (max-width: 640px) {
    .notification-bell [data-bell-panel] { position: fixed; left: 12px; right: 12px; top: 80px; width: auto !important; margin-top: 8px; }
}
</style>
<script src="{{ asset('js/notification-center.js') }}?v={{ filemtime(public_path('js/notification-center.js')) }}" defer></script>
@endonce
@endif
@endauth