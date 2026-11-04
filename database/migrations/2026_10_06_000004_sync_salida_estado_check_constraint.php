<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const ALLOWED_STATES = [
        'Pendiente',
        'Confirmada',
        'En ruta',
        'En camino',
        'Entregada',
        'Entregado',
        'Cancelada',
        'Cancelado',
        'Devolución',
        'Devolucion',
        'Cambio',
    ];

    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL: buscar con current_database()
            $constraint = DB::selectOne(
                "SELECT constraint_name FROM information_schema.table_constraints
                WHERE constraint_catalog = current_database()
                AND table_name = 'salida'
                AND constraint_name = 'chk_salida_estado'
                AND constraint_type = 'CHECK'"
            );
        } else {
            // MySQL / MariaDB
            $constraint = DB::selectOne(
                "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                AND TABLE_NAME = 'salida'
                AND CONSTRAINT_NAME = 'chk_salida_estado'
                AND CONSTRAINT_TYPE = 'CHECK'"
            );
        }

        if ($constraint) {
            // DROP CONSTRAINT funciona en ambos; DROP CHECK es solo MySQL
            try {
                DB::statement('ALTER TABLE salida DROP CONSTRAINT chk_salida_estado');
            } catch (\Throwable $e) {
                DB::statement('ALTER TABLE salida DROP CHECK chk_salida_estado');
            }
        }

        $states = implode(', ', array_map(
            fn (string $state) => DB::getPdo()->quote($state),
            self::ALLOWED_STATES
        ));

        DB::statement("ALTER TABLE salida ADD CONSTRAINT chk_salida_estado CHECK (estado IN ({$states}))");
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE salida DROP CONSTRAINT chk_salida_estado');
        } catch (\Throwable $e) {
            DB::statement('ALTER TABLE salida DROP CHECK chk_salida_estado');
        }

        $states = array_values(array_diff(self::ALLOWED_STATES, ['Cambio']));
        $quotedStates = implode(', ', array_map(
            fn (string $state) => DB::getPdo()->quote($state),
            $states
        ));

        DB::statement("ALTER TABLE salida ADD CONSTRAINT chk_salida_estado CHECK (estado IN ({$quotedStates}))");
    }
};
