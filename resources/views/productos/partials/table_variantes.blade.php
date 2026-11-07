<div class="overflow-x-auto">
    <table class="w-full text-sm" id="tablaVariantes">
        <thead>
            <tr class="border-b border-slate-100 bg-slate-50/80">
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Imagen</th>
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">SKU</th>
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Variante</th>
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Precio</th>
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Stock</th>
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Reserva</th>
                <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Estado</th>
                <th class="text-right px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($producto->variantes as $variante)
            <tr class="hover:bg-slate-50/60 transition-colors variante-row" 
                data-id="{{ $variante->id }}"
                data-nombre="{{ strtolower($variante->nombre_variante) }}"
                data-sku="{{ strtolower($variante->sku ?? '') }}"
                data-estado="{{ $variante->estado }}">
                <td class="px-6 py-3">
                    @php
                        $imgP = $variante->imagenes->where('es_principal', 1)->first() ?? $variante->imagenes->first();
                    @endphp
                    @if($imgP)
                        <img src="{{ str_starts_with($imgP->ruta_imagen, 'http') ? $imgP->ruta_imagen : \Illuminate\Support\Facades\Storage::disk('s3')->url($imgP->ruta_imagen) }}" alt="img"
                                class="w-10 h-10 rounded-lg object-cover border border-slate-200">
                    @else
                        <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300">
                            <i class="fas fa-image text-sm"></i>
                        </div>
                    @endif
                </td>
                <td class="px-6 py-3 font-mono text-xs text-slate-500">{{ $variante->sku ?? '—' }}</td>
                <td class="px-6 py-3 font-semibold text-slate-800">{{ $variante->nombre_variante }}</td>
                <td class="px-6 py-3 font-bold text-slate-800">${{ number_format($variante->precio_venta, 2) }}</td>
                <td class="px-6 py-3">
                    <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full
                        {{ $variante->stock > 10 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                            : ($variante->stock > 0  ? 'bg-amber-50 text-amber-700 border border-amber-200'
                                                    : 'bg-red-50 text-red-600 border border-red-200') }}">
                        {{ $variante->stock }}
                    </span>
                </td>
                <td class="px-6 py-3 hidden md:table-cell text-slate-500 text-xs">{{ $variante->reserva }}</td>
                <td class="px-6 py-3">
                    @if($variante->estado == 1)
                        <button type="button"
                                class="btn-toggle-estado-variante inline-flex items-center gap-1.5 text-xs font-bold rounded-full px-2.5 py-1 transition-all hover:scale-105 active:scale-95 cursor-pointer border text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border-emerald-200"
                                data-id="{{ $variante->id }}"
                                data-nombre="{{ $variante->nombre_variante }}"
                                data-estado="1"
                                title="Clic para desactivar variante">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="estado-label">Activo</span>
                        </button>
                    @else
                        <button type="button"
                                class="btn-toggle-estado-variante inline-flex items-center gap-1.5 text-xs font-bold rounded-full px-2.5 py-1 transition-all hover:scale-105 active:scale-95 cursor-pointer border text-red-600 bg-red-50 hover:bg-red-100 border-red-200"
                                data-id="{{ $variante->id }}"
                                data-nombre="{{ $variante->nombre_variante }}"
                                data-estado="0"
                                title="Clic para activar variante">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            <span class="estado-label">Inactivo</span>
                        </button>
                    @endif
                </td>
                <td class="px-6 py-3 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button"
                                class="btn-duplicar-variante p-2 text-xs font-bold text-violet-600 bg-violet-50 hover:bg-violet-100 border border-violet-200 rounded-lg transition-colors"
                                data-id="{{ $variante->id }}" title="Duplicar">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button type="button"
                                class="btn-editar-variante p-2 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors"
                                data-id="{{ $variante->id }}" title="Editar">
                            <i class="fas fa-pencil"></i>
                        </button>
                        <button type="button"
                                class="btn-eliminar-variante p-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors"
                                data-id="{{ $variante->id }}" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="rowNoVariantes">
                <td colspan="8" class="py-6">
                    <x-empty-state 
                        title="Este producto aún no tiene variantes" 
                        description="Usa el botón 'Agregar Variante' para comenzar a registrar opciones de stock y precio."
                        icon="folder-open" 
                    />
                </td>
            </tr>
            @endforelse
            <tr id="table-no-results-variantes" class="hidden">
                <td colspan="8" class="py-6">
                    <x-empty-state 
                        title="No se encontraron variantes" 
                        description="Intenta ajustar el término de búsqueda o el filtro de estado."
                        icon="search" 
                    />
                </td>
            </tr>
        </tbody>
    </table>
</div>
