<x-app title="Manejo de inventario | AXStore">
    <meta name="transferencia-tienda-url" content="{{ route('movimientos-bodega.transferencia-tienda') }}">

    <div class="max-w-[1440px] mx-auto space-y-6" x-data="gestionMovimientosKardex()">
        <div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 mb-4"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
            <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600 mb-1">Inventario</p>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Manejo de inventario</h1>
            <p class="text-sm text-slate-500 mt-2">Transfiere existencias y consulta el kardex completo del inventario.</p>
        </div>

        <nav class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" aria-label="Secciones de compras">
            <a href="{{ route('compras.create') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-cart-plus mr-2"></i>Nueva compra</a>
            <a href="{{ route('compras.historial') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-clock-rotate-left mr-2"></i>Historial</a>
            <a href="{{ route('compras.movimientos') }}" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm"><i class="fas fa-warehouse mr-2"></i>Gestión de Inventario</a>
            <a href="{{ route('compras.devoluciones') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-truck-arrow-right mr-2"></i>Devoluciones a proveedores</a>
        </nav>

        <nav class="flex max-w-xl gap-1 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Operaciones de inventario">
            <button type="button" @click="inventarioTab = 'transferir'" :class="inventarioTab === 'transferir' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50'" class="flex-1 rounded-xl px-4 py-2.5 text-sm font-bold transition-all">
                <i class="fas fa-arrow-right-arrow-left mr-2"></i>Transferir
            </button>
            <button type="button" @click="inventarioTab = 'kardex'" :class="inventarioTab === 'kardex' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50'" class="flex-1 rounded-xl px-4 py-2.5 text-sm font-bold transition-all">
                <i class="fas fa-list-check mr-2"></i>Kardex
            </button>
        </nav>

        <section x-show="inventarioTab === 'kardex'" x-cloak class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Filtrar Movimientos (Carga Inmediata)</p>
            <div class="flex flex-wrap gap-2.5">
                @php
                $filtros = [
                'todos' => ['icon' => 'fa-list-check', 'label' => 'Todos los movimientos'],
                'transferencias' => ['icon' => 'fa-arrow-right-arrow-left', 'label' => 'Transferencias a tienda'],
                'devoluciones' => ['icon' => 'fa-truck-arrow-right', 'label' => 'Devoluciones'],
                'compras' => ['icon' => 'fa-cart-flatbed', 'label' => 'Compras recibidas'],
                'entradas' => ['icon' => 'fa-arrow-right-to-bracket', 'label' => 'Solo entradas'],
                'salidas' => ['icon' => 'fa-arrow-right-from-bracket', 'label' => 'Solo salidas'],
                ];
                @endphp
                @foreach($filtros as $valor => $data)
                <button type="button" @click="filtrarKardex('{{ $valor }}')"
                    :class="filtroMovimiento === '{{ $valor }}' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200'"
                    class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-xs font-bold shadow-sm transition-all duration-200 cursor-pointer"
                    :disabled="cargandoKardex">
                    <i :class="cargandoKardex && filtroMovimiento === '{{ $valor }}' ? 'fas fa-spinner fa-spin text-white' : 'fas {{ $data['icon'] }} ' + (filtroMovimiento === '{{ $valor }}' ? 'text-white' : 'text-slate-400')"></i>
                    <span x-text="cargandoKardex && filtroMovimiento === '{{ $valor }}' ? 'Filtrando...' : '{{ $data['label'] }}'"></span>
                </button>
                @endforeach
            </div>
        </section>

        <section x-show="inventarioTab === 'transferir'" x-cloak class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
            x-data="transferenciaBodega({{ Js::from(
                     $variantes->map(fn ($v) => [
                         'id' => $v->id,
                         'producto' => $v->producto?->nombre ?? 'Producto',
                         'variante' => $v->nombre_variante,
                         'sku' => $v->sku,
                         'reserva' => $v->reserva,
                         'imagen' => ($imagen = $v->imagenes->firstWhere('es_principal', 1) ?? $v->imagenes->first())
                             ? (str_starts_with($imagen->ruta_imagen, 'http') ? $imagen->ruta_imagen : \Illuminate\Support\Facades\Storage::disk('s3')->url($imagen->ruta_imagen))
                             : null,
                     ])->values()
                 ) }})">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <i class="fas fa-truck-ramp-box"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900">Transferir a tienda</h2>
                    <p class="text-xs text-slate-500 mt-1">La cantidad se descuenta de bodega y se suma al stock.</p>
                </div>
            </div>

            <div x-show="mensaje" x-text="mensaje" class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700" style="display:none"></div>
            <div x-show="error" x-text="error" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700" style="display:none"></div>

            <template x-if="!varianteSeleccionada">
                <div>
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" x-show="!buscando"></i>
                        <i class="fas fa-spinner fa-spin absolute left-4 top-1/2 -translate-y-1/2 text-blue-500" x-show="buscando" style="display:none;"></i>
                        <input x-model="busqueda" type="search" placeholder="Escribe producto, variante o SKU..."
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3.5 pl-11 pr-4 text-sm focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner">
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3 mt-4 max-h-60 overflow-y-auto pr-2">
                        <template x-for="variante in variantesFiltradas" :key="variante.id">
                            <button type="button" @click="seleccionar(variante)" :disabled="variante.reserva <= 0"
                                class="text-left rounded-xl border border-slate-200 bg-white hover:border-amber-400 hover:shadow-md px-4 py-3 transition-all group disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:border-slate-200 disabled:hover:shadow-none">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="mb-2 h-12 w-12 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                            <template x-if="variante.imagen"><img :src="variante.imagen" :alt="variante.variante" class="h-full w-full object-cover"></template>
                                            <template x-if="!variante.imagen">
                                                <div class="flex h-full items-center justify-center text-slate-300"><i class="fas fa-image"></i></div>
                                            </template>
                                        </div>
                                        <p class="text-sm font-bold text-slate-800 truncate group-hover:text-amber-700" x-text="variante.producto"></p>
                                        <p class="text-xs text-slate-500 truncate mt-0.5" x-text="variante.variante"></p>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-2" x-text="variante.sku"></p>
                                    </div>
                                    <span class="text-xs font-bold px-2 py-1 rounded-lg flex-shrink-0"
                                        :class="variante.reserva > 0 ? 'text-amber-700 bg-amber-50' : 'text-slate-500 bg-slate-100'"
                                        x-text="variante.reserva > 0 ? variante.reserva + ' en bodega' : 'Sin existencia'"></span>
                                </div>
                            </button>
                        </template>
                        <p x-show="variantesFiltradas.length === 0 && !buscando" class="sm:col-span-2 text-sm font-medium text-slate-400 py-6 text-center" style="display:none;">
                            No hay variantes que coincidan con la búsqueda.
                        </p>
                    </div>
                </div>
            </template>

            <template x-if="varianteSeleccionada">
                <form @submit.prevent="Swal.fire({ title: '¿Estás seguro?', text: 'Verifica que las cantidades a transferir sean correctas.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Sí, transferir', cancelButtonText: 'Cancelar' }).then((result) => { if (result.isConfirmed) { enviar(); } })" class="space-y-4">
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800" x-text="varianteSeleccionada.producto"></p>
                            <p class="text-xs text-slate-500" x-text="varianteSeleccionada.variante + ' · ' + varianteSeleccionada.sku"></p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded-lg"
                                x-text="varianteSeleccionada.reserva + ' disponibles'"></span>
                            <button type="button" @click="limpiarSeleccion" class="text-slate-400 hover:text-red-600" title="Cambiar variante">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-[160px_minmax(0,1fr)_auto] md:items-end">
                        <label>
                            <span class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">Cantidad</span>
                            <input x-model.number="cantidad" type="number" min="1" :max="varianteSeleccionada.reserva" required
                                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                        </label>
                        <label>
                            <span class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">Observación <span class="font-normal normal-case text-slate-400">(opcional)</span></span>
                            <input x-model="observacion" type="text" maxlength="1000" placeholder="Ej. Reposición de vitrina"
                                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                        </label>
                        <button type="submit" :disabled="enviando || cantidad > varianteSeleccionada.reserva || cantidad < 1"
                            class="relative inline-flex items-center justify-center rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-black text-white shadow-lg shadow-amber-500/20 hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-60 transition-all">
                            <span class="inline-flex items-center gap-2" :class="{ 'invisible': enviando }">
                                <i class="fas fa-arrow-right"></i>
                                <span>Transferir</span>
                            </span>
                            <span class="absolute inset-0 flex items-center justify-center gap-2" x-show="enviando" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i>
                                <span>Procesando...</span>
                            </span>
                        </button>
                    </div>
                    <p x-show="cantidad > varianteSeleccionada.reserva" class="text-xs font-bold text-red-600" style="display:none">
                        No hay suficiente reserva en bodega para esa cantidad.
                    </p>
                </form>
            </template>
        </section>

        <section x-show="inventarioTab === 'kardex'" x-cloak class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/60 p-5">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Movimientos registrados</h2>
                    <p class="mt-1 text-xs text-slate-500">Kardex físico de existencias por variante.</p>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-clock-rotate-left text-xl text-amber-500"></i>
                </div>
            </div>
                <!-- Skeleton Table idéntica en dimensiones al contenido real -->
                <div x-show="cargandoKardex" class="p-5">
                    <x-skeleton-table :rows="8" :cols="6" :avatarCol="2" />
                </div>

                <!-- Contenedor real de la tabla -->
                <div id="contenedorTablaKardex" x-show="!cargandoKardex">
                    @include('compras.tabla-movimientos')

                </div>
        </section>

        <!-- MODAL ÚNICO DE DETALLE DE MOVIMIENTO CON SKELETON INTELIGENTE -->
        <div x-show="modalMovimientoAbierto" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <div class="relative w-full max-w-xl rounded-2xl border border-slate-200 bg-white shadow-2xl overflow-hidden"
                @click.away="cerrarDetalleMovimiento()">

                <!-- Encabezado del modal -->
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 p-5">
                    <div>
                        <span class="text-xs font-black uppercase tracking-widest text-blue-600">Detalle de Kardex</span>
                        <template x-if="movimientoSeleccionado">
                            <div>
                                <h3 class="mt-1 text-lg font-black text-slate-900" x-text="movimientoSeleccionado.producto"></h3>
                                <p class="text-xs text-slate-500" x-text="movimientoSeleccionado.variante + ' · ' + movimientoSeleccionado.fecha"></p>
                            </div>
                        </template>
                        <template x-if="!movimientoSeleccionado">
                            <div class="mt-2 space-y-2">
                                <div class="h-5 w-48 rounded-md ax-skeleton-shimmer"></div>
                                <div class="h-3.5 w-32 rounded ax-skeleton-shimmer"></div>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="cerrarDetalleMovimiento()" class="h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer" title="Cerrar">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                <!-- Skeleton de Carga del Modal -->
                <div x-show="cargandoDetalleMovimiento" class="p-6 space-y-5">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 space-y-2">
                            <div class="h-3 w-12 rounded ax-skeleton-shimmer"></div>
                            <div class="h-4 w-16 rounded ax-skeleton-shimmer"></div>
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 space-y-2">
                            <div class="h-3 w-14 rounded ax-skeleton-shimmer"></div>
                            <div class="h-4 w-10 rounded ax-skeleton-shimmer"></div>
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 space-y-2">
                            <div class="h-3 w-10 rounded ax-skeleton-shimmer"></div>
                            <div class="h-4 w-16 rounded ax-skeleton-shimmer"></div>
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 space-y-2">
                            <div class="h-3 w-12 rounded ax-skeleton-shimmer"></div>
                            <div class="h-4 w-16 rounded ax-skeleton-shimmer"></div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-slate-100 overflow-hidden space-y-2 p-4">
                        <div class="h-4 w-36 rounded ax-skeleton-shimmer mb-3"></div>
                        <div class="h-8 w-full rounded ax-skeleton-shimmer"></div>
                        <div class="h-8 w-full rounded ax-skeleton-shimmer"></div>
                        <div class="h-8 w-full rounded ax-skeleton-shimmer"></div>
                    </div>
                </div>

                <!-- Contenido Real del Movimiento -->
                <template x-if="!cargandoDetalleMovimiento && movimientoSeleccionado">
                    <div class="p-5 space-y-5">
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tipo</p>
                                <p class="mt-1 text-xs font-black text-slate-800" x-text="movimientoSeleccionado.tipo"></p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Cantidad</p>
                                <p class="mt-1 text-sm font-black text-amber-700" x-text="movimientoSeleccionado.cantidad"></p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">SKU</p>
                                <p class="mt-1 truncate text-xs font-black text-slate-800" x-text="movimientoSeleccionado.sku"></p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Origen</p>
                                <p class="mt-1 text-xs font-black text-slate-800" x-text="movimientoSeleccionado.origen"></p>
                            </div>
                        </div>

                        <div class="overflow-hidden rounded-xl border border-slate-200">
                            <div class="grid grid-cols-3 border-b border-slate-200 bg-slate-50 px-4 py-2.5 text-[10px] font-black uppercase tracking-wider text-slate-500">
                                <span>Ubicación</span>
                                <span class="text-center">Antes</span>
                                <span class="text-center">Después</span>
                            </div>
                            <div class="grid grid-cols-3 border-b border-slate-100 px-4 py-2.5 text-xs">
                                <span class="font-bold text-slate-700">Reserva</span>
                                <span class="text-center text-slate-600" x-text="movimientoSeleccionado.reserva_anterior"></span>
                                <span class="text-center font-bold text-slate-800" x-text="movimientoSeleccionado.reserva_nueva"></span>
                            </div>
                            <div class="grid grid-cols-3 border-b border-slate-100 px-4 py-2.5 text-xs">
                                <span class="font-bold text-slate-700">Tienda</span>
                                <span class="text-center text-slate-600" x-text="movimientoSeleccionado.stock_anterior"></span>
                                <span class="text-center font-bold text-slate-800" x-text="movimientoSeleccionado.stock_nuevo"></span>
                            </div>
                            <div class="grid grid-cols-3 px-4 py-2.5 text-xs">
                                <span class="font-bold text-slate-700">Cuarentena</span>
                                <span class="text-center text-slate-600" x-text="movimientoSeleccionado.stock_cuarentena_anterior"></span>
                                <span class="text-center font-bold text-slate-800" x-text="movimientoSeleccionado.stock_cuarentena_nuevo"></span>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Observación</p>
                            <p class="mt-1 text-xs text-slate-600" x-text="movimientoSeleccionado.observacion"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function gestionMovimientosKardex() {
            return {
                inventarioTab: @js($filtroMovimiento !== 'todos' ? 'kardex' : 'transferir'),
                filtroMovimiento: @js($filtroMovimiento),
                cargandoKardex: false,

                // Modal detalle
                modalMovimientoAbierto: false,
                cargandoDetalleMovimiento: false,
                movimientoSeleccionado: null,

                async abrirDetalleMovimiento(id) {
                    this.modalMovimientoAbierto = true;
                    this.cargandoDetalleMovimiento = true;
                    this.movimientoSeleccionado = null;

                    try {
                        const res = await fetch(`{{ url('compras/movimientos') }}/${id}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();
                        if (data.success && data.movimiento) {
                            this.movimientoSeleccionado = data.movimiento;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo cargar la información del movimiento.'
                            });
                            this.modalMovimientoAbierto = false;
                        }
                    } catch (err) {
                        console.error('Error al cargar detalle movimiento:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de red',
                            text: 'No fue posible conectar con el servidor.'
                        });
                        this.modalMovimientoAbierto = false;
                    } finally {
                        this.cargandoDetalleMovimiento = false;
                    }
                },

                cerrarDetalleMovimiento() {
                    this.modalMovimientoAbierto = false;
                    this.movimientoSeleccionado = null;
                },

                async filtrarKardex(tipo) {
                    this.filtroMovimiento = tipo;
                    this.cargandoKardex = true;
                    const url = `{{ route('compras.movimientos') }}?tipo=${tipo}`;
                    window.history.pushState({}, '', url);

                    try {
                        const res = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();
                        if (data.success && data.html) {
                            const contenedor = document.getElementById('contenedorTablaKardex');
                            if (contenedor) {
                                contenedor.innerHTML = data.html;
                                this.atarPaginacionKardex();
                            }
                        }
                    } catch (err) {
                        console.error('Error al filtrar kardex:', err);
                    } finally {
                        this.cargandoKardex = false;
                    }
                },

                atarPaginacionKardex() {
                    const contenedor = document.getElementById('contenedorTablaKardex');
                    if (!contenedor) return;
                    const links = contenedor.querySelectorAll('a[href*="page="]');
                    links.forEach(link => {
                        link.addEventListener('click', async (e) => {
                            e.preventDefault();
                            const href = link.getAttribute('href');
                            if (!href) return;
                            this.cargandoKardex = true;
                            window.history.pushState({}, '', href);
                            try {
                                const res = await fetch(href, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                                const data = await res.json();
                                if (data.success && data.html) {
                                    contenedor.innerHTML = data.html;
                                    this.atarPaginacionKardex();
                                }
                            } catch (err) {
                                console.error('Error en paginación kardex:', err);
                            } finally {
                                this.cargandoKardex = false;
                            }
                        });
                    });
                },

                init() {
                    this.atarPaginacionKardex();
                }
            };
        }
    </script>
</x-app>
