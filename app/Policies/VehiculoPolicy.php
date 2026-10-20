<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehiculo;

class VehiculoPolicy
{
    public function update(User $usuario, Vehiculo $vehiculo): bool
    {
        return true;
    }

    public function delete(User $usuario, Vehiculo $vehiculo): bool
    {
        return $usuario->esAdmin();
    }
}
