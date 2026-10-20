<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\User;

class ClientePolicy
{
    public function update(User $usuario, Cliente $cliente): bool
    {
        return true; // empleados pueden corregir datos de contacto
    }

    public function delete(User $usuario, Cliente $cliente): bool
    {
        return $usuario->esAdmin();
    }
}
