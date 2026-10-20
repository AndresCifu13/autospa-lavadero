<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\BienvenidaTrabajador;
use Illuminate\Support\Str;

/**
 * Gestión de trabajadores (HU-01 a HU-04).
 */
class UsuarioService
{
    public function __construct(private NotificadorService $notificador) {}

    /**
     * Crea un trabajador. Si no se escribe contraseña, se genera una
     * y se le envía por correo junto con la bienvenida.
     */
    public function crear(array $datos): User
    {
        $clave = ! empty($datos['password']) ? $datos['password'] : Str::password(10, symbols: false);

        $usuario = User::create([...$datos, 'password' => $clave]);

        $this->notificador->enviar($usuario, new BienvenidaTrabajador($clave));

        return $usuario;
    }

    public function actualizar(User $usuario, array $datos): User
    {
        // Si la contraseña viene vacía, se deja la que tenía
        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        $usuario->update($datos);

        return $usuario;
    }
}
