<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('movimiento_bodega', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_variante')->constrained('variante');
            $table->foreignId('id_compra')->nullable()->constrained('compra')->nullOnDelete();
            $table->foreignId('id_usuario')->nullable()->constrained('usuario')->nullOnDelete();
            $table->string('tipo', 30);
            $table->unsignedInteger('cantidad');
            $table->unsignedInteger('reserva_anterior');
            $table->unsignedInteger('reserva_nueva');
            $table->unsignedInteger('stock_anterior')->nullable();
            $table->unsignedInteger('stock_nuevo')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimiento_bodega');
    }
};