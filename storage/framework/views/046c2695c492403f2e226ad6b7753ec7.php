<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'comision', // 'comision' | 'venta'
    'status' => 'proyectada',
    'label' => null,
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
    'type' => 'comision', // 'comision' | 'venta'
    'status' => 'proyectada',
    'label' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $statusKey = strtolower(trim($status));
    $classes = '';
    $dotColor = '';
    $defaultLabel = $label;

    if ($type === 'comision') {
        switch ($statusKey) {
            case 'pagada':
            case 'pagado':
                $classes = 'bg-emerald-50 text-emerald-700 border border-emerald-200/80';
                $dotColor = 'bg-emerald-500';
                $defaultLabel = $label ?? 'Cobrada';
                break;
            case 'pendiente_cobro':
            case 'pendiente':
                $classes = 'bg-amber-50 text-amber-700 border border-amber-200/80';
                $dotColor = 'bg-amber-500';
                $defaultLabel = $label ?? 'Pendiente de cobro';
                break;
            case 'proyectada':
            case 'proyectado':
                $classes = 'bg-slate-100 text-slate-600 border border-slate-200/80';
                $dotColor = 'bg-slate-400';
                $defaultLabel = $label ?? 'Por ganar';
                break;
            case 'anulada':
            case 'anulado':
            case 'cancelada':
                $classes = 'bg-rose-50 text-rose-700 border border-rose-200/80';
                $dotColor = 'bg-rose-500';
                $defaultLabel = $label ?? 'Anulada';
                break;
            default:
                $classes = 'bg-slate-100 text-slate-600 border border-slate-200/80';
                $dotColor = 'bg-slate-400';
                $defaultLabel = $label ?? ucfirst($status);
                break;
        }
    } else { // 'venta'
        switch ($statusKey) {
            case 'entregada':
            case 'entregado':
                $classes = 'bg-emerald-50 text-emerald-700 border border-emerald-200/80';
                $dotColor = 'bg-emerald-500';
                $defaultLabel = $label ?? 'Entregada';
                break;
            case 'confirmada':
            case 'confirmado':
                $classes = 'bg-sky-50 text-sky-700 border border-sky-200/80';
                $dotColor = 'bg-sky-500';
                $defaultLabel = $label ?? 'Confirmada';
                break;
            case 'en_ruta':
            case 'en ruta':
                $classes = 'bg-indigo-50 text-indigo-700 border border-indigo-200/80';
                $dotColor = 'bg-indigo-500';
                $defaultLabel = $label ?? 'En ruta';
                break;
            case 'pendiente':
                $classes = 'bg-amber-50 text-amber-700 border border-amber-200/80';
                $dotColor = 'bg-amber-500';
                $defaultLabel = $label ?? 'Pendiente';
                break;
            case 'cancelada':
            case 'cancelado':
                $classes = 'bg-rose-50 text-rose-700 border border-rose-200/80';
                $dotColor = 'bg-rose-500';
                $defaultLabel = $label ?? 'Cancelada';
                break;
            case 'devolucion':
            case 'devolución':
                $classes = 'bg-orange-50 text-orange-700 border border-orange-200/80';
                $dotColor = 'bg-orange-500';
                $defaultLabel = $label ?? 'Devolución';
                break;
            case 'cambio':
                $classes = 'bg-purple-50 text-purple-700 border border-purple-200/80';
                $dotColor = 'bg-purple-500';
                $defaultLabel = $label ?? 'Cambio';
                break;
            default:
                $classes = 'bg-slate-100 text-slate-600 border border-slate-200/80';
                $dotColor = 'bg-slate-400';
                $defaultLabel = $label ?? ucfirst($status);
                break;
        }
    }
?>

<span <?php echo e($attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-tight whitespace-nowrap {$classes}"])); ?>>
    <span class="w-1.5 h-1.5 rounded-full <?php echo e($dotColor); ?>"></span>
    <span><?php echo e($defaultLabel); ?></span>
</span>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/components/vendedor/status-badge.blade.php ENDPATH**/ ?>