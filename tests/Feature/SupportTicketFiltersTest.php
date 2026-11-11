<?php

namespace Tests\Feature;

use App\Models\{SoporteTicket, User};
use Illuminate\Support\Facades\{DB, File};
use Tests\TestCase;

class SupportTicketFiltersTest extends TestCase
{
    private User $admin;
    private User $seller;
    private User $super;
    private array $team;
    private array $own;
    private SoporteTicket $escalated;
    private string $compiledViews;
    private string|false $previousCompiledViews;

    protected function setUp(): void
    {
        $this->compiledViews = sys_get_temp_dir().'/axstore-support-tests-'.bin2hex(random_bytes(6));
        mkdir($this->compiledViews, 0700);
        $this->previousCompiledViews = getenv('VIEW_COMPILED_PATH');
        putenv('VIEW_COMPILED_PATH='.$this->compiledViews);
        parent::setUp();
        // Isolated fixtures: never use the database configured in phpunit.xml.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        DB::purge('sqlite');
        config(['view.compiled' => $this->compiledViews]);
        $this->withoutVite();
        foreach (['0001_01_01_000000_create_usuario_table.php', '2026_10_25_000001_create_bitacora_table.php',
            '2026_11_04_130413_create_soporte_tickets_table.php', '2026_11_04_132105_enhance_security_and_hierarchical_support_system.php'] as $file) {
            (require database_path('migrations/'.$file))->up();
        }
        $this->admin = $this->user('admin', 'Admin');
        $this->seller = $this->user('vendedor', 'Seller');
        $this->super = $this->user('super_admin', 'Super');
        $otherAdmin = $this->user('admin', 'Other');
        $this->team = [
            $this->ticket($this->seller, 'Pendiente', 'Ventas', 'Coincidencia'),
            $this->ticket($this->seller, 'Pendiente', 'Compras', 'Coincidencia'),
            $this->ticket($this->seller, 'En proceso', 'Ventas', 'Consulta general'),
            $this->ticket($this->seller, 'Escalada', 'Ventas', 'Coincidencia'),
            $this->ticket($this->seller, 'Resuelta', 'Ventas', 'Coincidencia', 'Urgente'),
            $this->ticket($this->seller, 'Resuelta', 'Compras', 'Consulta general'),
            $this->ticket($this->seller, 'Cancelada', 'Ventas', 'Consulta general'),
        ];
        $this->own = [
            $this->ticket($this->admin, 'Pendiente', 'Ventas', 'Coincidencia', 'Media', 'super_admin'),
            $this->ticket($this->admin, 'Resuelta', 'Compras', 'Consulta general', 'Media', 'super_admin'),
        ];
        $this->ticket($otherAdmin, 'Cancelada', 'Ventas', 'Consulta privada', 'Media', 'super_admin');
        $this->escalated = $this->ticket($this->seller, 'Escalada', 'Ventas', 'Coincidencia', 'Media', 'super_admin');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->compiledViews);
        parent::tearDown();
        putenv($this->previousCompiledViews === false ? 'VIEW_COMPILED_PATH' : 'VIEW_COMPILED_PATH='.$this->previousCompiledViews);
    }

    private function user(string $role, string $name): User
    {
        $id = DB::table('usuario')->insertGetId(['nombre_real' => $name, 'username' => $name.'@test.local',
            'password' => bcrypt('Password123!'), 'rol' => $role, 'estado' => 1]);
        return User::findOrFail($id);
    }

    private function ticket(User $owner, string $state, string $module, string $text, string $priority = 'Media', string $level = 'admin'): SoporteTicket
    {
        $id = DB::table('soporte_tickets')->insertGetId(['codigo_ticket' => 'TK-'.(DB::table('soporte_tickets')->count() + 1),
            'id_usuario' => $owner->id, 'nombre_usuario' => $owner->nombre_real, 'rol_usuario' => $owner->rol,
            'modulo' => $module, 'tipo_consulta' => 'Duda', 'titulo' => $text, 'duda' => $text,
            'estado' => $state, 'prioridad' => $priority, 'nivel_escalamiento' => $level, 'created_at' => now(), 'updated_at' => now()]);
        return SoporteTicket::findOrFail($id);
    }

    private function counts(int $pending = 0, int $processing = 0, int $escalated = 0, int $resolved = 0, int $cancelled = 0): array
    {
        return ['todos' => $pending + $processing + $escalated + $resolved + $cancelled,
            'Pendiente' => $pending, 'En proceso' => $processing, 'Escalada' => $escalated, 'Resuelta' => $resolved, 'Cancelada' => $cancelled];
    }

    private function listing(array $params, array $counts, array $tickets, ?User $user = null, string $route = 'ayuda.tickets.index')
    {
        $response = $this->actingAs($user ?? $this->admin)->getJson(route($route, $params))
            ->assertOk()->assertJsonPath('success', true)->assertJsonPath('conteoEstados', $counts);
        preg_match_all('/#(TK-\d+)/', $response->json('html'), $matches);
        $actual = array_values(array_unique($matches[1]));
        $expected = array_map(fn ($ticket) => $ticket->codigo_ticket, $tickets);
        sort($actual);
        sort($expected);
        $this->assertSame($expected, $actual, 'The rendered tickets must match the selected filters.');
        return $response;
    }

    public function test_each_state_tab_matches_its_count_and_keeps_other_state_totals(): void
    {
        $counts = $this->counts(2, 1, 1, 2, 1);
        $this->listing([], $counts, $this->team)->assertJsonPath('misDudasCount', 2);
        foreach (SoporteTicket::ESTADOS as $state) {
            $tickets = array_values(array_filter($this->team, fn ($ticket) => $ticket->estado === $state));
            $this->listing(['estado' => $state], $counts, $tickets);
        }
    }

    public function test_search_module_and_priority_apply_to_both_counts_and_rows(): void
    {
        $filters = ['q' => 'Coincidencia', 'modulo' => 'Ventas'];
        $this->listing($filters, $this->counts(1, 0, 1, 1), [$this->team[0], $this->team[3], $this->team[4]]);
        $this->listing($filters + ['estado' => 'Resuelta'], $this->counts(1, 0, 1, 1), [$this->team[4]]);
        $this->listing($filters + ['prioridad' => 'Urgente'], $this->counts(0, 0, 0, 1), [$this->team[4]]);
        $this->listing(['q' => 'inexistente'], $this->counts(), []);
    }

    public function test_personal_inbox_counts_never_include_team_or_other_admin_tickets(): void
    {
        $this->listing(['mis_dudas' => 1], $this->counts(1, 0, 0, 1), $this->own);
        $this->listing(['mis_dudas' => 1, 'modulo' => 'Ventas'], $this->counts(1), [$this->own[0]]);
        $this->listing(['scope' => 'mis_dudas', 'estado' => 'Resuelta'], $this->counts(1, 0, 0, 1), [$this->own[1]]);
    }

    public function test_seller_only_counts_own_tickets_including_escalated_ones(): void
    {
        $this->listing([], $this->counts(2, 1, 2, 2, 1), [...$this->team, $this->escalated], $this->seller);
        $this->listing(['estado' => 'Escalada', 'modulo' => 'Ventas'], $this->counts(1, 1, 2, 1, 1),
            [$this->team[3], $this->escalated], $this->seller);
    }

    public function test_counts_cover_all_pages_and_update_after_persisted_status_changes(): void
    {
        $pending = [];
        for ($i = 0; $i < 17; $i++) {
            $pending[] = $this->ticket($this->seller, 'Pendiente', 'Ventas', 'Paginacion');
        }
        $filters = ['q' => 'Paginacion', 'modulo' => 'Ventas', 'estado' => 'Pendiente'];
        $response = $this->listing($filters + ['page' => 2], $this->counts(17), array_slice($pending, 15));
        $this->assertStringContainsString('q=Paginacion', $response->json('html'));
        $this->assertStringContainsString('estado=Pendiente', $response->json('html'));
        DB::table('soporte_tickets')->where('id', $pending[0]->id)->update(['estado' => 'Resuelta']);
        $this->listing($filters + ['page' => 2], $this->counts(16, 0, 0, 1), [$pending[16]]);
        $this->listing(['q' => 'Paginacion', 'estado' => 'Resuelta'], $this->counts(16, 0, 0, 1), [$pending[0]]);
    }

    public function test_superadmin_search_counts_and_all_statuses_use_the_same_scope(): void
    {
        $this->listing(['search' => 'Coincidencia', 'estado' => 'todos'], $this->counts(1, 0, 2),
            [$this->own[0], $this->team[3], $this->escalated], $this->super, 'superadmin.tickets');
        $this->listing(['search' => 'Coincidencia', 'estado' => 'Escalada'], $this->counts(1, 0, 2),
            [$this->team[3], $this->escalated], $this->super, 'superadmin.tickets');
        $this->listing(['search' => 'inexistente', 'estado' => 'todos'], $this->counts(), [], $this->super, 'superadmin.tickets');
    }
}
