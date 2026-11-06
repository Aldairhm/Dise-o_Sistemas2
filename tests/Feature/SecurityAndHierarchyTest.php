<?php

namespace Tests\Feature;

use App\Models\AuditoriaSesion;
use App\Models\Bitacora;
use App\Models\SoporteTicket;
use App\Models\User;
use App\Services\ImpersonationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityAndHierarchyTest extends TestCase
{
    protected User $superAdmin;
    protected User $admin;
    protected User $vendedor;

    protected function setUp(): void
    {
        parent::setUp();

        // Obtener o crear usuarios para pruebas
        $this->superAdmin = User::firstOrCreate(
            ['username' => 'test_superadmin@axstore.com'],
            [
                'nombre_real' => 'Ing. Alejandro Ramos',
                'password'    => Hash::make('SuperPassword2026!'),
                'rol'         => 'super_admin',
                'estado'      => 1,
            ]
        );

        $this->admin = User::firstOrCreate(
            ['username' => 'test_admin@axstore.com'],
            [
                'nombre_real' => 'Rodrigo Morales',
                'password'    => Hash::make('Password2026!'),
                'rol'         => 'admin',
                'estado'      => 1,
            ]
        );

        $this->vendedor = User::firstOrCreate(
            ['username' => 'test_vendedor@axstore.com'],
            [
                'nombre_real' => 'Valeria Gómez',
                'password'    => Hash::make('Password2026!'),
                'rol'         => 'vendedor',
                'estado'      => 1,
            ]
        );
    }

    /** Test 1: Vendedor no puede acceder al panel de Super Administrador */
    public function test_vendedor_cannot_access_superadmin_dashboard(): void
    {
        $response = $this->actingAs($this->vendedor)->get(route('superadmin.dashboard'));
        $response->assertRedirect(route('home'));
    }

    /** Test 2: Admin no puede acceder al panel de Super Administrador */
    public function test_admin_cannot_access_superadmin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('superadmin.dashboard'));
        $response->assertRedirect(route('home'));
    }

    /** Test 3: Super Administrador sí puede acceder a su dashboard de gobernanza */
    public function test_superadmin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Panel de Super Administrador');
    }

    /** Test 4: Administrador convencional no puede asignarse ni crear cuentas con rol super_admin */
    public function test_admin_cannot_create_or_assign_superadmin_role(): void
    {
        $response = $this->actingAs($this->admin)->post(route('usuarios.store'), [
            'nombre_real'           => 'Intento Hacking',
            'username'              => 'hacked@axstore.com',
            'rol'                   => 'super_admin',
            'estado'                => 1,
            'password'              => 'Password1234!',
            'password_confirmation' => 'Password1234!',
        ]);

        // Debe fallar por validación de regla 'in' en UserController
        $response->assertSessionHasErrors('rol');
    }

    /** Test 5: Flujo de Dudas Jerárquicas (Vendedor crea duda y no puede ver dudas ajenas) */
    public function test_vendedor_can_only_see_their_own_tickets(): void
    {
        $codePropio = 'TK-' . rand(100000, 999999);
        $codeAjeno  = 'TK-' . rand(100000, 999999);

        // Ticket del vendedor
        $ticketPropio = SoporteTicket::create([
            'codigo_ticket'      => $codePropio,
            'id_usuario'         => $this->vendedor->id,
            'nombre_usuario'     => $this->vendedor->nombre_real,
            'rol_usuario'        => $this->vendedor->rol,
            'modulo'             => 'Ventas',
            'tipo_consulta'      => 'Duda',
            'duda'               => 'Duda propia de vendedor',
            'estado'             => 'Pendiente',
            'nivel_escalamiento' => 'admin',
        ]);

        // Ticket privado de otro admin o super admin
        $ticketAjeno = SoporteTicket::create([
            'codigo_ticket'      => $codeAjeno,
            'id_usuario'         => $this->admin->id,
            'nombre_usuario'     => $this->admin->nombre_real,
            'rol_usuario'        => $this->admin->rol,
            'modulo'             => 'Configuración',
            'tipo_consulta'      => 'Consulta técnica reservada',
            'duda'               => 'Consulta interna al Super Admin',
            'estado'             => 'Pendiente',
            'nivel_escalamiento' => 'super_admin',
        ]);

        // 1. En el listado no debe aparecer el ajeno y debe aparecer el propio (consultando la última página o buscando por su código)
        $responseList = $this->actingAs($this->vendedor)->get(route('ayuda.tickets.index', ['q' => $codePropio]));
        $responseList->assertStatus(200);
        $responseList->assertSee($codePropio);
        $responseList->assertDontSee($codeAjeno);

        // 2. Al intentar acceder directamente por URL (Anti-IDOR) debe ser denegado con 403
        $responseShow = $this->actingAs($this->vendedor)->get(route('ayuda.tickets.show', $ticketAjeno->id));
        $responseShow->assertStatus(403);
    }

    /** Test 6: Secure Impersonation (Inicio, modo solo lectura, doble identidad en bitácora y finalización) */
    public function test_secure_impersonation_lifecycle(): void
    {
        // 1. Super Admin inicia auditoría sobre vendedor
        $sesion = ImpersonationService::iniciarAuditoria(
            $this->superAdmin,
            $this->vendedor,
            'Auditoría preventiva de comisiones',
            15,
            true // Solo lectura
        );

        $this->assertNotNull($sesion->token_sesion);
        $this->assertEquals('activa', $sesion->estado);
        $this->assertTrue(session()->has('auditoria_token'));
        $this->assertEquals($this->superAdmin->id, session('auditoria_actor_real_id'));

        // 2. Verificar que en modo solo lectura, una petición POST mutante es rechazada
        $responseMutate = $this->post(route('ayuda.tickets.store'), [
            'modulo'        => 'Ventas',
            'tipo_consulta' => 'Duda',
            'duda'          => 'Intento de escritura en auditoria',
        ]);
        $responseMutate->assertSessionHas('error');

        // 3. Finalizar auditoría y restaurar Super Admin
        $finalizado = ImpersonationService::finalizarAuditoria('Auditoría completada exitosamente');
        $this->assertTrue($finalizado);
        $this->assertFalse(session()->has('auditoria_token'));

        // 4. Verificar que se grabó la doble identidad en bitácora
        $ultimoLog = Bitacora::where('modulo', 'Auditoría y Seguridad')->latest('id')->first();
        $this->assertNotNull($ultimoLog);
    }

    /** Test 7: Bloqueo de inicio de sesión cuando la cuenta está en auditoría activa */
    public function test_login_is_blocked_when_account_is_under_active_audit(): void
    {
        // Aseguramos que no haya sesión previa
        $this->flushSession();
        auth()->logout();

        // Creamos una sesión de auditoría activa en base de datos para el vendedor
        $sesion = AuditoriaSesion::create([
            'token_sesion'        => 'token_test_audit_' . rand(1000, 9999),
            'id_super_admin'      => $this->superAdmin->id,
            'id_usuario_auditado' => $this->vendedor->id,
            'justificacion'       => 'Auditoría en curso de prueba',
            'solo_lectura'        => true,
            'estado'              => 'activa',
            'iniciada_en'         => now(),
            'expira_en'           => now()->addMinutes(15),
        ]);

        // Intentar iniciar sesión como el vendedor auditado
        $response = $this->post(route('login.post'), [
            'username' => $this->vendedor->username,
            'password' => 'Password2026!',
        ]);

        // Debe rebotar con alerta swal_warning informando que la cuenta está en auditoría
        $response->assertSessionHas('swal_warning');

        // Limpiar sesión de auditoría de prueba
        $sesion->delete();
    }

    /** Test 8: Si el usuario ya tenía sesión iniciada en otro dispositivo, se le cierra de inmediato al entrar en auditoría */
    public function test_active_user_session_is_terminated_when_audit_begins(): void
    {
        $sesion = AuditoriaSesion::create([
            'token_sesion'        => 'token_test_audit_term_' . rand(1000, 9999),
            'id_super_admin'      => $this->superAdmin->id,
            'id_usuario_auditado' => $this->vendedor->id,
            'justificacion'       => 'Auditoría en curso de prueba para expulsión',
            'solo_lectura'        => true,
            'estado'              => 'activa',
            'iniciada_en'         => now(),
            'expira_en'           => now()->addMinutes(15),
        ]);

        // Simular que el usuario ya estaba navegando con su sesión ordinaria (sin auditoria_token)
        $response = $this->actingAs($this->vendedor)->get(route('home'));

        // El middleware debe desconectarlo y redirigirlo al login con mensaje explicativo
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('swal_error');
        $this->assertGuest();

        $sesion->delete();
    }
}
