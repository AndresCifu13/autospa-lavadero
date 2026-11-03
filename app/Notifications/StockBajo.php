<?php

namespace App\Notifications;

use App\Models\Producto;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Avisa al administrador que un producto de la tienda se está acabando. */
class StockBajo extends Notification
{
    public function __construct(private Producto $producto) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("⚠️ Stock bajo: {$this->producto->nombre}")
            ->markdown('emails.notificaciones.stock-bajo', [
                'usuario' => $notifiable,
                'producto' => $this->producto,
            ]);
    }
}
