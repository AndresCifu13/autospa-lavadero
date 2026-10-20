<?php

use Illuminate\Support\Facades\Schedule;

// Todos los días a las 8:00 p.m. se envía el reporte del día al correo configurado.
// Para que funcione en el servidor hay que dejar corriendo: php artisan schedule:work
Schedule::command('reportes:enviar-diario')->dailyAt('20:00');
