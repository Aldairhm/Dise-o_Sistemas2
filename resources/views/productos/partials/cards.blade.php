@forelse ($productos as $prod)
@php
    $isActivo = $prod->estado == 1;
    $delay    = $loop->index * 50;
    $imgUrl   = $prod->imagen_principal ? asset('storage/' . $prod->imagen_principal) : null;
@endphp

<div class="producto-card group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 relative flex flex-col border border-slate-200/60 {{ !$isActivo ? 'opacity-70' : '' }}"
     data-id="{{ $prod->id }}"
     data-nombre="{{ strtolower($prod->nombre) }}"
     data-categoria="{{ strtolower($prod->categoria->nombre ?? '') }}"
     data-estado="{{ $prod->estado }}"
     style="animation: cardFadeIn 0.4s ease both; animation-delay: {{ $delay }}ms; border-top: 4px solid {{ $isActivo ? '#10b981' : '#ef4444' }};">

    <div class="p-5 flex-1 flex flex-col">
        {{-- Header: Imagen + Nombre --}}
        <div class="flex items-start gap-4 mb-4">
            {{-- Imagen --}}
            <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm bg-slate-100 overflow-hidden {{ !$isActivo ? 'grayscale' : '' }}">
                @if($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $prod->nombre }}" class="w-full h-full object-cover">
                @else
                    <i class="fas fa-box text-slate-300 text-2xl"></i>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0 pt-0.5">
                <h3 class="font-bold text-slate-800 text-sm leading-tight line-clamp-2 mb-1.5 {{ !$isActivo ? 'text-slate-400' : '' }}"
                    title="{{ $prod->nombre }}">
                    {{ $prod->nombre }}
                </h3>
                @if(Auth::user()->rol === 'admin')
                    @if($isActivo)
                        <button type="button"
                                class="btn-toggle-estado-producto inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-100 cursor-pointer transition-transform hover:scale-105 active:scale-95"
                                data-id="{{ $prod->id }}"
                                data-nombre="{{ $prod->nombre }}"
                                data-estado="1"
                                title="Clic para desactivar producto">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="estado-label">Activo</span>
                        </button>
                    @else
                        <button type="button"
                                class="btn-toggle-estado-producto inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 hover:bg-red-100 border border-red-100 cursor-pointer transition-transform hover:scale-105 active:scale-95"
                                data-id="{{ $prod->id }}"
                                data-nombre="{{ $prod->nombre }}"
                                data-estado="0"
                                title="Clic para activar producto">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            <span class="estado-label">Inactivo</span>
                        </button>
                    @endif
                @else
                    @if($isActivo)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Activo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 border border-red-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactivo
                        </span>
                    @endif
                @endif
            </div>
        </div>

        {{-- Detalles --}}
        <div class="mt-auto space-y-2">
            {{-- Categoría y Marca --}}
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">{{ $prod->categoria->nombre ?? 'Sin categoría' }}</span>
                @if($prod->marca)
                    <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-1.5 py-0.5 rounded uppercase border border-orange-100">{{ $prod->marca }}</span>
                @endif
            </div>

            {{-- Variantes --}}
            <div class="flex items-center justify-between text-sm text-slate-600">
                <span class="font-medium">Variantes:</span>
                <span class="inline-flex items-center gap-1 font-bold bg-slate-100 rounded px-1.5 py-0.5 text-xs">
                    <i class="fas fa-layer-group text-slate-400"></i> {{ $prod->variantes_count ?? 0 }}
                </span>
            </div>
        </div>
    </div>

    {{-- Divisor --}}
    <div class="px-5"><hr class="border-slate-100"></div>

    {{-- Botones de Accion --}}
    @if(Auth::user()->rol === 'admin')
    <div class="p-4 flex flex-wrap items-center justify-end gap-2 bg-slate-50/30 rounded-b-xl">
        {{-- Editar --}}
        <a href="{{ route('productos.edit', $prod->id) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold text-xs transition-colors border border-blue-200">
            <i class="fas fa-pen-to-square"></i> Editar
        </a>

        {{-- Eliminar --}}
        <form action="{{ route('productos.destroy', $prod->id) }}" method="POST" class="inline" id="form-delete-card-{{ $prod->id }}">
            @csrf
            @method('DELETE')
            <button type="button"
                    onclick="confirmarEliminar({{ $prod->id }}, '{{ addslashes($prod->nombre) }}')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-xs transition-colors border border-rose-200"
                    title="Eliminar Producto">
                <i class="fas fa-trash-can"></i> Eliminar
            </button>
        </form>
    </div>
    @endif
</div>
@empty
<div class="col-span-full flex flex-col items-center justify-center py-20 text-center empty-cards">
    <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-400 flex items-center justify-center text-2xl mb-4">
        <i class="fas fa-box-open"></i>
    </div>
    <h3 class="text-base font-bold text-slate-700 mb-1">No hay productos registrados</h3>
    @if(Auth::user()->rol === 'admin')
    <a href="{{ route('productos.create') }}" class="text-sm font-bold text-blue-600 hover:underline">
        + Crear el primero
    </a>
    @endif
</div>
@endforelse

<div id="cards-no-results" class="col-span-full hidden flex-col items-center justify-center py-20 text-center">
    <i class="fas fa-search text-4xl mb-3 block text-slate-300"></i>
    <p class="font-semibold text-sm text-slate-500">No se encontraron productos con estos filtros.</p>
</div>
