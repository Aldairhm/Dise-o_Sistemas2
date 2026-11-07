<?php

namespace App\Models;

use App\Support\CloudStorage;
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

    protected $appends = ['url'];

    public function getUrlAttribute(): ?string
    {
        return CloudStorage::url($this->ruta_imagen);
    }

    public function variante()
    {
        return $this->belongsTo(Variante::class, 'id_variante');
    }
}
