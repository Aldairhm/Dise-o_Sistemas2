<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VarianteImagen extends Model
{
    use HasFactory;

    protected $table = 'variante_imagen';

    protected $fillable = [
        'id_variante',
        'ruta_imagen',
        'es_principal'
    ];

    public function variante()
    {
        return $this->belongsTo(Variante::class, 'id_variante');
    }
}
