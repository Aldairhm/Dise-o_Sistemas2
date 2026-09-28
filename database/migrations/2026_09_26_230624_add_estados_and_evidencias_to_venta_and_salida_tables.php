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
            if (!Schema::hasColumn('venta', 'estado')) {
                $table->string('estado', 30)->default('Pendiente');
            }
            if (!Schema::hasColumn('venta', 'tipo_venta')) {
                $table->string('tipo_venta', 20)->default('Tienda');
            }
            if (!Schema::hasColumn('venta', 'observaciones')) {
                $table->text('observaciones')->nullable();
            }
            if (!Schema::hasColumn('venta', 'comprobante_paquete')) {
                $table->string('comprobante_paquete', 255)->nullable();
            }
            if (!Schema::hasColumn('venta', 'comprobante_devolucion')) {
                $table->string('comprobante_devolucion', 255)->nullable();
            }
            if (!Schema::hasColumn('venta', 'fecha_entrega')) {
                $table->timestamp('fecha_entrega')->nullable();
            }
            if (!Schema::hasColumn('venta', 'fecha_cancelacion')) {
                $table->timestamp('fecha_cancelacion')->nullable();
            }
        });

        Schema::table('salida', function (Blueprint $table) {
            if (!Schema::hasColumn('salida', 'comprobante_paquete')) {
                $table->string('comprobante_paquete', 255)->nullable();
            }
            if (!Schema::hasColumn('salida', 'comprobante_devolucion')) {
                $table->string('comprobante_devolucion', 255)->nullable();
            }
        });

        // Modificar restricción de estado en Postgres si aplica
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE salida DROP CONSTRAINT IF EXISTS chk_salida_estado");
            DB::statement("ALTER TABLE salida ADD CONSTRAINT chk_salida_estado CHECK (estado IN ('Pendiente', 'Confirmada', 'En ruta', 'En camino', 'Entregada', 'Entregado', 'Cancelada', 'Cancelado', 'Devolución', 'Devolucion'))");
        }

        // Actualizar ventas existentes según su dirección
        DB::statement("
            UPDATE venta v
            SET 
                tipo_venta = CASE 
                    WHEN EXISTS (SELECT 1 FROM salida s WHERE s.observaciones LIKE CONCAT('Venta #', v.id, '%') AND s.direccion != 'Venta en mostrador / POS') THEN 'Envio'
                    ELSE 'Tienda'
                END,
                estado = CASE 
                    WHEN EXISTS (SELECT 1 FROM salida s WHERE s.observaciones LIKE CONCAT('Venta #', v.id, '%') AND s.direccion != 'Venta en mostrador / POS') THEN 'Pendiente'
                    ELSE 'Entregada'
                END,
                fecha_entrega = CASE 
                    WHEN EXISTS (SELECT 1 FROM salida s WHERE s.observaciones LIKE CONCAT('Venta #', v.id, '%') AND s.direccion != 'Venta en mostrador / POS') THEN NULL
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
            $columns = [
                'estado',
                'tipo_venta',
                'observaciones',
                'comprobante_paquete',
                'comprobante_devolucion',
                'fecha_entrega',
                'fecha_cancelacion',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('venta', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('salida', function (Blueprint $table) {
            $columns = [
                'comprobante_paquete',
                'comprobante_devolucion',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('salida', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
