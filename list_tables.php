<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->boot();
$pdo = Illuminate\Support\Facades\DB::connection()->getPdo();
$stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
echo "=== TABLAS ===\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $table = $row['table_name'];
    echo "\n-- $table --\n";
    $cols = $pdo->query("SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = '$table' ORDER BY ordinal_position");
    foreach ($cols->fetchAll(PDO::FETCH_ASSOC) as $col) {
        echo "  {$col['column_name']} ({$col['data_type']}) nullable={$col['is_nullable']}\n";
    }
}
