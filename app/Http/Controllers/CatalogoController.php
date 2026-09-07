<?php
namespace App\Http\Controllers;

use App\Http\Requests\CatalogoRequest;
use App\Services\CatalogoService;
use Illuminate\Http\JsonResponse;

class CatalogoController extends Controller
{
    protected $catalogoService;

    // Inyección de dependencias del servicio
    public function __construct(CatalogoService $catalogoService)
    {
        $this->catalogoService = $catalogoService;
    }

    public function store(CatalogoRequest $request): JsonResponse
    {
        // $request->validated() devuelve solo los datos limpios que pasaron las reglas
        $catalogo = $this->catalogoService->guardarRecurso($request->validated());

        return response()->json([
            'message' => 'Recurso guardado correctamente',
            'catalogo' => $catalogo
        ], 201);
    }

    public function destroy($id): JsonResponse
    {
        $this->catalogoService->eliminarRecurso($id);

        return response()->json([
            'message' => 'Recurso eliminado correctamente'
        ], 200);
    }
}