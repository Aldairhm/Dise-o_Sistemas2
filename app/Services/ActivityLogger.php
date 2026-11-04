<?php

namespace App\Services;

use App\Models\AuditoriaSesion;
use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

/**
 * ActivityLogger — helper centralizado para registrar eventos en la bitácora
 * con soporte avanzado para auditoría y suplantación administrativa controlada (Secure Impersonation).
 */
class ActivityLogger
{
    /**
     * Registra un evento con el usuario actualmente autenticado o contexto de auditoría.
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
            $user = $userId ? User::find($userId) : Auth::user();

            // Detectar si la sesión actual está bajo Secure Impersonation / Auditoría
            $enAuditoria = false;
            $actorRealId = null;
            $actorRealUsername = null;
            $correlacionId = Session::get('auditoria_token');

            if (Session::has('auditoria_actor_real_id')) {
                $enAuditoria = true;
                $actorRealId = Session::get('auditoria_actor_real_id');
                $actorReal = User::find($actorRealId);
                $actorRealUsername = $actorReal?->username ?? 'super_admin';
            }

            Bitacora::create([
                'id_usuario'           => $user?->id,
                'username'             => $user?->username,
                'nombre_usuario'       => $user?->nombre_real,
                'rol_usuario'          => $user?->rol,
                'id_actor_real'        => $actorRealId,
                'username_actor_real'  => $actorRealUsername,
                'en_modo_auditoria'    => $enAuditoria,
                'correlacion_id'       => $correlacionId,
                'accion'               => strtoupper($accion),
                'modulo'               => $modulo,
                'descripcion'          => $descripcion,
                'datos_anteriores'     => $datosAnteriores,
                'datos_nuevos'         => $datosNuevos,
                'ip_address'           => Request::ip(),
                'user_agent'           => substr(Request::userAgent() ?? '', 0, 255),
                'created_at'           => now(),
            ]);
        } catch (\Throwable $e) {
            // Nunca interrumpir el flujo principal por un error de bitácora
            \Illuminate\Support\Facades\Log::error('ActivityLogger error: ' . $e->getMessage());
        }
    }

    /**
     * Registra un evento anónimo (sin usuario autenticado) — útil para login/logout o ataques.
     */
    public static function logAnon(
        string  $accion,
        string  $modulo,
        string  $descripcion,
        ?string $username = null
    ): void {
        try {
            Bitacora::create([
                'id_usuario'          => null,
                'username'            => $username,
                'nombre_usuario'      => null,
                'rol_usuario'         => null,
                'id_actor_real'       => null,
                'username_actor_real' => null,
                'en_modo_auditoria'   => false,
                'correlacion_id'      => null,
                'accion'              => strtoupper($accion),
                'modulo'              => $modulo,
                'descripcion'         => $descripcion,
                'ip_address'          => Request::ip(),
                'user_agent'          => substr(Request::userAgent() ?? '', 0, 255),
                'created_at'          => now(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ActivityLogger anon error: ' . $e->getMessage());
        }
    }
}
