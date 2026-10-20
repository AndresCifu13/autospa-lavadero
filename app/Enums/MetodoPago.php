<?php

namespace App\Enums;

/**
 * Formas de pago que recibe el lavadero.
 */
enum MetodoPago: string
{
    use TieneOpciones;

    case Efectivo = 'efectivo';
    case Nequi = 'nequi';
    case Daviplata = 'daviplata';
    case Tarjeta = 'tarjeta';
    case Transferencia = 'transferencia';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Efectivo => 'Efectivo',
            self::Nequi => 'Nequi',
            self::Daviplata => 'Daviplata',
            self::Tarjeta => 'Tarjeta',
            self::Transferencia => 'Transferencia',
        };
    }
}
