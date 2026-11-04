<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoporteTicketHistorial extends Model
{
    use HasFactory;

    protected $table = 'soporte_ticket_historial';

    protected $fillable = [
        'id_ticket',
        'id_usuario',
        'estado_anterior',
        'estado_nuevo',
        'motivo',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SoporteTicket::class, 'id_ticket');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}

