<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaSesion;
use App\Models\Bitacora;
use App\Models\SoporteTicket;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\ImpersonationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    /**
     * Dashboard Ejecutivo del Super Administrador
     */
    public function dashboard()
    {
        $stats = [
            'total_usuarios'         => User::count(),
            'total_superadmins'      => User::where('rol', 'super_admin')->count(),
            'total_admins'           => User::where('rol', 'admin')->count(),
            'total_vendedores'       => User::where('rol', 'vendedor')->count(),
            'usuarios_activos'       => User::where('estado', 1)->count(),
            'usuarios_inactivos'     => User::where('estado', 0)->count(),
            'dudas_pendientes'       => SoporteTicket::whereIn('estado', ['Pendiente', 'Asignada', 'En proceso'])->count(),
            'dudas_escaladas'        => SoporteTicket::where('estado', 'Escalada')->orWhere('nivel_escalamiento', 'super_admin')->count(),
            'dudas_resueltas'        => SoporteTicket::whereIn('estado', ['Resuelta', 'Cerrada'])->count(),
            'auditorias_activas'     => AuditoriaSesion::where('estado', 'activa')->where('expira_en', '>', now())->count(),
            'total_eventos_bitacora' => Bitacora::count(),
        ];

        // Últimas solicitudes escaladas o para el super admin
        $ticketsSuperAdmin = SoporteTicket::with(['usuario', 'escalador'])
            ->where(function ($q) {
                $q->where('nivel_escalamiento', 'super_admin')
                  ->orWhere('rol_usuario', 'admin')
                  ->orWhere('estado', 'Escalada');
            })
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Últimas sesiones de auditoría realizadas
        $ultimasAuditorias = AuditoriaSesion::with(['superAdmin', 'usuarioAuditado'])
            ->latest('id')
            ->take(5)
            ->get();

        // Eventos de seguridad recientes
        $eventosSeguridad = Bitacora::whereIn('modulo', ['Auditoría y Seguridad', 'Autenticación', 'Usuarios'])
            ->latest('id')
            ->take(6)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'ticketsSuperAdmin', 'ultimasAuditorias', 'eventosSeguridad'));
    }

    /**
     * Gestión Avanzada de Usuarios (Exclusivo Super Administrador)
     */
    public function usuarios(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nombre_real) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(username) like ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('rol') && $request->rol !== 'todos') {
            $query->where('rol', $request->rol);
        }

        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', (int) $request->estado);
        }

        $users = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('superadmin.usuarios', compact('users'));
    }

    /**
     * Iniciar sesión de auditoría segura (Secure Impersonation)
     */
    public function iniciarAuditoria(Request $request, User $user)
    {
        $request->validate([
            'justificacion' => 'required|string|min:10|max:500',
            'mfa_password'  => 'required|string',
            'duracion'      => 'nullable|integer|min:5|max:60',
            'solo_lectura'  => 'nullable|boolean',
        ], [
            'justificacion.required' => 'Debes ingresar una justificación formal obligatoria.',
            'justificacion.min'      => 'La justificación debe tener al menos 10 caracteres.',
            'mfa_password.required'  => 'Debes confirmar tu contraseña de Super Administrador para validar la acción.',
        ]);

        $superAdmin = Auth::user();

        // 1. Verificación reforzada con contraseña del Super Admin
        if (!Hash::check($request->mfa_password, $superAdmin->password)) {
            ActivityLogger::log(
                'INTENTO_AUDITORIA_FALLIDO',
                'Auditoría y Seguridad',
                "Intento fallido de iniciar auditoría sobre el usuario ID {$user->id} por contraseña incorrecta."
            );
            return back()->with('error', 'Credenciales de validación incorrectas. Acción rechazada.');
        }

        try {
            $duracion = $request->input('duracion', 15);
            $soloLectura = $request->boolean('solo_lectura', true);

            ImpersonationService::iniciarAuditoria($superAdmin, $user, $request->justificacion, $duracion, $soloLectura);

            return redirect()->route('home')->with('success', "Has ingresado al contexto de auditoría de {$user->nombre_real}.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Finalizar la sesión de auditoría en curso
     */
    public function finalizarAuditoria(Request $request)
    {
        $token = session('auditoria_token');
        $sesion = $token ? AuditoriaSesion::where('token_sesion', $token)->first() : null;

        $notas = $request->input('notas_cierre', 'Auditoría completada exitosamente.');
        ImpersonationService::finalizarAuditoria($notas);

        if ($sesion) {
            return redirect()->route('superadmin.auditorias.informe', $sesion->id)
                ->with('success', 'Sesión de auditoría finalizada exitosamente. A continuación se presenta el informe completo de la auditoría.');
        }

        return redirect()->route('superadmin.dashboard')->with('success', 'Sesión de auditoría finalizada. Has vuelto a tu perfil de Super Administrador.');
    }

    /**
     * Historial global de auditorías
     */
    public function historialAuditorias()
    {
        $auditorias = AuditoriaSesion::with(['superAdmin', 'usuarioAuditado'])
            ->latest('id')
            ->paginate(15);

        return view('superadmin.historial-auditorias', compact('auditorias'));
    }

    /**
     * Informe completo y detallado de una sesión de auditoría realizada
     */
    public function informeAuditoria(AuditoriaSesion $sesion)
    {
        $sesion->load(['superAdmin', 'usuarioAuditado']);

        // Consultar todos los eventos registrados durante la auditoría
        $eventos = \App\Models\Bitacora::where(function ($q) use ($sesion) {
            $q->where('correlacion_id', $sesion->token_sesion)
              ->orWhere(function ($sub) use ($sesion) {
                  $sub->where('en_modo_auditoria', true)
                      ->where('id_actor_real', $sesion->id_super_admin)
                      ->where('id_usuario', $sesion->id_usuario_auditado)
                      ->whereBetween('created_at', [
                          $sesion->iniciada_en,
                          $sesion->finalizada_en ?? ($sesion->expira_en ?? now())
                      ]);
              });
        })
        ->orderBy('id', 'asc')
        ->get();

        // Agrupaciones y resúmenes para el informe
        $modulosRevisados = $eventos->pluck('modulo')->unique()->filter()->values();
        $accionesTotales = $eventos->count();
        $resumenAcciones = $eventos->groupBy('accion')->map->count();

        // Calcular duración exacta de la auditoría
        $duracionRealMinutos = $sesion->finalizada_en 
            ? max(1, round($sesion->iniciada_en->diffInMinutes($sesion->finalizada_en)))
            : round($sesion->iniciada_en->diffInMinutes(now()));

        return view('superadmin.informe-auditoria', compact(
            'sesion',
            'eventos',
            'modulosRevisados',
            'accionesTotales',
            'resumenAcciones',
            'duracionRealMinutos'
        ));
    }

    /**
     * Descargar o imprimir/visualizar el informe de auditoría en formato PDF formal
     */
    public function exportarInformePdf(Request $request, AuditoriaSesion $sesion)
    {
        $sesion->load(['superAdmin', 'usuarioAuditado']);

        $eventos = \App\Models\Bitacora::where(function ($q) use ($sesion) {
            $q->where('correlacion_id', $sesion->token_sesion)
              ->orWhere(function ($sub) use ($sesion) {
                  $sub->where('en_modo_auditoria', true)
                      ->where('id_actor_real', $sesion->id_super_admin)
                      ->where('id_usuario', $sesion->id_usuario_auditado)
                      ->whereBetween('created_at', [
                          $sesion->iniciada_en,
                          $sesion->finalizada_en ?? ($sesion->expira_en ?? now())
                      ]);
              });
        })
        ->orderBy('id', 'asc')
        ->get();

        $modulosRevisados = $eventos->pluck('modulo')->unique()->filter()->values();
        $accionesTotales = $eventos->count();
        $resumenAcciones = $eventos->groupBy('accion')->map->count();

        $duracionRealMinutos = $sesion->finalizada_en 
            ? max(1, round($sesion->iniciada_en->diffInMinutes($sesion->finalizada_en)))
            : round($sesion->iniciada_en->diffInMinutes(now()));

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.informe-auditoria', compact(
            'sesion',
            'eventos',
            'modulosRevisados',
            'accionesTotales',
            'resumenAcciones',
            'duracionRealMinutos'
        ))->setPaper('letter', 'portrait');

        $filename = 'Informe_Auditoria_AUD-' . str_pad($sesion->id, 4, '0', STR_PAD_LEFT) . '_' . date('Ymd_His') . '.pdf';

        if ($request->query('action') === 'stream' || $request->has('print')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Revocar sesión de auditoría específica
     */
    public function revocarAuditoria(Request $request, AuditoriaSesion $sesion)
    {
        $motivo = $request->input('motivo', 'Revocación manual por el Super Administrador.');
        ImpersonationService::revocarAuditoria($sesion, $motivo);

        return back()->with('success', "La sesión de auditoría #{$sesion->id} ha sido revocada.");
    }

    /**
     * Centro de Dudas para el Super Administrador
     */
    public function tickets(Request $request)
    {
        $query = SoporteTicket::with(['usuario', 'asignado', 'escalador']);

        // Solo dudas que conciernen al nivel Super Admin o escaladas
        $query->where(function ($q) {
            $q->where('nivel_escalamiento', 'super_admin')
              ->orWhere('rol_usuario', 'admin')
              ->orWhere('estado', 'Escalada');
        });

        // Conteo base para las pestañas de estado
        $baseCounts = clone $query;
        $conteoEstados = [
            'todos'      => (clone $baseCounts)->count(),
            'Pendiente'  => (clone $baseCounts)->where('estado', 'Pendiente')->count(),
            'En proceso' => (clone $baseCounts)->where('estado', 'En proceso')->count(),
            'Escalada'   => (clone $baseCounts)->where('estado', 'Escalada')->count(),
            'Resuelta'   => (clone $baseCounts)->where('estado', 'Resuelta')->count(),
            'Cancelada'  => (clone $baseCounts)->where('estado', 'Cancelada')->count(),
        ];

        // Por defecto, siempre entra filtrando por 'Pendiente' para mayor autonomía
        $estadoTab = $request->query('estado', 'Pendiente');
        if ($estadoTab !== 'todos' && in_array($estadoTab, SoporteTicket::ESTADOS, true)) {
            $query->where('estado', $estadoTab);
        }

        if ($request->filled('search')) {
            $s = strtolower(trim($request->search));
            $query->where(function ($q) use ($s) {
                $q->whereRaw('LOWER(codigo_ticket) like ?', ["%{$s}%"])
                  ->orWhereRaw('LOWER(titulo) like ?', ["%{$s}%"])
                  ->orWhereRaw('LOWER(duda) like ?', ["%{$s}%"])
                  ->orWhereRaw('LOWER(nombre_usuario) like ?', ["%{$s}%"]);
            });
        }

        $tickets = $query->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.tickets', compact('tickets', 'conteoEstados', 'estadoTab'));
    }

    /**
     * Crear o actualizar un Super Administrador de forma segura
     */
    public function guardarSuperAdmin(Request $request)
    {
        $validated = $request->validate([
            'nombre_real'   => 'required|string|max:255',
            'username'      => 'required|email|max:255|unique:usuario,username',
            'password'      => 'required|string|min:10|confirmed',
            'auth_password' => 'required|string',
        ]);

        if (!Hash::check($request->auth_password, Auth::user()->password)) {
            return back()->with('error', 'Contraseña de autorización incorrecta.');
        }

        $nuevoAdmin = User::create([
            'nombre_real' => $validated['nombre_real'],
            'username'    => $validated['username'],
            'password'    => Hash::make($validated['password']),
            'rol'         => 'super_admin',
            'estado'      => 1,
        ]);

        ActivityLogger::log(
            'CREAR_SUPER_ADMIN',
            'Auditoría y Seguridad',
            "El Super Administrador " . Auth::user()->username . " creó una nueva cuenta SUPER_ADMIN para: {$nuevoAdmin->username}"
        );

        return back()->with('success', "Cuenta SUPER_ADMIN creada exitosamente para {$nuevoAdmin->nombre_real}.");
    }
}

