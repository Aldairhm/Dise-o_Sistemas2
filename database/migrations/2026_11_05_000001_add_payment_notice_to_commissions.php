<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comision_vendedor', function (Blueprint $table) {
            $table->timestamp('aviso_liquidacion_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('comision_vendedor', function (Blueprint $table) {
            $table->dropColumn('aviso_liquidacion_at');
        });
    }
};
