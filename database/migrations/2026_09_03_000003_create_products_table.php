<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('nombre');
            $table->string('nombre_categoria')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('reserva')->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->string('imagen')->nullable();
            $table->text('nombre_producto_padre')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};