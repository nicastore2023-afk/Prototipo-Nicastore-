<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php $user = $data['user']; ?>

<div class="container" style="padding: 40px 20px;">
    <h1 class="section-title" style="text-align: left; margin-bottom: 30px;">Mi Cuenta</h1>
    
    <div class="account-layout">
        <!-- Sidebar -->
        <aside class="account-sidebar">
            <div class="account-sidebar-header">
                <div class="account-avatar">👤</div>
                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                <p style="opacity: 0.9; font-size: 0.9rem;"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <ul class="account-nav">
                <li><a href="<?php echo AppConfig::url('/cuenta'); ?>" class="active">📊 Panel</a></li>
                <li><a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>">📦 Mis Pedidos</a></li>
                <li><a href="<?php echo AppConfig::url('/cuenta/perfil'); ?>">⚙️ Mi Perfil</a></li>
                <li><a href="<?php echo AppConfig::url('/logout'); ?>">🚪 Cerrar Sesión</a></li>
            </ul>
        </aside>
        
        <!-- Content -->
        <div class="account-content">
            <h2 style="margin-bottom: 25px;">Panel de Control</h2>
            
            <div class="stats-grid">
                <div class="stat-card purple">
                    <div class="stat-value"><?php echo count($data['orders']); ?></div>
                    <div class="stat-label">Pedidos Recientes</div>
                </div>
                <div class="stat-card pink">
                    <div class="stat-value">
                        <?php
                            $cart = new \Nicastore\Models\Cart();
                            echo $cart->getCount();
                        ?>
                    </div>
                    <div class="stat-label">En el Carrito</div>
                </div>
            </div>
            
            <h3 style="margin: 30px 0 20px;">Pedidos Recientes</h3>
            
            <?php if (empty($data['orders'])): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <h3>No tienes pedidos</h3>
                    <p>Cuando realices tu primera compra aparecerá aquí</p>
                    <a href="<?php echo AppConfig::url('/productos'); ?>" class="btn btn-primary" style="margin-top: 15px;">Ir a la Tienda</a>
                </div>
            <?php else: ?>
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['orders'] as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                    <td><strong><?php echo number_format($order['total'], 2, ',', '.'); ?> €</strong></td>
                                    <td>
                                        <span class="badge badge-<?php echo $order['status'] == 'pending' ? 'warning' : ($order['status'] == 'delivered' ? 'success' : 'info'); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo AppConfig::url('/cuenta/pedido/' . $order['id']); ?>" class="btn btn-sm btn-primary">Ver</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>" class="btn btn-secondary" style="margin-top: 20px;">Ver Todos los Pedidos</a>
            <?php endif; ?>
        </div>
    </div>
</div>