<?php

namespace App\Http\Controllers;

use App\Services\ReporteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Tareas automáticas que Vercel Cron llama una vez al día (ver "crons" en vercel.json).
 * En Vercel no existe "php artisan schedule:work", por eso se hace con una URL protegida.
 *
 * Todos los días envía el reporte diario; los domingos también el semanal
 * y el último día del mes también el mensual.
 */
class CronController extends Controller
{
    public function reportes(Request $request, ReporteService $reportes): JsonResponse
    {
        $secreto = config('services.cron.secret');

        // Vercel manda "Authorization: Bearer <CRON_SECRET>"
        if (! $secreto || ! hash_equals('Bearer '.$secreto, (string) $request->header('Authorization'))) {
            abort(401);
        }

        $hoy = now();
        $enviados = ['diario' => $reportes->enviarPorCorreo($reportes->diario($hoy))];

        if ($hoy->isSunday()) {
            $enviados['semanal'] = $reportes->enviarPorCorreo($reportes->semanal($hoy));
        }

        if ($hoy->isLastOfMonth()) {
            $enviados['mensual'] = $reportes->enviarPorCorreo($reportes->mensual($hoy->year, $hoy->month));
        }

        return response()->json(['fecha' => $hoy->toDateString(), 'enviados' => $enviados]);
    }
}
