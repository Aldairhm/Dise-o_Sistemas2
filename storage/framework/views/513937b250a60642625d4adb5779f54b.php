<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Historial de Auditorías | AXStore Super Admin']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Historial de Auditorías | AXStore Super Admin']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="mx-auto max-w-7xl space-y-6 pb-12">
        
        <!-- Header con estilo nativo AXStore -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="<?php echo e(route('superadmin.dashboard')); ?>" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Panel de Gobernanza</span>
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-base shrink-0 border border-purple-100">
                        <i class="fas fa-user-secret"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black uppercase tracking-[0.18em] text-purple-600">Auditoría Segura</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-100 text-purple-700">Impersonation</span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900">Historial de Sesiones de Auditoría</h1>
                    </div>
                </div>
            </div>
            
            <a href="<?php echo e(route('superadmin.usuarios')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-all">
                <i class="fas fa-plus text-xs"></i>
                <span>Iniciar Nueva Auditoría</span>
            </a>
        </div>

        <!-- Tabla de Auditorías -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-4">Sesión</th>
                            <th class="py-3 px-4">Super Admin Auditor</th>
                            <th class="py-3 px-4">Cuenta Auditada</th>
                            <th class="py-3 px-4 min-w-[220px]">Justificación & Cierre</th>
                            <th class="py-3 px-4 text-center">Modalidad</th>
                            <th class="py-3 px-4">Fecha / Hora</th>
                            <th class="py-3 px-4 text-center">Estado</th>
                            <th class="py-3 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $auditorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $aud): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-mono font-bold text-slate-800">
                                    <span class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200 text-[11px]">
                                        #AUD-<?php echo e(str_pad($aud->id, 4, '0', STR_PAD_LEFT)); ?>

                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-900 block leading-tight"><?php echo e($aud->superAdmin->nombre_real ?? 'Super Admin'); ?></span>
                                    <span class="text-[10px] text-slate-400 font-mono"><?php echo e($aud->superAdmin->username ?? '—'); ?></span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-900"><?php echo e($aud->usuarioAuditado->nombre_real ?? 'Usuario'); ?></span>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-slate-100 text-slate-600">
                                            <?php echo e($aud->usuarioAuditado->rol ?? '—'); ?>

                                        </span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono block"><?php echo e($aud->usuarioAuditado->username ?? '—'); ?></span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    <p class="text-xs text-slate-700 leading-snug font-medium italic" title="<?php echo e($aud->justificacion); ?>">
                                        "<?php echo e($aud->justificacion); ?>"
                                    </p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aud->notas_cierre): ?>
                                        <span class="text-[10px] text-slate-400 block mt-1">
                                            <strong class="text-slate-500">Cierre:</strong> <?php echo e($aud->notas_cierre); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo e($aud->solo_lectura ? 'bg-slate-100 text-slate-700 border border-slate-200' : 'bg-amber-100 text-amber-800 border border-amber-200'); ?>">
                                        <?php echo e($aud->solo_lectura ? 'Solo Lectura' : 'Permisivo'); ?>

                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-800"><?php echo e($aud->iniciada_en->format('d/m/Y')); ?></div>
                                    <div class="text-[10px] text-slate-400 font-mono"><?php echo e($aud->iniciada_en->format('h:i A')); ?></div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aud->estado === 'activa' && $aud->expira_en->isFuture()): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-purple-100 text-purple-700 animate-pulse border border-purple-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span> ACTIVA
                                        </span>
                                    <?php elseif($aud->estado === 'finalizada'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fas fa-check text-[9px]"></i> FINALIZADA
                                        </span>
                                    <?php elseif($aud->estado === 'revocada'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fas fa-ban text-[9px]"></i> REVOCADA
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-600 border border-slate-200">
                                            <i class="fas fa-clock text-[9px]"></i> EXPIRADA
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aud->estado === 'activa' && $aud->expira_en->isFuture()): ?>
                                            <form id="formRevocar<?php echo e($aud->id); ?>" action="<?php echo e(route('superadmin.auditorias.revocar', $aud->id)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="button" onclick="confirmarRevocacion(<?php echo e($aud->id); ?>)" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors inline-flex items-center gap-1 cursor-pointer">
                                                    <i class="fas fa-ban text-[10px]"></i>
                                                    <span>Revocar</span>
                                                </button>
                                            </form>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <a href="<?php echo e(route('bitacora.index', ['auditoria_id' => $aud->id])); ?>"
                                           title="Ver trazabilidad de eventos de la sesión #AUD-<?php echo e(str_pad($aud->id, 4, '0', STR_PAD_LEFT)); ?>"
                                           class="px-2.5 py-1 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors inline-flex items-center gap-1">
                                            <i class="fas fa-clipboard-list text-blue-500"></i>
                                            <span>Trazabilidad</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="8" class="py-10 text-center text-xs text-slate-400">
                                    <i class="fas fa-shield-cat text-2xl text-slate-300 mb-2 block"></i>
                                    No hay registros históricos de sesiones de auditoría.
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                <?php echo e($auditorias->links()); ?>

            </div>
        </div>

    </div>

    <script>
        function confirmarRevocacion(id) {
            Swal.fire({
                title: '¿Revocar Sesión de Auditoría?',
                text: 'Esta acción cancelará inmediatamente el acceso temporal de supervisión y reactivará la cuenta del usuario para el inicio de sesión ordinario.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, revocar acceso',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formRevocar' + id).submit();
                }
            });
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7ae6b45c011e855a5545a671a7f3568e)): ?>
<?php $attributes = $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e; ?>
<?php unset($__attributesOriginal7ae6b45c011e855a5545a671a7f3568e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7ae6b45c011e855a5545a671a7f3568e)): ?>
<?php $component = $__componentOriginal7ae6b45c011e855a5545a671a7f3568e; ?>
<?php unset($__componentOriginal7ae6b45c011e855a5545a671a7f3568e); ?>
<?php endif; ?>

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/superadmin/historial-auditorias.blade.php ENDPATH**/ ?>