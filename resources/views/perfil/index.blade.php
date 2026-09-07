<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mi Perfil — AXStore</title>

    <!-- Aplicar tema ANTES del render para evitar flash -->
    <script>
        (function() {
            var t = localStorage.getItem('ax_theme') || 'system';
            if (t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/css/perfil.css', 'resources/js/perfil.js'])
</head>
<body class="min-h-screen font-[Inter] transition-colors duration-300" style="background: var(--bg-page, #f8fafc);">

    <!-- ─── HEADER ─── -->
    <header class="sticky top-0 z-50 border-b shadow-sm transition-colors" style="background: var(--bg-card, #fff); border-color: var(--border, #e2e8f0);">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <!-- Back + Title -->
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}"
                   class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                   title="Volver al inicio">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h1 class="text-base font-black" style="color: var(--text-main, #0f172a);">Configurar Perfil</h1>
                    <p class="text-[11px] hidden sm:block" style="color: var(--text-muted, #64748b);">Gestiona tu cuenta y preferencias</p>
                </div>
            </div>

            <!-- Avatar + Nombre -->
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 text-white flex items-center justify-center font-black text-sm shadow-sm">
                    {{ strtoupper(substr(auth()->user()->nombre_real ?? auth()->user()->username, 0, 1)) }}
                </div>
                <span id="perfil-header-name" class="hidden sm:block text-sm font-bold" style="color: var(--text-main, #0f172a);">
                    {{ auth()->user()->nombre_real ?? auth()->user()->username }}
                </span>
            </div>
        </div>
    </header>

    <!-- ─── CONTENIDO ─── -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8">

        <!-- TABS NAV -->
        <div class="flex items-center gap-1 overflow-x-auto mb-6 bg-white rounded-2xl p-1.5 shadow-sm border" style="border-color: var(--border, #e2e8f0); background: var(--bg-card, #fff);">
            <button id="tab-btn-personal" onclick="switchTab('personal')"
                    class="profile-tab active">
                <i class="fas fa-user text-xs"></i>
                <span>Información Personal</span>
            </button>
            <button id="tab-btn-seguridad" onclick="switchTab('seguridad')"
                    class="profile-tab">
                <i class="fas fa-lock text-xs"></i>
                <span>Seguridad</span>
            </button>
            <button id="tab-btn-apariencia" onclick="switchTab('apariencia')"
                    class="profile-tab">
                <i class="fas fa-palette text-xs"></i>
                <span>Apariencia</span>
            </button>
        </div>

        <!-- ══════════════════════════════════════════════════════ -->
        <!-- TAB 1: INFORMACIÓN PERSONAL                            -->
        <!-- ══════════════════════════════════════════════════════ -->
        <div id="tab-personal" class="profile-tab-content active">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Avatar Card -->
                <div class="lg:col-span-1">
                    <div class="rounded-2xl border shadow-sm overflow-hidden" style="background: var(--bg-card, #fff); border-color: var(--border, #e2e8f0);">
                        <div class="h-1.5 bg-gradient-to-r from-blue-500 via-cyan-400 to-indigo-500"></div>
                        <div class="p-6 flex flex-col items-center text-center">
                            <div class="profile-avatar w-24 h-24 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-500 text-white flex items-center justify-center text-4xl font-black shadow-lg shadow-blue-500/25 mb-4">
                                {{ strtoupper(substr(auth()->user()->nombre_real ?? auth()->user()->username, 0, 1)) }}
                            </div>
                            <h2 class="text-base font-black mb-0.5" style="color: var(--text-main, #0f172a);">
                                {{ auth()->user()->nombre_real ?? auth()->user()->username }}
                            </h2>
                            <p class="text-xs font-mono mb-3" style="color: var(--text-muted, #64748b);">{{ auth()->user()->username }}</p>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold
                                {{ auth()->user()->rol === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                <i class="fas {{ auth()->user()->rol === 'admin' ? 'fa-shield-halved' : 'fa-tag' }} text-[9px]"></i>
                                {{ ucfirst(auth()->user()->rol) }}
                            </span>

                            <div class="w-full mt-5 pt-4 border-t text-xs space-y-2.5 text-left" style="border-color: var(--border, #e2e8f0);">
                                <div class="flex justify-between">
                                    <span style="color: var(--text-muted, #64748b);">Estado:</span>
                                    <span class="font-bold {{ auth()->user()->estado ? 'text-emerald-600' : 'text-slate-400' }}">
                                        {{ auth()->user()->estado ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                                @if(auth()->user()->telefono)
                                <div class="flex justify-between">
                                    <span style="color: var(--text-muted, #64748b);">Teléfono:</span>
                                    <span class="font-bold" style="color: var(--text-main, #0f172a);">{{ auth()->user()->telefono }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="lg:col-span-2">
                    <div class="rounded-2xl border shadow-sm" style="background: var(--bg-card, #fff); border-color: var(--border, #e2e8f0);">
                        <div class="px-6 py-4 border-b flex items-center gap-2.5" style="border-color: var(--border, #e2e8f0);">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i class="fas fa-user-pen text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black" style="color: var(--text-main, #0f172a);">Datos Personales</h3>
                                <p class="text-[11px]" style="color: var(--text-muted, #64748b);">Actualiza tu nombre, teléfono y correo</p>
                            </div>
                        </div>

                        <form id="form-personal" onsubmit="savePersonalInfo(event)" class="p-6 space-y-5">

                            <!-- Nombre Completo -->
                            <div>
                                <label class="block text-xs font-bold mb-1.5" style="color: var(--text-main, #0f172a);">
                                    Nombre Completo <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="perfil_nombre" name="nombre_real"
                                       value="{{ auth()->user()->nombre_real }}"
                                       required
                                       placeholder="Ej. Juan Pérez"
                                       class="profile-input">
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label class="block text-xs font-bold mb-1.5" style="color: var(--text-main, #0f172a);">
                                    Teléfono
                                    <span class="font-normal text-slate-400 ml-1">(opcional)</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 flex items-center gap-1 text-slate-400 text-xs font-bold select-none">
                                        <img src="https://flagcdn.com/w20/sv.png" alt="SV" class="h-3.5 rounded-sm">
                                        <span>+503</span>
                                    </div>
                                    <input type="text" id="perfil_telefono" name="telefono"
                                           value="{{ auth()->user()->telefono }}"
                                           maxlength="9"
                                           placeholder="XXXX-XXXX"
                                           class="profile-input"
                                           style="padding-left: 5.2rem;"
                                           oninput="formatTelefono(this)">
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2" id="telefono-icon"></div>
                                </div>
                                <p id="telefono-msg" class="text-xs text-slate-400 mt-1">
                                    Opcional. Formato: XXXX-XXXX (ej: 7890-1234)
                                </p>
                            </div>

                            <!-- Correo / Username -->
                            <div>
                                <label class="block text-xs font-bold mb-1.5" style="color: var(--text-main, #0f172a);">
                                    Correo Electrónico / Usuario <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" id="perfil_email" name="username"
                                       value="{{ auth()->user()->username }}"
                                       required
                                       placeholder="usuario@empresa.com"
                                       class="profile-input">
                                <p class="text-xs text-slate-400 mt-1">Este es tu identificador de inicio de sesión.</p>
                            </div>

                            <!-- Botón -->
                            <div class="flex justify-end pt-2">
                                <button type="submit" id="btn-save-personal"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-black transition-all shadow-md hover:shadow-blue-500/30 active:scale-95 cursor-pointer">
                                    <i class="fas fa-save"></i>
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════ -->
        <!-- TAB 2: SEGURIDAD (CAMBIO DE CONTRASEÑA)               -->
        <!-- ══════════════════════════════════════════════════════ -->
        <div id="tab-seguridad" class="profile-tab-content">
            <div class="max-w-xl mx-auto">
                <div class="rounded-2xl border shadow-sm" style="background: var(--bg-card, #fff); border-color: var(--border, #e2e8f0);">
                    <div class="px-6 py-4 border-b flex items-center gap-2.5" style="border-color: var(--border, #e2e8f0);">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black" style="color: var(--text-main, #0f172a);">Cambiar Contraseña</h3>
                            <p class="text-[11px]" style="color: var(--text-muted, #64748b);">Usa una contraseña segura de al menos 8 caracteres</p>
                        </div>
                    </div>

                    <form id="form-password" onsubmit="savePassword(event)" class="p-6 space-y-5">

                        <!-- Contraseña Actual -->
                        <div>
                            <label class="block text-xs font-bold mb-1.5" style="color: var(--text-main, #0f172a);">
                                Contraseña Actual <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="current_password" name="current_password"
                                       required placeholder="••••••••"
                                       class="profile-input pr-10">
                                <button type="button" onclick="togglePwdVisibility('current_password', 'eye-current')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 cursor-pointer p-1">
                                    <i class="fas fa-eye text-sm" id="eye-current"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Nueva Contraseña -->
                        <div>
                            <label class="block text-xs font-bold mb-1.5" style="color: var(--text-main, #0f172a);">
                                Nueva Contraseña <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="new_password" name="password"
                                       required placeholder="••••••••"
                                       class="profile-input pr-10"
                                       oninput="checkPasswordStrength(this, 'pwd-strength-fill', 'pwd-strength-label')">
                                <button type="button" onclick="togglePwdVisibility('new_password', 'eye-new')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 cursor-pointer p-1">
                                    <i class="fas fa-eye text-sm" id="eye-new"></i>
                                </button>
                            </div>
                            <!-- Barra de fortaleza -->
                            <div class="mt-2 space-y-1">
                                <div class="strength-bar">
                                    <div class="strength-fill" id="pwd-strength-fill"></div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <p class="text-[11px] text-slate-400">Mín. 8 caracteres, mayúsculas, números y símbolos</p>
                                    <span id="pwd-strength-label" class="text-xs font-bold"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmar Nueva Contraseña -->
                        <div>
                            <label class="block text-xs font-bold mb-1.5" style="color: var(--text-main, #0f172a);">
                                Confirmar Contraseña <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="confirm_password" name="password_confirmation"
                                       required placeholder="••••••••"
                                       class="profile-input pr-10">
                                <button type="button" onclick="togglePwdVisibility('confirm_password', 'eye-confirm')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 cursor-pointer p-1">
                                    <i class="fas fa-eye text-sm" id="eye-confirm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tips de seguridad -->
                        <div class="rounded-xl p-3.5 bg-blue-50 border border-blue-100 text-xs space-y-1.5 text-blue-700">
                            <p class="font-bold flex items-center gap-1.5"><i class="fas fa-shield-halved"></i> Consejos de seguridad:</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-600">
                                <li>Al menos 8 caracteres de longitud</li>
                                <li>Combina mayúsculas, minúsculas y números</li>
                                <li>Incluye símbolos como: @, #, $, %, !</li>
                                <li>No uses tu nombre ni fecha de nacimiento</li>
                            </ul>
                        </div>

                        <div class="flex justify-end pt-1">
                            <button type="submit" id="btn-save-password"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-black transition-all shadow-md hover:shadow-blue-500/30 active:scale-95 cursor-pointer">
                                <i class="fas fa-lock"></i>
                                Cambiar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════ -->
        <!-- TAB 3: APARIENCIA (TEMA)                               -->
        <!-- ══════════════════════════════════════════════════════ -->
        <div id="tab-apariencia" class="profile-tab-content">
            <div class="max-w-xl mx-auto">
                <div class="rounded-2xl border shadow-sm" style="background: var(--bg-card, #fff); border-color: var(--border, #e2e8f0);">
                    <div class="px-6 py-4 border-b flex items-center gap-2.5" style="border-color: var(--border, #e2e8f0);">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                            <i class="fas fa-palette text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black" style="color: var(--text-main, #0f172a);">Apariencia</h3>
                            <p class="text-[11px]" style="color: var(--text-muted, #64748b);">Elige cómo se ve el panel de control</p>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="text-sm font-bold mb-4" style="color: var(--text-main, #0f172a);">Modo de color</p>

                        <div class="flex flex-wrap gap-3">
                            <!-- Claro -->
                            <button onclick="setTheme('light')" data-theme="light" class="theme-option">
                                <div class="theme-icon bg-slate-100">
                                    <i class="fas fa-sun text-amber-400"></i>
                                </div>
                                <span class="theme-label">Claro</span>
                            </button>

                            <!-- Oscuro -->
                            <button onclick="setTheme('dark')" data-theme="dark" class="theme-option">
                                <div class="theme-icon bg-slate-800">
                                    <i class="fas fa-moon text-blue-400"></i>
                                </div>
                                <span class="theme-label">Oscuro</span>
                            </button>

                            <!-- Sistema (Predeterminado) -->
                            <button onclick="setTheme('system')" data-theme="system" class="theme-option">
                                <div class="theme-icon bg-gradient-to-br from-slate-100 to-slate-700">
                                    <i class="fas fa-circle-half-stroke text-white"></i>
                                </div>
                                <span class="theme-label">Sistema</span>
                            </button>
                        </div>

                        <p class="text-xs mt-4" style="color: var(--text-muted, #64748b);">
                            <i class="fas fa-info-circle mr-1"></i>
                            La preferencia se guarda en este navegador. El modo <strong>Sistema</strong> sigue automáticamente la configuración de tu dispositivo.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </main>

</body>
</html>
