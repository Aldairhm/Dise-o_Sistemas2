<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupController extends Controller
{
    /**
     * Mostrar el historial de respaldos desde el disco local.
     */
    public function index()
    {
        $disk = Storage::disk('local');

        // Spatie guarda los .zip en: {disk_root}/{app_name}/
        $backupFolder = config('backup.backup.name', 'laravel-backup');
        $path = $backupFolder;

        $files = [];

        if ($disk->exists($path)) {
            $zipFiles = collect($disk->files($path))
                ->filter(fn($file) => str_ends_with($file, '.zip'))
                ->sortByDesc(fn($file) => $disk->lastModified($file));

            foreach ($zipFiles as $file) {
                $files[] = [
                    'name'     => basename($file),
                    'path'     => $file,
                    'size'     => $this->formatBytes($disk->size($file)),
                    'size_raw' => $disk->size($file),
                    'date'     => \Carbon\Carbon::createFromTimestamp(
                        $disk->lastModified($file)
                    )->format('d/m/Y H:i:s'),
                    'date_human' => \Carbon\Carbon::createFromTimestamp(
                        $disk->lastModified($file)
                    )->diffForHumans(),
                ];
            }
        }

        return view('backups.index', compact('files'));
    }

    /**
     * Generar un nuevo respaldo ejecutando el comando de Spatie.
     */
    public function create()
    {
        try {
            // Aumentar el tiempo límite para permitir el volcado y la subida a Drive
            set_time_limit(300);

            // Generar respaldo (Base de datos - Stateless)
            // Desactivamos las notificaciones nativas de Spatie para usar nuestro MailService unificado
            $exitCode = Artisan::call('backup:run', [
                '--only-db' => true,
                '--disable-notifications' => true,
            ]);

            $output = Artisan::output();

            // Localizar el archivo generado en el almacenamiento local
            $disk = Storage::disk('local');
            $backupFolder = config('backup.backup.name', 'Axstore_Respaldos');
            $latestFile = collect($disk->files($backupFolder))
                ->filter(fn($f) => str_ends_with($f, '.zip'))
                ->sortByDesc(fn($f) => $disk->lastModified($f))
                ->first();

            // Verificar si el archivo local fue creado en los últimos 2 minutos
            $isRecentlyCreated = $latestFile && (time() - $disk->lastModified($latestFile)) < 120;

            // Si Spatie devolvió código de error Y no se generó el archivo local, reportar fallo
            if ($exitCode !== 0 && ! $isRecentlyCreated) {
                Log::error("Spatie Backup fallo con código $exitCode: " . $output);

                ActivityLogger::log(
                    'CANCELAR',
                    'Respaldos',
                    'Intento fallido al generar respaldo del sistema.'
                );

                // Notificar por correo a los administradores del fallo
                MailService::sendBackupNotification(
                    isSuccess: false,
                    fileName: 'No generado',
                    fileSize: '0 KB',
                    errorMessage: 'El comando de respaldo finalizó con código: ' . $exitCode . "\n" . substr($output, 0, 500)
                );

                return redirect()
                    ->route('backups.index')
                    ->with('error', 'No se pudo completar el respaldo. Por favor revisa el log de Laravel.');
            }

            $fileName = $latestFile ? basename($latestFile) : 'backup-' . date('Y-m-d-His') . '.zip';
            $fileSize = $latestFile ? $this->formatBytes($disk->size($latestFile)) : 'Desconocido';

            $mensajeExito = 'Respaldo generado exitosamente.';
            if ($exitCode !== 0) {
                Log::warning("Respaldo creado con éxito en local pero falló sincronización a Google Drive: " . $output);
                $mensajeExito = 'Respaldo guardado exitosamente en el servidor local. (Aviso: No se pudo subir a Google Drive).';
            }

            ActivityLogger::log(
                'CREAR',
                'Respaldos',
                "Generación exitosa de respaldo del sistema (BD): {$fileName} ({$fileSize})."
            );

            // Notificación UNIFICADA por correo a los administradores
            MailService::sendBackupNotification(
                isSuccess: true,
                fileName: $fileName,
                fileSize: $fileSize,
                errorMessage: $exitCode !== 0 ? 'Guardado en local. Hubo una advertencia en Google Drive.' : null
            );

            return redirect()
                ->route('backups.index')
                ->with('success', $mensajeExito);

        } catch (\Exception $e) {
            app(\App\Services\NotificationService::class)->backupFailed();
            ActivityLogger::log(
                'CANCELAR',
                'Respaldos',
                'Error inesperado al generar respaldo: ' . $e->getMessage()
            );

            MailService::sendBackupNotification(
                isSuccess: false,
                fileName: 'No generado',
                fileSize: '0 KB',
                errorMessage: $e->getMessage()
            );

            return redirect()
                ->route('backups.index')
                ->with('error', 'Error al generar el respaldo: ' . $e->getMessage());
        }
    }

    public function restoreDatabase(Request $request)
    {
        // 1. Defensa: Validación estricta exigiendo exclusivamente extensión .zip (hasta 100MB)
        $request->validate([
            'password'    => ['required', 'string', 'current_password'],
            'file_name'   => ['nullable', 'string'],
            'backup_file' => ['nullable', 'file', 'extensions:zip', 'max:102400'],
        ], [
            'password.required'         => 'Debes ingresar tu contraseña para autorizar la restauración.',
            'password.current_password' => 'La contraseña de confirmación es incorrecta.',
            'backup_file.extensions'    => 'El archivo de respaldo debe tener estrictamente la extensión .zip.',
            'backup_file.max'           => 'El archivo de respaldo no debe superar el límite de 100MB.',
        ]);

        if (! $request->hasFile('backup_file') && ! $request->filled('file_name')) {
            return back()->withErrors('Debes seleccionar un respaldo existente o subir un archivo comprimido (.zip).');
        }

        $origen = '';
        $tempExtractPath = storage_path('app/restore-temp-' . time());

        try {
            // Aumentar tiempo límite y memoria para la descompresión e importación
            set_time_limit(600);
            ini_set('memory_limit', '512M');

            // Determinar origen y ruta del archivo .zip a procesar
            if ($request->hasFile('backup_file')) {
                $file = $request->file('backup_file');
                $zipPath = $file->getRealPath();
                $origen = 'Archivo subido: ' . $file->getClientOriginalName();
            } else {
                $backupFolder = config('backup.backup.name', 'Axstore_Respaldos');
                $fileName = basename($request->input('file_name'));
                $filePath = $backupFolder . '/' . $fileName;
                $disk = Storage::disk('local');

                if (! $disk->exists($filePath)) {
                    return back()->withErrors("El archivo de respaldo seleccionado no existe en el almacenamiento: {$fileName}");
                }

                $zipPath = $disk->path($filePath);
                $origen = "Historial local: {$fileName}";
            }

            // Crear directorio temporal único si no existe
            if (! File::isDirectory($tempExtractPath)) {
                File::makeDirectory($tempExtractPath, 0755, true);
            }

            // 2. Extraer archivo .zip con ZipArchive
            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) {
                throw new \Exception('No se pudo abrir el archivo .zip de respaldo.');
            }

            // Los respaldos generados por Spatie pueden estar cifrados con la
            // misma contraseña configurada en BACKUP_ARCHIVE_PASSWORD.
            $archivePassword = config('backup.backup.password');
            if (is_string($archivePassword) && $archivePassword !== '') {
                $zip->setPassword($archivePassword);
            }

            if ($zip->extractTo($tempExtractPath) !== true) {
                $zip->close();
                throw new \Exception(
                    $archivePassword
                        ? 'No se pudo extraer el respaldo. Verifica que el ZIP corresponda a la contraseña configurada para los respaldos.'
                        : 'No se pudo extraer el contenido del archivo .zip de respaldo.'
                );
            }
            $zip->close();

            // 3. Restauración de Base de Datos: Escanear archivo .sql (normalmente en db-dumps/)
            $sqlFiles = collect(File::allFiles($tempExtractPath))
                ->filter(fn($f) => strtolower($f->getExtension()) === 'sql')
                ->values();

            if ($sqlFiles->isEmpty()) {
                throw new \Exception('El archivo comprimido .zip no contiene ningún script .sql para restaurar.');
            }

            // Seleccionar preferentemente el volcado en db-dumps o el primero encontrado
            $sqlFile = $sqlFiles->first(fn($f) => str_contains($f->getPathname(), 'db-dumps')) ?? $sqlFiles->first();
            $sqlContent = File::get($sqlFile->getRealPath());

            if (empty(trim($sqlContent))) {
                throw new \Exception('El script SQL extraído se encuentra vacío.');
            }

            // Inspección y verificación de seguridad anti-malware previa
            $this->validarSeguridadSql($sqlContent);

            // Desactivar temporalmente FOREIGN_KEY_CHECKS, ejecutar DB::unprepared() y reactivar inmediatamente
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            try {
                DB::unprepared($sqlContent);
            } finally {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            // 4. Limpieza: Destruir la carpeta temporal extraída
            if (File::isDirectory($tempExtractPath)) {
                File::deleteDirectory($tempExtractPath);
            }

            // 5. Auditoría y Notificaciones
            ActivityLogger::log(
                'RESTAURAR',
                'Respaldos',
                "Restauración completa de base de datos finalizada exitosamente ({$origen})"
            );

            // 5. Notificar a todos los administradores activos mediante el MailService unificado
            $notifications = app(\App\Services\NotificationService::class);
            $notifications->publish('database-restored:'.\Illuminate\Support\Str::uuid(), 'base_restaurada', 'administracion', 'critica',
                'Base de datos restaurada', 'Se confirmó una restauración de la base de datos. Consulta la bitácora autorizada.',
                'backup', null, $notifications->roles('super_admin'));
            MailService::sendDatabaseRestorationNotification(true, $origen, null, $request);

            // Renovar marca de confirmación de contraseña en sesión
            $request->session()->put('auth.password_confirmed_at', time());

            return redirect()
                ->route('backups.index')
                ->with('success', "Base de datos restaurada con éxito ({$origen}).");

        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            if (isset($tempExtractPath) && File::isDirectory($tempExtractPath)) {
                File::deleteDirectory($tempExtractPath);
            }

            ActivityLogger::log(
                'CANCELAR',
                'Respaldos',
                "Error en restauración ({$origen}): " . $e->getMessage()
            );

            Log::error('Error en restauración del sistema: ' . $e->getMessage(), ['exception' => $e]);

            MailService::sendDatabaseRestorationNotification(false, $origen, $e->getMessage(), $request);

            return back()->withErrors('Error al restaurar el sistema: ' . $e->getMessage());
        }
    }

    /**
     * Descargar un archivo .zip de respaldo desde el disco local.
     */
    public function download(string $fileName)
    {
        $backupFolder = config('backup.backup.name', 'laravel-backup');
        $filePath = $backupFolder . '/' . $fileName;
        $disk = Storage::disk('local');

        if (! $disk->exists($filePath)) {
            ActivityLogger::log(
                'CANCELAR',
                'Respaldos',
                "Intento fallido de descarga de respaldo inexistente: {$fileName}"
            );

            return redirect()
                ->route('backups.index')
                ->with('error', 'El archivo de respaldo no fue encontrado.');
        }

        ActivityLogger::log(
            'EXPORTAR',
            'Respaldos',
            "El administrador descargó exitosamente el archivo de respaldo: {$fileName}"
        );

        return $disk->download($filePath, $fileName);
    }

    /**
     * Valida la legitimidad y seguridad del contenido SQL antes de ejecutarlo.
     * Previene inyecciones de web shells, comandos del sistema de archivos y código malicioso.
     */
    private function validarSeguridadSql(string $sql): void
    {
        // 1. Debe contener al menos una estructura típica de SQL (CREATE, INSERT, DROP, ALTER, TABLE, etc.)
        $tieneEstructuraSql = preg_match('/\b(CREATE|INSERT\s+INTO|DROP\s+TABLE|ALTER\s+TABLE|LOCK\s+TABLES|REPLACE\s+INTO)\b/i', $sql);
        if (! $tieneEstructuraSql) {
            throw new \Exception('El archivo no parece ser un volcado de base de datos SQL válido o estructurado.');
        }

        // 2. Bloquear patrones de ataque dirigidos a escribir web shells en el servidor mediante MySQL
        $patronesPeligrosos = [
            '/\bINTO\s+OUTFILE\b/i'       => 'Intento de escritura arbitraria de archivos detectado (INTO OUTFILE).',
            '/\bINTO\s+DUMPFILE\b/i'      => 'Intento de volcado arbitrario de archivos detectado (INTO DUMPFILE).',
            '/\bLOAD_FILE\s*\(/i'         => 'Intento de lectura arbitraria de archivos del servidor detectado (LOAD_FILE).',
            '/\bLOAD\s+DATA\s+LOCAL\b/i'  => 'Intento de carga de archivos locales detectado (LOAD DATA LOCAL).',
            '/\bxp_cmdshell\b/i'          => 'Intento de ejecución de comandos detectado (xp_cmdshell).',
            '/<\?(php|=)/i'               => 'Se detectaron etiquetas de código PHP incrustadas dentro del script SQL.',
        ];

        foreach ($patronesPeligrosos as $patron => $mensaje) {
            if (preg_match($patron, $sql)) {
                throw new \Exception("Alerta de Seguridad: {$mensaje}");
            }
        }
    }

    /**
     * Convertir bytes a formato legible.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
