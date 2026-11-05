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
            @php
                $event = $item->event;
                $categoryLabel = ['ventas'=>'Ventas','comisiones'=>'Comisiones','ayuda'=>'Ayuda','auditorias'=>'Auditorías','seguridad'=>'Seguridad','administracion'=>'Administración'][$event->category] ?? 'Información';
                $categoryIcon = ['ventas'=>'fa-cart-shopping','comisiones'=>'fa-hand-holding-dollar','ayuda'=>'fa-circle-question','auditorias'=>'fa-clipboard-check','seguridad'=>'fa-shield-halved','administracion'=>'fa-gear'][$event->category] ?? 'fa-circle-info';
                $priorityLabel = ['informativa'=>'Informativa','importante'=>'Importante','alta'=>'Alta','critica'=>'Crítica'][$event->priority] ?? ucfirst($event->priority);
                $priorityColor = ['informativa'=>'text-slate-700','importante'=>'text-blue-600','alta'=>'text-orange-600','critica'=>'text-red-600'][$event->priority] ?? 'text-slate-700';
            @endphp
            <article aria-labelledby="notification-title-{{ $item->id }}"
                class="grid items-center gap-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md lg:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)_auto]">
                <div class="flex min-w-0 items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-600 text-xl text-white shadow-md shadow-blue-600/20">
                        <i class="fas {{ $categoryIcon }}" aria-hidden="true"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="mb-1.5 flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide text-blue-700">{{ $categoryLabel }}</span>
                            @if($item->archived_at)<span class="text-[10px] font-semibold text-slate-500"><i class="fas fa-box-archive" aria-hidden="true"></i> Archivada</span>@endif
                        </div>
                        <h2 id="notification-title-{{ $item->id }}" class="break-words text-sm font-extrabold leading-snug text-slate-900">{{ $event->title }}</h2>
                        <p class="mt-1.5 break-words text-xs leading-relaxed text-slate-500">{{ $event->message }}</p>
                    </div>
                </div>
                <div class="grid min-w-0 grid-cols-2 gap-4 rounded-2xl border border-slate-100 p-4 sm:grid-cols-3" style="background-color:var(--bg-page, #f8fafc)">
                    <div class="min-w-0">
                        <p class="mb-1 text-[9px] font-extrabold uppercase tracking-wide text-slate-400">Prioridad</p>
                        <p class="break-words text-xs font-extrabold {{ $priorityColor }}">{{ $priorityLabel }}</p>
                    </div>
                    <div class="min-w-0">
                        <p class="mb-1 text-[9px] font-extrabold uppercase tracking-wide text-slate-400">Lectura</p>
                        <p class="inline-flex items-center gap-1.5 text-xs font-extrabold {{ $item->read_at ? 'text-emerald-600' : 'text-blue-600' }}">
                            <i class="fas {{ $item->read_at ? 'fa-circle-check' : 'fa-circle' }} text-[9px]" aria-hidden="true"></i>
                            {{ $item->read_at ? 'Leída' : 'No leída' }}
                        </p>
                    </div>
                    <div class="col-span-2 min-w-0 sm:col-span-1">
                        <p class="mb-1 text-[9px] font-extrabold uppercase tracking-wide text-slate-400">Fecha</p>
                        <time datetime="{{ $event->created_at->toIso8601String() }}" class="block text-xs font-bold text-slate-700">
                            {{ $event->created_at->format('d/m/Y') }}
                            <span class="mt-0.5 block text-[10px] font-medium text-slate-500">{{ $event->created_at->format('H:i') }}</span>
                        </time>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3 lg:flex-col lg:items-stretch lg:gap-2">
                    <a href="{{ route('notifications.open', $item->id) }}" aria-label="Ver detalle: {{ $event->title }}"
                        style="background-color:var(--bg-page, #f1f5f9)"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-blue-100 hover:text-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <i class="fas fa-list-ul text-slate-400" aria-hidden="true"></i> Ver detalle <i class="fas fa-chevron-right text-[9px]" aria-hidden="true"></i>
                    </a>
                    @unless($item->read_at)
                        <form method="POST" action="{{ route('notifications.read', $item->id) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center gap-1.5 py-1 text-[11px] font-semibold text-blue-600 hover:underline lg:w-full">
                                <i class="fas fa-check" aria-hidden="true"></i> Marcar como leída
                            </button>
                        </form>
                    @endunless
                    @unless($item->archived_at)
                        <form method="POST" action="{{ route('notifications.archive', $item->id) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center gap-1.5 py-1 text-[11px] font-semibold text-slate-500 hover:underline lg:w-full">
                                <i class="fas fa-box-archive" aria-hidden="true"></i> Archivar
                            </button>
                        </form>
                    @endunless
                </div>
            </article>
        @empty
            <div class="text-center p-12 rounded-2xl bg-white border"><i class="fas fa-bell text-3xl text-gray-400" aria-hidden="true"></i><p class="mt-4 font-bold">No hay notificaciones para estos filtros.</p></div>
        @endforelse
        </div>
        <div class="mt-5">{{ $notifications->links() }}</div>
    </main>
</x-app>
