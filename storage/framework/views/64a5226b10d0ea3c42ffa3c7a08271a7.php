<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'fields' => 3
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
    'fields' => 3
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->merge(['class' => 'rounded-2xl border shadow-sm p-6 select-none bg-white space-y-5 animate-pulse'])); ?> role="status" aria-label="Cargando formulario...">
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
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < $fields; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="space-y-2">
                <div class="h-3.5 w-32 rounded ax-skeleton-shimmer"></div>
                <div class="h-10 w-full rounded-xl ax-skeleton-shimmer"></div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    <!-- Botón simulado -->
    <div class="flex justify-end pt-3">
        <div class="h-10 w-44 rounded-xl ax-skeleton-shimmer"></div>
    </div>
</div>

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/components/skeleton-form.blade.php ENDPATH**/ ?>