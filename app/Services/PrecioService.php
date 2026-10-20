<?php

namespace App\Services;

use App\Enums\TipoVehiculo;
use App\Models\Servicio;
use Illuminate\Support\Collection;

/**
 * Calcula el precio de un lavado según el tipo de vehículo
 * y los servicios elegidos (HU-07 y HU-08).
 */
class PrecioService
{
    /**
     * Devuelve el detalle: [servicio_id => precio] para guardarlo en el lavado.
     *
     * @param  Collection<int, Servicio>  $servicios
     * @return array<int, float>
     */
    public function detalle(TipoVehiculo $tipo, Collection $servicios): array
    {
        return $servicios
            ->mapWithKeys(fn (Servicio $servicio) => [$servicio->id => $servicio->precioPara($tipo)])
            ->all();
    }

    /** Suma total de los servicios para ese tipo de vehículo. */
    public function calcular(TipoVehiculo $tipo, Collection $servicios): float
    {
        return array_sum($this->detalle($tipo, $servicios));
    }
}
