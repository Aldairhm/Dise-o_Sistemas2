<?php

namespace App\Http\Controllers;

use App\Models\MovimientoBodega;
use App\Models\Variante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MovimientoBodegaController extends Controller
{
    public function transferirATienda(Request $request)
    {
        $validated = $request->validate([
            'id_variante' => ['required', 'integer', 'exists:variante,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ]);

        $movimiento = DB::transaction(function () use ($validated, $request) {
            $variante = Variante::whereKey($validated['id_variante'])
                ->lockForUpdate()
                ->firstOrFail();

            $cantidad = (int) $validated['cantidad'];
            $reservaAnterior = (int) $variante->reserva;
            $stockAnterior = (int) $variante->stock;

            if ($cantidad > $reservaAnterior) {
                throw ValidationException::withMessages([
                    'cantidad' => 'La cantidad solicitada supera las unidades disponibles en bodega.',
                ]);
            }

            $reservaNueva = $reservaAnterior - $cantidad;
            $stockNuevo = $stockAnterior + $cantidad;

            $variante->update([
                'reserva' => $reservaNueva,
                'stock' => $stockNuevo,
            ]);

            return MovimientoBodega::create([
                'id_variante' => $variante->id,
                'id_usuario' => $request->user()?->id,
                'tipo' => 'transferencia_tienda',
                'cantidad' => $cantidad,
                'reserva_anterior' => $reservaAnterior,
                'reserva_nueva' => $reservaNueva,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'observacion' => $validated['observacion'] ?? 'Transferencia de bodega a tienda.',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Transferencia realizada correctamente.',
            'movimiento' => $movimiento->load('variante'),
        ], 201);
    }
}
