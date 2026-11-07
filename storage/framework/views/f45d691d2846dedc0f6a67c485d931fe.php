<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-200/80 bg-gray-50/75 text-[11px] font-black uppercase tracking-wider text-gray-500">
                <th class="py-3.5 px-4 sm:px-6">Usuario</th>
                <th class="py-3.5 px-4">Nombre Real</th>
                <th class="py-3.5 px-4 hidden sm:table-cell">Teléfono</th>
                <th class="py-3.5 px-4 text-center">Rol</th>
                <th class="py-3.5 px-4 text-center">Estado</th>
                <th class="py-3.5 px-4 sm:px-6 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm text-gray-700 bg-white">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr class="hover:bg-blue-50/40 transition-colors group" id="user-row-<?php echo e($user->id); ?>">
                    <!-- Avatar y Usuario -->
                    <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-transform group-hover:scale-105 <?php echo e($user->rol === 'admin' ? 'bg-gradient-to-tr from-purple-600 to-indigo-600 text-white shadow-indigo-200' : 'bg-gradient-to-tr from-blue-500 to-cyan-500 text-white shadow-blue-200'); ?>">
                                <?php echo e(strtoupper(substr($user->nombre_real ?? $user->username, 0, 1))); ?>

                            </div>
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    <?php echo e($user->username); ?>

                                </span>
                            </div>
                        </div>
                    </td>

                    <!-- Nombre -->
                    <td class="py-4 px-4 whitespace-nowrap">
                        <span class="font-medium text-gray-800">
                            <?php echo e($user->nombre_real ?: '—'); ?>

                        </span>
                    </td>

                    <!-- Teléfono -->
                    <td class="py-4 px-4 whitespace-nowrap hidden sm:table-cell">
                        <span class="font-medium text-gray-800">
                            <?php echo e($user->telefono ?: '—'); ?>

                        </span>
                    </td>

                    <!-- Rol -->
                    <td class="py-4 px-4 text-center whitespace-nowrap">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->rol === 'admin'): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100/80 text-purple-700 border border-purple-200/60 shadow-xs">
                                <i class="fas fa-shield-halved text-[10px]"></i> Administrador
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100/80 text-blue-700 border border-blue-200/60 shadow-xs">
                                <i class="fas fa-tag text-[10px]"></i> Vendedor
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>

                    <!-- Estado con Toggle interactivo -->
                    <td class="py-4 px-4 text-center whitespace-nowrap">
                        <button type="button" 
                                onclick="toggleUserStatus('<?php echo e($user->id); ?>', this)" 
                                data-current="<?php echo e($user->estado); ?>"
                                data-name="<?php echo e($user->username); ?>"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold cursor-pointer transition-all hover:scale-105 active:scale-95 shadow-xs <?php echo e($user->estado == 1 ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 border border-emerald-200' : 'bg-rose-100 text-rose-700 hover:bg-rose-200 border border-rose-200'); ?>"
                                title="Haz clic para cambiar estado">
                            <span class="w-2 h-2 rounded-full <?php echo e($user->estado == 1 ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'); ?>"></span>
                            <span class="status-label"><?php echo e($user->estado == 1 ? 'Habilitado' : 'Deshabilitado'); ?></span>
                        </button>
                    </td>

                    <!-- Acciones -->
                    <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-2">
                            <!-- Boton Editar -->
                            <button type="button" 
                                    data-user="<?php echo e(json_encode($user)); ?>"
                                    onclick="openEditUserModal(JSON.parse(this.dataset.user))"
                                    class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 flex items-center justify-center transition-colors cursor-pointer"
                                    title="Editar Usuario">
                                <i class="fas fa-pen-to-square text-xs"></i>
                            </button>

                            <!-- Boton Eliminar -->
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->id() !== $user->id): ?>
                            <button type="button" 
                                    data-id="<?php echo e($user->id); ?>"
                                    data-name="<?php echo e($user->nombre_real ?? $user->username); ?>"
                                    onclick="confirmDeleteUser(this.dataset.id, this.dataset.name)"
                                    class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-rose-100 text-gray-600 hover:text-rose-600 flex items-center justify-center transition-colors cursor-pointer"
                                    title="Eliminar Usuario">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                            <?php else: ?>
                            <span class="w-8 h-8 rounded-lg bg-gray-50 text-gray-300 flex items-center justify-center" title="Tu usuario actual">
                                <i class="fas fa-user-check text-xs"></i>
                            </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="6" class="py-12 px-4 text-center">
                        <div class="max-w-xs mx-auto flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-2xl mb-3">
                                <i class="fas fa-users-slash"></i>
                            </div>
                            <h3 class="text-base font-bold text-gray-800">No se encontraron usuarios</h3>
                            <p class="text-xs text-gray-500 mt-1">Prueba cambiando los términos de búsqueda o filtros seleccionados.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Paginacion -->
<div class="py-4 px-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white">
    <div class="text-xs text-gray-500">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->total() > 0): ?>
            Mostrando <b><?php echo e($users->firstItem()); ?></b> a <b><?php echo e($users->lastItem()); ?></b> de <b><?php echo e($users->total()); ?></b> usuarios
        <?php else: ?>
            Sin resultados
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->hasPages()): ?>
    <nav class="flex items-center gap-1">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->onFirstPage()): ?>
            <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 text-gray-300 text-xs cursor-not-allowed">
                <i class="fas fa-chevron-left"></i>
            </span>
        <?php else: ?>
            <a href="<?php echo e($users->previousPageUrl()); ?>" class="pagination-link w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 text-xs transition-colors cursor-pointer">
                <i class="fas fa-chevron-left"></i>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users->getUrlRange(1, $users->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($page == $users->currentPage()): ?>
                <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-600 text-white text-xs font-bold shadow-sm shadow-blue-500/25">
                    <?php echo e($page); ?>

                </span>
            <?php else: ?>
                <a href="<?php echo e($url); ?>" class="pagination-link w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-600 text-xs font-bold transition-colors cursor-pointer">
                    <?php echo e($page); ?>

                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->hasMorePages()): ?>
            <a href="<?php echo e($users->nextPageUrl()); ?>" class="pagination-link w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 text-xs transition-colors cursor-pointer">
                <i class="fas fa-chevron-right"></i>
            </a>
        <?php else: ?>
            <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 text-gray-300 text-xs cursor-not-allowed">
                <i class="fas fa-chevron-right"></i>
            </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </nav>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/usuarios/partials/table.blade.php ENDPATH**/ ?>