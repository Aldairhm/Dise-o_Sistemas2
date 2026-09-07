<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-200/80 bg-gray-50/75 text-[11px] font-black uppercase tracking-wider text-gray-500">
                <th class="py-3.5 px-4 sm:px-6">Usuario</th>
                <th class="py-3.5 px-4">Nombre Real</th>
                <th class="py-3.5 px-4 text-center">Rol</th>
                <th class="py-3.5 px-4 text-center">Estado</th>
                <th class="py-3.5 px-4 sm:px-6 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm text-gray-700 bg-white">
            @forelse ($users as $user)
                <tr class="hover:bg-blue-50/40 transition-colors group" id="user-row-{{ $user->id }}">
                    <!-- Avatar y Usuario -->
                    <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-transform group-hover:scale-105 {{ $user->rol === 'admin' ? 'bg-gradient-to-tr from-purple-600 to-indigo-600 text-white shadow-indigo-200' : 'bg-gradient-to-tr from-blue-500 to-cyan-500 text-white shadow-blue-200' }}">
                                {{ strtoupper(substr($user->nombre_real ?? $user->username, 0, 1)) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    {{ $user->username }}
                                </span>
                            </div>
                        </div>
                    </td>

                    <!-- Nombre -->
                    <td class="py-4 px-4 whitespace-nowrap">
                        <span class="font-medium text-gray-800">
                            {{ $user->nombre_real ?: '—' }}
                        </span>
                    </td>

                    <!-- Rol -->
                    <td class="py-4 px-4 text-center whitespace-nowrap">
                        @if ($user->rol === 'admin')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100/80 text-purple-700 border border-purple-200/60 shadow-xs">
                                <i class="fas fa-shield-halved text-[10px]"></i> Administrador
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100/80 text-blue-700 border border-blue-200/60 shadow-xs">
                                <i class="fas fa-tag text-[10px]"></i> Vendedor
                            </span>
                        @endif
                    </td>

                    <!-- Estado con Toggle interactivo -->
                    <td class="py-4 px-4 text-center whitespace-nowrap">
                        <button type="button" 
                                onclick="toggleUserStatus('{{ $user->id }}', this)" 
                                data-current="{{ $user->estado }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold cursor-pointer transition-all hover:scale-105 active:scale-95 shadow-xs {{ $user->estado == 1 ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 border border-emerald-200' : 'bg-rose-100 text-rose-700 hover:bg-rose-200 border border-rose-200' }}"
                                title="Haz clic para cambiar estado">
                            <span class="w-2 h-2 rounded-full {{ $user->estado == 1 ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                            <span class="status-label">{{ $user->estado == 1 ? 'Activo' : 'Inactivo' }}</span>
                        </button>
                    </td>

                    <!-- Acciones -->
                    <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-2">
                            <!-- Boton Editar -->
                            <button type="button" 
                                    data-user="{{ json_encode($user) }}"
                                    onclick="openEditUserModal(JSON.parse(this.dataset.user))"
                                    class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 flex items-center justify-center transition-colors cursor-pointer"
                                    title="Editar Usuario">
                                <i class="fas fa-pen-to-square text-xs"></i>
                            </button>

                            <!-- Boton Eliminar -->
                            @if(auth()->id() !== $user->id)
                            <button type="button" 
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->nombre_real ?? $user->username }}"
                                    onclick="confirmDeleteUser(this.dataset.id, this.dataset.name)"
                                    class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-rose-100 text-gray-600 hover:text-rose-600 flex items-center justify-center transition-colors cursor-pointer"
                                    title="Eliminar Usuario">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                            @else
                            <span class="w-8 h-8 rounded-lg bg-gray-50 text-gray-300 flex items-center justify-center" title="Tu usuario actual">
                                <i class="fas fa-user-check text-xs"></i>
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-12 px-4 text-center">
                        <div class="max-w-xs mx-auto flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-2xl mb-3">
                                <i class="fas fa-users-slash"></i>
                            </div>
                            <h3 class="text-base font-bold text-gray-800">No se encontraron usuarios</h3>
                            <p class="text-xs text-gray-500 mt-1">Prueba cambiando los términos de búsqueda o filtros seleccionados.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Paginacion -->
<div class="py-4 px-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white">
    <div class="text-xs text-gray-500">
        @if($users->total() > 0)
            Mostrando <b>{{ $users->firstItem() }}</b> a <b>{{ $users->lastItem() }}</b> de <b>{{ $users->total() }}</b> usuarios
        @else
            Sin resultados
        @endif
    </div>

    @if ($users->hasPages())
    <nav class="flex items-center gap-1">
        {{-- Bot0n Anterior --}}
        @if ($users->onFirstPage())
            <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 text-gray-300 text-xs cursor-not-allowed">
                <i class="fas fa-chevron-left"></i>
            </span>
        @else
            <a href="{{ $users->previousPageUrl() }}" class="pagination-link w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 text-xs transition-colors cursor-pointer">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif

        {{-- Numeros de Pagina --}}
        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
            @if ($page == $users->currentPage())
                <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-600 text-white text-xs font-bold shadow-sm shadow-blue-500/25">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $url }}" class="pagination-link w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-600 text-xs font-bold transition-colors cursor-pointer">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Boton Siguiente --}}
        @if ($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}" class="pagination-link w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 text-xs transition-colors cursor-pointer">
                <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 text-gray-300 text-xs cursor-not-allowed">
                <i class="fas fa-chevron-right"></i>
            </span>
        @endif
    </nav>
    @endif
</div>
