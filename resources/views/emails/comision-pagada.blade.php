<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comisión Pagada — AXStore</title>
    <style>
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 15px 8px !important;
            }

            .email-card {
                border-radius: 18px !important;
            }

            .email-header {
                padding: 30px 16px 24px 16px !important;
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

<body
    style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">

    <!-- Contenedor Principal Centrado -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-wrapper"
        style="background-color: #f8fafc; padding: 30px 15px;">
        <tr>
            <td align="center">

                <!-- Tarjeta Central del Correo -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-card"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">

                    <!-- ─── HEADER EMERALD ─── -->
                    <tr>
                        <td align="center" class="email-header"
                            style="background: linear-gradient(135deg, #064e3b 0%, #059669 50%, #10b981 100%); padding: 38px 25px 32px 25px;">
                            <!-- Logo / Badge AXStore -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center"
                                        style="background-color: #ffffff; padding: 8px 18px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                        <span
                                            style="font-size: 22px; font-weight: 900; color: #0f172a; letter-spacing: -0.02em; font-family: 'Segoe UI', Arial, sans-serif;">
                                            AX<span style="color: #059669;">Store</span>
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="margin: 14px 0 0 0; color: #ecfdf5; font-size: 15px; font-weight: 600; letter-spacing: 0.02em;">
                                ¡Comisión Pagada Exitosamente!
                            </p>
                        </td>
                    </tr>

                    <!-- ─── CUERPO DEL CORREO ─── -->
                    <tr>
                        <td class="email-body" style="padding: 32px 32px 24px 32px; background-color: #ffffff;">

                            <!-- Saludo -->
                            <h2 class="email-title"
                                style="margin: 0 0 12px 0; color: #0f172a; font-size: 22px; font-weight: 800;">
                                ¡Hola, {{ $vendedor->nombre_real ?: $vendedor->username }}!
                            </h2>

                            <p style="margin: 0 0 24px 0; color: #475569; font-size: 15px; line-height: 1.6;">
                                Nos alegra informarte que se ha procesado correctamente la liquidación de tus
                                comisiones. A continuación encontrarás el desglose detallado de tu pago:
                            </p>

                            <!-- TARJETA DE RESUMEN DE PAGO -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 16px; margin-bottom: 28px; overflow: hidden;">
                                <tr>
                                    <td
                                        style="padding: 20px 24px; text-align: center; border-bottom: 1px solid #a7f3d0; background-color: #d1fae5;">
                                        <span
                                            style="font-size: 11px; font-weight: 900; color: #065f46; text-transform: uppercase; letter-spacing: 0.1em; display: block; margin-bottom: 4px;">Monto
                                            Total Liquidado</span>
                                        <span style="font-size: 32px; font-weight: 900; color: #047857;">$
                                            {{ number_format($totalPagado, 2) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 18px 24px;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                            width="100%" style="font-size: 14px; color: #064e3b;">
                                            <tr>
                                                <td style="padding: 5px 0; color: #047857; font-weight: 600;">Método de
                                                    Pago:</td>
                                                <td align="right"
                                                    style="padding: 5px 0; font-weight: 800; color: #065f46;">
                                                    {{ $metodoPago }}
                                                </td>
                                            </tr>
                                            @if(!empty($referenciaPago))
                                                <tr>
                                                    <td style="padding: 5px 0; color: #047857; font-weight: 600;">N°
                                                        Referencia / Comprobante:</td>
                                                    <td align="right"
                                                        style="padding: 5px 0; font-weight: 800; color: #065f46; font-family: monospace;">
                                                        {{ $referenciaPago }}
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 5px 0; color: #047857; font-weight: 600;">Cantidad
                                                    de Comisiones:</td>
                                                <td align="right"
                                                    style="padding: 5px 0; font-weight: 800; color: #065f46;">
                                                    {{ count($comisiones) }}
                                                    {{ count($comisiones) == 1 ? 'comisión' : 'comisiones' }}
                                                </td>
                                            </tr>
                                            @if(!empty($comprobantePath))
                                                <tr>
                                                    <td style="padding: 5px 0; color: #047857; font-weight: 600;">Comprobante Adjunto:</td>
                                                    <td align="right"
                                                        style="padding: 5px 0; font-weight: 800; color: #065f46;">
                                                        Adjunto en este correo
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 5px 0; color: #047857; font-weight: 600;">Fecha de
                                                    Liquidación:</td>
                                                <td align="right"
                                                    style="padding: 5px 0; font-weight: 700; color: #065f46;">
                                                    {{ now()->format('d/m/Y h:i A') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if(!empty($notas))
                                <!-- NOTAS ADICIONALES -->
                                <div
                                    style="background-color: #f1f5f9; border-left: 4px solid #64748b; padding: 12px 16px; border-radius: 8px; margin-bottom: 28px;">
                                    <span
                                        style="font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; display: block; margin-bottom: 2px;">Nota
                                        de Liquidación:</span>
                                    <p style="margin: 0; font-size: 13px; color: #334155; font-style: italic;">
                                        "{{ $notas }}"</p>
                                </div>
                            @endif

                            <!-- TABLA DESGLOSE DE COMISIONES -->
                            <h3
                                style="margin: 0 0 14px 0; font-size: 15px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.03em;">
                                Desglose de Comisiones Incluidas
                            </h3>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="border-collapse: collapse; margin-bottom: 28px; font-size: 13px;">
                                <thead>
                                    <tr style="background-color: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                                        <th align="left" style="padding: 10px 12px; font-weight: 800; color: #475569;">
                                            Concepto / Venta</th>
                                        <th align="center"
                                            style="padding: 10px 12px; font-weight: 800; color: #475569;">Fecha</th>
                                        <th align="right" style="padding: 10px 12px; font-weight: 800; color: #475569;">
                                            Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($comisiones as $c)
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <td style="padding: 12px; color: #1e293b; font-weight: 600;">
                                                @if($c->id_salida && $c->salida)
                                                    Salida #{{ $c->salida->codigo_salida ?? $c->id_salida }}
                                                @else
                                                    {{ $c->concepto ?? 'Bono / Incentivo' }}
                                                @endif
                                            </td>
                                            <td align="center" style="padding: 12px; color: #64748b;">
                                                {{ \Carbon\Carbon::parse($c->created_at)->format('d/m/Y') }}
                                            </td>
                                            <td align="right" style="padding: 12px; color: #047857; font-weight: 800;">
                                                $ {{ number_format($c->monto, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <p
                                style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5; text-align: center;">
                                Si tienes alguna duda respecto a tu pago, ponte en contacto con el administrador del
                                sistema.
                            </p>
                        </td>
                    </tr>

                    <!-- ─── FOOTER ─── -->
                    <tr>
                        <td align="center" class="email-footer"
                            style="background-color: #f8fafc; padding: 24px 30px; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 6px 0; color: #94a3b8; font-size: 12px; font-weight: 600;">
                                AXStore — Sistema de Gestión y Comisiones
                            </p>
                            <p style="margin: 0; color: #cbd5e1; font-size: 11px;">
                                Este es un correo automático, por favor no respondas a este mensaje.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>