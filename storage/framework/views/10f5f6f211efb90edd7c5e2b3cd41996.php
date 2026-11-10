<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'loading' => false,
    'loadingText' => 'Procesando...',
    'variant' => 'primary', // primary, success, danger, secondary
    'type' => 'submit'
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'loading' => false,
    'loadingText' => 'Procesando...',
    'variant' => 'primary', // primary, success, danger, secondary
    'type' => 'submit'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<button
    type="<?php echo e($type); ?>"
    <?php echo e($buttonAttributes->merge(['class' => $classes])); ?>

    <?php if($loadingExpression): ?>
        x-bind:disabled="(<?php echo e($loadingExpression); ?>) || (<?php echo e($disabledExpression); ?>)"
        x-bind:aria-busy="(<?php echo e($loadingExpression); ?>) ? 'true' : 'false'"
    <?php else: ?>
        <?php if($loading): echo 'disabled'; endif; ?>
        aria-busy="<?php echo e($loading ? 'true' : 'false'); ?>"
        <?php if($disabledExpression !== 'false'): ?> x-bind:disabled="<?php echo e($disabledExpression); ?>" <?php endif; ?>
    <?php endif; ?>
>
    <span class="inline-flex items-center gap-2 <?php echo e(!$loadingExpression && $loading ? 'invisible' : ''); ?>"
        <?php if($loadingExpression): ?> x-bind:class="{ 'invisible': <?php echo e($loadingExpression); ?> }" <?php endif; ?>>
        <?php echo e($slot); ?>

    </span>

    <span class="absolute inset-0 <?php echo e(!$loadingExpression && !$loading ? 'hidden' : 'flex'); ?> items-center justify-center gap-2"
        <?php if($loadingExpression): ?> x-cloak x-show="<?php echo e($loadingExpression); ?>" <?php endif; ?>
        role="status">
        <svg class="ax-spinner h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-xs font-semibold"><?php echo e($loadingText); ?></span>
    </span>
</button>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/components/loading-button.blade.php ENDPATH**/ ?>