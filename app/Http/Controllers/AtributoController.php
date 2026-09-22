<?php

namespace App\Http\Controllers;

use App\Models\Atributo;
use Illuminate\Http\Request;

class AtributoController extends Controller
{
    public function index()
    {
        $atributos = Atributo::all();
        return view('atributos.index', compact('atributos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:atributo,nombre',
        ], [
            'nombre.unique' => 'Ya existe un atributo con este nombre.',
            'nombre.required' => 'El nombre del atributo es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
        ]);

        $atributo = Atributo::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Atributo creado exitosamente.',
                'atributo' => $atributo
            ]);
        }
        return redirect()->route('atributos.index')->with('success', 'Atributo creado exitosamente.');
    }

    public function update(Request $request, Atributo $atributo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:atributo,nombre,' . $atributo->id,
        ], [
            'nombre.unique' => 'Ya existe un atributo con este nombre.',
            'nombre.required' => 'El nombre del atributo es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
        ]);

        $atributo->update($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Atributo actualizado exitosamente.',
                'atributo' => $atributo
            ]);
        }
        return redirect()->route('atributos.index')->with('success', 'Atributo actualizado exitosamente.');
    }

    public function destroy(Atributo $atributo)
    {
        // En una app real podríamos verificar si está en uso.
        $atributo->delete();

        return redirect()->route('atributos.index')
            ->with('success', 'Atributo eliminado exitosamente.');
    }
}
