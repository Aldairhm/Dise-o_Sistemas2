<x-app title="Historial de compras | AXStore">
    <div class="max-w-[1440px] mx-auto space-y-6">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 mb-4"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600 mb-1">Abastecimiento</p>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Compras</h1>
                <p class="text-sm text-slate-500 mt-2">Consulta las recepciones registradas y su estado.</p>
            </div>
        </div>

        <nav class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" aria-label="Secciones de compras">
            <a href="{{ route('compras.create') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-cart-plus mr-2"></i>Nueva compra</a>
            <a href="{{ route('compras.historial') }}" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm"><i class="fas fa-clock-rotate-left mr-2"></i>Historial</a>
            <a href="{{ route('compras.movimientos') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-warehouse mr-2"></i>Bodega a tienda</a>
        </nav>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/60 p-5">
                <div><h2 class="text-lg font-black text-slate-900">Compras registradas</h2><p class="text-xs text-slate-500 mt-1">{{ $compras->total() }} recepción(es) encontradas.</p></div>
                <i class="fas fa-file-invoice-dollar text-xl text-blue-600"></i>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-[10px] uppercase tracking-widest text-slate-500"><tr><th class="px-5 py-4">Fecha</th><th class="px-5 py-4">Proveedor</th><th class="px-5 py-4">Referencia</th><th class="px-5 py-4 text-center">Líneas</th><th class="px-5 py-4 text-right">Total</th><th class="px-5 py-4">Estado</th><th class="px-5 py-4"></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($compras as $compra)
                            <tr class="hover:bg-slate-50/60"><td class="px-5 py-4 font-semibold text-slate-700">{{ $compra->fecha_compra?->format('d/m/Y') }}</td><td class="px-5 py-4 font-bold text-slate-800">{{ $compra->proveedor?->nombre ?? 'Sin proveedor' }}</td><td class="px-5 py-4 text-slate-500">{{ $compra->referencia ?: 'Sin referencia' }}</td><td class="px-5 py-4 text-center text-slate-600">{{ $compra->detalles_count }}</td><td class="px-5 py-4 text-right font-black text-slate-800">${{ number_format((float) $compra->total, 2) }}</td><td class="px-5 py-4"><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold capitalize text-emerald-700">{{ $compra->estado }}</span></td><td class="px-5 py-4 text-right"><button type="button" onclick="document.getElementById('compra-detalle-{{ $compra->id }}').showModal()" class="text-xs font-bold text-blue-600 hover:text-blue-800"><i class="fas fa-eye mr-1"></i>Ver detalle</button><dialog id="compra-detalle-{{ $compra->id }}" class="m-auto w-[min(680px,calc(100vw-2rem))] max-w-none rounded-2xl border border-slate-200 bg-white p-0 text-left shadow-2xl backdrop:bg-slate-900/50"><div class="p-5"><div class="mb-4 flex items-start justify-between gap-4 border-b border-slate-100 pb-4"><div><p class="text-xs font-black uppercase tracking-wider text-blue-600">Detalle de recepción</p><p class="mt-1 text-sm font-bold text-slate-800">{{ $compra->proveedor?->nombre ?? 'Sin proveedor' }} · {{ $compra->fecha_compra?->format('d/m/Y') }}</p></div><button type="button" onclick="this.closest('dialog').close()" class="h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Cerrar"><i class="fas fa-xmark"></i></button></div><div class="mb-4 flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3"><span class="text-xs font-bold text-slate-500">Total de la recepción</span><span class="text-xl font-black text-slate-900">${{ number_format((float) $compra->total, 2) }}</span></div><p class="mb-4 text-xs text-slate-500">{{ $compra->observaciones ?: 'Sin observaciones' }}</p><div class="space-y-2">@foreach ($compra->detalles as $detalle) @php $imagen = $detalle->variante?->imagenes?->firstWhere('es_principal', 1) ?? $detalle->variante?->imagenes?->first(); @endphp<div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2"><div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-white">@if ($imagen)<img src="{{ asset('storage/' . $imagen->ruta_imagen) }}" alt="{{ $detalle->variante?->nombre_variante }}" class="h-full w-full object-cover">@else <div class="flex h-full items-center justify-center text-slate-300"><i class="fas fa-image"></i></div>@endif</div><div class="min-w-0 flex-1"><p class="truncate text-xs font-black text-slate-800">{{ $detalle->variante?->producto?->nombre ?? 'Producto' }}</p><p class="truncate text-xs text-slate-500">{{ $detalle->variante?->nombre_variante }}</p><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">SKU: {{ $detalle->variante?->sku ?: 'Sin SKU' }}</p></div><div class="shrink-0 text-right text-xs"><p class="font-bold text-slate-700">{{ $detalle->cantidad }} × ${{ number_format((float) $detalle->precio_unitario, 2) }}</p><p class="font-black text-slate-900">${{ number_format((float) $detalle->subtotal, 2) }}</p></div></div>@endforeach</div><div class="mt-4 border-t border-slate-100 pt-3"><div class="mb-2 flex items-center justify-between"><p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Movimientos generados</p><a href="{{ route('compras.movimientos') }}" class="text-[10px] font-bold text-blue-600 hover:text-blue-800">Ver todos</a></div>@forelse ($compra->movimientos as $movimiento)<div class="flex items-center justify-between gap-3 text-xs text-slate-600"><span>{{ $movimiento->tipo === 'compra_recibida' ? 'Entrada a bodega' : 'Transferencia a tienda' }}</span><span>{{ $movimiento->cantidad }} unidades · {{ $movimiento->created_at?->format('d/m/Y H:i') }}</span></div>@empty<p class="text-xs text-slate-400">Sin movimientos asociados.</p>@endforelse</div></div></dialog></td></tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-16 text-center text-sm text-slate-400"><i class="fas fa-inbox mb-3 block text-2xl"></i>Aún no hay compras registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($compras->hasPages()) <div class="border-t border-slate-100 p-4">{{ $compras->links() }}</div> @endif
        </section>
    </div>
</x-app>
