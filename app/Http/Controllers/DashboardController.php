<?php

namespace App\Http\Controllers;

use App\Enums\EstadoLavado;
use App\Models\Gasto;
use App\Models\Lavado;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Pantalla de inicio. Muestra cosas distintas según el rol.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $usuario = $request->user();

        return $usuario->esAdmin()
            ? $this->panelAdmin()
            : $this->panelEmpleado($usuario);
    }

    private function panelAdmin(): View
    {
        $lavadosHoy = Lavado::delDia()->with('vehiculo', 'cliente', 'empleado')->latest()->get();
        $ingresosLavados = $lavadosHoy->where('pagado', true)->sum('total');
        $ingresosTienda = Venta::delDia()->sum('total');
        $gastosHoy = Gasto::whereDate('fecha', today())->sum('monto');

        // Ingresos de los últimos 7 días para la gráfica de barras
        $semana = collect(range(6, 0))->mapWithKeys(function ($diasAtras) {
            $dia = now()->subDays($diasAtras);
            $total = Lavado::delDia($dia)->pagados()->sum('total') + Venta::delDia($dia)->sum('total');

            return [$dia->translatedFormat('D d') => (float) $total];
        });

        return view('dashboard.admin', [
            'lavadosHoy' => $lavadosHoy,
            'ingresosHoy' => $ingresosLavados + $ingresosTienda,
            'ingresosLavados' => $ingresosLavados,
            'ingresosTienda' => $ingresosTienda,
            'gastosHoy' => $gastosHoy,
            'porCobrar' => $lavadosHoy->where('pagado', false)->sum('total'),
            'enProceso' => $lavadosHoy->whereIn('estado', [EstadoLavado::Pendiente, EstadoLavado::EnProceso])->count(),
            'stockBajo' => Producto::activos()->conStockBajo()->orderBy('stock')->take(5)->get(),
            'semana' => $semana,
        ]);
    }

    private function panelEmpleado($usuario): View
    {
        $misLavados = Lavado::delDia()->deEmpleado($usuario)
            ->with('vehiculo', 'cliente', 'servicios')
            ->whereNot('estado', EstadoLavado::Entregado->value)
            ->oldest()->get();

        $sinAsignar = Lavado::delDia()->whereNull('empleado_id')
            ->with('vehiculo', 'servicios')
            ->where('estado', EstadoLavado::Pendiente->value)
            ->oldest()->get();

        $terminadosHoy = Lavado::delDia()->deEmpleado($usuario)
            ->whereIn('estado', [EstadoLavado::Terminado->value, EstadoLavado::Entregado->value])->count();

        return view('dashboard.empleado', compact('misLavados', 'sinAsignar', 'terminadosHoy'));
    }
}
