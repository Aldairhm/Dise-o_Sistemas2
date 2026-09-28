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
        Schema::table('salida', function (Blueprint $table) {
            if (!Schema::hasColumn('salida', 'telefono')) {
                $table->string('telefono', 20)->nullable()->after('direccion');
            }
        });

        Schema::table('venta', function (Blueprint $table) {
            if (!Schema::hasColumn('venta', 'telefono')) {
                $table->string('telefono', 20)->nullable()->after('metodo_pago');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salida', function (Blueprint $table) {
            if (Schema::hasColumn('salida', 'telefono')) {
                $table->dropColumn('telefono');
            }
        });

        Schema::table('venta', function (Blueprint $table) {
            if (Schema::hasColumn('venta', 'telefono')) {
                $table->dropColumn('telefono');
            }
        });
    }
};
