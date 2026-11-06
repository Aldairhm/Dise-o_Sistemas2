@props([
    'fields' => 3
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border shadow-sm p-6 select-none bg-white space-y-5 animate-pulse']) }} role="status" aria-label="Cargando formulario...">
    <!-- Header simulado -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
        <div class="w-8 h-8 rounded-lg ax-skeleton-shimmer shrink-0"></div>
        <div class="space-y-1.5 flex-1">
            <div class="h-4 w-36 rounded-md ax-skeleton-shimmer"></div>
            <div class="h-3 w-48 rounded-md ax-skeleton-shimmer"></div>
        </div>
    </div>

    <!-- Campos simulados -->
    <div class="space-y-4 pt-1">
        @for($i = 0; $i < $fields; $i++)
            <div class="space-y-2">
                <div class="h-3.5 w-32 rounded ax-skeleton-shimmer"></div>
                <div class="h-10 w-full rounded-xl ax-skeleton-shimmer"></div>
            </div>
        @endfor
    </div>

    <!-- Botón simulado -->
    <div class="flex justify-end pt-3">
        <div class="h-10 w-44 rounded-xl ax-skeleton-shimmer"></div>
    </div>
</div>

