<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categoria', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->string('color', 7)->default('#3b82f6');
            $table->boolean('estado')->default(true);
            $table->string('icono')->default('fa-tag');
            $table->integer('orden')->default(0);
        });
    }

    public function down(): void { Schema::dropIfExists('categoria'); }
};