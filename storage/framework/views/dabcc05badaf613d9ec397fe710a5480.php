<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'resumen' => [],
    'metaMensual' => 500.0,
    'progresoMeta' => 0.0,
    'variacion' => 0.0,
    'periodoActual' => 'mes',
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
    'resumen' => [],
    'metaMensual' => 500.0,
    'progresoMeta' => 0.0,
    'variacion' => 0.0,
    'periodoActual' => 'mes',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $totalGanado = (float) ($resumen['total_ganado'] ?? 0);
    $cobrado = (float) ($resumen['cobrado'] ?? 0);
    $pendiente = (float) ($resumen['pendiente_cobro'] ?? 0);
    $proyectada = (float) ($resumen['proyectada'] ?? 0);
    $anulada = (float) ($resumen['anulada'] ?? 0);
?>

<div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-indigo-700 p-6 sm:p-8 text-white shadow-xl shadow-indigo-600/10">
    
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(255,255,255,0.12)_0%,transparent_65%)]"></div>

    <div class="relative flex flex-col lg:flex-row lg:items-end justify-between gap-8">
        
        <div class="flex-1 space-y-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/70">Total Ganado</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/10 text-[10px] font-semibold text-white/90 backdrop-blur-sm">
                        Comisiones + Bonos
                    </span>
                </div>
                <div class="flex items-baseline gap-3 mt-1 flex-wrap">
                    <p class="text-4xl sm:text-5xl font-black tracking-tight leading-none">
                        $<span x-text="formatMoney(summary.total_ganado)"><?php echo e(number_format($totalGanado, 2)); ?></span>
                    </p>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodoActual !== 'todo' && $variacion != 0): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-bold <?php echo e($variacion > 0 ? 'bg-emerald-400/20 text-emerald-200' : 'bg-rose-400/20 text-rose-200'); ?>">
                        <i class="fas <?php echo e($variacion > 0 ? 'fa-arrow-up' : 'fa-arrow-down'); ?> text-[10px]"></i>
                        <?php echo e(abs($variacion)); ?>% vs <?php echo e($periodoActual === 'hoy' ? 'ayer' : ($periodoActual === 'semana' ? 'semana anterior' : 'mes anterior')); ?>

                    </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                
                <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-3 border border-white/10">
                    <span class="block text-[11px] font-semibold text-white/70">Cobrado</span>
                    <span class="text-lg font-black text-emerald-300 tracking-tight">
                        $<span x-text="formatMoney(summary.cobrado)"><?php echo e(number_format($cobrado, 2)); ?></span>
                    </span>
                    <span class="block text-[10px] text-white/50">Ya liquidado</span>
                </div>

                
                <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-3 border border-white/10">
                    <span class="block text-[11px] font-semibold text-white/70">Pendiente</span>
                    <span class="text-lg font-black text-amber-300 tracking-tight">
                        $<span x-text="formatMoney(summary.pendiente_cobro)"><?php echo e(number_format($pendiente, 2)); ?></span>
                    </span>
                    <span class="block text-[10px] text-white/50">Por liquidar</span>
                </div>

                
                <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-3 border border-white/10">
                    <span class="block text-[11px] font-semibold text-white/70">Por ganar</span>
                    <span class="text-lg font-black text-white/90 tracking-tight">
                        $<span x-text="formatMoney(summary.proyectada)"><?php echo e(number_format($proyectada, 2)); ?></span>
                    </span>
                    <span class="block text-[10px] text-white/50">En proceso</span>
                </div>

                
                <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-3 border border-white/10">
                    <span class="block text-[11px] font-semibold text-white/70">Anulada</span>
                    <span class="text-lg font-black text-rose-300 tracking-tight">
                        $<span x-text="formatMoney(summary.anulada)"><?php echo e(number_format($anulada, 2)); ?></span>
                    </span>
                    <span class="block text-[10px] text-white/50">Cancelada / devuelta</span>
                </div>
            </div>
        </div>

        
        <div class="lg:w-72 flex-shrink-0 rounded-2xl bg-white/10 backdrop-blur-sm p-4 sm:p-5 border border-white/15">
            <div class="flex items-center justify-between text-xs font-bold text-white/80 mb-2">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-bullseye text-[12px] text-white/70"></i> Meta mensual
                </span>
                <span>
                    $<span x-text="formatMoney(summary.total_ganado)"><?php echo e(number_format($totalGanado, 2)); ?></span> / $<?php echo e(number_format($metaMensual, 2)); ?>

                </span>
            </div>

            <div class="w-full h-3 rounded-full bg-black/20 overflow-hidden p-0.5 border border-white/10">
                <div class="h-full rounded-full transition-all duration-700 bg-gradient-to-r from-emerald-400 to-teal-300"
                     :style="`width: ${metaProgreso}%`"
                     style="width: <?php echo e($progresoMeta); ?>%"></div>
            </div>

            <div class="flex items-center justify-between text-[11px] font-medium text-white/70 mt-2">
                <span>Avance</span>
                <span class="font-bold text-white" x-text="metaProgreso >= 100 ? '¡Meta alcanzada! 🎉' : `${metaProgreso}% completado`">
                    <?php echo e($progresoMeta >= 100 ? '¡Meta alcanzada! 🎉' : $progresoMeta . '% completado'); ?>

                </span>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/components/vendedor/hero-card.blade.php ENDPATH**/ ?>