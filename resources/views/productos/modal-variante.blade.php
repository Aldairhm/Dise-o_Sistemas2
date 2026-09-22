{{-- Modal Variante — Estilo unificado con el sistema de modales del proyecto --}}
<div id="modalVariante" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="modalVarianteLabel">
    {{-- Backdrop --}}
    <div id="modalVarianteBackdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

    {{-- Panel --}}
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div id="modalVariantePanel"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all duration-300 scale-95 opacity-0 sm:my-8 sm:w-full sm:max-w-2xl border border-slate-100">

                {{-- Header Azul --}}
                <div class="bg-blue-600 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-white font-bold flex items-center gap-2 text-base">
                            <i class="fas fa-layer-group"></i>
                            <span id="modalVarianteLabel">Nueva Variante</span>
                        </h3>
                        <p class="text-xs text-blue-100 mt-0.5" id="modalVarianteSubtitle">Completa los campos para registrar la variante</p>
                    </div>
                    <button type="button" id="btnCerrarModalVariante"
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Formulario --}}
                <form id="formVariante" enctype="multipart/form-data" novalidate>
                    <input type="hidden" id="variante_id" name="variante_id" value="">
                    <input type="hidden" id="imagen_principal_index" name="imagen_principal_index" value="-1">
                    <input type="hidden" id="imagen_existente_principal_id" name="imagen_existente_principal_id" value="">
                    <div id="deletedImagesContainer"></div>

                    {{-- Body --}}
                    <div class="p-6 md:p-8 space-y-5">

                        {{-- Alerta de errores inline --}}
                        <div id="modalVarianteError" class="hidden px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-2">
                            <i class="fas fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
                            <p id="modalVarianteErrorMsg"></p>
                        </div>

                        {{-- Fila 1: Nombre + SKU (auto) + Estado --}}
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-5">
                            <div class="sm:col-span-5">
                                <label for="nombre_variante" class="block text-sm font-bold text-slate-700 mb-1.5">
                                    Nombre de Variante <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nombre_variante" name="nombre_variante" required
                                       class="w-full px-4 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all shadow-sm"
                                       placeholder="Ej: Rojo - Talla M">
                                <p class="mt-1.5 text-xs text-slate-400">Combinación de atributos que identifica esta variante.</p>
                            </div>

                            <div class="sm:col-span-4">
                                <label for="sku" class="block text-sm font-bold text-slate-700 mb-1.5">
                                    SKU <span class="text-xs font-normal text-slate-400">(generado)</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="sku" name="sku" readonly
                                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-500 font-mono cursor-not-allowed focus:outline-none shadow-sm"
                                           placeholder="SKU-XXXX">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 text-xs">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                </div>
                                <p class="mt-1.5 text-xs text-slate-400">Código único al guardar.</p>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="estado" class="block text-sm font-bold text-slate-700 mb-1.5">
                                    Estado
                                </label>
                                <select id="estado" name="estado"
                                        class="w-full px-4 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all shadow-sm">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        {{-- Fila Atributos --}}
                        @if(isset($producto) && $producto->atributos && $producto->atributos->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                            <div class="col-span-full mb-1">
                                <label class="block text-sm font-bold text-slate-700">
                                    <i class="fas fa-tags text-blue-500 mr-1"></i> Valores de Atributos
                                </label>
                                <p class="text-xs text-slate-500 mt-1">Define los valores específicos para esta variante (ej. Rojo, M).</p>
                            </div>
                            @foreach($producto->atributos as $atributo)
                            <div>
                                <label for="atributo_{{ $atributo->id }}" class="block text-xs font-bold text-slate-600 mb-1.5">
                                    {{ $atributo->nombre }}
                                </label>
                                <input type="text" id="atributo_{{ $atributo->id }}" name="valores[{{ $atributo->id }}]" 
                                       class="atributo-input w-full px-3 py-2 bg-white border border-slate-200 focus:border-blue-500 rounded-lg text-sm text-slate-800 placeholder:text-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all shadow-sm"
                                       placeholder="Valor">
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Fila 2: Costo, Ganancia y Precio Calculado --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <label for="costo_promedio" class="block text-sm font-bold text-slate-700 mb-1.5">
                                    Costo Promedio <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-bold">$</span>
                                    <input type="number" step="0.01" min="0" id="costo_promedio" name="costo_promedio" required
                                           class="w-full pl-7 pr-3 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all shadow-sm"
                                           placeholder="0.00">
                                </div>
                            </div>
                            
                            <div>
                                <label for="porcentaje_ganancia" class="block text-sm font-bold text-slate-700 mb-1.5">
                                    Ganancia (%) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" step="1" min="0" id="porcentaje_ganancia" name="porcentaje_ganancia" required
                                           class="w-full pl-4 pr-7 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all shadow-sm"
                                           placeholder="Ej: 30">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-bold">%</span>
                                </div>
                            </div>

                            <div>
                                <label for="precio_venta" class="block text-sm font-bold text-slate-700 mb-1.5">
                                    Precio Venta <span class="text-xs font-normal text-slate-400">(auto)</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-bold">$</span>
                                    <input type="number" step="0.01" min="0" id="precio_venta" name="precio_venta" readonly tabindex="-1"
                                           class="w-full pl-7 pr-3 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-500 font-bold cursor-not-allowed focus:outline-none shadow-sm"
                                           placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        {{-- Fila 3: Imágenes Drag & Drop --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">
                                Imágenes de la variante
                            </label>
                            <div class="drag-drop-area" id="dropZoneImagenes">
                                <i class="fas fa-cloud-upload-alt text-2xl text-blue-400 mb-2 block"></i>
                                <p class="text-sm font-semibold text-slate-600">Arrastra imágenes aquí</p>
                                <p class="text-xs text-slate-400 mt-1">o haz clic para seleccionar · PNG, JPG, WEBP</p>
                                <input type="file" id="imagenesVarianteInput" name="imagenes[]" multiple accept="image/*" class="hidden">
                            </div>
                            <div class="preview-zone mt-3 flex flex-wrap gap-2" id="previewImagenes"></div>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="px-6 md:px-8 py-5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" id="btnCancelarVariante"
                                class="px-6 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                            CANCELAR
                        </button>
                        <button type="submit" id="btnGuardarVariante"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-black text-sm text-white bg-slate-900 hover:bg-slate-800 shadow-lg shadow-slate-900/20 transition-all duration-300 hover:scale-[1.02] cursor-pointer">
                            <i class="fas fa-check"></i>
                            <span id="btnGuardarTexto">GUARDAR VARIANTE</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
