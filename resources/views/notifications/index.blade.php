<x-app title="Centro de Notificaciones">
    <main class="max-w-5xl mx-auto w-full px-4 py-8">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div><h1 class="text-2xl font-bold">Centro de Notificaciones</h1><p class="text-sm text-gray-500">Avisos y seguimiento de tus operaciones.</p></div>
            <form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="px-4 py-2 bg-blue-600 text-white rounded-xl">Marcar todas como leídas</button></form>
        </div>
        @php
            $filterControl = 'h-9 w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100';
            $filterLabel = 'mb-1.5 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wide text-slate-700';
        @endphp
        <form method="GET" action="{{ route('notifications.index') }}" aria-label="Filtros de notificaciones"
            class="mb-5 grid grid-cols-1 items-end gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:grid-cols-2 lg:grid-cols-7">
            <div class="min-w-0 sm:col-span-2">
                <label for="notification-search" class="{{ $filterLabel }}"><i class="fas fa-search text-slate-400" aria-hidden="true"></i> Buscar notificación</label>
                <div class="relative">
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i>
                    <input id="notification-search" type="search" name="q" value="{{ request('q') }}" maxlength="120"
                        class="{{ $filterControl }} pl-9 font-normal placeholder:text-slate-400" placeholder="Título o mensaje de la notificación...">
                </div>
            </div>
            <div class="min-w-0">
                <label for="notification-status" class="{{ $filterLabel }}"><i class="fas fa-envelope-open text-slate-400" aria-hidden="true"></i> Lectura</label>
                <select id="notification-status" name="status" class="{{ $filterControl }}">
                    @foreach(['all'=>'Todas','unread'=>'No leídas','read'=>'Leídas','archived'=>'Archivadas'] as $value=>$label)
                        <option value="{{ $value }}" @selected(request('status', 'all') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-0">
                <label for="notification-category" class="{{ $filterLabel }}"><i class="fas fa-layer-group text-slate-400" aria-hidden="true"></i> Categoría</label>
                <select id="notification-category" name="category" class="{{ $filterControl }}">
                    <option value="">Todas</option>
                    @foreach(['ventas'=>'Ventas','comisiones'=>'Comisiones','ayuda'=>'Ayuda','auditorias'=>'Auditorías','seguridad'=>'Seguridad','administracion'=>'Administración'] as $value=>$label)
                        <option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-0">
                <label for="notification-priority" class="{{ $filterLabel }}"><i class="fas fa-flag text-slate-400" aria-hidden="true"></i> Prioridad</label>
                <select id="notification-priority" name="priority" class="{{ $filterControl }}">
                    <option value="">Todas</option>
                    @foreach(['informativa'=>'Informativa','importante'=>'Importante','alta'=>'Alta','critica'=>'Crítica'] as $value=>$label)
                        <option value="{{ $value }}" @selected(request('priority') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-0">
                <label for="notification-order" class="{{ $filterLabel }}"><i class="fas fa-calendar-alt text-slate-400" aria-hidden="true"></i> Fecha</label>
                <select id="notification-order" name="order" class="{{ $filterControl }}">
                    <option value="desc" @selected(request('order', 'desc') === 'desc')>Más recientes</option>
                    <option value="asc" @selected(request('order') === 'asc')>Más antiguas</option>
                </select>
            </div>
            <button type="submit" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-xl bg-blue-600 px-4 text-xs font-bold text-white shadow-md shadow-blue-600/20 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:col-span-2 lg:col-span-1">
                <i class="fas fa-filter" aria-hidden="true"></i> Filtrar
            </button>
        </form>
        @if($errors->any())<p role="alert" class="p-4 text-red-700">{{ $errors->first() }}</p>@endif
        <div class="space-y-3">
        @forelse($notifications as $item)
            @php($event = $item->event)
            <article class="rounded-2xl border p-5 {{ $item->read_at ? 'bg-white border-gray-200' : 'bg-blue-50 border-blue-200' }}">
                <div class="flex gap-3">
                    <i aria-hidden="true" class="fas {{ ['ventas'=>'fa-cart-shopping','comisiones'=>'fa-hand-holding-dollar','ayuda'=>'fa-circle-question','auditorias'=>'fa-clipboard-check','seguridad'=>'fa-shield-halved','administracion'=>'fa-gear'][$event->category] ?? 'fa-circle-info' }} text-blue-600 mt-1"></i>
                    <div class="flex-1"><h2 class="font-bold">{{ $event->title }} @unless($item->read_at)<span class="text-xs text-blue-700">• No leída</span>@endunless</h2>
                    <p class="text-sm mt-1">{{ $event->message }}</p>
                    <p class="text-xs text-gray-600 mt-2"><span class="font-bold {{ in_array($event->priority, ['alta','critica']) ? 'text-red-700' : '' }}">{{ ucfirst($event->priority) }}</span> · {{ ucfirst($event->category) }} · <time datetime="{{ $event->created_at->toIso8601String() }}">{{ $event->created_at->format('d/m/Y H:i') }}</time></p>
                    <div class="flex flex-wrap items-center gap-4 mt-3 text-sm">
                        <a class="text-blue-700 font-bold" href="{{ route('notifications.open', $item->id) }}">Abrir contenido</a>
                        @unless($item->read_at)<form method="POST" action="{{ route('notifications.read', $item->id) }}">@csrf<button class="text-blue-700">Marcar como leída</button></form>@endunless
                        @unless($item->archived_at)<form method="POST" action="{{ route('notifications.archive', $item->id) }}">@csrf<button class="text-gray-600">Archivar</button></form>@endunless
                    </div></div>
                </div>
            </article>
        @empty
            <div class="text-center p-12 rounded-2xl bg-white border"><i class="fas fa-bell text-3xl text-gray-400" aria-hidden="true"></i><p class="mt-4 font-bold">No hay notificaciones para estos filtros.</p></div>
        @endforelse
        </div>
        <div class="mt-5">{{ $notifications->links() }}</div>
    </main>
</x-app>
