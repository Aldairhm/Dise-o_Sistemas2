<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->nullable()->constrained('usuario')->nullOnDelete();
            $table->string('username', 100)->nullable();      // guardamos por si el usuario se elimina
            $table->string('nombre_usuario', 150)->nullable();
            $table->string('rol_usuario', 50)->nullable();
            $table->string('accion', 50);                     // LOGIN, LOGOUT, CREAR, EDITAR, ELIMINAR, CAMBIO_ESTADO, etc.
            $table->string('modulo', 80);                     // Ventas, Compras, Usuarios, Productos, etc.
            $table->text('descripcion')->nullable();          // Descripción legible del evento
            $table->json('datos_anteriores')->nullable();     // Snapshot antes del cambio
            $table->json('datos_nuevos')->nullable();         // Snapshot después del cambio
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');
    }
};
