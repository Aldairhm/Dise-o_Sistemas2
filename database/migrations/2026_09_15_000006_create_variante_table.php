<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('variante', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_producto');
            $table->integer('estado')->default(1);
            $table->string('sku', 100)->nullable()->unique();
            $table->string('hash_combinacion', 32)->nullable();
            $table->string('nombre_variante', 200);
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('comision', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('reserva')->default(0);
            $table->decimal('costo_promedio', 10, 2)->default(0.00);
            $table->decimal('porcentaje_ganancia', 5, 2)->default(0.00);
            $table->string('imagen', 255)->default('');
            $table->timestamps();
            $table->foreign('id_producto')->references('id')->on('producto')->onDelete('cascade');
        });
    }

    public function down(): void { Schema::dropIfExists('variante'); }
};