<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-superadmin {--email=} {--name=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializa o promueve un usuario con el rol SUPER_ADMIN de forma controlada y segura';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('====================================================');
        $this->info('  AXSTORE - INICIALIZACIÓN CONTROLADA DE SUPER_ADMIN');
        $this->info('====================================================');

        $email = $this->option('email') ?: $this->ask('Correo electrónico del Super Administrador:');
        $name = $this->option('name') ?: $this->ask('Nombre completo:', 'Super Administrador');
        $password = $this->option('password') ?: $this->secret('Contraseña (mínimo 10 caracteres):');

        if (strlen($password) < 10) {
            $this->error('La contraseña debe tener al menos 10 caracteres.');
            return 1;
        }

        $user = User::where('username', $email)->first();

        if ($user) {
            if (!$this->confirm("El usuario {$email} ya existe con rol '{$user->rol}'. ¿Deseas promoverlo a SUPER_ADMIN?", true)) {
                $this->warn('Operación cancelada.');
                return 0;
            }

            $user->rol = 'super_admin';
            $user->estado = 1;
            if (!empty($password)) {
                $user->password = Hash::make($password);
            }
            $user->save();

            $this->info("✓ El usuario {$user->nombre_real} ({$user->username}) ha sido promovido exitosamente a SUPER_ADMIN.");
        } else {
            $user = User::create([
                'nombre_real' => $name,
                'username'    => $email,
                'password'    => Hash::make($password),
                'rol'         => 'super_admin',
                'estado'      => 1,
            ]);

            $this->info("✓ Super Administrador creado exitosamente para {$user->nombre_real} ({$user->username}).");
        }

        ActivityLogger::logAnon(
            'INICIALIZAR_SUPER_ADMIN',
            'Auditoría y Seguridad',
            "Se configuró el Super Administrador para {$user->username} mediante consola CLI controlada.",
            $user->username
        );

        return 0;
    }
}
