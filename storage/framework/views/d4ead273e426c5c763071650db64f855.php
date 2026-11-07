<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Control de Dudas y Tickets de Soporte | AXStore']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Control de Dudas y Tickets de Soporte | AXStore']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="mx-auto max-w-7xl space-y-6">
        
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="<?php echo e(route('ayuda.index')); ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 mb-1">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver a Guías de Ayuda</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                        <i class="fab fa-whatsapp"></i>
                    </span>
                    <span>Control de Dudas y Tickets de Soporte</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Seguimiento de consultas enviadas por el equipo, estado de resolución y registro de auditoría.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('ayuda.index')); ?>" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-sm">
                    <i class="fas fa-book-open mr-1.5 text-slate-400"></i>
                    Ver Guías
                </a>
                <a href="<?php echo e(route('bitacora.index', ['modulo' => 'Soporte'])); ?>" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-sm">
                    <i class="fas fa-clock-rotate-left mr-1.5 text-slate-400"></i>
                    Bitácora de Soporte
                </a>
            </div>
        </div>

        <!-- Barra de Pestañas por Estado -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
            <?php
                $tabs = [
                    'todos'      => ['label' => 'Todos',       'badge_bg' => 'bg-slate-100',   'badge_fg' => 'text-slate-700'],
                    'Pendiente'  => ['label' => 'Pendientes',  'badge_bg' => 'bg-amber-100',   'badge_fg' => 'text-amber-800'],
                    'En proceso' => ['label' => 'En proceso',  'badge_bg' => 'bg-blue-100',    'badge_fg' => 'text-blue-800'],
                    'Escalada'   => ['label' => 'Escaladas',   'badge_bg' => 'bg-purple-100',  'badge_fg' => 'text-purple-800'],
                    'Resuelta'   => ['label' => 'Resueltas',   'badge_bg' => 'bg-emerald-100', 'badge_fg' => 'text-emerald-800'],
                    'Cancelada'  => ['label' => 'Canceladas',  'badge_bg' => 'bg-rose-100',    'badge_fg' => 'text-rose-800'],
                ];
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e(route('ayuda.tickets.index', array_merge(request()->query(), ['estado' => $key, 'page' => 1]))); ?>"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all <?php echo e($estadoTab === $key ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'); ?>">
                    <span><?php echo e($t['label']); ?></span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?php echo e($estadoTab === $key ? 'bg-white/20 text-white' : $t['badge_bg'] . ' ' . $t['badge_fg']); ?>">
                        <?php echo e($conteoEstados[$key] ?? 0); ?>

                    </span>
                </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <!-- Filtros y Búsqueda -->
        <form action="<?php echo e(route('ayuda.tickets.index')); ?>" method="GET" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col sm:flex-row items-center gap-3">
            <input type="hidden" name="estado" value="<?php echo e($estadoTab); ?>">
            <div class="relative flex-1 w-full">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       name="q"
                       value="<?php echo e(request('q')); ?>"
                       placeholder="Buscar por código (#TK-...), usuario, módulo o texto de la duda..."
                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-300">
            </div>

            <div class="w-full sm:w-56 shrink-0">
                <select name="modulo" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-300">
                    <option value="">Todos los módulos</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $modulos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($m['module']); ?>" <?php echo e(request('modulo') === $m['module'] ? 'selected' : ''); ?>>
                            <?php echo e($m['module']); ?>

                        </option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 transition-colors shadow-sm">
                    Filtrar
                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['q', 'modulo']) || $estadoTab !== 'todos'): ?>
                    <a href="<?php echo e(route('ayuda.tickets.index')); ?>" class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors" title="Limpiar filtros">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </form>

        <!-- Tabla de Tickets -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-4 whitespace-nowrap">Ticket</th>
                            <th class="py-3 px-4 whitespace-nowrap">Fecha / Hora</th>
                            <th class="py-3 px-4 whitespace-nowrap">Solicitante</th>
                            <th class="py-3 px-4 whitespace-nowrap">Módulo / Tipo</th>
                            <th class="py-3 px-4 min-w-[260px]">Duda planteada</th>
                            <th class="py-3 px-4 text-center whitespace-nowrap">Evidencia</th>
                            <th class="py-3 px-4 whitespace-nowrap">Estado</th>
                            <th class="py-3 px-4 text-center whitespace-nowrap">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- Código de Ticket -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                        <?php echo e($ticket->codigo_ticket); ?>

                                    </span>
                                </td>

                                <!-- Fecha / Hora -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800"><?php echo e($ticket->created_at->format('d/m/Y')); ?></div>
                                    <div class="text-[10px] text-slate-400 font-medium"><?php echo e($ticket->created_at->format('h:i A')); ?> (<?php echo e($ticket->created_at->diffForHumans()); ?>)</div>
                                </td>

                                <!-- Solicitante -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-600 text-white font-bold text-[10px] flex items-center justify-center shrink-0 uppercase">
                                            <?php echo e(substr($ticket->nombre_usuario, 0, 1)); ?>

                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 leading-tight"><?php echo e($ticket->nombre_usuario); ?></p>
                                            <p class="text-[10px] text-slate-400 uppercase font-semibold"><?php echo e($ticket->rol_usuario ?? 'usuario'); ?></p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Módulo / Tipo -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-block font-bold text-slate-800 bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-100">
                                        <?php echo e($ticket->modulo); ?>

                                    </span>
                                    <p class="text-[10px] text-slate-500 mt-0.5 truncate max-w-[140px]"><?php echo e($ticket->tipo_consulta); ?></p>
                                </td>

                                <!-- Duda -->
                                <td class="py-3 px-4">
                                    <p class="text-xs text-slate-700 line-clamp-2 leading-relaxed" title="<?php echo e($ticket->duda); ?>">
                                        <?php echo e($ticket->duda); ?>

                                    </p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->resolucion): ?>
                                        <div class="mt-1.5 p-1.5 rounded bg-emerald-50 border border-emerald-100 text-[10px] text-emerald-800">
                                            <span class="font-bold">Resolución:</span> <?php echo e($ticket->resolucion); ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->resolvedor): ?>
                                                <span class="text-emerald-600">(<?php echo e($ticket->resolvedor->nombre_real); ?>)</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                <!-- Evidencia -->
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->evidencia_url): ?>
                                        <a href="<?php echo e($ticket->evidencia_url); ?>" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition-colors" title="Abrir imagen de evidencia">
                                            <i class="fas fa-image text-slate-500"></i>
                                            <span>Ver foto</span>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-slate-300 text-[11px]">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                <!-- Estado -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <?php
                                        $badgeStyles = [
                                            'Pendiente'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'En proceso' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'Escalada'   => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'Resuelta'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Cancelada'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ];
                                    ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border <?php echo e($badgeStyles[$ticket->estado] ?? 'bg-slate-50 text-slate-700 border-slate-200'); ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?php echo e($ticket->estado === 'Resuelta' ? 'bg-emerald-500' : ($ticket->estado === 'Pendiente' ? 'bg-amber-500' : ($ticket->estado === 'En proceso' ? 'bg-blue-500' : ($ticket->estado === 'Escalada' ? 'bg-purple-500' : 'bg-rose-500')))); ?>"></span>
                                        <?php echo e($ticket->estado); ?>

                                    </span>
                                </td>

                                <!-- Acciones -->
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="<?php echo e(route('ayuda.tickets.show', $ticket->id)); ?>"
                                           class="px-2.5 py-1 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors inline-flex items-center gap-1">
                                            <i class="fas fa-comments text-blue-500"></i>
                                            <span>Hilo</span>
                                        </a>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) && (!in_array($ticket->estado, ['Resuelta', 'Cancelada']) || auth()->user()->isSuperAdmin())): ?>
                                            <button type="button"
                                                    onclick='abrirModalGestion(<?php echo json_encode($ticket, 15, 512) ?>)'
                                                    class="px-2.5 py-1 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
                                                <i class="fas fa-sliders mr-1 text-slate-400"></i>
                                                Gestionar
                                            </button>
                                        <?php else: ?>
                                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold text-slate-400 bg-slate-50 border border-slate-100">
                                                <i class="fas fa-lock mr-0.5"></i> Cerrado
                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <i class="fas fa-ticket text-3xl mb-2 text-slate-300 block"></i>
                                    <p class="font-bold text-sm text-slate-600">No se encontraron tickets registrados</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Las dudas enviadas por WhatsApp se listarán en este panel automáticamente.</p>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tickets->hasPages()): ?>
                <div class="p-4 border-t border-slate-100">
                    <?php echo e($tickets->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- ─── MODAL DE GESTIÓN Y RESOLUCIÓN DE TICKET (ADMIN) ─── -->
    <div id="modalGestionTicket" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800" id="gestionTituloModal">Gestionar Ticket</h3>
                    <p class="text-[11px] text-slate-500" id="gestionSubtituloModal">Actualizar estado y resolución</p>
                </div>
                <button type="button" onclick="cerrarModalGestion()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="p-5 space-y-3.5">
                <!-- Estado nuevo -->
                <div>
                    <label for="gestionEstadoSelect" class="block text-xs font-bold text-slate-700 mb-1">
                        Estado del Ticket
                    </label>
                    <select id="gestionEstadoSelect" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-300">
                        <option value="Pendiente">Pendiente</option>
                        <option value="En proceso">En proceso</option>
                        <option value="Escalada">Escalada</option>
                        <option value="Resuelta">Resuelta</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>

                <!-- Detalle de la duda -->
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                    <span class="block text-[10px] font-bold uppercase text-slate-400 mb-0.5">Duda del usuario</span>
                    <p id="gestionDudaTexto" class="italic text-slate-700 line-clamp-3">...</p>
                </div>

                <!-- Resolución o Nota Técnica -->
                <div>
                    <label for="gestionResolucionTexto" class="block text-xs font-bold text-slate-700 mb-1">
                        Nota de Resolución / Solución brindada <span class="text-slate-400 font-normal">(opcional)</span>
                    </label>
                    <textarea id="gestionResolucionTexto"
                              rows="3"
                              placeholder="Describe brevemente cómo se resolvió la duda o la acción tomada..."
                              class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-300"></textarea>
                </div>
            </div>

            <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="cerrarModalGestion()" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200 transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="button"
                        id="btnGuardarGestion"
                        onclick="guardarGestionTicket()"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 transition-colors shadow-sm cursor-pointer">
                    Guardar Cambios
                </button>
            </div>
        </div>
    </div>

    <script>
        let ticketActualId = null;

        function abrirModalGestion(ticket) {
            ticketActualId = ticket.id;
            document.getElementById('gestionTituloModal').textContent = `Gestionar Ticket #${ticket.codigo_ticket}`;
            document.getElementById('gestionSubtituloModal').textContent = `${ticket.nombre_usuario} · Módulo: ${ticket.modulo}`;
            document.getElementById('gestionEstadoSelect').value = ticket.estado;
            document.getElementById('gestionDudaTexto').textContent = ticket.duda || 'Sin detalle';
            document.getElementById('gestionResolucionTexto').value = ticket.resolucion || '';
            document.getElementById('modalGestionTicket').classList.remove('hidden');
        }

        function cerrarModalGestion() {
            document.getElementById('modalGestionTicket').classList.add('hidden');
            ticketActualId = null;
        }

        async function guardarGestionTicket() {
            if (!ticketActualId) return;

            const estado = document.getElementById('gestionEstadoSelect').value;
            const resolucion = document.getElementById('gestionResolucionTexto').value;
            const btn = document.getElementById('btnGuardarGestion');

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';

            try {
                const response = await fetch(`/ayuda/tickets/${ticketActualId}/estado`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({ estado, resolucion })
                });

                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Estado Actualizado!',
                        text: data.message || 'El ticket ha sido modificado correctamente.',
                        timer: 1600,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Transición No Permitida',
                        text: data.message || 'No se pudo actualizar el ticket.',
                        confirmButtonColor: '#1e293b',
                        confirmButtonText: 'Entendido'
                    });
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Servidor',
                    text: 'Ocurrió un error al procesar la solicitud.',
                    confirmButtonColor: '#1e293b'
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Guardar Cambios';
            }
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

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/ayuda/tickets.blade.php ENDPATH**/ ?>