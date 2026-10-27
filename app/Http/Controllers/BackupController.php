<?php

namespace App\Http\Controllers;

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
            // --only-db → solo respaldo de base de datos (sin archivos)
            Artisan::call('backup:run', [
                '--only-db'       => true,
                '--disable-notifications' => false,
            ]);

            return redirect()
                ->route('backups.index')
                ->with('success', 'Respaldo de base de datos generado exitosamente.');
        } catch (\Exception $e) {
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
