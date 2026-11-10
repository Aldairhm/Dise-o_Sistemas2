<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app','data' => ['title' => 'Confirmar Contraseña | AXStore']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Confirmar Contraseña | AXStore']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <div class="max-w-md mx-auto mt-10">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-8">

            
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 rounded-2xl bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-shield-halved text-2xl text-amber-600"></i>
                </div>
            </div>

            <h2 class="text-xl font-black text-center text-slate-900 mb-2">Confirmar Contraseña</h2>
            <p class="text-xs text-slate-500 text-center mb-6">
                Esta es un área segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.
            </p>

            <form data-loading-form data-loading-label="Confirmando..." method="POST" action="<?php echo e(route('password.confirm')); ?>">
                <?php echo csrf_field(); ?>

                <div class="mb-5">
                    <label for="password" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
                        Contraseña
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           autofocus
                           autocomplete="current-password"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                           placeholder="Ingresa tu contraseña">

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs text-red-500 mt-2 font-semibold"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <button type="submit"
                        class="w-full px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-widest shadow-lg shadow-blue-600/20 transition-all duration-200">
                    <i class="fas fa-lock-open mr-2"></i>
                    Confirmar y Continuar
                </button>
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

<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/auth/confirm-password.blade.php ENDPATH**/ ?>