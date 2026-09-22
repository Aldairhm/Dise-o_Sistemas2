<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_categoria')->nullable();
            $table->foreign('id_categoria')->references('id')->on('categoria')->nullOnDelete();
            $table->string('sku')->nullable()->unique();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('comision', 10, 2)->default(0);
            $table->string('marca', 100)->nullable();
            $table->string('imagen_principal')->nullable();
            $table->smallInteger('estado')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('producto'); }
};