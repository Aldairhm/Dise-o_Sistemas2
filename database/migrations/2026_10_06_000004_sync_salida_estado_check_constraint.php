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
        $constraint = DB::selectOne(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
            AND TABLE_NAME = 'salida'
            AND CONSTRAINT_NAME = 'chk_salida_estado'
            AND CONSTRAINT_TYPE = 'CHECK'"
        );

        if ($constraint) {
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
        DB::statement('ALTER TABLE salida DROP CHECK chk_salida_estado');

        $states = array_values(array_diff(self::ALLOWED_STATES, ['Cambio']));
        $quotedStates = implode(', ', array_map(
            fn (string $state) => DB::getPdo()->quote($state),
            $states
        ));

        DB::statement("ALTER TABLE salida ADD CONSTRAINT chk_salida_estado CHECK (estado IN ({$quotedStates}))");
    }
};
