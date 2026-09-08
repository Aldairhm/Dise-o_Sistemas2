@if($categorias->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center col-span-full">
        <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mb-5 text-slate-300 text-4xl">
            <i class="fas fa-tag"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-700 mb-1">
            {{ $estado === 'inactivos' ? 'Sin categorías inactivas' : 'Sin categorías' }}
        </h3>
        <p class="text-sm text-slate-400">
            {{ $estado === 'inactivos' ? 'No hay categorías inactivas en este momento.' : 'No se encontraron categorías activas.' }}
        </p>
    </div>
@else
    @foreach($categorias as $categoria)
    @php
        $color  = $categoria->color ?? '#3b82f6';
        $icono  = $categoria->icono ?? 'fa-tag';
        $isBaja = !$categoria->estado;
        $delay  = $loop->index * 50;
    @endphp

    <div class="categoria-card drag-handle group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 relative flex flex-col border border-slate-200/60 {{ $isBaja ? 'opacity-75 grayscale-[20%]' : '' }}"
         data-id="{{ $categoria->id }}"
         data-nombre="{{ strtolower($categoria->nombre) }}"
         data-desc="{{ strtolower($categoria->descripcion ?? '') }}"
         style="animation: cardFadeIn 0.4s ease both; animation-delay: {{ $delay }}ms; border-left: 4px solid {{ $color }};">

        <div class="p-5 flex-1">
            <!-- Header de la tarjeta (Ícono + Título + Badge) -->
            <div class="flex items-start gap-4">
                <!-- Ícono circular -->
                <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
                     style="background-color: {{ $color }}15; color: {{ $color }};">
                    <i class="fas {{ $icono }} text-lg"></i>
                </div>

                <!-- Título y Estado -->
                <div class="flex-1 min-w-0 pt-0.5">
                    <h3 class="font-bold text-slate-800 text-lg leading-tight truncate mb-1.5 {{ $isBaja ? 'text-slate-500 line-through' : '' }}" title="{{ $categoria->nombre }}">
                        {{ $categoria->nombre }}
                    </h3>
                    
                    @if(!$isBaja)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-600">
                            Habilitado
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-500">
                            Deshabilitado
                        </span>
                    @endif
                </div>
            </div>

            <!-- Detalles (Descripción, Productos y Color) -->
            <div class="mt-5 space-y-3">
                <!-- Descripción -->
                <div class="flex items-start gap-3">
                    <div class="w-5 flex justify-center mt-0.5">
                        <i class="fas fa-align-left text-slate-400 text-sm"></i>
                    </div>
                    @if($categoria->descripcion)
                        <p class="text-sm text-slate-600 line-clamp-2 {{ $isBaja ? 'line-through text-slate-400' : '' }}" title="{{ $categoria->descripcion }}">
                            {{ $categoria->descripcion }}
                        </p>
                    @else
                        <p class="text-sm text-slate-400 italic">Sin descripción general.</p>
                    @endif
                </div>
                
                <!-- Contador de Productos -->
                <div class="flex items-center gap-3">
                    <div class="w-5 flex justify-center">
                        <i class="fas fa-box text-slate-400 text-sm"></i>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-slate-700">{{ $categoria->productos_count ?? 0 }}</span>
                        <span class="text-xs text-slate-500">productos</span>
                    </div>
                </div>

                <!-- Código de color (Opcional, lo ocultaremos si no hay espacio, pero lo dejamos por estética) -->
                <div class="flex items-center gap-3">
                    <div class="w-5 flex justify-center">
                        <i class="fas fa-palette text-slate-400 text-sm"></i>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-600 font-mono">{{ strtoupper($color) }}</span>
                        <span class="w-3.5 h-3.5 rounded-full border border-slate-200" style="background-color: {{ $color }};"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divisor -->
        <div class="px-5">
            <hr class="border-slate-100">
        </div>

        <!-- Botones de Acción -->
        <div class="p-4 flex items-center justify-end gap-2 bg-slate-50/30 rounded-b-xl">
            @if(!$isBaja)
                <button type="button" 
                        onclick="openEditModal({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}', '{{ addslashes($categoria->descripcion ?? '') }}', '{{ $color }}', '{{ $icono }}')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold text-xs transition-colors cursor-pointer">
                    <i class="fas fa-edit"></i>
                    <span>Editar</span>
                </button>

                <button type="button"
                        onclick="toggleStatus({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}', 0)"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-red-50 hover:bg-red-100 text-red-600 font-semibold text-xs transition-colors cursor-pointer">
                    <i class="fas fa-ban"></i>
                    <span>Deshabilitar</span>
                </button>
            @else
                <button type="button"
                        onclick="toggleStatus({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}', 1)"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-600 font-semibold text-xs transition-colors cursor-pointer">
                    <i class="fas fa-check"></i>
                    <span>Habilitar</span>
                </button>
            @endif
        </div>
    </div>
    @endforeach
@endif
