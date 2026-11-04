<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

/**
 * DatabaseDataSeeder
 *
 * Seeder con datos reales respetando fielmente toda la lógica de negocio de AXStore:
 * - Proveedores y Usuarios completos (≥ 15 cada uno).
 * - Compras con detalles y movimientos de bodega de entrada.
 * - Ventas con su desglose estricto:
 *     * Venta (Tienda o Envio, cálculo de totales, descuentos, envío, estados)
 *     * DetalleVenta (líneas de venta por variante)
 *     * Salida (1 registro de salida por cada línea de detalleventa, con 'Venta #ID' en observaciones)
 *     * Comisiones (solo para ventas 'Entregada', vinculadas a la Salida y al Vendedor)
 * - Devoluciones de Venta y Compra con detalles y movimientos de cuarentena/bodega.
 * - Bitácora de auditoría detallada.
 * - Actualización de secuencias de PostgreSQL.
 */
class DatabaseDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Iniciando DatabaseDataSeeder con logica estricta de negocio...');

        // Desactivar constraints temporalmente para limpieza segura de tablas transaccionales
        Schema::disableForeignKeyConstraints();

        // Limpiar solo tablas transaccionales (sin tocar producto, variante, categoria, atributo)
        DB::table('comision_vendedor')->truncate();
        DB::table('devolucion_detalles')->truncate();
        DB::table('devoluciones')->truncate();
        DB::table('movimiento_bodega')->truncate();
        DB::table('salida')->truncate();
        DB::table('detalleventa')->truncate();
        DB::table('venta')->truncate();
        DB::table('compra_detalle')->truncate();
        DB::table('compra')->truncate();
        DB::table('bitacora')->truncate();

        Schema::enableForeignKeyConstraints();

        // 1. Sembrar Proveedores (≥ 15)
        $this->seedProveedores();

        // 2. Sembrar Usuarios (≥ 15)
        $this->seedUsuarios();

        // 3. Sembrar Compras y Movimientos de Entrada (≥ 15)
        $this->seedCompras();

        // 4. Sembrar Ventas con toda su lógica detallada (≥ 18)
        $this->seedVentas();

        // 5. Sembrar Devoluciones (≥ 15)
        $this->seedDevoluciones();

        // 6. Sembrar Bitácora (≥ 20)
        $this->seedBitacora();

        // 7. Sincronizar secuencias de PostgreSQL
        $this->resetPostgresSequences();

        $this->command->info('DatabaseDataSeeder finalizado exitosamente con datos reales.');
    }

    /**
     * 1. PROVEEDORES
     */
    protected function seedProveedores(): void
    {
        $this->command->info('  -> Sembrando Proveedores...');

        $proveedores = [
            ['id' => 1,  'nombre' => 'La Curacao El Salvador',       'correo' => 'corporativo@curacao.com.sv',  'telefono' => '2278-4729', 'direccion' => 'Alameda Roosevelt #234, San Salvador'],
            ['id' => 2,  'nombre' => 'ImpreCar AutoPartes S.A.',     'correo' => 'ventas@imprecar.com.sv',      'telefono' => '2245-8899', 'direccion' => 'Blvd. Venezuela #1204, San Salvador'],
            ['id' => 3,  'nombre' => 'TechParts Mayoristas',         'correo' => 'contacto@techpartssv.com',    'telefono' => '2267-1100', 'direccion' => 'Blvd. Los Héroes, Edif. Roble, San Salvador'],
            ['id' => 4,  'nombre' => 'AutoRepuestos Nacional',       'correo' => 'info@autorepnacional.sv',     'telefono' => '2440-8800', 'direccion' => '5a Calle Poniente y 14 Av. Sur, Santa Ana'],
            ['id' => 5,  'nombre' => 'Distribuidora Oriental LED',   'correo' => 'ventas@orientalled.com',      'telefono' => '2661-3344', 'direccion' => 'Av. Roosevelt Sur #401, San Miguel'],
            ['id' => 6,  'nombre' => 'Hogar & Confort Distribuciones','correo' => 'pedidos@hogarconfort.sv',    'telefono' => '2290-3300', 'direccion' => 'Autopista Sur km 12, Soyapango'],
            ['id' => 7,  'nombre' => 'Importaciones Wang SV',        'correo' => 'wang.import@outlook.com',     'telefono' => '2550-7700', 'direccion' => 'Zona Franca San Bartolo, Ilopango'],
            ['id' => 8,  'nombre' => 'Electro Visión Tecnológica',   'correo' => 'evision@global.sv',           'telefono' => '2345-1200', 'direccion' => 'C.C. Plaza Mundo, L-45, Soyapango'],
            ['id' => 9,  'nombre' => 'Juguetes y Hobbies del Pacífico','correo' => 'info@juguetespacifico.com','telefono' => '2251-4400', 'direccion' => 'Metrocentro San Salvador, 8a Etapa'],
            ['id' => 10, 'nombre' => 'Repuestos Iluminación Latina', 'correo' => 'ventas@iluminacionlatina.sv', 'telefono' => '2401-5500', 'direccion' => 'Carretera Panamericana Km 10, Santa Tecla'],
            ['id' => 11, 'nombre' => 'Digital Zone Import El Salvador','correo' => 'digitalzone@sv.com',        'telefono' => '2512-8800', 'direccion' => 'Paseo General Escalón #3840, San Salvador'],
            ['id' => 12, 'nombre' => 'Moto Repuestos El Sol',        'correo' => 'contacto@repuestoselsol.sv',  'telefono' => '2600-4400', 'direccion' => 'Col. Zacamil, Calle Matasano, Mejicanos'],
            ['id' => 13, 'nombre' => 'Bodega Central de Iluminación','correo' => 'bodegacentral@sv.net',        'telefono' => '2233-6600', 'direccion' => 'Carretera Troncal del Norte Km 7, Apopa'],
            ['id' => 14, 'nombre' => 'Accesorios Veloz Automotriz',  'correo' => 'accesoriosveloz@gmail.com',   'telefono' => '2399-7700', 'direccion' => 'Colonia San Benito, Calle La Mascota #112'],
            ['id' => 15, 'nombre' => 'Cámaras y Seguridad Smart SV', 'correo' => 'ventas@seguridadsmart.sv',   'telefono' => '2288-5522', 'direccion' => 'Av. Las Palmas #230, San Benito, San Salvador'],
            ['id' => 16, 'nombre' => 'Comercializadora Cuscatlán',   'correo' => 'ventas@comercialcuscatlan.sv','telefono' => '2311-9988', 'direccion' => 'Final 49 Av. Sur, Edif. Cuscatlán, San Salvador'],
        ];

        foreach ($proveedores as $p) {
            DB::table('proveedors')->updateOrInsert(
                ['id' => $p['id']],
                array_merge($p, [
                    'created_at' => '2026-09-01 10:00:00',
                    'updated_at' => '2026-09-01 10:00:00',
                ])
            );
        }
    }

    /**
     * 2. USUARIOS
     */
    protected function seedUsuarios(): void
    {
        $this->command->info('  -> Sembrando Usuarios...');

        $passwordHash = Hash::make('password123');

        $usuarios = [
            ['id' => 1,  'nombre_real' => 'Josue Martinez',      'username' => 'steven19denoviembre@gmail.com', 'rol' => 'admin',     'porcentaje_comision' => null, 'telefono' => '7890-1234'],
            ['id' => 2,  'nombre_real' => 'Ana Plasma',          'username' => 'anaplasma@gmail.com',           'rol' => 'vendedor',  'porcentaje_comision' => 5.00, 'telefono' => '7906-5467'],
            ['id' => 3,  'nombre_real' => 'Carlos Mendoza',      'username' => 'carlos.mendoza@axstore.sv',     'rol' => 'vendedor',  'porcentaje_comision' => 5.00, 'telefono' => '7700-1100'],
            ['id' => 4,  'nombre_real' => 'María Elena López',   'username' => 'maria.lopez@axstore.sv',        'rol' => 'vendedor',  'porcentaje_comision' => 5.00, 'telefono' => '7811-2200'],
            ['id' => 5,  'nombre_real' => 'Roberto Flores',      'username' => 'roberto.flores@axstore.sv',     'rol' => 'vendedor',  'porcentaje_comision' => 4.50, 'telefono' => '7922-3300'],
            ['id' => 6,  'nombre_real' => 'Karla Rivas',         'username' => 'karla.rivas@axstore.sv',        'rol' => 'vendedor',  'porcentaje_comision' => 5.00, 'telefono' => '7033-4400'],
            ['id' => 7,  'nombre_real' => 'Diego Hernández',     'username' => 'diego.hernandez@axstore.sv',    'rol' => 'vendedor',  'porcentaje_comision' => 4.00, 'telefono' => '7144-5500'],
            ['id' => 8,  'nombre_real' => 'Lucía Castillo',      'username' => 'lucia.castillo@axstore.sv',     'rol' => 'vendedor',  'porcentaje_comision' => 5.50, 'telefono' => '7255-6600'],
            ['id' => 9,  'nombre_real' => 'Fernando Pérez',      'username' => 'fernando.perez@axstore.sv',     'rol' => 'bodeguero', 'porcentaje_comision' => null, 'telefono' => '7366-7700'],
            ['id' => 10, 'nombre_real' => 'Sandra Torres',       'username' => 'sandra.torres@axstore.sv',      'rol' => 'vendedor',  'porcentaje_comision' => 5.00, 'telefono' => '7477-8800'],
            ['id' => 11, 'nombre_real' => 'Miguel Ángel Cruz',   'username' => 'miguel.cruz@axstore.sv',        'rol' => 'vendedor',  'porcentaje_comision' => 4.75, 'telefono' => '7588-9900'],
            ['id' => 12, 'nombre_real' => 'Patricia Salgado',    'username' => 'patricia.salgado@axstore.sv',   'rol' => 'bodeguero', 'porcentaje_comision' => null, 'telefono' => '7699-0011'],
            ['id' => 13, 'nombre_real' => 'Andrés Vásquez',      'username' => 'andres.vasquez@axstore.sv',     'rol' => 'vendedor',  'porcentaje_comision' => 5.00, 'telefono' => '7700-1122'],
            ['id' => 14, 'nombre_real' => 'Claudia Martínez',    'username' => 'claudia.martinez@axstore.sv',   'rol' => 'vendedor',  'porcentaje_comision' => 5.25, 'telefono' => '7811-2233'],
            ['id' => 15, 'nombre_real' => 'Oscar Reyes',         'username' => 'oscar.reyes@axstore.sv',        'rol' => 'vendedor',  'porcentaje_comision' => 4.50, 'telefono' => '7922-3344'],
            ['id' => 16, 'nombre_real' => 'Gabriela Solórzano',  'username' => 'gabriela.s@axstore.sv',         'rol' => 'vendedor',  'porcentaje_comision' => 5.00, 'telefono' => '7123-4567'],
        ];

        foreach ($usuarios as $u) {
            DB::table('usuario')->updateOrInsert(
                ['id' => $u['id']],
                array_merge($u, [
                    'password'   => $passwordHash,
                    'estado'     => 1,
                    'created_at' => '2026-09-01 08:00:00',
                    'updated_at' => '2026-09-01 08:00:00',
                ])
            );
        }
    }

    /**
     * 3. COMPRAS Y MOVIMIENTOS DE ENTRADA
     */
    protected function seedCompras(): void
    {
        $this->command->info('  -> Sembrando Compras y Movimientos de Entrada...');

        // Matriz de compras reales: [proveedor_id, usuario_id, fecha, referencia, [ [variante_id, cantidad, costo_unitario], ... ]]
        $compras = [
            [1,  1, '2026-09-10', 'FAC-2026-001', [[1, 30, 8.00], [2, 25, 8.40]]],
            [2,  9, '2026-09-12', 'FAC-2026-002', [[14, 20, 30.00], [13, 25, 1.80]]],
            [3,  1, '2026-09-15', 'FAC-2026-003', [[10, 15, 10.00]]],
            [4,  9, '2026-09-18', 'FAC-2026-004', [[1, 20, 8.00], [9, 10, 13.33]]],
            [5,  1, '2026-09-20', 'FAC-2026-005', [[6, 40, 1.00], [7, 40, 1.00]]],
            [6,  9, '2026-09-22', 'FAC-2026-006', [[11, 15, 7.00], [12, 20, 1.50]]],
            [7,  1, '2026-09-25', 'FAC-2026-007', [[2, 20, 8.40], [14, 15, 30.00]]],
            [8,  9, '2026-09-28', 'FAC-2026-008', [[10, 10, 10.00], [1, 15, 8.00]]],
            [9,  1, '2026-10-02', 'FAC-2026-009', [[11, 20, 7.00], [12, 30, 1.50]]],
            [10, 9, '2026-10-05', 'FAC-2026-010', [[13, 30, 1.80], [14, 10, 30.00]]],
            [11, 1, '2026-10-08', 'FAC-2026-011', [[9, 15, 13.33], [2, 15, 8.40]]],
            [12, 9, '2026-10-12', 'FAC-2026-012', [[6, 30, 1.00], [7, 30, 1.00]]],
            [13, 1, '2026-10-15', 'FAC-2026-013', [[1, 25, 8.00], [2, 20, 8.40]]],
            [14, 9, '2026-10-18', 'FAC-2026-014', [[10, 12, 10.00]]],
            [15, 1, '2026-10-22', 'FAC-2026-015', [[14, 12, 30.00], [11, 10, 7.00]]],
            [16, 9, '2026-10-26', 'FAC-2026-016', [[1, 20, 8.00], [13, 20, 1.80]]],
        ];

        foreach ($compras as $index => $c) {
            [$provId, $usrId, $fecha, $ref, $lineas] = $c;
            $compraId = $index + 1;

            $subtotal = 0;
            foreach ($lineas as $l) {
                $subtotal += ($l[1] * $l[2]);
            }

            DB::table('compra')->insert([
                'id'            => $compraId,
                'id_proveedor'  => $provId,
                'id_usuario'    => $usrId,
                'fecha_compra'  => $fecha,
                'referencia'    => $ref,
                'observaciones' => "Recepción de mercadería según {$ref} en bodega central.",
                'subtotal'      => $subtotal,
                'total'         => $subtotal,
                'estado'        => 'recibida',
                'created_at'    => Carbon::parse($fecha)->setTime(9, 0, 0),
                'updated_at'    => Carbon::parse($fecha)->setTime(9, 30, 0),
            ]);

            foreach ($lineas as $l) {
                [$varId, $cant, $costo] = $l;
                DB::table('compra_detalle')->insert([
                    'id_compra'       => $compraId,
                    'id_variante'     => $varId,
                    'cantidad'        => $cant,
                    'precio_unitario' => $costo,
                    'subtotal'        => $cant * $costo,
                    'created_at'      => Carbon::parse($fecha)->setTime(9, 30, 0),
                    'updated_at'      => Carbon::parse($fecha)->setTime(9, 30, 0),
                ]);

                // Movimiento de bodega: entrada por compra
                DB::table('movimiento_bodega')->insert([
                    'id_variante'               => $varId,
                    'id_compra'                 => $compraId,
                    'devolucion_id'             => null,
                    'id_usuario'                => $usrId,
                    'tipo'                      => 'entrada',
                    'cantidad'                  => $cant,
                    'reserva_anterior'          => 0,
                    'reserva_nueva'             => 0,
                    'stock_anterior'            => 10,
                    'stock_nuevo'               => 10 + $cant,
                    'stock_cuarentena_anterior' => 0,
                    'stock_cuarentena_nuevo'    => 0,
                    'observacion'               => "Entrada por compra #{$compraId} ({$ref})",
                    'created_at'                => Carbon::parse($fecha)->setTime(9, 35, 0),
                    'updated_at'                => Carbon::parse($fecha)->setTime(9, 35, 0),
                ]);
            }
        }
    }

    /**
     * 4. VENTAS CON LÓGICA ESTRICTA (Venta -> DetalleVenta -> Salida -> Comisiones)
     */
    protected function seedVentas(): void
    {
        $this->command->info('  -> Sembrando Ventas, Detalles, Salidas y Comisiones segun logica de negocio...');

        // Datos maestros de variantes y sus comisiones fijas/porcentaje
        $variantesInfo = [
            1  => ['precio' => 19.00, 'costo' => 8.00,  'comision' => 3.00, 'nombre' => 'Luz Blanca',    'producto' => 'ALOGENAS ESPLORADORA'],
            2  => ['precio' => 19.80, 'costo' => 8.40,  'comision' => 3.00, 'nombre' => 'Luz Amarrilla', 'producto' => 'ALOGENAS ESPLORADORA'],
            6  => ['precio' => 2.00,  'costo' => 1.00,  'comision' => 2.00, 'nombre' => 'Morado',        'producto' => 'LUCES WATERMELON'],
            7  => ['precio' => 2.00,  'costo' => 1.00,  'comision' => 2.00, 'nombre' => 'Azul',          'producto' => 'LUCES WATERMELON'],
            9  => ['precio' => 29.67, 'costo' => 13.33, 'comision' => 3.00, 'nombre' => 'Combinada',    'producto' => 'ALOGENAS ESPLORADORA'],
            10 => ['precio' => 24.00, 'costo' => 10.00, 'comision' => 4.00, 'nombre' => 'Dos focos',    'producto' => 'CAMARA FOCO Giro 360°'],
            11 => ['precio' => 17.00, 'costo' => 7.00,  'comision' => 3.00, 'nombre' => 'Blanca',       'producto' => 'MOTO COLECCIÓN DEPORTIVA S1000'],
            12 => ['precio' => 3.00,  'costo' => 1.50,  'comision' => 3.00, 'nombre' => 'Negro',        'producto' => 'MOTO COLECCIÓN DEPORTIVA S1000'],
            13 => ['precio' => 3.00,  'costo' => 1.80,  'comision' => 3.00, 'nombre' => 'Roja',         'producto' => 'LUCES TRASERAS CON EFECTO 3D'],
            14 => ['precio' => 63.00, 'costo' => 30.00, 'comision' => 3.00, 'nombre' => 'Amarilla',     'producto' => 'LUCES TRASERAS CON EFECTO 3D'],
        ];

        // Definición de 20 ventas con variedad de modalidades y estados reales
        $ventasDef = [
            // 0: Tienda - Entregada de contado
            [
                'vendedor_id' => 2,
                'fecha'       => '2026-10-01 10:15:00',
                'tipo_venta'  => 'Tienda',
                'estado'      => 'Entregada',
                'metodo_pago' => 'Efectivo',
                'cliente'     => 'Mauricio Alexander Rivas',
                'depto'       => 'San Salvador',
                'muni'        => 'San Salvador',
                'telefono'    => '7845-1290',
                'envio'       => 0.00,
                'lineas'      => [ [1, 2, 0.00] ], // 2x Luz Blanca
                'comision_pagada' => true,
            ],
            // 1: Tienda - Entregada con transferencia
            [
                'vendedor_id' => 3,
                'fecha'       => '2026-10-02 11:30:00',
                'tipo_venta'  => 'Tienda',
                'estado'      => 'Entregada',
                'metodo_pago' => 'Transferencia Bancaria',
                'cliente'     => 'Beatriz Eugenia Sandoval',
                'depto'       => 'La Libertad',
                'muni'        => 'Santa Tecla',
                'telefono'    => '7190-3344',
                'envio'       => 0.00,
                'lineas'      => [ [14, 1, 0.00] ], // 1x Amarilla 3D
                'comision_pagada' => true,
            ],
            // 2: Envio - Entregada exitosa (comisión pagada)
            [
                'vendedor_id' => 2,
                'fecha'       => '2026-10-04 09:20:00',
                'tipo_venta'  => 'Envio',
                'estado'      => 'Entregada',
                'metodo_pago' => 'Efectivo',
                'cliente'     => 'Guillermo José Portillo',
                'depto'       => 'Santa Ana',
                'muni'        => 'Santa Ana',
                'telefono'    => '7244-5511',
                'envio'       => 4.00,
                'lineas'      => [ [10, 1, 0.00], [1, 1, 0.00] ], // Cámara + Luz Blanca
                'comision_pagada' => true,
            ],
            // 3: Envio - Entregada reciente (comisión aún pendiente de liquidar)
            [
                'vendedor_id' => 4,
                'fecha'       => '2026-10-06 14:10:00',
                'tipo_venta'  => 'Envio',
                'estado'      => 'Entregada',
                'metodo_pago' => 'Transferencia Bancaria',
                'cliente'     => 'Sonia Maribel Calderón',
                'depto'       => 'San Salvador',
                'muni'        => 'Mejicanos',
                'telefono'    => '7788-9922',
                'envio'       => 3.50,
                'lineas'      => [ [2, 2, 0.00] ], // 2x Luz Amarilla
                'comision_pagada' => false,
            ],
            // 4: Envio - En ruta (despachada a paquetería, NO genera comisión todavía)
            [
                'vendedor_id' => 5,
                'fecha'       => '2026-10-08 16:45:00',
                'tipo_venta'  => 'Envio',
                'estado'      => 'En ruta',
                'metodo_pago' => 'Efectivo',
                'cliente'     => 'Héctor Rolando Meléndez',
                'depto'       => 'San Miguel',
                'muni'        => 'San Miguel',
                'telefono'    => '7512-6677',
                'envio'       => 5.00,
                'lineas'      => [ [14, 1, 0.00] ],
                'comision_pagada' => false,
            ],
            // 5: Envio - Confirmada (esperando paquete, NO genera comisión)
            [
                'vendedor_id' => 6,
                'fecha'       => '2026-10-10 10:00:00',
                'tipo_venta'  => 'Envio',
                'estado'      => 'Confirmada',
                'metodo_pago' => 'Transferencia Bancaria',
                'cliente'     => 'Karina Vanessa Ortiz',
                'depto'       => 'Sonsonate',
                'muni'        => 'Sonsonate',
                'telefono'    => '7333-8899',
                'envio'       => 4.50,
                'lineas'      => [ [11, 2, 0.00] ],
                'comision_pagada' => false,
            ],
            // 6: Envio - Pendiente (recién registrada en web/vendedor, NO genera comisión)
            [
                'vendedor_id' => 7,
                'fecha'       => '2026-10-12 11:15:00',
                'tipo_venta'  => 'Envio',
                'estado'      => 'Pendiente',
                'metodo_pago' => 'Efectivo',
                'cliente'     => 'Josué Ernesto Benítez',
                'depto'       => 'La Paz',
                'muni'        => 'Zacatecoluca',
                'telefono'    => '7900-1122',
                'envio'       => 4.00,
                'lineas'      => [ [9, 1, 0.00] ],
                'comision_pagada' => false,
            ],
            // 7: Envio - Cancelada por el cliente (comisión NO existe o anulada)
            [
                'vendedor_id' => 8,
                'fecha'       => '2026-10-14 13:00:00',
                'tipo_venta'  => 'Envio',
                'estado'      => 'Cancelada',
                'metodo_pago' => 'Efectivo',
                'cliente'     => 'Nelson Vladimir Guardado',
                'depto'       => 'Cuscatlán',
                'muni'        => 'Cojutepeque',
                'telefono'    => '7455-6611',
                'envio'       => 3.50,
                'lineas'      => [ [1, 1, 0.00] ],
                'motivo_cancelacion' => 'Cliente canceló el pedido por viaje imprevisto antes del despacho.',
                'comision_pagada' => false,
            ],
            // 8: Tienda - Entregada con múltiples variantes
            [
                'vendedor_id' => 2,
                'fecha'       => '2026-10-15 15:30:00',
                'tipo_venta'  => 'Tienda',
                'estado'      => 'Entregada',
                'metodo_pago' => 'Efectivo',
                'cliente'     => 'Patricia Raquel Domínguez',
                'depto'       => 'San Salvador',
                'muni'        => 'Soyapango',
                'telefono'    => '7688-4455',
                'envio'       => 0.00,
                'lineas'      => [ [1, 1, 0.00], [2, 1, 0.00], [6, 2, 0.00] ],
                'comision_pagada' => true,
            ],
            // 9: Envio - Devolución procesada
            [
                'vendedor_id' => 10,
                'fecha'       => '2026-10-17 09:00:00',
                'tipo_venta'  => 'Envio',
                'estado'      => 'Devolución',
                'metodo_pago' => 'Transferencia Bancaria',
                'cliente'     => 'Ernesto Alonso Pineda',
                'depto'       => 'Ahuachapán',
                'muni'        => 'Ahuachapán',
                'telefono'    => '7122-3344',
                'envio'       => 4.50,
                'lineas'      => [ [10, 1, 0.00] ],
                'motivo_cancelacion' => 'Foco incompatible con la rosca del portalámpara del cliente.',
                'comision_pagada' => false,
            ],
            // 10: Envio - Cambio de producto
            [
                'vendedor_id' => 11,
                'fecha'       => '2026-10-19 14:20:00',
                'tipo_venta'  => 'Envio',
                'estado'      => 'Cambio',
                'metodo_pago' => 'Efectivo',
                'cliente'     => 'Fátima Lorena Menjívar',
                'depto'       => 'Chalatenango',
                'muni'        => 'Chalatenango',
                'telefono'    => '7899-0011',
                'envio'       => 4.00,
                'lineas'      => [ [1, 1, 0.00] ],
                'motivo_cancelacion' => 'Solicitud de cambio a luz cálida/amarilla.',
                'comision_pagada' => true,
            ],
            // 11: Tienda - Entregada (comisión pagada)
            [
                'vendedor_id' => 3,
                'fecha'       => '2026-10-21 11:00:00',
                'tipo_venta'  => 'Tienda',
                'estado'      => 'Entregada',
                'metodo_pago' => 'Efectivo',
                'cliente'     => 'David Antonio Quintanilla',
                'depto'       => 'La Libertad',
                'muni'        => 'Colón',
                'telefono'    => '7566-7788',
                'envio'       => 0.00,
                'lineas'      => [ [14, 1, 0.00] ],
                'comision_pagada' => true,
            ],
            // 12: Envio - Entregada (comisión pendiente de liquidar)
            [
                'vendedor_id' => 4,
                'fecha'       => '2026-10-23 10:45:00',
                'tipo_venta'  => 'Envio',
                'estado'      => 'Entregada',
                'metodo_pago' => 'Transferencia Bancaria',
                'cliente'     => 'Diana Marcela Galdámez',
                'depto'       => 'Usulután',
                'muni'        => 'Usulután',
                'telefono'    => '7311-2299',
                'envio'       => 5.00,
                'lineas'      => [ [2, 1, 0.00], [11, 1, 0.00] ],
                'comision_pagada' => false,
            ],
            // 13: Envio - Confirmada
            [
                'vendedor_id' => 13,
                'fecha'       => '2026-10-25 16:10:00',
                'tipo_venta'  => 'Envio',
                'estado'      => 'Confirmada',
                'metodo_pago' => 'Efectivo',
                'cliente'     => 'Rodrigo Isaac Palacios',
                'depto'       => 'San Salvador',
                'muni'        => 'Ilopango',
                'telefono'    => '7400-5566',
                'envio'       => 3.50,
                'lineas'      => [ [1, 2, 0.00] ],
                'comision_pagada' => false,
            ],
            // 14: Envio - En ruta
            [
                'vendedor_id' => 14,
                'fecha'       => '2026-10-27 08:30:00',
                'tipo_venta'  => 'Envio',
                'estado'      => 'En ruta',
                'metodo_pago' => 'Transferencia Bancaria',
                'cliente'     => 'Marta Alicia Coto',
                'depto'       => 'Morazán',
                'muni'        => 'San Francisco Gotera',
                'telefono'    => '7288-9900',
                'envio'       => 5.00,
                'lineas'      => [ [10, 1, 0.00] ],
                'comision_pagada' => false,
            ],
            // 15: Envio - Entregada
            [
                'vendedor_id' => 2,
                'fecha'       => '2026-10-28 12:00:00',
                'tipo_venta'  => 'Envio',
                'estado'      => 'Entregada',
                'metodo_pago' => 'Efectivo',
                'cliente'     => 'Víctor Manuel Barillas',
                'depto'       => 'La Unión',
                'muni'        => 'La Unión',
                'telefono'    => '7677-8899',
                'envio'       => 5.50,
                'lineas'      => [ [14, 1, 0.00] ],
                'comision_pagada' => true,
            ],
            // 16: Tienda - Entregada
            [
                'vendedor_id' => 15,
                'fecha'       => '2026-10-29 15:00:00',
                'tipo_venta'  => 'Tienda',
                'estado'      => 'Entregada',
                'metodo_pago' => 'Efectivo',
                'cliente'     => 'Oscar Armando Villeda',
                'depto'       => 'San Salvador',
                'muni'        => 'Apopa',
                'telefono'    => '7944-5566',
                'envio'       => 0.00,
                'lineas'      => [ [1, 1, 0.00], [9, 1, 0.00] ],
                'comision_pagada' => true,
            ],
            // 17: Envio - Pendiente
            [
                'vendedor_id' => 16,
                'fecha'       => '2026-10-30 09:40:00',
                'tipo_venta'  => 'Envio',
                'estado'      => 'Pendiente',
                'metodo_pago' => 'Efectivo',
                'cliente'     => 'Silvia Jeannette Alas',
                'depto'       => 'San Vicente',
                'muni'        => 'San Vicente',
                'telefono'    => '7155-6677',
                'envio'       => 4.00,
                'lineas'      => [ [2, 1, 0.00] ],
                'comision_pagada' => false,
            ],
            // 18: Tienda - Entregada
            [
                'vendedor_id' => 2,
                'fecha'       => '2026-10-31 11:20:00',
                'tipo_venta'  => 'Tienda',
                'estado'      => 'Entregada',
                'metodo_pago' => 'Efectivo',
                'cliente'     => 'César Augusto Morales',
                'depto'       => 'San Salvador',
                'muni'        => 'San Salvador',
                'telefono'    => '7822-3344',
                'envio'       => 0.00,
                'lineas'      => [ [1, 2, 0.00] ],
                'comision_pagada' => false,
            ],
        ];

        $salidaGlobalId = 1;

        foreach ($ventasDef as $idx => $v) {
            $ventaId = $idx + 1;
            $fechaCarbon = Carbon::parse($v['fecha']);

            // Calcular subtotales
            $subtotalVenta = 0;
            foreach ($v['lineas'] as $linea) {
                [$varId, $cant, $costoExtra] = $linea;
                $pUnit = $variantesInfo[$varId]['precio'];
                $subtotalVenta += ($cant * $pUnit) + $costoExtra;
            }
            $totalVenta = $subtotalVenta + $v['envio'];

            $fechaEntrega = null;
            if ($v['estado'] === 'Entregada') {
                $fechaEntrega = ($v['tipo_venta'] === 'Tienda')
                    ? $fechaCarbon
                    : $fechaCarbon->copy()->addDays(2)->setTime(16, 0, 0);
            }

            $fechaCancelacion = null;
            if ($v['estado'] === 'Cancelada') {
                $fechaCancelacion = $fechaCarbon->copy()->addDay()->setTime(11, 0, 0);
            }

            $observacionesVenta = "Cliente: {$v['cliente']} | {$v['depto']}, {$v['muni']}";
            if (!empty($v['motivo_cancelacion'])) {
                $observacionesVenta .= " | Motivo: {$v['motivo_cancelacion']}";
            }

            $direccionFinal = ($v['tipo_venta'] === 'Tienda')
                ? 'Venta en mostrador / POS'
                : "{$v['depto']}, {$v['muni']} - Calle principal #{$ventaId} (Ref: Frente a parque central)";

            // A. Registrar en tabla `venta`
            DB::table('venta')->insert([
                'id'                     => $ventaId,
                'id_usuario'             => $v['vendedor_id'],
                'fecha'                  => $fechaCarbon,
                'total'                  => $totalVenta,
                'metodo_pago'            => $v['metodo_pago'],
                'comprobante_pago'       => ($v['metodo_pago'] === 'Transferencia Bancaria') ? "comprobantes_pago/VNT-" . str_pad($ventaId, 5, '0', STR_PAD_LEFT) . "_{$fechaCarbon->format('Y-m-d')}_comprobante_pago.jpg" : null,
                'nombre_cliente'         => $v['cliente'],
                'departamento'           => $v['depto'],
                'municipio'              => $v['muni'],
                'telefono'               => $v['telefono'],
                'precio_envio'           => $v['envio'],
                'estado'                 => $v['estado'],
                'tipo_venta'             => $v['tipo_venta'],
                'observaciones'          => $observacionesVenta,
                'comprobante_paquete'    => in_array($v['estado'], ['En ruta', 'Entregada']) ? "comprobantes_paquete/VNT-" . str_pad($ventaId, 5, '0', STR_PAD_LEFT) . "_{$fechaCarbon->format('Y-m-d')}_paquete.jpg" : null,
                'comprobante_devolucion' => in_array($v['estado'], ['Devolución', 'Cambio']) ? "comprobantes_devolucion/VNT-" . str_pad($ventaId, 5, '0', STR_PAD_LEFT) . "_{$fechaCarbon->format('Y-m-d')}_devolucion.jpg" : null,
                'fecha_entrega'          => $fechaEntrega,
                'fecha_cancelacion'      => $fechaCancelacion,
            ]);

            // B. Registrar `detalleventa` y `salida` para cada producto del carrito
            foreach ($v['lineas'] as $linea) {
                [$varId, $cant, $costoExtra] = $linea;
                $var = $variantesInfo[$varId];
                $precioUnitario = $var['precio'];
                $costoUnitario = $var['costo'];
                $subtotalLinea = ($cant * $precioUnitario) + $costoExtra;
                $costoTotal = $costoUnitario * $cant;
                $comisionLinea = ($var['comision'] * $cant) + $costoExtra;

                // 1. Detalle de venta
                DB::table('detalleventa')->insert([
                    'id_venta'        => $ventaId,
                    'id_variante'     => $varId,
                    'cantidad'        => $cant,
                    'precio_unitario' => $precioUnitario,
                    'subtotal'        => $subtotalLinea,
                ]);

                // 2. Registro de Salida de inventario correspondiente
                $salidaObs = "Venta #{$ventaId}";
                if ($v['estado'] === 'Cancelada' && !empty($v['motivo_cancelacion'])) {
                    $salidaObs = "Venta #{$ventaId} - Cancelada: {$v['motivo_cancelacion']}";
                }

                $salidaId = $salidaGlobalId++;

                DB::table('salida')->insert([
                    'id'                   => $salidaId,
                    'id_variante'          => $varId,
                    'id_usuario'           => $v['vendedor_id'],
                    'cantidad'             => $cant,
                    'fecha_salida'         => $fechaCarbon->toDateString(),
                    'hora_salida'          => $fechaCarbon->toTimeString(),
                    'fecha_entrega'        => $fechaEntrega ? $fechaEntrega->toDateString() : null,
                    'nombre_cliente'       => $v['cliente'],
                    'departamento'         => $v['depto'],
                    'municipio'            => $v['muni'],
                    'direccion'            => $direccionFinal,
                    'telefono'             => $v['telefono'],
                    'precio_envio'         => $v['envio'],
                    'costo_extra'          => $costoExtra,
                    'precio_unitario'      => $precioUnitario,
                    'subtotal'             => $cant * $precioUnitario,
                    'descuento'            => 0.00,
                    'total'                => $subtotalLinea,
                    'costo_total_aplicado' => $costoTotal,
                    'comision_aplicada'    => $comisionLinea,
                    'observaciones'        => $salidaObs,
                    'estado'               => $v['estado'],
                    'fecha_cancelacion'    => $fechaCancelacion,
                    'created_at'           => $fechaCarbon,
                    'comprobante_paquete'  => in_array($v['estado'], ['En ruta', 'Entregada']) ? "comprobantes_paquete/VNT-" . str_pad($ventaId, 5, '0', STR_PAD_LEFT) . "_{$fechaCarbon->format('Y-m-d')}_paquete.jpg" : null,
                    'comprobante_devolucion'=> in_array($v['estado'], ['Devolución', 'Cambio']) ? "comprobantes_devolucion/VNT-" . str_pad($ventaId, 5, '0', STR_PAD_LEFT) . "_{$fechaCarbon->format('Y-m-d')}_devolucion.jpg" : null,
                ]);

                // 3. COMISIONES DE VENDEDOR:
                // REGLA DE NEGOCIO: Solo se generan si la venta está 'Entregada' o 'Cambio' (o cancelada con registro anulado)
                if ($v['estado'] === 'Entregada' || $v['estado'] === 'Cambio') {
                    $comisionPagada = $v['comision_pagada'];
                    $concepto = "Venta #{$ventaId} ({$cant}x {$var['producto']} - {$var['nombre']})";

                    DB::table('comision_vendedor')->insert([
                        'id_vendedor'       => $v['vendedor_id'],
                        'id_salida'         => $salidaId,
                        'concepto'          => $concepto,
                        'monto'             => $comisionLinea,
                        'porcentaje'        => $var['comision'],
                        'estado'            => $comisionPagada ? 'Pagada' : 'Pendiente',
                        'metodo_pago'       => $comisionPagada ? 'Transferencia' : null,
                        'referencia_pago'   => $comisionPagada ? 'TRF-' . str_pad($salidaId, 6, '0', STR_PAD_LEFT) : null,
                        'comprobante_pago'  => null,
                        'notas'             => ($v['tipo_venta'] === 'Envio') ? 'Comisión registrada tras entrega exitosa del envío.' : 'Venta directa en mostrador.',
                        'liquidado_por'     => $comisionPagada ? 1 : null,
                        'fecha_liquidacion' => $comisionPagada ? $fechaCarbon->copy()->addDays(3) : null,
                        'fecha_registro'    => $fechaEntrega ?? $fechaCarbon,
                    ]);
                }
            }
        }
    }

    /**
     * 5. DEVOLUCIONES
     */
    protected function seedDevoluciones(): void
    {
        $this->command->info('  -> Sembrando Devoluciones...');

        $resolucionesVenta = ['reembolso_tienda', 'reembolso_cuarentena', 'cambio_tienda', 'cambio_cuarentena'];
        $motivos = [
            'Producto no encajó en el modelo específico del vehículo del cliente.',
            'Luz presenta parpadeo intermitente al encender.',
            'Cliente solicitó cambio de color/temperatura de iluminación.',
            'Carcasa de cámara foco recibida con fisura leve de empaque.',
            'Color de accesorio no coincidió con la expectativa del comprador.',
            'Presentó falso contacto en el conector tras 2 días de uso.',
            'Cliente cambió de opinión antes del periodo de garantía de 4 días.',
            'Defecto de fábrica en el switch de encendido.',
        ];

        // 8 devoluciones de VENTA vinculadas a ventas entregadas o con reclamo
        for ($i = 1; $i <= 8; $i++) {
            $ventaId = $i;
            $resolucion = $resolucionesVenta[($i - 1) % count($resolucionesVenta)];
            $motivo = $motivos[$i - 1];
            $fecha = Carbon::parse('2026-10-10')->addDays($i);

            $devId = DB::table('devoluciones')->insertGetId([
                'origen_tipo'       => 'venta',
                'origen_id'         => $ventaId,
                'tipo_resolucion'   => $resolucion,
                'estado'            => 'completado',
                'detalles_json'     => json_encode(['gestionado_por' => 'Atención a Clientes AXStore']),
                'monto_reembolsado' => str_contains($resolucion, 'reembolso') ? 19.00 : 0.00,
                'motivo'            => $motivo,
                'comprobante'       => "comprobantes_devolucion/DEV-VNT-{$ventaId}.jpg",
                'created_at'        => $fecha,
                'updated_at'        => $fecha,
            ]);

            DB::table('devolucion_detalles')->insert([
                'devolucion_id' => $devId,
                'id_variante'   => 1, // Luz Blanca
                'cantidad'      => 1,
                'created_at'    => $fecha,
                'updated_at'    => $fecha,
            ]);

            DB::table('movimiento_bodega')->insert([
                'id_variante'               => 1,
                'id_compra'                 => null,
                'devolucion_id'             => $devId,
                'id_usuario'                => 1,
                'tipo'                      => 'devolucion_cliente',
                'cantidad'                  => 1,
                'reserva_anterior'          => 0,
                'reserva_nueva'             => 0,
                'stock_anterior'            => 15,
                'stock_nuevo'               => str_contains($resolucion, 'tienda') ? 16 : 15,
                'stock_cuarentena_anterior' => 0,
                'stock_cuarentena_nuevo'    => str_contains($resolucion, 'cuarentena') ? 1 : 0,
                'observacion'               => "Devolución de cliente #{$devId} por: {$motivo}",
                'created_at'                => $fecha,
                'updated_at'                => $fecha,
            ]);
        }

        // 7 devoluciones de COMPRA (a proveedores por defectos de lote)
        $motivosCompra = [
            'Lote con 3 unidades defectuosas en prueba de banco.',
            'Cajas de empaque dañadas durante transporte de importación.',
            'Variante enviada no corresponde al código solicitado en la orden.',
            'Faltante de 2 unidades verificado en conteo ciego de bodega.',
            'Conector suelto en lote de exploradoras alogenas.',
            'Color de iluminación fuera de tolerancia de temperatura Kelvin.',
            'Falla de encendido en switch del proveedor.',
        ];

        for ($j = 1; $j <= 7; $j++) {
            $compraId = $j;
            $motivo = $motivosCompra[$j - 1];
            $fecha = Carbon::parse('2026-10-15')->addDays($j);

            $devId = DB::table('devoluciones')->insertGetId([
                'origen_tipo'       => 'compra',
                'origen_id'         => $compraId,
                'tipo_resolucion'   => 'devolucion_proveedor',
                'estado'            => 'completado',
                'detalles_json'     => json_encode(['aprobado_por' => 'Jefe de Bodega AXStore']),
                'monto_reembolsado' => 24.00,
                'motivo'            => $motivo,
                'comprobante'       => "comprobantes_devolucion/DEV-CMP-{$compraId}.pdf",
                'created_at'        => $fecha,
                'updated_at'        => $fecha,
            ]);

            DB::table('devolucion_detalles')->insert([
                'devolucion_id' => $devId,
                'id_variante'   => 2, // Luz Amarilla
                'cantidad'      => 2,
                'created_at'    => $fecha,
                'updated_at'    => $fecha,
            ]);

            DB::table('movimiento_bodega')->insert([
                'id_variante'               => 2,
                'id_compra'                 => $compraId,
                'devolucion_id'             => $devId,
                'id_usuario'                => 1,
                'tipo'                      => 'devolucion_proveedor',
                'cantidad'                  => 2,
                'reserva_anterior'          => 0,
                'reserva_nueva'             => 0,
                'stock_anterior'            => 18,
                'stock_nuevo'               => 16,
                'stock_cuarentena_anterior' => 0,
                'stock_cuarentena_nuevo'    => 0,
                'observacion'               => "Devolución a proveedor según compra #{$compraId}: {$motivo}",
                'created_at'                => $fecha,
                'updated_at'                => $fecha,
            ]);
        }
    }

    /**
     * 6. BITÁCORA DE AUDITORÍA
     */
    protected function seedBitacora(): void
    {
        $this->command->info('  -> Sembrando Bitacora de auditoria...');

        $usuarios = DB::table('usuario')->get();
        $eventos = [
            ['LOGIN',         'Autenticación', 'Inicio de sesión en plataforma web', null, null],
            ['CREAR',         'Ventas',        'Se registró la venta #1 a Mauricio Alexander Rivas por un total de $38.00 (Método: Efectivo, Tipo: Tienda).', null, ['id_venta' => 1]],
            ['CREAR',         'Ventas',        'Se registró la venta #2 a Beatriz Eugenia Sandoval por un total de $63.00 (Método: Transferencia Bancaria, Tipo: Tienda).', null, ['id_venta' => 2]],
            ['CREAR',         'Ventas',        'Se registró la venta #3 a Guillermo José Portillo por un total de $47.00 (Método: Efectivo, Tipo: Envio).', null, ['id_venta' => 3]],
            ['CAMBIO_ESTADO', 'Ventas',        "Venta #3 cambió de estado: 'Pendiente' → 'Confirmada'.", ['estado' => 'Pendiente'], ['estado' => 'Confirmada']],
            ['CAMBIO_ESTADO', 'Ventas',        "Venta #3 cambió de estado: 'Confirmada' → 'En ruta'.", ['estado' => 'Confirmada'], ['estado' => 'En ruta']],
            ['CAMBIO_ESTADO', 'Ventas',        "Venta #3 cambió de estado: 'En ruta' → 'Entregada'.", ['estado' => 'En ruta'], ['estado' => 'Entregada']],
            ['CREAR',         'Compras',       'Se registró compra #1 con referencia FAC-2026-001 al proveedor La Curacao El Salvador.', null, ['id_compra' => 1]],
            ['CREAR',         'Compras',       'Se registró compra #2 con referencia FAC-2026-002 al proveedor ImpreCar AutoPartes S.A.', null, ['id_compra' => 2]],
            ['CREAR',         'Inventario',    'Entrada de bodega por recepción de compra #1.', null, ['modulo' => 'Bodega']],
            ['CREAR',         'Comisiones',    'Liquidación de comisiones de ventas de periodo quincenal a vendedor Ana Plasma.', null, ['vendedor' => 'Ana Plasma']],
            ['CAMBIO_ESTADO', 'Ventas',        "Venta #8 cambió de estado: 'Pendiente' → 'Cancelada'. Observaciones: Cliente canceló el pedido por viaje imprevisto.", ['estado' => 'Pendiente'], ['estado' => 'Cancelada']],
            ['CREAR',         'Devoluciones',  'Se procesó devolución #1 originada de la venta #1.', null, ['devolucion_id' => 1]],
            ['EDITAR',        'Usuarios',      'Se actualizó la información de contacto y comisión del usuario vendedor Carlos Mendoza.', null, ['usuario_id' => 3]],
            ['LOGIN',         'Autenticación', 'Inicio de sesión de administrador Josue Martinez desde IP autorizada.', null, null],
            ['CREAR',         'Ventas',        'Se registró la venta #4 a Sonia Maribel Calderón por un total de $43.10 (Método: Transferencia Bancaria, Tipo: Envio).', null, ['id_venta' => 4]],
            ['CAMBIO_ESTADO', 'Ventas',        "Venta #4 cambió de estado: 'En ruta' → 'Entregada'.", ['estado' => 'En ruta'], ['estado' => 'Entregada']],
            ['LOGOUT',        'Autenticación', 'Cierre de sesión seguro en sistema.', null, null],
            ['LOGIN',         'Autenticación', 'Inicio de sesión de vendedor Ana Plasma.', null, null],
            ['CAMBIO_ESTADO', 'Ventas',        "Venta #11 cambió de estado: 'Entregada' → 'Cambio'. Observaciones: Solicitud de cambio a luz cálida.", ['estado' => 'Entregada'], ['estado' => 'Cambio']],
        ];

        foreach ($eventos as $i => $ev) {
            $usr = $usuarios[$i % count($usuarios)];
            $fecha = Carbon::parse('2026-10-01')->addDays($i)->setTime(8 + ($i % 10), rand(10, 59), 0);

            DB::table('bitacora')->insert([
                'id_usuario'       => $usr->id,
                'username'         => $usr->username,
                'nombre_usuario'   => $usr->nombre_real,
                'rol_usuario'      => $usr->rol,
                'accion'           => $ev[0],
                'modulo'           => $ev[1],
                'descripcion'      => $ev[2],
                'datos_anteriores' => $ev[3] ? json_encode($ev[3]) : null,
                'datos_nuevos'     => $ev[4] ? json_encode($ev[4]) : null,
                'ip_address'       => '192.168.1.' . (10 + $i),
                'user_agent'       => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/156.0.0.0 Safari/537.36',
                'created_at'       => $fecha,
            ]);
        }
    }

    /**
     * 7. ACTUALIZACIÓN DE SECUENCIAS (PostgreSQL)
     */
    protected function resetPostgresSequences(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $tables = [
            'proveedors',
            'usuario',
            'compra',
            'compra_detalle',
            'movimiento_bodega',
            'venta',
            'detalleventa',
            'salida',
            'comision_vendedor',
            'devoluciones',
            'devolucion_detalles',
            'bitacora',
        ];

        foreach ($tables as $table) {
            try {
                $seq = DB::selectOne("SELECT pg_get_serial_sequence('{$table}', 'id') AS seq");
                if ($seq && $seq->seq) {
                    DB::statement("SELECT setval('{$seq->seq}', COALESCE((SELECT MAX(id) FROM \"{$table}\"), 1))");
                }
            } catch (\Throwable $e) {
                // Si la tabla no tiene secuencia serial, continuar
            }
        }
    }
}
