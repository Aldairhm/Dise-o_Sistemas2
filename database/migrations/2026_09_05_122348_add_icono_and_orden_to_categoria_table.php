<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categoria', function (Blueprint $table) {
            $table->string('icono')->default('fa-tag')->after('descripcion');
            $table->integer('orden')->default(0)->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('categoria', function (Blueprint $table) {
            $table->dropColumn(['icono', 'orden']);
        });
    }
};
