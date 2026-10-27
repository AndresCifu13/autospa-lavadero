<?php

// Reenviar peticiones de archivos estáticos
if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] !== '/') {
    $publicPath = __DIR__ . '/../public' . $_SERVER['REQUEST_URI'];
    if (file_exists($publicPath) && !is_dir($publicPath)) {
        return false;
    }
}

// Redirigir carpetas de escritura obligatorias a /tmp de Vercel
$storageDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache'
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

putenv('APP_STORAGE_PATH=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// Cargar Laravel
require __DIR__ . '/../public/index.php';