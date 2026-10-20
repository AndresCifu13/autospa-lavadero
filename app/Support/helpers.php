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
