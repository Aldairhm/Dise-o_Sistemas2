<?php

namespace App\Http\Controllers;

use App\Models\Atributo;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\ProductoAtributo;
use App\Models\Variante;
use App\Models\VarianteImagen;
use App\Models\VarianteValor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductoPropuestaController extends Controller
{
    public function create()
    {
        return view('productos.propuesta-create', $this->viewData());
    }

    public function edit(Producto $producto)
    {
        $producto->load('productoAtributos.atributo', 'variantes.valores.atributo', 'variantes.imagenes');
        $atributos = $producto->productoAtributos->map(fn (ProductoAtributo $relacion) => [
            'nombre' => $relacion->atributo->nombre,
            'valores' => $producto->variantes->flatMap(fn (Variante $variante) => $variante->valores)
                ->filter(fn ($valor) => $valor->id_atributo === $relacion->id_atributo)
                ->pluck('valor')->unique()->values()->all(),
        ])->values();
        $variantes = $producto->variantes->map(fn (Variante $variante) => [
            'id' => $variante->id,
            'valores' => $variante->valores->map(fn ($valor) => ['atributo' => $valor->atributo->nombre, 'valor' => $valor->valor])->values()->all(),
            'sku' => $variante->sku,
            'precio_venta' => (string) $variante->precio_venta,
            'comision' => (string) $variante->comision,
            'imagenes_existentes' => $variante->imagenes->map(fn ($imagen) => [
                'id' => $imagen->id,
                'url' => asset('storage/' . $imagen->ruta_imagen),
                'nombre' => basename($imagen->ruta_imagen),
            ])->values()->all(),
        ])->values();

        return view('productos.propuesta-create', $this->viewData([
            'modoEdicion' => true,
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'id_categoria' => $producto->id_categoria,
                'descripcion' => $producto->descripcion,
                'comision' => (string) $producto->comision,
                'tiene_variantes' => $variantes->contains(fn (array $variante) => count($variante['valores']) > 0) ? 'si' : 'no',
            ],
            'atributosIniciales' => $atributos,
            'variantesIniciales' => $variantes->all(),
        ]));
    }

    public function store(Request $request)
    {
        return $this->guardar($request);
    }

    public function update(Request $request, Producto $producto)
    {
        return $this->guardar($request, $producto);
    }

    private function guardar(Request $request, ?Producto $producto = null)
    {
        $payload = json_decode($request->input('payload', '{}'), true);
        $validated = validator($payload, [
            'nombre' => ['required', 'string', 'max:150'],
            'id_categoria' => ['required', 'exists:categoria,id'],
            'descripcion' => ['nullable', 'string'],
            'comision_general' => ['required', 'numeric', 'min:0'],
            'atributos' => ['present', 'array'],
            'atributos.*.nombre' => ['required', 'string', 'max:100'],
            'atributos.*.valores' => ['required', 'array'],
            'atributos.*.valores.*' => ['required', 'string', 'max:100'],
            'variantes' => ['required', 'array', 'min:1'],
            'variantes.*.id' => ['nullable', 'integer'],
            'variantes.*.precio_venta' => ['required', 'numeric', 'min:0'],
            'variantes.*.comision' => ['required', 'numeric', 'min:0'],
            'variantes.*.valores' => ['present', 'array'],
            'variantes.*.valores.*.atributo' => ['required', 'string', 'max:100'],
            'variantes.*.valores.*.valor' => ['required', 'string', 'max:100'],
            'variantes.*.imagenes_existentes' => ['nullable', 'array'],
            'variantes.*.imagenes_existentes.*' => ['integer'],
        ])->validate();

        $atributos = collect($validated['atributos'])->map(function (array $atributo) {
            $nombre = trim($atributo['nombre']);
            $valores = collect($atributo['valores'])->map(fn (string $valor) => trim($valor))->filter()->unique(fn (string $valor) => Str::lower($valor))->values();
            return compact('nombre', 'valores');
        });
            $nombresAtributos = $atributos->map(fn (array $atributo) => Str::lower($atributo['nombre']));
        if ($nombresAtributos->count() !== $nombresAtributos->unique()->count()) {
            throw ValidationException::withMessages(['atributos' => 'No puedes repetir el mismo atributo.']);
        }

        return DB::transaction(function () use ($validated, $atributos, $request, $producto) {
            $esNuevo = !$producto;
            $producto ??= new Producto();
            $producto->fill([
                'id_categoria' => $validated['id_categoria'],
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
                'comision' => $validated['comision_general'],
                'estado' => 1,
            ]);
            $producto->save();

            $atributosDb = [];
            foreach ($atributos as $atributoData) {
                $atributo = Atributo::firstOrCreate(['nombre' => $atributoData['nombre']]);
                $atributosDb[Str::lower($atributo->nombre)] = $atributo;
                ProductoAtributo::firstOrCreate(['id_producto' => $producto->id, 'id_atributo' => $atributo->id]);
            }
            ProductoAtributo::where('id_producto', $producto->id)->whereNotIn('id_atributo', collect($atributosDb)->map->id)->delete();

            $idsRecibidos = collect($validated['variantes'])->pluck('id')->filter()->map(fn ($id) => (int) $id);
            if (!$esNuevo) {
                foreach ($producto->variantes()->whereNotIn('id', $idsRecibidos)->get() as $variante) {
                    if ($variante->stock > 0 || $variante->reserva > 0) {
                        throw ValidationException::withMessages(['variantes' => 'No puedes eliminar variantes que todavía tienen existencias.']);
                    }
                    $this->eliminarImagenes($variante->imagenes);
                    $variante->delete();
                }
            }

            $hashes = [];
            foreach ($validated['variantes'] as $indice => $detalle) {
                $valores = collect($detalle['valores'])->map(function (array $valor) use ($atributosDb) {
                    $atributo = $atributosDb[Str::lower(trim($valor['atributo']))] ?? null;
                    if (!$atributo) throw ValidationException::withMessages(['variantes' => 'Una variante usa un atributo que no pertenece al producto.']);
                    return ['id_atributo' => $atributo->id, 'atributo' => $atributo->nombre, 'valor' => trim($valor['valor'])];
                })->values();
                $claves = $valores->map(fn (array $valor) => $valor['id_atributo'] . '=' . Str::lower($valor['valor']))->sort()->values()->all();
                $hash = md5(implode('|', $claves));
                if (in_array($hash, $hashes, true)) throw ValidationException::withMessages(['variantes' => 'Hay combinaciones de atributos repetidas.']);
                $hashes[] = $hash;

                $id = $detalle['id'] ?? null;
                $variante = $id ? $producto->variantes()->findOrFail($id) : new Variante(['stock' => 0, 'reserva' => 0]);
                $sku = $variante->exists && $variante->sku ? $variante->sku : $this->generarSku($producto->id, $indice);
                $variante->fill([
                    'id_producto' => $producto->id,
                    'estado' => 1,
                    'sku' => $sku,
                    'hash_combinacion' => $hash,
                    'nombre_variante' => $valores->pluck('valor')->join(' / ') ?: 'Variante única',
                    'precio_venta' => $detalle['precio_venta'],
                    'comision' => $detalle['comision'],
                ])->save();

                VarianteValor::where('id_variante', $variante->id)->delete();
                foreach ($valores as $valor) VarianteValor::create(['id_variante' => $variante->id, 'id_atributo' => $valor['id_atributo'], 'valor' => $valor['valor']]);
                $this->sincronizarImagenes($request, $variante, $indice, $detalle['imagenes_existentes'] ?? []);
            }

            return response()->json(['success' => true, 'message' => 'Producto y variantes guardados correctamente.', 'producto_id' => $producto->id], $esNuevo ? 201 : 200);
        });
    }

    private function sincronizarImagenes(Request $request, Variante $variante, int $indice, array $idsConservados): void
    {
        $idsConservados = array_map('intval', $idsConservados);
        $this->eliminarImagenes($variante->imagenes()->whereNotIn('id', $idsConservados)->get());
        foreach ($request->file("imagenes.$indice", []) as $imagen) {
            if (!$imagen->isValid() || !in_array($imagen->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)) throw ValidationException::withMessages(['imagenes' => 'Una imagen no es válida.']);
            VarianteImagen::create(['id_variante' => $variante->id, 'ruta_imagen' => $imagen->store('variantes', 'public'), 'es_principal' => $variante->imagenes()->count() === 0 ? 1 : 0]);
        }
    }

    private function eliminarImagenes($imagenes): void
    {
        foreach ($imagenes as $imagen) {
            Storage::disk('public')->delete($imagen->ruta_imagen);
            $imagen->delete();
        }
    }

    private function generarSku(int $productoId, int $indice): string
    {
        do {
            $sku = 'PRD-' . str_pad((string) $productoId, 6, '0', STR_PAD_LEFT) . '-' . str_pad((string) ($indice + 1), 3, '0', STR_PAD_LEFT) . '-' . Str::upper(Str::random(4));
        } while (Variante::where('sku', $sku)->exists());
        return $sku;
    }

    private function viewData(array $extra = []): array
    {
        return array_merge([
            'categorias' => Categoria::query()->orderBy('orden')->orderBy('nombre')->get(['id', 'nombre']),
            'modoEdicion' => false,
            'producto' => null,
            'atributosIniciales' => [],
            'variantesIniciales' => [],
        ], $extra);
    }
}
