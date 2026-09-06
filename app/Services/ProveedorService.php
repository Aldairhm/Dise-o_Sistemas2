<?php
// app/Services/ProveedorService.php

namespace App\Services;

use App\Models\Proveedor;
use Illuminate\Support\Facades\Storage;

class ProveedorService
{
    public function listar()
    {
        return Proveedor::withTrashed()->orderByDesc('created_at')->get();
    }

    public function buscar($id)
    {
        return Proveedor::find($id);
    }

    public function crearProveedor(array $datos, $archivo = null)
    {
        return Proveedor::create($datos);
    }

    public function catalogos($id){
        $proveedor = Proveedor::withTrashed()->find($id);
        if (!$proveedor) {
            return null;
        }
        return $proveedor->catalogos;
    }

    public function actualizarProveedor(Proveedor $proveedor, array $datos, $archivo = null)
    {
        $proveedor->update($datos);

        return $proveedor;
    }

    public function desactivar(Proveedor $proveedor)
    {
        $proveedor->delete(); 
        return $proveedor;
    }

    public function activar(Proveedor $proveedor)
    {
        $proveedor->restore(); 
        return $proveedor;
    }

    // ---------- MÉTODOS PRIVADOS DE APOYO ----------

    private function determinarTipoCatalogo($archivo, ?string $enlace)
    {
        if ($archivo) {
            $extension = strtolower($archivo->getClientOriginalExtension());
            
            if (in_array($extension, ['xls', 'xlsx'])) return 'excel';
            if (in_array($extension, ['png', 'jpg', 'jpeg'])) return 'imagen';
            if ($extension === 'pdf') return 'pdf';
            
            return 'pdf'; // Fallback por defecto
        }

        if (!empty($enlace) && str_starts_with($enlace, 'http')) {
            return 'link';
        }

        return null;
    }

    private function borrarArchivoLocal(?string $ruta)
    {
        if ($ruta && !str_starts_with($ruta, 'http')) {
            Storage::disk('public')->delete($ruta);
        }
    }
}