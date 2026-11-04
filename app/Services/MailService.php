<?php

namespace App\Services;

use App\Mail\BackupNotificationMail;
use App\Mail\ComisionPagadaMail;
use App\Mail\DatabaseRestoredMail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * MailService — Servicio centralizado y unificado para el envío de todos los
 * correos electrónicos del sistema AXStore (Notificaciones de respaldos, restauraciones,
 * liquidación de comisiones y restablecimiento de contraseñas).
 */
class MailService
{
    /**
     * Obtiene los correos de todos los administradores activos en el sistema.
     *
     * @return array<string>
     */
    public static function getAdminEmails(): array
    {
        try {
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
            Log::warning('MailService: Error al consultar administradores en BD: ' . $e->getMessage());
        }

        // Fallback a variables de entorno si la BD no responde o no hay administradores registrados
        $fallback = config('backup.notifications.mail.to');
        if (is_array($fallback)) {
            $emails = array_values(array_filter($fallback));
            if (!empty($emails)) {
                return $emails;
            }
        } elseif (!empty($fallback)) {
            return [$fallback];
        }

        $envBackupEmails = env('BACKUP_NOTIFY_EMAILS', env('BACKUP_NOTIFY_EMAIL'));
        if ($envBackupEmails) {
            return array_values(array_filter(array_map('trim', explode(',', $envBackupEmails))));
        }

        $fromAddress = config('mail.from.address');
        return $fromAddress ? [$fromAddress] : [];
    }

    /**
     * Envía la notificación unificada de creación de respaldo (éxito o fallo) a los administradores.
     */
    public static function sendBackupNotification(
        bool $isSuccess,
        string $fileName = 'No disponible',
        string $fileSize = '0 KB',
        ?string $errorMessage = null,
        string $disk = 'Servidor Local y Google Drive'
    ): bool {
        try {
            $adminEmails = self::getAdminEmails();

            if (empty($adminEmails)) {
                Log::warning('MailService: No hay correos de administradores configurados para notificación de respaldo.');
                return false;
            }

            $mailable = new BackupNotificationMail(
                isSuccess: $isSuccess,
                nombreArchivo: $fileName,
                tamano: $fileSize,
                fecha: now()->format('d/m/Y H:i:s'),
                destino: $disk,
                detallesError: $errorMessage
            );

            Mail::to($adminEmails)->send($mailable);

            Log::info("MailService: Notificación de respaldo (" . ($isSuccess ? 'ÉXITO' : 'FALLO') . ") enviada a: " . implode(', ', $adminEmails));
            return true;
        } catch (\Throwable $e) {
            Log::error('MailService: Error al enviar correo de notificación de respaldo: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envía la notificación unificada de restauración de base de datos a los administradores.
     */
    public static function sendDatabaseRestorationNotification(
        bool $isSuccess,
        string $origin,
        ?string $errorMessage = null,
        ?Request $request = null
    ): bool {
        try {
            $adminEmails = self::getAdminEmails();

            if (empty($adminEmails)) {
                Log::warning('MailService: No hay correos de administradores configurados para notificación de restauración.');
                return false;
            }

            $user = $request?->user();

            $mailable = new DatabaseRestoredMail(
                isSuccess: $isSuccess,
                usuarioNombre: $user?->nombre_real ?? 'Administrador',
                usuarioEmail: $user?->username ?? 'admin',
                usuarioRol: ucfirst($user?->rol ?? 'admin'),
                origen: $origin,
                fecha: now()->format('d/m/Y H:i:s'),
                ip: $request?->ip() ?: '127.0.0.1',
                detallesError: $errorMessage
            );

            Mail::to($adminEmails)->send($mailable);

            Log::info("MailService: Notificación de restauración de BD (" . ($isSuccess ? 'ÉXITO' : 'FALLO') . ") enviada a: " . implode(', ', $adminEmails));
            return true;
        } catch (\Throwable $e) {
            Log::error('MailService: Error al enviar correo de notificación de restauración: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envía el correo unificado de restablecimiento de contraseña.
     */
    public static function sendPasswordReset(User $user, string $resetUrl, string $expiresAt): array
    {
        $recipientEmail = filter_var($user->username ?? '', FILTER_VALIDATE_EMAIL) ? $user->username : null;
        if (!$recipientEmail) {
            $recipientEmail = config('mail.from.address');
        }

        if (!$recipientEmail) {
            return [
                'success' => false,
                'message' => 'El usuario no tiene una dirección de correo válida.',
            ];
        }

        try {
            Mail::to($recipientEmail)->send(new ResetPasswordMail($user, $resetUrl, $expiresAt));
            Log::info("MailService: Correo de restablecimiento enviado exitosamente a {$recipientEmail}");

            return [
                'success' => true,
                'message' => "Correo enviado con éxito a {$recipientEmail}.",
            ];
        } catch (\Throwable $e) {
            Log::warning("MailService: Falló envío con Laravel Mail ({$e->getMessage()}), intentando PHPMailer...");

            try {
                $phpMailer = new PHPMailerService();
                return $phpMailer->sendResetPasswordEmail($user, $resetUrl, $expiresAt, $recipientEmail);
            } catch (\Throwable $th) {
                Log::error("MailService: Falló también PHPMailer: " . $th->getMessage());
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }
    }

    /**
     * Envía el correo unificado de liquidación de comisión al vendedor.
     */
    public static function sendCommissionPaid(
        User $vendedor,
        $comisiones,
        string $metodoPago = 'Efectivo',
        ?string $referenciaPago = null,
        ?string $notas = null,
        ?string $comprobantePath = null
    ): bool {
        $recipientEmail = filter_var($vendedor->username ?? '', FILTER_VALIDATE_EMAIL) ? $vendedor->username : null;

        if (!$recipientEmail) {
            Log::warning("MailService: El vendedor ID {$vendedor->id} no tiene un correo válido registrado.");
            return false;
        }

        try {
            Mail::to($recipientEmail)->send(
                new ComisionPagadaMail($vendedor, $comisiones, $metodoPago, $referenciaPago, $notas, $comprobantePath)
            );

            Log::info("MailService: Correo de liquidación de comisiones enviado a: {$recipientEmail}");
            return true;
        } catch (\Throwable $e) {
            Log::error("MailService: Error al enviar correo de liquidación al vendedor {$recipientEmail}: " . $e->getMessage());
            return false;
        }
    }
}

