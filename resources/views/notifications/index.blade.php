<x-app title="Centro de Notificaciones">
    <main class="max-w-5xl mx-auto w-full px-4 py-8">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div><h1 class="text-2xl font-bold">Centro de Notificaciones</h1><p class="text-sm text-gray-500">Avisos y seguimiento de tus operaciones.</p></div>
            <form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="px-4 py-2 bg-blue-600 text-white rounded-xl">Marcar todas como leídas</button></form>
        </div>
        <form method="GET" class="flex flex-wrap gap-3 p-4 bg-white rounded-2xl border mb-5">
            <label class="text-sm">Buscar<input name="q" value="{{ request('q') }}" maxlength="120" class="block border rounded-lg px-3 py-2" placeholder="Título o mensaje"></label>
            <label class="text-sm">Lectura<select name="status" class="block border rounded-lg px-3 py-2">@foreach(['all'=>'Todas','unread'=>'No leídas','read'=>'Leídas','archived'=>'Archivadas'] as $value=>$label)<option value="{{ $value }}" @selected(request('status', 'all') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label class="text-sm">Categoría<select name="category" class="block border rounded-lg px-3 py-2"><option value="">Todas</option>@foreach(['ventas'=>'Ventas','comisiones'=>'Comisiones','ayuda'=>'Ayuda','auditorias'=>'Auditorías','seguridad'=>'Seguridad','administracion'=>'Administración'] as $value=>$label)<option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label class="text-sm">Prioridad<select name="priority" class="block border rounded-lg px-3 py-2"><option value="">Todas</option>@foreach(['informativa'=>'Informativa','importante'=>'Importante','alta'=>'Alta','critica'=>'Crítica'] as $value=>$label)<option value="{{ $value }}" @selected(request('priority') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label class="text-sm">Fecha<select name="order" class="block border rounded-lg px-3 py-2"><option value="desc" @selected(request('order', 'desc') === 'desc')>Más recientes</option><option value="asc" @selected(request('order') === 'asc')>Más antiguas</option></select></label>
            <button class="self-end px-4 py-2 rounded-lg bg-slate-800 text-white">Filtrar</button>
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
