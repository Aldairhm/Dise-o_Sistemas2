<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @media only screen and (max-width: 600px) {
            .email-wrapper { padding: 15px 8px !important; }
            .email-card { border-radius: 18px !important; }
            .email-header { padding: 30px 16px 24px 16px !important; }
            .email-body { padding: 24px 16px !important; }
            .email-btn { padding: 13px 22px !important; font-size: 14px !important; }
            .email-footer { padding: 22px 14px !important; }
            .email-title { font-size: 20px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">

    <!-- Contenedor Principal Centrado -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-wrapper" style="background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">

                <!-- Tarjeta Central del Correo (max 580px) -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-card" style="max-width: 580px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); border: 1px solid #e2e8f0;">

                    <!-- ─── HEADER CON DEGRADADO ─── -->
                    <tr>
                        <td align="center" class="email-header" style="background: linear-gradient(135deg, #0f172a 0%, #0369a1 50%, #0284c7 100%); padding: 42px 25px 36px 25px; border-top-left-radius: 24px; border-top-right-radius: 24px;">
                            <!-- Logo / Badge AXStore -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="background-color: #fffbeb; padding: 8px 18px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                        <span style="font-size: 20px; vertical-align: middle; margin-right: 6px;">🛍️</span>
                                        <span style="font-size: 22px; font-weight: 900; color: #0f172a; letter-spacing: -0.02em; font-family: 'Segoe UI', Arial, sans-serif;">
                                            AX<span style="color: #0284c7;">Store</span>
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Subtitulo del Header -->
                            <p style="margin: 14px 0 0 0; color: #e0f2fe; font-size: 15px; font-weight: 500; letter-spacing: 0.02em; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                                Restablecimiento de Contraseña
                            </p>
                        </td>
                    </tr>

                    <!-- ─── CUERPO DEL CORREO ─── -->
                    <tr>
                        <td class="email-body" style="padding: 35px 35px 25px 35px; background-color: #ffffff;">

                            <!-- Saludo Personalizado -->
                            <h2 class="email-title" style="margin: 0 0 16px 0; color: #0f172a; font-size: 23px; font-weight: 800; letter-spacing: -0.01em;">
                                ¡Hola, {{ $user->nombre_real ?? $user->username }}! 👋
                            </h2>

                            <!-- Parrafo Descriptivo -->
                            <p style="margin: 0 0 28px 0; color: #475569; font-size: 15px; line-height: 1.6;">
                                Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en 
                                <strong style="background-color: #fef08a; color: #713f12; padding: 2px 7px; border-radius: 4px; font-weight: 700;">AXStore</strong>. 
                                Para continuar con el proceso de forma segura, utiliza el botón que aparece a continuación:
                            </p>

                            <!-- Boton Principal "Restablecer mi Contraseña" -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center" style="border-radius: 999px; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); box-shadow: 0 6px 20px rgba(2, 132, 199, 0.35);">
                                                    <a href="{{ $resetUrl }}" target="_blank" class="email-btn" style="display: inline-block; padding: 15px 32px; font-size: 15px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 999px; letter-spacing: 0.01em; font-family: 'Segoe UI', Arial, sans-serif;">
                                                        <span style="margin-right: 8px;">🔐</span> Restablecer mi Contraseña
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!--n"¿El boton no funciona?" -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px; background-color: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding: 16px 18px;">
                                        <p style="margin: 0 0 8px 0; color: #64748b; font-size: 13px; font-weight: 600;">
                                            📎 ¿El botón no funciona?
                                        </p>
                                        <p style="margin: 0; font-size: 12px; line-height: 1.5; word-break: break-all;">
                                            <a href="{{ $resetUrl }}" target="_blank" style="color: #0284c7; text-decoration: underline;">
                                                {{ $resetUrl }}
                                            </a>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- "Información Importante" -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px; background-color: #fefce8; border-radius: 12px; border-left: 4px solid #eab308; border-top: 1px solid #fef08a; border-right: 1px solid #fef08a; border-bottom: 1px solid #fef08a;">
                                <tr>
                                    <td style="padding: 18px 20px;">
                                        <p style="margin: 0 0 12px 0; color: #854d0e; font-size: 14px; font-weight: 800;">
                                            Información Importante
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #713f12; font-size: 13px; line-height: 1.65;">
                                            <li style="margin-bottom: 6px;">
                                                Este enlace expira el <strong>{{ $expiresAt }}</strong> (en 15 minutos).
                                            </li>
                                            <li style="margin-bottom: 6px;">
                                                Nunca compartas este enlace con nadie.
                                            </li>
                                            <li style="margin-bottom: 6px;">
                                                Nuestro equipo jamás te pedirá este enlace por teléfono o email.
                                            </li>
                                            <li>
                                                Si no solicitaste este cambio, ignora este correo y tu cuenta permanecerá segura.
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- Linea divisoria -->
                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 25px 0 20px 0;">

                            <!-- Despedida -->
                            <p style="margin: 0; color: #64748b; font-size: 14px; line-height: 1.5;">
                                Con los mejores deseos,<br>
                                <span style="color: #0f172a; font-weight: 700;">
                                    El equipo de <span style="color: #0284c7;">AX Store</span> 💼
                                </span>
                            </p>

                        </td>
                    </tr>

                    <!-- ─── FOOTER INFERIOR ─── -->
                    <tr>
                        <td align="center" class="email-footer" style="background-color: #0b111c; padding: 28px 25px; border-bottom-left-radius: 24px; border-bottom-right-radius: 24px;">
                            
                            <p style="margin: 0 0 10px 0; color: #94a3b8; font-size: 12px; line-height: 1.6; max-width: 480px;">
                                Este es un correo electrónico automático generado por el sistema de seguridad de 
                                <strong style="background-color: #fef08a; color: #713f12; padding: 1px 6px; border-radius: 3px; font-size: 11px;">AXStore</strong>.
                            </p>
                            
                            <p style="margin: 0 0 18px 0; color: #64748b; font-size: 12px; line-height: 1.5; max-width: 460px;">
                                Por favor, no respondas a este mensaje. Si necesitas ayuda, contacta con nuestro equipo de soporte.
                            </p>

                            <!-- Separador -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="85%" style="margin-bottom: 16px;">
                                <tr>
                                    <td style="border-top: 1px solid rgba(255,255,255,0.08);"></td>
                                </tr>
                            </table>

                            <!-- Copyright & Seguridad -->
                            <p style="margin: 0 0 8px 0; color: #cbd5e1; font-size: 12px; font-weight: 600;">
                                © 2026 <span style="background-color: #fef08a; color: #713f12; padding: 1px 6px; border-radius: 3px;">AXStore</span>. Todos los derechos reservados.
                            </p>
                            
                            <p style="margin: 0; color: #38bdf8; font-size: 11px; font-weight: 600; letter-spacing: 0.03em;">
                                🔒 Tu seguridad es nuestra prioridad
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
