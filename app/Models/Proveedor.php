<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Proveedor extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'direccion',
    ];

    public function catalogos()
    {
        return $this->hasMany(Catalogo::class, 'id_proveedor');
    }
}
