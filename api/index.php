<?php

/*
 * Punto de entrada para Vercel.
 *
 * En Vercel el sistema de archivos es de solo lectura, excepto /tmp.
 * Por eso movemos a /tmp todo lo que Laravel necesita escribir:
 * storage (vistas compiladas, logs, sesiones de archivo) y bootstrap/cache.
 */

$storage = '/tmp/storage';

foreach ([
    $storage.'/app/public',
    $storage.'/framework/views',
    $storage.'/framework/cache/data',
    $storage.'/framework/sessions',
    $storage.'/logs',
    '/tmp/bootstrap/cache',
] as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

$runtimeEnv = [
    // Laravel lee esta variable para cambiar la ruta de storage
    'LARAVEL_STORAGE_PATH' => $storage,
    'VIEW_COMPILED_PATH' => $storage.'/framework/views',

    // Archivos de caché que normalmente van en bootstrap/cache
    'APP_SERVICES_CACHE' => '/tmp/bootstrap/cache/services.php',
    'APP_PACKAGES_CACHE' => '/tmp/bootstrap/cache/packages.php',
    'APP_CONFIG_CACHE' => '/tmp/bootstrap/cache/config.php',
    'APP_ROUTES_CACHE' => '/tmp/bootstrap/cache/routes.php',
    'APP_EVENTS_CACHE' => '/tmp/bootstrap/cache/events.php',
];

foreach ($runtimeEnv as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

// Los logs siempre van a stderr para verlos en el panel de Vercel (Logs).
// Un log en archivo quedaría en /tmp y se perdería sin que nadie lo vea.
putenv('LOG_CHANNEL=stderr');
$_ENV['LOG_CHANNEL'] = $_SERVER['LOG_CHANNEL'] = 'stderr';

require __DIR__.'/../public/index.php';
