<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Illuminate\Support\Facades\Log;

class PHPMailerService
{
    /**
     * Instancia configurada de PHPMailer
     */
    protected function createMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);

        // Configuración del Servidor SMTP
        $mail->isSMTP();
        $mail->CharSet  = 'UTF-8';
        $username       = (string) env('MAIL_USERNAME');
        $password       = (string) env('MAIL_PASSWORD');
        $host           = env('MAIL_HOST');
        $port           = env('MAIL_PORT');
        $encryption     = strtolower((string) env('MAIL_ENCRYPTION', 'tls'));

        // Auto-detección inteligente si el host no se especificó o se dejó el valor por defecto
        if (empty($host) || $host === 'smtp.gmail.com') {
            $userLower = strtolower($username);
            if (str_ends_with($userLower, '@icloud.com') || str_ends_with($userLower, '@me.com') || str_ends_with($userLower, '@mac.com')) {
                $host = 'smtp.mail.me.com';
                $port = $port ?: 587;
            } elseif (str_ends_with($userLower, '@outlook.com') || str_ends_with($userLower, '@hotmail.com') || str_ends_with($userLower, '@live.com')) {
                $host = 'smtp.office365.com';
                $port = $port ?: 587;
            } elseif (str_ends_with($userLower, '@yahoo.com') || str_ends_with($userLower, '@yahoo.es')) {
                $host = 'smtp.mail.yahoo.com';
                $port = $port ?: 587;
            } else {
                $host = $host ?: 'smtp.gmail.com';
                $port = $port ?: 587;
            }
        }

        $mail->Host     = $host;
        $mail->Port     = (int) ($port ?: 587);
        $mail->SMTPAuth = !empty($username);
        $mail->Username = $username;
        $mail->Password = $password;

        // Encriptación TLS o SSL
        if ($encryption === 'ssl' || $mail->Port === 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($encryption === 'tls' || $mail->Port === 587) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;
        }

        // Remitente por defecto: si no está definido o es genérico, usa el correo autenticado
        $fromAddress = env('MAIL_FROM_ADDRESS');
        if (empty($fromAddress) || $fromAddress === 'soporte@axstore.com') {
            $fromAddress = !empty($username) ? $username : 'soporte@axstore.com';
        }
        $fromName = env('MAIL_FROM_NAME', 'AXStore');
        $mail->setFrom($fromAddress, $fromName);

        // Opciones SSL permisivas para entornos locales de desarrollo (Laragon / Windows)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];

        return $mail;
    }

    /**
     * Enviar correo con el token / enlace de restablecimiento de contraseña
     *
     * @param object $user
     * @param string $resetUrl
     * @param string $expiresAt
     * @param string|null $toEmail
     * @return array{success: bool, message: string}
     */
    public function sendResetPasswordEmail($user, string $resetUrl, string $expiresAt, ?string $toEmail = null): array
    {
        try {
            $mail = $this->createMailer();

            // Determinar correo destinatario
            $recipientEmail = $toEmail ?? (filter_var($user->username ?? '', FILTER_VALIDATE_EMAIL) ? $user->username : null);

            if (!$recipientEmail) {
                $recipientEmail = env('MAIL_FROM_ADDRESS');
            }

            $recipientName = $user->nombre_real ?? $user->username ?? 'Usuario';

            $mail->addAddress($recipientEmail, $recipientName);

            // Contenido del Correo
            $mail->isHTML(true);
            $mail->Subject = 'Restablecimiento de Contraseña — AXStore';

            // Renderizar la vista Blade corporativa
            $mail->Body = view('emails.reset-password', [
                'user'      => $user,
                'resetUrl'  => $resetUrl,
                'expiresAt' => $expiresAt,
            ])->render();

            // Texto plano de respaldo
            $mail->AltBody = "Hola {$recipientName},\n\nHemos recibido una solicitud para restablecer tu contraseña en AXStore.\n"
                . "Para restablecerla, ingresa al siguiente enlace:\n{$resetUrl}\n\n"
                . "Este enlace expira el: {$expiresAt}.\nSi no solicitaste este cambio, puedes ignorar este mensaje.";

            $mail->send();

            Log::info("PHPMailer: Correo de restablecimiento enviado exitosamente a {$recipientEmail}");

            return [
                'success' => true,
                'message' => "Correo enviado con éxito a {$recipientEmail}.",
            ];
        } catch (Exception $e) {
            $errorMessage = $mail->ErrorInfo ?? $e->getMessage();
            Log::error("PHPMailer Error al enviar correo a " . ($recipientEmail ?? 'desconocido') . ": {$errorMessage}");

            return [
                'success' => false,
                'message' => "Error al enviar el correo: {$errorMessage}",
            ];
        } catch (\Throwable $th) {
            Log::error("Error inesperado en PHPMailerService: " . $th->getMessage());

            return [
                'success' => false,
                'message' => "Error inesperado: " . $th->getMessage(),
            ];
        }
    }
}
