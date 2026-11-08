<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Informe de Auditoría #AUD-'.e(str_pad($sesion->id, 4, '0', STR_PAD_LEFT)).' | AXStore Super Admin']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Informe de Auditoría #AUD-'.e(str_pad($sesion->id, 4, '0', STR_PAD_LEFT)).' | AXStore Super Admin']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="mx-auto max-w-6xl space-y-6 pb-16">

        <!-- Barra de navegación y Acciones -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
            <div>
                <a href="<?php echo e(route('superadmin.auditorias.historial')); ?>" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Historial de Auditorías</span>
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shrink-0 border border-indigo-100">
                        <i class="fas fa-file-shield"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black uppercase tracking-[0.18em] text-indigo-600">Reporte Oficial de Supervisión</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold <?php echo e($sesion->estado === 'finalizada' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'); ?>">
                                <?php echo e(strtoupper($sesion->estado)); ?>

                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900">
                            Informe de Auditoría #AUD-<?php echo e(str_pad($sesion->id, 4, '0', STR_PAD_LEFT)); ?>

                        </h1>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción: Imprimir y Descargar PDF -->
            <div class="flex items-center gap-2.5">
                <a href="<?php echo e(route('superadmin.auditorias.informe.pdf', ['sesion' => $sesion->id, 'action' => 'stream', 'print' => 1])); ?>"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold text-xs shadow-sm transition-all cursor-pointer">
                    <i class="fas fa-print text-slate-500"></i>
                    <span>Imprimir</span>
                </a>
                <a href="<?php echo e(route('superadmin.auditorias.informe.pdf', $sesion->id)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition-all">
                    <i class="fas fa-file-pdf"></i>
                    <span>Descargar PDF</span>
                </a>
            </div>
        </div>

        <!-- TARJETA PRINCIPAL DEL INFORME (DISEÑO FORMAL Y ELEGANTE) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-8 print:border-none print:shadow-none print:p-0">

            <!-- Encabezado Formal con Logotipo y Metadatos -->
            <div class="border-b border-slate-100 pb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2.5 mb-2">
                        <span class="font-black text-xl tracking-tight text-slate-900">AX<span class="text-blue-600">STORE</span></span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200 uppercase">Sistema de Gobernanza</span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">
                        Registro oficial de trazabilidad para sesiones administrativas de supervisión e inspección segura.
                    </p>
                </div>
                <div class="text-left sm:text-right text-xs space-y-1">
                    <div><span class="text-slate-400 font-medium">Folio de Auditoría:</span> <strong class="font-mono text-slate-800">#AUD-<?php echo e(str_pad($sesion->id, 4, '0', STR_PAD_LEFT)); ?></strong></div>
                    <div><span class="text-slate-400 font-medium">Fecha de Emisión:</span> <span class="font-mono text-slate-700"><?php echo e(now()->format('d/m/Y h:i A')); ?></span></div>
                    <div><span class="text-slate-400 font-medium">Identificador Hash:</span> <span class="font-mono text-[10px] text-slate-500"><?php echo e(substr($sesion->token_sesion, 0, 16)); ?>...</span></div>
                </div>
            </div>

            <!-- Resumen en 4 Columnas Métricas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Duración de la Sesión</span>
                    <span class="text-xl font-black text-slate-800"><?php echo e($duracionRealMinutos); ?> min</span>
                    <span class="text-[11px] text-slate-400 block mt-0.5 font-mono"><?php echo e($sesion->iniciada_en->format('H:i')); ?> &rarr; <?php echo e($sesion->finalizada_en ? $sesion->finalizada_en->format('H:i') : 'En curso'); ?></span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Modalidad de Acceso</span>
                    <span class="text-xl font-black <?php echo e($sesion->solo_lectura ? 'text-blue-600' : 'text-amber-600'); ?>">
                        <?php echo e($sesion->solo_lectura ? 'Solo Lectura' : 'Permisivo'); ?>

                    </span>
                    <span class="text-[11px] text-slate-400 block mt-0.5"><?php echo e($sesion->solo_lectura ? 'Sin permisos de mutación' : 'Con autorización de escritura'); ?></span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Eventos y Acciones</span>
                    <span class="text-xl font-black text-indigo-600"><?php echo e($accionesTotales); ?></span>
                    <span class="text-[11px] text-slate-400 block mt-0.5">Registros auditables en bitácora</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Módulos Inspeccionados</span>
                    <span class="text-xl font-black text-emerald-600"><?php echo e($modulosRevisados->count()); ?></span>
                    <span class="text-[11px] text-slate-400 block mt-0.5 truncate"><?php echo e($modulosRevisados->join(', ') ?: 'Sin navegación'); ?></span>
                </div>
            </div>

            <!-- Bloque 1: El PORQUÉ de la Auditoría y Cuentas Involucradas -->
            <div class="space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <i class="fas fa-circle-info text-blue-500"></i>
                    <span>1. Justificación Legal, Motivo y Actores del Procedimiento</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Super Admin Auditor -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-white">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2">Super Administrador Responsable (Auditor)</span>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-black text-base border border-purple-100">
                                <?php echo e(strtoupper(substr($sesion->superAdmin->nombre_real ?? 'S', 0, 1))); ?>

                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm"><?php echo e($sesion->superAdmin->nombre_real ?? 'Super Administrador'); ?></h4>
                                <p class="text-xs text-slate-500 font-mono"><?php echo e($sesion->superAdmin->username ?? '—'); ?></p>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-purple-100 text-purple-700">
                                    <?php echo e($sesion->superAdmin->rol ?? 'super_admin'); ?>

                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Usuario Auditado -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-white">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2">Cuenta Auditada / Supervisada</span>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-black text-base border border-blue-100">
                                <?php echo e(strtoupper(substr($sesion->usuarioAuditado->nombre_real ?? 'U', 0, 1))); ?>

                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm"><?php echo e($sesion->usuarioAuditado->nombre_real ?? 'Usuario'); ?></h4>
                                <p class="text-xs text-slate-500 font-mono"><?php echo e($sesion->usuarioAuditado->username ?? '—'); ?></p>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-blue-100 text-blue-700">
                                    <?php echo e($sesion->usuarioAuditado->rol ?? 'usuario'); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Justificación Formal (El Porqué) -->
                <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200/80">
                    <span class="text-xs font-black text-amber-900 flex items-center gap-1.5 mb-1.5">
                        <i class="fas fa-bullhorn text-amber-600"></i> Motivo y Justificación Formal de la Auditoría:
                    </span>
                    <p class="text-xs text-amber-950 leading-relaxed font-medium whitespace-pre-line">
                        <?php echo e($sesion->justificacion); ?>

                    </p>
                </div>

                <!-- Notas y Dictamen de Cierre -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sesion->notas_cierre): ?>
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-xs font-black text-slate-800 flex items-center gap-1.5 mb-1.5">
                            <i class="fas fa-clipboard-check text-emerald-600"></i> Dictamen y Notas de Cierre de la Sesión:
                        </span>
                        <p class="text-xs text-slate-700 leading-relaxed">
                            <?php echo e($sesion->notas_cierre); ?>

                        </p>
                        <div class="mt-2 text-[10px] text-slate-400 font-mono">
                            Cierre registrado el: <?php echo e($sesion->finalizada_en ? $sesion->finalizada_en->format('d/m/Y h:i:s A') : 'N/A'); ?>

                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <!-- Bloque 2: QUÉ SE REVISÓ (Módulos y Desglose Operativo) -->
            <div class="space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <i class="fas fa-layer-group text-indigo-500"></i>
                    <span>2. ¿Qué se revisó? Alcance de Módulos e Inspecciones</span>
                </h3>

                <div class="p-5 rounded-2xl border border-slate-200 bg-white space-y-3">
                    <span class="text-xs font-bold text-slate-700 block">Módulos del Sistema Consultados Durante la Sesión:</span>
                    <div class="flex flex-wrap gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $modulosRevisados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                <i class="fas fa-check-circle text-indigo-500 text-[10px]"></i>
                                <?php echo e($mod); ?>

                            </span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <span class="text-xs text-slate-400 italic">No se registraron cambios ni navegación adicional.</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resumenAcciones->isNotEmpty()): ?>
                        <div class="pt-3 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $resumenAcciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $accionNombre => $conteo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="bg-slate-50 p-2.5 rounded-xl">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block"><?php echo e($accionNombre); ?></span>
                                    <span class="text-sm font-black text-slate-800"><?php echo e($conteo); ?> evento(s)</span>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Bloque 3: CRONOGRAMA DETALLADO (TODO LO QUE SE HIZO PASO A PASO) -->
            <div class="space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <i class="fas fa-list-check text-emerald-500"></i>
                    <span>3. Cronograma Completo de Eventos y Trazabilidad Forense</span>
                </h3>

                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                                    <th class="py-3 px-4">Hora</th>
                                    <th class="py-3 px-4">Acción</th>
                                    <th class="py-3 px-4">Módulo</th>
                                    <th class="py-3 px-4">Descripción del Evento</th>
                                    <th class="py-3 px-4 text-center">Contexto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $eventos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-3 px-4 font-mono text-slate-500 text-[11px] whitespace-nowrap">
                                            <?php echo e($ev->created_at->format('H:i:s')); ?>

                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase
                                                <?php echo e($ev->accion === 'INICIO_AUDITORIA' ? 'bg-purple-100 text-purple-800' :
                                                   ($ev->accion === 'FIN_AUDITORIA' ? 'bg-emerald-100 text-emerald-800' :
                                                   ($ev->accion === 'REVISION_VISTA' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800'))); ?>">
                                                <?php echo e($ev->accion); ?>

                                            </span>
                                        </td>
                                        <td class="py-3 px-4 font-bold text-slate-800 whitespace-nowrap">
                                            <?php echo e($ev->modulo); ?>

                                        </td>
                                        <td class="py-3 px-4 text-slate-600 leading-snug">
                                            <?php echo e($ev->descripcion); ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ev->datos_nuevos && is_array($ev->datos_nuevos) && isset($ev->datos_nuevos['url'])): ?>
                                                <span class="block font-mono text-[10px] text-slate-400 mt-0.5 truncate max-w-md">
                                                    <?php echo e($ev->datos_nuevos['url']); ?>

                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <span class="text-[10px] font-mono text-slate-400">
                                                <?php echo e($ev->ip_address ?? '127.0.0.1'); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-xs text-slate-400">
                                            <i class="fas fa-clipboard-check text-xl mb-1 block text-slate-300"></i>
                                            No se generaron eventos de bitácora adicionales durante esta sesión.
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Firmas y Validación Institucional (Fijadas a pie de página en impresión) -->
            <div class="pt-8 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs text-slate-600 print:fixed print:bottom-0 print:left-0 print:right-0 print:pt-4 print:border-t print:border-slate-300">
                <div class="border-t border-slate-300 pt-3 text-center sm:text-left print:border-none print:pt-0">
                    <span class="font-bold text-slate-800 block text-sm"><?php echo e($sesion->superAdmin->nombre_real ?? 'Super Administrador'); ?></span>
                    <span class="text-slate-400 text-[11px] block">Super Administrador Auditor · ID #<?php echo e($sesion->id_super_admin); ?></span>
                    <span class="text-[10px] font-mono text-slate-400 mt-1 block">Validado mediante autenticación MFA y contraseña maestra</span>
                </div>
                <div class="border-t border-slate-300 pt-3 text-center sm:text-right print:border-none print:pt-0">
                    <span class="font-bold text-slate-800 block text-sm">Gobernanza & Auditoría AXStore</span>
                    <span class="text-slate-400 text-[11px] block">Certificado de Trazabilidad Forense</span>
                    <span class="text-[10px] font-mono text-slate-400 mt-1 block">Hash de Integridad: <?php echo e(sha1($sesion->token_sesion . $sesion->id)); ?></span>
                </div>
            </div>

        </div>
    </div>
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

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/superadmin/informe-auditoria.blade.php ENDPATH**/ ?>