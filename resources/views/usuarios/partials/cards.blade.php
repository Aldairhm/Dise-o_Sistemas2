@forelse ($users as $user)
@php
    $isAdmin  = $user->rol === 'admin';
    $isActivo = $user->estado == 1;
    $initials = strtoupper(substr($user->nombre_real ?? $user->username, 0, 1));
    $delay    = $loop->index * 50;
@endphp

<div class="usuario-card group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 relative flex flex-col border border-slate-200/60 {{ !$isActivo ? 'opacity-70' : '' }}"
     data-id="{{ $user->id }}"
     data-nombre="{{ strtolower($user->nombre_real ?? '') }}"
     data-username="{{ strtolower($user->username) }}"
     data-rol="{{ $user->rol }}"
     data-estado="{{ $user->estado }}"
     style="animation: cardFadeIn 0.4s ease both; animation-delay: {{ $delay }}ms; border-left: 4px solid {{ $isAdmin ? '#7c3aed' : '#2563eb' }};">

    <div class="p-5 flex-1">
        {{-- Header: Avatar + Nombre + Estado --}}
        <div class="flex items-start gap-4">
            {{-- Avatar --}}
            <div class="w-12 h-12 rounded-full flex items-center justify-center font-black text-lg flex-shrink-0 shadow-sm transition-transform group-hover:scale-105 {{ $isAdmin ? 'bg-gradient-to-tr from-purple-600 to-indigo-600 text-white' : 'bg-gradient-to-tr from-blue-500 to-cyan-500 text-white' }} {{ !$isActivo ? 'grayscale' : '' }}">
                {{ $initials }}
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0 pt-0.5">
                <h3 class="font-bold text-slate-800 text-base leading-tight truncate mb-1 {{ !$isActivo ? 'text-slate-400 line-through' : '' }}"
                    title="{{ $user->nombre_real ?? $user->username }}">
                    {{ $user->nombre_real ?: $user->username }}
                </h3>
                @if($isActivo)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Activo
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Inactivo
                    </span>
                @endif
            </div>
        </div>

        {{-- Detalles --}}
        <div class="mt-4 space-y-2.5">
            {{-- Username --}}
            <div class="flex items-center gap-3">
                <div class="w-5 flex justify-center">
                    <i class="fas fa-at text-slate-400 text-sm"></i>
                </div>
                <span class="text-sm text-slate-600 font-mono truncate">{{ $user->username }}</span>
            </div>

            {{-- Teléfono --}}
            @if($user->telefono)
            <div class="flex items-center gap-3">
                <div class="w-5 flex justify-center">
                    <i class="fas fa-phone text-slate-400 text-sm"></i>
                </div>
                <span class="text-sm text-slate-600 font-medium">{{ $user->telefono }}</span>
            </div>
            @endif

            {{-- Rol --}}
            <div class="flex items-center gap-3">
                <div class="w-5 flex justify-center">
                    <i class="fas fa-id-badge text-slate-400 text-sm"></i>
                </div>
                @if($isAdmin)
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">
                        <i class="fas fa-shield-halved text-[10px]"></i> Administrador
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                        <i class="fas fa-tag text-[10px]"></i> Vendedor
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Divisor --}}
    <div class="px-5"><hr class="border-slate-100"></div>

    {{-- Botones de Accion --}}
    <div class="p-4 flex items-center justify-end gap-2 bg-slate-50/30 rounded-b-xl">
        {{-- Editar --}}
        <button type="button"
                data-user="{{ json_encode($user) }}"
                onclick="openEditUserModal(JSON.parse(this.dataset.user))"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold text-xs transition-colors cursor-pointer"
                title="Editar Usuario">
            <i class="fas fa-pen-to-square"></i>
            <span>Editar</span>
        </button>

        {{-- Toggle Estado --}}
        <button type="button"
                onclick="toggleUserStatus('{{ $user->id }}', this)"
                data-current="{{ $user->estado }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md font-semibold text-xs transition-colors cursor-pointer {{ $isActivo ? 'bg-amber-50 hover:bg-amber-100 text-amber-600' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600' }}"
                title="{{ $isActivo ? 'Inactivar' : 'Activar' }}">
            <i class="fas {{ $isActivo ? 'fa-ban' : 'fa-check' }}"></i>
            <span>{{ $isActivo ? 'Inactivar' : 'Activar' }}</span>
        </button>

        {{-- Eliminar --}}
        @if(auth()->id() !== $user->id)
        <button type="button"
                data-id="{{ $user->id }}"
                data-name="{{ $user->nombre_real ?? $user->username }}"
                onclick="confirmDeleteUser(this.dataset.id, this.dataset.name)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-xs transition-colors cursor-pointer"
                title="Eliminar Usuario">
            <i class="fas fa-trash-can"></i>
        </button>
        @else
        <span class="w-8 h-8 rounded-md bg-slate-50 text-slate-300 flex items-center justify-center" title="Tu usuario actual">
            <i class="fas fa-user-check text-xs"></i>
        </span>
        @endif
    </div>
</div>
@empty
<div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
    <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-400 flex items-center justify-center text-2xl mb-4">
        <i class="fas fa-users-slash"></i>
    </div>
    <h3 class="text-base font-bold text-slate-700 mb-1">No se encontraron usuarios</h3>
    <p class="text-sm text-slate-400">Prueba cambiando los términos de búsqueda o filtros.</p>
</div>
@endforelse
