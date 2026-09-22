<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proveedor')->constrained('proveedors');
            $table->foreignId('id_usuario')->nullable()->constrained('usuario')->nullOnDelete();
            $table->date('fecha_compra');
            $table->string('referencia')->nullable();
            $table->text('observaciones')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('estado', 20)->default('recibida');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra');
    }
};