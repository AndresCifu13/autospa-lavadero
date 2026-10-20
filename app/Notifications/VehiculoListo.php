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
        $vehiculo = $this->lavado->vehiculo;

        $mensaje = (new MailMessage)
            ->subject("Tu vehículo {$vehiculo->placa} ya está listo 🚗✨")
            ->greeting("Hola {$notifiable->nombre},")
            ->line("Tu {$vehiculo->tipo->etiqueta()} de placa **{$vehiculo->placa}** ya está limpio y listo para recoger.")
            ->line('Servicios: '.$this->lavado->servicios->pluck('nombre')->implode(', ').'.');

        $mensaje = $this->lavado->pagado
            ? $mensaje->line('El servicio ya está pago. ¡Gracias!')
            : $mensaje->line('**Total a pagar:** '.dinero($this->lavado->total));

        return $mensaje->salutation('¡Te esperamos! — '.config('app.name'));
    }
}
