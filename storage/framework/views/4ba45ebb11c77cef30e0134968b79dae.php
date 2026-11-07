<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => 'AXStore ERP']));

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

foreach (array_filter((['title' => 'AXStore ERP']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title); ?></title>

    <!-- Dark mode init -->
    <script>
        (function() {
            var t = localStorage.getItem('ax_theme') || 'system';
            if (t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/images/logo.png')); ?>">

    <!-- 1. Recuperamos la tipografía original (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>[x-cloak] { display: none !important; }</style>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/home.css', 'resources/css/app.css', 'resources/js/app.js']); ?>
</head>

<!-- 2. Aplicamos el mismo color de fondo (bg-gray-50) y forzamos la fuente Inter -->

<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col" style="font-family: 'Inter', sans-serif;">

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('auditoria_token')): ?>
        <div class="bg-slate-900 text-slate-200 border-b border-slate-800 text-xs py-2 px-4 shadow-sm relative z-[60]">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2.5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                        Modo Auditoría
                    </span>
                    <span class="text-slate-300 text-xs">
                        Supervisando cuenta de: <strong class="text-white font-bold"><?php echo e(Auth::user()->nombre_real); ?></strong> <span class="text-slate-400 font-mono text-[11px]">(<?php echo e(Auth::user()->username); ?>)</span>
                    </span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('auditoria_solo_lectura', true)): ?>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-300 border border-amber-500/20">
                            Solo Lectura
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <form id="formFinalizarAuditoria" method="POST" action="<?php echo e(route('superadmin.auditorias.finalizar')); ?>" class="shrink-0 w-full sm:w-auto">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="notas_cierre" id="inputNotasCierreAuditoria" value="Auditoría completada exitosamente.">
                    <button type="button" onclick="confirmarSalidaAuditoria()" class="w-full sm:w-auto justify-center inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-800 hover:bg-rose-600 hover:text-white text-slate-300 border border-slate-700 hover:border-rose-600 font-bold text-xs transition-all shadow-sm cursor-pointer">
                        <i class="fas fa-arrow-right-from-bracket text-[11px]"></i>
                        <span>Finalizar Auditoría & Ver Informe</span>
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 transition-all duration-300">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::check() && Auth::user()->rol === 'vendedor'): ?>
            <?php echo $__env->make('componentsHome.header-vendedor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make('componentsHome.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </header>

    <!-- SWEETALERT2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            <?php if(session('success')): ?>
                Toast.fire({
                    icon: 'success',
                    title: "<?php echo e(session('success')); ?>"
                });
            <?php endif; ?>

            <?php if(session('error')): ?>
                Toast.fire({
                    icon: 'error',
                    title: 'Acción Denegada',
                    text: "<?php echo e(session('error')); ?>"
                });
            <?php endif; ?>

            <?php if($errors->any()): ?>
                Toast.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    html: `<?php echo implode('<br>', $errors->all()); ?>`
                });
            <?php endif; ?>
        });

        function confirmarSalidaAuditoria() {
            Swal.fire({
                title: '¿Finalizar Sesión de Auditoría?',
                text: 'Ingresa un breve dictamen o notas de cierre de lo revisado para anexarlo al informe completo:',
                input: 'textarea',
                inputPlaceholder: 'Ej: Se revisaron los módulos de ventas y comisiones. No se hallaron inconsistencias en las fechas auditadas...',
                inputValue: 'Auditoría completada exitosamente sin novedades críticas.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1e293b',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-file-shield mr-1"></i> Cerrar y Generar Informe',
                cancelButtonText: 'Continuar Auditando',
                reverseButtons: true,
                inputValidator: (value) => {
                    if (!value || value.trim().length < 5) {
                        return 'Debes escribir una nota o dictamen de cierre (mínimo 5 caracteres).';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('inputNotasCierreAuditoria').value = result.value.trim();
                    document.getElementById('formFinalizarAuditoria').submit();
                }
            });
        }
    </script>

    <main class="flex-grow p-6 w-full max-w-7xl mx-auto mt-4">
        <?php echo e($slot); ?>

    </main>

    <footer class="bg-white border-t border-gray-200 pt-16 pb-8 mt-auto">
        <?php echo $__env->make('componentsHome.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </footer>

</body>

</html>


<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/components/app.blade.php ENDPATH**/ ?>