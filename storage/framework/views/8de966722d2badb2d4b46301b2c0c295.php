<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Gestión de Cuentas y Auditorías | AXStore Super Admin']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Gestión de Cuentas y Auditorías | AXStore Super Admin']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="mx-auto max-w-7xl space-y-6 pb-12">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="<?php echo e(route('superadmin.dashboard')); ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 mb-1">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Panel de Gobernanza</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg shrink-0">
                        <i class="fas fa-users-gear"></i>
                    </span>
                    <span>Gestión de Usuarios y Auditoría Segura</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Supervisión de cuentas, roles, permisos y activación de sesiones de suplantación controlada (Secure Impersonation).
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('modalNuevoSuperAdmin').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fas fa-user-shield mr-1.5"></i>
                    Crear Super Admin
                </button>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <form method="GET" action="<?php echo e(route('superadmin.usuarios')); ?>" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[240px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Buscar por nombre o correo electrónico..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div>
                <select name="rol" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="todos">Todos los roles</option>
                    <option value="super_admin" <?php echo e(request('rol') === 'super_admin' ? 'selected' : ''); ?>>Super Admin</option>
                    <option value="admin" <?php echo e(request('rol') === 'admin' ? 'selected' : ''); ?>>Admin</option>
                    <option value="vendedor" <?php echo e(request('rol') === 'vendedor' ? 'selected' : ''); ?>>Vendedor</option>
                </select>
            </div>

            <div>
                <select name="estado" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="todos">Todos los estados</option>
                    <option value="1" <?php echo e(request('estado') === '1' ? 'selected' : ''); ?>>Activos</option>
                    <option value="0" <?php echo e(request('estado') === '0' ? 'selected' : ''); ?>>Inactivos</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition-colors">
                Filtrar
            </button>
        </form>

        <!-- Tabla de Usuarios -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 font-semibold uppercase text-[10px]">
                            <th class="py-3 px-4">Usuario</th>
                            <th class="py-3 px-4">Rol en Sistema</th>
                            <th class="py-3 px-4">Teléfono</th>
                            <th class="py-3 px-4">Estado</th>
                            <th class="py-3 px-4">Fecha Registro</th>
                            <th class="py-3 px-4 text-right">Acciones de Auditoría</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="hover:bg-slate-50/60">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center shrink-0 uppercase text-xs">
                                            <?php echo e(substr($u->nombre_real, 0, 2)); ?>

                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 block leading-tight"><?php echo e($u->nombre_real); ?></span>
                                            <span class="text-[11px] text-slate-400 font-mono"><?php echo e($u->username); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($u->rol === 'super_admin'): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-700">
                                            SUPER ADMIN
                                        </span>
                                    <?php elseif($u->rol === 'admin'): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-100 text-blue-700">
                                            ADMIN
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-700">
                                            VENDEDOR
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-slate-500 font-mono">
                                    <?php echo e($u->telefono ?: '—'); ?>

                                </td>
                                <td class="py-3 px-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($u->estado == 1): ?>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Inactivo
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">
                                    <?php echo e($u->created_at->format('d/m/Y')); ?>

                                </td>
                                <td class="py-3 px-4 text-right">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$u->isSuperAdmin()): ?>
                                        <button type="button" onclick="abrirModalAuditoria(<?php echo e($u->id); ?>, '<?php echo e(addslashes($u->nombre_real)); ?>', '<?php echo e($u->username); ?>')" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-purple-50 hover:bg-purple-100 text-purple-700 transition-colors inline-flex items-center gap-1.5">
                                            <i class="fas fa-user-secret"></i>
                                            <span>Auditar Cuenta</span>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-[11px] text-slate-400 italic">No auditable</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-xs text-slate-400">
                                    No se encontraron usuarios con los criterios de búsqueda.
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                <?php echo e($users->links()); ?>

            </div>
        </div>

    </div>

    <!-- Modal para Iniciar Auditoría Segura (Secure Impersonation) -->
    <div id="modalAuditoria" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
            <div class="p-6 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-base">
                        <i class="fas fa-user-shield"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-black">Iniciar Auditoría Segura</h3>
                        <p class="text-xs text-slate-400">Suplantación administrativa trazable</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modalAuditoria').classList.add('hidden')" class="text-slate-400 hover:text-white">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="formAuditoria" method="POST" action="" class="p-6 space-y-4 text-xs">
                <?php echo csrf_field(); ?>
                <div class="p-3 bg-purple-50 rounded-xl border border-purple-200 text-purple-900">
                    <span class="font-bold block">Cuenta a auditar:</span>
                    <span id="auditarNombreUsuario" class="text-sm font-black text-purple-950"></span>
                    <span id="auditarEmailUsuario" class="block text-slate-500 text-[11px] font-mono"></span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Justificación de la Auditoría <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="justificacion" required rows="3" placeholder="Describe el motivo formal (ej: Investigación de discrepancia en reporte de comisiones ticket #TK-84920)..." class="w-full p-3 rounded-xl border border-slate-200 focus:outline-none focus:border-purple-500 text-xs"></textarea>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Quedará registrado permanentemente en la bitácora inmutable.</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Duración Máxima</label>
                        <select name="duracion" class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-purple-500 text-xs">
                            <option value="15" selected>15 Minutos</option>
                            <option value="30">30 Minutos</option>
                            <option value="60">1 Hora (Extendido)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Modo de Operación</label>
                        <select name="solo_lectura" class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-purple-500 text-xs">
                            <option value="1" selected>Solo Lectura (Seguro)</option>
                            <option value="0">Permisivo</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Confirmación con Contraseña de Super Admin <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="mfa_password" required placeholder="Ingresa tu contraseña actual para autorizar" class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-purple-500 text-xs">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalAuditoria').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold transition-colors">
                        Iniciar Sesión de Auditoría
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Crear Nuevo Super Admin -->
    <div id="modalNuevoSuperAdmin" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
            <div class="p-6 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-base">
                        <i class="fas fa-shield-halved"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-black">Alta de Super Administrador</h3>
                        <p class="text-xs text-slate-400">Creación de cuenta con máximo nivel de privilegios</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modalNuevoSuperAdmin').classList.add('hidden')" class="text-slate-400 hover:text-white">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="<?php echo e(route('superadmin.crear')); ?>" class="p-6 space-y-3.5 text-xs">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nombre Completo <span class="text-rose-500">*</span></label>
                    <input type="text" name="nombre_real" required placeholder="Nombre y Apellido" class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Correo Institucional <span class="text-rose-500">*</span></label>
                    <input type="email" name="username" required placeholder="superadmin@axstore.com" class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Contraseña (Mín. 10 car.) <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Confirmar Contraseña <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" required class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <label class="block font-bold text-slate-700 mb-1">
                        Tu contraseña actual de Super Admin para autorizar <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="auth_password" required placeholder="Contraseña de autorización" class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-xs">
                </div>

                <div class="pt-3 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalNuevoSuperAdmin').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition-colors">
                        Crear Super Admin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalAuditoria(userId, nombre, email) {
            document.getElementById('auditarNombreUsuario').textContent = nombre;
            document.getElementById('auditarEmailUsuario').textContent = email;
            document.getElementById('formAuditoria').action = `/superadmin/usuarios/${userId}/auditar`;
            document.getElementById('modalAuditoria').classList.remove('hidden');
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

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/superadmin/usuarios.blade.php ENDPATH**/ ?>