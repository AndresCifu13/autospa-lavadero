<?php

namespace App\Rules;

use App\Models\Ajuste;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida uno o varios correos separados por coma, punto y coma o espacio.
 * Ejemplo válido: "dueno@gmail.com, contador@hotmail.com"
 */
class ListaDeCorreos implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $partes = preg_split('/[\s,;]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);
        $malos = array_filter($partes, fn ($correo) => ! filter_var($correo, FILTER_VALIDATE_EMAIL));

        if ($malos) {
            $fail('Estos correos no son válidos: '.implode(', ', $malos).'. Sepáralos con coma.');
        } elseif (count($partes) > Ajuste::MAXIMO_CORREOS) {
            $fail('Puedes poner máximo '.Ajuste::MAXIMO_CORREOS.' correos.');
        }
    }
}
