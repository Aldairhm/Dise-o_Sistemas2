@props([
    'resumen' => [],
    'metaMensual' => 500.0,
    'progresoMeta' => 0.0,
    'variacion' => 0.0,
    'periodoActual' => 'mes',
])

@php
    $totalGanado = (float) ($resumen['total_ganado'] ?? 0);
    $cobrado = (float) ($resumen['cobrado'] ?? 0);
    $pendiente = (float) ($resumen['pendiente_cobro'] ?? 0);
    $proyectada = (float) ($resumen['proyectada'] ?? 0);
    $anulada = (float) ($resumen['anulada'] ?? 0);
@endphp

<div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-indigo-700 p-6 sm:p-8 text-white shadow-xl shadow-indigo-600/10">
    {{-- Micro textura sutil sin círculos gigantes --}}
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(255,255,255,0.12)_0%,transparent_65%)]"></div>

    <div class="relative flex flex-col lg:flex-row lg:items-end justify-between gap-8">
        {{-- Bloque principal de Total Ganado --}}
        <div class="flex-1 space-y-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/70">Total Ganado</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/10 text-[10px] font-semibold text-white/90 backdrop-blur-sm">
                        Comisiones + Bonos
                    </span>
                </div>
                <div class="flex items-baseline gap-3 mt-1 flex-wrap">
                    <p class="text-4xl sm:text-5xl font-black tracking-tight leading-none">
                        $<span x-text="formatMoney(summary.total_ganado)">{{ number_format($totalGanado, 2) }}</span>
                    </p>

                    @if($periodoActual !== 'todo' && $variacion != 0)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-bold {{ $variacion > 0 ? 'bg-emerald-400/20 text-emerald-200' : 'bg-rose-400/20 text-rose-200' }}">
                        <i class="fas {{ $variacion > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-[10px]"></i>
                        {{ abs($variacion) }}% vs {{ $periodoActual === 'hoy' ? 'ayer' : ($periodoActual === 'semana' ? 'semana anterior' : 'mes anterior') }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Desglose con píldoras translúcidas --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                {{-- Cobrado --}}
                <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-3 border border-white/10">
                    <span class="block text-[11px] font-semibold text-white/70">Cobrado</span>
                    <span class="text-lg font-black text-emerald-300 tracking-tight">
                        $<span x-text="formatMoney(summary.cobrado)">{{ number_format($cobrado, 2) }}</span>
                    </span>
                    <span class="block text-[10px] text-white/50">Ya liquidado</span>
                </div>

                {{-- Pendiente de cobro --}}
                <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-3 border border-white/10">
                    <span class="block text-[11px] font-semibold text-white/70">Pendiente</span>
                    <span class="text-lg font-black text-amber-300 tracking-tight">
                        $<span x-text="formatMoney(summary.pendiente_cobro)">{{ number_format($pendiente, 2) }}</span>
                    </span>
                    <span class="block text-[10px] text-white/50">Por liquidar</span>
                </div>

                {{-- Por ganar (proyectada) --}}
                <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-3 border border-white/10">
                    <span class="block text-[11px] font-semibold text-white/70">Por ganar</span>
                    <span class="text-lg font-black text-white/90 tracking-tight">
                        $<span x-text="formatMoney(summary.proyectada)">{{ number_format($proyectada, 2) }}</span>
                    </span>
                    <span class="block text-[10px] text-white/50">En proceso</span>
                </div>

                {{-- Anulada --}}
                <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-3 border border-white/10">
                    <span class="block text-[11px] font-semibold text-white/70">Anulada</span>
                    <span class="text-lg font-black text-rose-300 tracking-tight">
                        $<span x-text="formatMoney(summary.anulada)">{{ number_format($anulada, 2) }}</span>
                    </span>
                    <span class="block text-[10px] text-white/50">Cancelada / devuelta</span>
                </div>
            </div>
        </div>

        {{-- Meta mensual --}}
        <div class="lg:w-72 flex-shrink-0 rounded-2xl bg-white/10 backdrop-blur-sm p-4 sm:p-5 border border-white/15">
            <div class="flex items-center justify-between text-xs font-bold text-white/80 mb-2">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-bullseye text-[12px] text-white/70"></i> Meta mensual
                </span>
                <span>
                    $<span x-text="formatMoney(summary.total_ganado)">{{ number_format($totalGanado, 2) }}</span> / ${{ number_format($metaMensual, 2) }}
                </span>
            </div>

            <div class="w-full h-3 rounded-full bg-black/20 overflow-hidden p-0.5 border border-white/10">
                <div class="h-full rounded-full transition-all duration-700 bg-gradient-to-r from-emerald-400 to-teal-300"
                     :style="`width: ${metaProgreso}%`"
                     style="width: {{ $progresoMeta }}%"></div>
            </div>

            <div class="flex items-center justify-between text-[11px] font-medium text-white/70 mt-2">
                <span>Avance</span>
                <span class="font-bold text-white" x-text="metaProgreso >= 100 ? '¡Meta alcanzada! 🎉' : `${metaProgreso}% completado`">
                    {{ $progresoMeta >= 100 ? '¡Meta alcanzada! 🎉' : $progresoMeta . '% completado' }}
                </span>
            </div>
        </div>
    </div>
</div>
