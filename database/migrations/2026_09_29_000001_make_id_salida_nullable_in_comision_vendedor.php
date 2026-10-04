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
        Schema::table('comision_vendedor', function (Blueprint $table) {
            $table->unsignedBigInteger('id_salida')->nullable()->change();
            if (Schema::hasColumn('comision_vendedor', 'porcentaje')) {
                $table->decimal('porcentaje', 5, 2)->default(0)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comision_vendedor', function (Blueprint $table) {
            $table->unsignedBigInteger('id_salida')->nullable(false)->change();
        });
    }
};
