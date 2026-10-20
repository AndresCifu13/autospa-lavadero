<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'documento' => (string) fake()->unique()->numberBetween(10000000, 1999999999),
            'telefono' => '3'.fake()->numerify('#########'),
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
