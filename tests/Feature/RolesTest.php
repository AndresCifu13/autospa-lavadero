<?php

namespace Tests\Feature;

use App\Models\Lavado;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RolesTest extends TestCase
{
    use RefreshDatabase;

    public static function rutasSoloAdmin(): array
    {
        return [
            ['trabajadores.index'], ['trabajadores.create'], ['servicios.index'], ['gastos.index'],
            ['productos.create'], ['reportes.diario'], ['reportes.mensual'], ['ajustes.edit'],
        ];
    }

    #[DataProvider('rutasSoloAdmin')]
    public function test_empleado_no_entra_a_secciones_de_admin(string $ruta): void
    {
        $this->actingAs($this->empleado())->get(route($ruta))->assertForbidden();
    }

    #[DataProvider('rutasSoloAdmin')]
    public function test_admin_si_entra(string $ruta): void
    {
        $this->actingAs($this->admin())->get(route($ruta))->assertOk();
    }

    public function test_empleado_no_puede_eliminar_lavados_ni_clientes(): void
    {
        $empleado = $this->empleado();
        $vehiculo = Vehiculo::factory()->create();
        $lavado = Lavado::create(['vehiculo_id' => $vehiculo->id, 'cliente_id' => $vehiculo->cliente_id, 'registrado_por' => $empleado->id]);

        $this->actingAs($empleado)->delete(route('lavados.destroy', $lavado))->assertForbidden();
        $this->actingAs($empleado)->delete(route('clientes.destroy', $vehiculo->cliente))->assertForbidden();
        $this->assertNotSoftDeleted($lavado);
    }

    public function test_empleado_no_avanza_lavado_de_otro_empleado(): void
    {
        $otro = $this->empleado();
        $vehiculo = Vehiculo::factory()->create();
        $lavado = Lavado::create(['vehiculo_id' => $vehiculo->id, 'cliente_id' => $vehiculo->cliente_id, 'registrado_por' => $otro->id, 'empleado_id' => $otro->id]);

        $this->actingAs($this->empleado())->patch(route('lavados.avanzar', $lavado))->assertForbidden();
    }

    public function test_admin_crea_trabajador_y_se_envia_correo(): void
    {
        Notification::fake();

        $this->actingAs($this->admin())->post(route('trabajadores.store'), [
            'name' => 'Nuevo Empleado', 'email' => 'nuevo@autospa.com', 'rol' => 'empleado', 'activo' => 1,
        ])->assertRedirect(route('trabajadores.index'));

        $nuevo = User::where('email', 'nuevo@autospa.com')->firstOrFail();
        Notification::assertSentTo($nuevo, \App\Notifications\BienvenidaTrabajador::class);
    }

    public function test_admin_no_puede_eliminarse_a_si_mismo(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->delete(route('trabajadores.destroy', $admin))->assertSessionHas('error');
        $this->assertNotSoftDeleted($admin);
    }

    public function test_usuario_desactivado_es_sacado_del_sistema(): void
    {
        $empleado = $this->empleado();
        $empleado->update(['activo' => false]);

        $this->actingAs($empleado)->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
