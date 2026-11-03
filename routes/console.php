<?php

use Illuminate\Support\Facades\Schedule;

// Todos los días a las 8:00 p.m. se envía el reporte del día al correo configurado.
// En tu computador o en un servidor normal: php artisan schedule:work
// En Vercel esto no corre; allí lo hace Vercel Cron llamando a /cron/reportes
// (ver "crons" en vercel.json y app/Http/Controllers/CronController.php).
Schedule::command('reportes:enviar-diario')->dailyAt('20:00');
