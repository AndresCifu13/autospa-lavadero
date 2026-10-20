<?php

namespace App\Http\Controllers;

use App\Enums\EstadoLavado;
use App\Enums\MetodoPago;
use App\Enums\Rol;
use App\Enums\TipoVehiculo;
use App\Http\Requests\LavadoRequest;
use App\Http\Requests\PagoRequest;
use App\Models\Lavado;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Vehiculo;
use App\Services\FidelizacionService;
use App\Services\LavadoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Registro y seguimiento de los vehículos que llegan al lavadero.
 * HU-06, HU-08, HU-10, HU-11, HU-12
 */
class LavadoController extends Controller
{
    // El servicio se "inyecta" solo: Laravel crea el objeto por nosotros
    public function __construct(private LavadoService $lavados) {}

    /** Lista de vehículos del día (HU-10). Se puede elegir otra fecha. */
    public function index(Request $request): View
    {
        $fecha = $request->date('fecha') ?? now();

        $lavados = Lavado::delDia($fecha)
            ->with('vehiculo', 'cliente', 'empleado', 'servicios')
            ->when($request->estado, fn ($q, $estado) => $q->where('estado', $estado))
            ->when($request->filled('pagado'), fn ($q) => $q->where('pagado', $request->boolean('pagado')))
            ->latest()
            ->get();

        return view('lavados.index', [
            'lavados' => $lavados,
            'fecha' => $fecha,
            'totalCobrado' => $lavados->where('pagado', true)->sum('total'),
            'totalPorCobrar' => $lavados->where('pagado', false)->sum('total'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('lavados.create', [
            'servicios' => Servicio::activos()->orderBy('nombre')->get(),
            'empleados' => User::activos()->where('rol', Rol::Empleado->value)->orderBy('name')->get(),
            'tipos' => TipoVehiculo::cases(),
            'placa' => $request->query('placa'),
        ]);
    }

    public function store(LavadoRequest $request): RedirectResponse
    {
        $lavado = $this->lavados->registrar($request->validated(), $request->user());

        $mensaje = "Vehículo {$lavado->vehiculo->placa} registrado. Total: ".dinero($lavado->total);
        if ($lavado->es_gratis) {
            $mensaje = "Vehículo {$lavado->vehiculo->placa} registrado con LAVADA GRATIS 🎉";
        }

        return redirect()->route('lavados.show', $lavado)->with('exito', $mensaje);
    }

    public function show(Lavado $lavado, FidelizacionService $fidelizacion): View
    {
        $lavado->load('vehiculo', 'cliente', 'empleado', 'registradoPor', 'servicios');

        return view('lavados.show', [
            'lavado' => $lavado,
            'progreso' => $fidelizacion->progreso($lavado->cliente),
            'metodos' => MetodoPago::cases(),
        ]);
    }

    /** Pasa al siguiente estado (Pendiente → En proceso → Terminado → Entregado). */
    public function avanzar(Lavado $lavado): RedirectResponse
    {
        $this->authorize('avanzar', $lavado);

        $this->lavados->avanzarEstado($lavado);

        $mensaje = "Placa {$lavado->vehiculo->placa}: ahora está \"{$lavado->estado->etiqueta()}\".";
        if ($lavado->estado === EstadoLavado::Terminado && $lavado->cliente->email) {
            $mensaje .= ' Se le avisó al cliente por correo.';
        }

        return back()->with('exito', $mensaje);
    }

    /** Marca como pagado (HU-11). */
    public function pagar(PagoRequest $request, Lavado $lavado): RedirectResponse
    {
        $this->authorize('pagar', $lavado);

        $ganoGratis = $this->lavados->registrarPago($lavado, $request->metodo());

        $mensaje = 'Pago registrado: '.dinero($lavado->total).' en '.$request->metodo()->etiqueta().'.';
        if ($ganoGratis) {
            $mensaje .= " 🎉 {$lavado->cliente->nombre} completó su tarjeta y ganó una lavada GRATIS.";
        }

        return back()->with('exito', $mensaje);
    }

    /** Elimina un registro (HU-12). Solo administrador. */
    public function destroy(Lavado $lavado): RedirectResponse
    {
        $this->authorize('delete', $lavado);

        $placa = $lavado->vehiculo->placa;
        $this->lavados->eliminar($lavado);

        return redirect()->route('lavados.index')->with('exito', "Se eliminó el registro de la placa {$placa}.");
    }

    /**
     * Busca una placa mientras el empleado escribe (devuelve JSON).
     * Así el formulario se llena solo si el vehículo ya vino antes.
     */
    public function buscarPlaca(Request $request, FidelizacionService $fidelizacion): JsonResponse
    {
        $placa = Vehiculo::limpiarPlaca((string) $request->query('placa', ''));
        $vehiculo = Vehiculo::with('cliente')->where('placa', $placa)->first();

        if (! $vehiculo) {
            return response()->json(['encontrado' => false]);
        }

        return response()->json([
            'encontrado' => true,
            'vehiculo' => $vehiculo->only('placa', 'marca', 'modelo', 'color') + ['tipo' => $vehiculo->tipo->value],
            'cliente' => $vehiculo->cliente->only('id', 'nombre', 'telefono', 'email'),
            'fidelizacion' => $fidelizacion->progreso($vehiculo->cliente),
            'visitas' => $vehiculo->lavados()->count(),
        ]);
    }
}
