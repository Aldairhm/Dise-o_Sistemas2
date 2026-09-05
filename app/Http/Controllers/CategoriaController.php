<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $estado = $request->get('estado', 'activas'); // activas | inactivos | todas

        $query = Categoria::withCount('productos');

        if ($estado === 'activas') {
            $query->where('estado', true);
        } elseif ($estado === 'inactivos') {
            $query->where('estado', false);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        // Ordenamos por el campo 'orden' primero, y luego alfabéticamente
        $categorias = $query->orderBy('orden')->orderBy('nombre')->get();

        $stats = [
            'total'     => Categoria::count(),
            'activas'   => Categoria::where('estado', true)->count(),
            'inactivos' => Categoria::where('estado', false)->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'table' => view('categorias.partials.table', compact('categorias', 'estado'))->render(),
                'cards' => view('categorias.partials.cards', compact('categorias', 'estado'))->render(),
                'stats' => $stats,
            ]);
        }

        return view('categorias.index', compact('categorias', 'stats', 'search', 'estado'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100|unique:categoria,nombre',
            'descripcion' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
            'icono'       => 'nullable|string|max:50',
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique'   => 'Ya existe una categoría con ese nombre.',
            'nombre.max'      => 'El nombre no puede superar los 100 caracteres.',
        ]);

        $maxOrden = Categoria::max('orden') ?? 0;

        Categoria::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'color'       => $request->color ?? '#3b82f6',
            'icono'       => $request->icono ?? 'fa-tag',
            'estado'      => true,
            'orden'       => $maxOrden + 1,
        ]);

        return response()->json(['success' => true, 'message' => 'Categoría creada correctamente.']);
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100|unique:categoria,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
            'icono'       => 'nullable|string|max:50',
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique'   => 'Ya existe otra categoría con ese nombre.',
            'nombre.max'      => 'El nombre no puede superar los 100 caracteres.',
        ]);

        $categoria->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'color'       => $request->color ?? $categoria->color,
            'icono'       => $request->icono ?? $categoria->icono,
        ]);

        return response()->json(['success' => true, 'message' => 'Categoría actualizada correctamente.']);
    }

    public function toggleStatus(Categoria $categoria)
    {
        $categoria->estado = !$categoria->estado;
        $categoria->save();

        $action = $categoria->estado ? 'activada' : 'inactivada';
        return response()->json([
            'success' => true,
            'message' => "Categoría «{$categoria->nombre}» $action correctamente."
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:categoria,id',
        ]);

        foreach ($request->order as $index => $id) {
            Categoria::where('id', $id)->update(['orden' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Orden actualizado correctamente.']);
    }

    public function exportPdf(Request $request)
    {
        $estado = $request->get('estado', 'todas');
        $query  = Categoria::withCount('productos')->orderBy('orden')->orderBy('nombre');

        if ($estado === 'activas') {
            $query->where('estado', true);
        } elseif ($estado === 'inactivos') {
            $query->where('estado', false);
        }

        $categorias = $query->get();

        $stats = [
            'total'     => Categoria::count(),
            'activas'   => Categoria::where('estado', true)->count(),
            'inactivos' => Categoria::where('estado', false)->count(),
        ];

        $totalProductos = $categorias->sum('productos_count');
        $user = Auth::user();
        $usuario = $user ? ($user->nombre_real ?? $user->username ?? 'Administrador') : 'Sistema AXStore';
        $fecha = date('d/m/Y h:i A');

        $logoBase64 = '';
        if (extension_loaded('gd')) {
            $logoPath = public_path('assets/images/logo.png');
            if (file_exists($logoPath)) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $pdf = Pdf::loadView('pdf.categorias', compact(
            'categorias', 'estado', 'stats', 'totalProductos', 'usuario', 'fecha', 'logoBase64'
        ))->setPaper('a4', 'portrait');

        $fileName = 'Reporte_Categorias_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }
}
