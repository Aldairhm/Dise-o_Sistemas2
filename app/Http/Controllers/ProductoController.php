<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Atributo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Producto::with('categoria')->withCount('variantes');

        if ($request->has('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }

        $productos = $query->get();
        $categorias = Categoria::all();
        return view('productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        $atributos = collect([]); // No mostramos los globales
        return view('productos.create', compact('categorias', 'atributos'));
    }

    public function store(StoreProductoRequest $request)
    {
        $validated = $request->validated();
        $validated['estado'] = isset($validated['estado']) ? (int) $validated['estado'] : 1;

        // Auto-generar SKU basado en categoría + nombre del producto
        if (empty($validated['sku'])) {
            $validated['sku'] = $this->generarSku(
                $validated['id_categoria'],
                $validated['nombre']
            );
        }

        if ($request->hasFile('imagen_principal')) {
            $path = $request->file('imagen_principal')->store('productos', 'public');
            $validated['imagen_principal'] = $path;
        }

        $producto = Producto::create($validated);

        $atributoIds = $request->input('atributos', []);
        
        if ($request->has('nuevos_atributos')) {
            foreach ($request->input('nuevos_atributos') as $nombreAttr) {
                $attr = Atributo::firstOrCreate(['nombre' => $nombreAttr]);
                $atributoIds[] = $attr->id;
            }
        }

        if (count($atributoIds) > 0) {
            $producto->atributos()->sync($atributoIds);
        }

        return redirect()->route('productos.edit', $producto->id)
                         ->with('success', 'Producto creado exitosamente. Ahora puedes agregar las variantes.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::all();
        $producto->load('atributos', 'variantes.imagenes', 'variantes.valores.atributo');
        $atributos = $producto->atributos; // Solo los que ya tiene este producto

        return view('productos.edit', compact('producto', 'categorias', 'atributos'));
    }

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $validated = $request->validated();
        if (isset($validated['estado'])) {
            $validated['estado'] = (int) $validated['estado'];
        }

        // Conservar SKU actual si no se proporcionó uno nuevo
        if (empty($validated['sku'])) {
            $validated['sku'] = $producto->sku ?: $this->generarSku(
                $validated['id_categoria'],
                $validated['nombre'],
                $producto->id
            );
        }

        if ($request->hasFile('imagen_principal')) {
            if ($producto->imagen_principal) {
                Storage::disk('public')->delete($producto->imagen_principal);
            }
            $path = $request->file('imagen_principal')->store('productos', 'public');
            $validated['imagen_principal'] = $path;
        }

        $producto->update($validated);

        $atributoIds = $request->input('atributos', []);
        
        if ($request->has('nuevos_atributos')) {
            foreach ($request->input('nuevos_atributos') as $nombreAttr) {
                $attr = Atributo::firstOrCreate(['nombre' => $nombreAttr]);
                $atributoIds[] = $attr->id;
            }
        }

        if (count($atributoIds) > 0) {
            $producto->atributos()->sync($atributoIds);
        } else {
            $producto->atributos()->detach();
        }

        return redirect()->route('productos.index')
                         ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Genera un SKU con formato: NOM-C-###
     * Ejemplo: CAM-R-001  (Camisa + Ropa)
     *
     * @param int    $idCategoria
     * @param string $nombreProducto
     * @param int|null $excluirId  ID del producto actual (para ignorarlo en el secuencial al editar)
     */
    private function generarSku(int $idCategoria, string $nombreProducto, ?int $excluirId = null): string
    {
        $categoria = Categoria::find($idCategoria);

        // Primeras 3 letras del nombre del producto
        $prefijoNombre = strtoupper(substr(
            preg_replace('/[^a-zA-Z]/u', '', Str::ascii($nombreProducto)),
            0, 3
        ));

        // Solo la primera letra de la categoría
        $letraCategoria = strtoupper(substr(
            preg_replace('/[^a-zA-Z]/u', '', Str::ascii($categoria->nombre ?? 'G')),
            0, 1
        ));

        $base = $prefijoNombre . '-' . $letraCategoria . '-';

        // Buscar el siguiente número secuencial libre para esa base
        $numero = 1;
        do {
            $sku = $base . str_pad($numero, 3, '0', STR_PAD_LEFT);
            $query = Producto::where('sku', $sku);
            if ($excluirId) {
                $query->where('id', '!=', $excluirId);
            }
            $existe = $query->exists();
            $numero++;
        } while ($existe);

        return $sku;
    }

    public function destroy(Producto $producto)
    {
        // Bloquear eliminación si el producto tiene variantes activas (estado = 1)
        $variantesActivas = $producto->variantes()->where('estado', 1)->count();

        if ($variantesActivas > 0) {
            return redirect()->route('productos.index')
                             ->with('error', "No se puede eliminar el producto porque tiene {$variantesActivas} variante(s) activa(s). Desactívalas primero.");
        }

        $producto->load('variantes.imagenes');

        if ($producto->imagen_principal) {
            Storage::disk('public')->delete($producto->imagen_principal);
        }

        foreach ($producto->variantes as $variante) {
            foreach ($variante->imagenes as $imagen) {
                Storage::disk('public')->delete($imagen->ruta_imagen);
            }
        }

        $producto->delete();

        return redirect()->route('productos.index')
                         ->with('success', 'Producto eliminado exitosamente.');
    }

    public function toggleStatus(Producto $producto)
    {
        $producto->estado = $producto->estado == 1 ? 0 : 1;
        $producto->save();

        $accion = $producto->estado == 1 ? 'activado' : 'desactivado';

        return response()->json([
            'success'      => true,
            'message'      => "Producto «{$producto->nombre}» {$accion} correctamente.",
            'nuevo_estado' => $producto->estado,
        ]);
    }
}
