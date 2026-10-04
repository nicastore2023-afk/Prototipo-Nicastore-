<?php
/** @var string $title */
/** @var string $app_name */
use Nicastore\Config\AppConfig;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Registrarse'); ?> - <?php echo htmlspecialchars($app_name); ?></title>
    <link rel="stylesheet" href="<?php echo AppConfig::asset('css/style.css'); ?>">
</head>
<body class="auth-page">
    <div class="auth-card fade-in">
        <h2>Crear Cuenta</h2>
        <p class="auth-subtitle">Únete a <?php echo htmlspecialchars($app_name); ?></p>
        
        <?php if (isset($flash) && $flash): ?>
            <div class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>" style="margin-bottom: 20px;">
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo AppConfig::url('/register'); ?>">
            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            
            <div class="form-group">
                <label class="form-label">Nombre completo</label>
                <input type="text" name="name" class="form-control" required autofocus placeholder="Tu nombre">
            </div>
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required placeholder="tu@email.com">
            </div>
            
            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required placeholder="Mínimo 8 caracteres" minlength="8">
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirmar contraseña</label>
                <input type="password" name="password_confirm" class="form-control" required placeholder="Repite tu contraseña">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Registrarse</button>
        </form>
        
        <div class="auth-footer">
            <p>¿Ya tienes cuenta? <a href="<?php echo AppConfig::url('/login'); ?>">Inicia sesión</a></p>
        </div>
    </div>
</body>
</html>