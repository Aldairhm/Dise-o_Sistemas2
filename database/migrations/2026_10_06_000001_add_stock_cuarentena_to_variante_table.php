<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('variante', 'stock_cuarentena')) {
            Schema::table('variante', function (Blueprint $table) {
                $table->integer('stock_cuarentena')->default(0);
            });
        }
    }

    public function down(): void
    {
        // This column is part of the original variante schema and must be preserved.
    }
};
