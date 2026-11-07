<?php

namespace App\Models;

use App\Support\CloudStorage;
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

    protected $appends = ['url'];

    public function getUrlAttribute(): ?string
    {
        if ($this->tipo === 'enlace') {
            return $this->ruta_destino;
        }

        if (!$this->ruta_destino) {
            return null;
        }

        return CloudStorage::url($this->ruta_destino);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }
}
