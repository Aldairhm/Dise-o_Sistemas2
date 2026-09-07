<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Catalogo extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'ruta_destino',
        'nombre_referencia',
        'id_proveedor',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }
}
