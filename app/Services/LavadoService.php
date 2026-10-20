<?php

namespace App\Services;

use App\Enums\EstadoLavado;
use App\Enums\MetodoPago;
use App\Enums\TipoVehiculo;
use App\Exceptions\LavadoException;
use App\Models\Cliente;
use App\Models\Lavado;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Vehiculo;
use App\Notifications\VehiculoListo;
use Illuminate\Support\Facades\DB;

/**
 * Toda la lógica del proceso de un vehículo en el lavadero:
 * registrarlo, avanzar su estado, cobrarlo y eliminarlo.
 */
class LavadoService
{
    public function __construct(
        private PrecioService $precios,
        private FidelizacionService $fidelizacion,
        private NotificadorService $notificador,
    ) {}

    /**
     * Registra un vehículo que llega al lavadero (HU-06 y HU-08).
     * Si la placa ya existe, reutiliza el vehículo y su cliente.
     */
    public function registrar(array $datos, User $usuario): Lavado
    {
        // DB::transaction: si algo falla en medio, se deshace todo (no quedan datos a medias)
        return DB::transaction(function () use ($datos, $usuario) {
            $tipo = TipoVehiculo::from($datos['tipo']);
            $vehiculo = $this->obtenerVehiculo($datos, $tipo);
            $cliente = $vehiculo->cliente;

            // Solo servicios activos y que apliquen a ese tipo (ej: tapicería no aplica a motos)
            $servicios = Servicio::activos()->whereIn('id', $datos['servicios'] ?? [])->get()
                ->filter(fn (Servicio $s) => $s->precioPara($tipo) > 0);
            if ($servicios->isEmpty()) {
                throw LavadoException::sinServicios();
            }

            $detallePrecios = $this->precios->detalle($tipo, $servicios);
            $subtotal = array_sum($detallePrecios);
            $esGratis = (bool) ($datos['usar_gratis'] ?? false);

            if ($esGratis) {
                $this->fidelizacion->usarLavadaGratis($cliente);
            }

            $lavado = Lavado::create([
                'vehiculo_id' => $vehiculo->id,
                'cliente_id' => $cliente->id,
                'empleado_id' => $datos['empleado_id'] ?? null,
                'registrado_por' => $usuario->id,
                'estado' => EstadoLavado::Pendiente,
                'subtotal' => $subtotal,
                'descuento' => $esGratis ? $subtotal : 0,
                'total' => $esGratis ? 0 : $subtotal,
                'es_gratis' => $esGratis,
                // Una lavada gratis queda pagada de una vez (vale $0)
                'pagado' => $esGratis,
                'pagado_en' => $esGratis ? now() : null,
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            foreach ($detallePrecios as $servicioId => $precio) {
                $lavado->servicios()->attach($servicioId, ['precio' => $precio]);
            }

            return $lavado->load('servicios', 'vehiculo', 'cliente');
        });
    }

    /**
     * Busca el vehículo por placa. Si no existe lo crea (y al cliente también si hace falta).
     */
    private function obtenerVehiculo(array $datos, TipoVehiculo $tipo): Vehiculo
    {
        $placa = Vehiculo::limpiarPlaca($datos['placa']);
        $vehiculo = Vehiculo::withTrashed()->where('placa', $placa)->first();

        if ($vehiculo) {
            $vehiculo->restore(); // por si estaba eliminado
            $vehiculo->update(array_filter([
                'tipo' => $tipo,
                'marca' => $datos['marca'] ?? null,
                'modelo' => $datos['modelo'] ?? null,
                'color' => $datos['color'] ?? null,
            ]));

            return $vehiculo;
        }

        $cliente = $this->obtenerCliente($datos);

        return $cliente->vehiculos()->create([
            'placa' => $placa,
            'tipo' => $tipo,
            'marca' => $datos['marca'] ?? null,
            'modelo' => $datos['modelo'] ?? null,
            'color' => $datos['color'] ?? null,
        ]);
    }

    private function obtenerCliente(array $datos): Cliente
    {
        if (! empty($datos['cliente_id'])) {
            return Cliente::findOrFail($datos['cliente_id']);
        }

        // Si ya existe un cliente con ese documento, lo usamos
        if (! empty($datos['cliente_documento'])) {
            $existente = Cliente::where('documento', $datos['cliente_documento'])->first();
            if ($existente) {
                return $existente;
            }
        }

        return Cliente::create([
            'nombre' => $datos['cliente_nombre'],
            'documento' => $datos['cliente_documento'] ?? null,
            'telefono' => $datos['cliente_telefono'] ?? null,
            'email' => $datos['cliente_email'] ?? null,
        ]);
    }

    /**
     * Pasa el lavado al siguiente estado: Pendiente → En proceso → Terminado → Entregado.
     * Cuando queda "Terminado" se le avisa al cliente por correo.
     */
    public function avanzarEstado(Lavado $lavado): Lavado
    {
        $siguiente = $lavado->estado->siguiente() ?? throw LavadoException::estadoFinal();

        if ($siguiente === EstadoLavado::Entregado && ! $lavado->pagado) {
            throw LavadoException::sinPagar();
        }

        $lavado->update(['estado' => $siguiente]);

        if ($siguiente === EstadoLavado::Terminado) {
            $this->notificador->enviar($lavado->cliente, new VehiculoListo($lavado));
        }

        return $lavado;
    }

    /**
     * Marca el lavado como pagado (HU-11) y suma la lavada a la tarjeta del cliente.
     *
     * @return bool true si con este pago el cliente ganó una lavada gratis
     */
    public function registrarPago(Lavado $lavado, MetodoPago $metodo): bool
    {
        if ($lavado->pagado) {
            throw LavadoException::yaPagado();
        }

        return DB::transaction(function () use ($lavado, $metodo) {
            $lavado->update([
                'pagado' => true,
                'metodo_pago' => $metodo,
                'pagado_en' => now(),
            ]);

            return $this->fidelizacion->sumarLavada($lavado->cliente);
        });
    }

    /**
     * Elimina un registro (HU-12). Si ya contaba para la tarjeta de fidelización,
     * se descuenta para que el conteo quede correcto.
     */
    public function eliminar(Lavado $lavado): void
    {
        DB::transaction(function () use ($lavado) {
            if ($lavado->es_gratis) {
                $this->fidelizacion->devolverLavadaGratis($lavado->cliente);
            } elseif ($lavado->pagado) {
                $this->fidelizacion->restarLavada($lavado->cliente);
            }

            $lavado->delete();
        });
    }
}
