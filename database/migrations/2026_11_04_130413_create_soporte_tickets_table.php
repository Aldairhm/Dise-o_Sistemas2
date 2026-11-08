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
        if (Schema::hasTable('soporte_tickets')) {
            return;
        }

        Schema::create('soporte_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_ticket', 25)->unique()->index();
            $table->foreignId('id_usuario')->constrained('usuario')->cascadeOnDelete();
            $table->string('nombre_usuario', 150);
            $table->string('rol_usuario', 50)->nullable();
            $table->string('modulo', 80);
            $table->string('tipo_consulta', 100);
            $table->text('duda');
            $table->string('evidencia_url', 500)->nullable();
            $table->string('estado', 30)->default('Pendiente')->index();
            $table->text('resolucion')->nullable();
            $table->foreignId('resuelto_por')->nullable()->constrained('usuario')->nullOnDelete();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soporte_tickets');
    }
};
