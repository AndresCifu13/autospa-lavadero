<?php

namespace App\Notifications;

use App\Models\Cliente;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Felicita al cliente porque completó su tarjeta y ganó una lavada gratis. */
class LavadaGratisGanada extends Notification
{
    public function __construct(private Cliente $cliente) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 ¡Ganaste una lavada gratis!')
            ->markdown('emails.notificaciones.lavada-gratis', ['cliente' => $this->cliente]);
    }
}
