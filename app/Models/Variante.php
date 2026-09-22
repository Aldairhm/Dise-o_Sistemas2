<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variante extends Model
{
    use HasFactory;

    protected $table = 'variante';

    protected $fillable = [
        'id_producto',
        'estado',
        'sku',
        'nombre_variante',
        'costo_promedio',
        'porcentaje_ganancia',
        'precio_venta',
        'comision',
        'stock',
        'reserva',
        'hash_combinacion'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function imagenes()
    {
        return $this->hasMany(VarianteImagen::class, 'id_variante');
    }

    public function valores()
    {
        return $this->hasMany(VarianteValor::class, 'id_variante');
    }
}
