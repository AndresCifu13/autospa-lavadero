<?php

namespace App\Enums;

/**
 * Estados por los que pasa un vehículo dentro del lavadero.
 * Pendiente -> En proceso -> Terminado -> Entregado
 */
enum EstadoLavado: string
{
    use TieneOpciones;

    case Pendiente = 'pendiente';
    case EnProceso = 'en_proceso';
    case Terminado = 'terminado';
    case Entregado = 'entregado';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::EnProceso => 'En proceso',
            self::Terminado => 'Terminado',
            self::Entregado => 'Entregado',
        };
    }

    /** Clase CSS del color pastel de la etiqueta. */
    public function color(): string
    {
        return match ($this) {
            self::Pendiente => 'pastel-amarillo',
            self::EnProceso => 'pastel-azul',
            self::Terminado => 'pastel-verde',
            self::Entregado => 'pastel-lila',
        };
    }

    /** El estado que sigue, o null si ya es el último. */
    public function siguiente(): ?self
    {
        return match ($this) {
            self::Pendiente => self::EnProceso,
            self::EnProceso => self::Terminado,
            self::Terminado => self::Entregado,
            self::Entregado => null,
        };
    }
}
