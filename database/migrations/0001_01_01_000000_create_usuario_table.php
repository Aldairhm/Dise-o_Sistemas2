<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_real');
            $table->string('telefono', 15)->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('rol');
            $table->integer('estado')->default(1);
            $table->string('token')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('usuario'); }
};