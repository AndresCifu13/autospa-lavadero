<?php

namespace Tests\Feature;

use App\Mail\ReporteMail;
use App\Models\Ajuste;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\Vehiculo;
use App\Enums\MetodoPago;
use App\Services\LavadoService;
use App\Services\ReporteService;
use App\Services\VentaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReporteTest extends TestCase
{
    use RefreshDatabase;

    public function test_reporte_diario_suma_ingresos_y_resta_gastos(): void
    {
        Notification::fake();
        $admin = $this->admin();
        $servicio = Servicio::factory()->create(['precio_auto' => 30000]);
        $lavados = app(LavadoService::class);

        // Un lavado pagado y uno sin pagar
        foreach (['AAA111', 'BBB222'] as $i => $placa) {
            $vehiculo = Vehiculo::factory()->create(['placa' => $placa, 'tipo' => 'auto']);
            $lavado = $lavados->registrar(['placa' => $placa, 'tipo' => 'auto', 'servicios' => [$servicio->id]], $admin);
            if ($i === 0) {
                $lavados->registrarPago($lavado, MetodoPago::Efectivo);
            }
        }

        $producto = Producto::factory()->create(['precio' => 5000]);
        app(VentaService::class)->registrar([['producto_id' => $producto->id, 'cantidad' => 2]], MetodoPago::Nequi, null, $admin);
        Gasto::create(['descripcion' => 'Jabón', 'categoria' => 'insumos', 'monto' => 12000, 'fecha' => today(), 'user_id' => $admin->id]);

        $reporte = app(ReporteService::class)->diario(now());

        $this->assertSame(2, $reporte->cantidadLavados);
        $this->assertSame(1, $reporte->lavadosPendientesDePago);
        $this->assertEquals(30000, $reporte->ingresosLavados);
        $this->assertEquals(10000, $reporte->ingresosTienda);
        $this->assertEquals(12000, $reporte->totalGastos);
        $this->assertEquals(28000, $reporte->utilidad());

        $this->actingAs($admin)->get(route('reportes.diario'))->assertOk()->assertSee('$ 28.000');
    }

    public function test_envia_reporte_por_correo(): void
    {
        Mail::fake();

        $this->actingAs($this->admin())->post(route('reportes.enviar'), [
            'tipo' => 'mensual', 'fecha' => now()->format('Y-m'), 'correo' => 'jefe@autospa.com',
        ])->assertSessionHas('exito');

        Mail::assertSent(ReporteMail::class, fn ($mail) => $mail->hasTo('jefe@autospa.com'));
    }

    public function test_comando_programado_envia_reporte_diario(): void
    {
        Mail::fake();
        Ajuste::guardar(Ajuste::CORREO_REPORTES, 'dueño@autospa.com');

        $this->artisan('reportes:enviar-diario')->assertSuccessful();
        Mail::assertSent(ReporteMail::class);
    }

    public function test_reporte_mail_se_renderiza(): void
    {
        $reporte = app(ReporteService::class)->diario(now());
        $html = (new ReporteMail($reporte))->render();

        $this->assertStringContainsString('Utilidad', $html);
    }
}
