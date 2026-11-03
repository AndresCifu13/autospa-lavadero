<?php

namespace App\Notifications;

use App\Models\Lavado;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Le avisa al cliente que su vehículo ya está listo para recoger. */
class VehiculoListo extends Notification
{
    public function __construct(private Lavado $lavado) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->lavado->loadMissing('vehiculo', 'servicios');

        return (new MailMessage)
            ->subject("Tu vehículo {$this->lavado->vehiculo->placa} ya está listo 🚗✨")
            ->markdown('emails.notificaciones.vehiculo-listo', [
                'cliente' => $notifiable,
                'lavado' => $this->lavado,
                'vehiculo' => $this->lavado->vehiculo,
            ]);
    }
}
