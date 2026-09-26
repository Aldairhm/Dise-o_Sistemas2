<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salida', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_variante')->constrained('variante')->restrictOnDelete();
            $table->foreignId('id_usuario')->constrained('usuario')->restrictOnDelete();
            $table->integer('cantidad');
            $table->date('fecha_salida');
            $table->time('hora_salida');
            $table->date('fecha_entrega')->nullable();
            $table->string('direccion', 255)->nullable();
            $table->decimal('precio_envio', 10, 2)->default(0.00);
            $table->decimal('costo_extra', 10, 2)->default(0.00);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->decimal('costo_total_aplicado', 10, 2)->nullable()->default(0.00);
            $table->decimal('comision_aplicada', 10, 2)->nullable()->default(0.00);
            $table->text('observaciones')->nullable();
            $table->string('estado', 20)->default('Pendiente');
            $table->timestamp('fecha_cancelacion')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement("ALTER TABLE salida ADD CONSTRAINT chk_salida_estado CHECK (estado IN ('Pendiente', 'En camino', 'Entregado', 'Cancelado'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salida');
    }
};
