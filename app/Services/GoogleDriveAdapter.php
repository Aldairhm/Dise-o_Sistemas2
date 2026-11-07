<?php

namespace App\Services;

use Masbug\Flysystem\GoogleDriveAdapter as BaseGoogleDriveAdapter;
use League\Flysystem\UnableToReadFile;
use Throwable;

class GoogleDriveAdapter extends BaseGoogleDriveAdapter
{
    /**
     * Sobrescribe listContents para evitar que se lance una excepción UnableToReadFile
     * cuando un directorio aún no existe en Google Drive.
     * 
     * Spatie Backup ejecuta $disk->files('Axstore_Respaldos') para verificar conectividad
     * y listar respaldos previos. Si el directorio no existe o no tiene archivos aún,
     * devolver un array vacío permite a Spatie considerar el disco accesible y proceder
     * a crear el directorio y subir el respaldo sin abortar.
     */
    public function listContents(string $directory, bool $recursive): iterable
    {
        try {
            return parent::listContents($directory, $recursive);
        } catch (UnableToReadFile $e) {
            return [];
        } catch (Throwable $e) {
            if (str_contains($e->getMessage(), 'File not found') || str_contains($e->getMessage(), 'Unable to read file')) {
                return [];
            }
            throw $e;
        }
    }
}

