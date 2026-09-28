<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('variante', function (Blueprint $table) {
            if (!Schema::hasColumn('variante', 'comision')) {
                $table->decimal('comision', 10, 2)->default(0)->after('precio_venta');
            }
            if (!Schema::hasColumn('variante', 'costo_promedio')) {
                $table->decimal('costo_promedio', 10, 2)->default(0)->after('reserva');
            }
            if (!Schema::hasColumn('variante', 'porcentaje_ganancia')) {
                $table->decimal('porcentaje_ganancia', 5, 2)->default(0)->after('costo_promedio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('variante', function (Blueprint $table) {
            $columns = array_filter(
                ['comision', 'costo_promedio', 'porcentaje_ganancia'],
                fn (string $column): bool => Schema::hasColumn('variante', $column)
            );

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};