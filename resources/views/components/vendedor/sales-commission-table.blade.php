@props([
    'ventas' => collect(),
])

<section class="space-y-4">
    {{-- Barra superior de Filtros y Búsqueda --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-black tracking-tight text-slate-900">Ventas y Comisiones</h2>
            <p class="text-xs text-slate-400">Cada venta tiene su comisión calculada y trazable en tiempo real.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            {{-- Buscador --}}
            <div class="relative min-w-[220px]">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Buscar por cliente o folio..."
                       class="w-full pl-9 pr-8 py-2 text-xs rounded-xl border border-slate-200 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                <button type="button"
                        x-show="searchQuery.length > 0"
                        @click="searchQuery = ''"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            </div>

            {{-- Filtros de estado de comisión --}}
            <div class="inline-flex items-center rounded-xl border border-slate-200 bg-white p-0.5 text-xs font-bold overflow-x-auto">
                <button type="button"
                        @click="filterStatus = 'todos'"
                        :class="filterStatus === 'todos' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50'"
                        class="px-3 py-1.5 rounded-[10px] transition-all whitespace-nowrap">
                    Todas (<span x-text="salesList.length"></span>)
                </button>
                <button type="button"
                        @click="filterStatus = 'pagada'"
                        :class="filterStatus === 'pagada' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-500 hover:text-emerald-700 hover:bg-emerald-50'"
                        class="px-3 py-1.5 rounded-[10px] transition-all whitespace-nowrap">
                    Cobradas
                </button>
                <button type="button"
                        @click="filterStatus = 'pendiente_cobro'"
                        :class="filterStatus === 'pendiente_cobro' ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-500 hover:text-amber-700 hover:bg-amber-50'"
                        class="px-3 py-1.5 rounded-[10px] transition-all whitespace-nowrap">
                    Por cobrar
                </button>
                <button type="button"
                        @click="filterStatus = 'proyectada'"
                        :class="filterStatus === 'proyectada' ? 'bg-slate-700 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50'"
                        class="px-3 py-1.5 rounded-[10px] transition-all whitespace-nowrap">
                    Por ganar
                </button>
                <button type="button"
                        @click="filterStatus = 'anulada'"
                        :class="filterStatus === 'anulada' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-500 hover:text-rose-700 hover:bg-rose-50'"
                        class="px-3 py-1.5 rounded-[10px] transition-all whitespace-nowrap">
                    Anuladas
                </button>
            </div>
        </div>
    </div>

    {{-- Contenedor principal de la tabla --}}
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden">
        {{-- VISTA DESKTOP: TABLA --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-5">Fecha</th>
                        <th class="py-3.5 px-5">Cliente</th>
                        <th class="py-3.5 px-5 text-right">Monto Venta</th>
                        <th class="py-3.5 px-4 text-center">% Comisión</th>
                        <th class="py-3.5 px-5 text-right">Comisión</th>
                        <th class="py-3.5 px-5 text-center">Estado Venta</th>
                        <th class="py-3.5 px-5 text-center">Estado Comisión</th>
                        <th class="py-3.5 px-5 text-center">Cálculo</th>
                    </tr>
                </thead>

                {{-- En Alpine, cada iteración de x-for debe tener un solo elemento raíz.
                     Usamos <tbody> por cada venta para contener tanto la fila principal como la fila expandible. --}}
                <template x-for="item in filteredSales" :key="item.id">
                    <tbody class="border-b border-slate-100 last:border-b-0 transition-colors">
                        <tr class="hover:bg-slate-50/80 transition-colors cursor-pointer select-none"
                            @click="toggleRow(item.id)">
                            {{-- Fecha y folio --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="text-xs font-bold text-slate-800 block" x-text="item.folio"></span>
                                <span class="text-[11px] text-slate-400 block" x-text="item.fecha_formateada"></span>
                            </td>

                            {{-- Cliente --}}
                            <td class="py-3.5 px-5 max-w-[200px]">
                                <span class="text-xs font-semibold text-slate-800 block truncate" x-text="item.cliente"></span>
                            </td>

                            {{-- Monto venta --}}
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <span class="text-xs font-bold text-slate-900" x-text="`$${formatMoney(item.monto)}`"></span>
                            </td>

                            {{-- % comisión --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-bold text-[11px]"
                                      x-text="`${item.comision.porcentaje}%`"></span>
                            </td>

                            {{-- Comisión con intención de color --}}
                            <td class="py-3.5 px-5 text-right whitespace-nowrap font-black">
                                <template x-if="item.comision.estado === 'pagada'">
                                    <span class="text-xs text-emerald-600" x-text="`+$${formatMoney(item.comision.monto)}`"></span>
                                </template>
                                <template x-if="item.comision.estado === 'pendiente_cobro'">
                                    <span class="text-xs text-amber-600" x-text="`+$${formatMoney(item.comision.monto)}`"></span>
                                </template>
                                <template x-if="item.comision.estado === 'proyectada'">
                                    <span class="text-xs text-slate-400" x-text="`$${formatMoney(item.comision.monto)}`"></span>
                                </template>
                                <template x-if="item.comision.estado === 'anulada'">
                                    <span class="text-xs text-rose-500 line-through" x-text="`$${formatMoney(item.comision.monto)}`"></span>
                                </template>
                            </td>

                            {{-- Estado venta badge --}}
                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-tight"
                                      :class="getVentaBadgeClass(item.estadoVenta)">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="getVentaDotClass(item.estadoVenta)"></span>
                                    <span x-text="item.estadoVentaLabel"></span>
                                </span>
                            </td>

                            {{-- Estado comisión badge --}}
                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-tight"
                                      :class="getComisionBadgeClass(item.comision.estado)">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="getComisionDotClass(item.comision.estado)"></span>
                                    <span x-text="getComisionLabel(item.comision.estado)"></span>
                                </span>
                            </td>

                            {{-- Botón interactivo para ver cálculo --}}
                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                <button type="button"
                                        @click.stop="toggleRow(item.id)"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold transition-all"
                                        :class="expandedRow === item.id ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-700 hover:bg-blue-100'">
                                    <i class="fas text-[10px]" :class="expandedRow === item.id ? 'fa-chevron-up' : 'fa-calculator'"></i>
                                    <span x-text="expandedRow === item.id ? 'Cerrar' : 'Detalle'"></span>
                                </button>
                            </td>
                        </tr>

                        {{-- Fila expandible con detalle del cálculo --}}
                        <tr x-show="expandedRow === item.id"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="bg-blue-50/40">
                            <td colspan="8" class="p-4 sm:px-6">
                                <div class="rounded-2xl bg-white p-4 border border-blue-100 shadow-sm space-y-3">
                                    <div class="flex items-center justify-between flex-wrap gap-2 pb-2 border-b border-slate-100">
                                        <div class="flex items-center gap-2">
                                            <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs">
                                                <i class="fas fa-calculator"></i>
                                            </span>
                                            <div>
                                                <p class="text-xs font-black text-slate-800">Fórmula de comisión aplicada</p>
                                                <p class="text-[11px] text-slate-400">Calculada en base a los productos y monto total de esta venta</p>
                                            </div>
                                        </div>

                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-100">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-500">Resultado:</span>
                                            <span class="text-xs font-mono font-black text-blue-800" x-text="item.comision.formula"></span>
                                        </div>
                                    </div>

                                    <template x-if="item.comision.detalles && item.comision.detalles.length > 0">
                                        <div>
                                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">
                                                Desglose de partidas y productos vendidos:
                                            </span>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                <template x-for="(d, i) in item.comision.detalles" :key="i">
                                                    <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700 font-medium">
                                                        <i class="fas fa-check-circle text-emerald-500 text-xs flex-shrink-0"></i>
                                                        <span class="truncate" x-text="d"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </template>

                {{-- Fila de totales (coincide exactamente con el resumen de los datos mostrados) --}}
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 text-xs font-black text-slate-900">
                    <tr>
                        <td colspan="2" class="py-3 px-5">
                            Totales visibles (<span x-text="filteredSales.length"></span> ventas)
                        </td>
                        <td class="py-3 px-5 text-right font-black" x-text="`$${formatMoney(filteredTotals.monto)}`"></td>
                        <td class="py-3 px-4 text-center text-slate-500 font-bold" x-text="`${filteredTotals.porcentajeAvg}% avg`"></td>
                        <td class="py-3 px-5 text-right text-indigo-700 font-black" x-text="`$${formatMoney(filteredTotals.comision)}`"></td>
                        <td colspan="3" class="py-3 px-5 text-slate-400 text-right text-[11px] font-medium">
                            Coincide 1:1 con el resumen
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- VISTA MÓVIL: LISTA DE TARJETAS --}}
        <div class="md:hidden divide-y divide-slate-100">
            <template x-for="item in filteredSales" :key="item.id">
                <div class="p-4 space-y-3 hover:bg-slate-50/50 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-xs font-black text-slate-900 block" x-text="item.folio"></span>
                            <span class="text-[11px] text-slate-400 block" x-text="item.fecha_formateada"></span>
                            <span class="text-xs font-semibold text-slate-700 block mt-0.5" x-text="item.cliente"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-slate-500 block">Venta</span>
                            <span class="text-sm font-black text-slate-900" x-text="`$${formatMoney(item.monto)}`"></span>
                        </div>
                    </div>

                    {{-- Comisión destacada ligada a la venta --}}
                    <div class="rounded-xl bg-slate-50 p-2.5 flex items-center justify-between border border-slate-100">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Comisión (<span x-text="`${item.comision.porcentaje}%`"></span>)</span>
                            <template x-if="item.comision.estado === 'pagada'">
                                <span class="text-sm font-black text-emerald-600" x-text="`+$${formatMoney(item.comision.monto)}`"></span>
                            </template>
                            <template x-if="item.comision.estado === 'pendiente_cobro'">
                                <span class="text-sm font-black text-amber-600" x-text="`+$${formatMoney(item.comision.monto)}`"></span>
                            </template>
                            <template x-if="item.comision.estado === 'proyectada'">
                                <span class="text-sm font-black text-slate-400" x-text="`$${formatMoney(item.comision.monto)}`"></span>
                            </template>
                            <template x-if="item.comision.estado === 'anulada'">
                                <span class="text-sm font-black text-rose-500 line-through" x-text="`$${formatMoney(item.comision.monto)}`"></span>
                            </template>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold"
                                  :class="getComisionBadgeClass(item.comision.estado)">
                                <span class="w-1.5 h-1.5 rounded-full" :class="getComisionDotClass(item.comision.estado)"></span>
                                <span x-text="getComisionLabel(item.comision.estado)"></span>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold"
                              :class="getVentaBadgeClass(item.estadoVenta)">
                            <span class="w-1.5 h-1.5 rounded-full" :class="getVentaDotClass(item.estadoVenta)"></span>
                            <span x-text="item.estadoVentaLabel"></span>
                        </span>

                        <button type="button"
                                @click="toggleRow(item.id)"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold transition-all"
                                :class="expandedRow === item.id ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700'">
                            <i class="fas text-[10px]" :class="expandedRow === item.id ? 'fa-chevron-up' : 'fa-calculator'"></i>
                            <span x-text="expandedRow === item.id ? 'Cerrar detalle' : 'Ver cálculo'"></span>
                        </button>
                    </div>

                    {{-- Detalle expandible en móvil --}}
                    <div x-show="expandedRow === item.id"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="rounded-xl bg-blue-50/70 p-3 border border-blue-100 text-xs space-y-2">
                        <div class="flex items-center gap-2 text-blue-900 font-bold">
                            <i class="fas fa-calculator text-blue-600"></i>
                            <span x-text="item.comision.formula"></span>
                        </div>
                        <template x-if="item.comision.detalles && item.comision.detalles.length > 0">
                            <ul class="space-y-1 text-[11px] text-slate-600 pt-1 border-t border-blue-100">
                                <template x-for="(d, i) in item.comision.detalles" :key="i">
                                    <li class="flex items-center gap-1.5">
                                        <i class="fas fa-check text-[9px] text-emerald-500"></i>
                                        <span x-text="d"></span>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        {{-- Estado vacío dentro de la tabla si no hay resultados de búsqueda --}}
        <div x-show="filteredSales.length === 0" class="py-12 px-6 text-center">
            <div class="w-12 h-12 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-search text-slate-300 text-lg"></i>
            </div>
            <p class="text-xs font-bold text-slate-600">No se encontraron ventas con los filtros aplicados</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Prueba cambiando el término de búsqueda o el estado seleccionado.</p>
        </div>
    </div>
</section>
