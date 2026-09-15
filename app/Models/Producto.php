<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categoria;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'producto';

    protected $fillable = [
        'id_categoria',
        'nombre',
        'sku',
        'descripcion',
        'comision',
        'imagen_principal',
        'estado'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function variantes()
    {
        return $this->hasMany(Variante::class, 'id_producto');
    }

    public function productoAtributos()
    {
        return $this->hasMany(ProductoAtributo::class, 'id_producto');
    }
}