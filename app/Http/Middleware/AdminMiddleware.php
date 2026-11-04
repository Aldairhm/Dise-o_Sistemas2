<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Permite el acceso a 'admin' y 'super_admin'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (auth()->user()->rol === 'admin' || auth()->user()->isSuperAdmin())) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado. Se requieren permisos administrativos.',
            ], 403);
        }

        return redirect()->route('home')->with('error', 'No tienes permiso para acceder a esta sección.');
    }
}
