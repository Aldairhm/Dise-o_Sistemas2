<?php

// Render the real component without leaving compiled fixtures in the checkout.
$compiled = sys_get_temp_dir().'/axstore-button-fixtures-'.bin2hex(random_bytes(6));
mkdir($compiled, 0700);
putenv('VIEW_COMPILED_PATH='.$compiled);

try {
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo Illuminate\Support\Facades\Blade::render(<<<'BLADE'
<div x-data="{ saving: false, blocked: false }" id="componentScope">
    <x-loading-button id="componentDynamic" x-show-loading="saving" x-bind:disabled="blocked" loading-text="Guardando...">Guardar</x-loading-button>
</div>
<x-loading-button id="componentIdle">Guardar</x-loading-button>
<x-loading-button id="componentBusy" :loading="true">Guardar</x-loading-button>
BLADE);
} finally {
    foreach (glob($compiled.'/*') as $file) {
        unlink($file);
    }
    rmdir($compiled);
}
