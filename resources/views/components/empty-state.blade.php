@props([
    'title' => 'No se encontraron resultados',
    'description' => 'Intenta ajustar los filtros o el término de búsqueda.',
    'icon' => 'folder-open'
])

<div {{ $attributes->merge(['class' => 'py-12 px-4 text-center select-none']) }} role="status">
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-slate-100/80 text-slate-400 mb-4 shadow-inner">
        @if($icon === 'search')
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        @else
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
        @endif
    </div>
    <h3 class="text-base font-semibold text-slate-700 mb-1">{{ $title }}</h3>
    <p class="text-xs text-slate-400 max-w-sm mx-auto">{{ $description }}</p>
    
    @if(isset($action))
        <div class="mt-4">
            {{ $action }}
        </div>
    @endif
</div>

