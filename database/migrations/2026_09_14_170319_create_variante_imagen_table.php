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
        Schema::create('variante_imagen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_variante');
            $table->string('ruta_imagen', 255);
            $table->integer('es_principal')->default(0);
            $table->timestamps();

            $table->foreign('id_variante')->references('id')->on('variante')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variante_imagen');
    }
};
