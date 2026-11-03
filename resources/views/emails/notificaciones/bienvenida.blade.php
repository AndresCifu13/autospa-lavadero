<x-mail::message>
# ¡Bienvenido al equipo! 👋

Hola **{{ $usuario->name }}**, ya tienes una cuenta para usar el sistema de **{{ config('app.name') }}**.

<x-mail::panel>
**Usuario:** {{ $usuario->email }}<br>
**Contraseña temporal:** {{ $claveTemporal }}
</x-mail::panel>

<x-mail::button :url="route('login')" color="primary">
Entrar al sistema
</x-mail::button>

🔒 Por seguridad, cambia tu contraseña desde **"Mi perfil"** la primera vez que entres.

¡Bienvenido!<br>
**{{ config('app.name') }}**
</x-mail::message>
