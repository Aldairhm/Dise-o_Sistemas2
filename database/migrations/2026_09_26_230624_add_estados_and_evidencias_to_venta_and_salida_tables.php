<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->string('estado', 30)->default('Pendiente');
            $table->string('tipo_venta', 20)->default('Tienda');
            $table->text('observaciones')->nullable();
            $table->string('comprobante_paquete', 255)->nullable();
            $table->string('comprobante_devolucion', 255)->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->timestamp('fecha_cancelacion')->nullable();
        });

        Schema::table('salida', function (Blueprint $table) {
            $table->string('comprobante_paquete', 255)->nullable();
            $table->string('comprobante_devolucion', 255)->nullable();
        });

        // Modificar restricción de estado en Postgres si existe
        DB::statement("ALTER TABLE salida DROP CONSTRAINT IF EXISTS chk_salida_estado");
        DB::statement("ALTER TABLE salida ADD CONSTRAINT chk_salida_estado CHECK (estado IN ('Pendiente', 'Confirmada', 'En ruta', 'En camino', 'Entregada', 'Entregado', 'Cancelada', 'Cancelado', 'Devolución', 'Devolucion'))");

        // Actualizar ventas existentes según su dirección
        DB::statement("
            UPDATE venta v
            SET 
                tipo_venta = CASE 
                    WHEN EXISTS (SELECT 1 FROM salida s WHERE s.observaciones LIKE 'Venta #' || v.id || '%' AND s.direccion != 'Venta en mostrador / POS') THEN 'Envio'
                    ELSE 'Tienda'
                END,
                estado = CASE 
                    WHEN EXISTS (SELECT 1 FROM salida s WHERE s.observaciones LIKE 'Venta #' || v.id || '%' AND s.direccion != 'Venta en mostrador / POS') THEN 'Pendiente'
                    ELSE 'Entregada'
                END,
                fecha_entrega = CASE 
                    WHEN EXISTS (SELECT 1 FROM salida s WHERE s.observaciones LIKE 'Venta #' || v.id || '%' AND s.direccion != 'Venta en mostrador / POS') THEN NULL
                    ELSE v.fecha
                END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->dropColumn([
                'estado',
                'tipo_venta',
                'observaciones',
                'comprobante_paquete',
                'comprobante_devolucion',
                'fecha_entrega',
                'fecha_cancelacion',
            ]);
        });

        Schema::table('salida', function (Blueprint $table) {
            $table->dropColumn([
                'comprobante_paquete',
                'comprobante_devolucion',
            ]);
        });
    }
};
