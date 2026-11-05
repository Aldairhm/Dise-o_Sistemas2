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

// Expiration must also notify accounts when the auditor no longer has an open browser.
Artisan::command('notifications:expire-audits', function () {
    \App\Models\AuditoriaSesion::where('estado', 'activa')->where('expira_en', '<=', now())
        ->chunkById(100, function ($audits) {
            foreach ($audits as $audit) {
                \Illuminate\Support\Facades\DB::transaction(function () use ($audit) {
                    $current = \App\Models\AuditoriaSesion::lockForUpdate()->find($audit->id);
                    if ($current && $current->estado === 'activa' && $current->expira_en->isPast()) {
                        $current->update(['estado' => 'expirada', 'finalizada_en' => now(), 'notas_cierre' => 'Sesión expirada automáticamente por límite de tiempo.']);
                    }
                });
            }
        });
})->purpose('Finaliza auditorías vencidas y persiste sus notificaciones');
Schedule::command('notifications:expire-audits')->everyMinute()->withoutOverlapping();
