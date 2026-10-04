<x-app title="Ajustes de Comisiones | AXStore">
    <div class="max-w-[1440px] mx-auto space-y-6">
        <div class="flex flex-col gap-2">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors">
                <i class="fas fa-arrow-left"></i> Volver al inicio
            </a>
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center">
                    <i class="fas fa-arrows-rotate text-xl"></i>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-amber-700 mb-0.5">Historial y motivos</p>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Ajustes de comisiones</h1>
                </div>
            </div>
        </div>

        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Secciones de comisiones">
            <a href="{{ route('comisiones.porVendedor') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-users-gear text-slate-400"></i><span>Comisiones por Vendedor</span>
            </a>
            <a href="{{ route('comisiones.index') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-coins text-slate-400"></i><span>Comisiones</span>
            </a>
            <a href="{{ route('comisiones.ajustes') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-arrows-rotate"></i><span>Ajustes</span>
            </a>
            <a href="{{ route('comisiones.porSemana') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-calendar-week text-slate-400"></i><span>Comisiones por Semana</span>
            </a>
        </nav>

        <form method="GET" action="{{ route('comisiones.ajustes') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end rounded-xl border border-slate-200 bg-white p-4">
            @if($isAdmin)
                <div class="lg:col-span-4">
                    <label for="vendedor_id" class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Vendedor</label>
                    <select id="vendedor_id" name="vendedor_id" class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700">
                        <option value="">Todos los vendedores</option>
                        @foreach($vendedores as $vendedor)
                            <option value="{{ $vendedor->id }}" {{ request('vendedor_id') == $vendedor->id ? 'selected' : '' }}>
                                {{ $vendedor->nombre_real }} ({{ $vendedor->username }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="{{ $isAdmin ? 'lg:col-span-3' : 'lg:col-span-5' }}">
                <label for="fecha_desde" class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Desde</label>
                <input id="fecha_desde" name="fecha_desde" type="date" value="{{ request('fecha_desde') }}" class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700">
            </div>
            <div class="{{ $isAdmin ? 'lg:col-span-3' : 'lg:col-span-5' }}">
                <label for="fecha_hasta" class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Hasta</label>
                <input id="fecha_hasta" name="fecha_hasta" type="date" value="{{ request('fecha_hasta') }}" class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700">
            </div>
            <div class="lg:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-xs font-bold text-white hover:bg-blue-700">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
                @if(request()->hasAny(['vendedor_id', 'fecha_desde', 'fecha_hasta']))
                    <a href="{{ route('comisiones.ajustes') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50" title="Limpiar filtros">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>

        <section class="border-y border-slate-200 bg-slate-50/70 px-4 py-5 sm:px-5" aria-labelledby="reglas-ajustes-titulo">
            <div class="mb-4">
                <h2 id="reglas-ajustes-titulo" class="text-sm font-black text-slate-900">Resumen de ajustes a comisiones</h2>
                <p class="mt-1 text-xs text-slate-500">Reglas aplicadas automáticamente al registrar descuentos, devoluciones y liquidaciones.</p>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="border-l-2 border-amber-400 pl-3">
                    <h3 class="text-xs font-extrabold text-slate-800">Descuento de venta</h3>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">Se distribuye proporcionalmente entre las comisiones de las líneas. Si supera una comisión, el saldo puede quedar negativo.</p>
                </div>
                <div class="border-l-2 border-indigo-400 pl-3">
                    <h3 class="text-xs font-extrabold text-slate-800">Devolución en tienda / POS</h3>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">Las comisiones de esa venta quedan en $0, aunque antes fueran positivas o negativas. El saldo anterior queda anotado.</p>
                </div>
                <div class="border-l-2 border-rose-400 pl-3">
                    <h3 class="text-xs font-extrabold text-slate-800">Devolución de envío</h3>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">Se crea un ajuste negativo equivalente al 50 % del costo de envío y se asocia a la venta.</p>
                </div>
                <div class="border-l-2 border-emerald-400 pl-3">
                    <h3 class="text-xs font-extrabold text-slate-800">Al liquidar</h3>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">Los ajustes negativos se restan del pago y no se pueden excluir. Si el neto es $0 o menos, no se emite pago.</p>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-black text-slate-900">Registro de ajustes</h2>
                    <p class="text-xs text-slate-500">Devoluciones, cambios, descuentos y otras cancelaciones</p>
                </div>
                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ $ajustes->total() }} registros</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left text-xs">
                    <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            @if($isAdmin)<th class="px-4 py-3">Vendedor</th>@endif
                            <th class="px-4 py-3">Tipo / referencia</th>
                            <th class="px-4 py-3">Motivo registrado</th>
                            <th class="px-4 py-3 text-right">Monto</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($ajustes as $ajuste)
                            @php
                                $motivo = $ajuste->notas ?: $ajuste->concepto ?: 'Sin motivo registrado';
                                $motivoBusqueda = \Illuminate\Support\Str::lower($motivo . ' ' . ($ajuste->concepto ?? ''));
                                $tipos = [];
                                if (str_contains($motivoBusqueda, 'devolución')) $tipos[] = 'Devolución';
                                if (str_contains($motivoBusqueda, 'cambio')) $tipos[] = 'Cambio';
                                if (str_contains($motivoBusqueda, 'descuento')) $tipos[] = 'Descuento';
                                if (str_contains($motivoBusqueda, 'envío')) $tipos[] = 'Envío';
                                if (empty($tipos)) $tipos[] = 'Cancelación';

                                $referencia = $ajuste->concepto ?? '';
                                if (preg_match('/VNT-(\d+)/i', $referencia, $folio)) {
                                    $referenciaVenta = 'VNT-' . str_pad($folio[1], 5, '0', STR_PAD_LEFT);
                                } elseif (preg_match('/Venta #(\d+)/i', $referencia, $ventaId)) {
                                    $referenciaVenta = 'VNT-' . str_pad($ventaId[1], 5, '0', STR_PAD_LEFT);
                                } else {
                                    $referenciaVenta = $ajuste->id_salida ? 'Salida #' . $ajuste->id_salida : 'Ajuste manual';
                                }
                            @endphp
                            <tr class="align-top hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                    <span class="block font-bold text-slate-800">{{ $ajuste->fecha_registro?->format('d/m/Y') ?? '—' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $ajuste->fecha_registro?->format('h:i A') }}</span>
                                </td>
                                @if($isAdmin)
                                    <td class="px-4 py-3 font-semibold text-slate-700">{{ $ajuste->vendedor?->nombre_real ?? $ajuste->vendedor?->username ?? '—' }}</td>
                                @endif
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($tipos as $tipo)
                                            <span class="rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-800">{{ $tipo }}</span>
                                        @endforeach
                                    </div>
                                    <span class="mt-1 block font-mono font-bold text-slate-700">{{ $referenciaVenta }}</span>
                                    @if($ajuste->salida?->variante?->producto)
                                        <span class="block text-[10px] text-slate-500">{{ $ajuste->salida->variante->producto->nombre }} {{ $ajuste->salida->variante->nombre_variante ? '- ' . $ajuste->salida->variante->nombre_variante : '' }}</span>
                                    @endif
                                </td>
                                <td class="max-w-xl whitespace-normal px-4 py-3 leading-relaxed text-slate-600">{{ $motivo }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right font-black {{ (float) $ajuste->monto < 0 ? 'text-rose-700' : 'text-slate-900' }}">
                                    {{ (float) $ajuste->monto < 0 ? '-$' . number_format(abs((float) $ajuste->monto), 2) : '$' . number_format((float) $ajuste->monto, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex rounded-full border border-slate-200 bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600">{{ $ajuste->estado }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 6 : 5 }}" class="px-5 py-12 text-center text-slate-500">
                                    <i class="fas fa-clipboard-check mb-2 block text-2xl text-slate-300"></i>
                                    <span class="font-semibold">No hay ajustes de comisión en el periodo seleccionado.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($ajustes->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $ajustes->links() }}</div>
            @endif
        </section>
    </div>
</x-app>