<?php

namespace App\Providers;

use App\Services\MailService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\Events\BackupHasFailed;
use Spatie\Backup\Events\BackupWasSuccessful;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ([\App\Models\User::class, \App\Models\Venta::class, \App\Models\ComisionVendedor::class, \App\Models\SoporteTicket::class, \App\Models\SoporteTicketMensaje::class, \App\Models\AuditoriaSesion::class, \App\Models\MovimientoBodega::class] as $model) {
            $model::observe(\App\Observers\NotificationObserver::class);
        }
        Event::listen(BackupHasFailed::class, function () {
            app(\App\Services\NotificationService::class)->backupFailed();
        });

        // 1. Proveedor de Google Drive Storage
        try {
            Storage::extend('google', function ($app, $config) {
                $options = [];
                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $folderId = $config['folderId'] ?? $config['folder'] ?? null;
                if (!empty($folderId) && $folderId !== '/') {
                    // Masbug adapter requiere sharedFolderId para fijar la carpeta por su ID único de Drive
                    $options['sharedFolderId'] = $folderId;
                }

                $client = new \Google\Client();
                $client->setClientId($config['clientId'] ?? '');
                $client->setClientSecret($config['clientSecret'] ?? '');
                $client->refreshToken($config['refreshToken'] ?? '');

                $service = new \Google\Service\Drive($client);
                $adapter = new \App\Services\GoogleDriveAdapter($service, '/', $options);
                $driver  = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch (\Throwable $e) {
            // Continúa sin bloquear el sistema si no hay credenciales de Drive
        }

        // 2. Escuchar eventos de Spatie Backup para ejecuciones desde consola / cron
        try {
            Event::listen(BackupWasSuccessful::class, function (BackupWasSuccessful $event) {
                // Solo ejecutar si se corre desde consola (en la interfaz web ya lo envía el BackupController)
                if (app()->runningInConsole()) {
                    MailService::sendBackupNotification(
                        isSuccess: true,
                        fileName: $event->backupName,
                        fileSize: 'Completado',
                        disk: $event->diskName
                    );
                }
            });

            Event::listen(BackupHasFailed::class, function (BackupHasFailed $event) {
                if (app()->runningInConsole()) {
                    MailService::sendBackupNotification(
                        isSuccess: false,
                        fileName: $event->backupName ?? 'No disponible',
                        fileSize: '0 KB',
                        errorMessage: $event->exception?->getMessage(),
                        disk: $event->diskName ?? 'Servidor'
                    );
                }
            });
        } catch (\Throwable $e) {
            // No interrumpir si el paquete o los eventos no están listos
        }
    }
}
