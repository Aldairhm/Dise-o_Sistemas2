<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedors', function (Blueprint $table) {
            $table->id();

            // Nombre: Obligatorio, texto/números, y con índice para búsquedas rápidas
            $table->string('nombre')->index();

            // Correo: Permite nulos y debe ser único en toda la tabla
            $table->string('correo')->unique()->nullable();

            // Teléfono: Longitud exacta de 9 caracteres (8 números + 1 espacio), permite nulos
            $table->string('telefono', 9)->nullable();

            // Dirección: Texto largo (hasta 500 caracteres), permite nulos
            $table->string('direccion', 500)->nullable();

            $table->softDeletes();

            $table->timestamps(); // Crea los campos created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedors');
    }
};
