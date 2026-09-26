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
            $table->foreignId('id_salida')->constrained('salida');
            $table->decimal('monto', 10, 2);
            $table->string('estado', 20)->default('Pendiente');
            $table->timestamp('fecha_registro')->useCurrent();
        });

        DB::statement("ALTER TABLE comision_vendedor ADD CONSTRAINT chk_comision_vendedor_estado CHECK (estado IN ('Pendiente', 'Pagada'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comision_vendedor');
    }
};
