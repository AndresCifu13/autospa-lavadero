<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServicioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => 'Servicio '.fake()->unique()->word(),
            'descripcion' => fake()->sentence(),
            'precio_auto' => 20000,
            'precio_camioneta' => 28000,
            'precio_moto' => 12000,
            'duracion_minutos' => 30,
            'activo' => true,
        ];
    }
}
