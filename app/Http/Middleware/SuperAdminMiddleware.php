<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     * Permite el acceso únicamente a usuarios con rol 'super_admin'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso restringido. Esta acción requiere privilegios de SUPER_ADMIN.',
            ], 403);
        }

        return redirect()->route('home')->with('error', 'Acceso denegado. Se requieren permisos de Super Administrador.');
    }
}

