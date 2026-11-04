<x-app title="Centro de Ayuda | AXStore">
    <style>@media (min-width: 768px) { .ayuda-modulos { grid-template-columns: minmax(235px, 285px) minmax(0, 1fr); } }</style>
    <div class="mx-auto max-w-6xl space-y-6">
        <header class="space-y-2">
            <p class="text-sm font-bold uppercase tracking-wide">AXStore · Ayuda</p>
            <h1 class="text-3xl font-black">¿Qué necesitas hacer?</h1>
            <p>Busca una tarea o elige una guía.</p>
        </header>
        <form action="{{ route('ayuda.index') }}" method="GET" role="search" class="flex flex-wrap gap-2">
            <label for="ayuda-busqueda" class="sr-only">Buscar una tarea</label>
            <input id="ayuda-busqueda" name="q" type="search" maxlength="120" value="{{ $query }}"
                   placeholder="Ejemplo: venta, stock o devolución" required
                   class="min-w-0 flex-1 rounded-lg border px-4 py-3 text-sm">
            <button class="rounded-lg bg-blue-700 px-5 py-3 text-sm font-bold text-white">Buscar</button>
        </form>

        <div class="grid items-start gap-5 ayuda-modulos">
            <nav aria-label="Módulos de ayuda" class="rounded-xl border p-3" style="background:#fff;color:#172334">
                <h2 class="px-3 pb-2 text-sm font-bold uppercase tracking-wide">Módulos</h2>
                <ul class="space-y-1">
                    @foreach ($guides as $key => $item)
                        <li>
                            <a href="{{ route('ayuda.index', ['tema' => $key]) }}"
                               @if ($topic === $key) aria-current="page" @endif
                               class="flex items-center gap-3 rounded-lg px-3 py-2.5 hover:underline"
                               style="color:#172334;background:{{ $topic === $key ? '#e9f1ff' : 'transparent' }}">
                                <span aria-hidden="true" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                                      style="background:{{ $item['icon_bg'] }};color:{{ $item['icon_fg'] }}">
                                    <i class="fas {{ $item['icon'] }}"></i>
                                </span>
                                <span>
                                    <span class="block text-sm font-bold">{{ $item['module'] }}</span>
                                    <span class="block text-xs" style="color:#475569">{{ $item['description'] }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
            <div>
        @if ($guide)
            <article class="rounded-xl border p-6" style="background:#f8fafc;color:#172334">
                <a href="{{ route('ayuda.index') }}" class="text-sm font-semibold underline" style="color:#1745a3">Volver a las tareas</a>
                <p class="mt-5 text-sm font-bold">{{ $guide['module'] }}</p>
                <h2 class="mt-1 text-2xl font-bold">{{ $topic === 'venta' ? 'Ventas en tienda y por envío' : $guide['title'] }}</h2>
                @php
                    $cases = $guide['cases'];
                    if (in_array($topic, ['inventario', 'venta'], true)) {
                        $cases = [$guide['title'] => $guide] + $cases;
                    }
                @endphp
                @if ($topic === 'devolucion')
                    <p class="mt-3">Antes de empezar, verifica que la venta esté entregada y dentro del plazo de garantía.</p>
                @endif
                @if ($cases)
                    <div class="mt-5 space-y-3">
                        @foreach ($cases as $caseTitle => $case)
                            <details class="rounded-lg border p-4">
                                <summary class="cursor-pointer font-bold">{{ $caseTitle }}</summary>
                <ol class="mt-5 list-decimal space-y-3 pl-6 leading-relaxed">
                    @foreach ($case['steps'] as $step)
                        <li class="pl-1">{{ $step }}</li>
                    @endforeach
                </ol>
                <div class="mt-6 rounded-lg border-l-4 p-4" style="background:#e6f5ef;border-color:#197354">
                    <h3 class="font-bold">Comprueba el resultado</h3>
                    <p class="mt-1">{{ $case['check'] }}</p>
                </div>
                            </details>
                        @endforeach
                    </div>
                @else
                    @php($case = $guide)
                <ol class="mt-5 list-decimal space-y-3 pl-6 leading-relaxed">
                    @foreach ($case['steps'] as $step)
                        <li class="pl-1">{{ $step }}</li>
                    @endforeach
                </ol>
                <div class="mt-6 rounded-lg border-l-4 p-4" style="background:#e6f5ef;border-color:#197354">
                    <h3 class="font-bold">Comprueba el resultado</h3>
                    <p class="mt-1">{{ $case['check'] }}</p>
                </div>
                @endif
                <a href="{{ route($guide['route']) }}" class="mt-5 inline-block rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">Abrir módulo</a>
            </article>
        @elseif ($query === '')
            <section class="rounded-xl border p-6" style="background:#f8fafc;color:#172334">
                <h2 class="text-2xl font-bold">Selecciona un módulo</h2>
                <p class="mt-2">Elige un módulo de la lista para consultar su guía paso a paso.</p>
            </section>
        @else
            <section class="space-y-3" aria-label="Tareas">
                @if ($query !== '')
                    <h2 class="text-lg font-bold">Resultados para «{{ $query }}»</h2>
                @endif
                @forelse ($query === '' ? $guides : $results as $key => $item)
                    <a href="{{ route('ayuda.index', ['tema' => $key]) }}"
                       class="block rounded-xl border p-4 hover:underline" style="background:#f8fafc;color:#172334">
                        <span class="block text-xs font-bold uppercase tracking-wide" style="color:#1745a3">{{ $item['module'] }}</span>
                        <span class="mt-1 block text-lg font-bold">{{ $item['title'] }} <span aria-hidden="true">→</span></span>
                    </a>
                @empty
                    <p>No encontramos una guía. Prueba con el nombre de una tarea.</p>
                @endforelse
                @if ($query !== '')
                    <a href="{{ route('ayuda.index') }}" class="inline-block font-semibold underline">Ver todas las tareas</a>
                @endif
            </section>
        @endif
            </div>
        </div>
    </div>
</x-app>






