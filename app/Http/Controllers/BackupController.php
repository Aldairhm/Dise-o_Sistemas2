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

            // --only-db → solo respaldo de base de datos (sin archivos)
            // Desactivamos las notificaciones nativas de Spatie para usar nuestro MailService unificado
            $exitCode = Artisan::call('backup:run', [
                '--only-db'               => true,
                '--disable-notifications' => true,
            ]);

            $output = Artisan::output();

            if ($exitCode !== 0) {
                app(\App\Services\NotificationService::class)->backupFailed();
                Log::error("Spatie Backup fallo con código $exitCode: " . $output);

                ActivityLogger::log(
                    'CANCELAR',
                    'Respaldos',
                    'Intento fallido al generar respaldo manual de base de datos MySQL.'
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

            // Localizar el último archivo generado para reportar nombre y tamaño exacto
            $disk = Storage::disk('local');
            $backupFolder = config('backup.backup.name', 'laravel-backup');
            $latestFile = collect($disk->files($backupFolder))
                ->filter(fn($f) => str_ends_with($f, '.zip'))
                ->sortByDesc(fn($f) => $disk->lastModified($f))
                ->first();

            $fileName = $latestFile ? basename($latestFile) : 'backup-' . date('Y-m-d-His') . '.zip';
            $fileSize = $latestFile ? $this->formatBytes($disk->size($latestFile)) : 'Desconocido';

            ActivityLogger::log(
                'CREAR',
                'Respaldos',
                "Generación exitosa de respaldo manual de base de datos MySQL: {$fileName} ({$fileSize})."
            );

            // Notificación UNIFICADA por correo a los administradores
            MailService::sendBackupNotification(
                isSuccess: true,
                fileName: $fileName,
                fileSize: $fileSize
            );

            return redirect()
                ->route('backups.index')
                ->with('success', 'Respaldo generado exitosamente. Se ha notificado por correo a los administradores.');

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
        // 1. Defensa: Validación estricta de credenciales y archivo
        $request->validate([
            'password'    => ['required', 'string', 'current_password'],
            'file_name'   => ['nullable', 'string'],
            'backup_file' => ['nullable', 'file', 'extensions:sql,zip', 'max:51200'],
        ], [
            'password.required'         => 'Debes ingresar tu contraseña para autorizar la restauración.',
            'password.current_password' => 'La contraseña de confirmación es incorrecta.',
            'backup_file.extensions'    => 'El archivo debe ser un script SQL válido (.sql) o un respaldo comprimido (.zip).',
            'backup_file.max'           => 'El archivo de respaldo no debe superar los 50MB.',
        ]);

        if (! $request->hasFile('backup_file') && ! $request->filled('file_name')) {
            return back()->withErrors('Debes seleccionar un respaldo existente o subir un archivo (.sql o .zip).');
        }

        $origen = '';
        $sqlContent = '';

        try {
            // Aumentar tiempo límite y memoria para la importación
            set_time_limit(600);
            ini_set('memory_limit', '512M');

            // 2. Extracción del contenido SQL según la fuente
            if ($request->hasFile('backup_file')) {
                $file = $request->file('backup_file');
                $ext = strtolower($file->getClientOriginalExtension());
                $origen = 'Archivo subido: ' . $file->getClientOriginalName();

                if ($ext === 'zip') {
                    $zip = new ZipArchive();
                    if ($zip->open($file->getRealPath()) !== true) {
                        throw new \Exception('No se pudo abrir el archivo .zip cargado.');
                    }

                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $name = $zip->getNameIndex($i);
                        if (str_ends_with(strtolower($name), '.sql')) {
                            $stat = $zip->statIndex($i);
                            if (($stat['size'] ?? 0) > 100 * 1024 * 1024) {
                                $zip->close();
                                throw new \Exception('El archivo SQL descomprimido supera el límite de seguridad permitido (100MB).');
                            }
                            $sqlContent = $zip->getFromIndex($i);
                            break;
                        }
                    }
                    $zip->close();

                    if (! $sqlContent) {
                        throw new \Exception('El archivo comprimido .zip no contiene ningún script .sql válido.');
                    }
                } else {
                    $sqlContent = file_get_contents($file->getRealPath());
                }
            } else {
                // Restauración desde historial local existente
                $backupFolder = config('backup.backup.name', 'laravel-backup');
                $fileName = basename($request->input('file_name'));
                $filePath = $backupFolder . '/' . $fileName;
                $disk = Storage::disk('local');

                if (! $disk->exists($filePath)) {
                    throw new \Exception("El archivo de respaldo seleccionado no existe en el almacenamiento: {$fileName}");
                }

                $origen = "Historial local: {$fileName}";
                $realPath = $disk->path($filePath);

                $zip = new ZipArchive();
                if ($zip->open($realPath) !== true) {
                    throw new \Exception("No se pudo abrir el archivo de respaldo {$fileName}.");
                }

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    if (str_ends_with(strtolower($name), '.sql')) {
                        $stat = $zip->statIndex($i);
                        if (($stat['size'] ?? 0) > 100 * 1024 * 1024) {
                            $zip->close();
                            throw new \Exception('El archivo SQL descomprimido supera el límite de seguridad permitido (100MB).');
                        }
                        $sqlContent = $zip->getFromIndex($i);
                        break;
                    }
                }
                $zip->close();

                if (! $sqlContent) {
                    throw new \Exception("No se encontró ningún script .sql dentro del archivo {$fileName}.");
                }
            }

            if (empty(trim($sqlContent))) {
                throw new \Exception('El script SQL está vacío o no contiene instrucciones ejecutables.');
            }

            // 3. Inspección y Verificación de Seguridad Anti-Malware en el contenido SQL
            $this->validarSeguridadSql($sqlContent);

            // 4. Ejecución segura en MySQL desactivando temporalmente llaves foráneas
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            try {
                DB::unprepared($sqlContent);
            } finally {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            // 4. Registro en Bitácora de Auditoría
            ActivityLogger::log(
                'RESTAURAR',
                'Respaldos',
                "Restauración de base de datos completada exitosamente ({$origen})"
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

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            ActivityLogger::log(
                'CANCELAR',
                'Respaldos',
                "Error en restauración de base de datos ({$origen}): " . $e->getMessage()
            );

            Log::error('Error en restauración DB: ' . $e->getMessage(), ['exception' => $e]);

            // Notificar a los administradores del intento fallido de restauración
            MailService::sendDatabaseRestorationNotification(false, $origen, $e->getMessage(), $request);

            return back()->withErrors('Error al restaurar la base de datos: ' . $e->getMessage());
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
