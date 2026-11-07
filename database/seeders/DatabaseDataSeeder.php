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

        // 1. Sembrar Proveedores (≥ 15)
        $this->seedProveedores();

        // 2. Sembrar Usuarios (≥ 15)
        $this->seedUsuarios();

        // 3. Sembrar Catálogo base (Categorías, Atributos, Productos, Variantes)
        $this->seedCategoria();
        $this->seedAtributo();
        $this->seedProducto();
        $this->seedCatalogos();
        $this->seedProductoatributo();
        $this->seedVariante();
        $this->seedVarianteImagen();
        $this->seedVariantevalor();

        // 4. Sembrar Compras y Movimientos de Entrada (≥ 15)
        $this->seedCompras();

        // 4. Sembrar Ventas con toda su lógica detallada (≥ 18)
        $this->seedVentas();

        // 5. Sembrar Devoluciones (≥ 15)
        $this->seedDevoluciones();

        // 6. Sembrar Bitácora (≥ 20)
        $this->seedBitacora();

        // 7. Sincronizar secuencias de PostgreSQL
        $this->resetPostgresSequences();

        Schema::enableForeignKeyConstraints();

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

    protected function seedCategoria(): void
    {
        $data = [
            [
                'id' => 1,
                'nombre' => 'Hogar',
                'descripcion' => 'Accesorios para el hogar',
                'color' => '#d946ef',
                'estado' => true,
                'icono' => 'fa-couch',
                'orden' => 1,
            ],
            [
                'id' => 2,
                'nombre' => 'AutoParte',
                'descripcion' => 'Repuesto de auto',
                'color' => '#f97316',
                'estado' => true,
                'icono' => 'fa-car',
                'orden' => 2,
            ],
            [
                'id' => 3,
                'nombre' => 'Tecnologia',
                'descripcion' => 'Productos electrónicos',
                'color' => '#10b981',
                'estado' => false,
                'icono' => 'fa-laptop',
                'orden' => 5,
            ],
            [
                'id' => 4,
                'nombre' => 'Cocina',
                'descripcion' => 'Todo para remodelar la cocina',
                'color' => '#ef4444',
                'estado' => true,
                'icono' => 'fa-utensils',
                'orden' => 3,
            ],
            [
                'id' => 5,
                'nombre' => 'Juguetes',
                'descripcion' => 'Juguetes para niños',
                'color' => '#f43f5e',
                'estado' => true,
                'icono' => 'fa-basketball',
                'orden' => 4,
            ],
        ];

        DB::table('categoria')->upsert($data, ['id'], ['nombre', 'descripcion', 'color', 'estado', 'icono', 'orden']);
    }

    protected function seedAtributo(): void
    {
        $data = [
            [
                'id' => 1,
                'nombre' => 'Medidas',
                'created_at' => '2026-09-21 22:34:23',
                'updated_at' => '2026-09-21 22:34:23',
            ],
            [
                'id' => 2,
                'nombre' => 'Lumenes',
                'created_at' => '2026-09-21 22:34:23',
                'updated_at' => '2026-09-21 22:34:23',
            ],
            [
                'id' => 3,
                'nombre' => 'Distancia',
                'created_at' => '2026-09-21 22:34:23',
                'updated_at' => '2026-09-21 22:34:23',
            ],
            [
                'id' => 4,
                'nombre' => 'Capacidad (voltios)',
                'created_at' => '2026-09-22 10:25:13',
                'updated_at' => '2026-09-22 10:25:13',
            ],
            [
                'id' => 5,
                'nombre' => 'Angulo',
                'created_at' => '2026-09-22 10:49:32',
                'updated_at' => '2026-09-22 10:49:32',
            ],
            [
                'id' => 6,
                'nombre' => 'Medida',
                'created_at' => '2026-09-23 08:56:55',
                'updated_at' => '2026-09-23 08:56:55',
            ],
        ];

        DB::table('atributo')->upsert($data, ['id'], ['nombre', 'created_at', 'updated_at']);
    }

    protected function seedProducto(): void
    {
        $data = [
            [
                'id' => 1,
                'id_categoria' => 2,
                'sku' => 'ALO-A-001',
                'nombre' => 'ALOGENAS ESPLORADORA FUERTE ILUMINACIÓN CON SWITCH.',
                'descripcion' => 'ALOGENAS ESPLORADORA FUERTE ILUMINACIÓN CON SWITCH.

PRECIO DEL PAR $19

ALOGENAS XPLORADORA PARA MOTOS Y AUTOMÓVIL🚗🏍️
✅LUZ BLANCA, Y AMARILLA. 
✅40mil lumines de potencia 
Distancia 450 metros mejor alumbrado. 
✅LO último en tecnología a pesar el tamaño q tiene maneja una intensidad de luz a gran distancia visible. 
✅Material metálico resistente, interperie. 
✅Swich de apagado 
Manejo de luz alta y baja 
✅TAMAÑO 5Cm ANCHO Y LARGO 5.5CM

ENVIOS A TODO EL PAIS',
                'comision' => '3.00',
                'marca' => 'Angel',
                'imagen_principal' => 'productos/qr7W1wJPgEFbzEoHgjRCJgWiKf3SUqwst2qYJihq.jpg',
                'estado' => 1,
                'created_at' => '2026-09-21 22:34:23',
                'updated_at' => '2026-09-23 09:05:52',
            ],
            [
                'id' => 3,
                'id_categoria' => 2,
                'sku' => 'LUC-A-001',
                'nombre' => 'LUCES WATERMELON PARA AUTOMOVIL',
                'descripcion' => 'LUCES WATERMELON PARA AUTOMOVIL 

PRECIO PAR $8

✅LUCES WATERMELON CON SISTEMA LUZ FIJA Y STROBERS LA MEJOR MANERA PARA DARLE UN DISEÑO ÚNICO.
✅️12 voltios a 24 voltios 
✅️Medida de 5cm de altura 
Ancho de 4CM 
✅️COLORES 
🔷️AZUL HIELO 
🔷️MORADO

BUSCANOS metro galerías local 3.5A tercer nivel frente a metro san salvador',
                'comision' => '2.00',
                'marca' => 'MelonWater',
                'imagen_principal' => 'productos/B3FG2ERyjZzyNBZSWmPXDBlQJiWgCV1tHfgqBAXy.jpg',
                'estado' => 1,
                'created_at' => '2026-09-22 10:23:45',
                'updated_at' => '2026-09-22 10:23:45',
            ],
            [
                'id' => 4,
                'id_categoria' => 1,
                'sku' => 'CAM-H-001',
                'nombre' => 'CAMARA FOCO Giro 360°',
                'descripcion' => 'CAMARA DE SEGURIDAD INTELIGENTE TIPO FOCO 2 CAMARAS 

Precio $24

✅ CAMARA INTELIGENTE YOOSSE PARA TU HOGAR O NEGOCIO GIRO 360° 
Camara FULL HD: Sensor de 1080p de ultima generación.
Seguimiento Automatico: Se activa automaticamente con los movimientos .
Sistema de audio: El microfono con grabadora de audio detecta todos los sonidos ambientales.
Compatibilidad: Todos los Smarphones Compatibles.
✅Se abre con aplicacion YOOSSE 
✅ Abre las dos camaras al mismo tiempo 
✅RESOLUCIÓN NOCTURNA HD. 
✅Pueden conectarse varios dispositivos ala ves. 

✅CONTAMOS CON ENVIOS TODO EL PAIS 🚗',
                'comision' => '4.00',
                'marca' => 'Jordan',
                'imagen_principal' => 'productos/mAGt1wqoh2oJHunOFmXlhFyZGLk8LR4kpPQCetQ0.jpg',
                'estado' => 1,
                'created_at' => '2026-09-22 10:49:32',
                'updated_at' => '2026-09-22 10:49:32',
            ],
            [
                'id' => 5,
                'id_categoria' => 5,
                'sku' => 'MOT-J-001',
                'nombre' => 'MOTO COLECCIÓN DEPORTIVA S1000 RR',
                'descripcion' => 'MOTO COLECCIÓN DEPORTIVA S1000 RR 

✅️MOTITO COLECCION S1000 RR DISEÑO BMW
CON SUSPENCION DE AMORTIGUACION FLEXIBLE Y LLANTAS RODABLES.
✅️Tanque metálico 
✅️Llantas de hule resistente 
✅️Base de exhibición.
✅Medida Largo 21CM
Altura 13CM
🔷️COLORES
⏺️Blanco 
⏺️Negro

ENVIOS TODO EL PAIS',
                'comision' => '3.00',
                'marca' => 'S1000 RR',
                'imagen_principal' => 'productos/TcPxauCfWnJBl8BfuuijymxQZvx9kDZYfQataxBP.jpg',
                'estado' => 1,
                'created_at' => '2026-09-22 21:50:28',
                'updated_at' => '2026-09-22 21:50:28',
            ],
            [
                'id' => 7,
                'id_categoria' => 2,
                'sku' => 'LUC-A-002',
                'nombre' => 'LUCES TRASERAS CON EFECTO 3D',
                'descripcion' => 'LUCES TRASERAS CON EFECTO 3D

✅️LUCES TRASERAS LED redondas de 4 pulgadas con diseño de efecto de túnel infinito.
✅️Luces redondas con efecto óptico 3D de profundidad tipo abismo o túnel infinito.Uso: Ideales para funciones de freno, parada, giro o posición en camiones y trailers.
✅️COLORES 
🔷️AMARILLO 
🔷️ROJO
🔷️MORADO 
🚚ENVIOS A TODO EL PAIS',
                'comision' => '3.00',
                'marca' => NULL,
                'imagen_principal' => 'productos/DWTCl2ADxJUA9eR70DPIckZ6L1Ol0e5LfFkBKKQ0.jpg',
                'estado' => 1,
                'created_at' => '2026-09-23 08:56:55',
                'updated_at' => '2026-09-23 08:56:55',
            ],
        ];

        DB::table('producto')->upsert($data, ['id'], ['id_categoria', 'sku', 'nombre', 'descripcion', 'comision', 'marca', 'imagen_principal', 'estado', 'created_at', 'updated_at']);
    }

    protected function seedCatalogos(): void
    {
        $data = [
            [
                'id' => 1,
                'tipo' => 'pdf',
                'ruta_destino' => 'catalogos_proveedores/Zh7eTlI5r1ui7u1itiY6ohz3KvIRpSY1KRshRGkG.pdf',
                'nombre_referencia' => 'Catalogo Verano 2026',
                'id_proveedor' => 1,
                'created_at' => '2026-09-21 22:31:21',
                'updated_at' => '2026-09-21 22:31:21',
            ],
        ];

        DB::table('catalogos')->upsert($data, ['id'], ['tipo', 'ruta_destino', 'nombre_referencia', 'id_proveedor', 'created_at', 'updated_at']);
    }

    protected function seedProductoatributo(): void
    {
        $data = [
            [
                'id' => 1,
                'id_producto' => 1,
                'id_atributo' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 4,
                'id_producto' => 3,
                'id_atributo' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 5,
                'id_producto' => 1,
                'id_atributo' => 2,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 6,
                'id_producto' => 1,
                'id_atributo' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 7,
                'id_producto' => 4,
                'id_atributo' => 5,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 8,
                'id_producto' => 5,
                'id_atributo' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 9,
                'id_producto' => 7,
                'id_atributo' => 2,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 10,
                'id_producto' => 7,
                'id_atributo' => 6,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
        ];

        DB::table('productoatributo')->upsert($data, ['id'], ['id_producto', 'id_atributo', 'created_at', 'updated_at']);
    }

    protected function seedVariante(): void
    {
        $data = [
            [
                'id' => 1,
                'id_producto' => 1,
                'estado' => 1,
                'sku' => 'VAR-LUZ-001',
                'hash_combinacion' => '20ce2348afb6724f378d959bcfc91c62',
                'nombre_variante' => 'Luz Blanca',
                'precio_venta' => '19.00',
                'comision' => '0.00',
                'stock' => 18,
                'reserva' => 30,
                'costo_promedio' => '8.00',
                'porcentaje_ganancia' => '100.00',
                'imagen' => '',
                'created_at' => '2026-09-21 22:46:57',
                'updated_at' => '2026-09-26 22:38:22',
            ],
            [
                'id' => 2,
                'id_producto' => 1,
                'estado' => 1,
                'sku' => 'VAR-LUZ-002',
                'hash_combinacion' => 'a8d8fe3f876778a4457abdc056db2cb1',
                'nombre_variante' => 'Luz Amarrilla',
                'precio_venta' => '19.80',
                'comision' => '0.00',
                'stock' => 14,
                'reserva' => 35,
                'costo_promedio' => '8.40',
                'porcentaje_ganancia' => '100.00',
                'imagen' => '',
                'created_at' => '2026-09-21 23:03:00',
                'updated_at' => '2026-09-26 19:41:10',
            ],
            [
                'id' => 6,
                'id_producto' => 3,
                'estado' => 1,
                'sku' => 'VAR-MOR-001',
                'hash_combinacion' => 'ed9ab28c836a3dc726f21b25fe8316c3',
                'nombre_variante' => 'Morado',
                'precio_venta' => '2.00',
                'comision' => '0.00',
                'stock' => 0,
                'reserva' => 0,
                'costo_promedio' => '0.00',
                'porcentaje_ganancia' => '100.00',
                'imagen' => '',
                'created_at' => '2026-09-22 10:25:55',
                'updated_at' => '2026-09-22 10:25:55',
            ],
            [
                'id' => 7,
                'id_producto' => 3,
                'estado' => 1,
                'sku' => 'VAR-AZU-001',
                'hash_combinacion' => '2e113ea850bb3db498aa5d5734f88057',
                'nombre_variante' => 'Azul',
                'precio_venta' => '2.00',
                'comision' => '0.00',
                'stock' => 0,
                'reserva' => 0,
                'costo_promedio' => '0.00',
                'porcentaje_ganancia' => '100.00',
                'imagen' => '',
                'created_at' => '2026-09-22 10:26:14',
                'updated_at' => '2026-09-22 10:26:14',
            ],
            [
                'id' => 9,
                'id_producto' => 1,
                'estado' => 1,
                'sku' => 'VAR-COM-001',
                'hash_combinacion' => '58c763ecab2290f0aecd0f2eb7803ae3',
                'nombre_variante' => 'Combinada',
                'precio_venta' => '29.67',
                'comision' => '0.00',
                'stock' => 0,
                'reserva' => 30,
                'costo_promedio' => '13.33',
                'porcentaje_ganancia' => '100.00',
                'imagen' => '',
                'created_at' => '2026-09-22 10:35:20',
                'updated_at' => '2026-09-22 10:37:49',
            ],
            [
                'id' => 10,
                'id_producto' => 4,
                'estado' => 1,
                'sku' => 'VAR-DOS-001',
                'hash_combinacion' => '771d042fd96cad260f7cc956f4e98775',
                'nombre_variante' => 'Dos focos',
                'precio_venta' => '24.00',
                'comision' => '0.00',
                'stock' => 0,
                'reserva' => 20,
                'costo_promedio' => '10.00',
                'porcentaje_ganancia' => '100.00',
                'imagen' => '',
                'created_at' => '2026-09-22 10:50:12',
                'updated_at' => '2026-09-22 10:51:54',
            ],
            [
                'id' => 11,
                'id_producto' => 5,
                'estado' => 1,
                'sku' => 'VAR-BLA-001',
                'hash_combinacion' => 'ed9dfc2a1a8f933e91f0d47f3e555db7',
                'nombre_variante' => 'Blanca',
                'precio_venta' => '17.00',
                'comision' => '0.00',
                'stock' => 0,
                'reserva' => 30,
                'costo_promedio' => '7.00',
                'porcentaje_ganancia' => '100.00',
                'imagen' => '',
                'created_at' => '2026-09-22 21:52:58',
                'updated_at' => '2026-09-22 22:05:39',
            ],
            [
                'id' => 12,
                'id_producto' => 5,
                'estado' => 0,
                'sku' => 'VAR-NEG-001',
                'hash_combinacion' => 'b68892266432161695f27329aa80820d',
                'nombre_variante' => 'Negro',
                'precio_venta' => '3.00',
                'comision' => '0.00',
                'stock' => 0,
                'reserva' => 0,
                'costo_promedio' => '0.00',
                'porcentaje_ganancia' => '100.00',
                'imagen' => '',
                'created_at' => '2026-09-22 21:54:10',
                'updated_at' => '2026-09-22 22:13:20',
            ],
            [
                'id' => 13,
                'id_producto' => 7,
                'estado' => 1,
                'sku' => 'VAR-ROJ-001',
                'hash_combinacion' => '8e92fb699b5c06058f9ae7993baaa1cf',
                'nombre_variante' => 'Roja',
                'precio_venta' => '3.00',
                'comision' => '0.00',
                'stock' => 0,
                'reserva' => 0,
                'costo_promedio' => '0.00',
                'porcentaje_ganancia' => '100.00',
                'imagen' => '',
                'created_at' => '2026-09-23 09:01:27',
                'updated_at' => '2026-09-23 09:01:27',
            ],
            [
                'id' => 14,
                'id_producto' => 7,
                'estado' => 1,
                'sku' => 'VAR-AMA-001',
                'hash_combinacion' => '0904d1201bc211e624b1878ec55425df',
                'nombre_variante' => 'Amarilla',
                'precio_venta' => '63.00',
                'comision' => '0.00',
                'stock' => 12,
                'reserva' => 7,
                'costo_promedio' => '30.00',
                'porcentaje_ganancia' => '100.00',
                'imagen' => '',
                'created_at' => '2026-09-23 09:02:13',
                'updated_at' => '2026-09-27 14:03:45',
            ],
        ];

        DB::table('variante')->upsert($data, ['id'], ['id_producto', 'estado', 'sku', 'hash_combinacion', 'nombre_variante', 'precio_venta', 'comision', 'stock', 'reserva', 'costo_promedio', 'porcentaje_ganancia', 'imagen', 'created_at', 'updated_at']);
    }

    protected function seedVarianteImagen(): void
    {
        $data = [
            [
                'id' => 4,
                'id_variante' => 2,
                'ruta_imagen' => 'variantes/B0XPX3OmBoDeb2Tmc9xZHCsVbYQqCuDuQiH6PAqo.jpg',
                'es_principal' => 1,
                'created_at' => '2026-09-21 23:03:00',
                'updated_at' => '2026-09-21 23:03:00',
            ],
            [
                'id' => 5,
                'id_variante' => 2,
                'ruta_imagen' => 'variantes/J9VPyl8EqJpRHuBWzouXDWHipdI9CeycZiq3VK4j.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-21 23:03:00',
                'updated_at' => '2026-09-21 23:03:00',
            ],
            [
                'id' => 6,
                'id_variante' => 2,
                'ruta_imagen' => 'variantes/bYL9LiUUVsZ6k6VFicTkycvDizZqTjW2SwIOjupJ.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-21 23:03:00',
                'updated_at' => '2026-09-21 23:03:00',
            ],
            [
                'id' => 13,
                'id_variante' => 6,
                'ruta_imagen' => 'variantes/qZfX5vlZBrZ3aONdweSeRbt7cntcHKcviUtgbYwS.jpg',
                'es_principal' => 1,
                'created_at' => '2026-09-22 10:25:55',
                'updated_at' => '2026-09-22 10:25:55',
            ],
            [
                'id' => 14,
                'id_variante' => 6,
                'ruta_imagen' => 'variantes/7hQNcdOsvwIWEgqBtRMWHBU5ouTevoboptOakvtl.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-22 10:25:55',
                'updated_at' => '2026-09-22 10:25:55',
            ],
            [
                'id' => 15,
                'id_variante' => 7,
                'ruta_imagen' => 'variantes/TUX2vD6XRSS4Dg1GICPnvaJCdUig7YwUv0FtVRE2.jpg',
                'es_principal' => 1,
                'created_at' => '2026-09-22 10:26:14',
                'updated_at' => '2026-09-22 10:26:14',
            ],
            [
                'id' => 16,
                'id_variante' => 7,
                'ruta_imagen' => 'variantes/FuCwRANQhKGeSvbOHBwiT8xm1ROkz3AvJu7NbWeT.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-22 10:26:14',
                'updated_at' => '2026-09-22 10:26:14',
            ],
            [
                'id' => 17,
                'id_variante' => 1,
                'ruta_imagen' => 'variantes/kNO3nARwL64Ra6SHy1vkjbewgs9OqFpil4c1RZaE.jpg',
                'es_principal' => 1,
                'created_at' => '2026-09-22 10:28:21',
                'updated_at' => '2026-09-22 10:34:56',
            ],
            [
                'id' => 18,
                'id_variante' => 1,
                'ruta_imagen' => 'variantes/5nO4xKefOrvrSTqXs2WhPFWyHFF4Ef6glZOSo7ij.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-22 10:28:21',
                'updated_at' => '2026-09-22 10:34:56',
            ],
            [
                'id' => 19,
                'id_variante' => 1,
                'ruta_imagen' => 'variantes/jm6ZtVARt4EjSI1JzZjswDrZom3o91syFnAKvFc7.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-22 10:28:21',
                'updated_at' => '2026-09-22 10:34:56',
            ],
            [
                'id' => 22,
                'id_variante' => 9,
                'ruta_imagen' => 'variantes/H8Jhjjf6mrW2ICNLo6XwaO5IyyZCpbmFXgvjlJVo.jpg',
                'es_principal' => 1,
                'created_at' => '2026-09-22 10:35:20',
                'updated_at' => '2026-09-22 10:35:20',
            ],
            [
                'id' => 23,
                'id_variante' => 9,
                'ruta_imagen' => 'variantes/6MomgLr6PSXfaGx0iGvjVdrEA7xrTziTLbBtOODv.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-22 10:35:20',
                'updated_at' => '2026-09-22 10:35:20',
            ],
            [
                'id' => 24,
                'id_variante' => 10,
                'ruta_imagen' => 'variantes/uqMHIzKVohoNXcwANgOawa55Ie64mxL05uIMenug.jpg',
                'es_principal' => 1,
                'created_at' => '2026-09-22 10:50:12',
                'updated_at' => '2026-09-22 10:50:12',
            ],
            [
                'id' => 25,
                'id_variante' => 10,
                'ruta_imagen' => 'variantes/vj1MtouiOVdTRq7Yb7KXQrzzoEactKT5YQ1IWK6Z.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-22 10:50:13',
                'updated_at' => '2026-09-22 10:50:13',
            ],
            [
                'id' => 26,
                'id_variante' => 10,
                'ruta_imagen' => 'variantes/awEgL0L00BxJLozQ0kyjIdHDreD5Nge5sKU76Wtp.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-22 10:50:13',
                'updated_at' => '2026-09-22 10:50:13',
            ],
            [
                'id' => 27,
                'id_variante' => 11,
                'ruta_imagen' => 'variantes/mVoAqLNdVMfpigrBgpWsiciqLSXu1xdj1iUAF3yI.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-22 21:52:59',
                'updated_at' => '2026-09-22 21:52:59',
            ],
            [
                'id' => 28,
                'id_variante' => 11,
                'ruta_imagen' => 'variantes/bdDcQpNz4aZqJuPZ2sfIv10BNFl7rGFVm9nPHQf1.jpg',
                'es_principal' => 1,
                'created_at' => '2026-09-22 21:52:59',
                'updated_at' => '2026-09-22 21:52:59',
            ],
            [
                'id' => 29,
                'id_variante' => 11,
                'ruta_imagen' => 'variantes/7a0bzQJRIdw67Ey9bfBYykcdxxH3QFQdogcuHQhs.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-22 21:52:59',
                'updated_at' => '2026-09-22 21:52:59',
            ],
            [
                'id' => 30,
                'id_variante' => 11,
                'ruta_imagen' => 'variantes/7HxObGQdLKJG1LhdVgQxxOljCIQMBAy3sT0CTc8L.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-22 21:52:59',
                'updated_at' => '2026-09-22 21:52:59',
            ],
            [
                'id' => 33,
                'id_variante' => 12,
                'ruta_imagen' => 'variantes/DhQm8icwtM457l5Nz9wNuE2E2GaFUmPp0SNpeJRc.jpg',
                'es_principal' => 1,
                'created_at' => '2026-09-22 21:54:10',
                'updated_at' => '2026-09-22 21:57:00',
            ],
            [
                'id' => 34,
                'id_variante' => 12,
                'ruta_imagen' => 'variantes/e6POemxoVzka1q1TZf8P1k0LWV1xiIT3X6GJdpY1.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-22 21:54:10',
                'updated_at' => '2026-09-22 21:57:00',
            ],
            [
                'id' => 35,
                'id_variante' => 13,
                'ruta_imagen' => 'variantes/zgIekJ0Ixr2BWbG3B6WDXgMQnyG3UhbFKS8ikv4z.jpg',
                'es_principal' => 1,
                'created_at' => '2026-09-23 09:01:27',
                'updated_at' => '2026-09-23 09:01:27',
            ],
            [
                'id' => 36,
                'id_variante' => 13,
                'ruta_imagen' => 'variantes/YwQ3APGHC6M2DE1HBdofZgFM2vpTgk2l7OGIYZRq.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-23 09:01:27',
                'updated_at' => '2026-09-23 09:01:27',
            ],
            [
                'id' => 37,
                'id_variante' => 13,
                'ruta_imagen' => 'variantes/bWW9axJESMcCmVdmaxdwFnUV5FpXF9K01vFM0Z1S.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-23 09:01:27',
                'updated_at' => '2026-09-23 09:01:27',
            ],
            [
                'id' => 38,
                'id_variante' => 13,
                'ruta_imagen' => 'variantes/DFX6ECZ6C7fsW8vToSN7xp1xXaX1kQJP9Lw2AIEb.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-23 09:01:27',
                'updated_at' => '2026-09-23 09:01:27',
            ],
            [
                'id' => 39,
                'id_variante' => 14,
                'ruta_imagen' => 'variantes/fqMRa4n3TonWI5Py04BGzA3eIZMA9Nw1evwXP2tB.jpg',
                'es_principal' => 1,
                'created_at' => '2026-09-23 09:02:13',
                'updated_at' => '2026-09-23 09:02:13',
            ],
            [
                'id' => 40,
                'id_variante' => 14,
                'ruta_imagen' => 'variantes/lHjtIEriNuNPpQIouBGVJTGlAAS9aWFwyHjk6WoO.jpg',
                'es_principal' => 0,
                'created_at' => '2026-09-23 09:02:13',
                'updated_at' => '2026-09-23 09:02:13',
            ],
        ];

        DB::table('variante_imagen')->upsert($data, ['id'], ['id_variante', 'ruta_imagen', 'es_principal', 'created_at', 'updated_at']);
    }

    protected function seedVariantevalor(): void
    {
        $data = [
            [
                'id' => 19,
                'id_variante' => 2,
                'id_atributo' => 1,
                'valor' => '20cm ancho x 13 cm alto',
                'created_at' => '2026-09-21 23:03:00',
                'updated_at' => '2026-09-21 23:03:00',
            ],
            [
                'id' => 20,
                'id_variante' => 2,
                'id_atributo' => 2,
                'valor' => '40 mil',
                'created_at' => '2026-09-21 23:03:00',
                'updated_at' => '2026-09-21 23:03:00',
            ],
            [
                'id' => 21,
                'id_variante' => 2,
                'id_atributo' => 3,
                'valor' => '450 metros',
                'created_at' => '2026-09-21 23:03:00',
                'updated_at' => '2026-09-21 23:03:00',
            ],
            [
                'id' => 25,
                'id_variante' => 6,
                'id_atributo' => 4,
                'valor' => '12 a 24 volt',
                'created_at' => '2026-09-22 10:25:55',
                'updated_at' => '2026-09-22 10:25:55',
            ],
            [
                'id' => 26,
                'id_variante' => 7,
                'id_atributo' => 4,
                'valor' => '12 a 24 volt',
                'created_at' => '2026-09-22 10:26:14',
                'updated_at' => '2026-09-22 10:26:14',
            ],
            [
                'id' => 29,
                'id_variante' => 1,
                'id_atributo' => 1,
                'valor' => '20cm ancho x 13 cm alto',
                'created_at' => '2026-09-22 10:34:56',
                'updated_at' => '2026-09-22 10:34:56',
            ],
            [
                'id' => 30,
                'id_variante' => 1,
                'id_atributo' => 2,
                'valor' => '40 mil',
                'created_at' => '2026-09-22 10:34:56',
                'updated_at' => '2026-09-22 10:34:56',
            ],
            [
                'id' => 31,
                'id_variante' => 1,
                'id_atributo' => 3,
                'valor' => '450 metros',
                'created_at' => '2026-09-22 10:34:56',
                'updated_at' => '2026-09-22 10:34:56',
            ],
            [
                'id' => 32,
                'id_variante' => 9,
                'id_atributo' => 1,
                'valor' => '20cm ancho x 13 cm alto',
                'created_at' => '2026-09-22 10:35:20',
                'updated_at' => '2026-09-22 10:35:20',
            ],
            [
                'id' => 33,
                'id_variante' => 9,
                'id_atributo' => 2,
                'valor' => '40 mil',
                'created_at' => '2026-09-22 10:35:20',
                'updated_at' => '2026-09-22 10:35:20',
            ],
            [
                'id' => 34,
                'id_variante' => 9,
                'id_atributo' => 3,
                'valor' => '650 metros',
                'created_at' => '2026-09-22 10:35:20',
                'updated_at' => '2026-09-22 10:35:20',
            ],
            [
                'id' => 35,
                'id_variante' => 10,
                'id_atributo' => 5,
                'valor' => '360 °',
                'created_at' => '2026-09-22 10:50:13',
                'updated_at' => '2026-09-22 10:50:13',
            ],
            [
                'id' => 36,
                'id_variante' => 11,
                'id_atributo' => 1,
                'valor' => '21cm ancho x 13 cm alto',
                'created_at' => '2026-09-22 21:52:59',
                'updated_at' => '2026-09-22 21:52:59',
            ],
            [
                'id' => 40,
                'id_variante' => 12,
                'id_atributo' => 1,
                'valor' => '21cm ancho x 13 cm alto',
                'created_at' => '2026-09-22 21:57:00',
                'updated_at' => '2026-09-22 21:57:00',
            ],
            [
                'id' => 41,
                'id_variante' => 13,
                'id_atributo' => 2,
                'valor' => '25 mil',
                'created_at' => '2026-09-23 09:01:27',
                'updated_at' => '2026-09-23 09:01:27',
            ],
            [
                'id' => 42,
                'id_variante' => 13,
                'id_atributo' => 6,
                'valor' => '4 pulgadas',
                'created_at' => '2026-09-23 09:01:27',
                'updated_at' => '2026-09-23 09:01:27',
            ],
            [
                'id' => 43,
                'id_variante' => 14,
                'id_atributo' => 2,
                'valor' => '25 mil',
                'created_at' => '2026-09-23 09:02:13',
                'updated_at' => '2026-09-23 09:02:13',
            ],
            [
                'id' => 44,
                'id_variante' => 14,
                'id_atributo' => 6,
                'valor' => '4 pulgadas',
                'created_at' => '2026-09-23 09:02:13',
                'updated_at' => '2026-09-23 09:02:13',
            ],
        ];

        DB::table('variantevalor')->upsert($data, ['id'], ['id_variante', 'id_atributo', 'valor', 'created_at', 'updated_at']);
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
            'categoria',
            'atributo',
            'producto',
            'catalogos',
            'productoatributo',
            'variante',
            'variante_imagen',
            'variantevalor',
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
