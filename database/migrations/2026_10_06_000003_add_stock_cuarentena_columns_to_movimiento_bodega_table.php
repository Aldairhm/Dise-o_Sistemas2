<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $missingColumns = array_filter(
            ['stock_cuarentena_anterior', 'stock_cuarentena_nuevo'],
            fn (string $column) => ! Schema::hasColumn('movimiento_bodega', $column)
        );

        if ($missingColumns !== []) {
            Schema::table('movimiento_bodega', function (Blueprint $table) use ($missingColumns) {
                foreach ($missingColumns as $column) {
                    $table->unsignedInteger($column)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // These columns are part of the expected movimiento_bodega schema.
    }
};
