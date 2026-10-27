<?php

namespace App\Notifications;

use App\Models\User;
use Spatie\Backup\Notifications\Notifiable as SpatieNotifiable;

class BackupNotifiable extends SpatieNotifiable
{
    /**
     * Determina las direcciones de correo destinatarias para las notificaciones de respaldo.
     * Busca automáticamente a todos los usuarios activos con rol 'admin' en la base de datos.
     *
     * @return string|array<string>
     */
    public function routeNotificationForMail(): string|array
    {
        try {
            // 1. Obtener los correos de todos los administradores activos en el sistema
            $adminEmails = User::where('rol', 'admin')
                ->where('estado', 1)
                ->pluck('username')
                ->filter(fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->values()
                ->toArray();

            if (!empty($adminEmails)) {
                return $adminEmails;
            }
        } catch (\Throwable $e) {
            // Si la base de datos no está disponible o falla, continúa con el fallback
        }

        // 2. Respaldo por defecto desde el .env si no hay admins en BD
        return parent::routeNotificationForMail();
    }
}

