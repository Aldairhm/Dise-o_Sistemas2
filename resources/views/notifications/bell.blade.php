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
        <ul data-bell-items class="divide-y divide-gray-100"></ul>
        <a href="{{ route('notifications.index') }}" class="block p-3 text-center text-sm font-bold text-blue-700">Ver todas las notificaciones</a>
    </section>
</div>
@once
<style>
@media (max-width: 640px) {
    .notification-bell [data-bell-panel] { position: fixed; left: 12px; right: 12px; top: 80px; width: auto !important; margin-top: 8px; }
}
</style>
<script src="{{ asset('js/notification-center.js') }}" defer></script>
@endonce
@endif
@endauth
