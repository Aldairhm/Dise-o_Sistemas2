<x-app title="Nueva Venta | AXStore">
    <div 
        x-data="carritoVentas({{ Js::from($variantes ?? []) }}, {{ Js::from($vendedores ?? []) }}, {{ (int) Auth::id() }})" 
        class="max-w-[1440px] mx-auto space-y-6"
    >

        <!-- TOAST / ALERTA NO INTRUSIVA EN TIEMPO REAL -->
        <div 
            x-show="toast.visible" 
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border text-sm font-semibold backdrop-blur-md transition-all"
            :class="{
                'bg-amber-50/95 border-amber-200 text-amber-900 shadow-amber-500/10': toast.tipo === 'warning',
                'bg-red-50/95 border-red-200 text-red-900 shadow-red-500/10': toast.tipo === 'error',
                'bg-emerald-50/95 border-emerald-200 text-emerald-900 shadow-emerald-500/10': toast.tipo === 'success',
                'bg-blue-50/95 border-blue-200 text-blue-900 shadow-blue-500/10': toast.tipo === 'info'
            }"
            style="display: none;"
        >
            <i class="fas text-base" :class="{
                'fa-triangle-exclamation text-amber-500': toast.tipo === 'warning',
                'fa-circle-xmark text-red-500': toast.tipo === 'error',
                'fa-circle-check text-emerald-500': toast.tipo === 'success',
                'fa-circle-info text-blue-500': toast.tipo === 'info'
            }"></i>
            <span x-text="toast.mensaje"></span>
            <button type="button" @click="cerrarToast()" class="ml-2 text-slate-400 hover:text-slate-600 p-1">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <!-- HEADER DEL MÓDULO -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <i class="fas fa-cash-register text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600 mb-0.5">Terminal de Venta</p>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Registrar Venta</h1>
                    </div>
                </div>
            </div>

            <!-- BADGE INFORMATIVO -->
            <div class="flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm text-xs font-bold text-slate-600">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Punto de Venta Activo</span>
                <span class="text-slate-300">|</span>
                <span class="text-slate-500 font-semibold">{{ date('d/m/Y') }}</span>
            </div>
        </div>

        <!-- SUB-NAV / PESTAÑAS -->
        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones de ventas">
            <a href="{{ route('ventas.create') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-cash-register"></i>
                <span>Terminal de Ventas (Carrito)</span>
            </a>
            <a href="{{ route('ventas.pedidos') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-boxes-packing text-slate-400"></i>
                <span>Control de Envíos y Estados</span>
            </a>
            <a href="{{ route('ventas.index') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-chart-line text-slate-400"></i>
                <span>Dashboard Histórico</span>
            </a>
        </nav>

        <!-- ÁREA DE TRABAJO DIVIDIDA: CATÁLOGO VS CARRITO -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- PANEL IZQUIERDO: BÚSQUEDA Y CATÁLOGO DE PRODUCTOS (7 COLUMNAS EN LG) -->
            <section class="lg:col-span-7 xl:col-span-7 bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-sm space-y-5">
                
                <!-- ENCABEZADO Y BUSCADOR -->
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Catálogo de Productos</h2>
                            <p class="text-xs text-slate-500">Haz clic en "Agregar" para incorporar el producto al carrito.</p>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 bg-slate-100 border border-slate-200 px-3 py-1 rounded-lg self-start sm:self-auto" x-text="variantesFiltradas.length + ' disponibles'">
                        </span>
                    </div>

                    <!-- BARRA DE BÚSQUEDA EN TIEMPO REAL -->
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input 
                            type="search" 
                            x-model="busqueda"
                            placeholder="Buscar por producto, variante, SKU o código..." 
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-inner"
                        >
                    </div>
                </div>

                <!-- LISTADO / GRILLA DE VARIANTES DISPONIBLES -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 max-h-[580px] overflow-y-auto pr-1">
                    
                    <template x-for="variante in variantesFiltradas" :key="variante.id">
                        <div class="group flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-4 hover:border-emerald-400 hover:shadow-md hover:shadow-emerald-500/5 transition-all">
                            <div class="flex items-start gap-3">
                                <!-- IMAGEN / THUMBNAIL -->
                                <div class="w-14 h-14 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden text-slate-400">
                                    <template x-if="variante.imagen">
                                        <img :src="variante.imagen" :alt="variante.variante" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!variante.imagen">
                                        <i class="fas fa-box text-xl"></i>
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="inline-block text-[10px] font-black uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded mb-1" x-text="variante.sku || 'Sin SKU'"></span>
                                    <h3 class="text-sm font-bold text-slate-800 truncate group-hover:text-emerald-700 transition-colors" x-text="variante.producto"></h3>
                                    <p class="text-xs text-slate-500 truncate" x-text="variante.variante"></p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between border-t border-slate-100 mt-3 pt-3">
                                <div>
                                    <span 
                                        class="text-[10px] uppercase font-bold block leading-tight"
                                        :class="variante.stock > 5 ? 'text-slate-400' : (variante.stock > 0 ? 'text-amber-600' : 'text-red-500')"
                                        x-text="'Stock: ' + variante.stock"
                                    ></span>
                                    <span class="text-base font-black text-slate-900 leading-tight" x-text="moneda(variante.precio_venta)"></span>
                                </div>
                                <button 
                                    type="button" 
                                    @click="agregarProducto(variante)"
                                    :disabled="variante.stock <= 0"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                                    :class="variante.stock > 0 ? 'bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white' : 'bg-slate-100 text-slate-400'"
                                >
                                    <i class="fas fa-plus"></i>
                                    <span x-text="variante.stock > 0 ? 'Agregar' : 'Agotado'"></span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- ESTADO VACÍO CUANDO NO HAY COINCIDENCIAS -->
                    <div 
                        x-show="variantesFiltradas.length === 0" 
                        class="sm:col-span-2 py-12 text-center text-slate-400"
                        style="display: none;"
                    >
                        <i class="fas fa-magnifying-glass text-3xl mb-3 text-slate-300"></i>
                        <p class="text-sm font-semibold">No se encontraron productos con ese criterio.</p>
                    </div>

                </div>

            </section>

            <!-- PANEL DERECHO: CARRITO DE COMPRAS Y RESUMEN (5 COLUMNAS EN LG) -->
            <section class="lg:col-span-5 xl:col-span-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col xl:sticky xl:top-24">
                
                <!-- ENCABEZADO DEL CARRITO -->
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 bg-slate-50/70">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-black text-slate-900">Carrito de Venta</h2>
                            <p class="text-xs text-slate-500">Detalle de los productos a facturar</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span 
                            class="text-xs font-bold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-lg"
                            x-text="totalArticulos + ' arts'"
                        ></span>
                        <button 
                            type="button" 
                            x-show="carrito.length > 0"
                            @click="vaciarCarrito()" 
                            class="text-[11px] font-bold text-slate-400 hover:text-red-500 transition-colors p-1"
                            title="Vaciar carrito"
                            style="display: none;"
                        >
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <!-- TABLA MINIMALISTA DEL CARRITO -->
                <div class="overflow-x-auto max-h-[360px] overflow-y-auto">
                    
                    <!-- ESTADO VACÍO DEL CARRITO -->
                    <div 
                        x-show="carrito.length === 0" 
                        class="py-16 px-6 text-center text-slate-400 space-y-2"
                    >
                        <div class="w-14 h-14 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-2 border border-slate-100">
                            <i class="fas fa-basket-shopping text-2xl"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-600">El carrito está vacío</p>
                        <p class="text-xs text-slate-400 max-w-xs mx-auto">Selecciona productos del catálogo a la izquierda para agregarlos a la venta.</p>
                    </div>

                    <!-- TABLA DE ARTÍCULOS ACTIVOS -->
                    <table 
                        x-show="carrito.length > 0" 
                        class="w-full text-left text-xs"
                        style="display: none;"
                    >
                        <thead class="bg-slate-50/80 text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-200">
                            <tr>
                                <th class="px-3 py-2.5">Producto</th>
                                <th class="px-1.5 py-2.5 text-center w-20">Cant.</th>
                                <th class="px-1.5 py-2.5 text-right w-16">P. Unit.</th>
                                <th class="px-2 py-2.5 text-right w-20">Subtotal</th>
                                <th class="px-1.5 py-2.5 text-center w-8"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            
                            <template x-for="item in carrito" :key="item.id">
                                <tr class="hover:bg-slate-50/60 transition-colors group">
                                    <td class="px-3 py-2.5">
                                        <div class="min-w-[110px]">
                                            <p class="font-bold text-slate-800 text-xs truncate max-w-[130px]" :title="item.producto" x-text="item.producto"></p>
                                            <p class="text-[11px] text-slate-400 truncate max-w-[130px]" x-text="item.variante"></p>
                                            
                                            <div class="flex items-center flex-wrap gap-x-2 gap-y-1 mt-1">
                                                <span class="text-[9px] font-bold text-slate-400" x-text="'Stock: ' + item.stock"></span>
                                                
                                                <!-- CAMPO COSTO EXTRA -->
                                                <div class="inline-flex items-center gap-1 bg-amber-50/90 border border-amber-200/80 rounded px-1.5 py-0.5" title="Costo extra agregado por el vendedor">
                                                    <span class="text-[9px] font-bold text-amber-700 uppercase tracking-tight">+ Extra:</span>
                                                    <div class="relative inline-block w-12">
                                                        <span class="absolute left-1 top-1/2 -translate-y-1/2 text-[9px] font-bold text-amber-600">$</span>
                                                        <input 
                                                            type="number" 
                                                            min="0" 
                                                            step="0.5" 
                                                            placeholder="0"
                                                            x-model.number="item.costo_extra"
                                                            @input="actualizarCostoExtra(item, $event)"
                                                            class="w-full pl-2.5 pr-0.5 py-0 bg-transparent text-[10px] font-black text-amber-900 border-0 focus:ring-0 text-right p-0 leading-none"
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-1.5 py-2.5 text-center">
                                        <!-- STEPPER DE CANTIDAD CON VALIDACIÓN DE STOCK -->
                                        <div class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50">
                                            <button 
                                                type="button" 
                                                @click="decrementar(item)"
                                                :disabled="item.cantidad <= 1"
                                                class="px-1.5 py-1 text-slate-500 hover:text-slate-800 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                            >
                                                <i class="fas fa-minus text-[9px]"></i>
                                            </button>
                                            <input 
                                                type="number" 
                                                min="1" 
                                                :max="item.stock"
                                                :value="item.cantidad"
                                                @change="actualizarCantidad(item, $event)"
                                                class="w-8 text-center bg-transparent border-0 py-1 text-xs font-black text-slate-800 focus:ring-0 p-0"
                                            >
                                            <button 
                                                type="button" 
                                                @click="incrementar(item)"
                                                :disabled="item.cantidad >= item.stock"
                                                class="px-1.5 py-1 text-slate-500 hover:text-slate-800 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                            >
                                                <i class="fas fa-plus text-[9px]"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-1.5 py-2.5 text-right font-medium text-slate-600" x-text="moneda(item.precio_venta)">
                                    </td>
                                    <td class="px-2 py-2.5 text-right font-medium">
                                        <span class="font-black text-slate-900 block text-xs" x-text="moneda(subtotalFila(item))"></span>
                                        <template x-if="Number(item.costo_extra) > 0">
                                            <span class="text-[9px] font-bold text-amber-600 block leading-tight" x-text="'+' + moneda(item.costo_extra) + ' extra'"></span>
                                        </template>
                                    </td>
                                    <td class="px-1.5 py-2.5 text-center">
                                        <button 
                                            type="button" 
                                            @click="eliminarItem(item.id)" 
                                            class="text-slate-300 hover:text-red-500 transition-colors p-1" 
                                            title="Quitar del carrito"
                                        >
                                            <i class="fas fa-trash-can text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>

                        </tbody>
                    </table>
                </div>

                <!-- SECCIÓN DE MÉTODO DE PAGO Y TOTALES (INFERIOR DEL CARRITO) -->
                <div class="p-5 bg-slate-50/80 border-t border-slate-200 space-y-4">
                    
                    <!-- MÉTODO DE PAGO -->
                    <div>
                        <label for="metodo_pago" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Método de Pago <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="metodo_pago" 
                            name="metodo_pago" 
                            x-model="metodoPago"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all cursor-pointer"
                        >
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                        </select>
                    </div>

                    <!-- ASIGNAR A OTRO VENDEDOR (CHECKBOX Y COMBOBOX CON BÚSQUEDA) - SOLO ADMINISTRADORES -->
                    @if(Auth::user()?->rol === 'admin')
                    <div class="pt-1">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none group">
                            <input 
                                type="checkbox" 
                                x-model="asignarOtroVendedor" 
                                class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500/20 focus:ring-offset-0 cursor-pointer"
                            >
                            <span class="text-xs font-bold text-slate-700 group-hover:text-blue-600 transition-colors flex items-center gap-1.5">
                                <i class="fas fa-user-tag text-slate-400 group-hover:text-blue-500 text-[11px]"></i>
                                Otro vendedor
                            </span>
                        </label>

                        <!-- COMBOBOX FILTRABLE DE VENDEDORES -->
                        <div 
                            x-show="asignarOtroVendedor" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="mt-2.5 p-3 rounded-xl border border-blue-200 bg-blue-50/40 space-y-2 relative"
                            @click.away="dropdownVendedoresAbierto = false"
                        >
                            <div class="flex items-center justify-between">
                                <label class="block text-[10px] font-black uppercase tracking-wider text-blue-900">
                                    Vendedor responsable <span class="text-rose-500">*</span>
                                </label>
                                <template x-if="vendedorSeleccionado">
                                    <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">
                                        Asignado: <span x-text="vendedorSeleccionado.nombre_real || vendedorSeleccionado.username"></span>
                                    </span>
                                </template>
                            </div>

                            <!-- INPUT COMBOBOX DE BÚSQUEDA -->
                            <div class="relative">
                                <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input 
                                    type="text" 
                                    x-model="busquedaVendedor"
                                    @focus="dropdownVendedoresAbierto = true"
                                    @input="dropdownVendedoresAbierto = true; idVendedorAsignado = null"
                                    placeholder="Escribe el nombre del vendedor..."
                                    class="w-full rounded-lg border border-slate-300 bg-white pl-8 pr-8 py-2 text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-2xs"
                                    :class="errorVendedor ? 'border-rose-400 ring-2 ring-rose-400/20' : ''"
                                >
                                <template x-if="busquedaVendedor">
                                    <button 
                                        type="button" 
                                        @click="limpiarVendedor()" 
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5 cursor-pointer"
                                        title="Limpiar vendedor"
                                    >
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </template>
                            </div>

                            <!-- MENÚ DESPLEGABLE DEPILADO EN TIEMPO REAL -->
                            <div 
                                x-show="dropdownVendedoresAbierto"
                                x-transition
                                class="absolute left-3 right-3 top-full mt-1 bg-white rounded-xl shadow-xl border border-slate-200 z-50 max-h-48 overflow-y-auto custom-scrollbar divide-y divide-slate-100 text-xs"
                                style="display: none;"
                            >
                                <template x-for="vend in vendedoresFiltrados" :key="vend.id">
                                    <button 
                                        type="button" 
                                        @click="seleccionarVendedor(vend)"
                                        class="w-full text-left px-3 py-2 hover:bg-blue-50 flex items-center justify-between gap-2 transition-colors cursor-pointer"
                                        :class="idVendedorAsignado === vend.id ? 'bg-blue-50/80 font-bold text-blue-700' : 'text-slate-700'"
                                    >
                                        <div class="min-w-0">
                                            <p class="font-bold truncate" x-text="vend.nombre_real || vend.username"></p>
                                            <p class="text-[10px] text-slate-400 truncate" x-text="'@' + vend.username + ' · ' + (vend.rol || 'Vendedor')"></p>
                                        </div>
                                        <template x-if="idVendedorAsignado === vend.id">
                                            <i class="fas fa-check text-blue-600 text-xs shrink-0"></i>
                                        </template>
                                    </button>
                                </template>

                                <template x-if="vendedoresFiltrados.length === 0">
                                    <div class="p-3 text-center text-slate-400 text-xs">
                                        <i class="fas fa-user-slash text-slate-300 block mb-1 text-sm"></i>
                                        No se encontró ningún vendedor con ese nombre
                                    </div>
                                </template>
                            </div>

                            <p class="text-[10px] text-blue-800/80 flex items-center gap-1">
                                <i class="fas fa-circle-info text-[9px] text-blue-600"></i>
                                La venta y sus comisiones se registrarán a nombre del vendedor seleccionado.
                            </p>
                        </div>
                    </div>
                    @endif

                    <!-- SECCIÓN DE COMPROBANTE DE TRANSFERENCIA (CONDICIONAL) -->
                    <div 
                        x-show="metodoPago === 'Transferencia Bancaria'" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-2 scale-98"
                        class="p-4 rounded-xl border border-blue-200 bg-blue-50/50 space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-blue-950 flex items-center gap-1.5">
                                <i class="fas fa-file-invoice-dollar text-blue-600"></i>
                                Comprobante de Transferencia <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <span class="text-[10px] font-semibold text-blue-700 bg-blue-100 border border-blue-200 px-2 py-0.5 rounded-full">
                                Imagen requerida
                            </span>
                        </div>

                        <!-- ZONA DE CARGA DE ARCHIVO (DROPZONE O INPUT) -->
                        <template x-if="!comprobantePreview">
                            <div 
                                @click="$refs.comprobanteInput.click()"
                                class="border-2 border-dashed border-blue-300 hover:border-blue-500 rounded-xl p-4 text-center bg-white cursor-pointer transition-all hover:bg-blue-50/40 group shadow-2xs"
                            >
                                <div class="w-10 h-10 mx-auto rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-inner">
                                    <i class="fas fa-cloud-arrow-up"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-800">Haz clic para subir el comprobante</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Formatos: JPG, PNG o WEBP (Máx. 5MB)</p>
                            </div>
                        </template>

                        <!-- PREVIEW DE LA IMAGEN CARGADA -->
                        <template x-if="comprobantePreview">
                            <div class="relative flex items-center gap-3 p-3 bg-white border border-blue-200 rounded-xl shadow-xs">
                                <img :src="comprobantePreview" alt="Preview Comprobante" class="w-14 h-14 object-cover rounded-lg border border-slate-200 shrink-0">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-900 truncate" x-text="comprobanteNombre"></p>
                                    <p class="text-[10px] text-slate-400 font-mono" x-text="comprobanteTamano"></p>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 mt-1">
                                        <i class="fas fa-circle-check text-[9px]"></i> Comprobante adjunto
                                    </span>
                                </div>
                                <button 
                                    type="button" 
                                    @click="removerComprobante()" 
                                    class="p-2 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer"
                                    title="Eliminar o cambiar imagen"
                                >
                                    <i class="fas fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </template>

                        <input 
                            type="file" 
                            x-ref="comprobanteInput" 
                            @change="onComprobanteSeleccionado($event)" 
                            accept="image/png,image/jpeg,image/jpg,image/webp" 
                            class="hidden"
                        >
                    </div>

                    <!-- DESCUENTO GLOBAL -->
                    <div class="flex items-center justify-between gap-4">
                        <label for="descuento-global" class="text-xs font-bold text-slate-600">
                            Descuento ($):
                        </label>
                        <div class="relative w-28">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">$</span>
                            <input 
                                type="number" 
                                id="descuento-global" 
                                min="0" 
                                step="0.5" 
                                x-model.number="descuentoGlobal"
                                class="w-full pl-7 pr-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-right text-slate-800 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10"
                            >
                        </div>
                    </div>

                    <!-- DESGLOSE FINANCIERO REACTIVO -->
                    <div class="space-y-2 text-xs border-t border-slate-200 pt-3">
                        <div class="flex justify-between items-center text-slate-600">
                            <span class="font-medium">Subtotal productos:</span>
                            <span class="font-bold text-slate-800" x-text="moneda(subtotalProductos)"></span>
                        </div>
                        <template x-if="totalCostosExtras > 0">
                            <div class="flex justify-between items-center text-amber-700 bg-amber-50/70 border border-amber-200/50 px-2.5 py-1.5 rounded-lg">
                                <span class="font-bold flex items-center gap-1.5">
                                    <i class="fas fa-plus-circle text-amber-500 text-[11px]"></i> Costos extras:
                                </span>
                                <span class="font-black text-amber-700" x-text="'+' + moneda(totalCostosExtras)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between items-center text-slate-600">
                            <span class="font-medium">Subtotal general:</span>
                            <span class="font-bold text-slate-800" x-text="moneda(subtotalGeneral)"></span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600">
                            <span class="font-medium">Descuentos:</span>
                            <span class="font-bold text-emerald-600" x-text="'-' + moneda(totalDescuento)"></span>
                        </div>
                        <div class="flex justify-between items-baseline border-t border-slate-200 pt-2 mt-2">
                            <span class="text-sm font-black text-slate-900 uppercase tracking-wider">Total a pagar:</span>
                            <span class="text-2xl font-black text-emerald-600 leading-none" x-text="moneda(totalPagar)"></span>
                        </div>
                    </div>

                    <!-- ACCIONES DE FACTURACIÓN Y DESPACHO -->
                    <div class="space-y-2 pt-1">
                        <!-- BOTÓN PROCESAR VENTA EN TIENDA -->
                        <button 
                            type="button" 
                            id="btn-procesar-venta"
                            @click="procesarVenta()"
                            :disabled="!puedeProcesar() || procesando"
                            class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 text-sm transition-all duration-200 shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2 cursor-pointer hover:scale-[1.01] active:scale-[0.99] disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none disabled:hover:scale-100 disabled:hover:bg-blue-600"
                        >
                            <i class="fas" :class="procesando ? 'fa-spinner fa-spin' : 'fa-check-circle text-base'"></i>
                            <span x-text="procesando ? 'Procesando Venta...' : 'Facturar Venta en Tienda (Mostrador)'"></span>
                        </button>

                        <!-- BOTÓN SECUNDARIO DESPACHAR COMO ENVÍO -->
                        <button 
                            type="button" 
                            x-show="carrito.length > 0"
                            @click="abrirEntregaDesdeCarrito()"
                            :disabled="procesando"
                            class="w-full rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold py-2.5 text-xs transition-all flex items-center justify-center gap-2 cursor-pointer border border-blue-200 shadow-2xs hover:scale-[1.01] active:scale-[0.99] disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            <i class="fas fa-truck-fast"></i>
                            <span>Despachar pedido como Envío / Delivery</span>
                        </button>
                    </div>

                </div>

            </section>

        </div>

        <!-- MODAL ELECCIÓN TIPO DE VENTA: TIENDA VS ENVÍO -->
        <template x-teleport="body">
            <div 
                x-show="modalTipoVentaAbierto" 
                x-cloak
                class="fixed inset-0 z-[9990] overflow-y-auto"
                style="display: none;"
                role="dialog"
                aria-modal="true"
                @keydown.escape.window="cerrarModalTipoVenta()"
            >
                <!-- Backdrop oscuro translúcido con desenfoque suave -->
                <div 
                    x-show="modalTipoVentaAbierto"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                    @click="cerrarModalTipoVenta()"
                ></div>

                <div class="flex min-h-screen items-center justify-center p-3 sm:p-4 text-center">
                    <div 
                        x-show="modalTipoVentaAbierto"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                        @click.away="cerrarModalTipoVenta()"
                        class="relative z-10 w-full max-w-lg transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all border border-slate-200 flex flex-col my-6"
                    >
                        <!-- CABECERA ELEGANTE AZUL AXSTORE -->
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-5 sm:px-6 py-4 flex items-center justify-between text-white shadow-md">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white text-base shadow-inner shrink-0">
                                    <i class="fas fa-hand-holding-dollar"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-black tracking-tight leading-tight">Tipo de Venta</h3>
                                    <p class="text-xs text-blue-100 font-medium">¿Cómo deseas despachar este producto?</p>
                                </div>
                            </div>
                            <button 
                                type="button" 
                                @click="cerrarModalTipoVenta()" 
                                class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer"
                                title="Cerrar ventana"
                            >
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>

                        <!-- RESUMEN DEL PRODUCTO SELECCIONADO -->
                        <template x-if="varianteSeleccionada">
                            <div class="p-4 bg-slate-50 border-b border-slate-200/80 flex items-center gap-3.5">
                                <div class="w-13 h-13 rounded-xl bg-white border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                                    <template x-if="varianteSeleccionada.imagen">
                                        <img :src="varianteSeleccionada.imagen" :alt="varianteSeleccionada.producto" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!varianteSeleccionada.imagen">
                                        <i class="fas fa-box text-slate-400 text-lg"></i>
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-[9px] font-black uppercase tracking-wider text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.2 rounded" x-text="varianteSeleccionada.sku || 'Sin SKU'"></span>
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.2 rounded" x-text="'Stock: ' + varianteSeleccionada.stock + ' uds'"></span>
                                    </div>
                                    <h4 class="text-xs font-black text-slate-900 truncate" x-text="varianteSeleccionada.producto"></h4>
                                    <p class="text-[11px] text-slate-500 font-semibold truncate" x-text="varianteSeleccionada.variante"></p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-[10px] text-slate-400 font-bold block uppercase">Precio</span>
                                    <span class="text-base font-black text-slate-900" x-text="moneda(varianteSeleccionada.precio_venta)"></span>
                                </div>
                            </div>
                        </template>

                        <!-- TARJETAS DE OPCIONES INTERACTIVAS -->
                        <div class="p-5 sm:p-6 space-y-3.5">
                            
                            <!-- Opción 1: Venta en Tienda (Mostrador) -->
                            <button 
                                type="button" 
                                @click="seleccionarVentaTienda()" 
                                class="w-full p-4 rounded-2xl border-2 border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/40 text-left transition-all duration-200 group flex items-start gap-4 cursor-pointer shadow-2xs hover:shadow-md hover:shadow-emerald-500/10"
                            >
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-xl group-hover:scale-105 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4 class="text-sm font-black text-slate-900 group-hover:text-emerald-700 transition-colors">
                                            Venta en Tienda (Mostrador)
                                        </h4>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded-full">
                                            Presencial
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 leading-snug">
                                        Entrega física inmediata al cliente en caja. Incorpora el producto al carrito de la terminal para facturación en mostrador.
                                    </p>
                                </div>
                                <div class="self-center text-slate-300 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all">
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </div>
                            </button>

                            <!-- Opción 2: Envío / Entrega a Domicilio -->
                            <button 
                                type="button" 
                                @click="seleccionarVentaEnvio()" 
                                class="w-full p-4 rounded-2xl border-2 border-slate-200 hover:border-blue-500 hover:bg-blue-50/40 text-left transition-all duration-200 group flex items-start gap-4 cursor-pointer shadow-2xs hover:shadow-md hover:shadow-blue-500/10"
                            >
                                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 text-xl group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-truck-fast"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4 class="text-sm font-black text-slate-900 group-hover:text-blue-700 transition-colors">
                                            Envío / Entrega a Domicilio
                                        </h4>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-blue-700 bg-blue-100 border border-blue-200 px-2 py-0.5 rounded-full">
                                            Delivery
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 leading-snug">
                                        Despacho con mensajería. Abre el formulario de entrega para especificar dirección, teléfono de contacto y tarifa de envío.
                                    </p>
                                </div>
                                <div class="self-center text-slate-300 group-hover:text-blue-600 group-hover:translate-x-1 transition-all">
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </div>
                            </button>

                        </div>

                        <!-- PIE DEL MODAL -->
                        <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                            <button 
                                type="button" 
                                @click="cerrarModalTipoVenta()" 
                                class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-200/60 rounded-xl transition-colors cursor-pointer"
                            >
                                Cancelar
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </template>

    </div>
</x-app>
