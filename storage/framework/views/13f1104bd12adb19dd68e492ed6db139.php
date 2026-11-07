<div <?php echo e($attributes->merge(['class' => 'p-6 space-y-6 select-none'])); ?> role="status" aria-label="Cargando detalles...">
    <!-- Header Summary KPI Skeletons -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 4; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="bg-slate-100/90 border border-slate-200/90 rounded-xl p-3.5 space-y-2.5 shadow-2xs">
                <div class="h-3.5 w-16 rounded ax-skeleton-shimmer"></div>
                <div class="h-6 w-24 rounded-md ax-skeleton-shimmer"></div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    <!-- Client & Order Info Box Skeleton -->
    <div class="bg-slate-100/90 border border-slate-200/90 rounded-2xl p-4 flex flex-col md:flex-row gap-4 justify-between items-start md:items-center shadow-2xs">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl ax-skeleton-shimmer flex-shrink-0"></div>
            <div class="space-y-2">
                <div class="h-4.5 w-40 rounded ax-skeleton-shimmer"></div>
                <div class="h-3.5 w-52 rounded ax-skeleton-shimmer"></div>
            </div>
        </div>
        <div class="flex gap-2">
            <div class="h-7 w-20 rounded-full ax-skeleton-shimmer"></div>
            <div class="h-7 w-24 rounded-full ax-skeleton-shimmer"></div>
        </div>
    </div>

    <!-- Products / Items Table Skeleton -->
    <div class="border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-xs">
        <div class="p-3.5 bg-slate-100/90 border-b border-slate-200 flex items-center justify-between">
            <div class="h-4 w-32 rounded ax-skeleton-shimmer"></div>
            <div class="h-4 w-16 rounded ax-skeleton-shimmer"></div>
        </div>
        <div class="divide-y divide-slate-100 p-2 space-y-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 3; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="p-3 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl ax-skeleton-shimmer flex-shrink-0"></div>
                        <div class="space-y-2">
                            <div class="h-4 w-44 rounded ax-skeleton-shimmer"></div>
                            <div class="h-3 w-28 rounded ax-skeleton-shimmer"></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="h-4 w-12 rounded ax-skeleton-shimmer"></div>
                        <div class="h-4 w-16 rounded ax-skeleton-shimmer"></div>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>

    <!-- Voucher / Totals Footer Skeleton -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-3 border-t border-slate-200">
        <div class="h-10 w-36 rounded-xl ax-skeleton-shimmer"></div>
        <div class="space-y-2.5 w-full sm:w-52">
            <div class="flex justify-between">
                <div class="h-3.5 w-20 rounded ax-skeleton-shimmer"></div>
                <div class="h-3.5 w-16 rounded ax-skeleton-shimmer"></div>
            </div>
            <div class="flex justify-between items-baseline">
                <div class="h-4 w-24 rounded ax-skeleton-shimmer"></div>
                <div class="h-6 w-20 rounded ax-skeleton-shimmer"></div>
            </div>
        </div>
    </div>
    <span class="sr-only">Cargando detalles...</span>
</div>

<?php /**PATH C:\laragon\www\diseno\resources\views/components/skeleton-modal-detalle.blade.php ENDPATH**/ ?>