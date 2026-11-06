<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DatabaseDataSeeder
 *
 * Seeder maestro para AXStore:
 * - Carga integral de todos los datos reales del sistema desde database/seeders/data_export.json.
 * - Incluye catálogos, productos, variantes, atributos, proveedores, usuarios con nombres reales,
 *   compras, ventas, detalles, salidas, comisiones, devoluciones, movimientos de bodega,
 *   auditorías seguras, centro de dudas/tickets con sus hilos e historiales, y bitácora.
 * - Sincroniza y reinicia de forma automática las secuencias de PostgreSQL.
 */
class DatabaseDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Iniciando carga de base de datos completa con nombres reales...');

        $jsonPath = __DIR__ . '/data_export.json';

        if (!file_exists($jsonPath)) {
            $this->command->error("El archivo {$jsonPath} no existe.");
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        if (!$data || !is_array($data)) {
            $this->command->error("No se pudo interpretar el archivo JSON de datos.");
            return;
        }

        // Orden de limpieza respetando dependencias de clave foránea
        $tablesTruncate = [
            'bitacora',
            'auditoria_sesiones',
            'soporte_ticket_historial',
            'soporte_ticket_mensajes',
            'soporte_tickets',
            'comision_vendedor',
            'devolucion_detalles',
            'devoluciones',
            'movimiento_bodega',
            'salida',
            'detalleventa',
            'venta',
            'compra_detalle',
            'compra',
            'catalogos',
            'variante_imagen',
            'variantevalor',
            'productoatributo',
            'variante',
            'producto',
            'atributo',
            'categoria',
            'proveedors',
            'usuario',
        ];

        Schema::disableForeignKeyConstraints();

        foreach ($tablesTruncate as $tbl) {
            try {
                DB::table($tbl)->truncate();
            } catch (\Throwable $e) {
                // Continuar si la tabla no existe o ya está limpia
            }
        }

        Schema::enableForeignKeyConstraints();

        // Orden estricto de inserción por dependencias relacionales
        $tablesInsertOrder = [
            'usuario',
            'proveedors',
            'categoria',
            'atributo',
            'producto',
            'variante',
            'variante_imagen',
            'productoatributo',
            'variantevalor',
            'catalogos',
            'compra',
            'compra_detalle',
            'venta',
            'detalleventa',
            'salida',
            'movimiento_bodega',
            'comision_vendedor',
            'devoluciones',
            'devolucion_detalles',
            'soporte_tickets',
            'soporte_ticket_mensajes',
            'soporte_ticket_historial',
            'auditoria_sesiones',
            'bitacora',
        ];

        foreach ($tablesInsertOrder as $table) {
            if (!isset($data[$table]) || empty($data[$table])) {
                continue;
            }

            $rows = $data[$table];
            $count = count($rows);
            $this->command->info("  -> Sembrando {$table} ({$count} registros)...");

            // Insertar por lotes de 100 para optimizar rendimiento y memoria
            $chunks = array_chunk($rows, 100);
            foreach ($chunks as $chunk) {
                DB::table($table)->insert($chunk);
            }
        }

        // Actualizar todas las secuencias seriales en PostgreSQL
        $this->resetPostgresSequences();

        $this->command->info('Base de datos sembrada y sincronizada exitosamente con datos y nombres reales.');
    }

    /**
     * Actualiza y sincroniza las secuencias autoincrementales en PostgreSQL
     */
    protected function resetPostgresSequences(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $tables = [
            'usuario',
            'proveedors',
            'categoria',
            'atributo',
            'producto',
            'variante',
            'variante_imagen',
            'productoatributo',
            'variantevalor',
            'catalogos',
            'compra',
            'compra_detalle',
            'venta',
            'detalleventa',
            'salida',
            'movimiento_bodega',
            'comision_vendedor',
            'devoluciones',
            'devolucion_detalles',
            'soporte_tickets',
            'soporte_ticket_mensajes',
            'soporte_ticket_historial',
            'auditoria_sesiones',
            'bitacora',
        ];

        foreach ($tables as $table) {
            try {
                $seq = DB::selectOne("SELECT pg_get_serial_sequence('{$table}', 'id') AS seq");
                if ($seq && $seq->seq) {
                    DB::statement("SELECT setval('{$seq->seq}', COALESCE((SELECT MAX(id) FROM \"{$table}\"), 1))");
                }
            } catch (\Throwable $e) {
                // Silenciar tablas sin columna id serial
            }
        }
    }
}
