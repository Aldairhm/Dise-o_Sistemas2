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
                Session::forget(['auditoria_token', 'auditoria_actor_real_id', 'auditoria_solo_lectura', 'auditoria_target_id', 'auditoria_last_logged_route']);

                if ($superAdminId) {
                    auth()->loginUsingId($superAdminId);
                }

                return redirect()->route('superadmin.dashboard')->with('error', 'La sesión de auditoría ha expirado o fue revocada por seguridad.');
            }

            // Registrar cada módulo / vista consultada durante la sesión de auditoría para el informe completo
            if ($request->isMethod('GET') && !$request->ajax() && !$request->wantsJson()) {
                $routeName = $request->route()?->getName() ?? $request->path();
                $lastRouteLogged = Session::get('auditoria_last_logged_route');

                if ($lastRouteLogged !== $routeName && !str_starts_with($request->path(), '_debugbar')) {
                    Session::put('auditoria_last_logged_route', $routeName);

                    // Mapeo amigable de módulo según prefijo de ruta
                    $path = $request->path();
                    $moduloAudit = 'Navegación General';
                    if (str_starts_with($path, 'ventas')) $moduloAudit = 'Ventas y Facturación';
                    elseif (str_starts_with($path, 'productos')) $moduloAudit = 'Catálogo de Productos';
                    elseif (str_starts_with($path, 'categorias')) $moduloAudit = 'Categorías';
                    elseif (str_starts_with($path, 'compras')) $moduloAudit = 'Compras';
                    elseif (str_starts_with($path, 'proveedores')) $moduloAudit = 'Proveedores';
                    elseif (str_starts_with($path, 'comisiones')) $moduloAudit = 'Comisiones';
                    elseif (str_starts_with($path, 'inventario')) $moduloAudit = 'Inventario y Bodegas';
                    elseif (str_starts_with($path, 'ayuda')) $moduloAudit = 'Centro de Ayuda y Tickets';
                    elseif (str_starts_with($path, 'admin/reportes')) $moduloAudit = 'Reportes Administrativos';
                    elseif (str_starts_with($path, 'bitacora')) $moduloAudit = 'Bitácora de Sistema';

                    \App\Services\ActivityLogger::log(
                        'REVISION_VISTA',
                        $moduloAudit,
                        "El Super Administrador revisó la sección [{$path}] de la cuenta del usuario auditado.",
                        null,
                        ['ruta' => $routeName, 'url' => $request->fullUrl()]
                    );
                }
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

        // VALIDACIÓN DE AUDITORÍA EN TIEMPO REAL:
        // Si el usuario autenticado ordinario tiene una auditoría activa sobre su cuenta pero esta petición NO es la del auditor:
        // Cerrar de inmediato su sesión ordinaria en este dispositivo (impedir concurrencia).
        if (!Session::has('auditoria_token') && auth()->check()) {
            $userId = auth()->id();
            $auditoriaEnCurso = AuditoriaSesion::where('id_usuario_auditado', $userId)
                ->where('estado', 'activa')
                ->where('expira_en', '>', now())
                ->first();

            if ($auditoriaEnCurso) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sesión cerrada: Tu cuenta ha entrado en un proceso formal de auditoría y supervisión por el Super Administrador.',
                    ], 403);
                }

                return redirect()->route('login')->with('swal_error', [
                    'title' => 'Sesión Finalizada por Auditoría',
                    'text'  => 'Tu sesión fue cerrada porque la cuenta entró en un proceso activo de supervisión y auditoría de seguridad por la Dirección / Super Administrador. No se permite el uso concurrente en dos dispositivos durante la auditoría.',
                ]);
            }
        }

        return $next($request);
    }
}

