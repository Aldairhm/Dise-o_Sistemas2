<?php

$compiled = sys_get_temp_dir().'/axstore-bitacora-fixtures-'.bin2hex(random_bytes(6));
mkdir($compiled, 0700);
putenv('VIEW_COMPILED_PATH='.$compiled);

try {
    require __DIR__.'/../../vendor/autoload.php';
    $app = require __DIR__.'/../../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    Illuminate\Support\Facades\URL::forceRootUrl($argv[1]);
    $source = file_get_contents(resource_path('views/bitacora/index.blade.php'));
    $section = strpos($source, '<!-- FORMULARIO DE FILTROS AVANZADOS -->');
    $start = strpos($source, '<button type="button"', $section);
    $clear = substr($source, $start, strpos($source, '</button>', $start) + 9 - $start);
    $start = strpos($source, '<form @submit.prevent="ejecutarFiltro()"', $section);
    $form = substr($source, $start, strpos($source, '</form>', $start) + 7 - $start);
    $start = strpos($source, 'function gestionBitacora(config)');
    $script = substr($source, $start, strrpos($source, '</script>') - $start);
    echo Illuminate\Support\Facades\Blade::render($clear.$form, [
        'modulos' => collect(['Productos', 'Usuarios']),
        'acciones' => collect(['CAMBIO_ESTADO', 'CREAR', 'EDITAR']),
        'usuarios' => collect([
            (object) ['id'=>1, 'nombre_real'=>'Luis Demo', 'username'=>'luis@example.test'],
            (object) ['id'=>2, 'nombre_real'=>'Ana Demo', 'username'=>'ana@example.test'],
        ]),
    ]);
    echo '<script>'.Illuminate\Support\Facades\Blade::render($script).'</script>';
} finally {
    foreach (glob($compiled.'/*') as $file) unlink($file);
    rmdir($compiled);
}
