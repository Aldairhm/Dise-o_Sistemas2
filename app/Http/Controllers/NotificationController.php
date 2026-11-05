<?php

namespace App\Http\Controllers;

use App\Models\{AuditoriaSesion, ComisionVendedor, Devolucion, NotificationDelivery, SoporteTicket, User, Venta};
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private function inbox(Request $request)
    {
        abort_if($request->session()->has('auditoria_token'), 403, 'Las notificaciones privadas no están disponibles durante una auditoría.');
        abort_unless((int) $request->user()->estado === 1, 403);
        $user = $request->user();
        return NotificationDelivery::where('user_id', $user->id)->whereHas('event', function ($q) use ($user) {
            if (!$user->isSuperAdmin()) {
                $q->whereNotIn('resource_type', ['auditoria', 'usuarios', 'backup']);
            }
            if (!$user->isAdmin()) {
                $q->where('resource_type', '!=', 'devolucion');
            }
            $q->where(function ($q) use ($user) {
                $q->where('resource_type', '!=', 'ticket')->orWhere(function ($q) use ($user) {
                    $q->where('resource_type', 'ticket')->whereIn('resource_id', SoporteTicket::query()->select('id')->where(function ($q) use ($user) {
                        if (!$user->isSuperAdmin()) {
                            $q->where('id_usuario', $user->id);
                            if ($user->rol === 'admin') {
                                $q->orWhere('nivel_escalamiento', 'admin');
                            }
                        }
                    }));
                    if (!$user->isAdmin()) {
                        $q->where(function ($q) { $q->whereNull('context->internal')->orWhere('context->internal', false); });
                    }
                });
            });
        });
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:all,unread,read,archived',
            'category' => 'nullable|in:ventas,comisiones,ayuda,auditorias,seguridad,administracion',
            'priority' => 'nullable|in:informativa,importante,alta,critica',
            'q' => 'nullable|string|max:120', 'order' => 'nullable|in:asc,desc',
        ]);
        $query = $this->inbox($request)->with('event');
        ($validated['status'] ?? '') === 'archived' ? $query->whereNotNull('archived_at') : $query->whereNull('archived_at');
        if (($validated['status'] ?? '') === 'unread') { $query->whereNull('read_at'); }
        if (($validated['status'] ?? '') === 'read') { $query->whereNotNull('read_at'); }
        $query->whereHas('event', function ($q) use ($validated) {
            foreach (['category', 'priority'] as $field) {
                if (!empty($validated[$field])) { $q->where($field, $validated[$field]); }
            }
            if (!empty($validated['q'])) {
                $term = $validated['q'];
                $q->where(fn ($q) => $q->where('title', 'like', "%{$term}%")->orWhere('message', 'like', "%{$term}%"));
            }
        });
        $notifications = $query->orderBy('id', $validated['order'] ?? 'desc')->paginate(20)->withQueryString();
        return view('notifications.index', compact('notifications'));
    }

    public function feed(Request $request)
    {
        $query = $this->inbox($request)->whereNull('archived_at');
        $unread = (clone $query)->whereNull('read_at')->count();
        $items = $query->with('event')->orderByDesc('id')->limit(8)->get()->map(fn ($item) => [
            'id' => $item->id, 'title' => $item->event->title, 'message' => $item->event->message,
            'category' => $item->event->category, 'priority' => $item->event->priority,
            'date' => $item->event->created_at->toIso8601String(), 'read' => $item->read_at !== null,
            'open' => route('notifications.open', $item->id, false),
        ]);
        return response()->json(['unread' => $unread, 'items' => $items])->header('Cache-Control', 'private, no-store');
    }

    public function read(Request $request, int $id)
    {
        $item = $this->inbox($request)->findOrFail($id);
        if (!$item->read_at) { $item->update(['read_at' => now()]); }
        return $request->expectsJson() ? response()->json(['ok' => true]) : back()->with('success', 'Notificación leída.');
    }

    public function readAll(Request $request)
    {
        $this->inbox($request)->whereNull('read_at')->whereNull('archived_at')->update(['read_at' => now()]);
        return $request->expectsJson() ? response()->json(['ok' => true]) : back()->with('success', 'Notificaciones marcadas como leídas.');
    }

    public function archive(Request $request, int $id)
    {
        $this->inbox($request)->findOrFail($id)->update(['archived_at' => now()]);
        return back()->with('success', 'Notificación archivada.');
    }

    public function open(Request $request, int $id, NotificationService $service)
    {
        $event = $this->inbox($request)->with('event')->findOrFail($id)->event;
        $user = $request->user();
        $resourceId = $event->resource_id;
        // Destinations are generated from this allowlist. No URL from clients or stored messages is used.
        $destination = null;
        switch ($event->resource_type) {
            case 'venta':
                $sale = Venta::find($resourceId);
                if ($sale && ($user->rol === 'admin' || (int) $sale->id_usuario === (int) $user->id)) {
                    $destination = route('ventas.show', $sale->id);
                }
                break;
            case 'comision':
                $commission = ComisionVendedor::find($resourceId);
                if ($commission && ($user->rol === 'admin' || (int) $commission->id_vendedor === (int) $user->id)) {
                    $destination = route('comisiones.index', ['notification_commission' => $commission->id]);
                }
                break;
            case 'ticket':
                $ticket = SoporteTicket::find($resourceId);
                if ($ticket && $service->canSeeTicket($user, $ticket) && (empty($event->context['internal']) || $user->isAdmin())) {
                    $destination = route('ayuda.tickets.show', $ticket->id);
                }
                break;
            case 'cuenta': $destination = route('perfil.show'); break;
            case 'auditoria':
                if ($user->isSuperAdmin() && AuditoriaSesion::find($resourceId)) { $destination = route('superadmin.auditorias.historial'); }
                break;
            case 'usuarios':
                if ($user->isSuperAdmin() && User::find($resourceId)) { $destination = route('superadmin.usuarios'); }
                break;
            case 'backup':
                if ($user->isSuperAdmin()) { $destination = route('backups.index'); }
                break;
            case 'devolucion':
                if ($user->isAdmin() && Devolucion::find($resourceId)) { $destination = route('compras.devoluciones'); }
                break;
        }
        return $destination ? redirect()->to($destination) : redirect()->route('notifications.index')->with('error', 'El recurso ya no está disponible o no tienes permiso para consultarlo.');
    }
}
