{{-- Diseño común para las páginas de error --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo') · AutoSpa</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/autospa.css') }}" rel="stylesheet">
</head>
<body class="p-0">
<div class="fondo-login d-flex align-items-center justify-content-center">
    <div class="tarjeta text-center" style="max-width:440px">
        <div class="tarjeta-cuerpo p-5">
            <div class="etiqueta @yield('color', 'pastel-azul') mb-3" style="font-size:2.2rem;padding:1rem 1.2rem"><i class="bi @yield('icono')"></i></div>
            <div class="fw-black text-suave mb-1">Error @yield('codigo')</div>
            <h1 class="h3">@yield('titulo')</h1>
            <p class="text-suave">@yield('mensaje')</p>
            <div class="d-flex gap-2 justify-content-center mt-4">
                <a href="javascript:history.back()" class="btn btn-blanco">Volver</a>
                <a href="{{ url('/') }}" class="btn btn-principal">Ir al inicio</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
