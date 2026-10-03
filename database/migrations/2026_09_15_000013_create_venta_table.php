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
        Schema::create('venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('usuario');
            $table->timestamp('fecha')->useCurrent();
            $table->decimal('total', 10, 2);
            $table->string('metodo_pago', 50);
            $table->string('comprobante_pago', 255)->nullable();
            $table->string('nombre_cliente', 255)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->string('municipio', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->decimal('precio_envio', 10, 2)->default(0.00);
            $table->string('estado', 30)->default('Pendiente');
            $table->string('tipo_venta', 20)->default('Tienda');
            $table->text('observaciones')->nullable();
            $table->string('comprobante_paquete', 255)->nullable();
            $table->string('comprobante_devolucion', 255)->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->timestamp('fecha_cancelacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta');
    }

};
