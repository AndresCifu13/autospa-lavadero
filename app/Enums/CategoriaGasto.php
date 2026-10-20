<?php

namespace App\Enums;

/**
 * Categorías para clasificar los gastos del día.
 */
enum CategoriaGasto: string
{
    use TieneOpciones;

    case Insumos = 'insumos';
    case ServiciosPublicos = 'servicios_publicos';
    case Pagos = 'pagos_personal';
    case Mantenimiento = 'mantenimiento';
    case Otros = 'otros';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Insumos => 'Insumos (jabón, cera, etc.)',
            self::ServiciosPublicos => 'Servicios públicos',
            self::Pagos => 'Pago a personal',
            self::Mantenimiento => 'Mantenimiento',
            self::Otros => 'Otros',
        };
    }
}
