<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('devoluciones', function (Blueprint $table) {
            if (Schema::hasColumn('devoluciones', 'ubicacion_falla')) {
                $table->dropColumn('ubicacion_falla');
            }
            if (!Schema::hasColumn('devoluciones', 'detalles_json')) {
                $table->json('detalles_json')->nullable()->after('motivo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('devoluciones', function (Blueprint $table) {
            if (Schema::hasColumn('devoluciones', 'detalles_json')) {
                $table->dropColumn('detalles_json');
            }
            if (!Schema::hasColumn('devoluciones', 'ubicacion_falla')) {
                $table->string('ubicacion_falla')->nullable()->after('motivo');
            }
        });
    }
};