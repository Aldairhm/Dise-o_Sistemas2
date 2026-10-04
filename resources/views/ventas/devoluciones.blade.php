<x-app title="Devoluciones de clientes | AXStore">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('ventas.index') }}" class="mb-4 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600"><i class="fas fa-arrow-left"></i> Volver a ventas</a>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600">Módulo de Ventas</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">Devoluciones de clientes</h1>
                <p class="mt-2 text-sm text-slate-500">Consulta los retornos registrados desde ventas entregadas.</p>
            </div>
            <div class="flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 shadow-sm"><i class="fas fa-rotate-left text-blue-600"></i><span class="text-sm font-bold text-blue-800">{{ $devoluciones->total() }} registros</span></div>
        </div>

        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones de ventas">
            <a href="{{ route('ventas.index') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-chart-line mr-2 text-slate-400"></i>Dashboard Histórico</a>
            <a href="{{ route('ventas.create') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-cash-register mr-2 text-slate-400"></i>Terminal de Ventas (Carrito)</a>
            <a href="{{ route('ventas.pedidos') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-boxes-packing mr-2 text-slate-400"></i>Control de Envíos y Estados</a>
            <a href="{{ route('ventas.devoluciones') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm"><i class="fas fa-rotate-left mr-2"></i>Devoluciones de clientes</a>
        </nav>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4"><h2 class="text-base font-black text-slate-900">Historial de devoluciones de clientes</h2><p class="mt-1 text-xs text-slate-500">Solo se muestran devoluciones cuyo origen es una venta.</p></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-white text-[10px] font-black uppercase tracking-widest text-slate-500"><tr><th class="px-5 py-4">Ticket / fecha</th><th class="px-5 py-4">Venta</th><th class="px-5 py-4">Resolución</th><th class="px-5 py-4 text-right">Impacto financiero</th><th class="px-5 py-4">Motivo</th><th class="px-5 py-4 text-right">Evidencia</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($devoluciones as $devolucion)
                            @php $resolucion = ucfirst(str_replace('_', ' ', $devolucion->tipo_resolucion ?? 'Sin resolución')); @endphp
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-5 py-4"><p class="font-mono text-xs font-black text-slate-800">DEV-{{ str_pad($devolucion->id, 5, '0', STR_PAD_LEFT) }}</p><p class="mt-1 text-xs text-slate-500">{{ optional($devolucion->created_at)->format('d/m/Y H:i') }}</p></td>
                                <td class="px-5 py-4"><span class="inline-flex rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-bold text-blue-700"><i class="fas fa-receipt mr-1.5"></i>Venta #{{ $devolucion->origen_id }}</span></td>
                                <td class="px-5 py-4"><span class="rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-bold capitalize text-slate-700">{{ $resolucion }}</span></td>
                                <td class="whitespace-nowrap px-5 py-4 text-right font-black {{ (float) $devolucion->monto_reembolsado > 0 ? 'text-red-600' : 'text-slate-500' }}">${{ number_format((float) $devolucion->monto_reembolsado, 2) }}</td>
                                <td class="max-w-[300px] px-5 py-4"><span class="block truncate text-xs text-slate-600" title="{{ $devolucion->motivo }}">{{ \Illuminate\Support\Str::limit($devolucion->motivo, 60) }}</span></td>
                                <td class="px-5 py-4 text-right">@if($devolucion->comprobante)<a href="{{ asset('storage/' . $devolucion->comprobante) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 hover:bg-blue-600 hover:text-white"><i class="fas fa-paperclip"></i> Ver evidencia</a>@else<span class="text-xs text-slate-400">Sin evidencia</span>@endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-16 text-center"><i class="fas fa-folder-open mb-3 block text-2xl text-slate-300"></i><p class="text-sm font-bold text-slate-700">No hay devoluciones de clientes</p><p class="mt-1 text-xs text-slate-400">Las devoluciones creadas desde el historial de ventas aparecerán aquí.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($devoluciones->hasPages())<div class="border-t border-slate-100 bg-slate-50/50 p-4">{{ $devoluciones->links() }}</div>@endif
        </section>
    </div>
</x-app>
