@extends('layouts.guest')
@section('titulo', 'Iniciar sesión')

@section('contenido')
<div class="caja-login">
    <div class="text-center mb-4">
        <div class="d-inline-block" style="transform:scale(1.35);transform-origin:center">@include('partials.marca')</div>
        <p class="text-suave mt-3 mb-0">Sistema del lavadero</p>
    </div>

    <div class="tarjeta">
        <div class="tarjeta-cuerpo p-4">
            <h1 class="h4 mb-1">¡Hola de nuevo! 👋</h1>
            <p class="text-suave mb-4">Ingresa con tu correo y contraseña.</p>

            @include('partials.alertas')

            <form method="POST" action="{{ route('login.store') }}" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Correo</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white" style="border-radius:12px 0 0 12px"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               @class(['form-control', 'is-invalid' => $errors->has('email')]) placeholder="tucorreo@autospa.com">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white" style="border-radius:12px 0 0 12px"><i class="bi bi-lock"></i></span>
                        <input type="password" id="password" name="password" required autocomplete="current-password" class="form-control" placeholder="••••••••">
                        <button class="btn btn-blanco" type="button" id="verClave" aria-label="Mostrar contraseña" style="border-radius:0 12px 12px 0">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="recordarme" id="recordarme" value="1">
                    <label class="form-check-label" for="recordarme">Mantener la sesión abierta</label>
                </div>

                <button type="submit" class="btn btn-principal w-100 py-2">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
                </button>
            </form>
        </div>
    </div>
    <p class="text-center text-suave small mt-4 mb-0">© {{ date('Y') }} {{ $nombreNegocio }}</p>
</div>

<script>
    // Mostrar / ocultar la contraseña
    document.getElementById('verClave').addEventListener('click', function () {
        const campo = document.getElementById('password');
        const visible = campo.type === 'text';
        campo.type = visible ? 'password' : 'text';
        this.innerHTML = visible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    });
</script>
@endsection
