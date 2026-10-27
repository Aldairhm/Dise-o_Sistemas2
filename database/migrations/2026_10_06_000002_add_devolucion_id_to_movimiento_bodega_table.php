<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('movimiento_bodega', 'devolucion_id')) {
            Schema::table('movimiento_bodega', function (Blueprint $table) {
                $table->unsignedBigInteger('devolucion_id')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('movimiento_bodega', 'devolucion_id')) {
            Schema::table('movimiento_bodega', function (Blueprint $table) {
                $table->dropColumn('devolucion_id');
            });
        }
    }
};
