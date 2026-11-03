<?php

namespace App\Support;

use Throwable;

/**
 * Traduce los errores de envío de correo a instrucciones claras en español,
 * para que el administrador sepa qué variable MAIL_ revisar en Vercel.
 */
class ErrorCorreo
{
    public static function explicar(Throwable $e): string
    {
        $m = $e->getMessage();

        $causa = match (true) {
            str_contains($m, 'scheme is not supported')
                => 'La variable MAIL_SCHEME tiene un valor inválido (por ejemplo "tls"). Bórrala en Vercel.',
            str_contains($m, 'Failed to authenticate'), str_contains($m, '535'), str_contains($m, 'BadCredentials')
                => 'Gmail rechazó el usuario o la clave. MAIL_USERNAME debe ser tu Gmail completo y MAIL_PASSWORD la clave de aplicación de 16 letras (no tu contraseña normal), sin espacios.',
            str_contains($m, '530'), str_contains($m, 'Authentication Required')
                => 'Falta MAIL_USERNAME o MAIL_PASSWORD en Vercel.',
            str_contains($m, 'Connection could not be established'), str_contains($m, 'timed out'), str_contains($m, 'getaddrinfo')
                => 'No se pudo conectar al servidor de correo. Usa MAIL_HOST=smtp.gmail.com y MAIL_PORT=587.',
            str_contains($m, '553'), str_contains($m, '550'), str_contains($m, 'sender')
                => 'Gmail no acepta el remitente. MAIL_FROM_ADDRESS debe ser el mismo correo de MAIL_USERNAME.',
            str_contains($m, 'does not comply with addr-spec')
                => 'Un correo está mal escrito (destinatario o MAIL_FROM_ADDRESS).',
            default => 'Detalle técnico: '.mb_strimwidth($m, 0, 200, '…'),
        };

        return 'No se pudo enviar el correo. '.$causa.' Después de cambiar variables en Vercel haz Redeploy.';
    }
}
