<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña — AXStore</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/forgot-password.css']); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/loading-states.js']); ?>
</head>
<body>

    <div class="recovery-card">

        <!-- Ícono superior -->
        <div class="top-icon-wrapper">
            <i class="fas fa-envelope-open-text"></i>
        </div>

        <!-- Pill Badge -->
        <div class="badge-pill">
            <span>Recuperación de acceso</span>
        </div>

        <!-- Encabezado -->
        <div class="card-heading">
            <h2>Recuperar Contraseña</h2>
            <p>Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>
        </div>

        <!-- Mensaje de error -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="alert-box alert-error">
                <i class="fas fa-circle-exclamation"></i>
                <span><?php echo e($errors->first()); ?></span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Mensaje de éxito -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
            <div class="alert-box alert-success">
                <i class="fas fa-circle-check"></i>
                <div>
                    <div><?php echo e(session('status')); ?></div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Formulario -->
        <form data-loading-form data-loading-label="Enviando..." method="POST" action="<?php echo e(route('password.email')); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label" for="username">
                    <i class="fas fa-envelope"></i>
                    <span>Correo Electrónico</span>
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="custom-input <?php echo e($errors->has('username') ? 'is-error' : ''); ?>" 
                    placeholder="nombre@empresa.com"
                    value="<?php echo e(old('username')); ?>" 
                    required 
                    autofocus
                >
            </div>

            <button type="submit" class="btn-submit" id="btn-submit-recovery">
                <span>Enviar Correo</span>
                <span class="btn-arrow-box">
                    <i class="fas fa-paper-plane"></i>
                </span>
            </button>
        </form>

        <!-- Volver al Login -->
        <div class="back-login">
            <a href="<?php echo e(route('login')); ?>">
                <i class="fas fa-arrow-left"></i>
                <span>Iniciar Sesión</span>
            </a>
        </div>

    </div>

</body>
</html>
<?php /**PATH /home/edwin/Desktop/UES II-2026/Diseño II/Proyecto/Dise-o_Sistemas2/resources/views/auth/forgot-password.blade.php ENDPATH**/ ?>