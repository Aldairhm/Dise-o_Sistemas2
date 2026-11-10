@props([
    'lines' => 3
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-100 p-5 shadow-sm space-y-4 animate-pulse select-none']) }} role="status">
    <div class="flex items-center justify-between">
        <div class="h-4 w-28 rounded-md ax-skeleton-shimmer"></div>
        <div class="h-8 w-8 rounded-lg ax-skeleton-shimmer"></div>
    </div>
    
    <div class="space-y-2">
        <div class="h-7 w-36 rounded-md ax-skeleton-shimmer"></div>
        @for($i = 0; $i < $lines; $i++)
            <div class="h-3 rounded ax-skeleton-shimmer" style="width: {{ max(25, 100 - ($i * 15)) }}%"></div>
        @endfor
    </div>
    <span class="sr-only">Cargando tarjeta...</span>
</div>

