<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('producto', 'marca')) {
            Schema::table('producto', function (Blueprint $table) {
                $table->string('marca', 100)->nullable()->after('nombre');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('producto', 'marca')) {
            Schema::table('producto', function (Blueprint $table) {
                $table->dropColumn('marca');
            });
        }
    }
};