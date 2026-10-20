<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Services\ReporteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Reportes y finanzas (HU-15, HU-16, HU-17). Solo administrador.
 */
class ReporteController extends Controller
{
    public function __construct(private ReporteService $reportes) {}

    public function diario(Request $request): View
    {
        $fecha = $request->date('fecha') ?? now();

        return view('reportes.diario', [
            'reporte' => $this->reportes->diario($fecha),
            'fecha' => $fecha,
            'correo' => Ajuste::obtener(Ajuste::CORREO_REPORTES),
        ]);
    }

    public function mensual(Request $request): View
    {
        $mes = $request->date('mes', 'Y-m') ?? now();

        return view('reportes.mensual', [
            'reporte' => $this->reportes->mensual($mes->year, $mes->month),
            'mes' => $mes,
            'correo' => Ajuste::obtener(Ajuste::CORREO_REPORTES),
        ]);
    }

    /** Envía el reporte por correo (HU-16). */
    public function enviar(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'tipo' => ['required', 'in:diario,mensual'],
            'fecha' => ['required', 'string'],
            'correo' => ['required', 'email'],
        ]);

        $reporte = $datos['tipo'] === 'diario'
            ? $this->reportes->diario(now()->parse($datos['fecha']))
            : $this->reportes->mensual(...array_map('intval', explode('-', $datos['fecha'])));

        return $this->reportes->enviarPorCorreo($reporte, $datos['correo'])
            ? back()->with('exito', "Reporte enviado a {$datos['correo']}.")
            : back()->with('error', 'No se pudo enviar el correo. Revisa la configuración MAIL_ del archivo .env.');
    }
}
