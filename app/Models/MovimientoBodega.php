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
        'devolucion_id',
        'id_usuario',
        'tipo',
        'cantidad',
        'reserva_anterior',
        'reserva_nueva',
        'stock_anterior',
        'stock_nuevo',
        'stock_cuarentena_anterior',
        'stock_cuarentena_nuevo',
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

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'devolucion_id');
    }
}