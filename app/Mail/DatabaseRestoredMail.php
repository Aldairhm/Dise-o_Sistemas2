<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DatabaseRestoredMail extends Mailable
{
    use Queueable, SerializesModels;

    public bool $isSuccess;
    public string $usuarioNombre;
    public string $usuarioEmail;
    public string $usuarioRol;
    public string $origen;
    public string $fecha;
    public string $ip;
    public ?string $detallesError;

    /**
     * Create a new message instance.
     */
    public function __construct(
        bool $isSuccess,
        string $usuarioNombre,
        string $usuarioEmail,
        string $usuarioRol,
        string $origen,
        string $fecha,
        string $ip,
        ?string $detallesError = null
    ) {
        $this->isSuccess     = $isSuccess;
        $this->usuarioNombre = $usuarioNombre;
        $this->usuarioEmail  = $usuarioEmail;
        $this->usuarioRol    = $usuarioRol;
        $this->origen        = $origen;
        $this->fecha         = $fecha;
        $this->ip            = $ip;
        $this->detallesError = $detallesError;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isSuccess
            ? 'Alerta de Seguridad: Base de Datos Restaurada Exitosamente — AXStore'
            : 'Alerta Crítica: Fallo en Restauración de Base de Datos — AXStore';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.database-restored',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}

