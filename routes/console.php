<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Primero limpia los respaldos viejos según las reglas (ej. a las 10:30 PM)
Schedule::command('backup:clean')->dailyAt('22:30');

// Luego genera el respaldo de hoy (a las 11:00 PM)
Schedule::command('backup:run --only-db')->dailyAt('23:00');