<?php
/** @var string $app_name */
use Nicastore\Config\AppConfig;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página No Encontrada - <?php echo htmlspecialchars($app_name ?? 'Nicastore'); ?></title>
    <link rel="stylesheet" href="<?php echo AppConfig::asset('css/style.css'); ?>">
</head>
<body style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--primary-dark), var(--secondary-dark));">
    <div style="text-align: center; color: white; padding: 40px;">
        <h1 style="font-size: 6rem; margin-bottom: 20px;">404</h1>
        <h2 style="margin-bottom: 15px;">Página No Encontrada</h2>
        <p style="margin-bottom: 30px; opacity: 0.9;">Lo sentimos, la página que buscas no existe.</p>
        <a href="<?php echo AppConfig::url('/'); ?>" class="btn btn-primary">Volver al Inicio</a>
    </div>
</body>
</html>