<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $query = Bitacora::with('usuario')->orderByDesc('created_at');

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
            $busqueda = $request->input('q');
            $query->where(function ($q) use ($busqueda) {
                $q->where('descripcion',    'like', "%{$busqueda}%")
                  ->orWhere('username',     'like', "%{$busqueda}%")
                  ->orWhere('nombre_usuario','like', "%{$busqueda}%")
                  ->orWhere('ip_address',   'like', "%{$busqueda}%");
            });
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
            'acciones'
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
