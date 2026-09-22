<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('variante', function (Blueprint $table) {
            $table->decimal('costo_promedio', 10, 2)->default(0.00)->after('nombre_variante');
            $table->decimal('porcentaje_ganancia', 5, 2)->default(0.00)->after('costo_promedio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variante', function (Blueprint $table) {
            $table->dropColumn(['costo_promedio', 'porcentaje_ganancia']);
        });
    }
};
