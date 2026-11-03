<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Correo de bienvenida con los datos para entrar al sistema. */
class BienvenidaTrabajador extends Notification
{
    public function __construct(private string $claveTemporal) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('¡Bienvenido al equipo de '.config('app.name').'!')
            ->markdown('emails.notificaciones.bienvenida', [
                'usuario' => $notifiable,
                'claveTemporal' => $this->claveTemporal,
            ]);
    }
}
