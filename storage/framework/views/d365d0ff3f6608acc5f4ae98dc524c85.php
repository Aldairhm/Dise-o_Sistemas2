<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'stats' => [],
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
    'stats' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $totalMonto = (float) ($stats['total_monto'] ?? 0);
    $totalOrdenes = (int) ($stats['total_ordenes'] ?? 0);
    $entregadas = (int) ($stats['entregadas'] ?? 0);
    $montoEntregadas = (float) ($stats['monto_entregadas'] ?? 0);
    $enProceso = (int) ($stats['en_proceso'] ?? 0);
    $pendientes = (int) ($stats['pendientes'] ?? 0);
    $confirmadas = (int) ($stats['confirmadas'] ?? 0);
    $enRuta = (int) ($stats['en_ruta'] ?? 0);
    $canceladas = (int) ($stats['canceladas'] ?? 0);
    $devoluciones = (int) ($stats['devoluciones'] ?? 0);
?>

<div class="bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden">
    <div class="grid grid-cols-2 lg:grid-cols-4 divide-y lg:divide-y-0 lg:divide-x divide-slate-100">
        
        <div class="p-4 sm:p-5 hover:bg-slate-50/50 transition-colors">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Vendido</span>
            <div class="flex items-baseline gap-1">
                <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    $<span x-text="formatMoney(salesStats.total_monto)"><?php echo e(number_format($totalMonto, 2)); ?></span>
                </span>
            </div>
            <span class="block text-xs font-medium text-slate-500 mt-0.5">
                <span x-text="salesStats.total_ordenes"><?php echo e($totalOrdenes); ?></span> órdenes
            </span>
        </div>

        
        <div class="p-4 sm:p-5 hover:bg-slate-50/50 transition-colors">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Entregadas</span>
            <div class="flex items-baseline gap-1">
                <span class="text-xl sm:text-2xl font-black text-emerald-600 tracking-tight" x-text="salesStats.entregadas">
                    <?php echo e($entregadas); ?>

                </span>
            </div>
            <span class="block text-xs font-medium text-slate-500 mt-0.5">
                $<span x-text="formatMoney(salesStats.monto_entregadas)"><?php echo e(number_format($montoEntregadas, 2)); ?></span> confirmado
            </span>
        </div>

        
        <div class="p-4 sm:p-5 hover:bg-slate-50/50 transition-colors">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">En proceso</span>
            <div class="flex items-baseline gap-1">
                <span class="text-xl sm:text-2xl font-black tracking-tight"
                      :class="salesStats.en_proceso > 0 ? 'text-amber-600' : 'text-slate-300'"
                      x-text="salesStats.en_proceso">
                    <?php echo e($enProceso); ?>

                </span>
            </div>
            <span class="block text-xs font-medium text-slate-500 mt-0.5 truncate"
                  x-text="`${salesStats.pendientes} pend · ${salesStats.confirmadas} conf · ${salesStats.en_ruta} ruta`">
                <?php echo e($pendientes); ?> pend · <?php echo e($confirmadas); ?> conf · <?php echo e($enRuta); ?> ruta
            </span>
        </div>

        
        <div class="p-4 sm:p-5 hover:bg-slate-50/50 transition-colors">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Canceladas</span>
            <div class="flex items-baseline gap-1">
                <span class="text-xl sm:text-2xl font-black tracking-tight"
                      :class="salesStats.canceladas > 0 ? 'text-rose-600' : 'text-slate-300'"
                      x-text="salesStats.canceladas">
                    <?php echo e($canceladas); ?>

                </span>
            </div>
            <span class="block text-xs font-medium mt-0.5"
                  :class="salesStats.devoluciones > 0 ? 'text-orange-500' : 'text-slate-500'"
                  x-text="`${salesStats.devoluciones} devolución(es)`">
                <?php echo e($devoluciones); ?> devolución(es)
            </span>
        </div>
    </div>
</div>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/components/vendedor/stats-row.blade.php ENDPATH**/ ?>