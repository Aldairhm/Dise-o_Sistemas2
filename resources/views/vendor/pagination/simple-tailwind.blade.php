@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Navegación de páginas') }}" class="flex items-center justify-between w-full">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-300 text-xs font-semibold cursor-not-allowed select-none">
                <i class="fas fa-chevron-left text-[10px]"></i> Anterior
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 text-xs font-semibold transition-colors cursor-pointer shadow-sm">
                <i class="fas fa-chevron-left text-[10px]"></i> Anterior
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 text-xs font-semibold transition-colors cursor-pointer shadow-sm">
                Siguiente <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-300 text-xs font-semibold cursor-not-allowed select-none">
                Siguiente <i class="fas fa-chevron-right text-[10px]"></i>
            </span>
        @endif
    </nav>
@endif
