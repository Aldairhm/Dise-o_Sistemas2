<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoBodega extends Model
{
    use HasFactory;

    protected $table = 'movimiento_bodega';

    protected $fillable = [
        'id_variante',
        'id_compra',
        'id_usuario',
        'tipo',
        'cantidad',
        'reserva_anterior',
        'reserva_nueva',
        'stock_anterior',
        'stock_nuevo',
        'observacion',
    ];

    public function variante()
    {
        return $this->belongsTo(Variante::class, 'id_variante');
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra');
    }
}