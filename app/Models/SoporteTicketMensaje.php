<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoporteTicketMensaje extends Model
{
    use \App\Models\Concerns\NotifiesBusinessChanges;

    use HasFactory;

    protected $table = 'soporte_ticket_mensajes';

    protected $fillable = [
        'id_ticket',
        'id_usuario',
        'nombre_usuario',
        'rol_usuario',
        'mensaje',
        'evidencia_url',
        'tipo_mensaje',
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

