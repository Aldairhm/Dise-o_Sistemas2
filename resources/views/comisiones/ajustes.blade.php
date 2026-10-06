<x-app title="Ajustes de Comisiones | AXStore">
    <div class="max-w-[1440px] mx-auto space-y-6">
        <div class="relative overflow-hidden rounded-3xl border border-amber-100 bg-gradient-to-br from-white via-amber-50/70 to-orange-50/80 p-5 shadow-sm sm:p-7">
            <div class="pointer-events-none absolute -right-8 -top-14 h-48 w-48 rounded-full bg-amber-200/20 blur-3xl"></div>
            <a href="{{ route('home') }}" class="relative inline-flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-widest text-slate-500 transition-colors hover:text-blue-600">
                <i class="fas fa-arrow-left"></i> Volver al inicio
            </a>
            <div class="relative mt-4 flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-lg shadow-amber-500/25 ring-4 ring-white/80">
                        <i class="fas fa-arrows-rotate text-xl"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-[10px] font-black uppercase tracking-[0.2em] text-amber-700">Historial y motivos</p>
                        <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">Ajustes de comisiones</h1>
                        <p class="mt-1 text-xs text-slate-600 sm:text-sm">Consulta descuentos, devoluciones y movimientos que afectan los pagos.</p>
                    </div>
                </div>
                <div class="flex w-fit items-center gap-2 rounded-2xl border border-white bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-700"><i class="fas fa-list-check"></i></span>
                    <div>
                        <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Registros</span>
                        <span class="block text-lg font-black leading-tight text-slate-900">{{ $ajustes->total() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200/80 bg-white p-1.5 shadow-md shadow-slate-200/40" aria-label="Secciones de comisiones">
            <a href="{{ route('comisiones.porSemana') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-calendar-week text-slate-400"></i><span>Comisiones por Semana</span>
            </a>
            <a href="{{ route('comisiones.porVendedor') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-users-gear text-slate-400"></i><span>Comisiones por Vendedor</span>
            </a>
            <a href="{{ route('comisiones.index') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-coins text-slate-400"></i><span>Comisiones</span>
            </a>
            <a href="{{ route('comisiones.ajustes') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm flex items-center gap-2 transition-all">
                <i class="fas fa-arrows-rotate"></i><span>Ajustes</span>
            </a>
        </nav>

        <form method="GET" action="{{ route('comisiones.ajustes') }}" class="grid grid-cols-1 items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-12 lg:p-5">
            @if($isAdmin)
                <div class="lg:col-span-4">
                    <label for="vendedor_id" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-slate-500"><i class="fas fa-user-tie mr-1 text-blue-500"></i>Vendedor</label>
                    <select id="vendedor_id" name="vendedor_id" data-searchable-vendedor class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-700 transition focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                        <option value="">Todos los vendedores</option>
                        @foreach($vendedores as $vendedor)
                            <option value="{{ $vendedor->id }}" {{ request('vendedor_id') == $vendedor->id ? 'selected' : '' }}>
                                {{ $vendedor->nombre_real ?: $vendedor->username }}{{ $vendedor->nombre_real ? ' (' . $vendedor->username . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="{{ $isAdmin ? 'lg:col-span-3' : 'lg:col-span-5' }}">
                <label for="fecha_desde" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-slate-500"><i class="fas fa-calendar-day mr-1 text-blue-500"></i>Desde</label>
                <input id="fecha_desde" name="fecha_desde" type="date" value="{{ request('fecha_desde') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-700 transition focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10">
            </div>
            <div class="{{ $isAdmin ? 'lg:col-span-3' : 'lg:col-span-5' }}">
                <label for="fecha_hasta" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-slate-500"><i class="fas fa-calendar-check mr-1 text-blue-500"></i>Hasta</label>
                <input id="fecha_hasta" name="fecha_hasta" type="date" value="{{ request('fecha_hasta') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-700 transition focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10">
            </div>
            <div class="lg:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-3 py-2.5 text-xs font-extrabold text-white shadow-md shadow-blue-600/20 transition hover:-translate-y-0.5 hover:shadow-lg">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
                @if(request()->hasAny(['vendedor_id', 'fecha_desde', 'fecha_hasta']))
                    <a href="{{ route('comisiones.ajustes') }}" class="flex items-center justify-center rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600" title="Limpiar filtros">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>

        <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-blue-50/50 p-5 shadow-sm sm:p-6" aria-labelledby="reglas-ajustes-titulo">
            <div class="mb-5 flex items-start gap-3">
                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700"><i class="fas fa-circle-info"></i></span>
                <div>
                    <h2 id="reglas-ajustes-titulo" class="text-base font-black text-slate-900">Cómo se calculan los ajustes</h2>
                    <p class="mt-1 text-xs text-slate-500">Reglas automáticas aplicadas a descuentos, devoluciones y liquidaciones.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="group rounded-2xl border border-amber-100 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                    <span class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-700 transition group-hover:scale-110"><i class="fas fa-tag"></i></span>
                    <h3 class="text-xs font-black text-slate-900">Descuento de venta</h3>
                    <p class="mt-1.5 text-xs leading-relaxed text-slate-600">Se distribuye proporcionalmente entre las comisiones de las líneas. Si supera una comisión, el saldo puede quedar negativo.</p>
                </div>
                <div class="group rounded-2xl border border-indigo-100 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                    <span class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 transition group-hover:scale-110"><i class="fas fa-rotate-left"></i></span>
                    <h3 class="text-xs font-black text-slate-900">Devolución en tienda / POS</h3>
                    <p class="mt-1.5 text-xs leading-relaxed text-slate-600">Las comisiones de esa venta quedan en $0, aunque antes fueran positivas o negativas. El saldo anterior queda anotado.</p>
                </div>
                <div class="group rounded-2xl border border-rose-100 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                    <span class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-rose-100 text-rose-700 transition group-hover:scale-110"><i class="fas fa-truck-fast"></i></span>
                    <h3 class="text-xs font-black text-slate-900">Devolución de envío</h3>
                    <p class="mt-1.5 text-xs leading-relaxed text-slate-600">Se crea un ajuste negativo equivalente al 50 % del costo de envío y se asocia a la venta.</p>
                </div>
                <div class="group rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                    <span class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 transition group-hover:scale-110"><i class="fas fa-money-bill-transfer"></i></span>
                    <h3 class="text-xs font-black text-slate-900">Al liquidar</h3>
                    <p class="mt-1.5 text-xs leading-relaxed text-slate-600">Los ajustes negativos se restan del pago y no se pueden excluir. Si el neto es $0 o menos, no se emite pago.</p>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/40">
            <div class="flex flex-col justify-between gap-3 border-b border-slate-100 bg-gradient-to-r from-white to-slate-50/70 px-5 py-5 sm:flex-row sm:items-center sm:px-6">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-md"><i class="fas fa-clock-rotate-left"></i></span>
                    <div>
                        <h2 class="text-base font-black text-slate-900">Registro de ajustes</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Devoluciones, cambios, descuentos y otras cancelaciones</p>
                    </div>
                </div>
                <span class="inline-flex w-fit items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-3 py-2 text-xs font-extrabold text-blue-700">
                    <i class="fas fa-layer-group"></i>{{ $ajustes->total() }} registros encontrados
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-xs">
                    <thead class="border-b border-slate-200 bg-slate-50/90 text-[10px] font-black uppercase tracking-wider text-slate-500">
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
                                $motivoLegible = $motivo;
                                $montoAjuste = (float) $ajuste->monto;
                                $notasAjuste = (string) $ajuste->notas;
                                if (preg_match('/Descuento de venta aplicado a comisión:\s*\$([\d,]+(?:\.\d{1,2})?);\s*comisión bruta:\s*\$([\d,]+(?:\.\d{1,2})?)/iu', $notasAjuste, $descuentoComision)) {
                                    $montoDescuento = (float) str_replace(',', '', $descuentoComision[1]);
                                    $comisionBruta = (float) str_replace(',', '', $descuentoComision[2]);
                                    $montoAjuste = -$montoDescuento;
                                    $motivoLegible = 'Se descontaron $' . number_format($montoDescuento, 2)
                                        . ' de la comisión de esta venta. Antes del descuento, la comisión era de $'
                                        . number_format($comisionBruta, 2) . '.';
                                } elseif (preg_match('/Deducción por devolución de envío:\s*-\$([\d,]+(?:\.\d{1,2})?)\s*\(50\s*%\s*del costo de envío:\s*\$([\d,]+(?:\.\d{1,2})?)\)/iu', $notasAjuste, $deduccionEnvio)) {
                                    $montoDeduccion = (float) str_replace(',', '', $deduccionEnvio[1]);
                                    $costoEnvio = (float) str_replace(',', '', $deduccionEnvio[2]);
                                    $motivoLegible = 'Por la devolución, se descontaron $' . number_format($montoDeduccion, 2)
                                        . ' de la comisión, equivalentes al 50 % del envío ($' . number_format($costoEnvio, 2) . ').';
                                } elseif (preg_match('/Descuento descontado:\s*\$([\d,]+(?:\.\d{1,2})?)/iu', $notasAjuste, $descuentoCancelacion)) {
                                    $montoDescuento = (float) str_replace(',', '', $descuentoCancelacion[1]);
                                    $montoAjuste = -$montoDescuento;
                                    $motivoLegible = 'Al cancelar la comisión, se descontaron $' . number_format($montoDescuento, 2) . ' por el descuento aplicado a la venta.';
                                } elseif (preg_match('/Ajuste POS por devolución: saldo anterior (.+?) -> \$0\.00\./iu', $notasAjuste, $ajustePos)) {
                                    $motivoLegible = 'La comisión se anuló por la devolución en tienda. Saldo anterior: '
                                        . trim($ajustePos[1]) . '; saldo final: $0.00.';
                                }

                                if (preg_match('/Cancelada por (Devolución|Cambio|Cancelada|anulación de salida|el administrador)\.?/iu', $notasAjuste, $cancelacion)) {
                                    $razonCancelacion = match (\Illuminate\Support\Str::lower($cancelacion[1])) {
                                        'devolución' => 'la venta fue devuelta',
                                        'cambio' => 'se realizó un cambio',
                                        'cancelada' => 'la venta fue cancelada',
                                        'anulación de salida' => 'se anuló la salida',
                                        'el administrador' => 'fue cancelada por un administrador',
                                        default => \Illuminate\Support\Str::lower($cancelacion[1]),
                                    };
                                    $motivoLegible .= ' La comisión quedó cancelada porque ' . $razonCancelacion . '.';
                                }
                                $tipos = [];
                                if (str_contains($motivoBusqueda, 'devolución')) $tipos[] = 'Devolución';
                                if (str_contains($motivoBusqueda, 'cambio')) $tipos[] = 'Cambio';
                                if (str_contains($motivoBusqueda, 'descuento')) $tipos[] = 'Descuento';
                                if (str_contains($motivoBusqueda, 'envío')) $tipos[] = 'Envío';
                                if (empty($tipos)) $tipos[] = 'Cancelación';

                                $coloresTipo = [
                                    'Devolución' => 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-200',
                                    'Cambio' => 'bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-200',
                                    'Descuento' => 'bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-200',
                                    'Envío' => 'bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-200',
                                    'Cancelación' => 'bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200',
                                ];
                                $colorEstado = match ($ajuste->estado) {
                                    'Pendiente' => 'border-amber-200 bg-amber-50 text-amber-700',
                                    'Pagada' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                    'Cancelada' => 'border-slate-200 bg-slate-100 text-slate-600',
                                    default => 'border-slate-200 bg-slate-100 text-slate-600',
                                };

                                $referencia = $ajuste->concepto ?? '';
                                if (preg_match('/VNT-(\d+)/i', $referencia, $folio)) {
                                    $referenciaVenta = 'VNT-' . str_pad($folio[1], 5, '0', STR_PAD_LEFT);
                                } elseif (preg_match('/Venta #(\d+)/i', $referencia, $ventaId)) {
                                    $referenciaVenta = 'VNT-' . str_pad($ventaId[1], 5, '0', STR_PAD_LEFT);
                                } else {
                                    $referenciaVenta = $ajuste->id_salida ? 'Salida #' . $ajuste->id_salida : 'Ajuste manual';
                                }
                            @endphp
                            <tr class="align-top transition-colors hover:bg-blue-50/40">
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                    <span class="block font-extrabold text-slate-800">{{ $ajuste->fecha_registro?->format('d/m/Y') ?? '—' }}</span>
                                    <span class="mt-0.5 inline-flex items-center gap-1 text-[10px] text-slate-400"><i class="far fa-clock"></i>{{ $ajuste->fecha_registro?->format('h:i A') }}</span>
                                </td>
                                @if($isAdmin)
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-blue-100 text-[10px] font-black text-blue-700">{{ strtoupper(substr($ajuste->vendedor?->nombre_real ?? $ajuste->vendedor?->username ?? '?', 0, 2)) }}</span>
                                            <span class="font-bold text-slate-700">{{ $ajuste->vendedor?->nombre_real ?? $ajuste->vendedor?->username ?? '—' }}</span>
                                        </div>
                                    </td>
                                @endif
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($tipos as $tipo)
                                            <span class="rounded-md px-2 py-0.5 text-[10px] font-bold {{ $coloresTipo[$tipo] ?? $coloresTipo['Cancelación'] }}">{{ $tipo }}</span>
                                        @endforeach
                                    </div>
                                    <span class="mt-1.5 inline-flex items-center gap-1 rounded-md bg-slate-100 px-1.5 py-0.5 font-mono text-[10px] font-bold text-slate-700"><i class="fas fa-receipt text-slate-400"></i>{{ $referenciaVenta }}</span>
                                    @if($ajuste->salida?->variante?->producto)
                                        <span class="block text-[10px] text-slate-500">{{ $ajuste->salida->variante->producto->nombre }} {{ $ajuste->salida->variante->nombre_variante ? '- ' . $ajuste->salida->variante->nombre_variante : '' }}</span>
                                    @endif
                                </td>
                                <td class="max-w-xl whitespace-normal px-4 py-3 leading-relaxed text-slate-600">{{ $motivoLegible }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <span class="inline-flex rounded-lg px-2 py-1 font-black {{ $montoAjuste < 0 ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-900' }}">
                                        {{ $montoAjuste < 0 ? '-$' . number_format(abs($montoAjuste), 2) : '$' . number_format($montoAjuste, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex rounded-full border px-2 py-0.5 text-[10px] font-bold {{ $colorEstado }}">{{ $ajuste->estado }}</span>
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
    @if($isAdmin)
        @include('comisiones.partials.searchable-vendedor-select')
    @endif
</x-app>