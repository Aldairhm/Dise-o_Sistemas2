<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Categorías — AXStore</title>
    <style>
        @page {
            margin: 25px 30px 40px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #334155;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* Header Layout */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-img {
            max-height: 48px;
            width: auto;
        }
        .company-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin: 0;
        }
        .company-title span {
            color: #2563eb;
        }
        .company-sub {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .report-title {
            text-align: right;
        }
        .report-title h2 {
            font-size: 13px;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-title p {
            font-size: 9px;
            color: #64748b;
            margin: 2px 0 0 0;
        }

        /* Meta summary box */
        .meta-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .meta-box td {
            padding: 8px 12px;
            vertical-align: top;
            width: 33.33%;
        }
        .meta-label {
            font-size: 8px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .meta-value {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
        }

        /* KPI Cards */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 16px;
        }
        .kpi-card {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
        }
        .kpi-card-blue { border-top: 3px solid #2563eb; }
        .kpi-card-green { border-top: 3px solid #16a34a; }
        .kpi-card-slate { border-top: 3px solid #64748b; }
        .kpi-card-purple { border-top: 3px solid #9333ea; }
        
        .kpi-num {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #0f172a;
        }
        .data-table td {
            padding: 7px 10px;
            font-size: 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-active {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .badge-inactive {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .color-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            vertical-align: middle;
            margin-right: 4px;
            border: 1px solid rgba(0,0,0,0.1);
        }

        /* Footer */
        .pdf-footer {
            position: fixed;
            bottom: -25px;
            left: 0;
            right: 0;
            height: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            font-size: 8px;
            color: #94a3b8;
            width: 100%;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <table style="border-collapse: collapse;">
                    <tr>
                        @if(!empty($logoBase64))
                            <td style="padding-right: 12px;">
                                <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo">
                            </td>
                        @else
                            <td style="padding-right: 12px;">
                                <div style="background-color: #2563eb; color: #ffffff; font-weight: 900; font-size: 16px; width: 40px; height: 40px; line-height: 40px; text-align: center; border-radius: 8px; font-family: sans-serif;">
                                    AX
                                </div>
                            </td>
                        @endif
                        <td>
                            <div class="company-title">AX<span>STORE</span></div>
                            <div class="company-sub">Sistema de Gestión de Inventario</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%;" class="report-title">
                <h2>Reporte de Categorías</h2>
                <p>Generado el {{ $fecha }}</p>
            </td>
        </tr>
    </table>

    <!-- Metadata Grid -->
    <table class="meta-box">
        <tr>
            <td>
                <div class="meta-label">Filtro Aplicado</div>
                <div class="meta-value">
                    @if($estado === 'activas') Categorías Activas
                    @elseif($estado === 'inactivos') Categorías Inactivas
                    @else Todas las Categorías
                    @endif
                </div>
            </td>
            <td>
                <div class="meta-label">Generado por</div>
                <div class="meta-value">{{ $usuario }}</div>
            </td>
            <td>
                <div class="meta-label">Total Registros</div>
                <div class="meta-value">{{ count($categorias) }} categoría(s)</div>
            </td>
        </tr>
    </table>

    <!-- KPI Summary Cards -->
    <table class="kpi-table">
        <tr>
            <td style="width: 25%;">
                <div class="kpi-card kpi-card-blue">
                    <div class="meta-label">Total Registradas</div>
                    <div class="kpi-num">{{ $stats['total'] }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card kpi-card-green">
                    <div class="meta-label">Activas</div>
                    <div class="kpi-num">{{ $stats['activas'] }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card kpi-card-slate">
                    <div class="meta-label">Inactivas</div>
                    <div class="kpi-num">{{ $stats['inactivos'] }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card kpi-card-purple">
                    <div class="meta-label">Total Productos</div>
                    <div class="kpi-num">{{ $totalProductos }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 25%;">Nombre de Categoría</th>
                <th style="width: 35%;">Descripción</th>
                <th style="width: 13%; text-align: center;">Color (Hex)</th>
                <th style="width: 10%; text-align: center;">Estado</th>
                <th style="width: 12%; text-align: right;">Productos</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categorias as $index => $cat)
                <tr>
                    <td style="text-align: center; color: #64748b; font-weight: bold;">{{ $index + 1 }}</td>
                    <td style="font-weight: bold; color: #0f172a;">{{ $cat->nombre }}</td>
                    <td style="color: #475569;">{{ $cat->descripcion ?? 'Sin descripción' }}</td>
                    <td style="text-align: center;">
                        <span class="color-dot" style="background-color: {{ $cat->color ?? '#3b82f6' }};"></span>
                        <span style="font-family: monospace; font-size: 9px; color: #64748b;">{{ strtoupper($cat->color ?? '#3B82F6') }}</span>
                    </td>
                    <td style="text-align: center;">
                        @if($cat->estado)
                            <span class="badge badge-active">Activa</span>
                        @else
                            <span class="badge badge-inactive">Inactiva</span>
                        @endif
                    </td>
                    <td style="text-align: right; font-weight: bold; color: #1e293b;">
                        {{ $cat->productos_count ?? 0 }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #94a3b8;">
                        No se encontraron categorías para este reporte.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="pdf-footer">
        <table class="footer-table">
            <tr>
                <td style="text-align: left;">
                    AXStore System — Documento Oficial de Registro de Categorías.
                </td>
                <td style="text-align: right;">
                    Página <span class="pagenum"></span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
