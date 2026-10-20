<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Error "esperado" del negocio (ej: no hay stock, ya estaba pagado).
 * No es un fallo del sistema: solo hay que mostrarle el mensaje al usuario.
 *
 * Laravel llama automáticamente al método render() cuando se lanza esta excepción,
 * así que en los controladores no hace falta poner try/catch para estos casos.
 */
class NegocioException extends RuntimeException
{
    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $this->getMessage()], 422);
        }

        return back()->withInput()->with('error', $this->getMessage());
    }

    /** Estos errores no se guardan en el log porque son normales. */
    public function report(): bool
    {
        return true;
    }
}
