<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->unsignedBigInteger('categoria_id')->nullable()->after('id');
            // Si la tabla categoria usa id como bigIncrements, esto vincula la FK.
            // Si no requieres FK estricta aún, esto es suficiente para el conteo.
            // $table->foreign('categoria_id')->references('id')->on('categoria')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('categoria_id');
        });
    }
};
