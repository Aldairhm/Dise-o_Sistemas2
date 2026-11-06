<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Oficial de Auditoría #AUD-{{ str_pad($sesion->id, 4, '0', STR_PAD_LEFT) }} — AXStore</title>
    <style>
        @page {
            margin: 25px 30px 105px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #334155;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .company-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .company-title span {
            color: #2563eb;
        }
        .company-sub {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .meta-box {
            text-align: right;
            font-size: 9px;
            color: #475569;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 14px 0 6px 0;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .info-grid td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            font-size: 9px;
        }
        .info-label {
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            font-size: 8px;
            display: block;
            margin-bottom: 2px;
        }

        .justificacion-box {
            background-color: #fefce8;
            border: 1px solid #fef08a;
            padding: 8px 10px;
            border-radius: 4px;
            margin-bottom: 12px;
            font-size: 9.5px;
            color: #713f12;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 8.5px;
        }
        .table-data th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 6px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .table-data td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-purple { background: #f3e8ff; color: #6b21a8; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-slate { background: #f1f5f9; color: #334155; }

        /* Pie de página fijo con firmas institucionales */
        .pdf-footer-fixed {
            position: fixed;
            bottom: -90px;
            left: 0;
            right: 0;
            height: 90px;
            width: 100%;
        }

        .footer-signatures {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .footer-signatures td {
            width: 50%;
            padding: 0 15px;
            vertical-align: top;
            font-size: 8px;
            color: #475569;
        }
        .sign-line {
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
        }

        .doc-watermark {
            width: 100%;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            font-size: 7.5px;
            color: #94a3b8;
        }
        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>
<body>

    <!-- Encabezado Oficial -->
    <table class="header-table">
        <tr>
            <td>
                <div class="company-title">AX<span>STORE</span></div>
                <div class="company-sub">Informe Oficial de Auditoría y Supervisión de Cuentas</div>
            </td>
            <td class="meta-box">
                <strong>Folio:</strong> #AUD-{{ str_pad($sesion->id, 4, '0', STR_PAD_LEFT) }}<br>
                <strong>Fecha Emisión:</strong> {{ now()->format('d/m/Y h:i A') }}<br>
                <strong>Estado:</strong> {{ strtoupper($sesion->estado) }}
            </td>
        </tr>
    </table>

    <!-- 1. El Porqué y Actores -->
    <div class="section-title">1. Resumen Ejecutivo y Justificación de la Auditoría</div>
    <table class="info-grid">
        <tr>
            <td width="50%">
                <span class="info-label">Super Administrador Auditor:</span>
                <strong>{{ $sesion->superAdmin->nombre_real ?? 'Super Administrador' }}</strong><br>
                {{ $sesion->superAdmin->username ?? '—' }} (Rol: {{ strtoupper($sesion->superAdmin->rol ?? 'super_admin') }})
            </td>
            <td width="50%">
                <span class="info-label">Cuenta Auditada / Supervisada:</span>
                <strong>{{ $sesion->usuarioAuditado->nombre_real ?? 'Usuario' }}</strong><br>
                {{ $sesion->usuarioAuditado->username ?? '—' }} (Rol: {{ strtoupper($sesion->usuarioAuditado->rol ?? 'usuario') }})
            </td>
        </tr>
        <tr>
            <td>
                <span class="info-label">Modalidad y Restricción:</span>
                <strong>{{ $sesion->solo_lectura ? 'SOLO LECTURA (Sin mutaciones)' : 'PERMISIVO (Modificaciones permitidas)' }}</strong>
            </td>
            <td>
                <span class="info-label">Intervalo de Duración:</span>
                <strong>{{ $duracionRealMinutos }} Minutos</strong> 
                ({{ $sesion->iniciada_en->format('d/m/Y H:i') }} &rarr; {{ $sesion->finalizada_en ? $sesion->finalizada_en->format('H:i') : 'Activa' }})
            </td>
        </tr>
    </table>

    <div class="justificacion-box">
        <strong>EL PORQUÉ DE LA AUDITORÍA (MOTIVO REGISTRADO):</strong><br>
        {{ $sesion->justificacion }}
    </div>

    @if($sesion->notas_cierre)
        <div style="background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 6px 10px; border-radius: 4px; margin-bottom: 12px; font-size: 9px;">
            <strong>DICTAMEN / NOTAS DE CIERRE:</strong> {{ $sesion->notas_cierre }}
        </div>
    @endif

    <!-- 2. Qué se revisó -->
    <div class="section-title">2. ¿Qué se revisó? (Alcance y Módulos Inspeccionados)</div>
    <div style="margin-bottom: 8px; font-size: 9px;">
        <strong>Módulos consultados:</strong> 
        {{ $modulosRevisados->join(' • ') ?: 'Navegación general del sistema' }}
        <br>
        <strong>Total de acciones registradas:</strong> {{ $accionesTotales }} eventos auditables en bitácora forense.
    </div>

    <!-- 3. Cronograma Detallado -->
    <div class="section-title">3. Cronograma Detallado de Actividades Realizadas</div>
    <table class="table-data">
        <thead>
            <tr>
                <th width="10%">Hora</th>
                <th width="15%">Acción</th>
                <th width="20%">Módulo</th>
                <th width="45%">Detalle de lo que se hizo</th>
                <th width="10%">IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($eventos as $ev)
                <tr>
                    <td style="font-family: monospace;">{{ $ev->created_at->format('H:i:s') }}</td>
                    <td>
                        <span class="badge {{ $ev->accion === 'INICIO_AUDITORIA' ? 'badge-purple' : ($ev->accion === 'FIN_AUDITORIA' ? 'badge-green' : ($ev->accion === 'REVISION_VISTA' ? 'badge-blue' : 'badge-slate')) }}">
                            {{ $ev->accion }}
                        </span>
                    </td>
                    <td><strong>{{ $ev->modulo }}</strong></td>
                    <td>
                        {{ $ev->descripcion }}
                    </td>
                    <td style="font-family: monospace; font-size: 7.5px;">{{ $ev->ip_address ?? '127.0.0.1' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8; padding: 10px;">
                        No se registraron eventos adicionales en esta sesión de auditoría.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pie de Página Fijo con Firmas Institucionales y Paginación -->
    <div class="pdf-footer-fixed">
        <table class="footer-signatures">
            <tr>
                <td>
                    <div class="sign-line">
                        <strong>{{ $sesion->superAdmin->nombre_real ?? 'Super Administrador' }}</strong><br>
                        Super Administrador Auditor · ID #{{ $sesion->id_super_admin }}<br>
                        Validado mediante autenticación MFA y sesión segura
                    </div>
                </td>
                <td style="text-align: right;">
                    <div class="sign-line">
                        <strong>Gobernanza y Auditoría AXStore</strong><br>
                        Certificado de Integridad y Trazabilidad Forense<br>
                        Hash: {{ substr(sha1($sesion->token_sesion . $sesion->id), 0, 24) }}
                    </div>
                </td>
            </tr>
        </table>
        <table class="doc-watermark">
            <tr>
                <td style="text-align: left;">
                    AXStore System — Documento Oficial de Auditoría #AUD-{{ str_pad($sesion->id, 4, '0', STR_PAD_LEFT) }}.
                </td>
                <td style="text-align: right;">
                    Página <span class="pagenum"></span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

