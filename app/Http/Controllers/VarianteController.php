<?php

namespace App\Http\Controllers;

use App\Models\Variante;
use App\Models\VarianteImagen;
use App\Http\Requests\StoreVarianteRequest;
use App\Http\Requests\UpdateVarianteRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VarianteController extends Controller
{
    public function store(StoreVarianteRequest $request, $producto)
    {
        // La ruta inyecta {producto} — puede ser modelo Eloquent o entero según binding
        $productoId = is_object($producto) ? $producto->id : $producto;

        $validated = $request->validated();

        $validated['id_producto']      = $productoId;
        $validated['estado']           = isset($validated['estado']) ? (int) $validated['estado'] : 1;
        $validated['stock']            = 0;
        $validated['reserva']          = 0;
        // Hash basado en nombre de variante + producto para detectar duplicados lógicos
        $validated['hash_combinacion'] = md5($productoId . ':' . strtolower(trim($validated['nombre_variante'])));

        // Auto-generar SKU si no se proporcionó
        if (empty($validated['sku'])) {
            $validated['sku'] = $this->generarSkuVariante($validated['nombre_variante']);
        }

        $variante = Variante::create($validated);

        if ($request->hasFile('imagenes')) {
            $principalIndex = (int) $request->input('imagen_principal_index', 0);
            foreach (array_values($request->file('imagenes')) as $index => $file) {
                $path = $file->store('variantes', 'public');
                VarianteImagen::create([
                    'id_variante' => $variante->id,
                    'ruta_imagen' => $path,
                    'es_principal' => $index === $principalIndex ? 1 : 0,
                ]);
            }
        }

        if ($request->has('valores')) {
            foreach ($request->input('valores') as $atributoId => $valor) {
                if (!empty($valor)) {
                    \App\Models\VarianteValor::create([
                        'id_variante' => $variante->id,
                        'id_atributo' => $atributoId,
                        'valor'       => $valor,
                    ]);
                }
            }
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Variante creada correctamente.',
            'variante' => $variante->load('imagenes'),
        ]);
    }

    public function show($id)
    {
        $variante = Variante::with('imagenes', 'valores.atributo')->findOrFail($id);
        return response()->json([
            'success'  => true,
            'variante' => $variante,
        ]);
    }

    public function update(UpdateVarianteRequest $request, $id)
    {
        $variante  = Variante::with('producto')->findOrFail($id);
        $validated = $request->validated();

        // Nunca sobreescribir stock, reserva ni costo_promedio desde el formulario
        // El costo_promedio solo se actualiza automáticamente desde las compras
        unset($validated['stock'], $validated['reserva'], $validated['costo_promedio']);

        if (isset($validated['estado'])) {
            $validated['estado'] = (int) $validated['estado'];
        }

        // Actualizar hash si cambió el nombre
        $validated['hash_combinacion'] = md5($variante->id_producto . ':' . strtolower(trim($validated['nombre_variante'])));

        // Conservar SKU actual; solo auto-generar si no tenía y no envió uno nuevo
        if (empty($validated['sku'])) {
            $validated['sku'] = $variante->sku ?: $this->generarSkuVariante($validated['nombre_variante'], $variante->id);
        }

        // Recalcular precio_venta con el costo_promedio real de la BD + ganancia + comisión del producto
        $porcentajeGanancia = isset($validated['porcentaje_ganancia'])
            ? (float) $validated['porcentaje_ganancia']
            : (float) $variante->porcentaje_ganancia;
        $comision = (float) ($variante->producto->comision ?? 0);
        $validated['precio_venta'] = ((float) $variante->costo_promedio * (1 + ($porcentajeGanancia / 100))) + $comision;

        $variante->update($validated);

        // Eliminar imágenes marcadas para borrar
        if ($request->has('imagenes_eliminadas')) {
            foreach ($request->input('imagenes_eliminadas') as $idImg) {
                $img = VarianteImagen::where('id', $idImg)
                                     ->where('id_variante', $variante->id)
                                     ->first();
                if ($img) {
                    Storage::disk('public')->delete($img->ruta_imagen);
                    $img->delete();
                }
            }
        }

        // Imagen existente como principal
        $principalExistenteId = (int) $request->input('imagen_existente_principal_id', 0);
        if ($principalExistenteId > 0) {
            VarianteImagen::where('id_variante', $variante->id)->update(['es_principal' => 0]);
            VarianteImagen::where('id', $principalExistenteId)
                          ->where('id_variante', $variante->id)
                          ->update(['es_principal' => 1]);
        }

        // Nuevas imágenes subidas
        if ($request->hasFile('imagenes')) {
            $principalIndex = (int) $request->input('imagen_principal_index', -1);

            if ($principalIndex >= 0) {
                VarianteImagen::where('id_variante', $variante->id)->update(['es_principal' => 0]);
            }

            foreach (array_values($request->file('imagenes')) as $index => $file) {
                $path = $file->store('variantes', 'public');
                VarianteImagen::create([
                    'id_variante'  => $variante->id,
                    'ruta_imagen'  => $path,
                    'es_principal' => $index === $principalIndex ? 1 : 0,
                ]);
            }
        }

        if ($request->has('valores')) {
            \App\Models\VarianteValor::where('id_variante', $variante->id)->delete();
            foreach ($request->input('valores') as $atributoId => $valor) {
                if (!empty($valor)) {
                    \App\Models\VarianteValor::create([
                        'id_variante' => $variante->id,
                        'id_atributo' => $atributoId,
                        'valor'       => $valor,
                    ]);
                }
            }
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Variante actualizada correctamente.',
            'variante' => $variante->load('imagenes'),
        ]);
    }

    public function destroy($id)
    {
        $variante = Variante::with('imagenes')->findOrFail($id);

        // Verificar si tiene stock o reserva antes de eliminar
        if ($variante->stock > 0 || $variante->reserva > 0) {
            return response()->json([
                'success' => false,
                'message' => "No puedes eliminar \"{$variante->nombre_variante}\" porque aún tiene {$variante->stock} unidad(es) en stock y {$variante->reserva} en reserva. Vacía el inventario primero.",
            ], 422);
        }

        try {
            foreach ($variante->imagenes as $img) {
                Storage::disk('public')->delete($img->ruta_imagen);
            }

            $variante->delete();

            return response()->json([
                'success' => true,
                'message' => 'Variante eliminada correctamente.',
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            // Llave foránea: la variante está referenciada en compras u otras tablas
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar \"{$variante->nombre_variante}\" porque tiene registros de compras asociados. Desactívala en lugar de eliminarla.",
            ], 422);
        }
    }

    public function toggleStatus($id)
    {
        $variante = Variante::findOrFail($id);
        $variante->estado = $variante->estado == 1 ? 0 : 1;
        $variante->save();

        $accion = $variante->estado == 1 ? 'activada' : 'desactivada';

        return response()->json([
            'success'      => true,
            'message'      => "Variante «{$variante->nombre_variante}» {$accion} correctamente.",
            'nuevo_estado' => $variante->estado,
        ]);
    }
    /**
     * Genera un SKU de variante con formato: VAR-NOM-###
     * Ejemplo: VAR-AZU-001  (variante "Azul L")
     *
     * @param string   $nombreVariante
     * @param int|null $excluirId  ID de la variante actual (para ignorarla en el secuencial al editar)
     */
    private function generarSkuVariante(string $nombreVariante, ?int $excluirId = null): string
    {
        // Primeras 3 letras del nombre de la variante (sin tildes, sin espacios)
        $prefijoNombre = strtoupper(substr(
            preg_replace('/[^a-zA-Z]/u', '', Str::ascii($nombreVariante)),
            0, 3
        ));

        $base = 'VAR-' . $prefijoNombre . '-';

        // Buscar el siguiente número secuencial libre
        $numero = 1;
        do {
            $sku = $base . str_pad($numero, 3, '0', STR_PAD_LEFT);
            $query = Variante::where('sku', $sku);
            if ($excluirId) {
                $query->where('id', '!=', $excluirId);
            }
            $existe = $query->exists();
            $numero++;
        } while ($existe);

        return $sku;
    }
}
