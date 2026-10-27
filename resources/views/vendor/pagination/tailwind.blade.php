@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Navegación de páginas') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3 w-full">
        <div class="text-xs text-slate-500 font-medium">
            @if ($paginator->firstItem())
                Mostrando <b class="text-slate-800 font-bold">{{ $paginator->firstItem() }}</b> a <b class="text-slate-800 font-bold">{{ $paginator->lastItem() }}</b> de <b class="text-slate-800 font-bold">{{ $paginator->total() }}</b> registros
            @else
                Mostrando <b class="text-slate-800 font-bold">{{ $paginator->count() }}</b> registros
            @endif
        </div>

        <div class="flex items-center gap-1.5">
            {{-- Botón Anterior --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 text-slate-300 text-xs cursor-not-allowed select-none">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 text-xs transition-colors cursor-pointer shadow-sm">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            {{-- Elementos de Paginación --}}
            @foreach ($elements as $element)
                {{-- Separador de puntos --}}
                @if (is_string($element))
                    <span aria-disabled="true" class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate-400 select-none">
                        {{ $element }}
                    </span>
                @endif

                {{-- Enlaces de Páginas --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-600 text-white text-xs font-black shadow-md shadow-blue-500/25">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 text-xs font-bold transition-colors cursor-pointer">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Botón Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 text-xs transition-colors cursor-pointer shadow-sm">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span aria-disabled="true" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 text-slate-300 text-xs cursor-not-allowed select-none">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
