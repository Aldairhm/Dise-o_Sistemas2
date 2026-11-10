<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    /** Mostrar formulario de login */
    public function showLogin()
    {
        // Si ya está logueado, redirigir según rol
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->rol);
        }

        Cookie::queue(Cookie::forget('remember_username'));

        return view('auth.login');
    }

    /** Procesar el login */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'El usuario es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $normalizedUser = strtolower(trim($request->username));
        $cacheKeyAttempts = 'login_attempts_' . md5($normalizedUser);
        $cacheKeyLockout  = 'login_lockout_' . md5($normalizedUser);

        // ── 0. Verificación Inmediata: ¿La cuenta existe y está en Auditoría Activa? ──
        $usuarioAuditado = \App\Models\User::where('username', $request->username)->first();
        if ($usuarioAuditado) {
            $auditoriaActiva = \App\Models\AuditoriaSesion::where('id_usuario_auditado', $usuarioAuditado->id)
                ->where('estado', 'activa')
                ->where('expira_en', '>', now())
                ->first();

            if ($auditoriaActiva) {
                ActivityLogger::logAnon(
                    'BLOQUEO_LOGIN_AUDITORIA',
                    'Auditoría y Seguridad',
                    "Intento de inicio de sesión bloqueado preventivamente por auditoría en curso para el usuario {$usuarioAuditado->username} (Sesión #{$auditoriaActiva->id}).",
                    $usuarioAuditado->username
                );

                return back()->with('swal_warning', [
                    'title' => 'Cuenta en Auditoría Activa',
                    'text'  => "Tu cuenta se encuentra actualmente en un proceso formal de auditoría y supervisión por la Dirección / Super Administrador. Por motivos de seguridad e integridad de datos, el acceso directo a esta cuenta está pausado temporalmente hasta que la sesión de auditoría finalice.",
                ])->withInput($request->only('username'));
            }
        }

        $attempts = Cache::get($cacheKeyAttempts, 0);

        // 1. Verificar si ya alcanzó el límite máximo de 15 intentos (bloqueo permanente hasta reset)
        if ($attempts >= 15) {
            return back()->with('swal_error', [
                'title' => 'Acceso Bloqueado',
                'text'  => 'Has superado el límite de intentos permitidos. Por tu seguridad, debes restablecer tu contraseña para desbloquear el acceso.',
                'show_reset' => true,
            ])->withInput($request->only('username'));
        }

        // 2. Verificar si se encuentra en periodo de timeout / bloqueo temporal (en segundos)
        if (Cache::has($cacheKeyLockout)) {
            $lockoutUntil = Cache::get($cacheKeyLockout);
            $secondsLeft = $lockoutUntil - now()->timestamp;

            if ($secondsLeft > 0) {
                return back()->with('swal_warning', [
                    'title' => 'Acceso Temporalmente Bloqueado',
                    'text'  => "Has superado el límite de intentos permitidos. Por favor espera {$secondsLeft} segundo(s) antes de volver a intentarlo.",
                ])->withInput($request->only('username'));
            } else {
                Cache::forget($cacheKeyLockout);
            }
        }

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];
        $remember = $request->boolean('remember');

        // Intentar autenticación
        if (Auth::attempt($credentials, $remember)) {
            $usuario = Auth::user();

            if ($usuario->estado != 1) {
                Auth::logout();
                return back()->with('swal_error', [
                    'title' => 'Cuenta Inactiva',
                    'text'  => 'Tu cuenta está desactivada y no puede acceder al sistema. Comunícate con el administrador o soporte técnico para solicitar la reactivación de tu cuenta.',
                ])->withInput();
            }

            // ── Validación de Auditoría Activa (Secure Impersonation Lock) ──
            $auditoriaActiva = \App\Models\AuditoriaSesion::where('id_usuario_auditado', $usuario->id)
                ->where('estado', 'activa')
                ->where('expira_en', '>', now())
                ->first();

            if ($auditoriaActiva) {
                Auth::logout();
                $minutosRestantes = ceil($auditoriaActiva->expira_en->diffInMinutes(now()));

                ActivityLogger::logAnon(
                    'BLOQUEO_LOGIN_AUDITORIA',
                    'Auditoría y Seguridad',
                    "Acceso bloqueado por auditoría activa para el usuario {$usuario->username} (Sesión #{$auditoriaActiva->id}).",
                    $usuario->username
                );

                return back()->with('swal_warning', [
                    'title' => 'Cuenta en Auditoría Activa',
                    'text'  => "Tu cuenta se encuentra actualmente en un proceso formal de auditoría y supervisión por la Dirección / Super Administrador. Por motivos de seguridad e integridad de datos, el acceso directo a esta cuenta está pausado temporalmente hasta que la sesión de auditoría finalice.",
                ])->withInput();
            }

            // Inicio de sesión exitoso: reiniciar contadores de seguridad
            Cache::forget($cacheKeyAttempts);
            Cache::forget($cacheKeyLockout);

            $request->session()->regenerate();

            // ── Bitácora: login exitoso ─────────────────────────────────
            ActivityLogger::log(
                'LOGIN',
                'Autenticación',
                "Inicio de sesión exitoso. Usuario: {$usuario->username} | Rol: {$usuario->rol}"
            );

            return $this->redirectByRole($usuario->rol);
        }

        // 3. Falló el intento: incrementar contador
        $attempts++;
        Cache::put($cacheKeyAttempts, $attempts, now()->addDays(2));

        if (in_array($attempts, [5, 10, 15], true) && $usuarioAuditado) {
            $notifications = app(\App\Services\NotificationService::class);
            $notifications->publish('login-threshold:'.$usuarioAuditado->id.':'.now()->format('Y-m-d-H').':'.$attempts,
                'acceso_bloqueado', 'seguridad', 'alta', 'Límite de acceso alcanzado',
                'La cuenta #'.$usuarioAuditado->id.' alcanzó un umbral de intentos fallidos. Revisa el registro de seguridad; este aviso no confirma un ataque.',
                'usuarios', $usuarioAuditado->id, $notifications->roles('super_admin'));
        }

        // Caso A: Alcanzó el límite máximo de intentos -> Bloqueo total
        if ($attempts >= 15) {
            return back()->with('swal_error', [
                'title' => '¡Cuenta Bloqueada!',
                'text'  => 'Has superado el límite de intentos permitidos. Tu cuenta ha sido bloqueada por seguridad. Es obligatorio restablecer tu contraseña.',
                'show_reset' => true,
            ])->withInput($request->only('username'));
        }

        // Caso B: Alcanzó múltiplo de 5 intentos (intento 5 o 10) -> Timeout en SEGUNDOS (60s o 120s)
        if ($attempts % 5 === 0) {
            $lockoutSeconds = ($attempts / 5) * 60; // 5to intento = 60 seg, 10mo intento = 120 seg
            Cache::put($cacheKeyLockout, now()->addSeconds($lockoutSeconds)->timestamp, now()->addSeconds($lockoutSeconds));

            return back()->with('swal_warning', [
                'title' => 'Acceso Temporalmente Bloqueado',
                'text'  => "Has superado el límite de intentos permitidos. Acceso bloqueado temporalmente por {$lockoutSeconds} segundos antes de volver a intentar.",
            ])->withInput($request->only('username'));
        }

        // ── Bitácora: intento fallido ───────────────────────────────────
        ActivityLogger::logAnon(
            'LOGIN',
            'Autenticación',
            "Intento de login fallido (intento #{$attempts}). Usuario ingresado: {$request->username}",
            $request->username
        );

        return back()->with('swal_error', [
            'title' => 'Credenciales Incorrectas',
            'text'  => 'El usuario o la contraseña ingresados son incorrectos. Por favor, verifica tus datos e inténtalo de nuevo.',
        ])->withInput($request->only('username'));
    }

    /** Cerrar sesión */
    public function logout(Request $request)
    {
        // ── Bitácora: logout (antes de cerrar sesión) ──────────────────
        $usuario = Auth::user();
        if ($usuario) {
            ActivityLogger::log(
                'LOGOUT',
                'Autenticación',
                "Cierre de sesión. Usuario: {$usuario->username} | Rol: {$usuario->rol}"
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('swal_success', [
            'title' => 'Sesión Cerrada',
            'text'  => 'Has cerrado sesión correctamente. ¡Vuelve pronto!'
        ]);
    }

    /** Mostrar formulario para solicitar recuperación de contraseña */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /** Procesar envío de enlace de recuperación */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ], [
            'username.required' => 'Debes ingresar tu correo o usuario.',
        ]);

        // Buscar el usuario
        $user = \App\Models\User::where('username', $request->username)->first();

        if (!$user) {
            return back()
                ->withErrors(['username' => 'No encontramos una cuenta asociada a este correo o usuario.'])
                ->withInput();
        }

        // Generar un token
        $token = \Illuminate\Support\Str::random(64);
        $user->token = $token;
        $user->save();

        // Guardar vigencia del token por 15 minutos
        Cache::put('password_reset_token_' . $token, $user->id, now()->addMinutes(15));

        // Enlace para resetear contraseña y expiración (15 minutos)
        $resetUrl = route('password.reset', ['token' => $token]);
        $expiresAt = now()->addMinutes(15)->format('d/m/Y H:i');

        // Enviar el correo usando el MailService unificado
        $resultado = MailService::sendPasswordReset($user, $resetUrl, $expiresAt);

        // Registrar en los logs de Laravel como respaldo
        \Illuminate\Support\Facades\Log::info("Enlace de recuperación generado para {$user->username}: {$resetUrl}");

        if ($resultado['success']) {
            return back()->with('status', 'Hemos enviado un correo electrónico a tu dirección con el enlace y las instrucciones para restablecer tu contraseña. Por favor, revisa tu bandeja de entrada.');
        } else {
            return back()->withErrors(['username' => 'No se pudo enviar el correo de recuperación: ' . ($resultado['message'] ?: 'Error en el servidor de correo.')]);
        }
    }


    /** formulario para ingresar nueva contraseña */
    public function showResetPassword($token)
    {
        $user = \App\Models\User::where('token', $token)->first();

        // Verificar si el token ha expirado (más de 15 minutos)
        $isExpired = false;
        if (!Cache::has('password_reset_token_' . $token)) {
            if (!$user || ($user->updated_at && $user->updated_at->addMinutes(15)->isPast())) {
                $isExpired = true;
            }
        }

        if (!$user || $isExpired) {
            if ($user && $isExpired) {
                $user->token = null;
                $user->save();
            }
            return redirect()->route('password.request')
                ->withErrors(['username' => 'El enlace de recuperación es inválido o ha expirado (vigencia máxima de 15 minutos). Solicita uno nuevo.']);
        }

        return view('auth.reset-password', ['token' => $token, 'user' => $user]);
    }

    /** Actualizar la contraseña del usuario */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => [
                'required',
                'string',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'password.required'  => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $user = \App\Models\User::where('token', $request->token)->first();

        // Verificar si el token ha expirado (más de 15 minutos)
        $isExpired = false;
        if (!Cache::has('password_reset_token_' . $request->token)) {
            if (!$user || ($user->updated_at && $user->updated_at->addMinutes(15)->isPast())) {
                $isExpired = true;
            }
        }

        if (!$user || $isExpired) {
            if ($user && $isExpired) {
                $user->token = null;
                $user->save();
            }
            return redirect()->route('password.request')
                ->withErrors(['username' => 'El enlace de recuperación es inválido o ha expirado (vigencia máxima de 15 minutos). Solicita uno nuevo.']);
        }

        // Guardar la nueva contraseña y limpiar el token
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->token = null;
        $user->save();

        Cache::forget('password_reset_token_' . $request->token);

        // Limpiar bloqueo de intentos al haber restablecido la contraseña
        $normalizedUser = strtolower(trim($user->username));
        Cache::forget('login_attempts_' . md5($normalizedUser));
        Cache::forget('login_lockout_' . md5($normalizedUser));

        return redirect()->route('login')->with('status_success', '¡Tu contraseña ha sido restablecida con éxito! Ya puedes iniciar sesión.');
    }

    /** Vista previa del correo de restablecimiento de contraseña */
    public function previewResetEmail()
    {
        $dummyUser = new \App\Models\User([
            'username' => 'usuario_demo',
            'email'    => 'demo@axstore.com',
        ]);
        $resetUrl = route('password.preview.view');
        $expiresAt = now()->addMinutes(15)->format('d/m/Y H:i');

        return view('emails.reset-password', [
            'user'      => $dummyUser,
            'resetUrl'  => $resetUrl,
            'expiresAt' => $expiresAt,
        ]);
    }

    /** Vista previa del formulario de restablecimiento */
    public function previewResetPasswordView()
    {
        $dummyUser = new \App\Models\User([
            'username' => 'usuario_demo',
            'email'    => 'demo@axstore.com',
        ]);

        return view('auth.reset-password', [
            'token' => 'preview-token-demo',
            'user'  => $dummyUser,
        ]);
    }

    /** Mostrar pantalla de confirmación de contraseña para acciones críticas */
    public function showConfirmPassword()
    {
        return view('auth.confirm-password');
    }

    /** Procesar confirmación de contraseña */
    public function confirmPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if (! \Illuminate\Support\Facades\Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors([
                'password' => 'La contraseña ingresada no es correcta.',
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('backups.index'));
    }

    /** Redirigir según el rol del usuario */
    private function redirectByRole(string $rol)
    {
        return match ($rol) {
            'admin'    => redirect()->route('home'),
            'vendedor' => redirect()->route('home'),
            default    => redirect()->route('home'),
        };
    }
}
