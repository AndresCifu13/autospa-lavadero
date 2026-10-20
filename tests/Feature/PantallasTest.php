<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Gasto;
use App\Models\Lavado;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Venta;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Abre todas las pantallas con datos de ejemplo para comprobar que ninguna se rompe.
 */
class PantallasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_todas_las_pantallas_del_admin_cargan(): void
    {
        $admin = User::where('email', 'admin@autospa.com')->first();

        $rutas = [
            route('dashboard'), route('perfil.edit'),
            route('lavados.index'), route('lavados.create'), route('lavados.show', Lavado::first()),
            route('clientes.index'), route('clientes.create'), route('clientes.show', Cliente::first()), route('clientes.edit', Cliente::first()),
            route('vehiculos.index'), route('vehiculos.edit', Vehiculo::first()),
            route('productos.index'), route('productos.create'), route('productos.edit', Producto::first()),
            route('ventas.index'), route('ventas.create'), route('ventas.show', Venta::first()),
            route('trabajadores.index'), route('trabajadores.create'), route('trabajadores.edit', $admin),
            route('servicios.index'), route('servicios.create'), route('servicios.edit', Servicio::first()),
            route('gastos.index'), route('gastos.create'), route('gastos.edit', Gasto::first()),
            route('reportes.diario'), route('reportes.mensual'), route('ajustes.edit'),
        ];

        foreach ($rutas as $ruta) {
            $this->actingAs($admin)->get($ruta)->assertOk();
        }
    }

    public function test_pantallas_del_empleado_cargan(): void
    {
        $empleado = User::where('email', 'empleado@autospa.com')->first();

        foreach ([route('dashboard'), route('lavados.index'), route('lavados.create'), route('ventas.create'), route('productos.index'), route('clientes.index')] as $ruta) {
            $this->actingAs($empleado)->get($ruta)->assertOk();
        }
    }

    public function test_registro_inexistente_muestra_404_amable(): void
    {
        $this->actingAs(User::first())->get('/clientes/99999')->assertNotFound()->assertSee('No encontramos esa página');
    }
}
