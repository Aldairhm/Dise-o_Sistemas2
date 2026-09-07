<?php
namespace App\Services;

use App\Models\Catalogo;
use Illuminate\Support\Facades\Storage;

class CatalogoService
{
    public function guardarRecurso(array $datos)
    {
        $catalogo = new Catalogo();
        $catalogo->id_proveedor = $datos['id_proveedor'];
        $catalogo->nombre_referencia = $datos['nombre_referencia'];

        if ($datos['tipo'] === 'enlace') {
            $catalogo->tipo = 'enlace';
            $catalogo->ruta_destino = $datos['ruta_destino'];
        } else {
            // Lógica para archivos
            $archivo = $datos['archivo'];
            
            // Calculamos el tipo lógico para el frontend según la extensión real
            $extension = strtolower($archivo->getClientOriginalExtension());
            $catalogo->tipo = $this->determinarTipoPorExtension($extension);
            
            // Guardamos físicamente en storage/app/public/catalogos_proveedores
            $ruta = $archivo->store('catalogos_proveedores', 'public');
            $catalogo->ruta_destino = $ruta;
        }

        $catalogo->save();

        return $catalogo;
    }

    private function determinarTipoPorExtension(string $extension): string
    {
        if ($extension === 'pdf') {
            return 'pdf';
        }
        
        if (in_array($extension, ['xls', 'xlsx'])) {
            return 'excel';
        }
        
        if (in_array($extension, ['png', 'jpg', 'jpeg'])) {
            return 'imagen';
        }

        return 'archivo'; // fallback genérico
    }

    public function eliminarRecurso(int $id)
    {
        $catalogo = Catalogo::findOrFail($id);
        
        // Si no es un enlace, eliminamos el archivo físico del servidor
        if ($catalogo->tipo !== 'enlace' && Storage::disk('public')->exists($catalogo->ruta_destino)) {
            Storage::disk('public')->delete($catalogo->ruta_destino);
        }

        $catalogo->delete();
    }
}