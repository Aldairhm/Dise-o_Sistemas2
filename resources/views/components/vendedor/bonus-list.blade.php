@props([
    'bonos' => collect(),
])

@if($bonos->isNotEmpty())
<section class="space-y-3">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-700">Bonos e Incentivos</h2>
            <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-bold text-[11px] border border-indigo-100">
                {{ $bonos->count() }}
            </span>
        </div>
        <span class="text-xs text-slate-400">Sumados a tu Total Ganado</span>
    </div>

    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden">
        <div class="divide-y divide-slate-100">
            @foreach($bonos as $bono)
            @php
                $esPagado = $bono['estado'] === 'pagado';
            @endphp
            <div class="p-4 sm:px-6 sm:py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 transition-colors">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0 mt-0.5 sm:mt-0">
                        <i class="fas fa-gift text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900 leading-snug">
                            {{ $bono['motivo'] }}
                        </p>
                        <p class="text-[11px] text-slate-400">
                            Registrado el {{ $bono['fecha'] }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between sm:justify-end gap-4 pl-12 sm:pl-0">
                    <span class="text-sm font-black text-indigo-700 tracking-tight">
                        +${{ number_format($bono['monto'], 2) }}
                    </span>
                    <x-vendedor.status-badge type="comision" :status="$esPagado ? 'pagada' : 'pendiente_cobro'" :label="$esPagado ? 'Cobrado' : 'Pendiente'" />
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
