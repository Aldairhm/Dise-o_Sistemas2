<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            if (!Schema::hasColumn('producto', 'nombre')) {
                $table->string('nombre', 150)->after('id_categoria');
            }
            if (!Schema::hasColumn('producto', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('producto', 'estado')) {
                $table->smallInteger('estado')->default(1)->after('descripcion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'descripcion', 'estado']);
        });
    }
};
