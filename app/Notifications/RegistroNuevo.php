<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Aviso por correo cada vez que se registra algo en el sistema
 * (lavado, venta, cliente, vehículo, gasto, producto o trabajador).
 * Los destinatarios se configuran en Administración → Configuración.
 */
class RegistroNuevo extends Notification
{
    /**
     * @param  array<string, string>  $datos  etiqueta => valor que se muestran en la tabla
     */
    public function __construct(
        public string $titulo,
        public string $icono,
        public array $datos,
        public ?string $url = null,
        public ?string $registradoPor = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("{$this->icono} {$this->titulo}")
            ->markdown('emails.notificaciones.registro-nuevo', [
                'titulo' => $this->titulo,
                'icono' => $this->icono,
                'datos' => $this->datos,
                'url' => $this->url,
                'registradoPor' => $this->registradoPor,
                'fecha' => now(),
            ]);
    }
}
