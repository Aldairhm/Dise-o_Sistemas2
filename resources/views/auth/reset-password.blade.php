<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Contraseña — AXStore</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/reset-password.css'])
</head>
<body>

    <div class="card-container">

        <!-- icono superior -->
        <div class="top-icon-wrapper">
            <i class="fas fa-lock"></i>
        </div>

        <!-- Pill Badge -->
        <div class="badge-pill">
            <span>Nueva Contraseña</span>
        </div>

        <!-- Titulos -->
        <div class="card-heading">
            <h2>Crear Nueva Contraseña</h2>
            <p>Elige una contraseña segura de mínimo 8 caracteres.</p>
        </div>

        <!-- Alerta de errores de validacion -->
        @if ($errors->any())
            <div class="alert-error">
                <i class="fas fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Formulario -->
        <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Nueva Contraseña -->
            <div class="form-group">
                <label class="form-label" for="password">
                    <i class="fas fa-lock"></i>
                    <span>Nueva Contraseña</span>
                </label>
                <div class="input-container">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="custom-input" 
                        placeholder="Mínimo 8 caracteres" 
                        required 
                        autofocus
                        autocomplete="new-password"
                    >
                    <button type="button" class="btn-toggle-eye" onclick="toggleVisibility('password', 'eye1')" title="Mostrar u ocultar contraseña">
                        <i class="fas fa-eye" id="eye1"></i>
                    </button>
                </div>

                <!-- Fuerza de contraseña -->
                <div class="strength-meter-box" id="strengthMeterBox">
                    <div class="strength-header">
                        <span class="strength-title">Seguridad de la contraseña:</span>
                        <span class="strength-status" id="strengthLabel">Esperando contraseña</span>
                    </div>
                    
                    <div class="strength-bars">
                        <div class="strength-bar" id="bar1"></div>
                        <div class="strength-bar" id="bar2"></div>
                        <div class="strength-bar" id="bar3"></div>
                        <div class="strength-bar" id="bar4"></div>
                    </div>

                    <!-- Lista de requisitos -->
                    <ul class="rules-list">
                        <li class="rule-item" id="rule-length">
                            <i class="fas fa-circle-xmark"></i>
                            <span>Mínimo 8 caracteres</span>
                        </li>
                        <li class="rule-item" id="rule-case">
                            <i class="fas fa-circle-xmark"></i>
                            <span>Letras mayúsculas (A-Z) y minúsculas (a-z)</span>
                        </li>
                        <li class="rule-item" id="rule-number">
                            <i class="fas fa-circle-xmark"></i>
                            <span>Al menos un número (0-9)</span>
                        </li>
                        <li class="rule-item" id="rule-symbol">
                            <i class="fas fa-circle-xmark"></i>
                            <span>Al menos un carácter especial (@, $, !, %, *, #, ?, &)</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Confirmar Contraseña -->
            <div class="form-group">
                <label class="form-label" for="password_confirmation">
                    <i class="fas fa-shield-halved"></i>
                    <span>Confirmar Contraseña</span>
                </label>
                <div class="input-container">
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="custom-input" 
                        placeholder="Repite la contraseña" 
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="btn-toggle-eye" onclick="toggleVisibility('password_confirmation', 'eye2')" title="Mostrar u ocultar contraseña">
                        <i class="fas fa-eye" id="eye2"></i>
                    </button>
                </div>
                <!-- Feedback en tiempo real de coincidencia -->
                <div class="match-feedback" id="matchFeedback">
                    <i class="fas fa-circle-check" id="matchIcon"></i>
                    <span id="matchText">Las contraseñas coinciden</span>
                </div>
            </div>

            <!-- Boton Guardar Contraseña -->
            <button type="submit" class="btn-submit" id="btnSubmit">
                <span>Guardar Contraseña</span>
                <span class="btn-icon-box">
                    <i class="fas fa-check"></i>
                </span>
            </button>

        </form>

        <!-- Volver al Login -->
        <div class="back-login">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-left"></i>
                <span>Iniciar Sesión</span>
            </a>
        </div>

    </div>

    @vite(['resources/js/reset-password.js'])
</body>
</html>
