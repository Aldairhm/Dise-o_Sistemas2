<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BackupNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public bool $isSuccess;
    public string $nombreArchivo;
    public string $tamano;
    public string $fecha;
    public string $destino;
    public ?string $detallesError;

    /**
     * Create a new message instance.
     */
    public function __construct(
        bool $isSuccess,
        string $nombreArchivo,
        string $tamano,
        string $fecha,
        string $destino = 'Servidor Local y Google Drive',
        ?string $detallesError = null
    ) {
        $this->isSuccess     = $isSuccess;
        $this->nombreArchivo = $nombreArchivo;
        $this->tamano        = $tamano;
        $this->fecha         = $fecha;
        $this->destino       = $destino;
        $this->detallesError = $detallesError;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isSuccess
            ? 'Respaldo de Base de Datos Generado Exitosamente — AXStore'
            : 'Alerta Crítica: Fallo al Generar Respaldo de Base de Datos — AXStore';

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
            view: 'emails.backup-notification',
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

