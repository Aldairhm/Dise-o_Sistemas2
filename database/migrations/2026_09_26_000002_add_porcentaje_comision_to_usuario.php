<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("usuario", function (Blueprint $table) {
            $table->decimal("porcentaje_comision", 5, 2)->nullable()->default(null)->after("rol");
        });
    }

    public function down(): void
    {
        Schema::table("usuario", function (Blueprint $table) {
            $table->dropColumn("porcentaje_comision");
        });
    }
};
