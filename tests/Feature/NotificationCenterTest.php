<?php

namespace Tests\Feature;

use App\Models\{AuditoriaSesion, ComisionVendedor, Devolucion, MovimientoBodega, NotificationDelivery, NotificationEvent, SoporteTicket, SoporteTicketMensaje, User, Venta};
use App\Observers\NotificationObserver;
use App\Services\{ComisionService, MailService, NotificationService};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    private User $seller;
    private User $other;
    private User $admin;
    private User $super;

    protected function setUp(): void
    {
        parent::setUp();
        // Dedicated in-memory database: never connect to the database named in the legacy phpunit.xml.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        DB::purge('sqlite');
        $this->withoutVite();
        foreach (['0001_01_01_000000_create_usuario_table.php', '2026_09_15_000013_create_venta_table.php', '2026_10_25_000001_create_bitacora_table.php', '2026_11_04_130413_create_soporte_tickets_table.php', '2026_11_04_132105_enhance_security_and_hierarchical_support_system.php'] as $file) {
            (require database_path('migrations/'.$file))->up();
        }
        // SQLite fixture for two legacy tables whose PostgreSQL ALTER CHECK migrations are not portable.
        Schema::create('salida', function (Blueprint $t) {
            $t->id(); $t->text('observaciones')->nullable();
            $t->unsignedBigInteger('id_variante')->nullable(); $t->unsignedInteger('cantidad')->nullable();
            $t->string('estado')->nullable(); $t->date('fecha_entrega')->nullable();
            $t->decimal('costo_extra', 10, 2)->nullable(); $t->decimal('comision_aplicada', 10, 2)->nullable();
        });
        Schema::create('detalleventa', function (Blueprint $t) { $t->id(); $t->unsignedBigInteger('id_venta'); });
        Schema::create('variante', function (Blueprint $t) { $t->id(); $t->string('sku'); $t->unsignedBigInteger('id_producto')->nullable(); });
        Schema::create('compra', fn (Blueprint $t) => $t->id());
        (require database_path('migrations/2026_09_15_000012_create_movimiento_bodega_table.php'))->up();
        Schema::create('comision_vendedor', function (Blueprint $t) {
            $t->id(); $t->foreignId('id_vendedor')->constrained('usuario'); $t->unsignedBigInteger('id_salida')->nullable();
            $t->string('concepto')->nullable(); $t->decimal('monto', 10, 2); $t->decimal('porcentaje', 5, 2)->nullable();
            $t->string('estado')->default('Pendiente'); $t->string('metodo_pago')->nullable(); $t->string('referencia_pago')->nullable();
            $t->string('comprobante_pago')->nullable(); $t->text('notas')->nullable(); $t->unsignedBigInteger('liquidado_por')->nullable();
            $t->timestamp('fecha_liquidacion')->nullable(); $t->timestamp('fecha_registro')->nullable();
        });
        Schema::create('devoluciones', fn (Blueprint $t) => $t->id());
        (require database_path('migrations/2026_11_04_150000_create_notification_center.php'))->up();
        (require database_path('migrations/2026_11_05_000001_add_payment_notice_to_commissions.php'))->up();
        $this->seller = $this->user('vendedor', 'seller');
        $this->other = $this->user('vendedor', 'other');
        $this->admin = $this->user('admin', 'admin');
        $this->super = $this->user('super_admin', 'super');
    }

    private function user(string $role, string $name): User
    {
        return User::create(['nombre_real' => $name, 'username' => $name.'@test.local', 'password' => 'Password123!', 'rol' => $role, 'estado' => 1]);
    }

    private function sale(): Venta
    {
        return Venta::create(['id_usuario' => $this->seller->id, 'total' => 100, 'metodo_pago' => 'Efectivo', 'estado' => 'Pendiente', 'tipo_venta' => 'Envio']);
    }

    private function ticket(User $owner, string $level = 'admin'): SoporteTicket
    {
        return SoporteTicket::create(['codigo_ticket' => 'TK-'.uniqid(), 'id_usuario' => $owner->id, 'nombre_usuario' => $owner->nombre_real,
            'rol_usuario' => $owner->rol, 'modulo' => 'ventas', 'tipo_consulta' => 'Consulta', 'duda' => 'Ayuda con una operación',
            'estado' => 'Pendiente', 'prioridad' => 'Media', 'nivel_escalamiento' => $level]);
    }

    private function feed(User $user)
    {
        return $this->actingAs($user)->getJson(route('notifications.feed'));
    }

    public function test_sale_creation_and_real_transition_target_only_authorized_recipients(): void
    {
        $this->actingAs($this->seller);
        $sale = $this->sale();
        $this->feed($this->seller)->assertOk()->assertJsonPath('unread', 1)
            ->assertJsonPath('items.0.title', 'Venta #'.$sale->id.': Pendiente');
        $this->feed($this->admin)->assertOk()->assertJsonPath('unread', 0);
        $this->feed($this->super)->assertJsonPath('unread', 0);
        $this->feed($this->other)->assertJsonPath('unread', 0);
        $sale->update(['estado' => 'Confirmada']);
        $this->feed($this->seller)->assertJsonPath('unread', 2)->assertJsonPath('items.0.title', 'Venta #'.$sale->id.': Confirmada');
        $sale->update(['estado' => 'Confirmada']);
        $this->assertSame(2, NotificationEvent::count());
    }

    public function test_admin_registering_a_sellers_sale_also_receives_confirmation(): void
    {
        $this->actingAs($this->admin);
        $sale = Venta::create(['id_usuario' => $this->seller->id, 'total' => 100, 'metodo_pago' => 'Efectivo', 'estado' => 'Entregada', 'tipo_venta' => 'Tienda']);
        app(NotificationObserver::class)->created($sale);
        $this->feed($this->admin)->assertOk()->assertJsonPath('unread', 1);
        $this->feed($this->seller)->assertOk()->assertJsonPath('unread', 1);
        $this->feed($this->other)->assertJsonPath('unread', 0);
        $this->feed($this->super)->assertJsonPath('unread', 0);
        $this->assertSame(1, NotificationEvent::where('type', 'venta_creada')->count());
        $this->assertSame(2, NotificationDelivery::count());
    }

    public function test_admin_registering_own_sale_receives_only_one_delivery(): void
    {
        $this->actingAs($this->admin);
        $sale = Venta::create(['id_usuario' => $this->admin->id, 'total' => 100, 'metodo_pago' => 'Efectivo', 'estado' => 'Entregada', 'tipo_venta' => 'Tienda']);
        $sale->save();
        $this->feed($this->admin)->assertOk()->assertJsonPath('unread', 1);
        $this->assertSame(1, NotificationDelivery::count());
        $this->assertSame(1, NotificationEvent::where('type', 'venta_creada')->count());
    }

    public function test_store_sale_notification_is_independent_of_commission_notification(): void
    {
        $this->actingAs($this->admin);
        $sale = Venta::create(['id_usuario' => $this->seller->id, 'total' => 100, 'metodo_pago' => 'Efectivo', 'estado' => 'Entregada', 'tipo_venta' => 'Tienda']);
        $this->saleCommission($sale);
        $sale->update(['estado' => 'Devolución']);
        $this->feed($this->admin)->assertOk()->assertJsonPath('unread', 1)
            ->assertJsonFragment(['title' => 'Venta #'.$sale->id.' realizada']);
        $this->feed($this->seller)->assertOk()->assertJsonPath('unread', 2)
            ->assertJsonFragment(['title' => 'Venta #'.$sale->id.' realizada'])
            ->assertJsonFragment(['category' => 'comisiones']);
    }

    private function saleCommission(Venta $sale): ComisionVendedor
    {
        $outbound = DB::table('salida')->insertGetId(['observaciones' => "Venta #{$sale->id}", 'id_variante' => 1, 'cantidad' => 2]);
        return ComisionVendedor::create(['id_vendedor' => $sale->id_usuario, 'id_salida' => $outbound, 'monto' => 15, 'estado' => 'Pendiente']);
    }

    public function test_shipping_notifies_admin_only_on_delivery_and_seller_on_each_transition(): void
    {
        $this->actingAs($this->admin);
        $sale = $this->sale();
        $this->assertSame(0, ComisionVendedor::count());
        $this->feed($this->admin)->assertOk()->assertJsonPath('unread', 0);
        $expected = 1;
        foreach (['Confirmada', 'En ruta', 'Entregada'] as $state) {
            $this->actingAs($this->admin);
            $sale->update(['estado' => $state]);
            $sale->update(['estado' => $state]);
            $expected++;
            $this->feed($this->admin)->assertOk()->assertJsonPath('unread', $state === 'Entregada' ? 1 : 0);
            $this->feed($this->seller)->assertJsonPath('unread', $expected)
                ->assertJsonPath('items.0.title', 'Venta #'.$sale->id.': '.$state);
        }
        $delivery = $this->feed($this->admin)->assertJsonPath('items.0.title', 'Venta #'.$sale->id.': Entregada')
            ->json('items.0.id');
        $this->get(route('notifications.open', $delivery))->assertRedirect(route('ventas.show', $sale->id));
        $this->feed($this->other)->assertJsonPath('unread', 0);
        $this->feed($this->super)->assertJsonPath('unread', 0);
        $this->assertSame(4, NotificationEvent::where('category', 'ventas')->count());
        $this->assertSame(5, NotificationDelivery::count());
    }

    public function test_commission_payment_is_emitted_only_after_actual_payment_and_is_idempotent(): void
    {
        $commission = $this->saleCommission($this->sale());
        $commission->update(['monto' => 9]);
        $this->feed($this->seller)->assertJsonPath('items.0.title', 'Comisión actualizada')
            ->assertSee('Monto anterior: $15.00', false);
        $this->assertSame(0, NotificationEvent::where('type', 'comision_pagada')->count());
        $commission->update(['estado' => 'Pagada', 'fecha_liquidacion' => now()]);
        app(NotificationObserver::class)->updated($commission);
        $commission->update(['estado' => 'Pagada']);
        $this->assertSame(1, NotificationEvent::where('type', 'comision_pagada')->count());
        $this->feed($this->other)->assertJsonPath('unread', 0);
    }

    public function test_payable_notices_group_bonuses_and_commissions_without_reminding_again(): void
    {
        $sale = $this->sale();
        $sale->update(['estado' => 'Entregada', 'fecha_entrega' => now()]);
        $commission = $this->saleCommission($sale);
        $bonus = ComisionVendedor::create(['id_vendedor' => $this->seller->id, 'monto' => 20, 'estado' => 'Pendiente', 'concepto' => 'Meta mensual']);
        $this->feed($this->seller)->assertJsonPath('items.0.title', 'Bono registrado');
        $this->feed($this->admin)->assertJsonPath('items.0.title', 'Bono pendiente de pago');
        $this->artisan('notifications:payable-commissions')->assertExitCode(0);
        $this->assertNull($commission->fresh()->aviso_liquidacion_at);
        $this->assertNotNull($bonus->fresh()->aviso_liquidacion_at);
        $this->travel(7)->days();
        $second = $this->saleCommission($sale);
        $this->artisan('notifications:payable-commissions')->assertExitCode(0);
        $this->artisan('notifications:payable-commissions')->assertExitCode(0);
        $this->assertNotNull($commission->fresh()->aviso_liquidacion_at);
        $this->assertNotNull($second->fresh()->aviso_liquidacion_at);
        $this->assertSame(2, NotificationEvent::where('type', 'liquidacion_pendiente')->count());
        $this->feed($this->admin)->assertJsonPath('items.0.message', 'seller tiene 2 comisión(es) y 0 bono(s) que ya se pueden liquidar. Subtotal de estos registros: $30.00.');
        $notice = NotificationDelivery::whereHas('event', fn ($query) => $query->where('type', 'liquidacion_pendiente'))->latest('id')->firstOrFail();
        $this->get(route('notifications.open', $notice))->assertRedirect(route('comisiones.index', ['vendedor_id' => $this->seller->id, 'estado' => 'Pendiente']));
        $this->actingAs($this->seller)->get(route('notifications.open', $notice))->assertNotFound();
    }

    public function test_recording_bonus_from_commissions_notifies_admin_immediately(): void
    {
        $this->actingAs($this->admin)->post(route('comisiones.store'), [
            'id_vendedor' => $this->seller->id, 'concepto' => 'Meta mensual', 'monto' => 20,
        ])->assertRedirect(route('comisiones.porVendedor'));

        $this->feed($this->admin)->assertJsonPath('unread', 1)
            ->assertJsonPath('items.0.title', 'Bono pendiente de pago')
            ->assertJsonPath('items.0.message', 'seller tiene un bono de $20.00 pendiente de pago. Concepto: Meta mensual.');
        $this->feed($this->seller)->assertJsonPath('items.0.title', 'Bono registrado');
        $this->feed($this->other)->assertJsonPath('unread', 0);

        $this->artisan('notifications:payable-commissions')->assertExitCode(0);
        $this->feed($this->admin)->assertJsonPath('unread', 1);
    }

    public function test_delivered_shipment_notifies_admin_of_commission_and_when_it_becomes_payable(): void
    {
        $this->travelTo(now()->setDate(2026, 3, 2)->startOfDay());
        $sale = $this->sale();
        $sale->update(['estado' => 'En ruta']);
        foreach ([5, 7, 0] as $amount) {
            DB::table('salida')->insert([
                'observaciones' => "Venta #{$sale->id}", 'cantidad' => 1,
                'costo_extra' => 7, 'comision_aplicada' => $amount,
            ]);
        }

        $route = route('ventas.actualizar-estado', $sale->id);
        $this->actingAs($this->admin)->patchJson($route, ['estado' => 'Entregada'])->assertOk();
        $this->patchJson($route, ['estado' => 'Entregada'])->assertUnprocessable();
        $notice = $this->feed($this->admin)->assertJsonPath('unread', 2)
            ->assertJsonPath('items.0.title', 'Comisión por envío entregado')
            ->assertJsonPath('items.0.message', "La entrega de la venta #{$sale->id} generó \$12.00 en comisiones para seller. Se podrá liquidar al vencer la garantía del 08/03/2026 a las 23:59.")
            ->json('items.0.id');
        $this->get(route('notifications.open', $notice))->assertRedirect(route('comisiones.index', ['vendedor_id' => $this->seller->id, 'estado' => 'Pendiente']));

        $this->artisan('notifications:payable-commissions')->assertExitCode(0);
        $this->assertSame(0, NotificationEvent::where('type', 'liquidacion_pendiente')->count());
        $this->travel(7)->days();
        $this->artisan('notifications:payable-commissions')->assertExitCode(0);
        $this->artisan('notifications:payable-commissions')->assertExitCode(0);
        $this->feed($this->admin)->assertJsonPath('unread', 3)->assertJsonPath('items.0.title', 'Pagos pendientes de liquidación');
        $this->assertSame(1, NotificationEvent::where('type', 'liquidacion_pendiente')->count());
    }

    public function test_return_adjusts_and_notifies_only_the_commission_of_the_matching_sale(): void
    {
        $sale = $this->sale();
        $otherSale = $sale->replicate()->forceFill(['id' => (int) ($sale->id.'0'), 'id_usuario' => $this->other->id]);
        $otherSale->save();
        $commission = $this->saleCommission($sale);
        $unrelated = $this->saleCommission($otherSale);
        DB::table('salida')->where('id', $commission->id_salida)->update(['observaciones' => "Venta #{$sale->id} (Costo extra)"]);

        app(ComisionService::class)->procesarResolucionDevolucion(
            (new Devolucion)->forceFill(['id' => 1]), $sale, 'reembolso_tienda', [['id_variante' => 1, 'cantidad' => 1]]
        );

        $this->assertSame('7.50', $commission->fresh()->monto);
        $this->assertSame('15.00', $unrelated->fresh()->monto);
        $this->feed($this->seller)->assertJsonPath('items.0.title', 'Comisión actualizada');
        $this->feed($this->other)->assertJsonPath('unread', 2)->assertJsonMissing(['title' => 'Comisión actualizada']);
    }

    public function test_kardex_alert_is_for_admin_and_links_to_the_recorded_movement(): void
    {
        $variantId = DB::table('variante')->insertGetId(['sku' => 'QA-01']);
        $movement = MovimientoBodega::create([
            'id_variante' => $variantId, 'id_usuario' => $this->admin->id,
            'tipo' => 'transferencia_tienda', 'cantidad' => 2,
            'reserva_anterior' => 8, 'reserva_nueva' => 6, 'stock_anterior' => 1, 'stock_nuevo' => 3,
        ]);
        app(NotificationObserver::class)->created($movement);
        $this->feed($this->seller)->assertJsonPath('unread', 0);
        $this->feed($this->admin)->assertJsonPath('unread', 1)
            ->assertJsonPath('items.0.category', 'inventario')
            ->assertJsonPath('items.0.message', "Variante #{$variantId} (QA-01): 2 unidad(es). Bodega 8 → 6. Tienda 1 → 3.");
        $this->get(route('notifications.index', ['category' => 'inventario']))->assertOk()->assertSee('Transferencia de bodega a tienda');
        $notice = NotificationDelivery::firstOrFail();
        $this->get(route('notifications.open', $notice))->assertRedirect(route('compras.movimientos', ['movimiento' => $movement->id]));
        $this->admin->update(['rol' => 'vendedor']);
        $this->feed($this->admin)->assertJsonMissing(['category' => 'inventario']);
        $this->get(route('notifications.open', $notice))->assertNotFound();
    }

    public function test_saving_profile_notifies_only_its_owner_when_values_change(): void
    {
        foreach ([$this->seller, $this->admin] as $owner) {
            $values = ['nombre_real' => 'Nombre actualizado', 'telefono' => '7890-1234', 'username' => $owner->username];
            $this->actingAs($owner)->putJson(route('perfil.update'), $values)->assertOk();
            $this->putJson(route('perfil.update'), $values)->assertOk();
            $this->feed($owner)->assertJsonPath('unread', 1)->assertJsonPath('items.0.title', 'Cambios en tu perfil')
                ->assertJsonMissing(['telefono' => '7890-1234'])->assertDontSee('7890-1234');
        }
        $this->feed($this->other)->assertJsonPath('unread', 0);
    }

    public function test_rollback_removes_business_changes_and_notifications(): void
    {
        DB::beginTransaction();
        $this->sale();
        DB::rollBack();
        $this->assertSame(0, Venta::count());
        $this->assertSame(0, NotificationEvent::count());
        $this->assertSame(0, NotificationDelivery::count());
    }

    public function test_ticket_hierarchy_public_replies_and_internal_privacy(): void
    {
        $this->actingAs($this->seller);
        $ticket = $this->ticket($this->seller);
        $this->feed($this->admin)->assertJsonPath('unread', 1);
        $this->feed($this->seller)->assertJsonPath('unread', 1);
        SoporteTicketMensaje::create(['id_ticket' => $ticket->id, 'id_usuario' => $this->seller->id, 'nombre_usuario' => 'seller', 'rol_usuario' => 'vendedor', 'mensaje' => 'Inicial', 'tipo_mensaje' => 'publico']);
        $this->actingAs($this->admin)->post(route('ayuda.tickets.responder', $ticket), ['mensaje' => 'Respuesta autorizada'])->assertRedirect();
        $this->feed($this->seller)->assertOk()->assertJsonFragment(['title' => 'Nueva respuesta #'.$ticket->codigo_ticket]);
        $before = NotificationDelivery::where('user_id', $this->seller->id)->count();
        SoporteTicketMensaje::create(['id_ticket' => $ticket->id, 'id_usuario' => $this->admin->id, 'nombre_usuario' => 'admin', 'rol_usuario' => 'admin', 'mensaje' => 'Privado', 'tipo_mensaje' => 'interno_admin']);
        $this->assertSame($before, NotificationDelivery::where('user_id', $this->seller->id)->count());
        $adminTicket = $this->ticket($this->admin, 'super_admin');
        $this->feed($this->super)->assertJsonFragment(['title' => 'Nueva solicitud #'.$adminTicket->codigo_ticket]);
        $this->feed($this->other)->assertJsonPath('unread', 0);
    }

    public function test_escalation_assignment_priority_and_resolution_emit_correctly(): void
    {
        $ticket = $this->ticket($this->seller);
        $this->actingAs($this->admin)->post(route('ayuda.tickets.escalar', $ticket), ['justificacion' => 'Requiere revisión de dirección'])->assertRedirect();
        $this->feed($this->super)->assertJsonFragment(['title' => 'Solicitud actualizada #'.$ticket->codigo_ticket]);
        $this->actingAs($this->super)->patchJson(route('ayuda.tickets.estado', $ticket), ['estado' => 'Resuelta', 'resolucion' => 'Revisión completada'])->assertOk();
        $this->feed($this->seller)->assertJsonFragment(['message' => 'Solicitud #'.$ticket->codigo_ticket.'. Estado: Resuelta. Prioridad: Media.']);
    }

    public function test_cannot_read_archive_open_or_list_another_users_notifications(): void
    {
        $this->sale();
        $item = NotificationDelivery::where('user_id', $this->seller->id)->firstOrFail();
        $this->actingAs($this->other)->postJson(route('notifications.read', $item->id))->assertNotFound();
        $this->postJson(route('notifications.archive', $item->id))->assertNotFound();
        $this->get(route('notifications.open', $item->id))->assertNotFound();
        $this->feed($this->other)->assertJsonPath('items', []);
    }

    public function test_read_all_archive_search_filters_pagination_and_source_are_independent(): void
    {
        $sale = $this->sale();
        $item = NotificationDelivery::where('user_id', $this->seller->id)->firstOrFail();
        $this->actingAs($this->seller)->postJson(route('notifications.read', $item->id))->assertOk();
        $this->feed($this->seller)->assertJsonPath('unread', 0);
        $this->assertSame('Pendiente', $sale->fresh()->estado);
        $this->get(route('notifications.index', ['category' => 'ventas', 'status' => 'read', 'q' => 'Venta']))->assertOk()->assertSee('Centro de Notificaciones');
        $this->post(route('notifications.archive', $item->id))->assertRedirect();
        $this->feed($this->seller)->assertJsonPath('items', []);
        $this->get(route('notifications.index', ['status' => 'archived']))->assertSee('Venta #'.$sale->id);
        $sale->update(['estado' => 'Confirmada']);
        $this->postJson(route('notifications.read-all'))->assertOk();
        $this->feed($this->seller)->assertJsonPath('unread', 0);
    }

    public function test_event_key_deduplication_persists_across_retries(): void
    {
        $service = app(NotificationService::class);
        for ($i = 0; $i < 3; $i++) {
            $service->publish('stable-event', 'example', 'seguridad', 'alta', 'Aviso', 'Aviso de cuenta', 'cuenta', $this->seller->id, [$this->seller->id, $this->seller->id]);
        }
        $this->assertSame(1, NotificationEvent::count());
        $this->assertSame(1, NotificationDelivery::count());
    }

    public function test_audit_start_end_and_revocation_are_private_and_persistent(): void
    {
        $audit = AuditoriaSesion::create(['token_sesion' => str_repeat('t',64), 'id_super_admin' => $this->super->id, 'id_usuario_auditado' => $this->seller->id,
            'justificacion' => 'Revisión autorizada', 'solo_lectura' => true, 'estado' => 'activa', 'iniciada_en' => now(), 'expira_en' => now()->addMinutes(15)]);
        $this->feed($this->seller)->assertJsonFragment(['title' => 'Auditoría administrativa de cuenta']);
        $this->actingAs($this->seller)->withSession(['auditoria_token' => $audit->token_sesion, 'auditoria_actor_real_id' => $this->super->id])->getJson(route('notifications.feed'))->assertForbidden();
        session()->forget(['auditoria_token','auditoria_actor_real_id']);
        $audit->update(['estado' => 'revocada']);
        $this->feed($this->super)->assertJsonFragment(['title' => 'Auditoría #'.$audit->id.': revocada']);
        $this->feed($this->seller)->assertJsonFragment(['message' => 'La revisión administrativa de tu cuenta está revocada.']);
    }

    public function test_disabled_or_unauthenticated_session_cannot_poll(): void
    {
        $this->getJson(route('notifications.feed'))->assertUnauthorized();
        $this->seller->update(['estado' => 0]);
        $this->feed($this->seller)->assertForbidden();
    }

    public function test_links_recheck_permissions_and_missing_resources(): void
    {
        $sale = $this->sale();
        $item = NotificationDelivery::where('user_id', $this->seller->id)->firstOrFail();
        $this->actingAs($this->seller)->get(route('notifications.open', $item->id))->assertRedirect(route('ventas.show', $sale->id));
        $sale->update(['id_usuario' => $this->other->id]);
        $this->get(route('notifications.open', $item->id))->assertRedirect(route('notifications.index'));
        $sale->delete();
        $this->get(route('notifications.open', $item->id))->assertRedirect(route('notifications.index'));
    }

    public function test_private_administrative_ticket_cannot_be_answered_or_changed_by_other_admin(): void
    {
        $ticket = $this->ticket($this->admin, 'super_admin');
        $otherAdmin = $this->user('admin', 'other-admin');
        $this->actingAs($otherAdmin)->post(route('ayuda.tickets.responder', $ticket), ['mensaje' => 'No autorizado'])->assertForbidden();
        $this->patchJson(route('ayuda.tickets.estado', $ticket), ['estado' => 'Resuelta'])->assertForbidden();
        $this->post(route('ayuda.tickets.escalar', $ticket), ['justificacion' => 'Sin acceso a este ticket'])->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_real_commission_service_confirms_payment_once_without_sending_external_mail(): void
    {
        \Mockery::mock('alias:App\Services\MailService')->shouldReceive('sendCommissionPaid')->once();
        $commission = ComisionVendedor::create(['id_vendedor' => $this->seller->id, 'monto' => 20, 'estado' => 'Pendiente']);
        $this->actingAs($this->admin);
        $service = app(ComisionService::class);
        $this->assertSame(1, $service->liquidarComisiones($this->seller->id, [$commission->id]));
        $this->assertSame('Pagada', $commission->fresh()->estado);
        $this->assertSame(0, $service->liquidarComisiones($this->seller->id, [$commission->id]));
        $this->assertSame(1, NotificationEvent::where('type', 'bono_liquidado')->count());
    }

    public function test_expired_audits_notify_once_even_without_an_open_session(): void
    {
        $audit = AuditoriaSesion::create(['token_sesion' => str_repeat('e',64), 'id_super_admin' => $this->super->id, 'id_usuario_auditado' => $this->seller->id,
            'justificacion' => 'Revisión autorizada', 'solo_lectura' => true, 'estado' => 'activa', 'iniciada_en' => now()->subHour(), 'expira_en' => now()->subMinute()]);
        $this->artisan('notifications:expire-audits')->assertExitCode(0);
        $this->artisan('notifications:expire-audits')->assertExitCode(0);
        $this->assertSame('expirada', $audit->fresh()->estado);
        $this->assertSame(1, NotificationEvent::where('type', 'auditoria_expirada')->count());
    }

    public function test_backup_failures_are_grouped_and_do_not_reach_sellers_or_admins(): void
    {
        app(NotificationService::class)->backupFailed();
        app(NotificationService::class)->backupFailed();
        $this->feed($this->super)->assertJsonPath('unread', 1)->assertJsonPath('items.0.priority', 'critica');
        $this->feed($this->admin)->assertJsonPath('unread', 0);
        $this->feed($this->seller)->assertJsonPath('unread', 0);
    }

    public function test_priority_filters_pagination_and_multiple_sessions_share_persistent_read_state(): void
    {
        $service = app(NotificationService::class);
        for ($i = 0; $i < 25; $i++) {
            $service->publish('pagination-'.$i, 'cuenta', 'seguridad', 'alta', 'Aviso '.$i, 'Mensaje', 'cuenta', $this->seller->id, [$this->seller->id]);
        }
        $this->feed($this->seller)->assertJsonPath('unread', 25)->assertJsonCount(8, 'items');
        $response = $this->get(route('notifications.index', ['priority' => 'alta']));
        $response->assertOk();
        $this->assertCount(20, $response->viewData('notifications')->items());
        $this->assertSame(25, $response->viewData('notifications')->total());
        $this->app['auth']->forgetGuards();
        $this->feed($this->seller)->assertJsonPath('unread', 25);
        $this->postJson(route('notifications.read-all'))->assertOk();
        $this->app['auth']->forgetGuards();
        $this->feed($this->seller)->assertJsonPath('unread', 0);
    }

    public function test_repeated_login_failures_alert_only_at_the_existing_threshold(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.post'), ['username' => $this->seller->username, 'password' => 'IncorrectPassword!'])->assertRedirect();
        }
        $this->assertSame(1, NotificationEvent::where('type', 'acceso_bloqueado')->count());
        $this->feed($this->super)->assertJsonPath('unread', 1);
        $this->feed($this->other)->assertJsonPath('unread', 0);
    }
}
