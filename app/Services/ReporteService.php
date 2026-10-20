<?php

namespace App\Services;

use App\DTO\ResumenReporte;
use App\Enums\MetodoPago;
use App\Mail\ReporteMail;
use App\Models\Ajuste;
use App\Models\Gasto;
use App\Models\Lavado;
use App\Models\Venta;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Arma los reportes de ingresos, gastos y utilidad (HU-15 y HU-17).
 */
class ReporteService
{
    /** Reporte de un solo día (HU-15). */
    public function diario(Carbon $fecha): ResumenReporte
    {
        return $this->generar(
            'Reporte diario del '.$fecha->translatedFormat('d \d\e F \d\e Y'),
            $fecha->copy()->startOfDay(),
            $fecha->copy()->endOfDay(),
        );
    }

    /** Reporte de un mes completo, con el detalle día por día (HU-17). */
    public function mensual(int $anio, int $mes): ResumenReporte
    {
        $desde = Carbon::create($anio, $mes, 1)->startOfMonth();

        return $this->generar(
            'Reporte mensual de '.$desde->translatedFormat('F \d\e Y'),
            $desde,
            $desde->copy()->endOfMonth(),
            conDetallePorDia: true,
        );
    }

    /**
     * Envía el reporte por correo (HU-16).
     * Si no se indica correo, usa el configurado en Ajustes.
     */
    public function enviarPorCorreo(ResumenReporte $reporte, ?string $correo = null): bool
    {
        $correo ??= Ajuste::obtener(Ajuste::CORREO_REPORTES);

        if (! $correo) {
            return false;
        }

        try {
            Mail::to($correo)->send(new ReporteMail($reporte));

            return true;
        } catch (Throwable $e) {
            Log::error('No se pudo enviar el reporte por correo', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function generar(string $titulo, Carbon $desde, Carbon $hasta, bool $conDetallePorDia = false): ResumenReporte
    {
        $lavados = Lavado::with(['servicios', 'empleado'])
            ->whereBetween('created_at', [$desde, $hasta])
            ->get();
        $pagados = $lavados->where('pagado', true);

        $ventas = Venta::whereBetween('created_at', [$desde, $hasta])->get();
        $gastos = Gasto::whereDate('fecha', '>=', $desde)->whereDate('fecha', '<=', $hasta)->get();

        return new ResumenReporte(
            titulo: $titulo,
            desde: $desde,
            hasta: $hasta,
            cantidadLavados: $lavados->count(),
            lavadosPendientesDePago: $lavados->where('pagado', false)->count(),
            lavadasGratis: $lavados->where('es_gratis', true)->count(),
            ingresosLavados: (float) $pagados->sum('total'),
            cantidadVentas: $ventas->count(),
            ingresosTienda: (float) $ventas->sum('total'),
            totalGastos: (float) $gastos->sum('monto'),
            porMetodoPago: $this->totalesPorMetodo($pagados, $ventas),
            porServicio: $this->totalesPorServicio($lavados),
            porEmpleado: $this->totalesPorEmpleado($lavados),
            gastosPorCategoria: $gastos->groupBy(fn (Gasto $g) => $g->categoria->etiqueta())
                ->map->sum('monto')->sortDesc()->all(),
            porDia: $conDetallePorDia ? $this->totalesPorDia($desde, $hasta, $pagados, $ventas, $gastos) : [],
        );
    }

    private function totalesPorMetodo($lavadosPagados, $ventas): array
    {
        $totales = [];
        foreach (MetodoPago::cases() as $metodo) {
            $valor = $lavadosPagados->where('metodo_pago', $metodo)->sum('total')
                + $ventas->where('metodo_pago', $metodo)->sum('total');
            if ($valor > 0) {
                $totales[$metodo->etiqueta()] = (float) $valor;
            }
        }

        return $totales;
    }

    private function totalesPorServicio($lavados): array
    {
        return $lavados->flatMap->servicios
            ->groupBy('id')
            ->map(fn ($grupo) => [
                'nombre' => $grupo->first()->nombre,
                'cantidad' => $grupo->count(),
                'total' => (float) $grupo->sum('pivot.precio'),
            ])
            ->sortByDesc('cantidad')->values()->all();
    }

    private function totalesPorEmpleado($lavados): array
    {
        return $lavados->whereNotNull('empleado_id')
            ->groupBy('empleado_id')
            ->map(fn ($grupo) => [
                'nombre' => $grupo->first()->empleado?->name ?? 'Sin nombre',
                'cantidad' => $grupo->count(),
                'total' => (float) $grupo->where('pagado', true)->sum('total'),
            ])
            ->sortByDesc('cantidad')->values()->all();
    }

    private function totalesPorDia(Carbon $desde, Carbon $hasta, $lavados, $ventas, $gastos): array
    {
        $dias = [];
        for ($dia = $desde->copy(); $dia->lte($hasta); $dia->addDay()) {
            $fecha = $dia->toDateString();
            $dias[$fecha] = [
                'ingresos' => (float) ($lavados->filter(fn ($l) => $l->created_at->toDateString() === $fecha)->sum('total')
                    + $ventas->filter(fn ($v) => $v->created_at->toDateString() === $fecha)->sum('total')),
                'gastos' => (float) $gastos->filter(fn ($g) => $g->fecha->toDateString() === $fecha)->sum('monto'),
            ];
        }

        return $dias;
    }
}
