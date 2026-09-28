<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $orphanCount = DB::table('producto as p')
            ->leftJoin('categoria as c', 'p.id_categoria', '=', 'c.id')
            ->whereNotNull('p.id_categoria')
            ->whereNull('c.id')
            ->count();

        if ($orphanCount > 0) {
            throw new RuntimeException('No se puede crear la clave foránea: hay productos sin categoría válida.');
        }

        $categoryIdType = DB::table('information_schema.columns')
            ->where('table_schema', DB::connection()->getDatabaseName())
            ->where('table_name', 'categoria')
            ->where('column_name', 'id')
            ->value('column_type');

        if ($categoryIdType === null) {
            throw new RuntimeException('No se encontró categoria.id.');
        }

        if ($categoryIdType === 'int unsigned') {
            DB::statement('ALTER TABLE categoria MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        } elseif ($categoryIdType !== 'bigint unsigned') {
            throw new RuntimeException('categoria.id tiene un tipo inesperado; no se modificó.');
        }

        $foreignKey = DB::selectOne(
            "SELECT 1
            FROM information_schema.key_column_usage
            WHERE constraint_schema = DATABASE()
                AND table_name = 'producto'
                AND column_name = 'id_categoria'
                AND referenced_table_name = 'categoria'
                AND referenced_column_name = 'id'
            LIMIT 1"
        );

        if ($foreignKey === null) {
            DB::statement(
                'ALTER TABLE producto ADD CONSTRAINT producto_id_categoria_foreign '
                . 'FOREIGN KEY (id_categoria) REFERENCES categoria (id) ON DELETE SET NULL'
            );
        }
    }

    public function down(): void
    {
        $foreignKey = DB::selectOne(
            "SELECT 1
            FROM information_schema.key_column_usage
            WHERE constraint_schema = DATABASE()
                AND table_name = 'producto'
                AND constraint_name = 'producto_id_categoria_foreign'
            LIMIT 1"
        );

        if ($foreignKey !== null) {
            DB::statement('ALTER TABLE producto DROP FOREIGN KEY producto_id_categoria_foreign');
        }

        DB::statement('ALTER TABLE categoria MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT');
    }
};