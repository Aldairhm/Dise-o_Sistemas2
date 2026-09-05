@if($categorias->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center" id="emptyState">
        <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mb-5 text-slate-300 text-4xl">
            <i class="fas fa-tag"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-700 mb-1">
            {{ $estado === 'inactivos' ? 'Sin categorías inactivas' : 'Sin categorías' }}
        </h3>
        <p class="text-sm text-slate-400">
            {{ $estado === 'inactivos' ? 'No hay categorías inactivas en este momento.' : 'No se encontraron categorías. Crea una nueva para comenzar.' }}
        </p>
    </div>
@else
    <table class="w-full text-sm" id="categoriasTable">
        <thead>
            <tr class="border-b border-slate-100 bg-slate-50/50">
                <th class="w-10"></th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">#</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Categoría</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Descripción</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:table-cell">Productos</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden lg:table-cell">Estado</th>
                <th class="text-right px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Acciones</th>
            </tr>
        </thead>
        <tbody id="categoriasBody">
            @foreach($categorias as $categoria)
            @php
                $color   = $categoria->color ?? '#3b82f6';
                $icono   = $categoria->icono ?? 'fa-tag';
                $isBaja  = !$categoria->estado;
            @endphp
            <tr class="border-b border-slate-50 transition-colors categoria-row {{ $isBaja ? 'bg-slate-50/40 opacity-70 hover:opacity-90' : 'hover:bg-slate-50/60' }}"
                data-id="{{ $categoria->id }}"
                data-nombre="{{ strtolower($categoria->nombre) }}"
                data-desc="{{ strtolower($categoria->descripcion ?? '') }}">

                <td class="w-10 text-center drag-handle text-slate-300 hover:text-slate-500">
                    <i class="fas fa-grip-vertical"></i>
                </td>

                <td class="px-6 py-4 text-slate-400 font-mono text-xs">{{ $loop->iteration }}</td>

                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm flex-shrink-0 transition-transform {{ $isBaja ? '' : 'hover:scale-110' }}"
                             style="background-color: {{ $color }}{{ $isBaja ? '18' : '20' }}; color: {{ $isBaja ? '#94a3b8' : $color }};">
                            <i class="fas {{ $icono }}"></i>
                        </div>
                        <div>
                            <span class="font-bold {{ $isBaja ? 'text-slate-400 line-through' : 'text-slate-800' }}">{{ $categoria->nombre }}</span>
                            @if(!$isBaja)
                                <span class="inline-block w-1.5 h-1.5 rounded-full ml-1.5 mb-0.5 align-middle"
                                      style="background-color: {{ $color }};"></span>
                            @endif
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4 hidden md:table-cell">
                    @if($categoria->descripcion)
                        <p class="text-slate-500 text-sm line-clamp-2 max-w-xs {{ $isBaja ? 'line-through text-slate-400' : '' }}">{{ $categoria->descripcion }}</p>
                    @else
                        <span class="text-slate-300 text-xs italic">Sin descripción</span>
                    @endif
                </td>

                <td class="px-6 py-4 hidden sm:table-cell">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                        <i class="fas fa-box text-slate-400"></i>
                        {{ $categoria->productos_count ?? 0 }}
                    </span>
                </td>

                <td class="px-6 py-4 hidden lg:table-cell">
                    @if($isBaja)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            Inactiva
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Activa
                        </span>
                    @endif
                </td>

                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2">
                        <!-- Editar -->
                        <button type="button"
                                onclick="openEditModal({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}', '{{ addslashes($categoria->descripcion ?? '') }}', '{{ $color }}', '{{ $icono }}')"
                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-600 flex items-center justify-center transition-colors cursor-pointer"
                                title="Editar categoría">
                            <i class="fas fa-pencil text-xs"></i>
                        </button>

                        @if($isBaja)
                            <!-- Activar -->
                            <button type="button"
                                    onclick="toggleStatus({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}', 1)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 font-bold text-xs transition-colors cursor-pointer border border-emerald-100"
                                    title="Activar categoría">
                                <i class="fas fa-check text-xs"></i>
                                <span>Activar</span>
                            </button>
                        @else
                            <!-- Inactivar -->
                            <button type="button"
                                    onclick="toggleStatus({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}', 0)"
                                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-500 hover:text-amber-600 flex items-center justify-center transition-colors cursor-pointer"
                                    title="Inactivar">
                                <i class="fas fa-ban text-xs"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer de la tabla -->
    <div class="px-6 py-3 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between">
        <span class="text-xs text-slate-400 font-medium">
            Mostrando <span class="font-bold text-slate-600">{{ $categorias->count() }}</span> categoría(s)
        </span>
        <div class="flex items-center gap-1">
            <span class="text-xs text-slate-400 mr-2"><i class="fas fa-info-circle"></i> Arrastra desde el borde izquierdo para reordenar</span>
        </div>
    </div>
@endif
