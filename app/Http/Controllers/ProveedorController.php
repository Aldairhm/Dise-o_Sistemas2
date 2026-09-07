<?php
// app/Http/Controllers/ProveedorController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Models\Proveedor;
use App\Services\ProveedorService;
use Illuminate\Http\JsonResponse;

class ProveedorController extends Controller
{
    public function index(ProveedorService $service)
    {
        // Traemos los datos y los mandamos a la vista Blade
        $proveedores = $service->listar();

        return view('proveedores.index', compact('proveedores'));
    }

    public function store(StoreProveedorRequest $request, ProveedorService $service): JsonResponse
    {
        $proveedor = $service->crearProveedor($request->validated(), $request->file('archivo'));

        return response()->json([
            'success' => true,
            'message' => 'Proveedor registrado exitosamente.',
            'proveedor' => $proveedor,
        ], 201);
    }

    public function update(UpdateProveedorRequest $request, Proveedor $proveedor, ProveedorService $service): JsonResponse
    {
        $proveedor = $service->actualizarProveedor($proveedor, $request->validated(), $request->file('archivo'));

        return response()->json([
            'success' => true,
            'message' => 'Proveedor actualizado exitosamente.',
            'proveedor' => $proveedor,
        ]);
    }

    // Soft delete (desactivar)
    public function destroy(Proveedor $proveedor, ProveedorService $service): JsonResponse
    {
        $proveedor = $service->desactivar($proveedor);

        return response()->json([
            'success' => true,
            'message' => 'Proveedor desactivado.',
            'proveedor' => $proveedor,
        ]);
    }

    // Restore (activar) — necesita withTrashed en el route model binding
    public function restore($id, ProveedorService $service): JsonResponse
    {
        $proveedor = Proveedor::withTrashed()->findOrFail($id);
        $proveedor = $service->activar($proveedor);

        return response()->json([
            'success' => true,
            'message' => 'Proveedor activado.',
            'proveedor' => $proveedor,
        ]);
    }

    public function show($id, ProveedorService $service)
    {
        // 1. Obtienes el proveedor
        $proveedor = $service->buscar($id);

        // 2. Defines tus dos arrays (pueden venir vacíos o con datos)
        $catalogos = $service->catalogos($id);
        $historial = [];

        return view('proveedores.show', compact('proveedor', 'catalogos','historial'));
    }
}
