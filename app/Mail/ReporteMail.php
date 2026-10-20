<?php

namespace App\Mail;

use App\DTO\ResumenReporte;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Correo con el resumen del reporte (HU-16).
 */
class ReporteMail extends Mailable
{
    public function __construct(public ResumenReporte $reporte) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '📊 '.$this->reporte->titulo.' — '.config('app.name'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.reporte');
    }
}
