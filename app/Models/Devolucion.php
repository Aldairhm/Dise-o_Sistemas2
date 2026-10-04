<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    protected $fillable = [
        'origen_tipo',
        'origen_id',
        'motivo',
        'tipo_resolucion',
        'estado',
        'detalles_json',
        'monto_reembolsado',
        'comprobante'
    ];

    public function detalles()
    {
        return $this->hasMany(DevolucionDetalle::class, 'devolucion_id');
    }

    protected $casts = [
        'detalles_json' => 'array',
        'monto_reembolsado' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'origen_id');
    }
}
