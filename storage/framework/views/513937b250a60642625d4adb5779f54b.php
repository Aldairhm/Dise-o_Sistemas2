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

    <div class="mx-auto max-w-7xl space-y-6 pb-12" x-data="gestionHistorialAuditorias()">
        
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

        <!-- Skeleton Loading Inteligente para Historial de Auditorías -->
        <p x-show="errorCarga" x-cloak x-text="errorCarga" role="alert" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700"></p>
        <div x-show="cargandoAuditorias" style="display: none;" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-2">
            <?php if (isset($component)) { $__componentOriginalb3a7e296665e2f58ea67cad97892cbe7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb3a7e296665e2f58ea67cad97892cbe7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.skeleton-table','data' => ['rows' => 5,'cols' => 8,'avatarCol' => null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('skeleton-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rows' => 5,'cols' => 8,'avatarCol' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(null)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb3a7e296665e2f58ea67cad97892cbe7)): ?>
<?php $attributes = $__attributesOriginalb3a7e296665e2f58ea67cad97892cbe7; ?>
<?php unset($__attributesOriginalb3a7e296665e2f58ea67cad97892cbe7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb3a7e296665e2f58ea67cad97892cbe7)): ?>
<?php $component = $__componentOriginalb3a7e296665e2f58ea67cad97892cbe7; ?>
<?php unset($__componentOriginalb3a7e296665e2f58ea67cad97892cbe7); ?>
<?php endif; ?>
        </div>

        <!-- Tabla de Auditorías -->
        <div id="contenedorTablaHistorialAuditorias" x-show="!cargandoAuditorias" :aria-busy="cargandoAuditorias" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <?php echo $__env->make('superadmin.tabla-historial-auditorias', ['auditorias' => $auditorias], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

    </div>

    <script>
        function gestionHistorialAuditorias() {
            return {
                cargandoAuditorias: false,
                solicitudCarga: 0,
                errorCarga: '',

                async cargarAuditorias(urlDestino) {
                    const solicitud = ++this.solicitudCarga;
                    this.cargandoAuditorias = true;
                    this.errorCarga = '';

                    try {
                        const res = await fetch(urlDestino, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error('No se pudo completar la solicitud.');
                        const data = await res.json();
                        if (solicitud !== this.solicitudCarga) return;
                        if (!data.success || typeof data.html !== 'string') {
                            throw new Error('La respuesta no contiene los datos esperados.');
                        }
                        if (data.success) {
                            window.history.pushState({}, '', urlDestino);
                            const contenedor = document.getElementById('contenedorTablaHistorialAuditorias');
                            if (contenedor) {
                                contenedor.innerHTML = data.html;
                                this.atarPaginacion();
                            }
                        }
                    } catch (err) {
                        if (solicitud !== this.solicitudCarga) return;
                        this.errorCarga = 'No se pudo cargar el historial de auditorías. Intenta de nuevo.';
                        console.error('Error al cargar historial de auditorías:', err);
                    } finally {
                        if (solicitud === this.solicitudCarga) this.cargandoAuditorias = false;
                    }
                },

                atarPaginacion() {
                    const contenedor = document.getElementById('contenedorTablaHistorialAuditorias');
                    if (!contenedor) return;
                    const links = contenedor.querySelectorAll('a[href*="page="]');
                    links.forEach(link => {
                        link.addEventListener('click', async (e) => {
                            e.preventDefault();
                            const href = link.getAttribute('href');
                            if (!href) return;
                            await this.cargarAuditorias(href);
                        });
                    });
                },

                init() {
                    this.atarPaginacion();
                }
            };
        }

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
                    const form = document.getElementById('formRevocar' + id);
                    if (!form) return;
                    if (window.AxLoading?.beginForm?.(form, form.querySelector('button')) === false) return;
                    form.submit();
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