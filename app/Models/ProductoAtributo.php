<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoAtributo extends Model
{
    use HasFactory;

    protected $table = 'productoatributo';

    protected $fillable = [
        'id_producto',
        'id_atributo'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function atributo()
    {
        return $this->belongsTo(Atributo::class, 'id_atributo');
    }
}
