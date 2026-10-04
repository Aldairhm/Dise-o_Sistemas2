<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            if (!Schema::hasColumn('venta', 'nombre_cliente')) {
                $table->string('nombre_cliente', 255)->nullable();
            }
            if (!Schema::hasColumn('venta', 'departamento')) {
                $table->string('departamento', 100)->nullable();
            }
            if (!Schema::hasColumn('venta', 'municipio')) {
                $table->string('municipio', 100)->nullable();
            }
        });

        Schema::table('salida', function (Blueprint $table) {
            if (!Schema::hasColumn('salida', 'nombre_cliente')) {
                $table->string('nombre_cliente', 255)->nullable();
            }
            if (!Schema::hasColumn('salida', 'departamento')) {
                $table->string('departamento', 100)->nullable();
            }
            if (!Schema::hasColumn('salida', 'municipio')) {
                $table->string('municipio', 100)->nullable();
            }
        });
    }

    public function down(): void
    {
        // These fields are part of the canonical base schema.
    }
};