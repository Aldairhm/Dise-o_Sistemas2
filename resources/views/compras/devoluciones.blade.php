<x-app title="Devoluciones a proveedores | AXStore">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('compras.historial') }}" class="mb-4 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600"><i class="fas fa-arrow-left"></i> Volver a compras</a>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-amber-600">Módulo de Compras</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">Devoluciones a proveedores</h1>
                <p class="mt-2 text-sm text-slate-500">Revisa los tickets pendientes y aplica su resolución.</p>
            </div>
            <div class="flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 shadow-sm"><i class="fas fa-ticket text-amber-600"></i><span class="text-sm font-bold text-amber-800">{{ $devoluciones->total() }} tickets</span></div>
        </div>

        <nav class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" aria-label="Secciones de compras">
            <a href="{{ route('compras.create') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-cart-plus mr-2"></i>Nueva compra</a>
            <a href="{{ route('compras.historial') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-clock-rotate-left mr-2"></i>Historial</a>
            <a href="{{ route('compras.movimientos') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-warehouse mr-2"></i>Gestión de Inventario</a>
            <a href="{{ route('compras.devoluciones') }}" class="rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm"><i class="fas fa-truck-arrow-right mr-2"></i>Devoluciones a proveedores</a>
        </nav>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4"><h2 class="text-base font-black text-slate-900">Tickets registrados</h2><p class="mt-1 text-xs text-slate-500">El inventario y el monto solo cambian al resolver un ticket pendiente.</p></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-white text-[10px] font-black uppercase tracking-widest text-slate-500"><tr><th class="px-5 py-4">Ticket / fecha</th><th class="px-5 py-4">Compra</th><th class="px-5 py-4">Ubicación</th><th class="px-5 py-4">Motivo</th><th class="px-5 py-4">Estado</th><th class="px-5 py-4 text-right">Evidencia</th><th class="px-5 py-4 text-right">Acción</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($devoluciones as $devolucion)
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-5 py-4"><p class="font-mono text-xs font-black text-slate-800">DEV-{{ str_pad($devolucion->id, 5, '0', STR_PAD_LEFT) }}</p><p class="mt-1 text-xs text-slate-500">{{ optional($devolucion->created_at)->format('d/m/Y H:i') }}</p></td>
                                <td class="px-5 py-4"><span class="inline-flex rounded-lg border border-violet-200 bg-violet-50 px-2.5 py-1.5 text-xs font-bold text-violet-700"><i class="fas fa-cart-shopping mr-1.5"></i>Compra #{{ $devolucion->origen_id }}</span></td>
                                <td class="px-5 py-4"><span class="rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-bold capitalize text-slate-700">{{ $devolucion->ubicacion_falla ?? 'Sin ubicación' }}</span></td>
                                <td class="max-w-[300px] px-5 py-4"><span class="block truncate text-xs text-slate-600" title="{{ $devolucion->motivo }}">{{ \Illuminate\Support\Str::limit($devolucion->motivo, 60) }}</span></td>
                                <td class="px-5 py-4"><span class="rounded-lg border px-2.5 py-1.5 text-xs font-bold {{ $devolucion->estado === 'pendiente' ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">{{ ucfirst($devolucion->estado ?? 'completado') }}</span></td>
                                <td class="px-5 py-4 text-right">@if($devolucion->comprobante)<a href="{{ asset('storage/' . $devolucion->comprobante) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 hover:bg-blue-600 hover:text-white"><i class="fas fa-paperclip"></i> Ver archivo</a>@else<span class="text-xs text-slate-400">Sin evidencia</span>@endif</td>
                                <td class="px-5 py-4 text-right">@if($devolucion->estado === 'pendiente')<button type="button" onclick="document.getElementById('resolver-proveedor-{{ $devolucion->id }}').showModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-black text-white hover:bg-amber-600"><i class="fas fa-check"></i> Resolver</button><dialog id="resolver-proveedor-{{ $devolucion->id }}" class="m-auto w-[min(440px,calc(100vw-2rem))] rounded-2xl border border-slate-200 bg-white p-0 text-left shadow-2xl backdrop:bg-slate-900/50"><form method="POST" action="{{ route('devoluciones.compra.resolver', $devolucion->id) }}" class="p-5">@csrf @method('PATCH')<div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3"><div><p class="text-xs font-black uppercase tracking-widest text-amber-600">Resolver ticket</p><h3 class="text-lg font-black text-slate-900">DEV-{{ str_pad($devolucion->id, 5, '0', STR_PAD_LEFT) }}</h3></div><button type="button" onclick="this.closest('dialog').close()" class="h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-100"><i class="fas fa-xmark"></i></button></div><p class="mb-4 text-xs text-slate-500">La falla está registrada en <strong>{{ $devolucion->ubicacion_falla }}</strong>.</p><label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Resolución final<select name="resolucion_final" required class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm"><option value="" disabled selected>Selecciona una opción</option><option value="cambio_fisico">Cambio físico</option><option value="reembolso">Reembolso</option><option value="merma">Merma / pérdida</option></select></label><div class="mt-5 flex justify-end gap-2"><button type="button" onclick="this.closest('dialog').close()" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600">Cancelar</button><button type="submit" class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-black text-white">Aplicar resolución</button></div></form></dialog>@else<span class="text-xs font-medium text-slate-400">Sin acciones</span>@endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-16 text-center"><i class="fas fa-ticket mb-3 block text-2xl text-slate-300"></i><p class="text-sm font-bold text-slate-700">No hay tickets de proveedores</p><p class="mt-1 text-xs text-slate-400">Créalo desde Historial de compras con el botón Devolver.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($devoluciones->hasPages())<div class="border-t border-slate-100 bg-slate-50/50 p-4">{{ $devoluciones->links() }}</div>@endif
        </section>
    </div>
</x-app>
