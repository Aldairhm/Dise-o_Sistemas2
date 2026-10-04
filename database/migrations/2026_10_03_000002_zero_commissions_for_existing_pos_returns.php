<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $comisiones = DB::table('comision_vendedor as cv')
            ->join('salida as s', 's.id', '=', 'cv.id_salida')
            ->whereIn('cv.estado', ['Pendiente', 'Pagada', 'Cancelada'])
            ->where('cv.monto', '>', 0)
            ->select('cv.id', 'cv.monto', 'cv.estado', 'cv.notas', 's.observaciones')
            ->get();

        foreach ($comisiones as $comision) {
            if (!preg_match('/Venta #(\d+)/i', (string) $comision->observaciones, $match)) {
                continue;
            }

            $venta = DB::table('venta')
                ->where('id', (int) $match[1])
                ->where('tipo_venta', 'Tienda')
                ->where('estado', 'Devolución')
                ->exists();

            if (!$venta) {
                continue;
            }

            $montoOriginal = number_format((float) $comision->monto, 2, '.', '');
            $notaAjuste = "Ajuste histórico POS por devolución: monto {$montoOriginal} y estado {$comision->estado} -> $0.00 / Cancelada.";
            $notas = trim((string) $comision->notas);

            DB::table('comision_vendedor')
                ->where('id', $comision->id)
                ->update([
                    'monto' => 0,
                    'estado' => 'Cancelada',
                    'notas' => $notas === '' ? $notaAjuste : $notas . ' | ' . $notaAjuste,
                ]);
        }
    }

    public function down(): void
    {
        $comisiones = DB::table('comision_vendedor')
            ->where('estado', 'Cancelada')
            ->where('notas', 'like', '%Ajuste histórico POS por devolución:%')
            ->get(['id', 'notas']);

        foreach ($comisiones as $comision) {
            if (!preg_match('/(?:^|\s*\|\s*)Ajuste histórico POS por devolución: monto ([0-9]+\.[0-9]{2}) y estado (Pendiente|Pagada|Cancelada) -> \$0\.00 \/ Cancelada\.$/', (string) $comision->notas, $match)) {
                continue;
            }

            $notas = preg_replace('/\s*\|\s*Ajuste histórico POS por devolución: monto [0-9]+\.[0-9]{2} y estado (?:Pendiente|Pagada|Cancelada) -> \$0\.00 \/ Cancelada\.$/', '', (string) $comision->notas);
            $notas = preg_replace('/^Ajuste histórico POS por devolución: monto [0-9]+\.[0-9]{2} y estado (?:Pendiente|Pagada|Cancelada) -> \$0\.00 \/ Cancelada\.$/', '', (string) $notas);

            DB::table('comision_vendedor')
                ->where('id', $comision->id)
                ->update([
                    'monto' => (float) $match[1],
                    'estado' => $match[2],
                    'notas' => trim((string) $notas) !== '' ? trim((string) $notas) : null,
                ]);
        }
    }
};