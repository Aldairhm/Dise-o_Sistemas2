<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isSuccess ? 'Respaldo Generado Exitosamente' : 'Fallo en Respaldo de Base de Datos' }} — AXStore</title>
    <style>
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 15px 8px !important;
            }

            .email-card {
                border-radius: 18px !important;
            }

            .email-header {
                padding: 28px 16px 24px 16px !important;
            }

            .email-body {
                padding: 24px 16px !important;
            }

            .email-footer {
                padding: 22px 14px !important;
            }

            .email-title {
                font-size: 20px !important;
            }
        }
    </style>
</head>

<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">

    <!-- Contenedor Principal Centrado -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-wrapper"
        style="background-color: #f8fafc; padding: 30px 15px;">
        <tr>
            <td align="center">

                <!-- Tarjeta Central del Correo -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-card"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07); border: 1px solid #e2e8f0;">

                    <!-- ─── HEADER CON DEGRADADO CORPORATIVO ─── -->
                    <tr>
                        <td align="center" class="email-header"
                            style="background: {{ $isSuccess ? 'linear-gradient(135deg, #0f172a 0%, #065f46 50%, #059669 100%)' : 'linear-gradient(135deg, #0f172a 0%, #9f1239 50%, #e11d48 100%)' }}; padding: 38px 25px 32px 25px;">
                            <!-- Logo / Badge AXStore -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center"
                                        style="background-color: #ffffff; padding: 8px 18px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                        <span
                                            style="font-size: 22px; font-weight: 900; color: #0f172a; letter-spacing: -0.02em; font-family: 'Segoe UI', Arial, sans-serif;">
                                            AX<span style="color: {{ $isSuccess ? '#059669' : '#e11d48' }};">Store</span>
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 14px 0 0 0; color: #f1f5f9; font-size: 15px; font-weight: 700; letter-spacing: 0.02em; text-transform: uppercase;">
                                {{ $isSuccess ? '💾 Notificación — Respaldo de Base de Datos' : '⚠️ Alerta — Error en Respaldo de Base de Datos' }}
                            </p>
                        </td>
                    </tr>

                    <!-- ─── CUERPO DEL CORREO ─── -->
                    <tr>
                        <td class="email-body" style="padding: 32px 32px 24px 32px; background-color: #ffffff;">

                            <!-- Estado / Badge -->
                            <div style="margin-bottom: 20px;">
                                @if($isSuccess)
                                    <span style="display: inline-block; background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">
                                        ✓ Respaldo Completado Exitosamente
                                    </span>
                                @else
                                    <span style="display: inline-block; background-color: #fff1f2; color: #9f1239; border: 1px solid #fecdd3; padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">
                                        ✕ Error al Generar el Respaldo
                                    </span>
                                @endif
                            </div>

                            <!-- Saludo / Título -->
                            <h2 class="email-title"
                                style="margin: 0 0 14px 0; color: #0f172a; font-size: 22px; font-weight: 900; letter-spacing: -0.01em;">
                                Estimado(a) Administrador(a),
                            </h2>

                            <!-- Mensaje explicativo -->
                            <p style="margin: 0 0 24px 0; color: #475569; font-size: 14px; line-height: 1.65;">
                                @if($isSuccess)
                                    Te informamos que se ha generado un <strong>nuevo respaldo de la base de datos MySQL</strong> en el sistema AXStore. La información ha sido empaquetada de manera segura y verificada.
                                @else
                                    Se produjo un <strong>error durante la ejecución del respaldo de la base de datos MySQL</strong> en el sistema AXStore. A continuación se detallan las incidencias registradas.
                                @endif
                            </p>

                            <!-- Tabla con Detalles de la Operación -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="background-color: #f8fafc; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 24px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; width: 38%;">
                                        Archivo Generado:
                                    </td>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #0f172a; font-family: monospace; font-weight: 700;">
                                        {{ $nombreArchivo }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase;">
                                        Tamaño del Archivo:
                                    </td>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #0f172a; font-weight: 600;">
                                        <span style="background-color: #eff6ff; color: #1d4ed8; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 800;">
                                            {{ $tamano }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase;">
                                        Fecha y Hora:
                                    </td>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #0f172a; font-weight: 600;">
                                        {{ $fecha }} (Hora local)
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 18px; font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase;">
                                        Destino de Almacenamiento:
                                    </td>
                                    <td style="padding: 14px 18px; font-size: 13px; color: #0f172a; font-weight: 600;">
                                        {{ $destino }}
                                    </td>
                                </tr>
                            </table>

                            @if(!$isSuccess && $detallesError)
                                <!-- Caja de Detalles del Error -->
                                <div style="background-color: #fff1f2; border: 1px solid #fecdd3; border-radius: 14px; padding: 14px 18px; margin-bottom: 24px;">
                                    <p style="margin: 0 0 6px 0; color: #9f1239; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">
                                        Detalle del Error Detectado:
                                    </p>
                                    <p style="margin: 0; color: #881337; font-size: 12px; font-family: monospace; line-height: 1.5; word-break: break-word;">
                                        {{ $detallesError }}
                                    </p>
                                </div>
                            @endif

                            <!-- Botones de Acción -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 10px;">
                                <tr>
                                    <td align="center">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center" style="border-radius: 12px; background-color: #0f172a; padding: 12px 24px; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.2);">
                                                    <a href="{{ url('/backups') }}" target="_blank"
                                                        style="color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 700; font-family: 'Segoe UI', Arial, sans-serif; display: inline-block;">
                                                        📂 Ir al Módulo de Respaldos
                                                    </a>
                                                </td>
                                                <td width="10"></td>
                                                <td align="center" style="border-radius: 12px; background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 12px 20px;">
                                                    <a href="{{ url('/bitacora') }}" target="_blank"
                                                        style="color: #334155; text-decoration: none; font-size: 13px; font-weight: 700; font-family: 'Segoe UI', Arial, sans-serif; display: inline-block;">
                                                        📋 Ver Bitácora de Auditoría
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- ─── FOOTER CORPORATIVO ─── -->
                    <tr>
                        <td align="center" class="email-footer"
                            style="background-color: #f8fafc; padding: 24px 30px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; line-height: 1.6;">
                            <p style="margin: 0 0 6px 0; font-weight: 700; color: #334155;">
                                AXStore ERP — Sistema de Gestión Comercial y Seguridad
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                                Este es un mensaje de notificación automático enviado mediante Resend. Por favor, no respondas a este correo.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>

