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
        Schema::table('comision_vendedor', function (Blueprint $table) {
            if (!Schema::hasColumn('comision_vendedor', 'metodo_pago')) {
                $table->string('metodo_pago', 50)->nullable()->after('estado');
            }
            if (!Schema::hasColumn('comision_vendedor', 'referencia_pago')) {
                $table->string('referencia_pago', 100)->nullable()->after('metodo_pago');
            }
            if (!Schema::hasColumn('comision_vendedor', 'comprobante_pago')) {
                $table->string('comprobante_pago', 255)->nullable()->after('referencia_pago');
            }
            if (!Schema::hasColumn('comision_vendedor', 'concepto')) {
                $table->string('concepto', 150)->nullable()->after('id_salida');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comision_vendedor', function (Blueprint $table) {
            $columns = ['metodo_pago', 'referencia_pago', 'comprobante_pago', 'concepto'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('comision_vendedor', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
