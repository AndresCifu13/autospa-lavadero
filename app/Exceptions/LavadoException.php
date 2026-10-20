<?php

namespace App\Exceptions;

/**
 * Errores al trabajar con lavados (pagar dos veces, entregar sin pagar, etc.)
 */
class LavadoException extends NegocioException
{
    public static function yaPagado(): self
    {
        return new self('Este vehículo ya estaba marcado como pagado.');
    }

    public static function sinPagar(): self
    {
        return new self('No se puede entregar el vehículo porque todavía no ha pagado.');
    }

    public static function estadoFinal(): self
    {
        return new self('Este vehículo ya fue entregado, no tiene más estados.');
    }

    public static function sinServicios(): self
    {
        return new self('Debes elegir al menos un servicio válido.');
    }

    public static function sinLavadaGratis(): self
    {
        return new self('Este cliente no tiene lavadas gratis disponibles.');
    }
}
