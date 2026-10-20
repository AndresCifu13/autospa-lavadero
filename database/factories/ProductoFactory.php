<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => 'Producto '.fake()->unique()->word(),
            'categoria' => 'Cuidado del vehículo',
            'precio' => 15000,
            'stock' => 20,
            'stock_minimo' => 3,
            'activo' => true,
        ];
    }
}
