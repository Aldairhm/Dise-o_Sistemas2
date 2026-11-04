<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaSesion;
use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $query = Bitacora::with('usuario')->orderByDesc('created_at');
        $sesionAuditoria = null;

        // ── Filtro Específico por Sesión de Auditoría ────────────────────────
        if ($request->filled('auditoria_id')) {
            $sesionAuditoria = AuditoriaSesion::with(['superAdmin', 'usuarioAuditado'])->find($request->input('auditoria_id'));
            if ($sesionAuditoria) {
                $query->where(function ($q) use ($sesionAuditoria) {
                    $q->where('correlacion_id', $sesionAuditoria->token_sesion)
                      ->orWhere(function ($sub) use ($sesionAuditoria) {
                          $sub->where('id_usuario', $sesionAuditoria->id_usuario_auditado)
                              ->whereBetween('created_at', [
                                  $sesionAuditoria->iniciada_en->subSeconds(10),
                                  ($sesionAuditoria->finalizada_en ?? $sesionAuditoria->expira_en)->addSeconds(10)
                              ]);
                      })
                      ->orWhere('descripcion', 'like', "%#{$sesionAuditoria->id}%")
                      ->orWhere('descripcion', 'like', "%#AUD-" . str_pad($sesionAuditoria->id, 4, '0', STR_PAD_LEFT) . "%");
                });
            }
        }

        // ── Filtros ──────────────────────────────────────────────────────────
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->input('modulo'));
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->input('accion'));
        }

        if ($request->filled('usuario_id')) {
            $query->where('id_usuario', $request->input('usuario_id'));
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->input('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->input('hasta'));
        }

        if ($request->filled('q')) {
            $busqueda = trim($request->input('q'));

            // Si la búsqueda parece un token de sesión de auditoría o coincide con una
            $sesionPorToken = AuditoriaSesion::where('token_sesion', $busqueda)->first();
            if ($sesionPorToken) {
                $sesionAuditoria = $sesionPorToken;
                $query->where(function ($q) use ($sesionPorToken) {
                    $q->where('correlacion_id', $sesionPorToken->token_sesion)
                      ->orWhere(function ($sub) use ($sesionPorToken) {
                          $sub->where('id_usuario', $sesionPorToken->id_usuario_auditado)
                              ->whereBetween('created_at', [
                                  $sesionPorToken->iniciada_en->subSeconds(10),
                                  ($sesionPorToken->finalizada_en ?? $sesionPorToken->expira_en)->addSeconds(10)
                              ]);
                      })
                      ->orWhere('descripcion', 'like', "%#{$sesionPorToken->id}%");
                });
            } else {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('descripcion',      'like', "%{$busqueda}%")
                      ->orWhere('username',       'like', "%{$busqueda}%")
                      ->orWhere('nombre_usuario', 'like', "%{$busqueda}%")
                      ->orWhere('ip_address',     'like', "%{$busqueda}%")
                      ->orWhere('correlacion_id', 'like', "%{$busqueda}%");
                });
            }
        }

        $registros = $query->paginate(25)->withQueryString();

        // ── Para el filtro de usuarios ───────────────────────────────────────
        $usuarios = User::orderBy('nombre_real')->get(['id', 'nombre_real', 'username']);

        // ── Módulos y acciones únicas para los selects ───────────────────────
        $modulos  = Bitacora::select('modulo')->distinct()->orderBy('modulo')->pluck('modulo');
        $acciones = Bitacora::select('accion')->distinct()->orderBy('accion')->pluck('accion');

        return view('bitacora.index', compact(
            'registros',
            'usuarios',
            'modulos',
            'acciones',
            'sesionAuditoria'
        ));
    }

    /**
     * Muestra el detalle completo de un registro (modal / página).
     */
    public function show(Bitacora $bitacora)
    {
        return view('bitacora.show', compact('bitacora'));
    }
}
