<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ComisionPagadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $vendedor;
    public $comisiones;
    public string $metodoPago;
    public ?string $referenciaPago;
    public ?string $notas;
    public ?string $comprobantePath;
    public float $totalPagado;

    /**
     * Create a new message instance.
     */
    public function __construct(
        User $vendedor,
        $comisiones,
        string $metodoPago = 'Efectivo',
        ?string $referenciaPago = null,
        ?string $notas = null,
        ?string $comprobantePath = null
    ) {
        $this->vendedor        = $vendedor;
        $this->comisiones      = $comisiones;
        $this->metodoPago      = $metodoPago;
        $this->referenciaPago  = $referenciaPago;
        $this->notas           = $notas;
        $this->comprobantePath = $comprobantePath;
        $this->totalPagado     = $comisiones->sum('monto');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $montoFormateado = '$' . number_format($this->totalPagado, 2);
        return new Envelope(
            subject: "¡Comisión Pagada! Resumen de Pago ({$montoFormateado}) — AXStore",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.comision-pagada',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        if ($this->comprobantePath && Storage::disk('public')->exists($this->comprobantePath)) {
            $fullPath = Storage::disk('public')->path($this->comprobantePath);
            $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
            return [
                Attachment::fromPath($fullPath)
                    ->as('Comprobante_Pago_' . date('Ymd_His') . '.' . $ext)
            ];
        }

        return [];
    }
}
