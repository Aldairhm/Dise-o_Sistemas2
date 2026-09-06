<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — AXStore</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/login.css', 'resources/js/login.js'])
</head>
<body>

    <div class="login-card">

        <!-- BRANDING / GESTION -->
        <div class="panel-brand">
            <div class="brand-arrow-cut"></div>

            <div class="brand-header">
                <div class="brand-logo-container">
                    <div class="logo-ambient-glow"></div>
                    <img src="{{ asset('assets/images/logo.png') }}" alt="AXStore" class="brand-logo-img">
                </div>

                <div class="brand-badge-pill">
                    <span class="brand-pulse-dot"></span>
                    <span>Plataforma Inteligente</span>
                </div>

                <p class="brand-subtitle">Sistema integral de gestión y control centralizado</p>
                <div class="brand-accent-line"></div>
            </div>

            <div class="features-list">
                <div class="feature-item">
                    <div class="feature-icon-box">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <span>Acceso seguro y cifrado</span>
                </div>

                <div class="feature-item">
                    <div class="feature-icon-box">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span>Panel de control en tiempo real</span>
                </div>

                <div class="feature-item">
                    <div class="feature-icon-box">
                        <i class="fas fa-users-gear"></i>
                    </div>
                    <span>Gestión de equipos centralizada</span>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE ACCESO -->
        <div class="panel-form">

            <!-- Pill Badge -->
            <div class="badge-pill">
                <i class="fas fa-circle-nodes"></i>
                <span>Sistema de acceso</span>
            </div>

            <!-- Titulo y subtitulo -->
            <div class="form-heading">
                <h2>Bienvenido de nuevo</h2>
                <p>Ingresa tus credenciales para continuar</p>
            </div>

            <!-- Alerta si hay error -->
            @if ($errors->any())
                <div class="error-banner">
                    <i class="fas fa-circle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Alerta de exito -->
            @if (session('status_success'))
                <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 12px; padding: 0.85rem 1rem; margin-bottom: 1.4rem; display: flex; align-items: center; gap: 0.75rem; color: #6ee7b7; font-size: 0.88rem;">
                    <i class="fas fa-circle-check" style="color: #10b981; font-size: 1rem; flex-shrink: 0;"></i>
                    <span>{{ session('status_success') }}</span>
                </div>
            @endif

            <!-- Formulario -->
            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <!-- Campo Usuario -->
                <div class="form-group">
                    <label class="form-label" for="username">
                        <i class="fas fa-envelope"></i>
                        <span>Correo Electrónico o Usuario</span>
                    </label>
                    <div class="input-container">
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="custom-input {{ $errors->has('username') ? 'is-error' : '' }}" 
                            placeholder="nombre@empresa.com o tu usuario"
                            value="{{ old('username', $savedUsername ?? '') }}"
                            required 
                            {{ empty($savedUsername) ? 'autofocus' : '' }}
                        >
                    </div>
                </div>

                <!-- Campo Contraseña -->
                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="fas fa-lock"></i>
                        <span>Contraseña</span>
                    </label>
                    <div class="input-container">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="custom-input {{ $errors->has('password') ? 'is-error' : '' }}" 
                            placeholder="••••••••" 
                            required
                            {{ !empty($savedUsername) ? 'autofocus' : '' }}
                        >
                        <button type="button" class="btn-toggle-eye" id="togglePasswordBtn" onclick="togglePassVisibility()" title="Mostrar/ocultar contraseña">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!--Recordarme / Olvido contraseña -->
                <div class="form-options">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember', !empty($savedUsername ?? '')) ? 'checked' : '' }}>
                        <span>Recordarme</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        ¿Olvidó su contraseña?
                    </a>
                </div>

                <!-- Boton de Iniciar Sesión -->
                <button type="submit" class="btn-submit" id="btn-login-submit">
                    <span>Iniciar Sesión</span>
                    <span class="btn-arrow-box">
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </button>

            </form>

            <!-- Soporte -->
            <div class="support-text">
                ¿Problemas para ingresar? <a href="https://wa.me/503XXXXXXXX" target="_blank">Contactar soporte</a>
            </div>


        </div>

    </div>
    @if(session('swal_error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: '{{ session('swal_error.title') }}',
                html:  '<p style="color:#475569;font-size:0.95rem;line-height:1.6;">{{ session('swal_error.text') }}</p>',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#2563eb',
                customClass: {
                    popup:          'swal-login-popup',
                    title:          'swal-login-title',
                    confirmButton:  'swal-login-btn',
                },
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
            });
        });
    </script>
    @endif

    @if(session('swal_warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'warning',
                title: '{{ session('swal_warning.title') }}',
                html:  '<p style="color:#475569;font-size:0.95rem;line-height:1.6;">{{ session('swal_warning.text') }}</p>',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#f59e0b',
                customClass: {
                    popup: 'swal-login-popup',
                    title: 'swal-login-title',
                },
            });
        });
    </script>
    @endif

</body>
</html>
