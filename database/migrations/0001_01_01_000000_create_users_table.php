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
        // 1. Forzamos el nombre de la tabla a 'usuario'
        Schema::create('usuario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_real'); // Reemplaza al 'name' tradicional
            $table->string('username')->unique(); // Reemplaza al 'email'
            $table->string('password');
            $table->string('rol');
            $table->integer('estado')->default(1); // Inyectamos su estado manual
            $table->string('token')->nullable(); // Su token manual para correos
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Adaptamos la tabla de resets por si acaso algún día se usa
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('username')->primary(); // Cambiado de email a username
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. La tabla de sesiones se queda intacta
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario'); // Actualizado aquí también
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};