<?php

namespace Tests\Feature;

use App\Enums\EstadoLavado;
use App\Models\Ajuste;
use App\Models\Cliente;
use App\Models\Lavado;
use App\Models\Servicio;
use App\Models\Vehiculo;
use App\Notifications\LavadaGratisGanada;
use App\Notifications\VehiculoListo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LavadoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    public function test_registra_vehiculo_nuevo_con_precio_segun_tipo(): void
    {
        $general = Servicio::factory()->create(['precio_auto' => 30000, 'precio_camioneta' => 40000, 'precio_moto' => 15000]);
        $motor = Servicio::factory()->create(['precio_auto' => 25000, 'precio_camioneta' => 30000, 'precio_moto' => 15000]);

        $this->actingAs($this->empleado())->post(route('lavados.store'), [
            'placa' => 'abc 123', 'tipo' => 'camioneta', 'cliente_nombre' => 'Ana Pérez',
            'servicios' => [$general->id, $motor->id],
        ])->assertRedirect();

        $lavado = Lavado::firstOrFail();
        $this->assertSame('ABC123', $lavado->vehiculo->placa);
        $this->assertEquals(70000, $lavado->total); // 40.000 + 30.000 (precios de camioneta)
        $this->assertSame('Ana Pérez', $lavado->cliente->nombre);
        $this->assertSame(EstadoLavado::Pendiente, $lavado->estado);
    }

    public function test_placa_existente_reutiliza_el_cliente(): void
    {
        $vehiculo = Vehiculo::factory()->create(['placa' => 'XYZ987', 'tipo' => 'auto']);
        $servicio = Servicio::factory()->create();

        $this->actingAs($this->empleado())->post(route('lavados.store'), [
            'placa' => 'xyz987', 'tipo' => 'auto', 'servicios' => [$servicio->id],
        ])->assertRedirect();

        $this->assertSame(1, Cliente::count());
        $this->assertSame($vehiculo->cliente_id, Lavado::first()->cliente_id);
    }

    public function test_placa_nueva_pide_nombre_del_cliente(): void
    {
        $servicio = Servicio::factory()->create();

        $this->actingAs($this->empleado())->post(route('lavados.store'), [
            'placa' => 'NUE123', 'tipo' => 'auto', 'servicios' => [$servicio->id],
        ])->assertSessionHasErrors('cliente_nombre');
    }

    public function test_buscar_placa_devuelve_datos_del_cliente(): void
    {
        $vehiculo = Vehiculo::factory()->create(['placa' => 'BUS123']);

        $this->actingAs($this->empleado())->getJson(route('lavados.buscar-placa', ['placa' => 'bus-123']))
            ->assertOk()
            ->assertJsonPath('encontrado', true)
            ->assertJsonPath('cliente.id', $vehiculo->cliente_id);
    }

    public function test_flujo_completo_estados_pago_y_aviso_al_cliente(): void
    {
        $lavado = $this->crearLavado();
        $empleado = $this->empleado();

        $this->actingAs($empleado)->patch(route('lavados.avanzar', $lavado)); // en proceso
        $this->actingAs($empleado)->patch(route('lavados.avanzar', $lavado)); // terminado
        Notification::assertSentTo($lavado->cliente, VehiculoListo::class);

        // No se puede entregar sin pagar
        $this->actingAs($empleado)->patch(route('lavados.avanzar', $lavado))->assertSessionHas('error');
        $this->assertSame(EstadoLavado::Terminado, $lavado->fresh()->estado);

        $this->actingAs($empleado)->patch(route('lavados.pagar', $lavado), ['metodo_pago' => 'nequi'])->assertSessionHas('exito');
        $this->actingAs($empleado)->patch(route('lavados.avanzar', $lavado));

        $lavado->refresh();
        $this->assertTrue($lavado->pagado);
        $this->assertSame(EstadoLavado::Entregado, $lavado->estado);
        $this->assertSame(1, $lavado->cliente->fresh()->lavadas_acumuladas);
    }

    public function test_no_se_puede_pagar_dos_veces(): void
    {
        $lavado = $this->crearLavado();
        $empleado = $this->empleado();

        $this->actingAs($empleado)->patch(route('lavados.pagar', $lavado), ['metodo_pago' => 'efectivo']);
        $this->actingAs($empleado)->patch(route('lavados.pagar', $lavado), ['metodo_pago' => 'efectivo'])
            ->assertSessionHas('error', 'Este vehículo ya estaba marcado como pagado.');

        $this->assertSame(1, $lavado->cliente->fresh()->lavadas_acumuladas);
    }

    public function test_al_completar_la_tarjeta_gana_lavada_gratis_y_la_puede_usar(): void
    {
        Ajuste::guardar(Ajuste::LAVADAS_PARA_GRATIS, 3);
        $empleado = $this->empleado();
        $lavado = $this->crearLavado();
        $cliente = $lavado->cliente;
        $cliente->update(['lavadas_acumuladas' => 2]);

        $this->actingAs($empleado)->patch(route('lavados.pagar', $lavado), ['metodo_pago' => 'efectivo']);

        $cliente->refresh();
        $this->assertSame(0, $cliente->lavadas_acumuladas);
        $this->assertSame(1, $cliente->lavadas_gratis);
        Notification::assertSentTo($cliente, LavadaGratisGanada::class);

        // Usa la gratis
        $this->actingAs($empleado)->post(route('lavados.store'), [
            'placa' => $lavado->vehiculo->placa, 'tipo' => $lavado->vehiculo->tipo->value,
            'servicios' => $lavado->servicios->pluck('id')->all(), 'usar_gratis' => 1,
        ]);

        $gratis = Lavado::latest('id')->first();
        $this->assertTrue($gratis->es_gratis);
        $this->assertEquals(0, $gratis->total);
        $this->assertTrue($gratis->pagado);
        $this->assertSame(0, $cliente->fresh()->lavadas_gratis);
    }

    public function test_no_puede_usar_gratis_si_no_tiene(): void
    {
        $lavado = $this->crearLavado();

        $this->actingAs($this->empleado())->post(route('lavados.store'), [
            'placa' => $lavado->vehiculo->placa, 'tipo' => 'auto',
            'servicios' => $lavado->servicios->pluck('id')->all(), 'usar_gratis' => 1,
        ])->assertSessionHas('error', 'Este cliente no tiene lavadas gratis disponibles.');

        $this->assertSame(1, Lavado::count());
    }

    public function test_admin_elimina_lavado_pagado_y_se_descuenta_de_la_tarjeta(): void
    {
        $lavado = $this->crearLavado();
        $admin = $this->admin();
        $this->actingAs($admin)->patch(route('lavados.pagar', $lavado), ['metodo_pago' => 'efectivo']);

        $this->actingAs($admin)->delete(route('lavados.destroy', $lavado))->assertRedirect(route('lavados.index'));

        $this->assertSoftDeleted($lavado);
        $this->assertSame(0, $lavado->cliente->fresh()->lavadas_acumuladas);
    }

    private function crearLavado(): Lavado
    {
        $servicio = Servicio::factory()->create(['precio_auto' => 20000]);
        $vehiculo = Vehiculo::factory()->create(['tipo' => 'auto']);

        return app(\App\Services\LavadoService::class)->registrar([
            'placa' => $vehiculo->placa, 'tipo' => 'auto', 'servicios' => [$servicio->id],
        ], $this->empleado());
    }
}
