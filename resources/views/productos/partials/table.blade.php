<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-100 bg-slate-50/80">
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">#</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Nombre</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:table-cell">Marca</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:table-cell">Categoría</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Variantes</th>
                <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Estado</th>
                @if(Auth::user()->rol === 'admin')
                <th class="text-right px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($productos as $prod)
            <tr class="hover:bg-slate-50/60 transition-colors producto-row"
                data-id="{{ $prod->id }}"
                data-nombre="{{ strtolower($prod->nombre) }}"
                data-categoria="{{ strtolower($prod->categoria->nombre ?? '') }}"
                data-estado="{{ $prod->estado }}">
                
                <td class="px-6 py-4 text-slate-400 font-mono text-xs">{{ $prod->id }}</td>
                <td class="px-6 py-4 font-semibold text-slate-800">{{ $prod->nombre }}</td>
                <td class="px-6 py-4 hidden sm:table-cell text-slate-500 text-xs">{{ $prod->marca ?? '—' }}</td>
                <td class="px-6 py-4 hidden sm:table-cell text-slate-500 text-xs">{{ $prod->categoria->nombre ?? '—' }}</td>
                <td class="px-6 py-4 hidden md:table-cell">
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 bg-slate-100 rounded-full px-2.5 py-1">
                        <i class="fas fa-layer-group text-xs"></i>
                        {{ $prod->variantes_count ?? 0 }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    @if($prod->estado == 1)
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-2.5 py-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Activo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 bg-red-50 border border-red-200 rounded-full px-2.5 py-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactivo
                        </span>
                    @endif
                </td>
                @if(Auth::user()->rol === 'admin')
                <td class="px-6 py-4 text-right">
                    <div class="inline-flex items-center gap-2">
                        <a href="{{ route('productos.edit', $prod->id) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors">
                            <i class="fas fa-pencil text-xs"></i> Editar / Variantes
                        </a>
                        <form action="{{ route('productos.destroy', $prod->id) }}" method="POST"
                                class="inline" id="form-delete-{{ $prod->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                    onclick="confirmarEliminar({{ $prod->id }}, '{{ addslashes($prod->nombre) }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
                                <i class="fas fa-trash text-xs"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </td>
                @endif
            </tr>
            @empty
            <tr class="empty-row">
                <td colspan="{{ Auth::user()->rol === 'admin' ? 7 : 6 }}" class="text-center py-16 text-slate-400">
                    <i class="fas fa-boxes-stacked text-4xl mb-3 block opacity-30"></i>
                    <p class="font-semibold text-sm">No hay productos registrados.</p>
                    @if(Auth::user()->rol === 'admin')
                    <a href="{{ route('productos.create') }}" class="mt-3 inline-block text-sm font-bold text-blue-600 hover:underline">
                        + Crear el primero
                    </a>
                    @endif
                </td>
            </tr>
            @endforelse
            <tr id="table-no-results" class="hidden">
                <td colspan="{{ Auth::user()->rol === 'admin' ? 7 : 6 }}" class="text-center py-16 text-slate-400">
                    <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
                    <p class="font-semibold text-sm">No se encontraron productos con estos filtros.</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
