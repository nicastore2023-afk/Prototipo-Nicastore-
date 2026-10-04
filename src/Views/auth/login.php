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
    <title><?php echo htmlspecialchars($title ?? 'Iniciar Sesión'); ?> - <?php echo htmlspecialchars($app_name); ?></title>
    <link rel="stylesheet" href="<?php echo AppConfig::asset('css/style.css'); ?>">
</head>
<body class="auth-page">
    <div class="auth-card fade-in">
        <h2>Iniciar Sesión</h2>
        <p class="auth-subtitle">Accede a tu cuenta de <?php echo htmlspecialchars($app_name); ?></p>
        
        <?php if (isset($flash) && $flash): ?>
            <div class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>" style="margin-bottom: 20px;">
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo AppConfig::url('/login'); ?>">
            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required autofocus placeholder="tu@email.com">
            </div>
            
            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="remember" id="remember" style="width: auto;">
                <label for="remember" style="margin: 0; font-weight: normal;">Recordarme</label>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Iniciar Sesión</button>
        </form>
        
        <div class="auth-footer">
            <p>¿No tienes cuenta? <a href="<?php echo AppConfig::url('/register'); ?>">Regístrate</a></p>
            <p style="margin-top: 8px; font-size: 0.85rem;">
                Demo: admin@nicastore.com / password123<br>
                Cliente: customer@nicastore.com / password123
            </p>
        </div>
    </div>
</body>
</html>