<?php

// Redirigir peticiones estáticas
if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] !== '/') {
    $publicPath = __DIR__ . '/../public' . $_SERVER['REQUEST_URI'];
    if (file_exists($publicPath) && !is_dir($publicPath)) {
        return false;
    }
}

// Crear carpetas temporales de almacenamiento en /tmp para Vercel
$_storage = '/tmp/storage';
$dirs = [
    $_storage . '/app/public',
    $_storage . '/framework/views',
    $_storage . '/framework/cache/data',
    $_storage . '/framework/sessions',
    $_storage . '/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Sobrescribir las rutas de almacenamiento de Laravel en tiempo de ejecución
$_ENV['APP_STORAGE_PATH'] = $_storage;

// Cargar Laravel
require __DIR__ . '/../public/index.php';