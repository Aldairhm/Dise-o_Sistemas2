<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditoriaSesion extends Model
{
    use \App\Models\Concerns\NotifiesBusinessChanges;

    use HasFactory;

    protected $table = 'auditoria_sesiones';

    protected $fillable = [
        'token_sesion',
        'id_super_admin',
        'id_usuario_auditado',
        'justificacion',
        'solo_lectura',
        'estado',
        'ip_origen',
        'user_agent',
        'iniciada_en',
        'expira_en',
        'finalizada_en',
        'notas_cierre',
    ];

    protected $casts = [
        'solo_lectura'  => 'boolean',
        'iniciada_en'   => 'datetime',
        'expira_en'     => 'datetime',
        'finalizada_en' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function superAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_super_admin');
    }

    public function usuarioAuditado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_auditado');
    }

    public function estaVigente(): bool
    {
        return $this->estado === 'activa' && $this->expira_en->isFuture();
    }
}

