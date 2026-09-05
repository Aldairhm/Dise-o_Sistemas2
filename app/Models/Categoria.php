<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria';

    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion', 'color', 'estado', 'icono', 'orden'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }
}
