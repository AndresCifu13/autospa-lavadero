<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\Venta;
use App\Notifications\StockBajo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TiendaTest extends TestCase
{
    use RefreshDatabase;

    public function test_venta_descuenta_el_inventario(): void
    {
        $producto = Producto::factory()->create(['precio' => 8000, 'stock' => 10]);

        $this->actingAs($this->empleado())->post(route('ventas.store'), [
            'metodo_pago' => 'efectivo',
            'items' => [['producto_id' => $producto->id, 'cantidad' => 3]],
        ])->assertRedirect();

        $this->assertSame(7, $producto->fresh()->stock);
        $this->assertEquals(24000, Venta::first()->total);
    }

    public function test_no_vende_mas_de_lo_que_hay(): void
    {
        $producto = Producto::factory()->create(['stock' => 2]);

        $this->actingAs($this->empleado())->post(route('ventas.store'), [
            'metodo_pago' => 'efectivo',
            'items' => [['producto_id' => $producto->id, 'cantidad' => 5]],
        ])->assertSessionHas('error');

        $this->assertSame(2, $producto->fresh()->stock);
        $this->assertSame(0, Venta::count()); // la transacción se deshizo
    }

    public function test_avisa_al_admin_cuando_el_stock_queda_bajo(): void
    {
        Notification::fake();
        $admin = $this->admin();
        $producto = Producto::factory()->create(['stock' => 5, 'stock_minimo' => 3]);

        $this->actingAs($this->empleado())->post(route('ventas.store'), [
            'metodo_pago' => 'nequi',
            'items' => [['producto_id' => $producto->id, 'cantidad' => 2]],
        ]);

        Notification::assertSentTo($admin, StockBajo::class);
    }

    public function test_admin_anula_venta_y_devuelve_stock(): void
    {
        $producto = Producto::factory()->create(['stock' => 10]);
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('ventas.store'), [
            'metodo_pago' => 'efectivo', 'items' => [['producto_id' => $producto->id, 'cantidad' => 4]],
        ]);

        $this->actingAs($admin)->delete(route('ventas.destroy', Venta::first()));

        $this->assertSame(10, $producto->fresh()->stock);
        $this->assertSame(0, Venta::count());
    }

    public function test_empleado_no_puede_anular_ventas(): void
    {
        $producto = Producto::factory()->create();
        $empleado = $this->empleado();
        $this->actingAs($empleado)->post(route('ventas.store'), [
            'metodo_pago' => 'efectivo', 'items' => [['producto_id' => $producto->id, 'cantidad' => 1]],
        ]);

        $this->actingAs($empleado)->delete(route('ventas.destroy', Venta::first()))->assertForbidden();
    }
}
