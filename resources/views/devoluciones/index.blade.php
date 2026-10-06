<x-app title="Auditoría de Devoluciones | AXStore">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('home') }}" class="mb-4 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 transition-colors hover:text-blue-600">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600">Control y trazabilidad</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">Auditoría de Devoluciones</h1>
                <p class="mt-2 text-sm text-slate-500">Consulta el origen, resolución, impacto financiero y evidencia de cada devolución.</p>
            </div>
            <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <i class="fas fa-file-invoice text-blue-600"></i>
                <span class="text-sm font-bold text-slate-700">{{ $devoluciones->total() }} registros</span>
            </div>
        </div>

        <nav class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" aria-label="Secciones de auditoría">
            <a href="{{ route('ventas.index') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 transition-colors hover:bg-slate-50 hover:text-blue-600">
                <i class="fas fa-chart-line mr-2 text-slate-400"></i>Ventas
            </a>
            <a href="{{ route('compras.historial') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 transition-colors hover:bg-slate-50 hover:text-blue-600">
                <i class="fas fa-cart-shopping mr-2 text-slate-400"></i>Compras
            </a>
            <a href="{{ route('devoluciones.index') }}" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm">
                <i class="fas fa-rotate-left mr-2"></i>Auditoría de devoluciones
            </a>
        </nav>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fas fa-list-check"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-900">Registro cronológico</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Las ventas y compras se identifican por su origen, sin alterar sus registros originales.</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-white text-[10px] font-black uppercase tracking-widest text-slate-500">
                        <tr>
                            <th class="px-5 py-4">ID / Fecha</th>
                            <th class="px-5 py-4">Origen</th>
                            <th class="px-5 py-4">Estado</th>
                            <th class="px-5 py-4">Resolución</th>
                            <th class="px-5 py-4 text-right">Impacto financiero</th>
                            <th class="px-5 py-4">Motivo</th>
                            <th class="px-5 py-4 text-right">Evidencia</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($devoluciones as $devolucion)
                            @php
                                $resolucion = str_replace('_', ' ', $devolucion->tipo_resolucion ?? 'Sin resolución');
                                $resolucion = ucfirst($resolucion);
                                $esVenta = $devolucion->origen_tipo === 'venta';
                            @endphp
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="font-mono text-xs font-black text-slate-800">#DEV-{{ str_pad($devolucion->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ optional($devolucion->created_at)->format('d/m/Y H:i') }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-bold {{ $esVenta ? 'border-blue-200 bg-blue-50 text-blue-700' : 'border-violet-200 bg-violet-50 text-violet-700' }}">
                                        <i class="fas {{ $esVenta ? 'fa-receipt' : 'fa-cart-shopping' }} text-[10px]"></i>
                                        {{ $esVenta ? 'Venta' : 'Compra' }} #{{ $devolucion->origen_id }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-lg border px-2.5 py-1.5 text-xs font-bold {{ $devolucion->estado === 'pendiente' ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">
                                        {{ ucfirst($devolucion->estado ?? 'completado') }}
                                    </span>
                                    @if (!$esVenta && ($devolucion->estado ?? null) === 'pendiente')
                                        <button type="button" onclick="document.getElementById('resolver-devolucion-{{ $devolucion->id }}').showModal()" class="mt-2 inline-flex items-center gap-1 rounded-lg bg-amber-500 px-2.5 py-1.5 text-[11px] font-bold text-white hover:bg-amber-600"><i class="fas fa-check"></i> Resolver</button>
                                        <dialog id="resolver-devolucion-{{ $devolucion->id }}" class="m-auto w-[min(440px,calc(100vw-2rem))] rounded-2xl border border-slate-200 bg-white p-0 text-left shadow-2xl backdrop:bg-slate-900/50"><form method="POST" action="{{ route('devoluciones.compra.resolver', $devolucion->id) }}" class="p-5">@csrf @method('PATCH')<div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3"><div><p class="text-xs font-black uppercase tracking-widest text-amber-600">Resolver ticket</p><h3 class="text-lg font-black text-slate-900">DEV-{{ str_pad($devolucion->id, 5, '0', STR_PAD_LEFT) }}</h3></div><button type="button" onclick="this.closest('dialog').close()" class="h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-100"><i class="fas fa-xmark"></i></button></div><p class="mb-4 text-xs text-slate-500">Ubicación registrada: <strong>{{ ucfirst($devolucion->ubicacion_falla) }}</strong></p><label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Resolución final<select name="resolucion_final" required class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm"><option value="" disabled selected>Selecciona una opción</option><option value="cambio_fisico">Cambio físico</option><option value="reembolso">Reembolso</option><option value="merma">Merma / pérdida</option></select></label><div class="mt-5 flex justify-end gap-2"><button type="button" onclick="this.closest('dialog').close()" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600">Cancelar</button><button type="submit" class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-black text-white">Aplicar resolución</button></div></form></dialog>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="inline-flex rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-bold capitalize text-slate-700">
                                            {{ $resolucion }}
                                        </span>
                                        @if($esVenta && ($devolucion->detalles_json['modalidad_entrega_cambio'] ?? null) === 'envio')
                                            <span class="inline-flex items-center gap-1 rounded-md border border-teal-200 bg-teal-50 px-2 py-0.5 text-[10px] font-bold text-teal-800" title="Despacho programado por envío">
                                                <i class="fas fa-truck-fast text-[9px]"></i> Reenvío a domicilio
                                            </span>
                                        @elseif($esVenta && ($devolucion->detalles_json['modalidad_entrega_cambio'] ?? null) === 'tienda' && str_contains($devolucion->tipo_resolucion ?? '', 'cambio'))
                                            <span class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700" title="Entregado en tienda física">
                                                <i class="fas fa-store text-[9px]"></i> Cambio en tienda
                                            </span>
                                            @if(!empty($devolucion->detalles_json['nueva_venta_id']))
                                                <a href="{{ route('ventas.show', $devolucion->detalles_json['nueva_venta_id']) }}" class="inline-flex items-center gap-1 rounded-md border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-800 hover:bg-emerald-100 transition-colors" title="Ver nueva venta de reemplazo">
                                                    <i class="fas fa-arrow-up-right-from-square text-[8px]"></i> Nueva Venta #{{ $devolucion->detalles_json['nueva_venta_id'] }}
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <span class="font-black {{ (float) $devolucion->monto_reembolsado > 0 ? 'text-red-600' : 'text-slate-500' }}">
                                        ${{ number_format((float) $devolucion->monto_reembolsado, 2) }}
                                    </span>
                                </td>
                                <td class="max-w-[280px] px-5 py-4">
                                    <span class="block truncate text-xs text-slate-600" title="{{ $devolucion->motivo }}">
                                        {{ \Illuminate\Support\Str::limit($devolucion->motivo, 50) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if ($devolucion->comprobante)
                                        <a href="{{ asset('storage/' . $devolucion->comprobante) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700 transition-colors hover:bg-blue-600 hover:text-white">
                                            <i class="fas fa-paperclip"></i> Ver ticket / foto
                                        </a>
                                    @else
                                        <span class="text-xs font-medium text-slate-400">Sin evidencia</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-50 text-slate-300">
                                        <i class="fas fa-folder-open text-xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700">No hay devoluciones registradas</p>
                                    <p class="mt-1 text-xs text-slate-400">Las devoluciones de ventas y compras aparecerán aquí.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($devoluciones->hasPages())
                <div class="border-t border-slate-100 bg-slate-50/50 p-4">
                    {{ $devoluciones->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app>
