<?php

/*
 * Funciones de ayuda que se pueden usar en cualquier parte (también en las vistas).
 * Se cargan solas gracias a "autoload.files" en composer.json.
 */

if (! function_exists('dinero')) {
    /** Formatea un valor como pesos colombianos: 25000 => "$ 25.000" */
    function dinero(float|int|string|null $valor): string
    {
        return '$ '.number_format((float) $valor, 0, ',', '.');
    }
}

if (! function_exists('activo')) {
    /** Devuelve "active" si la ruta actual coincide (para resaltar el menú). */
    function activo(string ...$rutas): string
    {
        return request()->routeIs(...$rutas) ? 'active' : '';
    }
}

if (! function_exists('limpiar_env')) {
    /**
     * Lee una variable de entorno quitando espacios, saltos de línea y comillas
     * de los extremos. Útil para valores pegados a mano en Vercel.
     * Devuelve '' si no existe o es "null".
     */
    function limpiar_env(string $clave): string
    {
        $valor = env($clave);

        return is_string($valor) || is_int($valor)
            ? trim((string) $valor, " \t\n\r\0\x0B\"'")
            : '';
    }
}
