<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'line', // 'line' o 'bar'
    'height' => 'h-[280px]',
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
    'type' => 'line', // 'line' o 'bar'
    'height' => 'h-[280px]',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->merge(['class' => 'w-full ' . $height . ' flex flex-col justify-between p-4 bg-slate-50/90 rounded-2xl select-none relative overflow-hidden'])); ?> role="status" aria-label="Cargando gráfico...">
    <!-- Eje Y y Guías horizontales de fondo del gráfico -->
    <div class="w-full flex-1 flex flex-col justify-between py-2 border-b-2 border-slate-200">
        <div class="w-full border-b border-dashed border-slate-200"></div>
        <div class="w-full border-b border-dashed border-slate-200"></div>
        <div class="w-full border-b border-dashed border-slate-200"></div>
        <div class="w-full border-b border-dashed border-slate-200"></div>
    </div>

    <!-- Simulación visual de barras o silueta lineal -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'bar'): ?>
        <div class="absolute inset-x-6 bottom-9 top-8 flex items-end justify-between gap-2 sm:gap-4 px-2">
            <?php
                $alturas = [45, 75, 30, 90, 60, 85, 40, 70, 55, 95, 65, 80];
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $alturas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="flex-1 flex items-end justify-center gap-1.5 h-full">
                    <div class="w-full max-w-[14px] bg-slate-300 rounded-t-md ax-skeleton-shimmer shadow-xs" style="height: <?php echo e($h); ?>%;"></div>
                    <div class="w-full max-w-[14px] bg-indigo-200/80 rounded-t-md ax-skeleton-shimmer shadow-xs" style="height: <?php echo e(max(20, $h - 25)); ?>%;"></div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Simulación de gráfico de líneas con gradiente de shimmer fluido y puntos -->
        <div class="absolute inset-x-6 bottom-9 top-8 flex items-end">
            <div class="w-full h-full flex items-end justify-between gap-1.5">
                <?php
                    $puntos = [25, 45, 35, 65, 55, 80, 65, 90, 70, 95, 80, 60];
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $puntos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="flex-1 flex flex-col items-center justify-end h-full">
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-500 mb-1 ax-skeleton-shimmer shadow-xs"></div>
                        <div class="w-full bg-gradient-to-t from-blue-200/60 via-blue-100/30 to-transparent rounded-t-md ax-skeleton-shimmer" style="height: <?php echo e($p); ?>%;"></div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Eje X con etiquetas simuladas -->
    <div class="flex items-center justify-between pt-2.5 px-2 text-[10px] text-slate-400">
        <div class="h-2.5 w-9 rounded-md bg-slate-300 ax-skeleton-shimmer"></div>
        <div class="h-2.5 w-9 rounded-md bg-slate-300 ax-skeleton-shimmer"></div>
        <div class="h-2.5 w-9 rounded-md bg-slate-300 ax-skeleton-shimmer"></div>
        <div class="h-2.5 w-9 rounded-md bg-slate-300 ax-skeleton-shimmer"></div>
        <div class="h-2.5 w-9 rounded-md bg-slate-300 ax-skeleton-shimmer"></div>
        <div class="h-2.5 w-9 rounded-md bg-slate-300 ax-skeleton-shimmer"></div>
    </div>
</div>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/components/skeleton-chart.blade.php ENDPATH**/ ?>