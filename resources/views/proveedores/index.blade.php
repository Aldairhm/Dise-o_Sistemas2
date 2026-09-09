<x-app title="Proveedores | AXStore">

    <div x-data="gestionProveedores({{ Js::from($proveedores ?? []) }})" class="max-w-7xl mx-auto p-4 sm:p-6">

        <!-- ENCABEZADO (igual estilo que Categorías) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1.5">
                    <i class="fas fa-truck-loading"></i>
                    <span>Módulo de Proveedores</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Directorio de Proveedores
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Administra los proveedores del catálogo de productos de AXStore.
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <!-- Toggle tabla / cards -->
                <div class="flex items-center bg-slate-100 rounded-xl p-1 gap-1">
                    <button type="button" @click="setVista('table')"
                        class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 cursor-pointer"
                        :class="vista === 'table' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                        title="Vista tabla">
                        <i class="fas fa-list text-sm"></i>
                    </button>
                    <button type="button" @click="setVista('cards')"
                        class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 cursor-pointer"
                        :class="vista === 'cards' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                        title="Vista tarjetas">
                        <i class="fas fa-grip text-sm"></i>
                    </button>
                </div>

                <!-- Botón agregar -->
                <button @click="abrirCrear()" type="button"
                    class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-lg shadow-blue-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-xs">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span>AGREGAR</span>
                </button>
            </div>
        </div>

        <!-- MÉTRICAS (igual estilo que Categorías) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 mb-8">
            <div @click="setEstado('todas')"
                class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-blue-300 transition-colors cursor-pointer">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Proveedores</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                        <i class="fas fa-truck"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-3" x-text="kpiTotal">0</p>
            </div>

            <div @click="setEstado('habilitados')"
                class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-emerald-300 transition-colors cursor-pointer">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Habilitados</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-3" x-text="kpiHabilitados">0</p>
            </div>

            <div @click="setEstado('deshabilitados')"
                class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-slate-400 transition-colors cursor-pointer">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Deshabilitados</span>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-base">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-3" x-text="kpiDeshabilitados">0</p>
            </div>
        </div>

        <!-- BÚSQUEDA + FILTROS (igual estilo que Categorías) -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs mb-6">
            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-search text-sm"></i>
                    </div>
                    <input type="text" x-model="busqueda" @input="paginaActual = 1"
                        placeholder="Buscar proveedor por nombre o correo..."
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 focus:border-blue-600 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all">
                    <button type="button" @click="clearSearch()" x-show="busqueda !== ''" style="display:none;"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:inline">Ver:</span>

                    <button type="button" @click="setEstado('todas')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                        :class="estadoFiltro === 'todas' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
                        Todos
                    </button>
                    <button type="button" @click="setEstado('habilitados')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                        :class="estadoFiltro === 'habilitados' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
                        Habilitados
                    </button>
                    <button type="button" @click="setEstado('deshabilitados')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                        :class="estadoFiltro === 'deshabilitados' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
                        Deshabilitados
                    </button>
                </div>

                <button type="button" @click="busqueda = ''; setEstado('todas')"
                    class="p-2 text-slate-400 hover:text-blue-600 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer flex-shrink-0"
                    title="Limpiar">
                    <i class="fas fa-rotate-right text-xs"></i>
                </button>
            </div>
        </div>

        <!-- CONTENEDOR TABLA / CARDS -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden relative">

            <!-- Loader -->
            <div x-show="loadingLista" style="display:none;"
                class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-20">
                <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-2xl shadow-xl border border-slate-100 text-blue-600 text-sm font-bold">
                    <i class="fas fa-spinner fa-spin text-lg"></i>
                    <span>Buscando proveedores...</span>
                </div>
            </div>

            <!-- Mensaje sin datos -->
            <div x-show="proveedoresFiltrados.length === 0" style="display:none;"
                class="text-center py-12 text-gray-500">
                <i class="fas fa-folder-open text-3xl mb-3 text-gray-400"></i>
                <p>No hay proveedores que coincidan con la búsqueda.</p>
            </div>

            <!-- ===================== VISTA TABLA ===================== -->
            <div x-show="vista === 'table' && proveedoresFiltrados.length > 0" style="display:none;">
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-sm min-w-[600px]">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50">
                                
                                <th class="text-left px-4 sm:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Proveedor</th>
                                <th class="text-left px-4 sm:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Correo</th>
                                <th class="text-left px-4 sm:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:table-cell">Teléfono</th>
                                <th class="text-left px-4 sm:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest hidden lg:table-cell">Estado</th>
                                <th class="text-right px-4 sm:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(proveedor, index) in proveedoresPaginados" :key="proveedor.id">
                                <tr class="border-b border-slate-50 transition-colors hover:bg-slate-50/60">

                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm flex-shrink-0"
                                                :class="proveedor.deleted_at ? 'bg-slate-100 text-slate-400' : 'bg-blue-50 text-blue-600'">
                                                <i class="fas fa-building"></i>
                                            </div>
                                            <span class="font-bold text-slate-800" x-text="proveedor.nombre"></span>
                                        </div>
                                    </td>

                                    <td class="px-4 sm:px-6 py-4 hidden md:table-cell text-slate-500"
                                        x-text="proveedor.correo || 'Sin correo'"></td>

                                    <td class="px-4 sm:px-6 py-4 hidden sm:table-cell text-slate-500"
                                        x-text="proveedor.telefono || 'Sin teléfono'"></td>

                                    <td class="px-4 sm:px-6 py-4 hidden lg:table-cell">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                                            :class="proveedor.deleted_at ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100'">
                                            <span class="w-1.5 h-1.5 rounded-full"
                                                :class="proveedor.deleted_at ? 'bg-red-400' : 'bg-emerald-400 animate-pulse'"></span>
                                            <span x-text="proveedor.deleted_at ? 'Deshabilitado' : 'Habilitado'"></span>
                                        </span>
                                    </td>

                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a :href="'/proveedor/' + proveedor.id"
                                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-600 flex items-center justify-center transition-colors cursor-pointer"
                                                title="Ver detalles">
                                                <i class="fas fa-folder-open text-xs"></i>
                                            </a>
                                            <button type="button" @click="abrirEditar(proveedor)"
                                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-600 flex items-center justify-center transition-colors cursor-pointer"
                                                title="Editar proveedor">
                                                <i class="fas fa-pencil text-xs"></i>
                                            </button>
                                            <button type="button" @click="cambiarEstado(proveedor)"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors cursor-pointer"
                                                :class="proveedor.deleted_at ? 'bg-slate-100 hover:bg-emerald-100 text-slate-500 hover:text-emerald-600' : 'bg-slate-100 hover:bg-amber-100 text-slate-500 hover:text-amber-600'"
                                                :title="proveedor.deleted_at ? 'Habilitar' : 'Deshabilitar'">
                                                <i class="fas text-xs" :class="proveedor.deleted_at ? 'fa-check' : 'fa-ban'"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Footer de la tabla -->
                <div class="px-4 sm:px-6 py-3 bg-slate-50/60 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-center sm:text-left">
                    <span class="text-xs text-slate-400 font-medium">
                        Mostrando <span class="font-bold text-slate-600" x-text="proveedoresPaginados.length"></span> proveedor(es)
                    </span>
                </div>
            </div>

            <!-- ===================== VISTA CARDS ===================== -->
            <div x-show="vista === 'cards' && proveedoresFiltrados.length > 0" style="display:none;" class="p-4 sm:p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="proveedor in proveedoresPaginados" :key="proveedor.id">
                        <div class="group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 relative flex flex-col border border-slate-200/60"
                            :style="proveedor.deleted_at ? 'border-left: 4px solid #94a3b8' : 'border-left: 4px solid #3b82f6'">

                            <div class="p-5 flex-1">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
                                        :class="proveedor.deleted_at ? 'bg-slate-100 text-slate-400' : 'bg-blue-50 text-blue-600'">
                                        <i class="fas fa-building text-lg"></i>
                                    </div>

                                    <div class="flex-1 min-w-0 pt-0.5">
                                        <h3 class="font-bold text-slate-800 text-lg leading-tight truncate mb-1.5"
                                            x-text="proveedor.nombre" :title="proveedor.nombre"></h3>

                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold"
                                            :class="proveedor.deleted_at ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600'"
                                            x-text="proveedor.deleted_at ? 'Deshabilitado' : 'Habilitado'"></span>
                                    </div>
                                </div>

                                <div class="mt-5 space-y-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-5 flex justify-center mt-0.5">
                                            <i class="fas fa-envelope text-slate-400 text-sm"></i>
                                        </div>
                                        <p class="text-sm text-slate-600 line-clamp-2"
                                            x-text="proveedor.correo || 'Correo electrónico aún no asignado'"></p>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="w-5 flex justify-center">
                                            <i class="fas fa-phone text-slate-400 text-sm"></i>
                                        </div>
                                        <p class="text-sm text-slate-600"
                                            x-text="proveedor.telefono || 'Teléfono aún no asignado'"></p>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="w-5 flex justify-center mt-0.5">
                                            <i class="fas fa-map-marker-alt text-slate-400 text-sm"></i>
                                        </div>
                                        <p class="text-sm text-slate-600 line-clamp-2"
                                            x-text="proveedor.direccion || 'Dirección física aún no asignada'"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="px-5">
                                <hr class="border-slate-100">
                            </div>

                            <div class="p-4 flex items-center justify-end gap-2 bg-slate-50/30 rounded-b-xl flex-wrap">
                                <a :href="'/proveedor/' + proveedor.id"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs transition-colors cursor-pointer">
                                    <i class="fas fa-folder-open"></i>
                                    <span>Ver Detalles</span>
                                </a>

                                <button type="button" @click="abrirEditar(proveedor)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold text-xs transition-colors cursor-pointer">
                                    <i class="fas fa-edit"></i>
                                    <span>Editar</span>
                                </button>

                                <button type="button" @click="cambiarEstado(proveedor)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md font-semibold text-xs transition-colors cursor-pointer"
                                    :class="proveedor.deleted_at ? 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600' : 'bg-red-50 hover:bg-red-100 text-red-600'">
                                    <i class="fas" :class="proveedor.deleted_at ? 'fa-check-circle' : 'fa-ban'"></i>
                                    <span x-text="proveedor.deleted_at ? 'Habilitar' : 'Deshabilitar'"></span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- CONTROLES DE PAGINACIÓN -->
        <div x-show="totalPaginas > 1" style="display:none;"
            class="mt-6 flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
            <button @click="irPagina(paginaActual - 1)" :disabled="paginaActual === 1"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-chevron-left"></i> Anterior
            </button>

            <div class="text-sm text-gray-600 font-medium">
                Página <span x-text="paginaActual" class="font-bold text-gray-900"></span> de
                <span x-text="totalPaginas" class="font-bold text-gray-900"></span>
            </div>

            <button @click="irPagina(paginaActual + 1)" :disabled="paginaActual === totalPaginas"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                Siguiente <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- MODAL: CREAR / EDITAR -->
        <div x-show="openModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>

            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:items-center sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="openModal" @click.away="openModal = false" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full border border-slate-100">

                    <!-- Header Azul -->
                    <div class="bg-blue-600 px-6 py-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold flex items-center gap-2 text-base">
                                <i class="fas fa-truck-moving"></i>
                                <span x-text="editando ? 'Editar Proveedor' : 'Nuevo Proveedor'">Nuevo Proveedor</span>
                            </h3>
                            <p class="text-xs text-blue-100 mt-0.5">Completa los campos para registrar un proveedor</p>
                        </div>
                        <button @click="openModal = false" type="button"
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all cursor-pointer">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form @submit.prevent="guardarProveedor">
                        <div class="p-6 md:p-8">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">

                                <!-- COLUMNA IZQUIERDA: VISTA PREVIA -->
                                <div class="md:col-span-4 flex flex-col">
                                    <h4 class="text-xs font-black text-slate-500 tracking-widest uppercase mb-4">
                                        Información del Proveedor
                                    </h4>
                                    <div class="bg-white rounded-2xl border-2 border-slate-100 p-6 flex flex-col items-center text-center shadow-sm relative overflow-hidden h-full">
                                        <div class="absolute top-0 left-0 right-0 h-1 bg-blue-500"></div>

                                        <div class="w-24 h-24 rounded-2xl flex items-center justify-center mb-4 bg-blue-500 shadow-[0_10px_25px_-5px_rgba(59,130,246,0.4)]">
                                            <i class="fas fa-building text-white text-4xl"></i>
                                        </div>

                                        <h3 class="text-xl font-black text-slate-800 mb-2 truncate w-full"
                                            x-text="form.nombre || 'Nuevo Proveedor'">Nuevo Proveedor</h3>

                                        <div class="flex items-center justify-center gap-2 mb-6 flex-wrap">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                                <i class="fas fa-truck"></i> Proveedor
                                            </span>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Habilitado
                                            </span>
                                        </div>

                                        <div class="w-full border-t border-slate-100 pt-4 mt-auto">
                                            <div class="flex justify-between items-center text-sm mb-2">
                                                <span class="text-slate-400">Correo:</span>
                                                <span class="font-bold text-slate-700 truncate max-w-[140px]"
                                                    x-text="form.correo || '---'">---</span>
                                            </div>
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-slate-400">Teléfono:</span>
                                                <span class="font-bold text-slate-700 truncate max-w-[140px]"
                                                    x-text="form.telefono || '---'">---</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- COLUMNA DERECHA: CAMPOS -->
                                <div class="md:col-span-8 flex flex-col">
                                    <h4 class="text-xs font-black text-slate-500 tracking-widest uppercase mb-4">
                                        Datos del Proveedor
                                    </h4>

                                    <div class="space-y-5 flex-1">
                                        <!-- Nombre -->
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-1.5">
                                                Nombre de la Empresa <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" x-model="form.nombre"
                                                class="w-full px-4 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all shadow-sm"
                                                placeholder="Ej: Accesorios Eléctricos S.A.">
                                            <p class="mt-1.5 text-xs text-slate-400">Nombre público que verán los usuarios (Campo obligatorio)</p>
                                            <template x-if="errors.nombre">
                                                <p class="mt-1 text-xs text-red-500 font-medium" x-text="errors.nombre[0]"></p>
                                            </template>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <!-- Correo -->
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-1.5">
                                                    <i class="fas fa-envelope text-slate-400 mr-1"></i> Correo Electrónico
                                                </label>
                                                <div class="relative">
                                                    <input type="email" x-model="form.correo" @input="validarCorreoLive"
                                                        placeholder="ventas@empresa.com"
                                                        class="w-full px-4 py-3 border rounded-xl text-sm outline-none transition-all focus:ring-2 shadow-sm"
                                                        :class="{
                                                            'border-emerald-500 bg-emerald-50 text-emerald-600 focus:ring-emerald-500/20': form.correo && correoValido(),
                                                            'border-rose-500 bg-rose-50 text-rose-600 focus:ring-rose-500/20': form.correo && !correoValido(),
                                                            'border-slate-200 text-slate-800 bg-white focus:border-blue-500 focus:ring-blue-500/20': !form.correo
                                                        }">
                                                </div>
                                                <template x-if="form.correo && correoValido()">
                                                    <p class="mt-1.5 text-xs text-emerald-600 font-medium">Correo válido ✓</p>
                                                </template>
                                                <template x-if="form.correo && !correoValido()">
                                                    <p class="mt-1.5 text-xs text-rose-500 font-medium">Formato incorrecto. Ej: usuario@dominio.com</p>
                                                </template>
                                                <template x-if="!form.correo && errors.correo">
                                                    <p class="mt-1.5 text-xs text-rose-500 font-medium" x-text="errors.correo[0]"></p>
                                                </template>
                                            </div>

                                            <!-- Teléfono -->
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-1.5">
                                                    <i class="fas fa-phone text-slate-400 mr-1"></i> Teléfono
                                                </label>
                                                <div class="relative">
                                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 flex items-center gap-1 text-slate-400 text-xs font-bold select-none">
                                                        <img src="https://flagcdn.com/w20/sv.png" alt="SV" class="h-3.5 rounded-sm opacity-90">
                                                        <span>+503</span>
                                                    </div>
                                                    <input type="text" inputmode="numeric" :value="form.telefono"
                                                        @input="formatearTelefono($event)"
                                                        @keydown="if(!/[0-9]/.test($event.key) && !['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete'].includes($event.key)) $event.preventDefault()"
                                                        maxlength="9" placeholder="XXXX-XXXX"
                                                        style="padding-left: 5.2rem;"
                                                        class="w-full pr-4 py-3 border rounded-xl text-sm outline-none transition-all focus:ring-2 shadow-sm"
                                                        :class="{
                                                            'border-emerald-500 bg-emerald-50 text-emerald-600 focus:ring-emerald-500/20': form.telefono && telefonoValido(),
                                                            'border-rose-500 bg-rose-50 text-rose-600 focus:ring-rose-500/20': form.telefono && !telefonoValido(),
                                                            'border-slate-200 text-slate-800 bg-white focus:border-blue-500 focus:ring-blue-500/20': !form.telefono
                                                        }">
                                                </div>
                                                <template x-if="form.telefono && telefonoValido()">
                                                    <p class="mt-1.5 text-xs text-emerald-600 font-medium">Número válido ✓</p>
                                                </template>
                                                <template x-if="form.telefono && !telefonoValido()">
                                                    <p class="mt-1.5 text-xs text-rose-500 font-medium" x-text="obtenerErrorTelefono()"></p>
                                                </template>
                                                <template x-if="!form.telefono && errors.telefono">
                                                    <p class="mt-1.5 text-xs text-rose-500 font-medium" x-text="errors.telefono[0]"></p>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Dirección -->
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-1.5">
                                                <i class="fas fa-map-marker-alt text-slate-400 mr-1"></i> Dirección
                                            </label>
                                            <textarea rows="2" x-model="form.direccion"
                                                placeholder="Dirección física o sucursal principal..."
                                                class="w-full px-4 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all resize-none shadow-sm"></textarea>
                                            <template x-if="errors.direccion">
                                                <p class="mt-1.5 text-xs text-red-500 font-medium" x-text="errors.direccion[0]"></p>
                                            </template>

                                            <div class="mt-2 flex items-start gap-2 rounded-xl bg-blue-50 border border-blue-100 px-4 py-3">
                                                <i class="fas fa-circle-info text-blue-500 mt-0.5"></i>
                                                <p class="text-xs text-blue-700 leading-relaxed">
                                                    <span class="font-semibold">Nota:</span>
                                                    El catálogo del proveedor puede configurarse posteriormente desde
                                                    <span class="font-semibold">"Ver Detalles"</span>.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 md:px-8 py-5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl">
                            <button @click="openModal = false" type="button"
                                class="w-full sm:w-auto px-6 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                                CANCELAR
                            </button>
                            <button type="submit" :disabled="loading"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-black text-sm text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-slate-900 shadow-slate-900/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i :class="loading ? 'fas fa-spinner fa-spin text-xs' : 'fas fa-check text-xs'"></i>
                                <span x-text="loading ? 'GUARDANDO...' : (editando ? 'ACTUALIZAR CAMBIOS' : 'GUARDAR CAMBIOS')">GUARDAR CAMBIOS</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app>