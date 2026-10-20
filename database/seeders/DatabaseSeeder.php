<?php

namespace Database\Seeders;

use App\Enums\CategoriaGasto;
use App\Enums\MetodoPago;
use App\Enums\Rol;
use App\Models\Ajuste;
use App\Models\Cliente;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Vehiculo;
use App\Services\LavadoService;
use App\Services\VentaService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

/**
 * Llena la base de datos con información de ejemplo para poder probar.
 * Se ejecuta con: php artisan migrate:fresh --seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Mientras llenamos datos de prueba no mandamos correos de verdad
        Notification::fake();

        // ---------- Configuración ----------
        Ajuste::guardar(Ajuste::NOMBRE_NEGOCIO, 'AutoSpa M&A');
        Ajuste::guardar(Ajuste::LAVADAS_PARA_GRATIS, 10);
        Ajuste::guardar(Ajuste::CORREO_REPORTES, 'admin@autospa.com');

        // ---------- Usuarios (equipo del proyecto) ----------
        $admin = User::create([
            'name' => 'Administrador AutoSpa', 'email' => 'admin@autospa.com',
            'documento' => '1000000001', 'telefono' => '3001112233',
            'password' => 'Admin123*', 'rol' => Rol::Admin,
        ]);

        $empleados = collect([
            ['name' => 'Germán Tulcán', 'email' => 'empleado@autospa.com', 'documento' => '1000000002'],
            ['name' => 'Cristian Lucero', 'email' => 'cristian@autospa.com', 'documento' => '1000000003'],
            ['name' => 'Mauricio Ortiz', 'email' => 'mauricio@autospa.com', 'documento' => '1000000004'],
        ])->map(fn ($datos) => User::create([...$datos, 'telefono' => '3100000000', 'password' => 'Empleado123*', 'rol' => Rol::Empleado]));

        // ---------- Servicios con precio por tipo de vehículo ----------
        $servicios = collect([
            ['Lavado sencillo', 'Lavado exterior con shampoo y secado.', 18000, 25000, 10000, 25],
            ['Lavado general', 'Exterior, interior, aspirado y llantas.', 30000, 40000, 15000, 45],
            ['Polichado', 'Brillo y protección de la pintura.', 60000, 80000, 30000, 90],
            ['Lavado de motor', 'Desengrase y limpieza del motor.', 25000, 30000, 15000, 30],
            ['Lavado de tapicería', 'Limpieza profunda de sillas y alfombras.', 90000, 120000, 0, 180],
            ['Aspirado', 'Aspirado completo del interior.', 10000, 12000, 0, 15],
        ])->map(fn ($s) => Servicio::create([
            'nombre' => $s[0], 'descripcion' => $s[1], 'precio_auto' => $s[2],
            'precio_camioneta' => $s[3], 'precio_moto' => $s[4], 'duracion_minutos' => $s[5],
        ]));

        // ---------- Productos de la tienda ----------
        collect([
            ['Ambientador pino', 'Ambientadores', 8000, 30],
            ['Ambientador vainilla', 'Ambientadores', 8000, 4],
            ['Shampoo para carro 1L', 'Limpieza', 22000, 15],
            ['Cera líquida 500ml', 'Limpieza', 35000, 10],
            ['Paño de microfibra', 'Accesorios', 6000, 40],
            ['Silicona para tablero', 'Limpieza', 15000, 12],
            ['Agua (botella)', 'Bebidas', 2500, 48],
            ['Gaseosa 400ml', 'Bebidas', 3500, 36],
            ['Aceite 20W-50 1/4', 'Lubricantes', 28000, 8],
        ])->each(fn ($p) => Producto::create([
            'nombre' => $p[0], 'categoria' => $p[1], 'precio' => $p[2], 'stock' => $p[3], 'stock_minimo' => 5,
        ]));

        // ---------- Clientes y vehículos ----------
        $clientes = Cliente::factory(12)->create()->each(
            fn (Cliente $c) => Vehiculo::factory(fake()->numberBetween(1, 2))->create(['cliente_id' => $c->id])
        );
        // Un cliente frecuente al que le falta 1 lavada para la gratis
        $clientes->first()->update(['lavadas_acumuladas' => 9]);

        // ---------- Lavados de hoy y de días anteriores ----------
        $lavadoService = app(LavadoService::class);
        $ventaService = app(VentaService::class);

        $hoy = now();
        foreach (range(10, 0) as $diasAtras) {
            $this->travelA($diasAtras > 0 ? $hoy->copy()->subDays($diasAtras)->setTime(9, 0) : null);

            foreach (Vehiculo::inRandomOrder()->take(fake()->numberBetween(3, 6))->get() as $vehiculo) {
                $lavado = $lavadoService->registrar([
                    'placa' => $vehiculo->placa,
                    'tipo' => $vehiculo->tipo->value,
                    'servicios' => $servicios->random(fake()->numberBetween(1, 2))
                        ->filter(fn ($s) => $s->precioPara($vehiculo->tipo) > 0)->pluck('id')->whenEmpty(fn () => collect([$servicios[0]->id]))->all(),
                    'empleado_id' => $empleados->random()->id,
                ], $admin);

                // Los días anteriores quedan pagados y entregados; hoy queda una mezcla
                if ($diasAtras > 0 || fake()->boolean(50)) {
                    $lavadoService->registrarPago($lavado, fake()->randomElement(MetodoPago::cases()));
                    $lavado->update(['estado' => $diasAtras > 0 ? 'entregado' : 'terminado']);
                } elseif (fake()->boolean()) {
                    $lavado->update(['estado' => 'en_proceso']);
                }
            }

            // Algunas ventas y gastos
            $ventaService->registrar(
                [['producto_id' => Producto::inRandomOrder()->value('id'), 'cantidad' => 1]],
                MetodoPago::Efectivo, null, $empleados->random()
            );
            Gasto::create([
                'descripcion' => fake()->randomElement(['Compra de shampoo', 'Pago de agua', 'Almuerzo trabajadores', 'Compra de cera']),
                'categoria' => fake()->randomElement(CategoriaGasto::cases()),
                'monto' => fake()->randomElement([15000, 25000, 40000, 60000]),
                'fecha' => now()->toDateString(),
                'user_id' => $admin->id,
            ]);
        }

        $this->travelA(null);
    }

    /** Cambia la "fecha actual" para crear datos en días pasados (null = volver a hoy). */
    private function travelA(?Carbon $fecha): void
    {
        Carbon::setTestNow($fecha);
    }
}
