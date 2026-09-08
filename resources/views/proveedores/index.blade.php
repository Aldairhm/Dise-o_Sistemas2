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
                    class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all bg-white">
            </div>

            <!-- 3. Botón (Derecha) -->
            <button @click="abrirCrear()" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm flex items-center justify-center gap-2 transition-colors shrink-0">
                <i class="fas fa-plus-circle"></i> Nuevo Proveedor
            </button>

        </div>

        <!-- LOADING LISTA -->
        <div x-show="loadingLista" class="text-center text-gray-500 py-10">
            <i class="fas fa-spinner fa-spin mr-2"></i> Cargando proveedores...
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
                    :class="proveedor.deleted_at ? 'opacity-70 bg-gray-50' : ''"> <!-- Se opaca si está inactivo -->

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
                                x-text="proveedor.deleted_at ? 'Inactivo' : 'Activo'">
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

                        <!-- Botón Eliminar / Activar -->
                        <button @click="cambiarEstado(proveedor)"
                            class="px-2.5 py-1.5 text-xs font-medium rounded-md transition-colors flex items-center gap-1.5 whitespace-nowrap"
                            :class="proveedor.deleted_at ? 'text-green-700 bg-green-50 hover:bg-green-100' : 'text-red-700 bg-red-50 hover:bg-red-100'">
                            <i class="fas" :class="proveedor.deleted_at ? 'fa-check-circle' : 'fa-trash-alt'"></i>
                            <span x-text="proveedor.deleted_at ? 'Activar' : 'Eliminar'"></span>
                        </button>
                    </div>
                </div>
            </template>
        </div>

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

            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="openModal" @click.away="openModal = false"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">

                    <form @submit.prevent="guardarProveedor">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="fas fa-truck-moving text-blue-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title"
                                        x-text="editando ? 'Editar Proveedor' : 'Registrar Nuevo Proveedor'"></h3>

                                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

                                        <div class="sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center justify-between">
                                                <span><i class="fas fa-building text-gray-400 mr-1"></i> Nombre de la Empresa <span class="text-red-500">*</span></span>

                                                <!-- TOOLTIP RECUPERADO -->
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
                                            <input type="text" x-model="form.nombre" placeholder="Ej. Accesorios Eléctricos S.A." class="w-full pl-3 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                            <template x-if="errors.nombre">
                                                <p class="text-red-500 text-xs mt-1" x-text="errors.nombre[0]"></p>
                                            </template>
                                        </div>

                                        <!-- Input Correo -->
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
                                                <p class="text-red-500 text-xs mt-1" x-text="errors.direccion[0]"></p>
                                            </template>

                                            <!-- Nota informativa -->
                                            <div class="mt-2 flex items-start gap-2 rounded-lg bg-blue-50 border border-blue-100 px-3 py-2.5">
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

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                            <button type="submit" :disabled="loading" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm items-center gap-2 transition-colors disabled:opacity-50">
                                <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>
                                <span x-text="loading ? 'Guardando...' : (editando ? 'Actualizar Proveedor' : 'Guardar Proveedor')"></span>
                            </button>
                            <button @click="openModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm items-center gap-2 transition-colors">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</x-app>