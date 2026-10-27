@props([
    'stats' => [],
])

@php
    $totalMonto = (float) ($stats['total_monto'] ?? 0);
    $totalOrdenes = (int) ($stats['total_ordenes'] ?? 0);
    $entregadas = (int) ($stats['entregadas'] ?? 0);
    $montoEntregadas = (float) ($stats['monto_entregadas'] ?? 0);
    $enProceso = (int) ($stats['en_proceso'] ?? 0);
    $pendientes = (int) ($stats['pendientes'] ?? 0);
    $confirmadas = (int) ($stats['confirmadas'] ?? 0);
    $enRuta = (int) ($stats['en_ruta'] ?? 0);
    $canceladas = (int) ($stats['canceladas'] ?? 0);
    $devoluciones = (int) ($stats['devoluciones'] ?? 0);
@endphp

<div class="bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden">
    <div class="grid grid-cols-2 lg:grid-cols-4 divide-y lg:divide-y-0 lg:divide-x divide-slate-100">
        {{-- Vendido --}}
        <div class="p-4 sm:p-5 hover:bg-slate-50/50 transition-colors">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Vendido</span>
            <div class="flex items-baseline gap-1">
                <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    $<span x-text="formatMoney(salesStats.total_monto)">{{ number_format($totalMonto, 2) }}</span>
                </span>
            </div>
            <span class="block text-xs font-medium text-slate-500 mt-0.5">
                <span x-text="salesStats.total_ordenes">{{ $totalOrdenes }}</span> órdenes
            </span>
        </div>

        {{-- Entregadas --}}
        <div class="p-4 sm:p-5 hover:bg-slate-50/50 transition-colors">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Entregadas</span>
            <div class="flex items-baseline gap-1">
                <span class="text-xl sm:text-2xl font-black text-emerald-600 tracking-tight" x-text="salesStats.entregadas">
                    {{ $entregadas }}
                </span>
            </div>
            <span class="block text-xs font-medium text-slate-500 mt-0.5">
                $<span x-text="formatMoney(salesStats.monto_entregadas)">{{ number_format($montoEntregadas, 2) }}</span> confirmado
            </span>
        </div>

        {{-- En proceso --}}
        <div class="p-4 sm:p-5 hover:bg-slate-50/50 transition-colors">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">En proceso</span>
            <div class="flex items-baseline gap-1">
                <span class="text-xl sm:text-2xl font-black tracking-tight"
                      :class="salesStats.en_proceso > 0 ? 'text-amber-600' : 'text-slate-300'"
                      x-text="salesStats.en_proceso">
                    {{ $enProceso }}
                </span>
            </div>
            <span class="block text-xs font-medium text-slate-500 mt-0.5 truncate"
                  x-text="`${salesStats.pendientes} pend · ${salesStats.confirmadas} conf · ${salesStats.en_ruta} ruta`">
                {{ $pendientes }} pend · {{ $confirmadas }} conf · {{ $enRuta }} ruta
            </span>
        </div>

        {{-- Canceladas / Devoluciones --}}
        <div class="p-4 sm:p-5 hover:bg-slate-50/50 transition-colors">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Canceladas</span>
            <div class="flex items-baseline gap-1">
                <span class="text-xl sm:text-2xl font-black tracking-tight"
                      :class="salesStats.canceladas > 0 ? 'text-rose-600' : 'text-slate-300'"
                      x-text="salesStats.canceladas">
                    {{ $canceladas }}
                </span>
            </div>
            <span class="block text-xs font-medium mt-0.5"
                  :class="salesStats.devoluciones > 0 ? 'text-orange-500' : 'text-slate-500'"
                  x-text="`${salesStats.devoluciones} devolución(es)`">
                {{ $devoluciones }} devolución(es)
            </span>
        </div>
    </div>
</div>
