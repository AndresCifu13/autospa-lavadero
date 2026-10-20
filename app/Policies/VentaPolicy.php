<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venta;

class VentaPolicy
{
    /** Anular una venta solo lo puede hacer el administrador. */
    public function delete(User $usuario, Venta $venta): bool
    {
        return $usuario->esAdmin();
    }
}
