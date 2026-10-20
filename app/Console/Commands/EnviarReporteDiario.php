<?php

namespace App\Console\Commands;

use App\Services\ReporteService;
use Illuminate\Console\Command;

/**
 * Envía el reporte del día por correo.
 * Se puede correr a mano:   php artisan reportes:enviar-diario
 * o dejarlo programado (ver routes/console.php).
 */
class EnviarReporteDiario extends Command
{
    protected $signature = 'reportes:enviar-diario {--fecha= : Fecha del reporte (AAAA-MM-DD). Por defecto hoy}';

    protected $description = 'Envía por correo el reporte diario del lavadero';

    public function handle(ReporteService $reportes): int
    {
        $fecha = $this->option('fecha') ? now()->parse($this->option('fecha')) : now();
        $reporte = $reportes->diario($fecha);

        if (! $reportes->enviarPorCorreo($reporte)) {
            $this->error('No se envió. Revisa el "correo para reportes" en Ajustes y la configuración MAIL_ del .env');

            return self::FAILURE;
        }

        $this->info("Listo: {$reporte->titulo} enviado.");

        return self::SUCCESS;
    }
}
