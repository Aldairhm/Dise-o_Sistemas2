<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Usuario Administrador
        User::updateOrCreate(
            ['username' => 'steven19denoviembre@gmail.com'],
            [
                'nombre_real' => 'Josue',
                'password'    => Hash::make('123456789'),
                'rol'         => 'admin',
                'estado'      => 1,
            ]
        );

    }
}
