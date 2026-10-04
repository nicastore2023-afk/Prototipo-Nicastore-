<?php
/** @var string $app_name */
/** @var string $title */
/** @var array|null $flash */
use Nicastore\Config\AppConfig;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($app_name); ?> - Tu tienda online">
    <title><?php echo htmlspecialchars($title ?? $app_name); ?> - <?php echo htmlspecialchars($app_name); ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(AppConfig::asset('css/style.css')); ?>">
</head>
<body>
    <?php if (isset($flash) && $flash): ?>
        <div class="flash-messages">
            <div class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="<?php echo AppConfig::url('/'); ?>" class="logo">Nicastore</a>
            
            <ul class="nav-menu">
                <li><a href="<?php echo AppConfig::url('/'); ?>">Inicio</a></li>
                <li><a href="<?php echo AppConfig::url('/productos'); ?>">Productos</a></li>
                <li><a href="<?php echo AppConfig::url('/productos'); ?>">Categorías</a></li>
                <?php if ($user): ?>
                    <li><a href="<?php echo AppConfig::url('/cuenta'); ?>">Mi cuenta</a></li>
                    <li><a href="<?php echo AppConfig::url('/logout'); ?>">Cerrar sesión</a></li>
                <?php else: ?>
                    <li><a href="<?php echo AppConfig::url('/login'); ?>">Iniciar sesión</a></li>
                <?php endif; ?>
            </ul>
            
            <div class="nav-icons">
                <a href="<?php echo AppConfig::url('/carrito'); ?>" class="nav-icon">
                    🛒
                    <?php
                        $cart = new \Nicastore\Models\Cart();
                        $count = $cart->getCount();
                    ?>
                    <?php if ($count > 0): ?>
                        <span class="badge" style="display:flex;"><?php echo $count; ?></span>
                    <?php endif; ?>
                </a>
                
                <div class="hamburger" onclick="document.querySelector('.nav-menu').classList.toggle('active')">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main>
        <?php require $viewPath; ?>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="logo">Nicastore</div>
                    <p>Tu tienda online minimalista con los mejores productos en moda, electrónica, hogar y más.</p>
                </div>
                <div>
                    <h4>Enlaces</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo AppConfig::url('/'); ?>">Inicio</a></li>
                        <li><a href="<?php echo AppConfig::url('/productos'); ?>">Productos</a></li>
                        <li><a href="<?php echo AppConfig::url('/carrito'); ?>">Carrito</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Cuenta</h4>
                    <ul class="footer-links">
                        <?php if ($user): ?>
                            <li><a href="<?php echo AppConfig::url('/cuenta'); ?>">Mi cuenta</a></li>
                            <li><a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>">Mis pedidos</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo AppConfig::url('/login'); ?>">Iniciar sesión</a></li>
                            <li><a href="<?php echo AppConfig::url('/register'); ?>">Registrarse</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div>
                    <h4>Contacto</h4>
                    <ul class="footer-links">
                        <li>info@nicastore.com</li>
                        <li>+34 123 456 789</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($app_name); ?>. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
    
    <script src="<?php echo htmlspecialchars(AppConfig::asset('js/main.js')); ?>"></script>
</body>
</html>