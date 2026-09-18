<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('productoatributo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_atributo');
            $table->timestamps();
            $table->foreign('id_producto')->references('id')->on('producto')->onDelete('cascade');
            $table->foreign('id_atributo')->references('id')->on('atributo')->onDelete('cascade');
        });
    }

    public function down(): void { Schema::dropIfExists('productoatributo'); }
};