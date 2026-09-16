<?php

namespace App\Http\Controllers;

use App\Models\Variante;
use App\Models\VarianteImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VarianteController extends Controller
{
    public function store(Request $request, $productoId)
    {
        $validated = $request->validate([
            'sku' => 'nullable|string|max:100',
            'nombre_variante' => 'required|string|max:200',
            'precio_venta' => 'required|numeric',
            'stock' => 'required|integer',
            'reserva' => 'required|integer',
            'estado' => 'nullable|integer|in:0,1'
        ]);

        $validated['id_producto'] = $productoId;
        // Simulando generación de hash para combinación
        $validated['hash_combinacion'] = md5(uniqid());

        $variante = Variante::create($validated);

        // Si se suben imágenes mediante Dropzone/input file
        if ($request->hasFile('imagenes')) {
            $principalIndex = (int) $request->input('imagen_principal_index', 0);
            foreach (array_values($request->file('imagenes')) as $index => $file) {
                $path = $file->store('variantes', 'public');
                VarianteImagen::create([
                    'id_variante' => $variante->id,
                    'ruta_imagen' => $path,
                    'es_principal' => $index === $principalIndex ? 1 : 0
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Variante creada correctamente.',
            'variante' => $variante->load('imagenes')
        ]);
    }

    public function show($id)
    {
        $variante = Variante::with('imagenes', 'valores.atributo')->findOrFail($id);
        return response()->json([
            'success' => true,
            'variante' => $variante
        ]);
    }

    public function update(Request $request, $id)
    {
        $variante = Variante::findOrFail($id);

        $validated = $request->validate([
            'sku' => 'nullable|string|max:100',
            'nombre_variante' => 'required|string|max:200',
            'precio_venta' => 'required|numeric',
            'stock' => 'required|integer',
            'reserva' => 'required|integer',
            'estado' => 'nullable|integer|in:0,1'
        ]);

        $variante->update($validated);

        // Procesar imágenes eliminadas
        if ($request->has('imagenes_eliminadas')) {
            $eliminadas = $request->input('imagenes_eliminadas');
            foreach ($eliminadas as $idImg) {
                $img = VarianteImagen::where('id', $idImg)->where('id_variante', $variante->id)->first();
                if ($img) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($img->ruta_imagen);
                    $img->delete();
                }
            }
        }

        // Si se eligió una imagen existente como principal
        $principalExistenteId = (int) $request->input('imagen_existente_principal_id', 0);
        if ($principalExistenteId > 0) {
            // Quitar principal a todas y poner a la elegida
            VarianteImagen::where('id_variante', $variante->id)->update(['es_principal' => 0]);
            VarianteImagen::where('id', $principalExistenteId)->where('id_variante', $variante->id)->update(['es_principal' => 1]);
        }

        if ($request->hasFile('imagenes')) {
            $principalIndex = (int) $request->input('imagen_principal_index', -1);
            
            // Si el usuario eligió una nueva imagen como principal, desmarcamos las existentes
            if ($principalIndex >= 0) {
                VarianteImagen::where('id_variante', $variante->id)->update(['es_principal' => 0]);
            }

            foreach (array_values($request->file('imagenes')) as $index => $file) {
                $path = $file->store('variantes', 'public');
                VarianteImagen::create([
                    'id_variante' => $variante->id,
                    'ruta_imagen' => $path,
                    'es_principal' => $index === $principalIndex ? 1 : 0
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Variante actualizada correctamente.',
            'variante' => $variante->load('imagenes')
        ]);
    }

    public function destroy($id)
    {
        $variante = Variante::findOrFail($id);
        
        // Eliminar imagenes fisicas si las hay
        foreach ($variante->imagenes as $img) {
            Storage::disk('public')->delete($img->ruta_imagen);
        }
        
        $variante->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variante eliminada correctamente.'
        ]);
    }
}
