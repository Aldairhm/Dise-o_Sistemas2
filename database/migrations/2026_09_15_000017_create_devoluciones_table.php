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
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();
            $table->enum('origen_tipo', ['venta', 'compra']);
            $table->unsignedBigInteger('origen_id');
            $table->string('tipo_resolucion');
            $table->enum('estado', ['pendiente', 'resuelto', 'rechazado', 'completado'])->default('completado');
            $table->json('detalles_json')->nullable();
            $table->decimal('monto_reembolsado', 10, 2)->default(0.00);
            $table->text('motivo');
            $table->string('comprobante')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
