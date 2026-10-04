<?php
/** @var string $app_name */
/** @var string $title */
use Nicastore\Config\AppConfig;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Panel'); ?> - <?php echo htmlspecialchars($app_name); ?></title>
    <link rel="stylesheet" href="<?php echo AppConfig::asset('css/style.css'); ?>">
    <style>
        .admin-layout { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: var(--text-primary); color: white; padding: 20px 0; flex-shrink: 0; }
        .admin-sidebar .brand { padding: 0 20px 20px; font-size: 1.4rem; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .admin-nav { list-style: none; }
        .admin-nav li a { display: flex; align-items: center; gap: 12px; padding: 14px 20px; color: rgba(255,255,255,0.7); font-size: 0.95rem; }
        .admin-nav li a:hover, .admin-nav li a.active { background: rgba(255,255,255,0.1); color: white; border-left: 3px solid var(--secondary); }
        .admin-main { flex: 1; background: var(--bg-light); padding: 30px; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .admin-header h1 { font-size: 1.8rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--bg-white); padding: 25px; border-radius: var(--radius); box-shadow: var(--shadow); border-left: 4px solid var(--primary); }
        .stat-card.purple { border-left-color: var(--primary); }
        .stat-card.pink { border-left-color: var(--secondary); }
        .stat-card.green { border-left-color: var(--success); }
        .stat-card.orange { border-left-color: var(--warning); }
        .stat-value { font-size: 2rem; font-weight: 700; margin-bottom: 5px; }
        .stat-label { color: var(--text-secondary); font-size: 0.9rem; }
        .data-table { width: 100%; background: var(--bg-white); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
        .data-table table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 15px 20px; text-align: left; border-bottom: 1px solid var(--border); }
        .data-table th { background: var(--bg-light); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); }
        .data-table tr:hover { background: rgba(107, 47, 160, 0.02); }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 500; }
        .badge-primary { background: var(--purple-soft); color: var(--primary-dark); }
        .badge-success { background: #E8F5E9; color: var(--success); }
        .badge-warning { background: #FFF3E0; color: var(--warning); }
        .badge-danger { background: #FFEBEE; color: var(--error); }
        .badge-info { background: #E3F2FD; color: #1976D2; }
        @media (max-width: 768px) {
            .admin-layout { flex-direction: column; }
            .admin-sidebar { width: 100%; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="brand"><?php echo htmlspecialchars($app_name); ?> Admin</div>
            <ul class="admin-nav">
                <li><a href="<?php echo AppConfig::url('/admin'); ?>" class="<?php echo str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin') && !str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/') ? 'active' : '' ?>">📊 Dashboard</a></li>
                <li><a href="<?php echo AppConfig::url('/admin/productos'); ?>" class="<?php echo str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/productos') ? 'active' : '' ?>">📦 Productos</a></li>
                <li><a href="<?php echo AppConfig::url('/admin/categorias'); ?>" class="<?php echo str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/categorias') ? 'active' : '' ?>">📁 Categorías</a></li>
                <li><a href="<?php echo AppConfig::url('/admin/pedidos'); ?>" class="<?php echo str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/pedidos') ? 'active' : '' ?>">📋 Pedidos</a></li>
                <li><a href="<?php echo AppConfig::url('/admin/configuracion'); ?>" class="<?php echo str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/configuracion') ? 'active' : '' ?>">⚙️ Configuración</a></li>
                <li><a href="<?php echo AppConfig::url('/'); ?>">🏪 Ver Tienda</a></li>
                <li><a href="<?php echo AppConfig::url('/logout'); ?>">🚪 Cerrar Sesión</a></li>
            </ul>
        </aside>
        
        <main class="admin-main">
            <?php require $viewPath; ?>
        </main>
    </div>
    
    <script src="<?php echo AppConfig::asset('js/main.js'); ?>"></script>
</body>
</html>