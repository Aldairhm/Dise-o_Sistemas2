<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('variantevalor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_variante');
            $table->unsignedBigInteger('id_atributo');
            $table->string('valor', 100);
            $table->timestamps();
            $table->foreign('id_variante')->references('id')->on('variante')->onDelete('cascade');
            $table->foreign('id_atributo')->references('id')->on('atributo')->onDelete('cascade');
        });
    }

    public function down(): void { Schema::dropIfExists('variantevalor'); }
};