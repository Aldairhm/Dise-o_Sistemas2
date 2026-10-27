<?php

return [

    'backup' => [

        /*
         * El nombre del respaldo. Se usa como nombre de la carpeta contenedora.
         */
        'name' => env('APP_NAME', 'laravel-backup'),

        'source' => [

            'files' => [

                /*
                 * Carpetas/archivos a incluir en el respaldo.
                 * Vacío = solo base de datos (lo que necesitamos).
                 */
                'include' => [
                    // base_path(),
                ],

                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ],

                'follow_links' => false,
                'ignore_unreadable_directories' => false,
                'relative_path' => base_path(),
            ],

            'databases' => [
                'mysql', // Respalda la conexión MySQL configurada en .env
            ],
        ],

        /*
         * Formato del nombre del archivo .zip generado.
         */
        'filename_prefix' => '',

        'destination' => [

            /*
             * Comprimir el respaldo como .zip
             */
            'compression_method' => ZipArchive::CM_DEFAULT,
            'compression_level'  => 9,

            /*
             * Discos donde se almacenarán los respaldos:
             * 'local' siempre activo para descargas rápidas en el ERP.
             * 'google' se activa automáticamente al colocar las credenciales en .env.
             */
            'disks' => array_values(array_filter([
                'local',
                env('GOOGLE_DRIVE_REFRESH_TOKEN') ? 'google' : null,
            ])),
        ],

        /*
         * Directorio temporal para la creación del .zip
         */
        'temporary_directory' => storage_path('app/backup-temp'),

        'password'            => env('BACKUP_ARCHIVE_PASSWORD'),
        'encryption'          => 'default',
        'tries'               => 1,
        'retry_delay'         => 0,
    ],

    /*
     |--------------------------------------------------------------------------
     | Notificaciones
     |--------------------------------------------------------------------------
     */
    'notifications' => [

        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class         => ['mail'],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class        => ['mail'],
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class     => ['mail'],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class   => [],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class    => [],
        ],

        /*
         * Notifiable personalizado: Consulta dinámicamente en la BD
         * a todos los usuarios con rol 'admin' (con fallback a las variables .env).
         */
        'notifiable' => \App\Notifications\BackupNotifiable::class,

        'mail' => [
            /*
             * Arreglo con los correos de los administradores que recibirán las alertas.
             * Puedes colocar uno o varios correos separados por coma en BACKUP_NOTIFY_EMAILS en .env
             */
            'to' => array_values(array_filter(array_map('trim', explode(',', env('BACKUP_NOTIFY_EMAILS', env('BACKUP_NOTIFY_EMAIL', 'ascencioriveraedwinjosue@gmail.com')))))),

            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'ascencioriveraedwinjosue@gmail.com'),
                'name'    => env('MAIL_FROM_NAME', 'AXStore ERP'),
            ],
        ],

        'slack' => [
            'webhook_url' => '',
            'channel'     => null,
            'username'    => null,
            'icon'        => null,
        ],

        'discord' => [
            'webhook_url' => '',
            'username'    => '',
            'avatar_url'  => '',
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Monitoreo de salud de los respaldos
     |--------------------------------------------------------------------------
     */
    'monitor_backups' => [
        [
            'name'  => env('APP_NAME', 'laravel-backup'),
            'disks' => ['local'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class          => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class  => 5000,
            ],
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Política de Limpieza (Cleanup)
     |--------------------------------------------------------------------------
     | Retener:
     |   • Copias diarias de los últimos 7 días
     |   • 1 copia semanal durante 1 mes
     |   • Máximo ~11 archivos en total
     |--------------------------------------------------------------------------
     */
    'cleanup' => [
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [

            'keep_all_backups_for_days'                            => 7,
            'keep_daily_backups_for_days'                          => 7,
            'keep_weekly_backups_for_weeks'                        => 4,
            'keep_monthly_backups_for_months'                      => 0,
            'keep_yearly_backups_for_years'                        => 0,

            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
    ],

];

