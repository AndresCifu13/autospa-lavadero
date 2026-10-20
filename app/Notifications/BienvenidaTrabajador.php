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
            ->greeting("Hola {$notifiable->name},")
            ->line('Ya tienes una cuenta para usar el sistema del lavadero.')
            ->line("**Usuario:** {$notifiable->email}")
            ->line("**Contraseña:** {$this->claveTemporal}")
            ->action('Entrar al sistema', route('login'))
            ->line('Por seguridad, cambia tu contraseña desde "Mi perfil" la primera vez que entres.');
    }
}
