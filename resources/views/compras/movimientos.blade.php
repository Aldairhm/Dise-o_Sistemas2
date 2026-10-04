<x-app title="Manejo de inventario | AXStore">
    <meta name="transferencia-tienda-url" content="{{ route('movimientos-bodega.transferencia-tienda') }}">

    <div class="max-w-[1440px] mx-auto space-y-6" x-data="{ inventarioTab: @js($filtroMovimiento !== 'todos' ? 'kardex' : 'transferir') }">
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
                    <a href="{{ route('compras.movimientos', ['tipo' => $valor]) }}" 
                       class="inline-flex items-center gap-2 rounded-xl border {{ $filtroMovimiento === $valor ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200' }} px-4 py-2.5 text-xs font-bold shadow-sm transition-all duration-200">
                        <i class="fas {{ $data['icon'] }} {{ $filtroMovimiento === $valor ? 'text-white' : 'text-slate-400' }}"></i> {{ $data['label'] }}
                    </a>
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

        <section x-show="inventarioTab === 'kardex'" x-cloak class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/60 p-5">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Movimientos registrados</h2>
                    <p class="mt-1 text-xs text-slate-500">Kardex físico de existencias por variante.</p>
                </div>
                <i class="fas fa-clock-rotate-left text-xl text-amber-500"></i>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-[10px] uppercase tracking-widest text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Fecha</th>
                            <th class="px-5 py-4">Tipo</th>
                            <th class="px-5 py-4">Producto / variante</th>
                            <th class="px-5 py-4 text-center">Cantidad</th>
                            <th class="px-5 py-4">Origen</th>
                            <th class="px-5 py-4 text-right">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($movimientos as $movimiento)
                            @php
                                $imagenMovimiento = $movimiento->variante?->imagenes?->firstWhere('es_principal', 1) ?? $movimiento->variante?->imagenes?->first();
                                $esDevolucion = $movimiento->devolucion_id || in_array($movimiento->tipo, ['devolucion_cliente', 'devolucion_proveedor']) || str_contains(strtolower($movimiento->observacion ?? ''), 'devolu');
                                $etiquetaTipo = match ($movimiento->tipo) {
                                    'transferencia_tienda' => 'Transferencia',
                                    'compra_recibida' => 'Compra recibida',
                                    'devolucion_cliente' => 'Devolución cliente',
                                    'devolucion_proveedor' => 'Devolución proveedor',
                                    'Entrada', 'entrada' => 'Entrada',
                                    'Salida', 'salida' => 'Salida',
                                    default => $movimiento->tipo,
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500">{{ $movimiento->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex whitespace-nowrap rounded-lg border px-2.5 py-1 text-[11px] font-bold {{ $esDevolucion ? 'border-red-200 bg-red-50 text-red-700' : 'border-slate-200 bg-slate-50 text-slate-700' }}">
                                        {{ $etiquetaTipo }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                            @if ($imagenMovimiento)
                                                <img src="{{ asset('storage/' . $imagenMovimiento->ruta_imagen) }}" alt="{{ $movimiento->variante?->nombre_variante ?? 'Producto' }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full items-center justify-center text-slate-300"><i class="fas fa-image text-xs"></i></div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-800">{{ $movimiento->variante?->producto?->nombre ?? 'Producto' }}</p>
                                            <p class="text-xs text-slate-500">{{ $movimiento->variante?->nombre_variante ?? 'Variante sin nombre' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center font-black text-amber-700">{{ $movimiento->cantidad }}</td>
                                <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                                    @if ($movimiento->devolucion)
                                        <span class="font-bold text-blue-600">DEV-{{ str_pad($movimiento->devolucion_id, 5, '0', STR_PAD_LEFT) }}</span>
                                    @elseif ($movimiento->compra)
                                        <span>Compra #{{ $movimiento->id_compra }}</span>
                                    @else
                                        <span class="text-slate-400">Inventario</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button type="button" onclick="document.getElementById('movimiento-detalle-{{ $movimiento->id }}').showModal()" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-blue-600 shadow-sm hover:bg-blue-50">
                                        <i class="fas fa-eye"></i> Ver detalle
                                    </button>
                                </td>
                            </tr>
                            <dialog id="movimiento-detalle-{{ $movimiento->id }}" class="m-auto w-[min(620px,calc(100vw-2rem))] max-w-none rounded-2xl border border-slate-200 bg-white p-0 text-left shadow-2xl backdrop:bg-slate-900/50">
                                <div class="p-5">
                                    <div class="mb-5 flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                                        <div><p class="text-xs font-black uppercase tracking-widest text-blue-600">Movimiento #{{ $movimiento->id }}</p><h3 class="mt-1 text-lg font-black text-slate-900">{{ $movimiento->variante?->producto?->nombre ?? 'Producto' }}</h3><p class="text-sm text-slate-500">{{ $movimiento->variante?->nombre_variante ?? 'Variante' }} · {{ $movimiento->created_at?->format('d/m/Y H:i') }}</p></div>
                                        <button type="button" onclick="this.closest('dialog').close()" class="h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Cerrar"><i class="fas fa-xmark"></i></button>
                                    </div>
                                    <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                        <div class="rounded-xl bg-slate-50 p-3"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tipo</p><p class="mt-1 text-sm font-black text-slate-800">{{ $etiquetaTipo }}</p></div>
                                        <div class="rounded-xl bg-slate-50 p-3"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Cantidad</p><p class="mt-1 text-sm font-black text-amber-700">{{ $movimiento->cantidad }}</p></div>
                                        <div class="rounded-xl bg-slate-50 p-3"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">SKU</p><p class="mt-1 truncate text-sm font-black text-slate-800">{{ $movimiento->variante?->sku ?: 'Sin SKU' }}</p></div>
                                        <div class="rounded-xl bg-slate-50 p-3"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Origen</p><p class="mt-1 text-sm font-black text-slate-800">{{ $movimiento->devolucion ? 'DEV-' . str_pad($movimiento->devolucion_id, 5, '0', STR_PAD_LEFT) : ($movimiento->compra ? 'Compra #' . $movimiento->id_compra : 'Inventario') }}</p></div>
                                    </div>
                                    <div class="overflow-hidden rounded-xl border border-slate-200"><div class="grid grid-cols-3 border-b border-slate-200 bg-slate-50 px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-500"><span>Ubicación</span><span class="text-center">Antes</span><span class="text-center">Después</span></div><div class="grid grid-cols-3 border-b border-slate-100 px-4 py-3 text-sm"><span class="font-bold text-slate-700">Reserva</span><span class="text-center text-slate-600">{{ $movimiento->reserva_anterior }}</span><span class="text-center font-bold text-slate-800">{{ $movimiento->reserva_nueva }}</span></div><div class="grid grid-cols-3 border-b border-slate-100 px-4 py-3 text-sm"><span class="font-bold text-slate-700">Tienda</span><span class="text-center text-slate-600">{{ $movimiento->stock_anterior }}</span><span class="text-center font-bold text-slate-800">{{ $movimiento->stock_nuevo }}</span></div><div class="grid grid-cols-3 px-4 py-3 text-sm"><span class="font-bold text-slate-700">Cuarentena</span><span class="text-center text-slate-600">{{ $movimiento->stock_cuarentena_anterior ?? 0 }}</span><span class="text-center font-bold text-slate-800">{{ $movimiento->stock_cuarentena_nuevo ?? 0 }}</span></div></div>
                                    <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-4"><p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Observación</p><p class="mt-1 text-sm text-slate-600">{{ $movimiento->observacion ?: 'Sin observación' }}</p></div>
                                </div>
                            </dialog>
                        @empty
                            <tr><td colspan="6" class="px-5 py-16 text-center text-sm text-slate-400"><i class="fas fa-boxes-stacked mb-3 block text-2xl"></i>No hay movimientos para el filtro seleccionado.</td></tr>
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