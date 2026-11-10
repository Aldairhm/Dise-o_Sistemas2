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
    $loadingExpression = $attributes->get('x-show-loading');
    $disabledExpression = $attributes->get('x-bind:disabled', $attributes->get(':disabled', 'false'));
    $buttonAttributes = $attributes->except(['x-show-loading', 'x-bind:disabled', ':disabled']);
@endphp

<button
    type="{{ $type }}"
    {{ $buttonAttributes->merge(['class' => $classes]) }}
    @if($loadingExpression)
        x-bind:disabled="({{ $loadingExpression }}) || ({{ $disabledExpression }})"
        x-bind:aria-busy="({{ $loadingExpression }}) ? 'true' : 'false'"
    @else
        @disabled($loading)
        aria-busy="{{ $loading ? 'true' : 'false' }}"
        @if($disabledExpression !== 'false') x-bind:disabled="{{ $disabledExpression }}" @endif
    @endif
>
    <span class="inline-flex items-center gap-2 {{ !$loadingExpression && $loading ? 'invisible' : '' }}"
        @if($loadingExpression) x-bind:class="{ 'invisible': {{ $loadingExpression }} }" @endif>
        {{ $slot }}
    </span>

    <span class="absolute inset-0 {{ !$loadingExpression && !$loading ? 'hidden' : 'flex' }} items-center justify-center gap-2"
        @if($loadingExpression) x-cloak x-show="{{ $loadingExpression }}" @endif
        role="status">
        <svg class="ax-spinner h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-xs font-semibold">{{ $loadingText }}</span>
    </span>
</button>
