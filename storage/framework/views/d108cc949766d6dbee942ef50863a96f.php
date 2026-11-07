<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Detalle del Ticket #'.e($ticket->codigo_ticket).' | AXStore']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Detalle del Ticket #'.e($ticket->codigo_ticket).' | AXStore']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="mx-auto max-w-5xl space-y-6 pb-12">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="<?php echo e(Auth::user()->isSuperAdmin() ? route('superadmin.tickets') : route('ayuda.tickets.index')); ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 mb-1">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Listado de Dudas</span>
                </a>
                <div class="flex items-center gap-3">
                    <span class="font-mono text-xl sm:text-2xl font-black text-slate-800">
                        #<?php echo e($ticket->codigo_ticket); ?>

                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-black 
                        <?php echo e($ticket->estado === 'Escalada' ? 'bg-amber-100 text-amber-800' : ($ticket->estado === 'Resuelta' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800')); ?>">
                        <?php echo e($ticket->estado); ?>

                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold 
                        <?php echo e($ticket->prioridad === 'Urgente' ? 'bg-rose-100 text-rose-800' : ($ticket->prioridad === 'Alta' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700')); ?>">
                        <?php echo e($ticket->prioridad); ?>

                    </span>
                </div>
                <h1 class="text-base sm:text-lg font-bold text-slate-700 mt-1">
                    <?php echo e($ticket->titulo ?? 'Consulta sobre ' . $ticket->modulo); ?>

                </h1>
            </div>

            <!-- Acciones según Rol -->
            <div class="flex flex-wrap items-center gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->isAdmin() && !in_array($ticket->estado, ['Escalada', 'Resuelta', 'Cancelada'])): ?>
                    <button type="button" onclick="document.getElementById('modalEscalar').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white transition-colors shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                        <span>Escalar a Super Admin</span>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($ticket->estado, ['Resuelta', 'Cancelada']) || Auth::user()->isSuperAdmin()): ?>
                    <button type="button" onclick="document.getElementById('modalCambiarEstado').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-white transition-colors shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-sliders"></i>
                        <span>Cambiar Estado</span>
                    </button>
                <?php else: ?>
                    <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                        <i class="fas fa-lock mr-1 text-slate-400"></i> Estado Definitivo
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <!-- Información General y Trazabilidad -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Solicitante</span>
                <span class="font-bold text-slate-800 text-sm"><?php echo e($ticket->nombre_usuario); ?></span>
                <span class="text-slate-400 block text-[11px] font-mono"><?php echo e($ticket->usuario->username ?? '—'); ?> (<?php echo e($ticket->rol_usuario); ?>)</span>
            </div>
            <div>
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Módulo / Tipo</span>
                <span class="font-bold text-slate-800"><?php echo e($ticket->modulo); ?></span>
                <span class="text-slate-500 block text-[11px]"><?php echo e($ticket->tipo_consulta); ?></span>
            </div>
            <div>
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Nivel Jerárquico</span>
                <span class="font-bold <?php echo e($ticket->nivel_escalamiento === 'super_admin' ? 'text-indigo-600' : 'text-blue-600'); ?>">
                    <?php echo e($ticket->nivel_escalamiento === 'super_admin' ? 'Super Administrador' : 'Administrador'); ?>

                </span>
                <span class="text-slate-400 block text-[11px]">Asignado: <?php echo e($ticket->asignado->nombre_real ?? 'Sin asignar'); ?></span>
            </div>
            <div>
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Fechas Clave</span>
                <span class="text-slate-700 block">Apertura: <?php echo e($ticket->created_at->format('d/m/Y H:i')); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->fecha_resolucion): ?>
                    <span class="text-emerald-700 font-bold block">Resuelto: <?php echo e($ticket->fecha_resolucion->format('d/m/Y H:i')); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->resolucion): ?>
            <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200 text-xs text-emerald-900">
                <span class="font-black text-emerald-950 flex items-center gap-1.5 mb-1">
                    <i class="fas fa-check-double text-emerald-600"></i> Resolución Final:
                </span>
                <p class="leading-relaxed"><?php echo e($ticket->resolucion); ?></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->resolvedor): ?>
                    <span class="text-[10px] text-emerald-700 block mt-1">
                        Registrada por: <?php echo e($ticket->resolvedor->nombre_real); ?> (<?php echo e($ticket->resolvedor->rol); ?>)
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Hilo de Conversación -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-comments text-slate-500"></i>
                    <h2 class="text-sm font-black text-slate-800">Historial de Conversación y Seguimiento</h2>
                </div>
                <span class="text-xs text-slate-400 font-mono"><?php echo e($mensajes->count()); ?> mensajes</span>
            </div>

            <div class="p-6 space-y-4 max-h-[600px] overflow-y-auto">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $mensajes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $esMio = $msg->id_usuario === Auth::id();
                        $esInterno = $msg->tipo_mensaje === 'interno_admin';
                        $esPrimerMensaje = ($index === 0);
                    ?>
                    <div class="flex flex-col <?php echo e($esMio ? 'items-end' : 'items-start'); ?>">
                        <div class="max-w-xl rounded-2xl p-4 text-xs shadow-sm <?php echo e($esInterno ? 'bg-amber-50 border border-amber-200 text-amber-950' : ($esMio ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-800')); ?>">
                            <div class="flex items-center justify-between gap-3 mb-1 text-[10px] <?php echo e($esMio && !$esInterno ? 'text-blue-100' : 'text-slate-400'); ?>">
                                <span class="font-bold <?php echo e($esInterno ? 'text-amber-800' : ''); ?>">
                                    <?php echo e($msg->nombre_usuario); ?> (<?php echo e(ucfirst($msg->rol_usuario)); ?>)
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($esInterno): ?>
                                        <span class="ml-1 px-1.5 py-0.5 rounded bg-amber-200 text-amber-900 font-black">
                                            NOTA INTERNA
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($esPrimerMensaje): ?>
                                        <span class="ml-1 px-1.5 py-0.5 rounded <?php echo e($esMio ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800'); ?> font-bold">
                                            Ticket Apertura
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                                <span class="font-mono"><?php echo e($msg->created_at->format('d/m/Y H:i')); ?></span>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($esPrimerMensaje): ?>
                                <!-- Tarjeta de Contexto Rápido del Ticket -->
                                <div class="my-2 p-2.5 rounded-xl <?php echo e($esMio ? 'bg-white/10 border border-white/20' : 'bg-white border border-slate-200'); ?> grid grid-cols-2 gap-2 text-[11px]">
                                    <div>
                                        <span class="<?php echo e($esMio ? 'text-blue-200' : 'text-slate-400'); ?> block text-[10px] uppercase font-bold">Módulo:</span>
                                        <span class="font-bold <?php echo e($esMio ? 'text-white' : 'text-slate-800'); ?>"><?php echo e($ticket->modulo); ?></span>
                                    </div>
                                    <div>
                                        <span class="<?php echo e($esMio ? 'text-blue-200' : 'text-slate-400'); ?> block text-[10px] uppercase font-bold">Motivo / Tipo:</span>
                                        <span class="font-bold <?php echo e($esMio ? 'text-white' : 'text-slate-800'); ?>"><?php echo e($ticket->tipo_consulta); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <p class="leading-relaxed whitespace-pre-line"><?php echo e($msg->mensaje); ?></p>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($msg->evidencia_url): ?>
                                <div class="mt-3 pt-2 border-t <?php echo e($esMio && !$esInterno ? 'border-blue-500' : 'border-slate-200'); ?>">
                                    <a href="<?php echo e($msg->evidencia_url); ?>" target="_blank" class="inline-flex items-center gap-1.5 font-bold underline text-[11px] <?php echo e($esMio && !$esInterno ? 'text-white' : 'text-blue-600'); ?>">
                                        <i class="fas fa-paperclip"></i> Ver archivo adjunto / evidencia
                                    </a>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <!-- Formulario de Respuesta o Aviso de Ticket Cerrado -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($ticket->estado, ['Resuelta', 'Cancelada'])): ?>
                <form method="POST" action="<?php echo e(route('ayuda.tickets.responder', $ticket->id)); ?>" class="p-5 bg-slate-50 border-t border-slate-200 space-y-3">
                    <?php echo csrf_field(); ?>
                    <div>
                        <textarea name="mensaje" required rows="3" placeholder="Escribe tu respuesta o información adicional..." class="w-full p-3.5 rounded-2xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-blue-500 bg-white"></textarea>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin()): ?>
                                <label class="inline-flex items-center gap-2 text-xs text-amber-900 font-bold cursor-pointer">
                                    <input type="checkbox" name="tipo_mensaje" value="interno_admin" class="rounded text-amber-600 focus:ring-0">
                                    <span>Nota interna privada (No visible para el vendedor)</span>
                                </label>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors shadow-sm flex items-center gap-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>Enviar Respuesta</span>
                        </button>
                    </div>
                </form>
            <?php elseif($ticket->estado === 'Resuelta'): ?>
                <div class="p-5 bg-emerald-50/70 border-t border-emerald-100 flex items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2.5 text-emerald-900">
                        <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                            <i class="fas fa-lock"></i>
                        </span>
                        <div>
                            <span class="font-bold text-sm block">Ticket Resuelto</span>
                            <span class="text-emerald-700 text-[11px]">Esta duda ha sido resuelta satisfactoriamente y su estado se encuentra cerrado de forma definitiva.</span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="p-4 bg-slate-100 text-center text-xs text-slate-500 font-medium border-t border-slate-200">
                    <i class="fas fa-ban mr-1.5 text-slate-400"></i>
                    Este ticket se encuentra cancelado. Puedes reactivarlo cambiando su estado a 'En proceso' si es necesario.
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- Trazabilidad de Historial de Estados -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-3">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-wider">Historial de Transiciones de Estado</h3>
            <div class="divide-y divide-slate-100 text-xs">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $historial; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="py-2.5 flex items-center justify-between gap-4">
                        <div class="space-y-0.5">
                            <span class="font-bold text-slate-800">
                                <?php echo e($h->estado_anterior ? "'{$h->estado_anterior}' → " : ''); ?>'<?php echo e($h->estado_nuevo); ?>'
                            </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($h->motivo): ?>
                                <p class="text-[11px] text-slate-500"><?php echo e($h->motivo); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="text-right text-[10px] text-slate-400 shrink-0">
                            <span><?php echo e($h->usuario->nombre_real ?? 'Sistema'); ?></span>
                            <span class="block font-mono"><?php echo e($h->created_at->format('d/m/Y H:i:s')); ?></span>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Modal para Escalamiento (Admin -> Super Admin) -->
    <div id="modalEscalar" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
            <div class="p-6 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-base">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-black">Escalar Caso al Super Admin</h3>
                        <p class="text-xs text-slate-400">Pasa el control a nivel superior de gobernanza</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modalEscalar').classList.add('hidden')" class="text-slate-400 hover:text-white">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="<?php echo e(route('ayuda.tickets.escalar', $ticket->id)); ?>" class="p-6 space-y-4 text-xs">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Justificación del Escalamiento <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="justificacion" required rows="4" placeholder="Explica detalladamente por qué este caso requiere la intervención o autorización del Super Administrador..." class="w-full p-3 rounded-xl border border-slate-200 focus:outline-none focus:border-amber-500 text-xs"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalEscalar').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold transition-colors">
                        Confirmar Escalamiento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Cambio de Estado -->
    <div id="modalCambiarEstado" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
            <div class="p-6 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-slate-800 text-blue-400 flex items-center justify-center text-base">
                        <i class="fas fa-sliders"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-black">Actualizar Estado del Ticket</h3>
                        <p class="text-xs text-slate-400">Flujo jerárquico de control</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modalCambiarEstado').classList.add('hidden')" class="text-slate-400 hover:text-white">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="<?php echo e(route('ayuda.tickets.estado', $ticket->id)); ?>" class="p-6 space-y-4 text-xs">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nuevo Estado <span class="text-rose-500">*</span></label>
                    <select name="estado" required class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Models\SoporteTicket::ESTADOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($st); ?>" <?php echo e($ticket->estado === $st ? 'selected' : ''); ?>><?php echo e($st); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Resolución o Motivo del Cambio</label>
                    <textarea name="resolucion" rows="3" placeholder="Detalla la solución brindada o razón del cambio de estado..." class="w-full p-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs"><?php echo e($ticket->resolucion); ?></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalCambiarEstado').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold transition-colors">
                        Guardar Cambio
                    </button>
                </div>
            </form>
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

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/ayuda/ver-ticket.blade.php ENDPATH**/ ?>