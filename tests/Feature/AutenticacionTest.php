<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_sin_sesion_redirige_al_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk()->assertSee('Iniciar sesión', false);
    }

    public function test_inicia_sesion_con_datos_correctos(): void
    {
        $usuario = User::factory()->create(['password' => 'Clave1234']);

        $this->post(route('login.store'), ['email' => $usuario->email, 'password' => 'Clave1234'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($usuario);
    }

    public function test_no_entra_con_clave_mala(): void
    {
        $usuario = User::factory()->create();

        $this->post(route('login.store'), ['email' => $usuario->email, 'password' => 'mala'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_usuario_inactivo_no_puede_entrar(): void
    {
        $usuario = User::factory()->inactivo()->create(['password' => 'Clave1234']);

        $this->post(route('login.store'), ['email' => $usuario->email, 'password' => 'Clave1234'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_bloquea_despues_de_cinco_intentos(): void
    {
        $usuario = User::factory()->create();

        foreach (range(1, 5) as $i) {
            $this->post(route('login.store'), ['email' => $usuario->email, 'password' => 'mala']);
        }

        $this->post(route('login.store'), ['email' => $usuario->email, 'password' => 'mala'])
            ->assertSessionHasErrors('email');

        $this->assertStringContainsString('Demasiados intentos', session('errors')->first('email'));
    }

    public function test_cerrar_sesion(): void
    {
        $this->actingAs($this->empleado())->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
