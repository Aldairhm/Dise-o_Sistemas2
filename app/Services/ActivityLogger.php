<?php

namespace App\Services;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * ActivityLogger — helper centralizado para registrar eventos en la bitácora.
 *
 * Uso básico:
 *   ActivityLogger::log('CREAR', 'Ventas', 'Se creó la venta #123');
 *
 * Con datos antes/después:
 *   ActivityLogger::log('EDITAR', 'Productos', 'Se editó el producto Camiseta',
 *       $datosAnteriores, $datosNuevos);
 *
 * Sin usuario autenticado (p. ej. login fallido):
 *   ActivityLogger::logAnon('LOGIN', 'Autenticación', 'Intento fallido para: admin');
 */
class ActivityLogger
{
    /**
     * Registra un evento con el usuario actualmente autenticado.
     */
    public static function log(
        string $accion,
        string $modulo,
        string $descripcion,
        ?array $datosAnteriores = null,
        ?array $datosNuevos    = null,
        ?int   $userId         = null
    ): void {
        try {
            $user = $userId ? \App\Models\User::find($userId) : Auth::user();

            Bitacora::create([
                'id_usuario'       => $user?->id,
                'username'         => $user?->username,
                'nombre_usuario'   => $user?->nombre_real,
                'rol_usuario'      => $user?->rol,
                'accion'           => strtoupper($accion),
                'modulo'           => $modulo,
                'descripcion'      => $descripcion,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos'     => $datosNuevos,
                'ip_address'       => Request::ip(),
                'user_agent'       => substr(Request::userAgent() ?? '', 0, 255),
                'created_at'       => now(),
            ]);
        } catch (\Throwable $e) {
            // Nunca interrumpir el flujo principal por un error de bitácora
            \Illuminate\Support\Facades\Log::error('ActivityLogger error: ' . $e->getMessage());
        }
    }

    /**
     * Registra un evento anónimo (sin usuario autenticado) — útil para login/logout.
     */
    public static function logAnon(
        string  $accion,
        string  $modulo,
        string  $descripcion,
        ?string $username = null
    ): void {
        try {
            Bitacora::create([
                'id_usuario'     => null,
                'username'       => $username,
                'nombre_usuario' => null,
                'rol_usuario'    => null,
                'accion'         => strtoupper($accion),
                'modulo'         => $modulo,
                'descripcion'    => $descripcion,
                'ip_address'     => Request::ip(),
                'user_agent'     => substr(Request::userAgent() ?? '', 0, 255),
                'created_at'     => now(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ActivityLogger (anon) error: ' . $e->getMessage());
        }
    }
}
