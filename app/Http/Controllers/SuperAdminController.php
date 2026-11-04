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
        $notas = $request->input('notas_cierre', 'Auditoría completada exitosamente.');
        ImpersonationService::finalizarAuditoria($notas);

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

        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('prioridad') && $request->prioridad !== 'todos') {
            $query->where('prioridad', $request->prioridad);
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

        $tickets = $query->orderByRaw("
            CASE 
                WHEN estado = 'Escalada' THEN 1
                WHEN estado = 'Pendiente' THEN 2
                WHEN estado = 'En proceso' THEN 3
                ELSE 4
            END
        ")->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('superadmin.tickets', compact('tickets'));
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

