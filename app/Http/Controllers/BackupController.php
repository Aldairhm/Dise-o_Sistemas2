<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

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
            $exitCode = Artisan::call('backup:run', [
                '--only-db'               => true,
                '--disable-notifications' => false,
            ]);

            $output = Artisan::output();

            if ($exitCode !== 0) {
                \Illuminate\Support\Facades\Log::error("Spatie Backup fallo con código $exitCode: " . $output);

                ActivityLogger::log(
                    'CANCELAR',
                    'Respaldos',
                    'Intento fallido al generar respaldo manual de base de datos MySQL.'
                );

                return redirect()
                    ->route('backups.index')
                    ->with('error', 'No se pudo completar el respaldo. Por favor revisa el log de Laravel.');
            }

            ActivityLogger::log(
                'CREAR',
                'Respaldos',
                'Generación exitosa de respaldo manual de base de datos MySQL sincronizado con almacenamiento local y Google Drive.'
            );

            return redirect()
                ->route('backups.index')
                ->with('success', 'Respaldo generado y sincronizado con Google Drive exitosamente.');
        } catch (\Exception $e) {
            ActivityLogger::log(
                'CANCELAR',
                'Respaldos',
                'Error inesperado al generar respaldo: ' . $e->getMessage()
            );

            return redirect()
                ->route('backups.index')
                ->with('error', 'Error al generar el respaldo: ' . $e->getMessage());
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
            return redirect()
                ->route('backups.index')
                ->with('error', 'El archivo de respaldo no fue encontrado.');
        }

        ActivityLogger::log(
            'EXPORTAR',
            'Respaldos',
            "El administrador descargó el archivo de respaldo: {$fileName}"
        );

        return $disk->download($filePath, $fileName);
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
