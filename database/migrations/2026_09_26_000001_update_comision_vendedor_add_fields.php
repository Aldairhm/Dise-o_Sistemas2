<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("comision_vendedor", function (Blueprint $table) {
            if (!Schema::hasColumn('comision_vendedor', 'porcentaje')) {
                $table->decimal("porcentaje", 5, 2)->default(0)->after("monto");
            }
            if (!Schema::hasColumn('comision_vendedor', 'notas')) {
                $table->text("notas")->nullable()->after("porcentaje");
            }
            if (!Schema::hasColumn('comision_vendedor', 'liquidado_por')) {
                $table->unsignedBigInteger("liquidado_por")->nullable()->after("notas");
                $table->foreign("liquidado_por")->references("id")->on("usuario")->nullOnDelete();
            }
            if (!Schema::hasColumn('comision_vendedor', 'fecha_liquidacion')) {
                $table->timestamp("fecha_liquidacion")->nullable()->after("liquidado_por");
            }
        });

        $this->dropEstadoCheckIfExists();
        DB::statement("ALTER TABLE comision_vendedor ADD CONSTRAINT chk_comision_vendedor_estado CHECK (estado IN ('Pendiente','Pagada','Cancelada'))");
    }

    public function down(): void
    {
        Schema::table("comision_vendedor", function (Blueprint $table) {
            $table->dropForeign(["liquidado_por"]);
            $table->dropColumn(["porcentaje", "notas", "liquidado_por", "fecha_liquidacion"]);
        });
        $this->dropEstadoCheckIfExists();
        DB::statement("ALTER TABLE comision_vendedor ADD CONSTRAINT chk_comision_vendedor_estado CHECK (estado IN ('Pendiente','Pagada'))");
    }

    private function dropEstadoCheckIfExists(): void
    {
        $constraint = DB::selectOne(
            "SELECT 1
            FROM information_schema.table_constraints
            WHERE constraint_schema = DATABASE()
                AND table_name = ?
                AND constraint_name = ?
                AND constraint_type = 'CHECK'
            LIMIT 1",
            ['comision_vendedor', 'chk_comision_vendedor_estado']
        );

        if ($constraint !== null) {
            DB::statement('ALTER TABLE comision_vendedor DROP CHECK chk_comision_vendedor_estado');
        }
    }
};
