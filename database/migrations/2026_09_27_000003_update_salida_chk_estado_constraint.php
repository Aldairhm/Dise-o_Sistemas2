<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            try {
                DB::statement("ALTER TABLE salida DROP CHECK chk_salida_estado");
            } catch (\Throwable $e) {
                // Intentar con DROP CONSTRAINT si DROP CHECK falla
                try {
                    DB::statement("ALTER TABLE salida DROP CONSTRAINT chk_salida_estado");
                } catch (\Throwable $e2) {
                    // Continuar si no existe
                }
            }

            try {
                DB::statement("ALTER TABLE salida ADD CONSTRAINT chk_salida_estado CHECK (estado IN ('Pendiente', 'Confirmada', 'En ruta', 'En camino', 'Entregada', 'Entregado', 'Cancelada', 'Cancelado', 'Devolución', 'Devolucion'))");
            } catch (\Throwable $e) {
                // En caso de versiones que no soporten CHECK
            }
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE salida DROP CONSTRAINT IF EXISTS chk_salida_estado");
            DB::statement("ALTER TABLE salida ADD CONSTRAINT chk_salida_estado CHECK (estado IN ('Pendiente', 'Confirmada', 'En ruta', 'En camino', 'Entregada', 'Entregado', 'Cancelada', 'Cancelado', 'Devolución', 'Devolucion'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
