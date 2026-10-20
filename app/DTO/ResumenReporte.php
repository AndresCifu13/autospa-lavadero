<?php

namespace App\DTO;

use Illuminate\Support\Carbon;

/**
 * Objeto que guarda los números de un reporte (diario o mensual).
 * Es "readonly": una vez creado, no se puede cambiar. Así evitamos errores.
 */
final readonly class ResumenReporte
{
    public function __construct(
        public string $titulo,
        public Carbon $desde,
        public Carbon $hasta,
        public int $cantidadLavados,
        public int $lavadosPendientesDePago,
        public int $lavadasGratis,
        public float $ingresosLavados,
        public int $cantidadVentas,
        public float $ingresosTienda,
        public float $totalGastos,
        /** @var array<string, float> método de pago => total */
        public array $porMetodoPago = [],
        /** @var array<int, array{nombre:string, cantidad:int, total:float}> */
        public array $porServicio = [],
        /** @var array<int, array{nombre:string, cantidad:int, total:float}> */
        public array $porEmpleado = [],
        /** @var array<string, float> categoría => total */
        public array $gastosPorCategoria = [],
        /** @var array<string, array{ingresos:float, gastos:float}> fecha => valores (solo mensual) */
        public array $porDia = [],
    ) {}

    public function totalIngresos(): float
    {
        return $this->ingresosLavados + $this->ingresosTienda;
    }

    /** Lo que queda después de restar los gastos. */
    public function utilidad(): float
    {
        return $this->totalIngresos() - $this->totalGastos;
    }
}
