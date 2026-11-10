<?php

$compiled = sys_get_temp_dir().'/axstore-variant-fixtures-'.bin2hex(random_bytes(6));
mkdir($compiled, 0700);
putenv('VIEW_COMPILED_PATH='.$compiled);

try {
    require __DIR__.'/../../vendor/autoload.php';
    $app = require __DIR__.'/../../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    echo view('productos.modal-variante', [
        'producto' => (object) ['comision' => 0, 'atributos' => collect()],
    ])->render();
} finally {
    foreach (glob($compiled.'/*') as $file) {
        unlink($file);
    }
    rmdir($compiled);
}
