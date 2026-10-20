<?php

namespace App\Enums;

/**
 * Tipos de vehículo que atiende el lavadero.
 * Cada tipo tiene un precio diferente en los servicios.
 */
enum TipoVehiculo: string
{
    use TieneOpciones;

    case Auto = 'auto';
    case Camioneta = 'camioneta';
    case Moto = 'moto';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Auto => 'Automóvil',
            self::Camioneta => 'Camioneta',
            self::Moto => 'Moto',
        };
    }

    /** Ícono de Bootstrap Icons para mostrar en pantalla. */
    public function icono(): string
    {
        return match ($this) {
            self::Auto => 'bi-car-front',
            self::Camioneta => 'bi-truck-front',
            self::Moto => 'bi-bicycle',
        };
    }

    /** Nombre de la columna de precio en la tabla "servicios". */
    public function columnaPrecio(): string
    {
        return 'precio_'.$this->value;
    }
}
