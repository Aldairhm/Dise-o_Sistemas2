<div class="overflow-x-auto">
    <table class="w-full min-w-[760px] text-left text-sm">
        <thead class="border-b border-slate-200 bg-slate-50 text-[10px] uppercase tracking-widest text-slate-500">
            <tr>
                <th class="px-5 py-4">Fecha</th>
                <th class="px-5 py-4">Tipo</th>
                <th class="px-5 py-4">Producto / variante</th>
                <th class="px-5 py-4 text-center">Cantidad</th>
                <th class="px-5 py-4">Origen</th>
                <th class="px-5 py-4 text-right">Detalle</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($movimientos as $movimiento)
                @php
                    $imagenMovimiento = $movimiento->variante?->imagenes?->firstWhere('es_principal', 1) ?? $movimiento->variante?->imagenes?->first();
                    $esDevolucion = $movimiento->devolucion_id || in_array($movimiento->tipo, ['devolucion_cliente', 'devolucion_proveedor']) || str_contains(strtolower($movimiento->observacion ?? ''), 'devolu');
                    $etiquetaTipo = match ($movimiento->tipo) {
                        'transferencia_tienda' => 'Transferencia',
                        'compra_recibida' => 'Compra recibida',
                        'devolucion_cliente' => 'Devolución cliente',
                        'devolucion_proveedor' => 'Devolución proveedor',
                        'Entrada', 'entrada' => 'Entrada',
                        'Salida', 'salida' => 'Salida',
                        default => $movimiento->tipo,
                    };
                @endphp
                <tr class="hover:bg-slate-50/60">
                    <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500">{{ $movimiento->created_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-4">
                        <span class="inline-flex whitespace-nowrap rounded-lg border px-2.5 py-1 text-[11px] font-bold {{ $esDevolucion ? 'border-red-200 bg-red-50 text-red-700' : 'border-slate-200 bg-slate-50 text-slate-700' }}">
                            {{ $etiquetaTipo }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                @if ($movimiento->variante?->imagen_url)
                                    <img src="{{ $movimiento->variante->imagen_url }}" alt="{{ $movimiento->variante?->nombre_variante ?? 'Producto' }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center text-slate-300"><i class="fas fa-image text-xs"></i></div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-slate-800">{{ $movimiento->variante?->producto?->nombre ?? 'Producto' }}</p>
                                <p class="text-xs text-slate-500">{{ $movimiento->variante?->nombre_variante ?? 'Variante sin nombre' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-center font-black text-amber-700">{{ $movimiento->cantidad }}</td>
                    <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                        @if ($movimiento->devolucion)
                            <span class="font-bold text-blue-600">DEV-{{ str_pad($movimiento->devolucion_id, 5, '0', STR_PAD_LEFT) }}</span>
                        @elseif ($movimiento->compra)
                            <span>Compra #{{ $movimiento->id_compra }}</span>
                        @else
                            <span class="text-slate-400">Inventario</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <button type="button" 
                                @click="abrirDetalleMovimiento({{ $movimiento->id }})" 
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-blue-600 shadow-sm hover:bg-blue-50 cursor-pointer">
                            <i class="fas fa-eye"></i> Ver detalle
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-12">
                        <x-empty-state 
                            title="No hay movimientos registrados" 
                            description="No se encontraron registros para el filtro seleccionado en el kardex."
                            icon="folder-open" 
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if ($movimientos->hasPages())
    <div class="border-t border-slate-100 p-4">{{ $movimientos->links() }}</div>
@endif

