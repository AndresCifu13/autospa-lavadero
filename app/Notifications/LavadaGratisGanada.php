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
            ->greeting("¡Felicitaciones {$this->cliente->nombre}!")
            ->line('Completaste tu tarjeta de fidelización y tu próxima lavada es GRATIS.')
            ->line("Lavadas gratis disponibles: **{$this->cliente->lavadas_gratis}**")
            ->line('Solo dile al empleado que quieres usarla la próxima vez que vengas.')
            ->salutation('Gracias por preferirnos — '.config('app.name'));
    }
}
