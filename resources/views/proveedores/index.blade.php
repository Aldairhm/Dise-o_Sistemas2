<x-app title="Lista de Proveedores | AXStore">

    <div x-data="gestionProveedores({{ Js::from($proveedores) }})" class="max-w-7xl mx-auto p-4 sm:p-6">

        <!-- ENCABEZADO Y BUSCADOR INTEGRADO -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">

            <!-- 1. Título (Izquierda) -->
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2 shrink-0">
                <i class="fas fa-truck-loading text-blue-600"></i> Directorio de Proveedores
            </h1>

            <!-- 2. Buscador (Centro) -->
            <div class="relative w-full md:max-w-md flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" x-model="busqueda" @input="paginaActual = 1"
                    placeholder="Buscar por nombre o correo..."
                    class="custom-input w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all bg-white">
            </div>

            <!-- 3. Botón (Derecha) -->
            <button @click="abrirCrear()" class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-lg shadow-blue-500/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer shrink-0 w-full md:w-auto">
                <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-xs">
                    <i class="fas fa-plus"></i>
                </div>
                <span>AGREGAR</span>
            </button>

        </div>

        <!-- CONTENEDOR DE CARDS CON LOADER -->
        <div class="relative min-h-[200px]">
            <!-- LOADING LISTA -->
            <div x-show="loadingLista" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-20">
                <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-2xl shadow-xl border border-slate-100 text-blue-600 text-sm font-bold">
                    <i class="fas fa-spinner fa-spin text-lg"></i>
                    <span>Buscando proveedores...</span>
                </div>
            </div>

            <!-- GRID DE CARDS DINÁMICAS -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Mensaje si no hay datos -->
            <div x-show="proveedores.length === 0" class="col-span-full text-center py-12 text-gray-500 bg-white rounded-xl border border-dashed border-gray-300">
                <i class="fas fa-folder-open text-3xl mb-3 text-gray-400"></i>
                <p>No hay proveedores registrados aún.</p>
            </div>

            <!-- El bucle de Alpine que recorre el arreglo de proveedores -->
            <template x-for="proveedor in proveedoresPaginados" :key="proveedor.id">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow group relative overflow-hidden flex flex-col h-full"
                    :class="proveedor.deleted_at ? 'opacity-70 bg-gray-50' : ''"> <!-- Se opaca si está deshabilitado -->

                    <!-- Detalle visual lateral -->
                    <div class="absolute top-0 left-0 w-1 h-full transition-colors"
                        :class="proveedor.deleted_at ? 'bg-gray-400' : 'bg-blue-500'"></div>

                    <!-- Cabecera de la Card -->
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl transition-colors"
                            :class="proveedor.deleted_at ? 'bg-gray-200 text-gray-500' : 'bg-blue-50 text-blue-600'">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors" x-text="proveedor.nombre"></h3>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full"
                                :class="proveedor.deleted_at ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'"
                                x-text="proveedor.deleted_at ? 'Deshabilitado' : 'Habilitado'">
                            </span>
                        </div>
                    </div>

                    <!-- Datos de contacto -->
                    <div class="space-y-3 text-sm text-gray-600">
                        <p class="flex items-center gap-3"><i class="fas fa-envelope text-gray-400 w-4"></i> <span x-text="proveedor.correo || 'Correo electronico aun no asignado'"></span></p>

                        <p class="flex items-center gap-3"><i class="fas fa-phone text-gray-400 w-4"></i> <span x-text="proveedor.telefono || 'Telefono aun no asignado' "></span></p>

                        <p class="flex items-center gap-3 items-start"><i class="fas fa-map-marker-alt text-gray-400 w-4 mt-1"></i> <span x-text="proveedor.direccion || 'Direccion fisica aun no asignada'"></span></p>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="mt-auto pt-4 border-t border-gray-100 flex justify-end gap-2 overflow-hidden">

                        <!-- Botón Ver Detalles / Perfil -->
                        <a :href="'/proveedor/' + proveedor.id"
                            class="px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fas fa-folder-open text-blue-600"></i> Ver Detalles
                        </a>

                        <!-- Botón Editar -->
                        <button @click="abrirEditar(proveedor)" class="px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fas fa-edit"></i> Editar
                        </button>

                        <!-- Botón Eliminar / Habilitar -->
                        <button @click="cambiarEstado(proveedor)"
                            class="px-2.5 py-1.5 text-xs font-medium rounded-md transition-colors flex items-center gap-1.5 whitespace-nowrap"
                            :class="proveedor.deleted_at ? 'text-green-700 bg-green-50 hover:bg-green-100' : 'text-red-700 bg-red-50 hover:bg-red-100'">
                            <i class="fas" :class="proveedor.deleted_at ? 'fa-check-circle' : 'fa-trash-alt'"></i>
                            <span x-text="proveedor.deleted_at ? 'Habilitar' : 'Eliminar'"></span>
                        </button>
                    </div>
                </div>
            </template>
        </div> <!-- Fin grid -->
        </div> <!-- Fin contenedor relativo -->

        <!-- CONTROLES DE PAGINACIÓN -->
        <div x-show="totalPaginas > 1" class="mt-8 flex justify-between items-center bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <button @click="irPagina(paginaActual - 1)"
                :disabled="paginaActual === 1"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-chevron-left"></i> Anterior
            </button>

            <div class="text-sm text-gray-600 font-medium">
                Página <span x-text="paginaActual" class="font-bold text-gray-900"></span> de <span x-text="totalPaginas" class="font-bold text-gray-900"></span>
            </div>

            <button @click="irPagina(paginaActual + 1)"
                :disabled="paginaActual === totalPaginas"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                Siguiente <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- MODAL: CREAR / EDITAR (mismo modal para ambos casos) -->
        <div x-show="openModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <div x-show="openModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-400/40 backdrop-blur-[1px] transition-opacity"></div>

            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:items-center sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="openModal" @click.away="openModal = false"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full border border-slate-100">

                    <!-- Header Azul -->
                    <div class="bg-blue-600 px-6 py-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold flex items-center gap-2 text-base">
                                <i class="fas fa-truck-moving"></i>
                                <span x-text="editando ? 'Editar Proveedor' : 'Nuevo Proveedor'"></span>
                            </h3>
                            <p class="text-xs text-blue-100 mt-0.5">Completa los campos para registrar un proveedor</p>
                        </div>
                        <button @click="openModal = false" type="button" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all cursor-pointer">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form @submit.prevent="guardarProveedor">
                        <div class="p-6 md:p-8">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                                <!-- Left Column: Info Preview -->
                                <div class="md:col-span-4 flex flex-col">
                                    <h4 class="text-xs font-black text-slate-500 tracking-widest uppercase mb-4">Información del Proveedor</h4>
                                    <div class="bg-white rounded-2xl border-2 border-slate-100 p-6 flex flex-col items-center text-center shadow-sm relative overflow-hidden h-full">
                                        <div class="absolute top-0 left-0 right-0 h-1 bg-blue-500"></div>
                                        
                                        <div class="w-24 h-24 rounded-2xl flex items-center justify-center mb-4 transition-all duration-300 bg-blue-500 shadow-[0_10px_25px_-5px_rgba(59,130,246,0.4)]">
                                            <i class="fas fa-building text-white text-4xl"></i>
                                        </div>
                                        
                                        <h3 class="text-xl font-black text-slate-800 mb-2 truncate w-full" x-text="form.nombre || 'Nuevo Proveedor'"></h3>
                                        
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
                                                <span class="text-slate-400"><i class="fas fa-envelope mr-1"></i> Correo:</span>
                                                <span class="font-bold text-slate-700 truncate max-w-[120px]" x-text="form.correo || '---'"></span>
                                            </div>
                                            <div class="flex justify-between items-center text-sm mb-2">
                                                <span class="text-slate-400"><i class="fas fa-phone mr-1"></i> Teléfono:</span>
                                                <span class="font-bold text-slate-700 truncate max-w-[120px]" x-text="form.telefono || '---'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Column: Fields -->
                                <div class="md:col-span-8 flex flex-col">
                                    <h4 class="text-xs font-black text-slate-500 tracking-widest uppercase mb-4">Datos del Proveedor</h4>
                                    
                                    <div class="space-y-5 flex-1">
                                        <!-- Nombre -->
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                                <span>Nombre de la Empresa <span class="text-red-500">*</span></span>
                                                <div x-data="{ showInfo: false }" class="relative flex items-center">
                                                    <button @mouseenter="showInfo = true" @mouseleave="showInfo = false" @click="showInfo = !showInfo" type="button" class="text-gray-400 hover:text-blue-500 transition-colors">
                                                        <i class="fas fa-circle-question"></i>
                                                    </button>
                                                    <div x-show="showInfo" style="display: none;" class="absolute bottom-full right-0 mb-2 w-48 p-2 bg-gray-800 text-xs text-white rounded shadow-lg z-10">
                                                        Ingresa el nombre comercial o razón social completa.
                                                        <div class="absolute top-full right-2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                                    </div>
                                                </div>
                                            </label>
                                            <input type="text" x-model="form.nombre"
                                                   class="custom-input w-full px-4 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all shadow-sm"
                                                   placeholder="Ej: Accesorios Eléctricos S.A.">
                                            <p class="mt-1.5 text-xs text-slate-400">Nombre de la empresa que verán los usuarios (Campo obligatorio)</p>
                                            <template x-if="errors.nombre">
                                                <p class="mt-1 text-xs text-red-500 font-medium" x-text="errors.nombre[0]"></p>
                                            </template>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <!-- Correo -->
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Correo Electrónico</label>
                                                <input type="email" x-model="form.correo" @blur="validarCorreo"
                                                       class="custom-input w-full px-4 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all shadow-sm"
                                                       placeholder="ventas@empresa.com"
                                                       :class="errors.correo ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20' : ''">
                                                <template x-if="errors.correo">
                                                    <p class="mt-1 text-xs text-red-500 font-medium" x-text="errors.correo[0]"></p>
                                                </template>
                                            </div>

                                            <!-- Teléfono -->
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Teléfono</label>
                                                <input type="text" inputmode="numeric" :value="form.telefono" @input="formatearTelefono($event)" maxlength="9"
                                                       class="custom-input w-full px-4 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all shadow-sm"
                                                       placeholder="0000 0000"
                                                       :class="errors.telefono ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20' : ''">
                                                <template x-if="errors.telefono">
                                                    <p class="mt-1 text-xs text-red-500 font-medium" x-text="errors.telefono[0]"></p>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Dirección -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-envelope text-gray-400 mr-1"></i> Correo Electrónico
                                            </label>
                                            <input type="email"
                                                x-model="form.correo"
                                                @blur="validarCorreo"
                                                placeholder="ventas@empresa.com"
                                                class="w-full pl-3 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                                :class="errors.correo ? 'border-red-400' : 'border-gray-300'">
                                            <template x-if="errors.correo">
                                                <p class="text-red-500 text-xs mt-1" x-text="errors.correo[0]"></p>
                                            </template>
                                        </div>

                                        <!-- Input Teléfono -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-phone text-gray-400 mr-1"></i> Teléfono
                                            </label>
                                            <input type="text"
                                                inputmode="numeric"
                                                :value="form.telefono"
                                                @input="formatearTelefono($event)"
                                                @keydown="if(!/[0-9]/.test($event.key) && !['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete'].includes($event.key)) $event.preventDefault()"
                                                maxlength="9"
                                                placeholder="0000 0000"
                                                class="w-full pl-3 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                                :class="errors.telefono ? 'border-red-400' : 'border-gray-300'">
                                            <template x-if="errors.telefono">
                                                <p class="text-red-500 text-xs mt-1" x-text="errors.telefono[0]"></p>
                                            </template>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> Dirección
                                            </label>

                                            <textarea
                                                rows="2"
                                                x-model="form.direccion"
                                                placeholder="Dirección física o sucursal principal..."
                                                class="w-full pl-3 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"></textarea>

                                            <template x-if="errors.direccion">
                                                <p class="mt-1 text-xs text-red-500 font-medium" x-text="errors.direccion[0]"></p>
                                            </template>

                                            <!-- Nota informativa -->
                                            <div class="mt-2 flex items-start gap-2 rounded-xl bg-blue-50 border border-blue-100 px-4 py-3">
                                                <i class="fas fa-circle-info text-blue-500 mt-0.5"></i>
                                                <p class="text-xs text-blue-700 leading-relaxed">
                                                    <span class="font-semibold">Nota:</span>
                                                    El catálogo del proveedor puede configurarse posteriormente desde
                                                    <span class="font-semibold">“Ver Detalles”</span>.
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 md:px-8 py-5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl">
                            <button @click="openModal = false" type="button" class="w-full sm:w-auto px-6 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                                CANCELAR
                            </button>
                            <button type="submit" :disabled="loading" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-black text-sm text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-slate-900 shadow-slate-900/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i :class="loading ? 'fas fa-spinner fa-spin text-xs' : 'fas fa-check text-xs'"></i>
                                <span x-text="loading ? 'GUARDANDO...' : (editando ? 'ACTUALIZAR CAMBIOS' : 'GUARDAR CAMBIOS')"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>

    </div>
</x-app>