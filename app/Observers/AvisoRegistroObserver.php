<?php

namespace App\Observers;

use App\Models\Ajuste;
use App\Models\Cliente;
use App\Models\Gasto;
use App\Models\Lavado;
use App\Models\Producto;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Venta;
use App\Notifications\RegistroNuevo;
use App\Services\NotificadorService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * Cada vez que se crea un registro, envía un aviso a los correos de
 * Configuración → "Correos que reciben avisos".
 *
 * ShouldHandleEventsAfterCommit: espera a que la transacción termine, así el
 * lavado ya tiene sus servicios y la venta sus productos y su total.
 */
class AvisoRegistroObserver implements ShouldHandleEventsAfterCommit
{
    /** Modelo => clave del tipo de aviso (Ajuste::TIPOS_AVISO) */
    public const MODELOS = [
        Lavado::class => 'lavados',
        Venta::class => 'ventas',
        Cliente::class => 'clientes',
        Vehiculo::class => 'vehiculos',
        Gasto::class => 'gastos',
        Producto::class => 'productos',
        User::class => 'trabajadores',
    ];

    public function __construct(private NotificadorService $notificador) {}

    public function created(Model $modelo): void
    {
        // Seeders, tinker y comandos no envían avisos
        if (app()->runningInConsole()) {
            return;
        }

        try {
            $tipo = self::MODELOS[$modelo::class] ?? null;
            $correos = Ajuste::listaCorreos(Ajuste::obtener(Ajuste::CORREOS_AVISOS));

            if (! $tipo || ! $correos || ! in_array($tipo, Ajuste::avisosActivos(), true)) {
                return;
            }

            [$titulo, $icono, $datos, $url] = $this->describir($modelo->fresh() ?? $modelo);

            $this->notificador->enviar(
                Notification::route('mail', $correos),
                new RegistroNuevo($titulo, $icono, array_filter($datos, fn ($v) => $v !== null && $v !== ''), $url, auth()->user()?->name),
            );
        } catch (Throwable $e) {
            // Un aviso nunca debe impedir que se guarde el registro
            Log::error('No se pudo preparar el aviso de registro', ['modelo' => $modelo::class, 'error' => $e->getMessage()]);
        }
    }

    /** @return array{0:string, 1:string, 2:array<string, mixed>, 3:?string} */
    private function describir(Model $m): array
    {
        return match (true) {
            $m instanceof Lavado => [
                "Nuevo lavado: {$m->vehiculo?->placa}", '🚗',
                [
                    'Código' => $m->codigo(),
                    'Placa' => $m->vehiculo?->placa,
                    'Vehículo' => trim($m->vehiculo?->tipo?->etiqueta().' '.$m->vehiculo?->marca.' '.$m->vehiculo?->modelo),
                    'Cliente' => $m->cliente?->nombre,
                    'Servicios' => $m->servicios->pluck('nombre')->implode(', '),
                    'Total' => $m->es_gratis ? 'GRATIS (fidelización)' : dinero($m->total),
                    'Pago' => $m->pagado ? 'Pagado ('.$m->metodo_pago?->etiqueta().')' : 'Pendiente',
                    'Empleado' => $m->empleado?->name,
                    'Estado' => $m->estado?->etiqueta(),
                ],
                route('lavados.show', $m),
            ],
            $m instanceof Venta => [
                'Nueva venta en tienda: '.dinero($m->total), '🛒',
                [
                    'Código' => $m->codigo(),
                    'Productos' => $m->detalles()->with('producto')->get()
                        ->map(fn ($d) => "{$d->cantidad} x {$d->producto?->nombre}")->implode(', '),
                    'Total' => dinero($m->total),
                    'Método de pago' => $m->metodo_pago?->etiqueta(),
                    'Cliente' => $m->cliente?->nombre,
                    'Vendedor' => $m->vendedor?->name,
                ],
                route('ventas.show', $m),
            ],
            $m instanceof Cliente => [
                "Nuevo cliente: {$m->nombre}", '👤',
                ['Nombre' => $m->nombre, 'Documento' => $m->documento, 'Teléfono' => $m->telefono, 'Correo' => $m->email],
                route('clientes.show', $m),
            ],
            $m instanceof Vehiculo => [
                "Nuevo vehículo: {$m->placa}", '🚙',
                [
                    'Placa' => $m->placa,
                    'Tipo' => $m->tipo?->etiqueta(),
                    'Marca / modelo' => trim($m->marca.' '.$m->modelo),
                    'Color' => $m->color,
                    'Cliente' => $m->cliente?->nombre,
                ],
                $m->cliente ? route('clientes.show', $m->cliente) : route('vehiculos.index'),
            ],
            $m instanceof Gasto => [
                'Nuevo gasto: '.dinero($m->monto), '💸',
                [
                    'Descripción' => $m->descripcion,
                    'Categoría' => $m->categoria?->etiqueta(),
                    'Monto' => dinero($m->monto),
                    'Fecha' => $m->fecha?->format('d/m/Y'),
                ],
                route('gastos.index'),
            ],
            $m instanceof Producto => [
                "Nuevo producto: {$m->nombre}", '📦',
                [
                    'Nombre' => $m->nombre,
                    'Categoría' => $m->categoria,
                    'Precio' => dinero($m->precio),
                    'Stock inicial' => "{$m->stock} und (mínimo {$m->stock_minimo})",
                ],
                route('productos.index'),
            ],
            $m instanceof User => [
                "Nuevo trabajador: {$m->name}", '🧑‍🔧',
                ['Nombre' => $m->name, 'Correo' => $m->email, 'Documento' => $m->documento, 'Rol' => $m->rol?->etiqueta()],
                route('trabajadores.index'),
            ],
        };
    }
}
