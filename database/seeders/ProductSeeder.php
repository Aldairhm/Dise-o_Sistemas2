<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Product::insert([
            [
                'nombre' => 'Collar de Perlas Elegante',
                'nombre_categoria' => 'Joyería Fina',
                'nombre_producto_padre' => 'Collares',
                'comision' => 2.50,
                'precio_venta' => 45.00,
                'sku' => 'COL-PER-001',
                'stock' => 10,
                'reserva' => 2,
                'imagen' => 'perla.png',
                'imagen_hover' => 'perla.png',
            ],
            [
                'nombre' => 'Anillo de Diamante Simulado',
                'nombre_categoria' => 'Anillos',
                'nombre_producto_padre' => 'Anillos de Compromiso',
                'comision' => 5.00,
                'precio_venta' => 75.00,
                'sku' => 'ANI-DIA-001',
                'stock' => 5,
                'reserva' => 1,
                'imagen' => 'anillo.png',
                'imagen_hover' => 'anillo2.png',
            ],
            [
                'nombre' => 'Pulsera de Oro 18k',
                'nombre_categoria' => 'Pulseras',
                'nombre_producto_padre' => 'Joyería Fina',
                'comision' => 1.50,
                'precio_venta' => 35.00,
                'sku' => 'PUL-ORO-001',
                'stock' => 0,
                'reserva' => 0,
                'imagen' => 'pulsera.png',
                'imagen_hover' => 'pulsera.png',
            ],
            [
                'nombre' => 'Pendientes Clásicos',
                'nombre_categoria' => 'Pendientes',
                'nombre_producto_padre' => 'Aretes',
                'comision' => 0.50,
                'precio_venta' => 15.00,
                'sku' => 'PEN-CLA-001',
                'stock' => 0,
                'reserva' => 5,
                'imagen' => 'pendiente.png',
                'imagen_hover' => 'pendiente.png',
            ],
        ]);
    }
}
