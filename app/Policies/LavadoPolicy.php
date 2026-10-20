<?php

namespace App\Policies;

use App\Models\Lavado;
use App\Models\User;

/**
 * Reglas de quién puede hacer qué con un lavado.
 * Laravel encuentra esta clase sola porque se llama igual que el modelo + "Policy".
 */
class LavadoPolicy
{
    /** El admin puede todo: este método se revisa antes que los demás. */
    public function before(User $usuario): ?bool
    {
        return $usuario->esAdmin() ? true : null;
    }

    public function avanzar(User $usuario, Lavado $lavado): bool
    {
        // El empleado puede mover sus lavados o los que no tienen a nadie asignado
        return $lavado->empleado_id === null || $lavado->empleado_id === $usuario->id;
    }

    public function pagar(User $usuario, Lavado $lavado): bool
    {
        return true; // cualquier empleado puede cobrar
    }

    public function delete(User $usuario, Lavado $lavado): bool
    {
        return false; // solo el admin (ver before)
    }
}
