<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Columnas de seguridad reforzada en usuario (MFA para SUPER_ADMIN)
        Schema::table('usuario', function (Blueprint $table) {
            if (!Schema::hasColumn('usuario', 'mfa_enabled')) {
                $table->boolean('mfa_enabled')->default(false)->after('token');
            }
            if (!Schema::hasColumn('usuario', 'mfa_secret')) {
                $table->string('mfa_secret', 255)->nullable()->after('mfa_enabled');
            }
            if (!Schema::hasColumn('usuario', 'mfa_backup_codes')) {
                $table->text('mfa_backup_codes')->nullable()->after('mfa_secret');
            }
        });

        // 2. Extender bitácora para registrar trazabilidad de impersonación/auditoría
        Schema::table('bitacora', function (Blueprint $table) {
            if (!Schema::hasColumn('bitacora', 'id_actor_real')) {
                $table->foreignId('id_actor_real')->nullable()->after('id_usuario')->constrained('usuario')->nullOnDelete();
            }
            if (!Schema::hasColumn('bitacora', 'username_actor_real')) {
                $table->string('username_actor_real', 100)->nullable()->after('id_actor_real');
            }
            if (!Schema::hasColumn('bitacora', 'en_modo_auditoria')) {
                $table->boolean('en_modo_auditoria')->default(false)->after('username_actor_real');
            }
            if (!Schema::hasColumn('bitacora', 'correlacion_id')) {
                $table->string('correlacion_id', 64)->nullable()->after('en_modo_auditoria');
            }
        });

        // 3. Tabla de Sesiones de Auditoría Segura (Impersonation)
        if (!Schema::hasTable('auditoria_sesiones')) {
            Schema::create('auditoria_sesiones', function (Blueprint $table) {
                $table->id();
                $table->string('token_sesion', 80)->unique();
                $table->foreignId('id_super_admin')->constrained('usuario')->cascadeOnDelete();
                $table->foreignId('id_usuario_auditado')->constrained('usuario')->cascadeOnDelete();
                $table->string('justificacion', 500);
                $table->boolean('solo_lectura')->default(true);
                $table->string('estado', 20)->default('activa'); // activa, finalizada, revocada, expirada
                $table->string('ip_origen', 45)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->timestamp('iniciada_en')->useCurrent();
                $table->timestamp('expira_en');
                $table->timestamp('finalizada_en')->nullable();
                $table->text('notas_cierre')->nullable();
                $table->timestamps();
            });
        }

        // 4. Extender soporte_tickets para jerarquía completa
        Schema::table('soporte_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('soporte_tickets', 'titulo')) {
                $table->string('titulo', 200)->nullable()->after('codigo_ticket');
            }
            if (!Schema::hasColumn('soporte_tickets', 'prioridad')) {
                $table->string('prioridad', 20)->default('Media')->after('tipo_consulta'); // Baja, Media, Alta, Urgente
            }
            if (!Schema::hasColumn('soporte_tickets', 'id_asignado')) {
                $table->foreignId('id_asignado')->nullable()->after('id_usuario')->constrained('usuario')->nullOnDelete();
            }
            if (!Schema::hasColumn('soporte_tickets', 'nivel_escalamiento')) {
                $table->string('nivel_escalamiento', 30)->default('admin')->after('estado'); // admin, super_admin
            }
            if (!Schema::hasColumn('soporte_tickets', 'escalado_por')) {
                $table->foreignId('escalado_por')->nullable()->after('nivel_escalamiento')->constrained('usuario')->nullOnDelete();
            }
            if (!Schema::hasColumn('soporte_tickets', 'justificacion_escalamiento')) {
                $table->text('justificacion_escalamiento')->nullable()->after('escalado_por');
            }
            if (!Schema::hasColumn('soporte_tickets', 'id_ticket_padre')) {
                $table->foreignId('id_ticket_padre')->nullable()->after('justificacion_escalamiento')->constrained('soporte_tickets')->nullOnDelete();
            }
            if (!Schema::hasColumn('soporte_tickets', 'categoria')) {
                $table->string('categoria', 60)->nullable()->after('modulo');
            }
            if (!Schema::hasColumn('soporte_tickets', 'fecha_cerrado')) {
                $table->timestamp('fecha_cerrado')->nullable()->after('fecha_resolucion');
            }
        });

        // 5. Tabla de Mensajes del Ticket (Conversaciones con visibilidad por rol)
        if (!Schema::hasTable('soporte_ticket_mensajes')) {
            Schema::create('soporte_ticket_mensajes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_ticket')->constrained('soporte_tickets')->cascadeOnDelete();
                $table->foreignId('id_usuario')->constrained('usuario')->cascadeOnDelete();
                $table->string('nombre_usuario', 150);
                $table->string('rol_usuario', 50);
                $table->text('mensaje');
                $table->string('evidencia_url', 500)->nullable();
                $table->string('tipo_mensaje', 30)->default('publico'); // publico, interno_admin (solo visible para admin y super_admin)
                $table->timestamps();
            });
        }

        // 6. Tabla de Historial de Estados (Auditoría de transiciones)
        if (!Schema::hasTable('soporte_ticket_historial')) {
            Schema::create('soporte_ticket_historial', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_ticket')->constrained('soporte_tickets')->cascadeOnDelete();
                $table->foreignId('id_usuario')->nullable()->constrained('usuario')->nullOnDelete();
                $table->string('estado_anterior', 40)->nullable();
                $table->string('estado_nuevo', 40);
                $table->text('motivo')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soporte_ticket_historial');
        Schema::dropIfExists('soporte_ticket_mensajes');
        Schema::dropIfExists('auditoria_sesiones');

        Schema::table('soporte_tickets', function (Blueprint $table) {
            $cols = ['titulo', 'prioridad', 'id_asignado', 'nivel_escalamiento', 'escalado_por', 'justificacion_escalamiento', 'id_ticket_padre', 'categoria', 'fecha_cerrado'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('soporte_tickets', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('bitacora', function (Blueprint $table) {
            $cols = ['id_actor_real', 'username_actor_real', 'en_modo_auditoria', 'correlacion_id'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('bitacora', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('usuario', function (Blueprint $table) {
            $cols = ['mfa_enabled', 'mfa_secret', 'mfa_backup_codes'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('usuario', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
