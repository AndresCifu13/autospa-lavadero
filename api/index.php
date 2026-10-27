<?php

// Redirigir las solicitudes estáticas o de recursos
if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] !== '/') {
    $publicPath = __DIR__ . '/../public' . $_SERVER['REQUEST_URI'];
    if (file_exists($publicPath) && !is_dir($publicPath)) {
        return false;
    }
}

// Cargar la aplicación de Laravel
require __DIR__ . '/../public/index.php';