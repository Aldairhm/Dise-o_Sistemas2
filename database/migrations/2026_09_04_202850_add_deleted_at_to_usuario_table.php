<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('usuario')) {
            Schema::create('usuario', function (Blueprint $table) {
                $table->id();
                $table->string('nombre_real');
                $table->string('username')->unique();
                $table->string('password');
                $table->string('rol')->default('usuario');
                $table->boolean('estado')->default(true);
                $table->string('token')->nullable();
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });
        } elseif (! Schema::hasColumn('usuario', 'deleted_at')) {
            Schema::table('usuario', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('usuario') && Schema::hasColumn('usuario', 'deleted_at')) {
            Schema::table('usuario', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};

