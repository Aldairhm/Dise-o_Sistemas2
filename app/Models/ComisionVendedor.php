<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComisionVendedor extends Model
{
    public $timestamps = false;
    protected $table = 'comision_vendedor';

    protected $fillable = [
        'id_vendedor','id_salida','monto','porcentaje',
        'estado','notas','liquidado_por','fecha_liquidacion','fecha_registro',
    ];

    protected $casts = [
        'monto'             => 'decimal:2',
        'porcentaje'        => 'decimal:2',
        'fecha_registro'    => 'datetime',
        'fecha_liquidacion' => 'datetime',
    ];

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_vendedor');
    }

    public function salida(): BelongsTo
    {
        return $this->belongsTo(Salida::class, 'id_salida');
    }

    public function liquidadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'liquidado_por');
    }

    public function scopePendientes($query) { return $query->where('estado','Pendiente'); }
    public function scopePagadas($query)    { return $query->where('estado','Pagada'); }
    public function scopeCanceladas($query) { return $query->where('estado','Cancelada'); }
    public function scopeDeVendedor($query, int $id) { return $query->where('id_vendedor',$id); }
}
