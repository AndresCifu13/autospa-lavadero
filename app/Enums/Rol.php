<?php

namespace App\Enums;

/**
 * Roles que puede tener un usuario del sistema.
 */
enum Rol: string
{
    use TieneOpciones;

    case Admin = 'admin';
    case Empleado = 'empleado';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Empleado => 'Empleado',
        };
    }
}
