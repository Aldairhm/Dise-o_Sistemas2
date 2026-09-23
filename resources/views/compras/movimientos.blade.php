<x-app title="Movimientos de bodega | AXStore">
    <meta name="transferencia-tienda-url" content="{{ route('movimientos-bodega.transferencia-tienda') }}">

    <div class="max-w-[1440px] mx-auto space-y-6">
        <div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 mb-4"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
            <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600 mb-1">Inventario</p>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Movimientos de bodega</h1>
            <p class="text-sm text-slate-500 mt-2">Transfiere unidades recibidas hacia el stock disponible de tienda.</p>
        </div>

        <nav class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" aria-label="Secciones de compras">
            <a href="{{ route('compras.create') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-cart-plus mr-2"></i>Nueva compra</a>
            <a href="{{ route('compras.historial') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-blue-600"><i class="fas fa-clock-rotate-left mr-2"></i>Historial</a>
            <a href="{{ route('compras.movimientos') }}" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm"><i class="fas fa-warehouse mr-2"></i>Bodega a tienda</a>
        </nav>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
                 x-data="transferenciaBodega({{ Js::from(
                     $variantes->map(fn ($v) => [
                         'id' => $v->id,
                         'producto' => $v->producto?->nombre ?? 'Producto',
                         'variante' => $v->nombre_variante,
                         'sku' => $v->sku,
                         'reserva' => $v->reserva,
                         'imagen' => ($imagen = $v->imagenes->firstWhere('es_principal', 1) ?? $v->imagenes->first())
                             ? asset('storage/' . $imagen->ruta_imagen)
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
                                            <template x-if="!variante.imagen"><div class="flex h-full items-center justify-center text-slate-300"><i class="fas fa-image"></i></div></template>
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
                <form @submit.prevent="enviar" class="space-y-4">
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
                                class="rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-black text-white shadow-lg shadow-amber-500/20 hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-60">
                            <i class="fas fa-arrow-right mr-2"></i><span x-text="enviando ? 'Procesando...' : 'Transferir'"></span>
                        </button>
                    </div>
                    <p x-show="cantidad > varianteSeleccionada.reserva" class="text-xs font-bold text-red-600" style="display:none">
                        No hay suficiente reserva en bodega para esa cantidad.
                    </p>
                </form>
            </template>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/60 p-5">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Transferencias realizadas</h2>
                    <p class="mt-1 text-xs text-slate-500">Historial de unidades enviadas desde bodega hacia tienda.</p>
                </div>
                <i class="fas fa-clock-rotate-left text-xl text-amber-500"></i>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-[10px] uppercase tracking-widest text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Fecha</th>
                            <th class="px-5 py-4">Variante</th>
                            <th class="px-5 py-4 text-center">Cantidad</th>
                            <th class="px-5 py-4 text-center">Bodega</th>
                            <th class="px-5 py-4 text-center">Tienda</th>
                            <th class="px-5 py-4">Observación</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($movimientos as $movimiento)
                            @php $imagenMovimiento = $movimiento->variante?->imagenes?->firstWhere('es_principal', 1) ?? $movimiento->variante?->imagenes?->first(); @endphp
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-5 py-4 whitespace-nowrap text-slate-500">{{ $movimiento->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                            @if ($imagenMovimiento)
                                                <img src="{{ asset('storage/' . $imagenMovimiento->ruta_imagen) }}" alt="{{ $movimiento->variante?->nombre_variante }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full items-center justify-center text-slate-300"><i class="fas fa-image"></i></div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-800">{{ $movimiento->variante?->producto?->nombre ?? 'Producto' }}</p>
                                            <p class="text-xs text-slate-500">{{ $movimiento->variante?->nombre_variante }}</p>
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">SKU: {{ $movimiento->variante?->sku ?: 'Sin SKU' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center font-black text-amber-700">{{ $movimiento->cantidad }}</td>
                                <td class="px-5 py-4 text-center text-slate-600">{{ $movimiento->reserva_anterior }} <i class="fas fa-arrow-right mx-1 text-xs text-slate-400"></i> {{ $movimiento->reserva_nueva }}</td>
                                <td class="px-5 py-4 text-center text-slate-600">{{ $movimiento->stock_anterior }} <i class="fas fa-arrow-right mx-1 text-xs text-slate-400"></i> {{ $movimiento->stock_nuevo }}</td>
                                <td class="px-5 py-4 text-slate-500">{{ $movimiento->observacion ?: 'Sin observación' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-16 text-center text-sm text-slate-400"><i class="fas fa-truck-ramp-box mb-3 block text-2xl"></i>Aún no hay transferencias de bodega a tienda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($movimientos->hasPages())
                <div class="border-t border-slate-100 p-4">{{ $movimientos->links() }}</div>
            @endif
        </section>
    </div>
</x-app>