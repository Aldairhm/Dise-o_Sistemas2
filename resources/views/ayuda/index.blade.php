<x-app title="Centro de Ayuda | AXStore">
    <section class="rounded-2xl border border-blue-200 bg-blue-50 p-6 text-center text-slate-900 shadow-sm sm:p-10">
        <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Soporte AXStore ERP</p>
        <h1 class="mt-2 text-3xl font-black">Centro de Ayuda</h1>
        <p class="mt-2 text-sm text-slate-600">Consulta los pasos para realizar las tareas de tu cuenta.</p>
        <form action="{{ route('ayuda.index') }}" method="GET" role="search" class="mx-auto mt-6 flex max-w-2xl flex-wrap gap-2">
            <label for="ayuda-busqueda" class="sr-only">Buscar ayuda</label>
            <input id="ayuda-busqueda" name="q" type="search" maxlength="120" value="{{ $query }}"
                   placeholder="Ejemplo: ventas o existencias" required
                   class="min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900">
            <button class="rounded-xl bg-blue-700 px-5 py-3 text-sm font-bold text-white hover:bg-blue-800">Buscar</button>
        </form>
        <div class="mt-5 flex flex-wrap justify-center gap-2">
            @foreach ($guides as $key => $item)
                <a href="{{ route('ayuda.index', ['tema' => $key]) }}"
                   class="rounded-full border border-blue-200 bg-white px-3 py-2 text-sm font-semibold text-blue-800 hover:underline">
                    {{ $item['question'] }}
                </a>
            @endforeach
        </div>
    </section>

    <div class="mt-6 grid items-start gap-6 md:grid-cols-3">
        <nav aria-label="Temas de ayuda" class="rounded-2xl border border-slate-200 bg-white p-4 text-slate-900 shadow-sm">
            <h2 class="px-3 pb-2 font-bold">Temas de ayuda</h2>
            @foreach ($guides as $key => $item)
                <a href="{{ route('ayuda.index', ['tema' => $key]) }}"
                   @if ($topic === $key) aria-current="page" @endif
                   class="block rounded-lg px-3 py-3 text-sm font-semibold hover:bg-blue-50 {{ $topic === $key ? 'bg-blue-50 text-blue-800' : 'text-slate-700' }}">
                    {{ $item['title'] }}
                </a>
            @endforeach
        </nav>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-900 shadow-sm md:col-span-2">
            @if ($guide)
                <a href="{{ route('ayuda.index') }}" class="text-sm font-semibold text-blue-700 hover:underline">Volver al inicio de ayuda</a>
                <h2 class="mt-4 text-2xl font-bold">{{ $guide['title'] }}</h2>
                <ol class="mt-5 space-y-3" aria-label="Paso a paso">
                    @foreach ($guide['steps'] as $step)
                        <li class="flex items-start gap-4 rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <span aria-hidden="true" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-700 font-bold text-white">{{ $loop->iteration }}</span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-blue-700">Paso {{ $loop->iteration }} de {{ $loop->count }}</p>
                                <p class="mt-1 text-base leading-relaxed" style="color: #1e293b">{{ $step }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
                <a href="{{ route($guide['route']) }}" class="mt-5 inline-block rounded-xl bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800">Abrir módulo</a>
            @elseif ($query !== '')
                <h2 class="text-xl font-bold">Resultados para «{{ $query }}»</h2>
                @forelse ($results as $key => $item)
                    <article class="mt-4 border-t border-slate-200 pt-4">
                        <h3 class="font-bold">{{ $item['title'] }}</h3>
                        <p class="mt-1 text-sm text-slate-700">{{ $item['steps'][0] }}</p>
                        <a href="{{ route('ayuda.index', ['tema' => $key]) }}" class="mt-2 inline-block text-sm font-semibold text-blue-700 hover:underline">Ver guía completa</a>
                    </article>
                @empty
                    <p class="mt-3 text-sm text-slate-700">No encontramos una guía para esa búsqueda. Prueba con el nombre de un módulo o elige un tema del menú.</p>
                @endforelse
                <a href="{{ route('ayuda.index') }}" class="mt-5 inline-block text-sm font-semibold text-blue-700 hover:underline">Limpiar búsqueda</a>
            @else
                <h2 class="text-xl font-bold">¿Qué necesitas hacer?</h2>
                <p class="mt-2 text-sm text-slate-700">Elige un tema para ver los pasos y el acceso directo al módulo.</p>
            @endif
        </section>
    </div>
</x-app>





