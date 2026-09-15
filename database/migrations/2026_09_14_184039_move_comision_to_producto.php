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
        Schema::table('producto', function (Blueprint $table) {
            $table->decimal('comision', 10, 2)->default(0)->after('descripcion');
        });

        Schema::table('variante', function (Blueprint $table) {
            if (Schema::hasColumn('variante', 'comision')) {
                $table->dropColumn('comision');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variante', function (Blueprint $table) {
            $table->decimal('comision', 10, 2)->default(0)->after('reserva');
        });

        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn('comision');
        });
    }
};
