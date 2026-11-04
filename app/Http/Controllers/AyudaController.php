<?php

namespace App\Http\Controllers;

use App\Models\SoporteTicket;
use App\Models\SoporteTicketHistorial;
use App\Models\SoporteTicketMensaje;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AyudaController extends Controller
{
    /**
     * Muestra la vista principal del Centro de Ayuda.
     */
    public function index(Request $request): View
    {
        $guides = config('ayuda', []);

        $user = $request->user();
        $role = $user ? $user->rol : 'vendedor';

        // Si no es admin ni super_admin, limitar módulos según rol
        if ($role !== 'admin' && $role !== 'super_admin') {
            unset($guides['inventario'], $guides['devolucion'], $guides['categorias'], $guides['compras'], $guides['proveedores'], $guides['variantes']);
        }

        foreach ($guides as $key => &$item) {
            $item['cases'] = array_filter(
                config('ayuda_casos.'.$key, []),
                static fn (array $case) => !isset($case['roles']) || in_array($role, $case['roles'], true)
            );
        }
        unset($item);

        $topic = $request->query('tema');
        $guide = is_string($topic) ? ($guides[$topic] ?? null) : null;
        abort_if($topic !== null && $guide === null, 404);

        $query = $request->query('q', '');
        $query = is_string($query) ? Str::limit(trim($query), 120, '') : '';
        $normalize = static fn (string $text) => Str::lower(Str::ascii($text));
        $words = preg_split('/[^a-z0-9]+/', $normalize($query), -1, PREG_SPLIT_NO_EMPTY);
        $words = array_diff($words, ['como', 'que', 'en', 'el', 'la', 'los', 'las', 'de', 'del', 'un', 'una', 'a', 'puedo']);
        $scores = [];

        foreach ($guides as $key => $item) {
            $haystack = $normalize(implode(' ', [$item['title'] ?? '', $item['module'] ?? '', $item['description'] ?? '', $item['keywords'] ?? '']));
            if (!empty($item['cases'])) {
                $haystack .= ' ' . $normalize(implode(' ', array_keys($item['cases'])));
            }
            foreach (array_unique($words) as $word) {
                if (str_contains($haystack, $word)) {
                    $scores[$key] = ($scores[$key] ?? 0) + 1;
                }
            }
        }

        arsort($scores);
        $results = [];
        foreach (array_keys($scores) as $key) {
            $results[$key] = $guides[$key];
        }

        return view('ayuda.index', compact('guides', 'guide', 'topic', 'query', 'results'));
    }

    /**
     * Sube una captura o imagen de evidencia a almacenamiento público.
     */
    public function subirEvidencia(Request $request)
    {
        $request->validate([
            'evidencia' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'evidencia.required' => 'Debes seleccionar una imagen.',
            'evidencia.image'    => 'El archivo seleccionado debe ser una imagen válida.',
            'evidencia.mimes'    => 'Formatos soportados: JPG, PNG o WEBP.',
            'evidencia.max'      => 'La imagen no puede pesar más de 5MB.',
        ]);

        $file = $request->file('evidencia');
        $nombre = 'soporte_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('soporte/evidencias', $nombre, 'public');

        return response()->json([
            'success' => true,
            'url' => asset('storage/' . $path),
            'filename' => $nombre,
        ]);
    }

    /**
     * Registra un ticket de consulta de soporte (Vendedor o Administrador).
     */
    public function guardarTicket(Request $request)
    {
        $validated = $request->validate([
            'codigo_ticket'       => ['nullable', 'string', 'max:25'],
            'titulo'              => ['nullable', 'string', 'max:200'],
            'modulo'              => ['required', 'string', 'max:80'],
            'tipo_consulta'       => ['required', 'string', 'max:100'],
            'prioridad'           => ['nullable', 'string', 'in:Baja,Media,Alta,Urgente'],
            'duda'                => ['nullable', 'string', 'max:2000'],
            'evidencia_url'       => ['nullable', 'string', 'max:500'],
            'nivel_escalamiento'  => ['nullable', 'string', 'in:admin,super_admin'],
        ]);

        $user = $request->user();
        $codigo = $validated['codigo_ticket'] ?: ('TK-' . rand(10000, 99999));
        $dudaTexto = !empty(trim($validated['duda'] ?? ''))
            ? trim($validated['duda'])
            : 'Consulta general de asistencia técnica sobre el módulo.';

        // Determinar destinatario jerárquico por rol
        // Si el usuario es ADMIN, su duda va por defecto a SUPER_ADMIN
        $nivelDestino = $user->isAdmin() ? 'super_admin' : 'admin';
        if ($request->filled('nivel_escalamiento')) {
            $nivelDestino = $validated['nivel_escalamiento'];
        }

        $ticket = SoporteTicket::create([
            'codigo_ticket'      => $codigo,
            'titulo'             => $validated['titulo'] ?? Str::limit($dudaTexto, 50),
            'id_usuario'         => $user->id,
            'nombre_usuario'     => $user->nombre_real ?? $user->username,
            'rol_usuario'        => $user->rol,
            'modulo'             => $validated['modulo'],
            'tipo_consulta'      => $validated['tipo_consulta'],
            'prioridad'          => $validated['prioridad'] ?? 'Media',
            'duda'               => $dudaTexto,
            'evidencia_url'      => $validated['evidencia_url'] ?? null,
            'estado'             => 'Pendiente',
            'nivel_escalamiento' => $nivelDestino,
        ]);

        // Guardar mensaje inicial en el hilo
        SoporteTicketMensaje::create([
            'id_ticket'      => $ticket->id,
            'id_usuario'     => $user->id,
            'nombre_usuario' => $user->nombre_real ?? $user->username,
            'rol_usuario'    => $user->rol,
            'mensaje'        => $dudaTexto,
            'evidencia_url'  => $validated['evidencia_url'] ?? null,
            'tipo_mensaje'   => 'publico',
        ]);

        // Registrar en historial
        SoporteTicketHistorial::create([
            'id_ticket'       => $ticket->id,
            'id_usuario'      => $user->id,
            'estado_anterior' => null,
            'estado_nuevo'    => 'Pendiente',
            'motivo'          => 'Apertura de la duda o ticket',
        ]);

        // ── Registro en Bitácora del Sistema ──
        ActivityLogger::log(
            'CREAR',
            'Soporte',
            "Se registró el ticket de soporte #{$ticket->codigo_ticket} sobre '{$ticket->modulo}' por {$ticket->nombre_usuario} [Nivel: {$nivelDestino}].",
            null,
            [
                'codigo'    => $ticket->codigo_ticket,
                'modulo'    => $ticket->modulo,
                'prioridad' => $ticket->prioridad,
                'estado'    => $ticket->estado,
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'ticket'  => $ticket,
                'message' => 'Ticket registrado correctamente.',
            ]);
        }

        return redirect()->route('ayuda.tickets.index')->with('success', 'Ticket creado con éxito.');
    }

    /**
     * Muestra el panel de control y seguimiento de dudas según la jerarquía del usuario.
     */
    public function tickets(Request $request): View
    {
        $user = $request->user();
        $query = SoporteTicket::with(['usuario', 'asignado', 'resolvedor'])->orderByDesc('created_at');

        // SEGURIDAD & PRIVACIDAD JERÁRQUICA:
        // 1. Vendedor: SOLO ve sus propias dudas. No ve dudas ajenas ni internas.
        if ($user->isVendedor()) {
            $query->where('id_usuario', $user->id);
        }
        // 2. Administrador convencional: ve dudas de vendedores + sus propias dudas enviadas al Super Admin
        elseif ($user->rol === 'admin') {
            $query->where(function ($q) use ($user) {
                $q->where('nivel_escalamiento', 'admin')
                  ->orWhere('id_usuario', $user->id);
            });
        }
        // 3. Super Admin: ve todo el sistema (o redirige a su panel exclusivo)

        // Filtro por estado
        $estadoTab = $request->query('estado', 'todos');
        if (in_array($estadoTab, SoporteTicket::ESTADOS, true)) {
            $query->where('estado', $estadoTab);
        }

        // Filtro por módulo
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->query('modulo'));
        }

        // Filtro por prioridad
        if ($request->filled('prioridad') && $request->prioridad !== 'todos') {
            $query->where('prioridad', $request->prioridad);
        }

        // Búsqueda libre
        if ($request->filled('q')) {
            $q = trim($request->query('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('codigo_ticket', 'like', "%{$q}%")
                    ->orWhere('titulo', 'like', "%{$q}%")
                    ->orWhere('nombre_usuario', 'like', "%{$q}%")
                    ->orWhere('modulo', 'like', "%{$q}%")
                    ->orWhere('duda', 'like', "%{$q}%")
                    ->orWhere('resolucion', 'like', "%{$q}%");
            });
        }

        // Conteos por estado para las pestañas
        $baseCounts = SoporteTicket::query();
        if ($user->isVendedor()) {
            $baseCounts->where('id_usuario', $user->id);
        } elseif ($user->rol === 'admin') {
            $baseCounts->where(function ($q) use ($user) {
                $q->where('nivel_escalamiento', 'admin')
                  ->orWhere('id_usuario', $user->id);
            });
        }

        $conteoEstados = [
            'todos'               => (clone $baseCounts)->count(),
            'Pendiente'           => (clone $baseCounts)->where('estado', 'Pendiente')->count(),
            'En proceso'          => (clone $baseCounts)->where('estado', 'En proceso')->count(),
            'Esperando respuesta' => (clone $baseCounts)->where('estado', 'Esperando respuesta')->count(),
            'Escalada'            => (clone $baseCounts)->where('estado', 'Escalada')->count(),
            'Resuelta'            => (clone $baseCounts)->where('estado', 'Resuelta')->count(),
            'Cerrada'             => (clone $baseCounts)->where('estado', 'Cerrada')->count(),
        ];

        $tickets = $query->paginate(15)->withQueryString();
        $modulos = config('ayuda');

        return view('ayuda.tickets', compact('tickets', 'conteoEstados', 'estadoTab', 'modulos'));
    }

    /**
     * Muestra el detalle del ticket con su hilo de conversación y trazabilidad.
     */
    public function verTicket(SoporteTicket $ticket): View
    {
        $user = Auth::user();

        // Control estricto de acceso (Anti-IDOR)
        if ($user->isVendedor() && $ticket->id_usuario !== $user->id) {
            abort(403, 'No tienes autorización para consultar esta duda privada.');
        }

        if ($user->rol === 'admin' && $ticket->nivel_escalamiento === 'super_admin' && $ticket->id_usuario !== $user->id) {
            abort(403, 'Este ticket corresponde a comunicaciones reservadas del Super Administrador.');
        }

        // Cargar mensajes según el nivel de visibilidad
        $mensajesQuery = $ticket->mensajes()->with('usuario');
        if ($user->isVendedor()) {
            // El vendedor NO ve mensajes internos marcados entre admin y super admin
            $mensajesQuery->where('tipo_mensaje', 'publico');
        }
        $mensajes = $mensajesQuery->get();

        $historial = $ticket->historial()->with('usuario')->get();

        return view('ayuda.ver-ticket', compact('ticket', 'mensajes', 'historial'));
    }

    /**
     * Responder en el hilo de la duda
     */
    public function responderTicket(Request $request, SoporteTicket $ticket)
    {
        $user = Auth::user();

        // Validar acceso
        if ($user->isVendedor() && $ticket->id_usuario !== $user->id) {
            abort(403, 'No autorizado.');
        }

        $request->validate([
            'mensaje'       => 'required|string|max:2000',
            'evidencia_url' => 'nullable|string|max:500',
            'tipo_mensaje'  => 'nullable|string|in:publico,interno_admin',
        ]);

        $tipoMensaje = 'publico';
        // Solo administradores o super admins pueden publicar notas internas
        if ($request->tipo_mensaje === 'interno_admin' && ($user->isAdmin() || $user->isSuperAdmin())) {
            $tipoMensaje = 'interno_admin';
        }

        $mensaje = SoporteTicketMensaje::create([
            'id_ticket'      => $ticket->id,
            'id_usuario'     => $user->id,
            'nombre_usuario' => $user->nombre_real ?? $user->username,
            'rol_usuario'    => $user->rol,
            'mensaje'        => trim($request->mensaje),
            'evidencia_url'  => $request->evidencia_url,
            'tipo_mensaje'   => $tipoMensaje,
        ]);

        // Ajustar estado según quién respondió
        if ($user->isVendedor()) {
            if ($ticket->estado === 'Esperando respuesta') {
                $ticket->update(['estado' => 'En proceso']);
            }
        } elseif ($user->isAdmin() || $user->isSuperAdmin()) {
            if ($ticket->estado === 'Pendiente') {
                $ticket->update(['estado' => 'En proceso', 'id_asignado' => $user->id]);
            }
        }

        ActivityLogger::log(
            'RESPUESTA_TICKET',
            'Soporte',
            "{$user->nombre_real} respondió en el ticket #{$ticket->codigo_ticket} ({$tipoMensaje})."
        );

        return back()->with('success', 'Respuesta registrada correctamente.');
    }

    /**
     * Escalar un ticket al Super Administrador (FLUJO C)
     */
    public function escalarTicket(Request $request, SoporteTicket $ticket)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Solo un administrador puede escalar dudas al Super Administrador.');
        }

        $request->validate([
            'justificacion' => 'required|string|min:10|max:1000',
        ], [
            'justificacion.required' => 'Debes proporcionar una justificación para el escalamiento.',
            'justificacion.min'      => 'La justificación debe tener al menos 10 caracteres.',
        ]);

        $estadoAnterior = $ticket->estado;

        $ticket->update([
            'estado'                     => 'Escalada',
            'nivel_escalamiento'         => 'super_admin',
            'escalado_por'               => $user->id,
            'justificacion_escalamiento' => trim($request->justificacion),
        ]);

        // Registrar en historial
        SoporteTicketHistorial::create([
            'id_ticket'       => $ticket->id,
            'id_usuario'      => $user->id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => 'Escalada',
            'motivo'          => "Escalado a Super Admin por {$user->nombre_real}. Motivo: {$request->justificacion}",
        ]);

        // Mensaje interno automático en la conversación
        SoporteTicketMensaje::create([
            'id_ticket'      => $ticket->id,
            'id_usuario'     => $user->id,
            'nombre_usuario' => $user->nombre_real,
            'rol_usuario'    => $user->rol,
            'mensaje'        => "⚠️ CASO ESCALADO AL SUPER ADMINISTRADOR\nJustificación: {$request->justificacion}",
            'tipo_mensaje'   => 'interno_admin',
        ]);

        ActivityLogger::log(
            'ESCALAMIENTO_TICKET',
            'Soporte',
            "El ticket #{$ticket->codigo_ticket} fue escalado al Super Administrador por {$user->nombre_real}."
        );

        return back()->with('success', 'El caso ha sido escalado exitosamente al Super Administrador.');
    }

    /**
     * Actualiza el estado o resolución de un ticket validando la matriz jerárquica de permisos.
     */
    public function actualizarEstadoTicket(Request $request, SoporteTicket $ticket)
    {
        $user = $request->user();

        $validated = $request->validate([
            'estado'     => ['required', 'string', 'in:' . implode(',', SoporteTicket::ESTADOS)],
            'resolucion' => ['nullable', 'string', 'max:2000'],
            'motivo'     => ['nullable', 'string', 'max:500'],
        ]);

        $nuevoEstado = $validated['estado'];

        // Validación estricta de transición en backend
        if (!$ticket->puedeTransicionarA($nuevoEstado, $user)) {
            return response()->json([
                'success' => false,
                'message' => "Transición no permitida de '{$ticket->estado}' a '{$nuevoEstado}' para tu rol actual.",
            ], 422);
        }

        $estadoAnterior = $ticket->estado;
        $ticket->estado = $nuevoEstado;

        if ($request->filled('resolucion')) {
            $ticket->resolucion = trim($validated['resolucion']);
        }

        if ($nuevoEstado === 'Resuelta') {
            $ticket->resuelto_por = $user->id;
            $ticket->fecha_resolucion = now();
        }

        if ($nuevoEstado === 'Cerrada') {
            $ticket->fecha_cerrado = now();
        }

        $ticket->save();

        // Registrar en historial
        SoporteTicketHistorial::create([
            'id_ticket'       => $ticket->id,
            'id_usuario'      => $user->id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => $nuevoEstado,
            'motivo'          => $validated['motivo'] ?? ($ticket->resolucion ?? 'Cambio de estado operativo'),
        ]);

        // ── Registro en Bitácora con Diff ──
        ActivityLogger::log(
            'CAMBIO_ESTADO',
            'Soporte',
            "Ticket de soporte #{$ticket->codigo_ticket} cambió de estado: '{$estadoAnterior}' → '{$nuevoEstado}'.",
            ['estado' => $estadoAnterior],
            [
                'estado'           => $nuevoEstado,
                'resolucion'       => $ticket->resolucion,
                'resuelto_por'     => $user->nombre_real,
                'fecha_resolucion' => $ticket->fecha_resolucion?->toDateTimeString(),
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Ticket #{$ticket->codigo_ticket} actualizado a '{$nuevoEstado}'.",
                'ticket'  => $ticket->fresh(['usuario', 'resolvedor']),
            ]);
        }

        return back()->with('success', "Ticket actualizado a estado '{$nuevoEstado}'.");
    }
}
