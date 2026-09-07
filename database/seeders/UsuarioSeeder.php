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
            ['username' => 'ar23052@ues.edu.sv'],
            [
                'nombre_real' => 'Edwin Ascencio',
                'password'    => Hash::make('AR23052_175$'),
                'rol'         => 'admin',
                'estado'      => 1,
            ]
        );

    }
}
