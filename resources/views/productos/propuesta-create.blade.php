<x-app title="{{ $modoEdicion ? 'Editar producto' : 'Crear producto' }} | AXStore">
    <meta name="producto-propuesta-store-url" content="{{ $modoEdicion ? route('productos.update', $producto['id']) : route('productos.store') }}">
    <meta name="producto-propuesta-mode" content="{{ $modoEdicion ? 'edit' : 'create' }}">
    <meta name="producto-propuesta-data" content="{{ base64_encode(json_encode(['producto' => $producto, 'atributos' => $atributosIniciales, 'variantes' => $variantesIniciales], JSON_UNESCAPED_UNICODE)) }}">

    <div x-data="productoPropuesta()" class="max-w-[1180px] mx-auto space-y-6">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('productos.index') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 mb-4 transition-colors"><i class="fas fa-arrow-left"></i> Productos</a>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/20"><i class="fas fa-box-open text-xl"></i></div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600 mb-1">Catálogo</p>
                        <h1 class="text-3xl font-black tracking-tight text-slate-900">{{ $modoEdicion ? 'Editar producto' : 'Crear producto' }}</h1>
                    </div>
                </div>
                <p class="text-sm text-slate-500 mt-3 max-w-2xl">Registra un producto único en un par de pasos, o define sus variaciones (talla, color, etc.) y deja que el sistema arme cada versión por ti.</p>
            </div>
        </header>

        <div x-show="guardado" x-transition class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700 shadow-sm" style="display:none;"><i class="fas fa-circle-check text-lg"></i><span>Producto guardado correctamente en el catálogo.</span></div>
        <div x-show="error" x-transition class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700 shadow-sm" style="display:none;"><i class="fas fa-circle-exclamation text-lg"></i><span x-text="error"></span></div>

        <!-- Navegación por Pasos (se ajusta según si el producto tiene variantes) -->
        <nav class="grid gap-2 sm:gap-4 mb-8" :class="tieneVariantes === 'no' ? 'grid-cols-2' : 'grid-cols-3'" aria-label="Pasos del producto">
            <template x-for="item in pasosNav()" :key="item.id">
                <button type="button" @click="paso > item.id && volver(item.id)" class="text-left border-b-2 pb-3 transition-all" :class="paso >= item.id ? 'border-blue-600' : 'border-slate-200 hover:border-slate-300'">
                    <span class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black transition-colors" :class="paso >= item.id ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'bg-slate-100 text-slate-400'" x-text="item.id"></span>
                        <span class="text-sm font-bold" :class="paso >= item.id ? 'text-slate-900' : 'text-slate-400'" x-text="item.label"></span>
                    </span>
                </button>
            </template>
        </nav>

        <!-- PASO 1: Producto Base -->
        <section x-show="paso === 1" x-transition.opacity class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-slate-100">
                <h2 class="text-xl font-black text-slate-900">Información del producto</h2>
                <p class="text-sm text-slate-500 mt-1">Estos datos describen el artículo general.</p>
            </div>
            <div class="p-6 sm:p-8 grid gap-6 md:grid-cols-12">
                <label class="block md:col-span-6">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nombre del producto <b class="text-red-500">*</b></span>
                    <input x-model="nombre" type="text" placeholder="Ej. Camiseta Premium Algodón" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                </label>
                <label class="block md:col-span-6">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Categoría <b class="text-red-500">*</b></span>
                    <select x-model="categoriaId" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <option value="">Selecciona una categoría</option>
                        @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block md:col-span-8">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Descripción <span class="font-normal text-slate-400 normal-case">(opcional)</span></span>
                    <textarea x-model="descripcion" rows="2" placeholder="Describe el producto general..." class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all resize-none"></textarea>
                </label>
                <label class="block md:col-span-4">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2" title="Se heredará a todas las variantes nuevas">Comisión Base ($) <b class="text-red-500">*</b></span>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                        <input x-model="comisionGeneral" type="number" step="0.01" min="0" placeholder="0.00" class="w-full rounded-xl border border-slate-300 bg-slate-50 pl-8 pr-4 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5 leading-tight">Valor por defecto para la comisión.</p>
                </label>

                <!-- SWITCH: ¿tiene variantes? -->
                <div class="block md:col-span-12 pt-2">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">¿Este producto tiene variantes? <b class="text-red-500">*</b></span>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="flex items-start gap-3 rounded-xl border-2 p-4 cursor-pointer transition-all" :class="tieneVariantes === 'no' ? 'border-blue-500 bg-blue-50/50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" x-model="tieneVariantes" value="no" class="mt-1">
                            <span>
                                <span class="block text-sm font-black text-slate-900">No, es un producto único</span>
                                <span class="block text-xs text-slate-500 mt-0.5">Ej. una silla de oficina específica, con un solo precio y SKU.</span>
                            </span>
                        </label>
                        <label class="flex items-start gap-3 rounded-xl border-2 p-4 cursor-pointer transition-all" :class="tieneVariantes === 'si' ? 'border-blue-500 bg-blue-50/50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" x-model="tieneVariantes" value="si" class="mt-1">
                            <span>
                                <span class="block text-sm font-black text-slate-900">Sí, viene en varias opciones</span>
                                <span class="block text-xs text-slate-500 mt-0.5">Ej. una camiseta en varias tallas y colores.</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="px-6 sm:px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="button" @click="siguiente()" :disabled="camposFaltantesPaso1().length > 0" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white px-6 py-3 text-sm font-black transition-all shadow-lg shadow-blue-600/20 disabled:shadow-none">
                    <span x-text="tieneVariantes === 'no' ? 'Continuar' : 'Configurar atributos'"></span> <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </section>

        <!-- PASO 2 (registro rápido): producto sin variantes -->
        <section x-show="paso === 2 && tieneVariantes === 'no'" x-transition.opacity class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" style="display:none;">
            <div class="p-6 sm:p-8 border-b border-slate-100">
                <h2 class="text-xl font-black text-slate-900">Detalles del producto</h2>
                <p class="text-sm text-slate-500 mt-1">Como no tiene variantes, con esto queda listo para el catálogo.</p>
            </div>
            <template x-if="combinaciones.length">
                <div class="p-6 sm:p-8 grid gap-6 md:grid-cols-12">
                    <label class="block md:col-span-4">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">SKU <span class="font-normal normal-case text-slate-400">(opcional)</span></span>
                        <input x-model="combinaciones[0].sku" readonly type="text" placeholder="Se genera al guardar" class="w-full rounded-xl border border-slate-300 bg-slate-100 px-4 py-2.5 text-sm font-mono text-slate-500 cursor-not-allowed">
                    </label>
                    <label class="block md:col-span-4">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Precio venta <b class="text-red-500">*</b></span>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                            <input x-model="combinaciones[0].precio_venta" type="number" min="0" step="0.01" placeholder="0.00" class="w-full rounded-xl border border-slate-300 bg-slate-50 pl-8 pr-4 py-2.5 text-sm font-bold focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                        </div>
                    </label>
                    <label class="block md:col-span-4">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Comisión <b class="text-red-500">*</b></span>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-500 font-bold">$</span>
                            <input x-model="combinaciones[0].comision" type="number" min="0" step="0.01" class="w-full rounded-xl border border-emerald-200 bg-emerald-50 pl-8 pr-4 py-2.5 text-sm font-bold text-emerald-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        </div>
                    </label>
                    <div class="block md:col-span-12">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Imágenes <span class="font-normal normal-case text-slate-400">(opcional · la primera será principal)</span></span>
                        <label class="inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-blue-300 bg-blue-50/50 px-4 py-2.5 text-sm font-bold text-blue-700 hover:bg-blue-100 cursor-pointer transition-colors">
                            <i class="fas fa-images"></i> Agregar imágenes
                            <input type="file" accept="image/jpeg,image/png,image/gif,image/webp" multiple class="hidden" @change="seleccionarImagenes($event, combinaciones[0])">
                        </label>
                        <div x-show="combinaciones[0].imagenes.length" class="flex flex-wrap gap-1.5 mt-3" style="display:none;">
                            <template x-for="(imagen, imagenIndice) in combinaciones[0].imagenes" :key="imagenIndice">
                                <div class="relative group w-12 h-12 rounded-lg overflow-hidden border border-slate-200 bg-slate-100">
                                    <img :src="imagen.existing ? imagen.url : URL.createObjectURL(imagen)" class="w-full h-full object-cover" :alt="imagen.name">
                                    <button type="button" @click="quitarImagen(combinaciones[0], imagenIndice)" class="absolute top-0.5 right-0.5 w-5 h-5 rounded-full bg-red-600 text-white text-[10px] opacity-0 group-hover:opacity-100 transition-opacity"><i class="fas fa-xmark"></i></button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
            <div class="px-6 sm:px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-between gap-3">
                <button type="button" @click="volver(1)" class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 transition-colors"><i class="fas fa-arrow-left mr-2"></i> Volver</button>
                <button type="button" @click="guardarProducto()" :disabled="!formularioValido() || guardando" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed text-white px-6 py-3 text-sm font-black transition-all shadow-lg shadow-emerald-600/20 disabled:shadow-none">
                    <i class="fas fa-check" x-show="!guardando"></i>
                    <i class="fas fa-circle-notch fa-spin" x-show="guardando" style="display:none;"></i>
                    <span x-text="guardando ? 'Guardando...' : 'Guardar producto'"></span>
                </button>
            </div>
        </section>

        <!-- PASO 2 (flujo combinatorio): Atributos -->
        <section x-show="paso === 2 && tieneVariantes === 'si'" x-transition.opacity class="space-y-5" style="display:none;">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-black text-slate-900">Variaciones del producto</h2>
                        <p class="text-sm text-slate-500 mt-1 max-w-xl">Ej. si vendes camisetas en varias tallas y colores, agrégalos aquí: el sistema arma automáticamente cada versión posible para que solo completes precio e imagen.</p>
                    </div>
                    <button type="button" @click="agregarAtributo()" class="rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 px-5 py-2.5 text-sm font-black transition-colors border border-blue-200">
                        <i class="fas fa-plus mr-1"></i> Agregar atributo
                    </button>
                </div>
                <div class="p-6 sm:p-8 space-y-6">
                    <template x-for="(atributo, indice) in atributos" :key="indice">
                        <div class="rounded-2xl border-2 border-dashed border-slate-200 p-5 bg-slate-50/50 transition-colors focus-within:border-blue-300 focus-within:bg-blue-50/20 relative group">
                            <button type="button" @click="quitarAtributo(indice)" class="absolute top-4 right-4 text-slate-400 hover:text-red-500 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50" title="Eliminar atributo"><i class="fas fa-trash-can"></i></button>

                            <div class="w-full sm:w-1/3 mb-4 pr-10">
                                <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nombre del atributo</span>
                                <input x-model="atributo.nombre" type="text" placeholder="Ej. Talla" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                            </div>

                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Valores para <span class="text-blue-600" x-text="atributo.nombre || 'este atributo'"></span></span>
                                <div class="flex flex-wrap gap-2 items-center">
                                    <template x-for="(valor, valorIndice) in atributo.valores" :key="valorIndice">
                                        <div class="flex items-center gap-1 rounded-lg bg-white border border-slate-300 pl-3 pr-1 py-1 shadow-sm focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20">
                                            <input x-model="atributo.valores[valorIndice]" type="text" placeholder="Ej. XL" class="w-24 bg-transparent border-0 p-0 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-0">
                                            <button type="button" @click="quitarValor(atributo, valorIndice)" class="w-6 h-6 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-colors"><i class="fas fa-xmark text-xs"></i></button>
                                        </div>
                                    </template>
                                    <button type="button" @click="agregarValor(atributo)" class="rounded-lg border-2 border-dashed border-slate-300 text-slate-500 hover:text-blue-600 hover:border-blue-400 hover:bg-blue-50 px-3 py-1.5 text-xs font-bold transition-colors">
                                        <i class="fas fa-plus mr-1"></i> Añadir valor
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="atributos.length === 0" class="rounded-2xl border-2 border-dashed border-slate-200 p-12 text-center bg-slate-50/50" style="display:none;">
                        <i class="fas fa-sliders text-3xl text-slate-300 mb-4 block"></i>
                        <p class="font-bold text-slate-700">Aún no agregas ninguna variación</p>
                        <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">Agrega al menos un atributo (ej. Talla) con sus valores (ej. S, M, L).</p>
                    </div>
                </div>
            </div>

            <!-- PANEL DE RECONCILIACIÓN: aparece si ya había variantes cargadas y el catálogo cambió -->
            <div x-show="cambiosPendientes.length > 0" x-transition class="rounded-2xl border-2 border-amber-300 bg-amber-50 p-6 space-y-4" style="display:none;">
                <div class="flex items-center gap-2 text-amber-800">
                    <i class="fas fa-triangle-exclamation text-lg"></i>
                    <span class="font-black">Ya tienes variantes con datos cargados. Elige cómo aplicar cada cambio:</span>
                </div>
                <template x-for="(cambio, indice) in cambiosPendientes" :key="indice">
                    <div class="rounded-xl bg-white border border-amber-200 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="text-sm text-slate-700">
                            <b x-text="cambio.atributo"></b> — valor(es) nuevo(s):
                            <span class="font-mono text-xs bg-slate-100 rounded px-2 py-0.5 ml-1" x-text="cambio.nuevosValores.join(', ')"></span>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center">
                            <button type="button" x-show="cambio.nuevosValores.length === 1" @click="aplicarValorATodas(cambio)" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors">
                                Aplicar a tus <span x-text="resumenCambio(cambio).actuales"></span> variantes actuales
                            </button>
                            <button type="button" @click="expandirPorCambio(cambio)" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                                Expandir: tendrás <span x-text="resumenCambio(cambio).totalExpandido"></span> en total
                            </button>
                        </div>
                    </div>
                </template>
                <p class="text-[11px] text-amber-700">"Aplicar" agrega el valor como una etiqueta más a cada variante sin crear filas nuevas. "Expandir" multiplica tus variantes actuales por los valores nuevos, copiando precio y SKU como punto de partida para que solo ajustes lo necesario.</p>
            </div>

            <div x-show="cambiosPendientes.length === 0" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <p class="text-xs text-slate-500 max-w-md">
                    Esto generará <b class="text-slate-800" x-text="previewVariantes().cantidad"></b>
                    <span x-text="previewVariantes().cantidad === 1 ? 'variante' : 'variantes'"></span>
                    <span x-show="previewVariantes().detalle">(<span x-text="previewVariantes().detalle"></span>)</span>.
                    ¿Alguna no sigue este patrón? Podrás agregarla manualmente en el siguiente paso sin afectar a las demás.
                </p>
                <div class="flex justify-end gap-3 shrink-0">
                    <button type="button" @click="volver(1)" class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 transition-colors"><i class="fas fa-arrow-left mr-2"></i> Volver</button>
                    <button type="button" @click="revisarCombinaciones()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 text-sm font-black transition-all shadow-lg shadow-blue-600/20">
                        <span x-text="combinaciones.length ? 'Continuar con variantes' : 'Generar variantes'"></span> <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- PASO 3: Variantes -->
        <section x-show="paso === 3" x-transition.opacity class="space-y-5" style="display:none;">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-slate-900">Configuración final de variantes</h2>
                        <p class="text-sm text-slate-500 mt-1">
                            <span x-text="combinaciones.length"></span> variante(s). Haz clic en una fila para editarla o agregarle un atributo propio.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 p-2 rounded-xl">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 px-2">Precio único:</span>
                            <div class="relative w-28">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">$</span>
                                <input x-model="precioMasivo" type="number" min="0" step="0.01" placeholder="0.00" class="w-full rounded-lg border border-slate-300 bg-white pl-7 pr-2 py-1 text-xs font-bold text-slate-800 focus:border-blue-500 focus:outline-none">
                            </div>
                            <button type="button" @click="aplicarPrecioMasivo()" class="px-3 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">Aplicar</button>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 p-2 rounded-xl">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 px-2">Comisión única:</span>
                            <div class="relative w-28">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-emerald-500 font-bold text-xs">$</span>
                                <input x-model="comisionMasiva" type="number" min="0" step="0.01" placeholder="0.00" class="w-full rounded-lg border border-slate-300 bg-white pl-7 pr-2 py-1 text-xs font-bold text-slate-800 focus:border-blue-500 focus:outline-none">
                            </div>
                            <button type="button" @click="aplicarComisionMasiva()" class="px-3 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">Aplicar</button>
                        </div>
                        <button type="button" @click="toggleTodas()" class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-bold hover:bg-slate-50 transition-colors">
                            <i class="fas" :class="expandirTodo ? 'fa-compress' : 'fa-expand'"></i> <span x-text="expandirTodo ? 'Colapsar todo' : 'Expandir todo'"></span>
                        </button>
                        <button type="button" @click="agregarVarianteManual()" class="px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors">
                            <i class="fas fa-plus mr-1"></i> Variante manual
                        </button>
                    </div>
                </div>

                <div x-show="conteoConflictos() > 0" class="mx-6 sm:mx-8 mt-4 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs font-bold text-red-700" style="display:none;">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span><span x-text="conteoConflictos()"></span> variante(s) con SKU repetido o combinación de atributos duplicada. Están resaltadas abajo.</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-[10px] uppercase tracking-widest text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 font-bold" colspan="4">Variante</th>
                                <th class="px-4 py-4 font-bold w-32 text-right">Precio</th>
                                <th class="px-4 py-4 font-bold w-24 text-right">Acciones</th>
                            </tr>
                        </thead>

                        <template x-for="variante in combinaciones" :key="variante.id">
                            <tbody class="border-b border-slate-100" :class="{'bg-red-50/60': variante._dupSku || variante._dupCombinacion}">

                                <tr class="hover:bg-slate-50/60 cursor-pointer transition-colors" @click="toggleFila(variante)">
                                    <td class="px-6 py-3.5" colspan="4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="text-xs font-bold text-slate-700" x-text="resumenVariante(variante)"></span>
                                            <span x-show="variante._dupCombinacion" class="text-[10px] font-black text-red-600"><i class="fas fa-triangle-exclamation"></i> combinación repetida</span>
                                            <span x-show="variante._dupSku" class="text-[10px] font-black text-red-600"><i class="fas fa-triangle-exclamation"></i> SKU repetido</span>
                                            <span x-show="!variante.sku" class="text-[10px] text-slate-400">Sin SKU</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-bold text-slate-800">
                                        <span x-text="variante.precio_venta !== '' ? '$' + Number(variante.precio_venta).toFixed(2) : 'Sin precio'"></span>
                                    </td>
                                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                        <button type="button" @click.stop="toggleFila(variante)" class="w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50" :title="variante.expandido ? 'Colapsar' : 'Editar'">
                                            <i class="fas" :class="variante.expandido ? 'fa-chevron-up' : 'fa-pen'"></i>
                                        </button>
                                        <button type="button" @click.stop="quitarVariante(combinaciones.indexOf(variante))" class="w-8 h-8 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600" title="Quitar variante">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <tr x-show="variante.expandido" x-transition>
                                    <td colspan="6" class="px-6 sm:px-8 py-5 bg-slate-50/70 border-t border-slate-100">
                                        <div class="grid gap-5 lg:grid-cols-12">

                                            <div class="lg:col-span-12">
                                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-500 mb-2">Atributos de esta variante</span>
                                                <div class="flex flex-wrap gap-2 items-center">
                                                    <template x-for="(valor, vi) in variante.valores" :key="vi">
                                                        <div class="flex items-center gap-1 rounded-lg bg-white border border-slate-300 pl-2 pr-1 py-1">
                                                            <template x-if="!valor._nuevoAtributo">
                                                                <select x-model="valor.atributo" @change="valor.atributo === '__nuevo__' ? (valor._nuevoAtributo = true, valor.atributo = '') : recalcularDuplicados()" class="text-xs font-bold text-blue-700 bg-transparent border-0 focus:ring-0 focus:outline-none">
                                                                    <option value="">Atributo…</option>
                                                                    <template x-for="op in opcionesAtributo()" :key="op"><option :value="op" x-text="op"></option></template>
                                                                    <option value="__nuevo__">+ Nuevo atributo…</option>
                                                                </select>
                                                            </template>
                                                            <template x-if="valor._nuevoAtributo">
                                                                <input type="text" placeholder="Nombre atributo" autofocus
                                                                    @keydown.enter="registrarAtributoNuevo($event.target.value); valor.atributo = $event.target.value.trim(); valor._nuevoAtributo = false; recalcularDuplicados()"
                                                                    @blur="registrarAtributoNuevo($event.target.value); valor.atributo = $event.target.value.trim(); valor._nuevoAtributo = false; recalcularDuplicados()"
                                                                    class="text-xs w-24 border-b border-blue-400 focus:outline-none bg-transparent">
                                                            </template>

                                                            <span class="text-slate-300">:</span>

                                                            <template x-if="!valor._nuevoValor">
                                                                <select x-model="valor.valor" @change="valor.valor === '__nuevo__' ? (valor._nuevoValor = true, valor.valor = '') : recalcularDuplicados()" class="text-xs text-slate-700 bg-transparent border-0 focus:ring-0 focus:outline-none">
                                                                    <option value="">Valor…</option>
                                                                    <template x-for="op in opcionesValor(valor.atributo)" :key="op"><option :value="op" x-text="op"></option></template>
                                                                    <option value="__nuevo__">+ Nuevo valor…</option>
                                                                </select>
                                                            </template>
                                                            <template x-if="valor._nuevoValor">
                                                                <input type="text" placeholder="Valor" autofocus
                                                                    @keydown.enter="registrarValorNuevo(valor.atributo, $event.target.value); valor.valor = $event.target.value.trim(); valor._nuevoValor = false; recalcularDuplicados()"
                                                                    @blur="registrarValorNuevo(valor.atributo, $event.target.value); valor.valor = $event.target.value.trim(); valor._nuevoValor = false; recalcularDuplicados()"
                                                                    class="text-xs w-20 border-b border-blue-400 focus:outline-none bg-transparent">
                                                            </template>

                                                            <button type="button" @click="quitarValorVariante(variante, vi)" class="text-slate-400 hover:text-red-500 px-1"><i class="fas fa-xmark text-[10px]"></i></button>
                                                        </div>
                                                    </template>
                                                    <button type="button" @click="agregarValorVariante(variante)" class="rounded-lg border border-dashed border-blue-300 px-2 py-1.5 text-[10px] font-bold text-blue-600 hover:bg-blue-50">
                                                        <i class="fas fa-plus mr-1"></i> Atributo propio de esta variante
                                                    </button>
                                                </div>
                                            </div>

                                            <label class="block lg:col-span-3">
                                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1">SKU <span class="font-normal normal-case text-slate-400">(opcional)</span></span>
                                                <input x-model="variante.sku" readonly type="text" placeholder="Se genera al guardar" class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-xs font-mono text-slate-500 cursor-not-allowed">
                                            </label>

                                            <label class="block lg:col-span-3">
                                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1">Precio venta <b class="text-red-500">*</b></span>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">$</span>
                                                    <input x-model="variante.precio_venta" type="number" min="0" step="0.01" placeholder="0.00" class="w-full rounded-lg border border-slate-200 bg-white pl-7 pr-2 py-2 text-sm font-bold text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                                                </div>
                                            </label>

                                            <label class="block lg:col-span-3">
                                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1">Comisión <b class="text-red-500">*</b></span>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-emerald-500 font-bold text-xs">$</span>
                                                    <input x-model="variante.comision" type="number" min="0" step="0.01" class="w-full rounded-lg border border-emerald-200 bg-emerald-50 pl-7 pr-2 py-2 text-sm font-bold text-emerald-800 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all">
                                                </div>
                                            </label>

                                            <div class="lg:col-span-3">
                                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1">Imágenes</span>
                                                <label class="inline-flex items-center gap-2 rounded-lg border border-dashed border-blue-300 bg-blue-50/50 px-3 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100 cursor-pointer transition-colors">
                                                    <i class="fas fa-images"></i> Agregar
                                                    <input type="file" accept="image/jpeg,image/png,image/gif,image/webp" multiple class="hidden" @change="seleccionarImagenes($event, variante)">
                                                </label>
                                                <div x-show="variante.imagenes.length" class="flex flex-wrap gap-1.5 mt-2" style="display:none;">
                                                    <template x-for="(imagen, imagenIndice) in variante.imagenes" :key="imagenIndice">
                                                        <div class="relative group w-10 h-10 rounded-lg overflow-hidden border border-slate-200 bg-slate-100">
                                                            <img :src="imagen.existing ? imagen.url : URL.createObjectURL(imagen)" class="w-full h-full object-cover" :alt="imagen.name">
                                                            <button type="button" @click="quitarImagen(variante, imagenIndice)" class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full bg-red-600 text-white text-[9px] opacity-0 group-hover:opacity-100 transition-opacity"><i class="fas fa-xmark"></i></button>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </template>
                    </table>
                </div>
            </div>

            <div class="flex justify-between gap-3">
                <button type="button" @click="volver(2)" class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 transition-colors"><i class="fas fa-arrow-left mr-2"></i> Editar atributos</button>
                <button type="button" @click="guardarProducto()" :disabled="!formularioValido() || guardando" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed text-white px-6 py-3 text-sm font-black transition-all shadow-lg shadow-emerald-600/20 disabled:shadow-none">
                    <i class="fas fa-check" x-show="!guardando"></i>
                    <i class="fas fa-circle-notch fa-spin" x-show="guardando" style="display:none;"></i>
                    <span x-text="guardando ? 'Guardando catálogo...' : 'Confirmar y Guardar'"></span>
                </button>
            </div>
        </section>

        <!-- PASO 4: Éxito -->
        <section x-show="paso === 4" x-transition class="bg-white border border-emerald-200 rounded-2xl shadow-sm p-12 text-center" style="display:none;">
            <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-inner border border-emerald-200 mb-6"><i class="fas fa-check text-3xl"></i></div>
            <h2 class="text-3xl font-black text-slate-900">¡Catálogo actualizado!</h2>
            <p class="text-slate-500 mt-3 max-w-lg mx-auto">El producto ya está listo en la base de datos. Actualmente su stock es 0, listo para recibir su primera orden de compra.</p>
            <a href="{{ route('productos.index') }}" class="inline-flex mt-8 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 text-sm font-black shadow-lg shadow-blue-600/20 transition-all hover:scale-[1.02]"><i class="fas fa-boxes-stacked mr-2"></i> Ver inventario</a>
        </section>
    </div>
</x-app>