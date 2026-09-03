<x-app title="Lista de Proveedores | AXStore">

    <!-- Envolvemos todo en x-data de Alpine para controlar el modal de registro -->
    <div x-data="{ openModal: false }" class="max-w-7xl mx-auto p-4 sm:p-6">

        <!-- ENCABEZADO Y BOTÓN NUEVO -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-truck-loading text-blue-600"></i> Directorio de Proveedores
            </h1>
            <button @click="openModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm flex items-center gap-2 transition-colors">
                <i class="fas fa-plus-circle"></i> Nuevo Proveedor
            </button>
        </div>

        <!-- GRID DE CARDS (PROVEEDORES) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Card de Ejemplo 1 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow group relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div> <!-- Detalle visual lateral -->

                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-xl">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">AgroInsumos Apastepeque</h3>
                        <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">Activo</span>
                    </div>
                </div>

                <div class="space-y-3 text-sm text-gray-600">
                    <p class="flex items-center gap-3">
                        <i class="fas fa-envelope text-gray-400 w-4"></i> contacto@agroapastepeque.com
                    </p>
                    <p class="flex items-center gap-3">
                        <i class="fas fa-phone text-gray-400 w-4"></i> +503 7777-8888
                    </p>
                    <p class="flex items-center gap-3 items-start">
                        <i class="fas fa-map-marker-alt text-gray-400 w-4 mt-1"></i>
                        <span>Calle Principal, desvío a la loma, San Vicente.</span>
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end gap-2 overflow-hidden">
                    <!-- Botón Catálogo -->
                    <button class="px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fas fa-box-open"></i> Catálogo
                    </button>

                    <!-- Botón Editar -->
                    <button class="px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fas fa-edit"></i> Editar
                    </button>

                    <!-- Botón Eliminar -->
                    <button class="px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                </div>
            </div>

            <!-- Card de Ejemplo 2 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow group relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>

                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-xl">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Distribuidora El Volcán</h3>
                        <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">Activo</span>
                    </div>
                </div>

                <div class="space-y-3 text-sm text-gray-600">
                    <p class="flex items-center gap-3">
                        <i class="fas fa-envelope text-gray-400 w-4"></i> ventas@elvolcan.sv
                    </p>
                    <p class="flex items-center gap-3">
                        <i class="fas fa-phone text-gray-400 w-4"></i> +503 2222-3333
                    </p>
                    <p class="flex items-center gap-3 items-start">
                        <i class="fas fa-map-marker-alt text-gray-400 w-4 mt-1"></i>
                        <span>Zona Industrial, San Salvador.</span>
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end gap-2 overflow-hidden">
                    <!-- Botón Catálogo -->
                    <button class="px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fas fa-box-open"></i> Catálogo
                    </button>

                    <!-- Botón Editar -->
                    <button class="px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fas fa-edit"></i> Editar
                    </button>

                    <!-- Botón Eliminar -->
                    <button class="px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                </div>
            </div>

        </div>

        <!-- MODAL DE REGISTRO (Oculto por defecto) -->
        <!-- MODAL DE REGISTRO (Oculto por defecto) -->
        <div x-show="openModal"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <!-- Fondo suave -->
            <div x-show="openModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-400/40 backdrop-blur-[1px] transition-opacity"></div>

            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Contenedor del Modal (Ampliamos a max-w-2xl para que quepan 2 columnas) -->
                <div x-show="openModal"
                    @click.away="openModal = false"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-truck-moving text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                    Registrar Nuevo Proveedor
                                </h3>

                                <!-- ESTRUCTURA EN GRID: 1 columna en móvil, 2 en pantallas grandes -->
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

                                    <!-- Input Nombre (Ocupa las 2 columnas) -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center justify-between">
                                            <span><i class="fas fa-building text-gray-400 mr-1"></i> Nombre de la Empresa <span class="text-red-500">*</span></span>
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
                                        <input type="text" placeholder="Ej. Accesorios Eléctricos S.A." class="w-full pl-3 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                    </div>

                                    <!-- Input Correo (1 columna) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-envelope text-gray-400 mr-1"></i> Correo Electrónico
                                        </label>
                                        <input type="email" placeholder="ventas@empresa.com" class="w-full pl-3 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                    </div>

                                    <!-- Input Teléfono (1 columna) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-phone text-gray-400 mr-1"></i> Teléfono
                                        </label>
                                        <input type="tel" placeholder="+503 0000-0000" class="w-full pl-3 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                    </div>

                                    <!-- Input Dirección (Ocupa 2 columnas) -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> Dirección
                                        </label>
                                        <textarea rows="2" placeholder="Dirección física o sucursal principal..." class="w-full pl-3 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"></textarea>
                                    </div>

                                    <!-- SECCIÓN DE CATÁLOGO HÍBRIDO (Enlace o Archivo) -->
                                    <div class="sm:col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center justify-between">
                                            <span><i class="fas fa-folder-open text-blue-600 mr-1"></i> Catálogo o Enlace del Proveedor</span>
                                            <span class="text-xs text-gray-500 font-normal">Soporta PDF, Excel, Imágenes o Links</span>
                                        </label>

                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <!-- Opción 1: Escribir enlace web (Ocupa 2 columnas) -->
                                            <div class="sm:col-span-2">
                                                <div class="relative flex items-center">
                                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                        <i class="fas fa-link"></i>
                                                    </span>
                                                    <input type="text" placeholder="Pega el enlace web o URL del catálogo..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white transition-all">
                                                </div>
                                            </div>

                                            <!-- Opción 2: O subir archivo local (1 columna) con nombre dinámico -->
                                            <div x-data="{ fileName: '' }">
                                                <label class="w-full flex items-center justify-center px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer transition-all">
                                                    <i class="fas fa-cloud-upload-alt text-blue-600 mr-2"></i>
                                                    <!-- Si hay archivo, muestra el nombre; si no, muestra el texto por defecto -->
                                                    <span class="truncate" x-text="fileName ? fileName : 'Subir archivo'">Subir archivo</span>

                                                    <!-- El evento @change lee el nombre del archivo seleccionado -->
                                                    <input type="file"
                                                        @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                                        accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg"
                                                        class="hidden">
                                                </label>

                                                <!-- Un pequeño aviso en texto si ya se seleccionó algo -->
                                                <template x-if="fileName">
                                                    <p class="text-[10px] text-green-600 mt-1 truncate flex items-center gap-1">
                                                        <i class="fas fa-check-circle"></i> <span x-text="fileName"></span>
                                                    </p>
                                                </template>
                                            </div>
                                        </div>
                                        <p class="text-[11px] text-gray-500 mt-1.5">Puedes pegar un enlace directo a su web o subir un archivo (PDF, Excel, Imagen) según lo que te comparta el proveedor. Puede dejar vacio en caso de no poseer catalogo</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones del Modal -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                        <button type="button" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm items-center gap-2 transition-colors">
                            <i class="fas fa-save"></i> Guardar Proveedor
                        </button>
                        <button @click="openModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm items-center gap-2 transition-colors">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app>