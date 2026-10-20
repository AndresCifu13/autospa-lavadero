<?php

namespace Database\Factories;

use App\Enums\TipoVehiculo;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehiculoFactory extends Factory
{
    public function definition(): array
    {
        $tipo = fake()->randomElement(TipoVehiculo::cases());

        return [
            'cliente_id' => Cliente::factory(),
            // Motos en Colombia: ABC12D. Carros: ABC123
            'placa' => $tipo === TipoVehiculo::Moto ? fake()->unique()->bothify('???##?') : fake()->unique()->bothify('???###'),
            'tipo' => $tipo,
            'marca' => fake()->randomElement(['Chevrolet', 'Renault', 'Mazda', 'Kia', 'Toyota', 'Yamaha', 'Suzuki', 'Nissan']),
            'modelo' => (string) fake()->numberBetween(2008, 2026),
            'color' => fake()->randomElement(['Blanco', 'Negro', 'Gris', 'Rojo', 'Azul', 'Plata']),
        ];
    }
}
