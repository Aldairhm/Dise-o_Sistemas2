<?php

namespace App\Observers;

use App\Models\ComisionVendedor;
use App\Services\ComisionService;

/**
 * SalidaObserver
 *
 * Este observer se registrará en AppServiceProvider cuando el módulo
 * de Salida esté listo. Por ahora solo contiene la lógica lista para conectar.
 *
 * Para activarlo, agrega en AppServiceProvider::boot():
 *   \App\Models\Salida::observe(\App\Observers\SalidaObserver::class);
 */
class SalidaObserver
{
    public function __construct(protected ComisionService $comisionService) {}

    /**
     * Al crear una salida, genera la comisión automáticamente.
     * Solo aplica si la salida tiene un vendedor asignado.
     */
    public function created($salida): void
    {
        if (!$salida->id_usuario) {
            return;
        }

        $vendedor   = $salida->vendedor;
        $variante   = $salida->variante ?? null;
        $porcentaje = $this->comisionService->resolverPorcentaje($vendedor, $variante);

        $this->comisionService->crearParaSalida(
            $salida->id,
            $salida->id_usuario,
            (float) $salida->total,
            $porcentaje
        );
    }

    /**
     * Si la salida se cambia a 'Cancelado', anula la comisión pendiente.
     */
    public function updated($salida): void
    {
        if ($salida->isDirty('estado') && $salida->estado === 'Cancelado') {
            $this->comisionService->cancelarPorSalida($salida->id);
        }
    }
}
