<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Producto::with('categoria')->withCount('variantes');
        
        if ($request->has('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }
        
        $productos = $query->get();
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'id_categoria' => 'required|exists:categoria,id',
            'descripcion' => 'nullable|string',
            'comision' => 'required|numeric',
            'estado' => 'required|integer|in:0,1',
            'imagen_principal' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sku' => 'nullable|string|max:50|unique:producto,sku'
        ]);

        if (empty($validated['sku'])) {
            $validated['sku'] = 'PROD-' . strtoupper(\Illuminate\Support\Str::random(8));
        }

        if ($request->hasFile('imagen_principal')) {
            $path = $request->file('imagen_principal')->store('productos', 'public');
            $validated['imagen_principal'] = $path;
        }

        $producto = Producto::create($validated);

        // Redirigir a edición para agregar variantes
        return redirect()->route('productos.edit', $producto->id)
                         ->with('success', 'Producto creado exitosamente. Ahora puedes agregar las variantes.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::all();
        // Cargar variantes del producto para mostrar en la tabla inferior
        $producto->load('variantes.imagenes', 'variantes.valores.atributo');
        
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'id_categoria' => 'required|exists:categoria,id',
            'descripcion' => 'nullable|string',
            'comision' => 'required|numeric',
            'estado' => 'required|integer|in:0,1',
            'imagen_principal' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sku' => 'nullable|string|max:50|unique:producto,sku,' . $producto->id
        ]);

        if (empty($validated['sku'])) {
            $validated['sku'] = $producto->sku ?: 'PROD-' . strtoupper(\Illuminate\Support\Str::random(8));
        }

        if ($request->hasFile('imagen_principal')) {
            // Delete old image if exists
            if ($producto->imagen_principal) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($producto->imagen_principal);
            }
            $path = $request->file('imagen_principal')->store('productos', 'public');
            $validated['imagen_principal'] = $path;
        }

        $producto->update($validated);

        return redirect()->route('productos.index')
                         ->with('success', 'Producto actualizado exitosamente.');
    }
}
