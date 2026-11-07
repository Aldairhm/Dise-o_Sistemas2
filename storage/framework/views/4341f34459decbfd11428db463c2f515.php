<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'bonos' => collect(),
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
    'bonos' => collect(),
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bonos->isNotEmpty()): ?>
<section class="space-y-3">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-700">Bonos e Incentivos</h2>
            <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-bold text-[11px] border border-indigo-100">
                <?php echo e($bonos->count()); ?>

            </span>
        </div>
        <span class="text-xs text-slate-400">Sumados a tu Total Ganado</span>
    </div>

    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden">
        <div class="divide-y divide-slate-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $bonos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bono): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $esPagado = $bono['estado'] === 'pagado';
            ?>
            <div class="p-4 sm:px-6 sm:py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 transition-colors">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0 mt-0.5 sm:mt-0">
                        <i class="fas fa-gift text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900 leading-snug">
                            <?php echo e($bono['motivo']); ?>

                        </p>
                        <p class="text-[11px] text-slate-400">
                            Registrado el <?php echo e($bono['fecha']); ?>

                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between sm:justify-end gap-4 pl-12 sm:pl-0">
                    <span class="text-sm font-black text-indigo-700 tracking-tight">
                        +$<?php echo e(number_format($bono['monto'], 2)); ?>

                    </span>
                    <?php if (isset($component)) { $__componentOriginal16e00b87e9c221a18173feb93119d17a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal16e00b87e9c221a18173feb93119d17a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.vendedor.status-badge','data' => ['type' => 'comision','status' => $esPagado ? 'pagada' : 'pendiente_cobro','label' => $esPagado ? 'Cobrado' : 'Pendiente']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('vendedor.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'comision','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($esPagado ? 'pagada' : 'pendiente_cobro'),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($esPagado ? 'Cobrado' : 'Pendiente')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal16e00b87e9c221a18173feb93119d17a)): ?>
<?php $attributes = $__attributesOriginal16e00b87e9c221a18173feb93119d17a; ?>
<?php unset($__attributesOriginal16e00b87e9c221a18173feb93119d17a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal16e00b87e9c221a18173feb93119d17a)): ?>
<?php $component = $__componentOriginal16e00b87e9c221a18173feb93119d17a; ?>
<?php unset($__componentOriginal16e00b87e9c221a18173feb93119d17a); ?>
<?php endif; ?>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/components/vendedor/bonus-list.blade.php ENDPATH**/ ?>