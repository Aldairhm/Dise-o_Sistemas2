<?php

namespace App\Http\Middleware;

use App\Models\AuditoriaSesion;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AuditoriaSesionMiddleware
{
    /**
     * Valida la vigencia y restricciones de una sesión de auditoría (Secure Impersonation).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Session::has('auditoria_token')) {
            $token = Session::get('auditoria_token');
            $sesion = AuditoriaSesion::where('token_sesion', $token)->first();

            // Si la sesión no existe, o ya no está activa, o ha expirado:
            if (!$sesion || !$sesion->estaVigente()) {
                if ($sesion && $sesion->estado === 'activa') {
                    $sesion->update([
                        'estado'        => 'expirada',
                        'finalizada_en' => now(),
                        'notas_cierre'  => 'Sesión expirada automáticamente por límite de tiempo.',
                    ]);
                }

                // Restaurar sesión original de Super Admin si es posible
                $superAdminId = Session::get('auditoria_actor_real_id');
                Session::forget(['auditoria_token', 'auditoria_actor_real_id', 'auditoria_solo_lectura', 'auditoria_target_id']);

                if ($superAdminId) {
                    auth()->loginUsingId($superAdminId);
                }

                return redirect()->route('superadmin.dashboard')->with('error', 'La sesión de auditoría ha expirado o fue revocada por seguridad.');
            }

            // RESTRICCIÓN OBLIGATORIA: Si está en modo solo lectura, bloquear métodos mutantes (POST, PUT, PATCH, DELETE)
            // excepto la ruta de finalizar la propia auditoría
            if (Session::get('auditoria_solo_lectura', true) && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                if (!$request->routeIs('superadmin.auditorias.finalizar')) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'MODO AUDITORÍA (SOLO LECTURA): No está permitido realizar modificaciones, ventas ni transacciones financieras sobre la cuenta auditada.',
                        ], 403);
                    }
                    return back()->with('error', 'Acción bloqueada: La sesión de auditoría está en modo SOLO LECTURA.');
                }
            }
        }

        return $next($request);
    }
}

