<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VarianteValor extends Model
{
    use HasFactory;

    protected $table = 'variantevalor';

    protected $fillable = [
        'id_variante',
        'id_atributo',
        'valor'
    ];

    public function variante()
    {
        return $this->belongsTo(Variante::class, 'id_variante');
    }

    public function atributo()
    {
        return $this->belongsTo(Atributo::class, 'id_atributo');
    }
}
