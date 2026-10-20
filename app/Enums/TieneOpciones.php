<?php

namespace App\Enums;

/**
 * Trait que comparten los enums.
 * Sirve para sacar la lista de opciones (valor => texto) y llenar los <select>.
 */
trait TieneOpciones
{
    /** Devuelve ['valor' => 'Texto bonito', ...] */
    public static function opciones(): array
    {
        $opciones = [];
        foreach (self::cases() as $caso) {
            $opciones[$caso->value] = $caso->etiqueta();
        }

        return $opciones;
    }

    /** Devuelve solo los valores, útil para las reglas de validación. */
    public static function valores(): array
    {
        return array_column(self::cases(), 'value');
    }
}
