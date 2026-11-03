<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Rules\ListaDeCorreos;
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

    public function semanal(Request $request): View
    {
        $reporte = $this->reportes->semanal($request->date('semana') ?? now());

        return view('reportes.semanal', [
            'reporte' => $reporte,
            'semana' => $reporte->desde,
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
            'tipo' => ['required', 'in:diario,semanal,mensual'],
            'fecha' => ['required', 'string'],
            'correo' => ['required', 'string', 'max:500', new ListaDeCorreos],
        ]);

        $reporte = match ($datos['tipo']) {
            'diario' => $this->reportes->diario(now()->parse($datos['fecha'])),
            'semanal' => $this->reportes->semanal(now()->parse($datos['fecha'])),
            'mensual' => $this->reportes->mensual(...array_map('intval', explode('-', $datos['fecha']))),
        };

        $correos = implode(', ', Ajuste::listaCorreos($datos['correo']));

        return $this->reportes->enviarPorCorreo($reporte, $correos)
            ? back()->with('exito', "Reporte enviado a {$correos}.")
            : back()->with('error', 'No se pudo enviar el correo. Revisa la configuración MAIL_ del archivo .env.');
    }
}
