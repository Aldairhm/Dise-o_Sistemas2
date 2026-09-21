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
        if (Schema::hasTable('producto')) {
            return;
        }

        Schema::create('producto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_categoria');
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->smallInteger('estado')->default(1);
            $table->timestamps();

            // Asumiendo que existe una tabla 'categoria'
            // $table->foreign('id_categoria')->references('id')->on('categoria')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto');
    }
};
