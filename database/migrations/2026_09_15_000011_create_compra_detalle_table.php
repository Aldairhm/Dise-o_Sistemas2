<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('compra_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_compra')->constrained('compra')->cascadeOnDelete();
            $table->foreignId('id_variante')->constrained('variante');
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra_detalle');
    }
};