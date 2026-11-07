<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class CloudStorage
{
    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return Storage::disk('s3')->url(ltrim($path, '/'));
    }

    public static function delete(?string $path): bool
    {
        if (!$path || filter_var($path, FILTER_VALIDATE_URL)) {
            return false;
        }

        return Storage::disk('s3')->delete(ltrim($path, '/'));
    }
}
