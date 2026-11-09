<?php

namespace App\Models;

use App\Support\CloudStorage;
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
        'stock_cuarentena',
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

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'id_variante');
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class, 'id_variante');
    }

    /**
     * Accesor para obtener la URL de imagen de la variante.
     * Cascada: 1) Imagen principal en variante_imagen -> 2) Cualquier imagen en variante_imagen ->
     *          3) Columna imagen propia -> 4) Imagen principal del producto padre -> null.
     */
    public function getImagenUrlAttribute(): ?string
    {
        $img = $this->imagenes?->firstWhere('es_principal', 1) ?? $this->imagenes?->first();
        if ($img && !empty($img->ruta_imagen)) {
            return CloudStorage::url($img->ruta_imagen);
        }

        if (!empty($this->imagen)) {
            return CloudStorage::url($this->imagen);
        }

        if ($this->producto && !empty($this->producto->imagen_principal)) {
            return CloudStorage::url($this->producto->imagen_principal);
        }

        return null;
    }
}
