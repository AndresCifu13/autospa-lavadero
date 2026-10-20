<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Clase padre de todos los controladores.
 * AuthorizesRequests nos da $this->authorize() para usar las Policies.
 */
abstract class Controller
{
    use AuthorizesRequests;
}
