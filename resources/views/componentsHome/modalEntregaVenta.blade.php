{{-- MODAL "REGISTRAR ENTREGA DE PRODUCTO" (REVISAR BOLSA Y PAGAR) --}}
<div x-data="modalEntregaVenta()">
    <template x-teleport="body">
        <div 
            x-show="abierto" 
            x-cloak
            class="fixed inset-0 z-[70] overflow-y-auto"
            style="display: none;"
            role="dialog"
            aria-modal="true"
            @keydown.escape.window="cerrar()"
        >
    {{-- Backdrop oscuro translúcido --}}
    <div 
        x-show="abierto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
        @click="cerrar()"
    ></div>

    <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
        <div 
            x-show="abierto"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            @click.away="cerrar()"
            class="relative z-10 w-full max-w-2xl sm:max-w-3xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-slate-200 flex flex-col my-6"
        >
            {{-- CABECERA AZUL ROYAL --}}
            <div class="bg-blue-600 px-5 sm:px-6 py-3.5 sm:py-4 flex items-center justify-between text-white shadow-md">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-white text-blue-600 flex items-center justify-center text-xs font-black shadow-xs shrink-0">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-white tracking-wide">
                            Registrar Entrega de Producto
                        </h3>
                        <template x-if="nombreVendedorAsignado">
                            <p class="text-[11px] text-blue-100 flex items-center gap-1 font-medium">
                                <i class="fas fa-user-tag text-xs"></i>
                                Venta para vendedor: <strong class="text-white underline" x-text="nombreVendedorAsignado"></strong>
                            </p>
                        </template>
                    </div>
                </div>
                <button 
                    type="button" 
                    @click="cerrar()" 
                    class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer"
                    title="Cerrar ventana"
                >
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- CUERPO: DOS COLUMNAS (INFORMACIÓN DEL PRODUCTO Y DATOS DE LA ENTREGA) --}}
            <div id="modal-entrega-body" class="grid grid-cols-1 md:grid-cols-12 gap-6 p-5 sm:p-7 max-h-[calc(90vh-140px)] overflow-y-auto custom-scrollbar">
                
                {{-- BANNER DE ERROR GENERAL INLINE (Sin popups ni alertas saltarinas) --}}
                <template x-if="errorGeneral">
                    <div class="col-span-12 p-3.5 bg-rose-50 border border-rose-300 rounded-xl text-xs text-rose-800 flex items-start justify-between gap-2 shadow-xs transition-all">
                        <div class="flex items-start gap-2.5">
                            <i class="fas fa-circle-exclamation text-rose-600 mt-0.5 text-base shrink-0"></i>
                            <div>
                                <p class="font-bold text-rose-950 leading-snug">Atención</p>
                                <p class="text-[11px] text-rose-800 mt-0.5" x-text="errorGeneral"></p>
                            </div>
                        </div>
                        <button type="button" @click="errorGeneral = ''" class="text-rose-400 hover:text-rose-700 p-1 cursor-pointer">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </template>

                {{-- COLUMNA IZQUIERDA: INFORMACIÓN DEL PRODUCTO --}}
                <div class="md:col-span-5 flex flex-col border-b md:border-b-0 md:border-r border-slate-100 pb-5 md:pb-0 md:pr-6">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">
                        INFORMACIÓN DEL PRODUCTO
                    </h4>

                    {{-- Selector de producto si hay múltiples ítems en la bolsa --}}
                    <template x-if="items.length > 1">
                        <div class="mb-3">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">
                                Selecciona ítem (<span x-text="(indiceActivo + 1) + '/' + items.length"></span>):
                            </span>
                            <div class="flex items-center gap-1.5 overflow-x-auto pb-1.5 custom-scrollbar">
                                <template x-for="(item, idx) in items" :key="item.id">
                                    <button 
                                        type="button" 
                                        @click="indiceActivo = idx"
                                        :class="indiceActivo === idx ? 'ring-2 ring-blue-600 border-blue-600 scale-105' : 'border-slate-200 opacity-60 hover:opacity-100'"
                                        class="w-11 h-11 rounded-lg border bg-white p-0.5 shrink-0 transition-all cursor-pointer relative"
                                        :title="item.producto"
                                    >
                                        <img :src="item.imagen || '/assets/images/placeholder.png'" class="w-full h-full object-cover rounded">
                                        <span class="absolute -top-1 -right-1 bg-black text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center" x-text="item.cantidad"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Marco de la imagen del producto --}}
                    <div class="w-full aspect-square max-h-56 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-center p-3 overflow-hidden shadow-2xs mb-4">
                        <template x-if="productoActual && productoActual.imagen">
                            <img :src="productoActual.imagen" :alt="productoActual.producto" class="max-h-full max-w-full object-contain">
                        </template>
                        <template x-if="!productoActual || !productoActual.imagen">
                            <div class="text-slate-300 flex flex-col items-center justify-center text-sm font-bold">
                                <i class="fas fa-image text-3xl mb-1 text-slate-200"></i>
                                <span>Producto</span>
                            </div>
                        </template>
                    </div>

                    {{-- Ficha técnica del producto --}}
                    <div class="space-y-2 text-xs text-slate-700">
                        <p class="leading-snug">
                            <strong class="text-slate-900 font-bold">Producto:</strong> 
                            <span class="font-normal text-slate-700" x-text="productoActual ? (productoActual.producto + (productoActual.variante ? ' / ' + productoActual.variante : '')) : 'Sin producto'"></span>
                        </p>
                        <p class="flex items-center gap-2">
                            <strong class="text-slate-900 font-bold">SKU:</strong> 
                            <span class="inline-block bg-black text-white text-[10px] font-mono px-2 py-0.5 rounded-full font-bold" x-text="productoActual?.sku || 'N/A'"></span>
                        </p>
                        <p class="flex items-center gap-2">
                            <strong class="text-slate-900 font-bold">Precio:</strong> 
                            <span class="text-blue-600 font-extrabold text-sm" x-text="moneda(productoActual?.precio_venta || productoActual?.precio)"></span>
                        </p>
                        <template x-if="Number(productoActual?.costo_extra || 0) > 0">
                            <p class="flex items-center gap-2">
                                <strong class="text-amber-800 font-bold">Costo Extra:</strong> 
                                <span class="inline-block bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded" x-text="'+' + moneda(productoActual?.costo_extra)"></span>
                            </p>
                        </template>
                        <p class="flex items-center gap-2">
                            <strong class="text-slate-900 font-bold">Disponible:</strong> 
                            <span class="inline-block bg-emerald-700 text-white text-[10px] font-bold px-2 py-0.5 rounded" x-text="(productoActual?.stock || 0) + ' unidades'"></span>
                        </p>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: DATOS DE LA ENTREGA Y RESUMEN FINANCIERO --}}
                <div class="md:col-span-7 flex flex-col space-y-3.5">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">
                        DATOS DE LA ENTREGA
                    </h4>

                    {{-- Fila 1: Cantidad a Despachar y Costo Extra --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Cantidad a Despachar --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Cantidad a Despachar <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                min="1" 
                                :max="productoActual?.stock || 9999" 
                                x-model.number="productoActual.cantidad" 
                                @input="onCantidadInput($event)"
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-xs font-semibold text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all"
                            >
                            <span class="text-[11px] text-slate-400 block mt-0.5">Unidades para despacho</span>
                        </div>

                        {{-- Costo Extra ($) --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>Costo Extra ($)</span>
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.2 rounded" x-show="Number(productoActual?.costo_extra || 0) > 0" x-text="'+' + moneda(productoActual?.costo_extra)"></span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">$</span>
                                <input 
                                    type="number" 
                                    min="0" 
                                    step="0.5" 
                                    x-model.number="productoActual.costo_extra" 
                                    @input="onCostoExtraInput($event)"
                                    placeholder="0.00" 
                                    class="w-full rounded-lg border border-slate-300 pl-7 pr-3 py-2 text-xs font-semibold text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all"
                                >
                            </div>
                            <span class="text-[11px] text-slate-400 block mt-0.5">Costo extra por producto/envío</span>
                        </div>
                    </div>

                    {{-- Fila 2: Fecha y Hora de Creación (No editables) --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>Fecha de Creación</span>
                                <span class="text-[10px] text-slate-400 font-normal"><i class="fas fa-lock text-[9px] mr-0.5"></i> Automático</span>
                            </label>
                            <input 
                                type="date" 
                                x-model="fechaSalida" 
                                readonly
                                tabindex="-1"
                                class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 cursor-not-allowed select-none focus:outline-none shadow-2xs"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>Hora de Creación</span>
                                <span class="text-[10px] text-slate-400 font-normal"><i class="fas fa-lock text-[9px] mr-0.5"></i> Automático</span>
                            </label>
                            <input 
                                type="time" 
                                x-model="horaSalida" 
                                readonly
                                tabindex="-1"
                                class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 cursor-not-allowed select-none focus:outline-none shadow-2xs"
                            >
                        </div>
                    </div>

                    {{-- Fila 3: Nombre del Cliente (Obligatorio) --}}
                    <div id="seccion-cliente-entrega">
                        <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                            <span>Nombre del Cliente <span class="text-red-500">*</span></span>
                            <span x-show="errorCliente" class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200">Requerido</span>
                        </label>
                        <input 
                            id="input-cliente-entrega"
                            type="text" 
                            x-model="nombreCliente" 
                            @input="errorCliente = false"
                            placeholder="Ej. Juan Carlos Pérez" 
                            :class="errorCliente ? 'border-rose-500 bg-rose-50/40 text-rose-800 focus:border-rose-500 focus:ring-rose-500/20' : 'border-slate-300 text-slate-800 focus:border-blue-500 focus:ring-blue-500/20'"
                            class="w-full rounded-lg border px-3.5 py-2 text-xs font-semibold focus:outline-none focus:ring-2 transition-all"
                        >
                        <span x-show="!errorCliente" class="text-[11px] text-slate-400 block mt-0.5">Nombre completo del destinatario o comprador</span>
                        <span x-show="errorCliente" class="text-[11px] font-bold text-rose-600 flex items-center gap-1 mt-0.5">
                            <i class="fas fa-triangle-exclamation text-[10px]"></i> Por favor ingresa el nombre del cliente.
                        </span>
                    </div>

                    {{-- Fila 4: Departamento y Municipio de El Salvador (Obligatorios) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div id="seccion-departamento-entrega">
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>Departamento <span class="text-red-500">*</span></span>
                                <span x-show="errorDepartamento" class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200">Requerido</span>
                            </label>
                            <select 
                                id="select-departamento-entrega"
                                x-model="departamento" 
                                @change="onDepartamentoChange()"
                                :class="errorDepartamento ? 'border-rose-500 bg-rose-50/40 text-rose-800 focus:border-rose-500 focus:ring-rose-500/20' : 'border-slate-300 text-slate-800 focus:border-blue-500 focus:ring-blue-500/20'"
                                class="w-full rounded-lg border px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-2 transition-all bg-white"
                            >
                                <option value="">Selecciona departamento</option>
                                <template x-for="dep in listaDepartamentos" :key="dep">
                                    <option :value="dep" x-text="dep"></option>
                                </template>
                            </select>
                            <span x-show="!errorDepartamento" class="text-[11px] text-slate-400 block mt-0.5">Departamento destino</span>
                            <span x-show="errorDepartamento" class="text-[11px] font-bold text-rose-600 flex items-center gap-1 mt-0.5">
                                <i class="fas fa-triangle-exclamation text-[10px]"></i> Selecciona departamento.
                            </span>
                        </div>

                        <div id="seccion-municipio-entrega">
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>Municipio <span class="text-red-500">*</span></span>
                                <span x-show="errorMunicipio" class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200">Requerido</span>
                            </label>
                            <select 
                                id="select-municipio-entrega"
                                x-model="municipio" 
                                @change="errorMunicipio = false"
                                :disabled="!departamento"
                                :class="errorMunicipio ? 'border-rose-500 bg-rose-50/40 text-rose-800 focus:border-rose-500 focus:ring-rose-500/20' : 'border-slate-300 text-slate-800 focus:border-blue-500 focus:ring-blue-500/20 disabled:bg-slate-100 disabled:text-slate-400'"
                                class="w-full rounded-lg border px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-2 transition-all bg-white"
                            >
                                <option value="" x-text="departamento ? 'Selecciona municipio' : 'Primero elige departamento'"></option>
                                <template x-for="mun in municipiosDisponibles" :key="mun">
                                    <option :value="mun" x-text="mun"></option>
                                </template>
                            </select>
                            <span x-show="!errorMunicipio" class="text-[11px] text-slate-400 block mt-0.5">Municipio según departamento</span>
                            <span x-show="errorMunicipio" class="text-[11px] font-bold text-rose-600 flex items-center gap-1 mt-0.5">
                                <i class="fas fa-triangle-exclamation text-[10px]"></i> Selecciona municipio.
                            </span>
                        </div>
                    </div>

                    {{-- Fila 5: Dirección de Entrega (Sin icono de ubicación) --}}
                    <div id="seccion-direccion-entrega">
                        <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                            <span>Dirección Específica <span class="text-red-500">*</span></span>
                            <span x-show="errorDireccion" class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200">Requerido</span>
                        </label>
                        <input 
                            id="input-direccion-entrega"
                            type="text" 
                            x-model="direccionEntrega" 
                            @input="errorDireccion = false"
                            placeholder="Ej. Calle Principal #123, Colonia San Benito" 
                            :class="errorDireccion ? 'border-rose-500 bg-rose-50/40 text-rose-800 focus:border-rose-500 focus:ring-rose-500/20' : 'border-slate-300 text-slate-800 focus:border-blue-500 focus:ring-blue-500/20'"
                            class="w-full rounded-lg border px-3.5 py-2 text-xs font-semibold focus:outline-none focus:ring-2 transition-all"
                        >
                        <span x-show="!errorDireccion" class="text-[11px] text-slate-400 block mt-0.5">Colonia, calle, pasaje o número de casa</span>
                        <span x-show="errorDireccion" class="text-[11px] font-bold text-rose-600 flex items-center gap-1 mt-0.5">
                            <i class="fas fa-triangle-exclamation text-[10px]"></i> Por favor ingresa la dirección de entrega.
                        </span>
                    </div>

                    {{-- Fila 4: Punto de Referencia y Número de Teléfono con validaciones del sistema --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Punto de Referencia
                            </label>
                            <input 
                                type="text" 
                                x-model="puntoReferencia" 
                                placeholder="Ej. Casa frente al parque" 
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-xs text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            >
                            <span class="text-[11px] text-slate-400 block mt-0.5">Casa, color o referencia</span>
                        </div>

                        <div id="seccion-telefono-entrega">
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>Número de Teléfono <span class="text-red-500">*</span></span>
                                <span x-show="errorTelefonoTocado && (!telefono || !esTelefonoValido)" class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200">Requerido</span>
                            </label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5 text-slate-400 text-xs font-bold select-none pointer-events-none">
                                    <img src="https://flagcdn.com/w20/sv.png" alt="SV" class="h-3.5 rounded-xs opacity-90 shadow-2xs">
                                    <span class="text-slate-500 font-mono">+503</span>
                                </div>
                                <input 
                                    id="input-telefono-entrega"
                                    type="text" 
                                    inputmode="numeric"
                                    maxlength="9"
                                    x-model="telefono" 
                                    @input="errorTelefonoTocado = false; onTelefonoInput($event)"
                                    placeholder="XXXX-XXXX" 
                                    :class="{
                                        'border-emerald-500 bg-emerald-50/40 text-emerald-800 focus:ring-emerald-500/20 focus:border-emerald-500': telefono && esTelefonoValido,
                                        'border-rose-500 bg-rose-50/40 text-rose-800 focus:ring-rose-500/20 focus:border-rose-500': (errorTelefonoTocado || (telefono && !esTelefonoValido)),
                                        'border-slate-300 text-slate-800 focus:border-blue-500 focus:ring-blue-500/20': !telefono && !errorTelefonoTocado
                                    }"
                                    class="w-full rounded-lg border py-2 pr-9 text-xs font-semibold focus:outline-none focus:ring-2 transition-all"
                                    style="padding-left: 4.8rem;"
                                >
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center pointer-events-none">
                                    <template x-if="telefono && esTelefonoValido">
                                        <i class="fas fa-check text-emerald-500 text-xs"></i>
                                    </template>
                                    <template x-if="errorTelefonoTocado || (telefono && !esTelefonoValido)">
                                        <i class="fas fa-triangle-exclamation text-rose-500 text-xs"></i>
                                    </template>
                                </div>
                            </div>
                            <div class="mt-0.5">
                                <template x-if="!telefono && !errorTelefonoTocado">
                                    <span class="text-[11px] text-slate-400 block">Formato: XXXX-XXXX (ej: 7890-1234)</span>
                                </template>
                                <template x-if="telefono && esTelefonoValido">
                                    <span class="text-[11px] text-emerald-600 font-semibold block">Número válido de El Salvador ✓</span>
                                </template>
                                <template x-if="errorTelefonoTocado || (telefono && !esTelefonoValido)">
                                    <span class="text-[11px] font-bold text-rose-600 flex items-center gap-1" x-text="errorTelefono || 'Ingresa un número de teléfono válido de El Salvador.'"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Fila 5: Costo de Envío y Descuento --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Costo de Envío --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>Costo de Envío ($)</span>
                                <span class="text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.2 rounded" x-show="Number(costoEnvio || 0) > 0" x-text="'+' + moneda(costoEnvio)"></span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">$</span>
                                <input 
                                    type="number" 
                                    min="0" 
                                    step="0.5" 
                                    x-model.number="costoEnvio" 
                                    placeholder="0.00" 
                                    class="w-full rounded-lg border border-slate-300 pl-7 pr-3 py-2 text-xs font-semibold text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all"
                                >
                            </div>
                            <span class="text-[11px] text-slate-400 block mt-0.5">Tarifa de entrega / flete</span>
                        </div>

                        {{-- Descuento --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Descuento ($)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">$</span>
                                <input 
                                    type="number" 
                                    min="0" 
                                    step="0.5" 
                                    x-model.number="descuento" 
                                    placeholder="0.00" 
                                    class="w-full rounded-lg border border-slate-300 pl-7 pr-3 py-2 text-xs font-semibold text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                >
                            </div>
                            <span class="text-[11px] text-slate-400 block mt-0.5">Monto a descontar (opcional)</span>
                        </div>
                    </div>

                    {{-- Fila 6: Método de Pago --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Método de Pago <span class="text-red-500">*</span>
                        </label>
                        <select 
                            x-model="metodoPago" 
                            class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-xs font-bold text-slate-800 bg-white focus:border-blue-500 focus:outline-none cursor-pointer"
                        >
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                        </select>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Modalidad de pago</span>
                    </div>

                    {{-- Comprobante de Transferencia Bancaria (Condicional) --}}
                    <div 
                        id="seccion-comprobante-entrega"
                        x-show="metodoPago === 'Transferencia Bancaria'" 
                        x-transition
                        class="p-3.5 rounded-xl border transition-all space-y-2.5"
                        :class="errorComprobante ? 'border-rose-400 bg-rose-50/80 ring-2 ring-rose-500/20' : 'border-blue-200 bg-blue-50/50'"
                    >
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold flex items-center gap-1.5" :class="errorComprobante ? 'text-rose-950 font-black' : 'text-blue-950'">
                                <i class="fas fa-file-invoice-dollar" :class="errorComprobante ? 'text-rose-600' : 'text-blue-600'"></i>
                                <span>Comprobante de Transferencia</span>
                                <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full transition-all"
                                  :class="errorComprobante ? 'text-rose-700 bg-rose-100 border border-rose-300 animate-pulse' : 'text-blue-700 bg-blue-100 border border-blue-200'">
                                <span x-text="errorComprobante ? '¡Falta comprobante!' : 'Imagen requerida'"></span>
                            </span>
                        </div>

                        {{-- ALERTA INLINE VISIBLE CUANDO NO SE HA ADJUNTADO EL COMPROBANTE --}}
                        <template x-if="errorComprobante">
                            <div class="p-3 bg-rose-100/90 border border-rose-300 rounded-xl text-rose-800 text-xs font-semibold flex items-start gap-2.5 shadow-2xs">
                                <i class="fas fa-circle-exclamation text-rose-600 mt-0.5 text-base shrink-0"></i>
                                <div class="flex-1">
                                    <p class="font-bold text-rose-900 leading-snug">¡No has adjuntado el comprobante de pago!</p>
                                    <p class="text-[11px] text-rose-700 mt-0.5 leading-relaxed" x-text="errorComprobanteMensaje || 'Debes subir la foto o captura de pantalla de la transferencia para poder registrar y autorizar la entrega.'"></p>
                                </div>
                            </div>
                        </template>

                        {{-- Área de carga o preview --}}
                        <template x-if="!comprobantePreview">
                            <div>
                                <div 
                                    @click="$refs.modalComprobanteInput.click()" 
                                    class="border-2 border-dashed rounded-xl p-3 text-center bg-white cursor-pointer transition-all shadow-2xs group"
                                    :class="errorComprobante ? 'border-rose-400 hover:border-rose-600 hover:bg-rose-50/50 ring-1 ring-rose-300' : 'border-blue-300 hover:border-blue-500 hover:bg-blue-50/40'"
                                >
                                    <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center text-sm mb-1 group-hover:scale-110 transition-transform"
                                         :class="errorComprobante ? 'bg-rose-100 text-rose-600' : 'bg-blue-50 text-blue-600'">
                                        <i class="fas" :class="errorComprobante ? 'fa-triangle-exclamation text-rose-600 text-sm' : 'fa-cloud-arrow-up'"></i>
                                    </div>
                                    <p class="text-xs font-bold" :class="errorComprobante ? 'text-rose-800' : 'text-slate-800'">
                                        Haz clic para subir comprobante
                                    </p>
                                    <p class="text-[10px]" :class="errorComprobante ? 'text-rose-500 font-medium' : 'text-slate-400'">
                                        JPG, PNG o WEBP (Máx. 5MB)
                                    </p>
                                </div>
                                <template x-if="errorComprobante">
                                    <span class="text-[11px] font-bold text-rose-600 flex items-center gap-1 mt-1.5 pl-1">
                                        <i class="fas fa-arrow-up text-[10px]"></i> Haz clic arriba para seleccionar el archivo de la transferencia.
                                    </span>
                                </template>
                            </div>
                        </template>

                        <template x-if="comprobantePreview">
                            <div class="relative flex items-center gap-3 p-2.5 bg-white border border-emerald-300 rounded-xl shadow-xs">
                                <img :src="comprobantePreview" alt="Preview Comprobante" class="w-12 h-12 object-cover rounded-lg border border-slate-200 shrink-0">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-900 truncate" x-text="comprobanteNombre"></p>
                                    <p class="text-[10px] text-slate-400 font-mono" x-text="comprobanteTamano"></p>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600">
                                        <i class="fas fa-circle-check text-[9px]"></i> Comprobante adjunto
                                    </span>
                                </div>
                                <button 
                                    type="button" 
                                    @click="removerComprobante()" 
                                    class="p-1.5 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer"
                                    title="Eliminar o cambiar imagen"
                                >
                                    <i class="fas fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </template>

                        <input 
                            type="file" 
                            x-ref="modalComprobanteInput" 
                            @change="onComprobanteSeleccionado($event)" 
                            accept="image/png,image/jpeg,image/jpg,image/webp" 
                            class="hidden"
                        >
                    </div>

                    {{-- RESUMEN FINANCIERO (CAJA CYAN / AQUA COMO EN LA IMAGEN) --}}
                    <div class="bg-[#e0f7fa] border border-[#b2ebf2] rounded-xl p-3.5 space-y-1.5 text-xs">
                        <div class="flex justify-between items-center text-slate-700 font-bold">
                            <span>Subtotal:</span>
                            <span class="text-slate-900" x-text="moneda(subtotalBaseProductos)"></span>
                        </div>
                        <template x-if="totalCostoExtra > 0">
                            <div class="flex justify-between items-center text-amber-800 font-bold">
                                <span class="flex items-center gap-1">
                                    <span>Costo Extra:</span>
                                    <span class="text-[10px] font-normal text-amber-600 bg-amber-100/60 px-1 rounded">(Especial)</span>
                                </span>
                                <span class="text-amber-700" x-text="'+' + moneda(totalCostoExtra)"></span>
                            </div>
                        </template>
                        <template x-if="costoEnvioCalculado > 0">
                            <div class="flex justify-between items-center text-blue-800 font-bold">
                                <span class="flex items-center gap-1">
                                    <span>Costo de Envío:</span>
                                    <span class="text-[10px] font-normal text-blue-600 bg-blue-100/60 px-1 rounded">(Delivery)</span>
                                </span>
                                <span class="text-blue-700" x-text="'+' + moneda(costoEnvioCalculado)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between items-center text-slate-700 font-bold">
                            <span>Descuento:</span>
                            <span class="text-red-500" x-text="'-' + moneda(descuentoCalculado)"></span>
                        </div>
                        <div class="flex justify-between items-baseline pt-1.5 border-t border-cyan-200/60">
                            <span class="text-xs uppercase font-black text-slate-800">TOTAL:</span>
                            <span class="text-lg font-black text-blue-600" x-text="moneda(totalCalculado)"></span>
                        </div>
                    </div>

                </div>

            </div>

            {{-- BOTONES DE ACCIÓN (PIE DEL MODAL) --}}
            <div class="p-4 sm:p-5 bg-white border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl">
                <button 
                    type="button" 
                    @click="cerrar()" 
                    class="px-6 sm:px-7 py-2.5 rounded-lg bg-black hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer"
                >
                    CANCELAR
                </button>
                <button 
                    type="button" 
                    @click="procesarEntrega()" 
                    :disabled="procesando || items.length === 0" 
                    class="px-6 sm:px-7 py-2.5 rounded-lg bg-black hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition-colors cursor-pointer disabled:opacity-50"
                >
                    <i class="fas" :class="procesando ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                    <span x-text="procesando ? 'REGISTRANDO...' : 'REGISTRAR ENTREGA'"></span>
                </button>
            </div>

        </div>
    </div>
    </template>
</div>

{{-- CONTROLADOR REACTIVO ALPINE.JS PARA EL MODAL DE ENTREGA --}}
<script>
    function modalEntregaVenta() {
        return {
            abierto: false,
            procesando: false,
            items: [],
            indiceActivo: 0,
            idVendedorAsignado: null,
            nombreVendedorAsignado: '',

            nombreCliente: '',
            departamento: '',
            municipio: '',

            listaDepartamentos: [
                'Ahuachapán',
                'Cabañas',
                'Chalatenango',
                'Cuscatlán',
                'La Libertad',
                'La Paz',
                'La Unión',
                'Morazán',
                'San Miguel',
                'San Salvador',
                'San Vicente',
                'Santa Ana',
                'Sonsonate',
                'Usulután'
            ],

            municipiosPorDepartamento: {
                'Ahuachapán': ['Ahuachapán', 'Apaneca', 'Atiquizaya', 'Concepción de Ataco', 'El Refugio', 'Guaymango', 'Jujutla', 'San Francisco Menéndez', 'San Lorenzo', 'San Pedro Puxtla', 'Tacuba', 'Turín'],
                'Cabañas': ['Sensuntepeque', 'Cinquera', 'Dolores', 'Guacotecti', 'Ilobasco', 'Jutiapa', 'San Isidro', 'Tejutepeque', 'Victoria'],
                'Chalatenango': ['Chalatenango', 'Agua Caliente', 'Arcatao', 'Azacualpa', 'Cancasque', 'Citalá', 'Comalapa', 'Concepción Quezaltepeque', 'Dulce Nombre de María', 'El Carrizal', 'El Paraíso', 'La Laguna', 'La Palma', 'La Reina', 'Las Vueltas', 'Nombre de Jesús', 'Nueva Concepción', 'Nueva Trinidad', 'Ojos de Agua', 'Potonico', 'San Antonio de la Cruz', 'San Antonio Los Ranchos', 'San Fernando', 'San Francisco Lempa', 'San Francisco Morazán', 'San Ignacio', 'San Isidro Labrador', 'San José Cancasque', 'San José Las Flores', 'San Luis del Carmen', 'San Miguel de Mercedes', 'San Rafael', 'Santa Rita', 'Tejutla'],
                'Cuscatlán': ['Cojutepeque', 'Candelaria', 'El Carmen', 'El Rosario', 'Monte San Juan', 'Oratorio de Concepción', 'San Bartolomé Perulapía', 'San Cristóbal', 'San José Guayabal', 'San Pedro Perulapán', 'San Rafael Cedros', 'San Ramón', 'Santa Cruz Analquito', 'Santa Cruz Michapa', 'Suchitoto', 'Tenancingo'],
                'La Libertad': ['Santa Tecla', 'Antiguo Cuscatlán', 'Chiltiupán', 'Ciudad Arce', 'Colón', 'Comasagua', 'Huizúcar', 'Jayaque', 'Jicalapa', 'La Libertad', 'Nuevo Cuscatlán', 'Quezaltepeque', 'Sacacoyo', 'San José Villanueva', 'San Juan Opico', 'San Matías', 'San Pablo Tacachico', 'Talnique', 'Tamanique', 'Teotepeque', 'Tepecoyo', 'Zaragoza'],
                'La Paz': ['Zacatecoluca', 'Cuyultitán', 'El Rosario', 'Jerusalén', 'Mercedes La Ceiba', 'Olocuilta', 'Paraíso de Osorio', 'San Antonio Masahuat', 'San Emigdio', 'San Francisco Chinameca', 'San Juan Nonualco', 'San Juan Talpa', 'San Juan Tepezontes', 'San Luis La Herradura', 'San Luis Talpa', 'San Miguel Tepezontes', 'San Pedro Masahuat', 'San Pedro Nonualco', 'San Rafael Obrajuelo', 'Santa María Ostuma', 'Santiago Nonualco', 'Tapalhuaca'],
                'La Unión': ['La Unión', 'Anamorós', 'Bolívar', 'Concepción de Oriente', 'Conchagua', 'El Carmen', 'El Sauce', 'Intipucá', 'Lislique', 'Meanguera del Golfo', 'Nueva Esparta', 'Pasaquina', 'Polorós', 'San Alejo', 'San José', 'Santa Rosa de Lima', 'Yayantique', 'Yucuaiquín'],
                'Morazán': ['San Francisco Gotera', 'Arambala', 'Cacaopera', 'Chilanga', 'Corinto', 'Delicias de Concepción', 'El Divisadero', 'El Rosario', 'Gualococti', 'Guatajiagua', 'Joateca', 'Jocoaitique', 'Jocoro', 'Lolotiquillo', 'Meanguera', 'Osicala', 'Perquín', 'San Carlos', 'San Fernando', 'San Isidro', 'San Simón', 'Sensembra', 'Sociedad', 'Torola', 'Yamabal', 'Yoloaiquín'],
                'San Miguel': ['San Miguel', 'Carolina', 'Chapeltique', 'Chinameca', 'Chirilagua', 'Ciudad Barrios', 'Comacarán', 'El Tránsito', 'Lolotique', 'Moncagua', 'Nueva Guadalupe', 'Nuevo Edén de San Juan', 'Quelepa', 'San Antonio del Mosco', 'San Gerardo', 'San Jorge', 'San Luis de la Reina', 'San Rafael Oriente', 'Sesori', 'Uluazapa'],
                'San Salvador': ['San Salvador', 'Aguilares', 'Apopa', 'Ayutuxtepeque', 'Cuscatancingo', 'Delgado', 'El Paisnal', 'Guazapa', 'Ilopango', 'Mejicanos', 'Nejapa', 'Panchimalco', 'Rosario de Mora', 'San Marcos', 'San Martín', 'Santiago Texacuangos', 'Santo Tomás', 'Soyapango', 'Tonacatepeque'],
                'San Vicente': ['San Vicente', 'Apastepeque', 'Guadalupe', 'San Cayetano Istepeque', 'San Esteban Catarina', 'San Ildefonso', 'San Lorenzo', 'San Sebastián', 'Santa Clara', 'Santo Domingo', 'Tecoluca', 'Tepetitán', 'Verapaz'],
                'Santa Ana': ['Santa Ana', 'Candelaria de la Frontera', 'Chalchuapa', 'Coatepeque', 'El Congo', 'El Porvenir', 'Masahuat', 'Metapán', 'San Antonio Pajonal', 'San Sebastián Salitrillo', 'Santa Rosa Guachipilín', 'Santiago de la Frontera', 'Texistepeque'],
                'Sonsonate': ['Sonsonate', 'Acajutla', 'Armenia', 'Caluco', 'Cuisnahuat', 'Izalco', 'Juayúa', 'Nahuizalco', 'Nahulingo', 'Salcoatitán', 'San Antonio del Monte', 'San Julián', 'Santa Catarina Masahuat', 'Santa Isabel Ishuatán', 'Santo Domingo de Guzmán', 'Sonzacate'],
                'Usulután': ['Usulután', 'Alegría', 'Berlín', 'California', 'Concepción Batres', 'El Triunfo', 'Ereguayquín', 'Estanzuelas', 'Jiquilisco', 'Jucuapa', 'Jucuarán', 'Mercedes Umaña', 'Nueva Granada', 'Ozatlán', 'Puerto El Triunfo', 'San Agustín', 'San Buenaventura', 'San Dionisio', 'San Francisco Javier', 'Santa Elena', 'Santa María', 'Santiago de María', 'Tecapán']
            },

            fechaSalida: '',
            horaSalida: '',
            direccionEntrega: '',
            puntoReferencia: '',
            telefono: '',
            costoEnvio: 0,
            descuento: 0,
            metodoPago: 'Efectivo',

            errorGeneral: '',
            errorCliente: false,
            errorDepartamento: false,
            errorMunicipio: false,
            errorDireccion: false,
            errorTelefonoTocado: false,

            archivoComprobante: null,
            comprobantePreview: null,
            comprobanteNombre: '',
            comprobanteTamano: '',
            errorComprobante: false,
            errorComprobanteMensaje: '',

            get municipiosDisponibles() {
                if (!this.departamento || !this.municipiosPorDepartamento[this.departamento]) {
                    return [];
                }
                return this.municipiosPorDepartamento[this.departamento];
            },

            onDepartamentoChange() {
                this.municipio = '';
                this.errorDepartamento = false;
                this.errorMunicipio = false;
            },

            init() {
                // Escuchar evento para abrir modal desde la bolsa de compras
                window.addEventListener('abrir-modal-entrega', (e) => {
                    this.abrir(e.detail);
                });

                // Escuchar actualizaciones globales de la bolsa
                window.addEventListener('ax-cart-updated', (e) => {
                    if (this.abierto && Array.isArray(e.detail)) {
                        this.items = e.detail;
                        if (this.indiceActivo >= this.items.length) {
                            this.indiceActivo = Math.max(0, this.items.length - 1);
                        }
                    }
                });

                this.$watch('metodoPago', (val) => {
                    this.errorComprobante = false;
                    this.errorComprobanteMensaje = '';
                    if (val !== 'Transferencia Bancaria') {
                        this.removerComprobante();
                    }
                });
            },

            abrir(payload = null) {
                let source = [];
                this.idVendedorAsignado = null;
                this.nombreVendedorAsignado = '';

                if (Array.isArray(payload)) {
                    source = payload;
                } else if (payload && typeof payload === 'object') {
                    source = payload.items || [];
                    this.idVendedorAsignado = payload.id_usuario || null;
                    this.nombreVendedorAsignado = payload.nombre_vendedor || '';
                } else {
                    source = (window.AXCart ? window.AXCart.getItems() : []);
                }

                this.items = JSON.parse(JSON.stringify(source || []));
                this.items.forEach(item => {
                    item.costo_extra = Math.max(0, Number(item.costo_extra || 0));
                });
                this.indiceActivo = 0;
                this.costoEnvio = 0;
                this.descuento = 0;
                this.metodoPago = 'Efectivo';
                this.nombreCliente = '';
                this.departamento = '';
                this.municipio = '';
                this.direccionEntrega = '';
                this.puntoReferencia = '';
                this.telefono = '';

                // Fechas por defecto locales
                const hoy = new Date();
                const yyyy = hoy.getFullYear();
                const mm = String(hoy.getMonth() + 1).padStart(2, '0');
                const dd = String(hoy.getDate()).padStart(2, '0');
                this.fechaSalida = `${yyyy}-${mm}-${dd}`;

                const hh = String(hoy.getHours()).padStart(2, '0');
                const min = String(hoy.getMinutes()).padStart(2, '0');
                this.horaSalida = `${hh}:${min}`;

                this.errorGeneral = '';
                this.errorCliente = false;
                this.errorDepartamento = false;
                this.errorMunicipio = false;
                this.errorDireccion = false;
                this.errorTelefonoTocado = false;
                this.errorComprobante = false;
                this.errorComprobanteMensaje = '';
                this.abierto = true;
            },

            cerrar() {
                this.abierto = false;
                this.nombreCliente = '';
                this.departamento = '';
                this.municipio = '';
                this.direccionEntrega = '';
                this.puntoReferencia = '';
                this.telefono = '';
                this.descuento = 0;
                this.costoEnvio = 0;
                this.errorGeneral = '';
                this.errorCliente = false;
                this.errorDepartamento = false;
                this.errorMunicipio = false;
                this.errorDireccion = false;
                this.errorTelefonoTocado = false;
                this.removerComprobante();
            },

            get productoActual() {
                if (this.items && this.items.length > 0 && this.items[this.indiceActivo]) {
                    return this.items[this.indiceActivo];
                }
                return {
                    id: null,
                    cantidad: 1,
                    costo_extra: 0,
                    stock: 9999,
                    producto: '',
                    variante: '',
                    sku: 'N/A',
                    precio_venta: 0,
                    precio: 0,
                    imagen: ''
                };
            },

            onCantidadInput(event) {
                if (!this.productoActual || !this.productoActual.id) return;
                let val = parseInt(event.target.value, 10);
                if (isNaN(val) || val < 1) val = 1;

                if (this.productoActual.stock && val > this.productoActual.stock) {
                    val = this.productoActual.stock;
                    if (window.AXCart && typeof window.AXCart.showToast === 'function') {
                        window.AXCart.showToast(`Stock máximo alcanzado (${this.productoActual.stock} uds).`, 'warning');
                    }
                }

                this.productoActual.cantidad = val;
                // Sincronizar con el carrito global
                if (window.AXCart) {
                    window.AXCart.updateQuantity(this.productoActual.id, val);
                }
            },

            onCostoExtraInput(event) {
                if (!this.productoActual || !this.productoActual.id) return;
                let val = parseFloat(event.target.value);
                if (isNaN(val) || val < 0) val = 0;
                this.productoActual.costo_extra = val;
                if (window.AXCart && typeof window.AXCart.updateCostoExtra === 'function') {
                    window.AXCart.updateCostoExtra(this.productoActual.id, val);
                }
            },

            onTelefonoInput(event) {
                let val = event.target.value.replace(/\D/g, '').slice(0, 8);
                if (val.length > 4) {
                    val = val.slice(0, 4) + '-' + val.slice(4);
                }
                this.telefono = val;
                event.target.value = val;
            },

            get esTelefonoValido() {
                if (!this.telefono) return false;
                return /^[267]\d{3}-\d{4}$/.test(this.telefono.trim());
            },

            get errorTelefono() {
                if (!this.telefono) return 'Ingresa un número de teléfono de contacto.';
                const clean = this.telefono.replace(/\D/g, '');
                if (!clean) return 'Ingresa un número de teléfono de contacto.';
                if (!/^[267]/.test(clean)) {
                    return 'Formato incorrecto. Debe iniciar con 2, 6 o 7.';
                }
                if (clean.length < 8) {
                    return 'Debe completar los 8 números (ej: 7890-1234).';
                }
                return '';
            },

            onComprobanteSeleccionado(event) {
                const file = event.target.files ? event.target.files[0] : null;
                if (!file) return;

                const tipos = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                if (!tipos.includes(file.type)) {
                    this.errorComprobante = true;
                    this.errorComprobanteMensaje = 'El comprobante debe ser un archivo de imagen válido (JPG, PNG o WEBP).';
                    event.target.value = '';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    this.errorComprobante = true;
                    this.errorComprobanteMensaje = 'La imagen del comprobante no debe superar los 5MB.';
                    event.target.value = '';
                    return;
                }

                this.archivoComprobante = file;
                this.comprobanteNombre = file.name;
                this.comprobanteTamano = (file.size / 1024).toFixed(1) + ' KB';
                this.errorComprobante = false;
                this.errorComprobanteMensaje = '';

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.comprobantePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            },

            removerComprobante() {
                this.archivoComprobante = null;
                this.comprobantePreview = null;
                this.comprobanteNombre = '';
                this.comprobanteTamano = '';
                this.errorComprobante = false;
                this.errorComprobanteMensaje = '';
                const input = document.querySelector('input[x-ref="modalComprobanteInput"]');
                if (input) input.value = '';
            },

            get costoEnvioCalculado() {
                return Math.max(0, Number(this.costoEnvio || 0));
            },

            get subtotalBaseProductos() {
                return this.items.reduce((total, item) => {
                    const precio = Number(item.precio_venta || item.precio || 0);
                    const cant = Number(item.cantidad || 0);
                    return total + (precio * cant);
                }, 0);
            },

            get totalCostoExtra() {
                return this.items.reduce((total, item) => {
                    return total + Math.max(0, Number(item.costo_extra || 0));
                }, 0);
            },

            get subtotalCalculado() {
                return this.subtotalBaseProductos + this.totalCostoExtra + this.costoEnvioCalculado;
            },

            get descuentoCalculado() {
                const desc = Number(this.descuento || 0);
                return Math.min(this.subtotalCalculado, Math.max(0, desc));
            },

            get totalCalculado() {
                return Math.max(0, this.subtotalCalculado - this.descuentoCalculado);
            },

            moneda(val) {
                return new Intl.NumberFormat('es-SV', {
                    style: 'currency',
                    currency: 'USD',
                    minimumFractionDigits: 2
                }).format(Number(val || 0));
            },

            async procesarEntrega() {
                if (this.procesando) return;
                this.errorGeneral = '';

                if (!this.items || this.items.length === 0) {
                    this.errorGeneral = 'No hay productos en la bolsa para procesar la entrega.';
                    this.$nextTick(() => {
                        document.getElementById('modal-entrega-body')?.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                    return;
                }

                // 1. Validación de Nombre del Cliente (Obligatorio)
                if (!this.nombreCliente || !this.nombreCliente.trim()) {
                    this.errorCliente = true;
                    this.$nextTick(() => {
                        const el = document.getElementById('input-cliente-entrega');
                        el?.focus();
                        el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                    return;
                }

                // 2. Validación de Departamento de El Salvador (Obligatorio)
                if (!this.departamento || !this.departamento.trim()) {
                    this.errorDepartamento = true;
                    this.$nextTick(() => {
                        const el = document.getElementById('select-departamento-entrega');
                        el?.focus();
                        el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                    return;
                }

                // 3. Validación de Municipio de El Salvador (Obligatorio)
                if (!this.municipio || !this.municipio.trim()) {
                    this.errorMunicipio = true;
                    this.$nextTick(() => {
                        const el = document.getElementById('select-municipio-entrega');
                        el?.focus();
                        el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                    return;
                }

                // 4. Validación de Dirección Específica (Obligatorio)
                if (!this.direccionEntrega.trim()) {
                    this.errorDireccion = true;
                    this.$nextTick(() => {
                        const el = document.getElementById('input-direccion-entrega');
                        el?.focus();
                        el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                    return;
                }

                // 5. Validación de Teléfono (Obligatorio)
                if (!this.telefono || !this.esTelefonoValido) {
                    this.errorTelefonoTocado = true;
                    this.$nextTick(() => {
                        const el = document.getElementById('input-telefono-entrega');
                        el?.focus();
                        el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                    return;
                }

                // Validación de Comprobante de Transferencia Bancaria
                if (this.metodoPago === 'Transferencia Bancaria' && !this.archivoComprobante) {
                    this.errorComprobante = true;
                    this.errorComprobanteMensaje = 'Debes adjuntar la imagen del comprobante de transferencia bancaria.';

                    this.$nextTick(() => {
                        const elem = document.getElementById('seccion-comprobante-entrega');
                        if (elem) {
                            elem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    });
                    return;
                }

                this.procesando = true;
                const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

                const formData = new FormData();
                if (token) {
                    formData.append('_token', token);
                }
                formData.append('metodo_pago', this.metodoPago);
                formData.append('tipo_venta', 'Envio');
                formData.append('descuento', Number(this.descuento || 0));
                formData.append('precio_envio', this.costoEnvioCalculado);
                formData.append('nombre_cliente', this.nombreCliente.trim());
                formData.append('departamento', this.departamento.trim());
                formData.append('municipio', this.municipio.trim());
                formData.append('direccion_entrega', this.direccionEntrega.trim());
                formData.append('punto_referencia', (this.puntoReferencia || '').trim());
                formData.append('telefono', this.telefono.trim());
                formData.append('fecha_salida', this.fechaSalida);
                formData.append('hora_salida', this.horaSalida);

                if (this.idVendedorAsignado) {
                    formData.append('id_usuario', this.idVendedorAsignado);
                }

                this.items.forEach((item, index) => {
                    formData.append(`lineas[${index}][id_variante]`, item.id);
                    formData.append(`lineas[${index}][cantidad]`, Number(item.cantidad || 1));
                    formData.append(`lineas[${index}][precio_unitario]`, Number(item.precio_venta || item.precio || 0));
                    formData.append(`lineas[${index}][costo_extra]`, Math.max(0, Number(item.costo_extra) || 0));
                });

                if (this.metodoPago === 'Transferencia Bancaria' && this.archivoComprobante) {
                    formData.append('comprobante_pago', this.archivoComprobante);
                }

                try {
                    const response = await fetch('/ventas', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: formData
                    });

                    if (response.status === 419) {
                        throw new Error('La sesión de seguridad ha expirado. Por favor recarga la página para procesar la venta.');
                    }

                    const rawText = await response.text();
                    let data = null;
                    try {
                        // Limpiar posible BOM (UTF-8) o whitespace antes/después del JSON
                        const cleanText = rawText.replace(/^\uFEFF/, '').trim();
                        data = JSON.parse(cleanText);
                    } catch (e) {
                        console.warn('JSON parse falló, rawText (primeros 200 chars):', rawText.substring(0, 200));
                        data = null;
                    }

                    // Si el servidor devolvió un error HTTP (4xx, 5xx)
                    if (!response.ok) {
                        if (data && data.message && data.message.toLowerCase().includes('comprobante')) {
                            this.errorComprobante = true;
                            this.errorComprobanteMensaje = data.message;
                            this.$nextTick(() => {
                                const elem = document.getElementById('seccion-comprobante-entrega');
                                if (elem) {
                                    elem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            });
                            return;
                        }
                        
                        let mensajeError = (data && data.message) ? data.message : '';
                        if (!mensajeError && data && data.errors) {
                            mensajeError = Object.values(data.errors).flat().join(' ');
                        }
                        if (!mensajeError && rawText) {
                            const doc = new DOMParser().parseFromString(rawText, 'text/html');
                            const title = doc.querySelector('.exception_title, title, h1, .text-xl')?.textContent?.trim();
                            mensajeError = title || `Error al procesar la venta (${response.status} ${response.statusText}).`;
                        }
                        if (!mensajeError) {
                            mensajeError = `Error del servidor (${response.status} ${response.statusText}). Por favor revisa los datos e intenta nuevamente.`;
                        }

                        this.errorGeneral = mensajeError;
                        this.$nextTick(() => {
                            document.getElementById('modal-entrega-body')?.scrollTo({ top: 0, behavior: 'smooth' });
                        });
                        return;
                    }

                    // ── RESPUESTA EXITOSA (2xx) ──
                    // Si response.ok es true, la venta YA se registró en el servidor.
                    // Aunque JSON.parse falle, tratamos como éxito para no confundir al usuario.

                    this.cerrar();

                    // Limpiar carrito local, global y persistencia en navegador
                    try {
                        localStorage.removeItem('ax_carrito');
                    } catch (e) {}

                    if (window.AXCart && typeof window.AXCart.clearCart === 'function') {
                        window.AXCart.clearCart();
                    } else {
                        window.dispatchEvent(new CustomEvent('ax-cart-updated', { detail: [] }));
                    }

                    this.items = [];

                    // Extraer datos del JSON si se pudo parsear, o usar valores genéricos
                    const idVenta = data ? data.id_venta : null;
                    const totalVenta = data ? data.total : null;

                    // Mostrar confirmación y redirigir directamente al dashboard de ventas
                    if (typeof Swal !== 'undefined') {
                        let htmlContent = '<div class="text-sm space-y-1 text-slate-600">';
                        if (idVenta) {
                            htmlContent += `<p>Folio: <strong class="text-blue-600 font-mono">#VNT-${String(idVenta).padStart(5, '0')}</strong></p>`;
                        }
                        if (totalVenta !== null) {
                            htmlContent += `<p>Total: <strong class="text-emerald-600 font-black">$${Number(totalVenta).toFixed(2)}</strong></p>`;
                        }
                        htmlContent += '<p class="text-xs text-amber-600 font-semibold mt-2">Redirigiendo a Control de Envíos y Estados...</p></div>';

                        Swal.fire({
                            icon: 'success',
                            title: '¡Entrega y Venta Registrada!',
                            html: htmlContent,
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            customClass: {
                                container: '!z-[100000]'
                            }
                        }).then(() => {
                            window.location.href = '{{ route("ventas.pedidos") }}';
                        });
                    } else {
                        window.location.href = '{{ route("ventas.pedidos") }}';
                    }
                } catch (err) {
                    console.error('Error al procesar venta:', err);
                    if (err.message && err.message.toLowerCase().includes('comprobante')) {
                        this.errorComprobante = true;
                        this.errorComprobanteMensaje = err.message;
                        this.$nextTick(() => {
                            const elem = document.getElementById('seccion-comprobante-entrega');
                            if (elem) {
                                elem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        });
                        return;
                    }
                    this.errorGeneral = err.message || 'No se pudo completar el registro de la entrega.';
                    this.$nextTick(() => {
                        document.getElementById('modal-entrega-body')?.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                } finally {
                    this.procesando = false;
                }
            }
        };
    }
</script>
