<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Respaldos de Base de Datos | AXStore']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Respaldos de Base de Datos | AXStore']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <div class="max-w-[1440px] mx-auto space-y-6"
        x-data="backupManager()">

        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="<?php echo e(route('home')); ?>"
                    class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-lg shadow-emerald-600/20">
                        <i class="fas fa-database text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-black uppercase tracking-[0.18em] text-emerald-600">Seguridad</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700">
                                MySQL
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                            Respaldos de Base de Datos
                        </h1>
                    </div>
                </div>
            </div>

            
            <div class="flex flex-wrap items-center gap-3">
                
                <button type="button"
                    @click="openRestoreModal('upload')"
                    class="inline-flex items-center gap-2.5 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs uppercase tracking-widest shadow-lg shadow-amber-500/20 transition-all duration-200 cursor-pointer">
                    <i class="fas fa-rotate-left text-sm"></i>
                    <span>Restaurar Base de Datos</span>
                </button>

                
                <div x-data="{ loading: false }" @pageshow.window="loading = false">
                    <form action="<?php echo e(route('backups.create')); ?>" method="POST"
                        @submit="if (loading) { $event.preventDefault(); } else { loading = true; }">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                            :disabled="loading"
                            class="inline-flex items-center gap-2.5 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-widest shadow-lg shadow-emerald-600/20 transition-all duration-200 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                            <template x-if="!loading">
                                <span class="flex items-center gap-2.5">
                                    <i class="fas fa-plus-circle text-sm"></i>
                                    Generar Nuevo Respaldo
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center gap-2.5">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Generando respaldo...
                                </span>
                            </template>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
            
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="h-1 w-full bg-blue-600 rounded-full -mt-5 mb-4 -mx-0 rounded-t-2xl" style="margin-left:-1.25rem; margin-right:-1.25rem; margin-top:-1.25rem; width:calc(100% + 2.5rem); border-radius: 1rem 1rem 0 0;"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Respaldos</p>
                        <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1"><?php echo e(count($files)); ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-archive text-blue-600"></i>
                    </div>
                </div>
            </div>

            
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="h-1 w-full bg-emerald-600 rounded-full -mt-5 mb-4 -mx-0 rounded-t-2xl" style="margin-left:-1.25rem; margin-right:-1.25rem; margin-top:-1.25rem; width:calc(100% + 2.5rem); border-radius: 1rem 1rem 0 0;"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Último Respaldo</p>
                        <p class="text-sm font-bold text-slate-900 mt-1">
                            <?php echo e(count($files) > 0 ? $files[0]['date_human'] : 'Sin respaldos'); ?>

                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-clock text-emerald-600"></i>
                    </div>
                </div>
            </div>

            
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="h-1 w-full bg-amber-500 rounded-full -mt-5 mb-4 -mx-0 rounded-t-2xl" style="margin-left:-1.25rem; margin-right:-1.25rem; margin-top:-1.25rem; width:calc(100% + 2.5rem); border-radius: 1rem 1rem 0 0;"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Espacio Total</p>
                        <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
                            <?php
                            $totalBytes = collect($files)->sum('size_raw');
                            $units = ['B', 'KB', 'MB', 'GB'];
                            $pow = $totalBytes > 0 ? floor(log($totalBytes) / log(1024)) : 0;
                            $pow = min($pow, count($units) - 1);
                            ?>
                            <?php echo e(round($totalBytes / pow(1024, $pow), 2)); ?> <?php echo e($units[$pow]); ?>

                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                        <i class="fas fa-hdd text-amber-500"></i>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

            
            <div class="px-5 py-4 border-b border-slate-200/80 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                        <i class="fas fa-list text-slate-500 text-xs"></i>
                    </div>
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Historial de Respaldos</h2>
                </div>
                <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">
                    <?php echo e(count($files)); ?> <?php echo e(count($files) === 1 ? 'archivo' : 'archivos'); ?>

                </span>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($files) > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80">
                            <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">#</th>
                            <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Archivo</th>
                            <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tamaño</th>
                            <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-5 py-3 text-center text-[11px] font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-slate-50/60 transition-colors text-xs text-slate-700">
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-slate-100 text-[10px] font-bold text-slate-500">
                                    <?php echo e($index + 1); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-file-zipper text-emerald-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-xs"><?php echo e($file['name']); ?></p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Base de datos MySQL (.zip)</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold">
                                    <i class="fas fa-weight-hanging text-[9px]"></i>
                                    <?php echo e($file['size']); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-slate-700"><?php echo e($file['date']); ?></p>
                                <p class="text-[10px] text-slate-400 mt-0.5"><?php echo e($file['date_human']); ?></p>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    
                                    <a href="<?php echo e(route('backups.download', $file['name'])); ?>"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold shadow-sm transition-all duration-200"
                                        title="Descargar archivo .zip">
                                        <i class="fas fa-download text-[10px]"></i>
                                        <span>Descargar</span>
                                    </a>

                                    
                                    <button type="button"
                                        @click="openRestoreModal('existing', '<?php echo e($file['name']); ?>')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-bold shadow-sm transition-all duration-200 cursor-pointer"
                                        title="Restaurar este respaldo en la base de datos">
                                        <i class="fas fa-rotate-left text-[10px]"></i>
                                        <span>Restaurar</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            
            <div class="px-5 py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-database text-2xl text-slate-300"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-500 mb-1">No hay respaldos aún</h3>
                <p class="text-xs text-slate-400">
                    Genera tu primer respaldo de base de datos haciendo clic en el botón
                    <span class="font-bold text-emerald-600">"Generar Nuevo Respaldo"</span>.
                </p>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-info-circle text-blue-600 text-xs"></i>
                    </div>
                    <div class="text-xs text-slate-500 space-y-1">
                        <p class="font-bold text-slate-700">Política de retención</p>
                        <p>• Se conserva <strong class="text-slate-700">1 respaldo por día</strong> durante los últimos 7 días.</p>
                        <p>• Se conserva <strong class="text-slate-700">1 respaldo por semana</strong> durante las últimas 8 semanas.</p>
                        <p>• Los respaldos antiguos se eliminan automáticamente según esta política.</p>
                        <p>• Sincronización automática simultánea con Google Drive.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-shield-halved text-amber-600 text-xs"></i>
                    </div>
                    <div class="text-xs text-slate-500 space-y-1">
                        <p class="font-bold text-slate-700">Restauración de la Base de Datos</p>
                        <p>• Puedes restaurar cualquier respaldo del historial con un solo clic.</p>
                        <p>• También puedes subir archivos comprimidos <strong class="text-slate-700">.zip</strong> de hasta 100MB.</p>
                        <p>• Se exige la confirmación con tu contraseña de administrador para prevenir accidentes.</p>
                        <p>• Cada operación queda registrada de forma auditable en la bitácora del sistema.</p>
                    </div>
                </div>
            </div>
        </div>

        
        <div x-show="restoreModalOpen"
            x-cloak
            class="fixed inset-0 z-[80] overflow-y-auto"
            style="display: none;"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            
            <div x-show="restoreModalOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                @click="if(!restoreLoading) restoreModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="restoreModalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="backup-restore-modal relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-200/80 p-0">

                    
                    <form id="restoreForm"
                        action="<?php echo e(route('backups.restore')); ?>"
                        method="POST"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>

                        
                        <div class="px-6 pt-6 sm:px-7 sm:pt-7">
                            <div class="flex items-start justify-between gap-4 pb-5 border-b border-slate-100">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-50 text-amber-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <i class="fas fa-triangle-exclamation text-xl"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-600">Acción Crítica</span>
                                    <h3 class="text-lg font-black text-slate-900" id="modal-title">
                                        Restaurar Base de Datos
                                    </h3>
                                </div>
                            </div>
                            <button type="button"
                                :disabled="restoreLoading"
                                @click="restoreModalOpen = false"
                                class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition-colors">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                        </div>

                        
                        <div class="mx-6 mt-5 sm:mx-7 p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs text-amber-900 leading-relaxed shadow-sm">
                            <p class="font-bold mb-1 flex items-center gap-1.5">
                                <i class="fas fa-triangle-exclamation text-amber-600"></i>
                                ¡Advertencia importante!
                            </p>
                            Esta operación sobrescribirá la base de datos con la información del respaldo seleccionado. Cualquier cambio no respaldado se perderá de forma permanente (las imágenes se encuentran protegidas en el almacenamiento de Supabase).
                        </div>

                        
                        <div class="px-6 mt-5 space-y-4 sm:px-7">
                            
                            <div class="flex items-center p-1 rounded-xl bg-slate-100 text-xs font-bold">
                                <button type="button"
                                    @click="restoreMode = 'existing'"
                                    :class="restoreMode === 'existing' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                    class="flex-1 py-1.5 px-3 rounded-lg transition-all text-center">
                                    <i class="fas fa-server mr-1"></i> Respaldo del Historial
                                </button>
                                <button type="button"
                                    @click="restoreMode = 'upload'"
                                    :class="restoreMode === 'upload' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                    class="flex-1 py-1.5 px-3 rounded-lg transition-all text-center">
                                    <i class="fas fa-upload mr-1"></i> Subir Respaldo (.zip)
                                </button>
                            </div>

                            
                            <template x-if="restoreMode === 'existing'">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Archivo a restaurar:
                                    </label>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($files) > 0): ?>
                                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-file-zipper text-sm"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-black text-slate-900 truncate" x-text="selectedFileName || '<?php echo e($files[0]['name']); ?>'"></p>
                                                <p class="text-[10px] text-slate-400 mt-0.5">Almacenado localmente en el servidor</p>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="pt-1">
                                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">
                                            Cambiar por otro respaldo del historial:
                                        </label>
                                        <select x-model="selectedFileName"
                                            class="w-full text-xs rounded-xl border border-slate-300 bg-white px-3 py-2 font-medium text-slate-700 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <option value="<?php echo e($f['name']); ?>"><?php echo e($f['name']); ?> (<?php echo e($f['date']); ?>) - <?php echo e($f['size']); ?></option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                    </div>

                                    <input type="hidden" name="file_name" :value="selectedFileName || '<?php echo e($files[0]['name']); ?>'">
                                    <?php else: ?>
                                    <div class="p-4 rounded-xl bg-slate-50 text-center text-xs text-slate-500">
                                        No hay respaldos locales disponibles. Usa la opción de <strong>Subir Archivo</strong>.
                                    </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </template>

                            
                            <template x-if="restoreMode === 'upload'">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Selecciona tu respaldo comprimido (.zip):
                                    </label>
                                    <input type="file"
                                        id="backup_file_input"
                                        name="backup_file"
                                        accept=".zip"
                                        :required="restoreMode === 'upload'"
                                        class="block w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 border border-slate-300 rounded-xl cursor-pointer bg-white">
                                    <p class="text-[10px] text-slate-400">
                                        Tamaño máximo admitido: 100MB. Debe ser un archivo .zip generado con Spatie Backup.
                                    </p>
                                </div>
                            </template>

                            
                            <div class="pt-2 border-t border-slate-100 space-y-1.5">
                                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                    Contraseña de Administrador <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input type="password"
                                        name="password"
                                        required
                                        autocomplete="current-password"
                                        placeholder="Ingresa tu contraseña para autorizar"
                                        class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                                </div>
                                <p class="text-[10px] text-slate-400">
                                    Por seguridad corporativa, debes confirmar tu identidad para aplicar la restauración.
                                </p>
                            </div>
                        </div>

                        
                        <div class="mt-6 px-6 pb-6 pt-5 sm:px-7 sm:pb-7 border-t border-slate-100 bg-slate-50/70 flex items-center justify-end gap-3">
                            <button type="button"
                                :disabled="restoreLoading"
                                @click="restoreModalOpen = false"
                                class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors cursor-pointer">
                                Cancelar
                            </button>

                            <button type="button"
                                id="btnConfirmRestore"
                                @click="confirmRestoreAction()"
                                :disabled="restoreLoading"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-amber-500/20 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                                <template x-if="!restoreLoading">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-rotate-left"></i>
                                        <span>Confirmar y Restaurar</span>
                                    </span>
                                </template>
                                <template x-if="restoreLoading">
                                    <span class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        <span>Restaurando sistema...</span>
                                    </span>
                                </template>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    
    <script>
        function backupManager() {
            return {
                restoreModalOpen: false,
                restoreMode: 'upload', // 'existing' | 'upload'
                selectedFileName: <?php echo json_encode($files[0]['name'] ?? '', 15, 512) ?>,
                restoreLoading: false,

                openRestoreModal(mode, fileName = '') {
                    this.restoreMode = mode;
                    if (fileName) {
                        this.selectedFileName = fileName;
                    }
                    this.restoreModalOpen = true;
                },

                confirmRestoreAction() {
                    if (this.restoreLoading) return;
                    const form = document.getElementById('restoreForm');
                    if (!form) return;

                    const passwordInput = form.querySelector('input[name="password"]');
                    if (!passwordInput || !passwordInput.value.trim()) {
                        this.showRestoreToast({
                            icon: 'warning',
                            title: 'Contraseña requerida',
                            text: 'Debes ingresar tu contraseña de administrador para autorizar la restauración.',
                        });
                        passwordInput?.focus();
                        return;
                    }

                    if (this.restoreMode === 'upload') {
                        const fileInput = document.getElementById('backup_file_input');
                        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                            this.showRestoreToast({
                                icon: 'warning',
                                title: 'Archivo requerido',
                                text: 'Por favor selecciona un archivo comprimido .zip de respaldo para restaurar.',
                            });
                            return;
                        }

                        const fileName = fileInput.files[0].name.toLowerCase();
                        if (!fileName.endsWith('.zip')) {
                            this.showRestoreToast({
                                icon: 'error',
                                title: 'Extensión no válida',
                                text: 'El archivo debe tener estrictamente la extensión .zip.',
                            });
                            return;
                        }

                        if (fileInput.files[0].size > 100 * 1024 * 1024) {
                            this.showRestoreToast({
                                icon: 'error',
                                title: 'Archivo demasiado grande',
                                text: 'El archivo de respaldo no debe superar el límite de 100MB.',
                            });
                            return;
                        }
                    }

                    this.restoreModalOpen = false;
                    Swal.fire({
                        title: '¿Confirmar Restauración de la Base de Datos?',
                        html: `
                            <div class="text-left text-xs text-slate-700 space-y-2.5 mt-2 leading-relaxed">
                                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 font-semibold flex items-start gap-2">
                                    <i class="fas fa-triangle-exclamation text-amber-600 mt-0.5 text-sm flex-shrink-0"></i>
                                    <span>¡Acción destructiva e irrecuperable! Se reemplazará de forma permanente la base de datos actual.</span>
                                </div>
                                <p>Al proceder con esta operación:</p>
                                <ul class="list-disc list-inside space-y-1 font-semibold text-slate-800">
                                    <li>La base de datos actual será sobrescrita en su totalidad con el volcado SQL.</li>
                                    <li>Las imágenes y multimedia se mantienen intactas en Supabase S3.</li>
                                </ul>
                                <p class="text-slate-500 pt-1">¿Estás absolutamente seguro de continuar?</p>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d97706',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: '<i class="fas fa-check mr-1.5"></i> Sí, restaurar base de datos',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true,
                        focusCancel: true,
                        allowOutsideClick: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.restoreLoading = true;
                            Swal.fire({
                                title: 'Restaurando base de datos...',
                                html: '<p class="text-xs text-slate-500 mt-1">Por favor espera mientras se extrae el archivo y se ejecuta la restauración del script SQL.<br><strong class="text-amber-600">No recargues ni cierres la página.</strong></p>',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            form.submit();
                        } else {
                            this.restoreModalOpen = true;
                        }
                    });
                },

                showRestoreToast(options) {
                    Swal.fire({
                        ...options,
                        toast: true,
                        position: 'top-end',
                        timer: 4200,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'backup-toast',
                        },
                    });
                }
            };
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
<?php endif; ?><?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/backups/index.blade.php ENDPATH**/ ?>