<?php

namespace App\Services;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Envía notificaciones por correo de forma "segura".
 *
 * Si el correo falla (sin internet, clave mala de Gmail, etc.) NO se cae la aplicación:
 * el error se guarda en el log y el usuario puede seguir trabajando.
 */
class NotificadorService
{
    /**
     * @param  mixed  $destinatarios  Un modelo con Notifiable o una colección de ellos.
     * @return bool  true si se envió, false si falló o no había correo.
     */
    public function enviar(mixed $destinatarios, Notification $notificacion): bool
    {
        $lista = collect(is_iterable($destinatarios) ? $destinatarios : [$destinatarios])
            ->filter(fn ($d) => ! empty($d?->routeNotificationFor('mail')));

        if ($lista->isEmpty()) {
            return false;
        }

        try {
            foreach ($lista as $destinatario) {
                $destinatario->notify($notificacion);
            }

            return true;
        } catch (Throwable $e) {
            Log::error('No se pudo enviar la notificación por correo', [
                'notificacion' => $notificacion::class,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
