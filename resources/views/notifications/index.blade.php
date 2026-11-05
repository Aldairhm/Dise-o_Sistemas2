<x-app title="Centro de Notificaciones">
    <main class="max-w-6xl mx-auto w-full px-4 py-8">
        {{-- Encabezado Principal --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-7">
            <div class="flex items-center gap-3.5">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-lg shadow-blue-600/20">
                    <i class="fas fa-bell text-xl" aria-hidden="true"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">Centro de Notificaciones</h1>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5">Avisos del sistema y seguimiento en tiempo real de tus operaciones.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 hover:shadow-blue-600/30 transition-all active:scale-95">
                    <i class="fas fa-check-double text-xs" aria-hidden="true"></i>
                    Marcar todas como leídas
                </button>
            </form>
        </div>

        @php
            $labelBase = 'mb-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-tight';
            $btnSelectBase = 'h-10 w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50/90 px-3.5 text-xs font-bold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 flex items-center justify-between gap-2 cursor-pointer text-left';
            $hasFilters = request()->hasAny(['q', 'status', 'category', 'priority']) && (request('status', 'all') !== 'all' || request('q') || request('category') || request('priority'));

            // Definición de opciones para los selects custom
            $statusOptions = [
                'all'      => ['label' => 'Todas', 'icon' => 'fas fa-list text-slate-400'],
                'unread'   => ['label' => 'No leídas', 'icon' => 'fas fa-circle text-blue-500 text-[9px]'],
                'read'     => ['label' => 'Leídas', 'icon' => 'fas fa-circle-check text-emerald-500 text-[11px]'],
                'archived' => ['label' => 'Archivadas', 'icon' => 'fas fa-box-archive text-slate-400 text-[11px]'],
            ];
            $currentStatus = request('status', 'all');
            $activeStatus = $statusOptions[$currentStatus] ?? $statusOptions['all'];

            $categoryOptions = [
                ''               => ['label' => 'Todas', 'icon' => 'fas fa-border-all text-slate-400'],
                'ventas'         => ['label' => 'Ventas', 'icon' => 'fas fa-cart-shopping text-blue-500'],
                'comisiones'     => ['label' => 'Comisiones', 'icon' => 'fas fa-hand-holding-dollar text-emerald-500'],
                'ayuda'          => ['label' => 'Ayuda', 'icon' => 'fas fa-circle-question text-sky-500'],
                'auditorias'     => ['label' => 'Auditorías', 'icon' => 'fas fa-clipboard-check text-purple-500'],
                'seguridad'      => ['label' => 'Seguridad', 'icon' => 'fas fa-shield-halved text-amber-500'],
                'administracion' => ['label' => 'Administración', 'icon' => 'fas fa-gear text-slate-500'],
            ];
            $currentCategory = request('category', '');
            $activeCategory = $categoryOptions[$currentCategory] ?? $categoryOptions[''];

            $priorityOptions = [
                ''            => ['label' => 'Todas', 'icon' => 'fas fa-tags text-slate-400'],
                'informativa' => ['label' => 'Informativa', 'icon' => 'fas fa-circle-info text-slate-500'],
                'importante'  => ['label' => 'Importante', 'icon' => 'fas fa-bell text-blue-500'],
                'alta'        => ['label' => 'Alta', 'icon' => 'fas fa-triangle-exclamation text-amber-500'],
                'critica'     => ['label' => 'Crítica', 'icon' => 'fas fa-circle-radiation text-red-500'],
            ];
            $currentPriority = request('priority', '');
            $activePriority = $priorityOptions[$currentPriority] ?? $priorityOptions[''];

            $orderOptions = [
                'desc' => ['label' => 'Más recientes', 'icon' => 'fas fa-arrow-down-wide-short text-blue-600'],
                'asc'  => ['label' => 'Más antiguas',  'icon' => 'fas fa-arrow-up-wide-short text-slate-500'],
            ];
            $currentOrder = request('order', 'desc');
            $activeOrder = $orderOptions[$currentOrder] ?? $orderOptions['desc'];
        @endphp

        {{-- Formulario de Filtros con Selects Totalmente Personalizados --}}
        <form method="GET" action="{{ route('notifications.index') }}" aria-label="Filtros de notificaciones"
            class="mb-6 rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm transition-all hover:shadow-md">
            
            <div class="grid grid-cols-1 items-end gap-3.5 sm:grid-cols-2 lg:grid-cols-12">
                {{-- Buscar Notificación --}}
                <div class="min-w-0 sm:col-span-2 lg:col-span-3">
                    <label for="notification-search" class="{{ $labelBase }}">
                        <i class="fas fa-search text-blue-600 text-xs" aria-hidden="true"></i>
                        <span>Buscar notificación</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-search pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i>
                        <input id="notification-search" type="search" name="q" value="{{ request('q') }}" maxlength="120"
                            class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50/80 pl-9 pr-3 text-xs font-medium text-slate-800 placeholder:text-slate-400 shadow-sm transition hover:border-slate-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10"
                            placeholder="Buscar por título o contenido...">
                    </div>
                </div>

                {{-- Select Custom: Estado / Lectura --}}
                <div class="min-w-0 sm:col-span-1 lg:col-span-2 relative" data-custom-select>
                    <label class="{{ $labelBase }}">
                        <i class="fas fa-envelope-open-text text-blue-600 text-xs" aria-hidden="true"></i>
                        <span>Estado</span>
                    </label>
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                    <button type="button" class="{{ $btnSelectBase }}" data-toggle aria-expanded="false">
                        <span class="flex items-center gap-2 truncate">
                            <i class="{{ $activeStatus['icon'] }}" data-icon aria-hidden="true"></i>
                            <span class="truncate" data-label>{{ $activeStatus['label'] }}</span>
                        </span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" data-arrow aria-hidden="true"></i>
                    </button>
                    <div class="hidden absolute left-0 top-full z-50 mt-1.5 w-full min-w-[170px] rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xl" data-menu>
                        <div class="space-y-0.5">
                            @foreach($statusOptions as $val => $opt)
                                @php $isSelected = ($currentStatus === $val); @endphp
                                <button type="button" data-option data-value="{{ $val }}" data-label="{{ $opt['label'] }}" data-icon="{{ $opt['icon'] }}"
                                    class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left {{ $isSelected ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <span class="flex items-center gap-2 truncate">
                                        <i class="{{ $opt['icon'] }}" aria-hidden="true"></i>
                                        <span>{{ $opt['label'] }}</span>
                                    </span>
                                    <i class="fas fa-check text-blue-600 text-[10px] {{ $isSelected ? '' : 'hidden' }}" data-check aria-hidden="true"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Select Custom: Categoría --}}
                <div class="min-w-0 sm:col-span-1 lg:col-span-2 relative" data-custom-select>
                    <label class="{{ $labelBase }}">
                        <i class="fas fa-layer-group text-blue-600 text-xs" aria-hidden="true"></i>
                        <span>Categoría</span>
                    </label>
                    <input type="hidden" name="category" value="{{ $currentCategory }}">
                    <button type="button" class="{{ $btnSelectBase }}" data-toggle aria-expanded="false">
                        <span class="flex items-center gap-2 truncate">
                            <i class="{{ $activeCategory['icon'] }}" data-icon aria-hidden="true"></i>
                            <span class="truncate" data-label>{{ $activeCategory['label'] }}</span>
                        </span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" data-arrow aria-hidden="true"></i>
                    </button>
                    <div class="hidden absolute left-0 top-full z-50 mt-1.5 w-full min-w-[180px] rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xl max-h-64 overflow-y-auto" data-menu>
                        <div class="space-y-0.5">
                            @foreach($categoryOptions as $val => $opt)
                                @php $isSelected = ($currentCategory === $val); @endphp
                                <button type="button" data-option data-value="{{ $val }}" data-label="{{ $opt['label'] }}" data-icon="{{ $opt['icon'] }}"
                                    class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left {{ $isSelected ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <span class="flex items-center gap-2 truncate">
                                        <i class="{{ $opt['icon'] }}" aria-hidden="true"></i>
                                        <span>{{ $opt['label'] }}</span>
                                    </span>
                                    <i class="fas fa-check text-blue-600 text-[10px] {{ $isSelected ? '' : 'hidden' }}" data-check aria-hidden="true"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Select Custom: Prioridad --}}
                <div class="min-w-0 sm:col-span-1 lg:col-span-2 relative" data-custom-select>
                    <label class="{{ $labelBase }}">
                        <i class="fas fa-flag text-blue-600 text-xs" aria-hidden="true"></i>
                        <span>Prioridad</span>
                    </label>
                    <input type="hidden" name="priority" value="{{ $currentPriority }}">
                    <button type="button" class="{{ $btnSelectBase }}" data-toggle aria-expanded="false">
                        <span class="flex items-center gap-2 truncate">
                            <i class="{{ $activePriority['icon'] }}" data-icon aria-hidden="true"></i>
                            <span class="truncate" data-label>{{ $activePriority['label'] }}</span>
                        </span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" data-arrow aria-hidden="true"></i>
                    </button>
                    <div class="hidden absolute left-0 top-full z-50 mt-1.5 w-full min-w-[170px] rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xl" data-menu>
                        <div class="space-y-0.5">
                            @foreach($priorityOptions as $val => $opt)
                                @php $isSelected = ($currentPriority === $val); @endphp
                                <button type="button" data-option data-value="{{ $val }}" data-label="{{ $opt['label'] }}" data-icon="{{ $opt['icon'] }}"
                                    class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left {{ $isSelected ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <span class="flex items-center gap-2 truncate">
                                        <i class="{{ $opt['icon'] }}" aria-hidden="true"></i>
                                        <span>{{ $opt['label'] }}</span>
                                    </span>
                                    <i class="fas fa-check text-blue-600 text-[10px] {{ $isSelected ? '' : 'hidden' }}" data-check aria-hidden="true"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Select Custom: Fecha / Orden --}}
                <div class="min-w-0 sm:col-span-1 lg:col-span-2 relative" data-custom-select>
                    <label class="{{ $labelBase }}">
                        <i class="fas fa-arrow-down-wide-short text-blue-600 text-xs" aria-hidden="true"></i>
                        <span>Orden</span>
                    </label>
                    <input type="hidden" name="order" value="{{ $currentOrder }}">
                    <button type="button" class="{{ $btnSelectBase }}" data-toggle aria-expanded="false">
                        <span class="flex items-center gap-2 truncate">
                            <i class="{{ $activeOrder['icon'] }}" data-icon aria-hidden="true"></i>
                            <span class="truncate" data-label>{{ $activeOrder['label'] }}</span>
                        </span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" data-arrow aria-hidden="true"></i>
                    </button>
                    <div class="hidden absolute left-0 top-full z-50 mt-1.5 w-full min-w-[170px] rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xl" data-menu>
                        <div class="space-y-0.5">
                            @foreach($orderOptions as $val => $opt)
                                @php $isSelected = ($currentOrder === $val); @endphp
                                <button type="button" data-option data-value="{{ $val }}" data-label="{{ $opt['label'] }}" data-icon="{{ $opt['icon'] }}"
                                    class="w-full flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors text-left {{ $isSelected ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <span class="flex items-center gap-2 truncate">
                                        <i class="{{ $opt['icon'] }}" aria-hidden="true"></i>
                                        <span>{{ $opt['label'] }}</span>
                                    </span>
                                    <i class="fas fa-check text-blue-600 text-[10px] {{ $isSelected ? '' : 'hidden' }}" data-check aria-hidden="true"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Botón Filtrar y Limpiar --}}
                <div class="min-w-0 flex items-center gap-2 sm:col-span-2 lg:col-span-1">
                    <button type="submit" class="inline-flex h-10 w-full items-center justify-center gap-1.5 rounded-xl bg-blue-600 px-3 text-xs font-bold text-white shadow-md shadow-blue-600/20 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 active:scale-95" title="Aplicar filtros">
                        <i class="fas fa-filter text-xs" aria-hidden="true"></i>
                        <span class="lg:hidden">Filtrar</span>
                    </button>
                    @if($hasFilters)
                        <a href="{{ route('notifications.index') }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-2.5 text-xs font-bold text-slate-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition" title="Limpiar filtros">
                            <i class="fas fa-rotate-left" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if($errors->any())
            <div role="alert" class="mb-5 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-xs font-bold text-red-700">
                <i class="fas fa-triangle-exclamation text-base" aria-hidden="true"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        {{-- Listado de Notificaciones --}}
        <div class="space-y-3.5">
        @forelse($notifications as $item)
            @php
                $event = $item->event;
                $categoryConfig = [
                    'ventas' => [
                        'label'    => 'Ventas',
                        'icon'     => 'fa-cart-shopping',
                        'gradient' => 'from-blue-600 to-indigo-600',
                        'badge'    => 'bg-blue-50 text-blue-700 border-blue-200/80',
                    ],
                    'comisiones' => [
                        'label'    => 'Comisiones',
                        'icon'     => 'fa-hand-holding-dollar',
                        'gradient' => 'from-emerald-500 to-teal-600',
                        'badge'    => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                    ],
                    'ayuda' => [
                        'label'    => 'Ayuda',
                        'icon'     => 'fa-circle-question',
                        'gradient' => 'from-sky-500 to-blue-600',
                        'badge'    => 'bg-sky-50 text-sky-700 border-sky-200/80',
                    ],
                    'auditorias' => [
                        'label'    => 'Auditorías',
                        'icon'     => 'fa-clipboard-check',
                        'gradient' => 'from-purple-600 to-indigo-600',
                        'badge'    => 'bg-purple-50 text-purple-700 border-purple-200/80',
                    ],
                    'seguridad' => [
                        'label'    => 'Seguridad',
                        'icon'     => 'fa-shield-halved',
                        'gradient' => 'from-amber-500 to-orange-600',
                        'badge'    => 'bg-amber-50 text-amber-800 border-amber-200/80',
                    ],
                    'administracion' => [
                        'label'    => 'Administración',
                        'icon'     => 'fa-gear',
                        'gradient' => 'from-slate-600 to-slate-800',
                        'badge'    => 'bg-slate-100 text-slate-700 border-slate-200',
                    ],
                ][$event->category] ?? [
                    'label'    => 'Información',
                    'icon'     => 'fa-circle-info',
                    'gradient' => 'from-blue-600 to-slate-700',
                    'badge'    => 'bg-slate-100 text-slate-700 border-slate-200',
                ];

                $priorityConfig = [
                    'informativa' => ['label' => 'Informativa', 'badge' => 'bg-slate-100 text-slate-700 border-slate-200'],
                    'importante'  => ['label' => 'Importante',  'badge' => 'bg-blue-50 text-blue-700 border-blue-200'],
                    'alta'        => ['label' => 'Alta',        'badge' => 'bg-amber-50 text-amber-800 border-amber-200'],
                    'critica'     => ['label' => 'Crítica',     'badge' => 'bg-red-50 text-red-700 border-red-200'],
                ][$event->priority] ?? ['label' => ucfirst($event->priority), 'badge' => 'bg-slate-100 text-slate-700 border-slate-200'];
            @endphp
            <article aria-labelledby="notification-title-{{ $item->id }}"
                class="group grid items-center gap-5 rounded-2xl border {{ $item->read_at ? 'border-slate-200/80 bg-white' : 'border-blue-200 bg-blue-50/20' }} p-5 shadow-sm transition-all hover:shadow-md hover:border-blue-300 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)_auto]">
                
                {{-- Columna 1: Icono, Categoría, Título y Descripción --}}
                <div class="flex min-w-0 items-start gap-4">
                    <div class="flex h-13 w-13 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $categoryConfig['gradient'] }} text-xl text-white shadow-md shadow-blue-600/15 group-hover:scale-105 transition-transform">
                        <i class="fas {{ $categoryConfig['icon'] }}" aria-hidden="true"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="mb-1.5 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wide {{ $categoryConfig['badge'] }}">
                                <i class="fas {{ $categoryConfig['icon'] }} text-[9px]" aria-hidden="true"></i>
                                {{ $categoryConfig['label'] }}
                            </span>
                            @if($item->archived_at)
                                <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500">
                                    <i class="fas fa-box-archive text-[9px]" aria-hidden="true"></i> Archivada
                                </span>
                            @endif
                        </div>
                        <h2 id="notification-title-{{ $item->id }}" class="break-words text-base font-extrabold leading-snug text-slate-900 group-hover:text-blue-600 transition-colors">
                            <a href="{{ route('notifications.open', $item->id) }}" class="focus:outline-none focus:underline">
                                {{ $event->title }}
                            </a>
                        </h2>
                        <p class="mt-1.5 break-words text-xs leading-relaxed text-slate-600">{{ $event->message }}</p>
                    </div>
                </div>

                {{-- Columna 2: Ficha Técnica (Prioridad, Lectura, Fecha) --}}
                <div class="grid min-w-0 grid-cols-2 gap-3.5 rounded-2xl border border-slate-100 p-3.5 sm:grid-cols-3" style="background-color:var(--bg-page, #f8fafc)">
                    <div class="min-w-0">
                        <p class="mb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Prioridad</p>
                        <span class="inline-flex items-center rounded-lg border px-2 py-0.5 text-xs font-bold {{ $priorityConfig['badge'] }}">
                            {{ $priorityConfig['label'] }}
                        </span>
                    </div>
                    <div class="min-w-0">
                        <p class="mb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Lectura</p>
                        <span class="inline-flex items-center gap-1.5 rounded-lg border px-2 py-0.5 text-xs font-bold {{ $item->read_at ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-blue-200 bg-blue-50 text-blue-700' }}">
                            <i class="fas {{ $item->read_at ? 'fa-circle-check text-emerald-500' : 'fa-circle text-blue-500' }} text-[9px]" aria-hidden="true"></i>
                            {{ $item->read_at ? 'Leída' : 'No leída' }}
                        </span>
                    </div>
                    <div class="col-span-2 min-w-0 sm:col-span-1">
                        <p class="mb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Fecha y Hora</p>
                        <time datetime="{{ $event->created_at->toIso8601String() }}" class="block text-xs font-bold text-slate-700">
                            {{ $event->created_at->format('d/m/Y') }}
                            <span class="mt-0.5 flex items-center gap-1 text-[10px] font-medium text-slate-500">
                                <i class="far fa-clock text-[9px]" aria-hidden="true"></i> {{ $event->created_at->format('H:i') }}
                            </span>
                        </time>
                    </div>
                </div>

                {{-- Columna 3: Botones y Acciones --}}
                <div class="flex flex-wrap items-center gap-2.5 lg:flex-col lg:items-stretch lg:min-w-[145px]">
                    <a href="{{ route('notifications.open', $item->id) }}" aria-label="Ver detalle: {{ $event->title }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-blue-600 hover:text-white hover:border-blue-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 active:scale-95">
                        <i class="fas fa-list-ul text-xs" aria-hidden="true"></i>
                        <span>Ver detalle</span>
                        <i class="fas fa-chevron-right text-[9px]" aria-hidden="true"></i>
                    </a>
                    @unless($item->read_at)
                        <form method="POST" action="{{ route('notifications.read', $item->id) }}">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl border border-transparent px-2.5 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-50 transition active:scale-95">
                                <i class="fas fa-check text-xs" aria-hidden="true"></i>
                                <span>Marcar leída</span>
                            </button>
                        </form>
                    @endunless
                    @unless($item->archived_at)
                        <form method="POST" action="{{ route('notifications.archive', $item->id) }}">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl border border-transparent px-2.5 py-1.5 text-xs font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition active:scale-95">
                                <i class="fas fa-box-archive text-xs" aria-hidden="true"></i>
                                <span>Archivar</span>
                            </button>
                        </form>
                    @endunless
                </div>
            </article>
        @empty
            <div class="text-center p-14 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 text-2xl mb-3">
                    <i class="fas fa-bell-slash" aria-hidden="true"></i>
                </div>
                <h3 class="text-base font-extrabold text-slate-800">No hay notificaciones para estos filtros</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Prueba cambiando los criterios de búsqueda, estado o categoría para encontrar otras alertas.</p>
                @if($hasFilters)
                    <a href="{{ route('notifications.index') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-blue-50 text-blue-700 text-xs font-bold rounded-xl hover:bg-blue-100 transition">
                        <i class="fas fa-rotate-left text-xs" aria-hidden="true"></i> Restablecer filtros
                    </a>
                @endif
            </div>
        @endforelse
        </div>

        <div class="mt-6">{{ $notifications->links() }}</div>
    </main>

    {{-- Script para interactividad fluida de los Custom Dropdowns --}}
    <script>
    (() => {
        function initCustomSelects() {
            const dropdowns = document.querySelectorAll('[data-custom-select]');
            if (!dropdowns.length) return;
            
            function closeAll() {
                dropdowns.forEach(dd => {
                    const menu = dd.querySelector('[data-menu]');
                    const arrow = dd.querySelector('[data-arrow]');
                    const toggle = dd.querySelector('[data-toggle]');
                    if (menu) menu.classList.add('hidden');
                    if (arrow) arrow.classList.remove('rotate-180');
                    if (toggle) toggle.setAttribute('aria-expanded', 'false');
                });
            }

            dropdowns.forEach(dd => {
                const toggle = dd.querySelector('[data-toggle]');
                const menu = dd.querySelector('[data-menu]');
                const arrow = dd.querySelector('[data-arrow]');
                const input = dd.querySelector('input[type="hidden"]');
                const label = dd.querySelector('[data-label]');
                const icon = dd.querySelector('[data-icon]');
                const options = dd.querySelectorAll('[data-option]');

                if (!toggle || !menu) return;

                toggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isHidden = menu.classList.contains('hidden');
                    closeAll();
                    if (isHidden) {
                        menu.classList.remove('hidden');
                        if (arrow) arrow.classList.add('rotate-180');
                        toggle.setAttribute('aria-expanded', 'true');
                    }
                });

                options.forEach(opt => {
                    opt.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const val = opt.dataset.value;
                        const text = opt.dataset.label;
                        const iconClass = opt.dataset.icon;

                        if (input) input.value = val;
                        if (label) label.textContent = text;
                        if (icon && iconClass) {
                            icon.className = iconClass;
                        }

                        options.forEach(o => {
                            const check = o.querySelector('[data-check]');
                            if (o === opt) {
                                o.classList.add('bg-blue-50', 'text-blue-700');
                                o.classList.remove('text-slate-700');
                                if (check) check.classList.remove('hidden');
                            } else {
                                o.classList.remove('bg-blue-50', 'text-blue-700');
                                o.classList.add('text-slate-700');
                                if (check) check.classList.add('hidden');
                            }
                        });

                        closeAll();
                    });
                });
            });

            document.addEventListener('click', () => closeAll());
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeAll();
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCustomSelects);
        } else {
            initCustomSelects();
        }
    })();
    </script>
</x-app>
