<x-app title="Perfil de Proveedor | AXStore">

    <div x-data="perfilProveedor({{ Js::from($proveedor) }}, {{ Js::from($catalogos) }}, {{ Js::from($historial) }})" class="max-w-7xl mx-auto p-4 sm:p-6">

        <!-- ENCABEZADO Y NAVEGACIÓN -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <a href="{{ route('proveedores.index') }}" class="text-sm text-gray-500 hover:text-blue-600 mb-2 inline-flex items-center gap-1 transition-colors">
                    <i class="fas fa-arrow-left"></i> Volver al directorio
                </a>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-building text-blue-600"></i> <span x-text="proveedor.nombre"></span>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full align-middle ml-2"
                        :class="proveedor.deleted_at ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'"
                        x-text="proveedor.deleted_at ? 'Deshabilitado' : 'Habilitado'">
                    </span>
                </h1>
            </div>
            
            
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- COLUMNA IZQUIERDA: DATOS DE CONTACTO -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Información de Contacto</h3>
                    
                    <div class="space-y-4 text-sm text-gray-600">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Correo Electrónico</p>
                            <p class="flex items-center gap-2">
                                <i class="fas fa-envelope text-gray-400"></i> 
                                <span x-text="proveedor.correo || 'No registrado'"></span>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Teléfono</p>
                            <p class="flex items-center gap-2">
                                <i class="fas fa-phone text-gray-400"></i> 
                                <span x-text="proveedor.telefono || 'No registrado'"></span>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Dirección Físíca</p>
                            <p class="flex items-start gap-2">
                                <i class="fas fa-map-marker-alt text-gray-400 mt-1"></i> 
                                <span x-text="proveedor.direccion || 'No registrada'"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: CATÁLOGOS E HISTORIAL -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- SECCIÓN DE CATÁLOGOS (La relación 1 a Muchos) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-folder-open text-blue-600"></i> Catálogos y Recursos
                        </h3>
                        <button @click="abrirModalCatalogo()" class="text-sm bg-blue-600 hover:bg-blue-700 text-white font-medium py-1.5 px-3 rounded-md transition-colors flex items-center gap-1.5">
                            <i class="fas fa-plus"></i> Añadir
                        </button>
                    </div>
                    
                    <div class="p-6">
                        <div x-show="catalogos.length === 0" class="text-center py-6 text-gray-500 border border-dashed border-gray-300 rounded-lg">
                            <i class="fas fa-file-excel text-2xl mb-2 text-gray-400"></i>
                            <p class="text-sm">Este proveedor no tiene catálogos o enlaces registrados.</p>
                        </div>

                        <ul x-show="catalogos.length > 0" class="divide-y divide-gray-100">
                            <template x-for="item in catalogos" :key="item.id">
                                <li class="py-3 flex items-center justify-between group">
                                    <div class="flex items-center gap-3">
                                        <!-- Icono dinámico según el tipo -->
                                        <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center text-lg border border-gray-100">
                                            <i class="fas" :class="{
                                                'fa-link text-blue-500': item.tipo === 'enlace',
                                                'fa-file-pdf text-red-500': item.tipo === 'pdf',
                                                'fa-file-excel text-green-600': item.tipo === 'excel',
                                                'fa-image text-purple-500': item.tipo === 'imagen'
                                            }"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800" x-text="item.nombre_referencia"></p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 transition-opacity">
                                        <a :href="item.tipo === 'enlace' ? item.ruta_destino : '/storage/' + item.ruta_destino" target="_blank" class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors">
                                            <i class="fas fa-external-link-alt"></i> Ver
                                        </a>
                                        <button @click="eliminarCatalogo(item.id)" class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- SECCIÓN HISTORIAL DE COMPRAS (Entradas) -->
                 <!--
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-history text-blue-600"></i> Historial de Compras (Entradas)
                        </h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase font-semibold text-gray-500">
                                <tr>
                                    <th class="px-6 py-3">Fecha</th>
                                    <th class="px-6 py-3">Factura/Ref</th>
                                    <th class="px-6 py-3">Variante</th>
                                    <th class="px-6 py-3 text-right">Cantidad</th>
                                    <th class="px-6 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr x-show="historial.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        No se han registrado compras a este proveedor.
                                    </td>
                                </tr>
                                <template x-for="compra in historial" :key="compra.id">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-3 whitespace-nowrap" x-text="compra.fecha_entrada"></td>
                                        <td class="px-6 py-3 whitespace-nowrap font-medium text-gray-800" x-text="compra.numero_factura || 'N/A'"></td>
                                        <td class="px-6 py-3" x-text="compra.variante_nombre"></td>
                                        <td class="px-6 py-3 text-right font-medium" x-text="compra.cantidad"></td>
                                        <td class="px-6 py-3 text-right font-bold text-green-600" x-text="'$' + compra.costo_total"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                    -->
            </div>
        </div>

        <!-- MODAL EXCLUSIVO PARA SUBIR CATÁLOGO / ENLACE -->
        <div x-show="modalCatalogo" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="modalCatalogo" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <div x-show="modalCatalogo" @click.away="modalCatalogo = false" x-transition class="relative z-10 bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-lg w-full border border-slate-100">
                    
                    <!-- Header Azul -->
                    <div class="bg-blue-600 px-6 py-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold flex items-center gap-2 text-base">
                                <i class="fas fa-file-circle-plus"></i>
                                <span>Añadir Nuevo Recurso</span>
                            </h3>
                            <p class="text-xs text-blue-100 mt-0.5">Agrega un enlace o sube un archivo (PDF, Excel, Img)</p>
                        </div>
                        <button type="button" @click="modalCatalogo = false" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all cursor-pointer">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form @submit.prevent="guardarCatalogo">
                        <div class="p-6 md:p-8 space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Nombre de Referencia <span class="text-red-500">*</span></label>
                                <input type="text" x-model="formCatalogo.nombre_referencia" placeholder="Ej. Catálogo Verano 2026" class="w-full px-4 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all shadow-sm" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Tipo de Recurso</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="border rounded-xl px-4 py-3 flex items-center justify-center gap-2 cursor-pointer transition-colors" :class="formCatalogo.tipo === 'enlace' ? 'border-blue-500 bg-blue-50 text-blue-700 font-bold ring-2 ring-blue-500/20' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-medium'">
                                        <input type="radio" x-model="formCatalogo.tipo" value="enlace" class="hidden">
                                        <i class="fas fa-link"></i> Enlace Web
                                    </label>
                                    <label class="border rounded-xl px-4 py-3 flex items-center justify-center gap-2 cursor-pointer transition-colors" :class="formCatalogo.tipo === 'archivo' ? 'border-blue-500 bg-blue-50 text-blue-700 font-bold ring-2 ring-blue-500/20' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-medium'">
                                        <input type="radio" x-model="formCatalogo.tipo" value="archivo" class="hidden">
                                        <i class="fas fa-file-upload"></i> Subir Archivo
                                    </label>
                                </div>
                            </div>

                            <div x-show="formCatalogo.tipo === 'enlace'" class="mt-3">
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">URL / Enlace</label>
                                <input type="url" x-model="formCatalogo.ruta_destino" placeholder="https://" class="w-full px-4 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all shadow-sm">
                            </div>

                            <div x-show="formCatalogo.tipo === 'archivo'" class="mt-3">
                                <label class="w-full flex items-center justify-center px-4 py-8 border-2 border-dashed border-slate-300 rounded-xl hover:bg-slate-50 hover:border-blue-400 cursor-pointer transition-colors bg-white">
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-blue-400 mb-3"></i>
                                        <p class="text-sm text-slate-700 font-bold" x-text="archivoSeleccionado ? archivoSeleccionado.name : 'Haz clic para seleccionar archivo'"></p>
                                        <p class="text-xs text-slate-400 mt-1">PDF, Excel o Imágenes</p>
                                    </div>
                                    <input type="file" @change="archivoSeleccionado = $event.target.files[0]" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg" class="hidden">
                                </label>
                            </div>
                        </div>

                        <div class="px-6 md:px-8 py-5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="modalCatalogo = false" class="w-full sm:w-auto px-6 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">CANCELAR</button>
                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-black text-sm text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-slate-900 shadow-slate-900/20">
                                <i class="fas fa-check text-xs"></i> GUARDAR CAMBIOS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app>