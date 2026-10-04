<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionDetalle extends Model
{
    protected $table = 'devolucion_detalles';

    protected $fillable = [
        'devolucion_id',
        'id_variante',
        'cantidad'
    ];

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'devolucion_id');
    }

    public function variante()
    {
        return $this->belongsTo(Variante::class, 'id_variante');
    }
}
