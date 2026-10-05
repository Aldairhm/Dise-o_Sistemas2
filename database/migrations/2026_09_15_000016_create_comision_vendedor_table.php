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
        Schema::create('comision_vendedor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_vendedor')->constrained('usuario');
            $table->foreignId('id_salida')->nullable()->constrained('salida')->nullOnDelete();
            $table->string('concepto', 150)->nullable();
            $table->decimal('monto', 10, 2);
            $table->decimal('porcentaje', 5, 2)->default(0)->nullable();
            $table->string('estado', 20)->default('Pendiente');
            $table->string('metodo_pago', 50)->nullable();
            $table->string('referencia_pago', 100)->nullable();
            $table->string('comprobante_pago', 255)->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('liquidado_por')->nullable()->constrained('usuario')->nullOnDelete();
            $table->timestamp('fecha_liquidacion')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
        });

        DB::statement("ALTER TABLE comision_vendedor ADD CONSTRAINT chk_comision_vendedor_estado CHECK (estado IN ('Pendiente', 'Pagada', 'Cancelada'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comision_vendedor');
    }
};
