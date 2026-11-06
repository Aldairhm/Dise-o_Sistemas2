@props([
    'loading' => false,
    'loadingText' => 'Procesando...',
    'variant' => 'primary', // primary, success, danger, secondary
    'type' => 'submit'
])

@php
    $baseStyles = "relative inline-flex items-center justify-center font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed";
    
    $variants = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500 shadow-sm shadow-blue-500/20 active:scale-[0.98]',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white focus:ring-emerald-500 shadow-sm shadow-emerald-500/20 active:scale-[0.98]',
        'danger' => 'bg-rose-600 hover:bg-rose-700 text-white focus:ring-rose-500 shadow-sm shadow-rose-500/20 active:scale-[0.98]',
        'secondary' => 'bg-slate-100 hover:bg-slate-200 text-slate-700 focus:ring-slate-400 active:scale-[0.98]',
    ];

    $classes = $baseStyles . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => $classes]) }}
    :disabled="{{ $attributes->has('x-bind:disabled') ? $attributes->get('x-bind:disabled') : ($loading ? 'true' : 'false') }}"
>
    <!-- Contenido normal / Slot -->
    <span class="inline-flex items-center gap-2" :class="{ 'invisible': {{ $attributes->has('x-show-loading') ? $attributes->get('x-show-loading') : ($loading ? 'true' : 'false') }} }">
        {{ $slot }}
    </span>

    <!-- Spinner localizado y texto de carga centrado para prevenir layout shift -->
    <span 
        class="absolute inset-0 flex items-center justify-center gap-2"
        style="display: none;"
        @if($attributes->has('x-show-loading'))
            x-show="{{ $attributes->get('x-show-loading') }}"
        @elseif($loading)
            x-show="true"
        @endif
    >
        <svg class="animate-spin h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-xs font-semibold">{{ $loadingText }}</span>
    </span>
</button>

