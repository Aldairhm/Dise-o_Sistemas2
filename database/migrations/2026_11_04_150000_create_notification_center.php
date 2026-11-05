<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $sources = ['usuario', 'venta', 'comision_vendedor', 'soporte_tickets', 'soporte_ticket_mensajes', 'auditoria_sesiones', 'devoluciones'];

    public function up(): void
    {
        Schema::create('notification_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_key', 180)->unique();
            $table->string('type', 80);
            $table->string('category', 30)->index();
            $table->string('priority', 20)->index();
            $table->string('title');
            $table->text('message');
            $table->string('resource_type', 40);
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->json('context')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
        Schema::create('notification_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('notification_events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('usuario')->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->unique(['event_id', 'user_id']);
            $table->index(['user_id', 'archived_at', 'read_at', 'id'], 'notification_inbox_index');
        });
        foreach ($this->sources as $source) {
            Schema::table($source, fn (Blueprint $table) => $table->uuid('notification_event_key')->nullable());
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_deliveries');
        Schema::dropIfExists('notification_events');
        foreach ($this->sources as $source) {
            Schema::table($source, fn (Blueprint $table) => $table->dropColumn('notification_event_key'));
        }
    }
};
