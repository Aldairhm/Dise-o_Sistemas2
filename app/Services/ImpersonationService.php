<?php

namespace App\Services;

use App\Models\AuditoriaSesion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ImpersonationService
{
    /**
     * Inicia una sesión de auditoría segura sobre la cuenta de un usuario.
     */
    public static function iniciarAuditoria(User $superAdmin, User $targetUser, string $justificacion, int $duracionMinutos = 15, bool $soloLectura = true): AuditoriaSesion
    {
        // 1. Validaciones estrictas de seguridad
        if (!$superAdmin->isSuperAdmin()) {
            throw new \InvalidArgumentException('Solo un usuario SUPER_ADMIN puede iniciar sesiones de auditoría.');
        }

        if ($targetUser->isSuperAdmin()) {
            throw new \InvalidArgumentException('No está permitido auditar cuentas de otros Super Administradores mediante este mecanismo.');
        }

        if (Session::has('auditoria_token')) {
            throw new \InvalidArgumentException('Ya existe una sesión de auditoría activa. No se permiten auditorías anidadas.');
        }

        if (empty(trim($justificacion))) {
            throw new \InvalidArgumentException('Es obligatorio registrar una justificación formal para iniciar una auditoría.');
        }

        // 2. Generar token único e impredecible de sesión
        $token = Str::random(64);
        $expiraEn = now()->addMinutes($duracionMinutos);

        $sesion = AuditoriaSesion::create([
            'token_sesion'        => $token,
            'id_super_admin'      => $superAdmin->id,
            'id_usuario_auditado' => $targetUser->id,
            'justificacion'       => trim($justificacion),
            'solo_lectura'        => $soloLectura,
            'estado'              => 'activa',
            'ip_origen'           => Request::ip(),
            'user_agent'          => substr(Request::userAgent() ?? '', 0, 255),
            'iniciada_en'         => now(),
            'expira_en'           => $expiraEn,
        ]);

        // 3. Establecer el contexto seguro en sesión sin perder la identidad real
        Session::put('auditoria_token', $token);
        Session::put('auditoria_actor_real_id', $superAdmin->id);
        Session::put('auditoria_target_id', $targetUser->id);
        Session::put('auditoria_solo_lectura', $soloLectura);

        // 4. Cambiar el usuario autenticado al usuario auditado
        Auth::login($targetUser);

        // 5. Registrar en bitácora
        ActivityLogger::log(
            'INICIO_AUDITORIA',
            'Auditoría y Seguridad',
            "El Super Administrador {$superAdmin->nombre_real} ({$superAdmin->username}) inició sesión de auditoría sobre {$targetUser->nombre_real} ({$targetUser->username}). Justificación: {$justificacion} (Modo: " . ($soloLectura ? 'Solo Lectura' : 'Permisivo') . ", Expira: {$expiraEn->format('d/m/Y H:i:s')})"
        );

        return $sesion;
    }

    /**
     * Finaliza la sesión de auditoría activa y restaura al Super Administrador.
     */
    public static function finalizarAuditoria(?string $notasCierre = null): bool
    {
        $token = Session::get('auditoria_token');
        $superAdminId = Session::get('auditoria_actor_real_id');

        if (!$token || !$superAdminId) {
            return false;
        }

        $sesion = AuditoriaSesion::where('token_sesion', $token)->first();
        if ($sesion) {
            $sesion->update([
                'estado'        => 'finalizada',
                'finalizada_en' => now(),
                'notas_cierre'  => $notasCierre ?? 'Sesión finalizada manualmente por el Super Administrador.',
            ]);
        }

        $superAdmin = User::find($superAdminId);
        $targetUser = Auth::user();

        // Limpiar tokens de auditoría
        Session::forget(['auditoria_token', 'auditoria_actor_real_id', 'auditoria_target_id', 'auditoria_solo_lectura']);

        // Registrar en bitácora
        if ($superAdmin && $targetUser) {
            ActivityLogger::log(
                'FIN_AUDITORIA',
                'Auditoría y Seguridad',
                "El Super Administrador {$superAdmin->nombre_real} finalizó la sesión de auditoría de la cuenta {$targetUser->nombre_real} ({$targetUser->username}).",
                null,
                null,
                $superAdmin->id
            );
        }

        // Restaurar login del Super Admin
        if ($superAdmin) {
            Auth::login($superAdmin);
        }

        return true;
    }

    /**
     * Revoca forzosamente una sesión de auditoría.
     */
    public static function revocarAuditoria(AuditoriaSesion $sesion, string $motivo): void
    {
        $sesion->update([
            'estado'        => 'revocada',
            'finalizada_en' => now(),
            'notas_cierre'  => "Revocada: {$motivo}",
        ]);

        ActivityLogger::log(
            'REVOCAR_AUDITORIA',
            'Auditoría y Seguridad',
            "Se revocó la sesión de auditoría #{$sesion->id}. Motivo: {$motivo}"
        );
    }
}

