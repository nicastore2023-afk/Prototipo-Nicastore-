<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php
    $stats = $data['stats'];
    $recentOrders = $data['recentOrders'];
?>

<div class="admin-header">
    <h1>Dashboard</h1>
    <span><?php echo date('d/m/Y H:i'); ?></span>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card purple">
        <div class="stat-value"><?php echo $stats['users']; ?></div>
        <div class="stat-label">Usuarios</div>
    </div>
    <div class="stat-card pink">
        <div class="stat-value"><?php echo $stats['products']; ?></div>
        <div class="stat-label">Productos</div>
    </div>
    <div class="stat-card green">
        <div class="stat-value"><?php echo $stats['orders']; ?></div>
        <div class="stat-label">Pedidos</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-value"><?php echo number_format($stats['revenue'], 2, ',', '.'); ?> €</div>
        <div class="stat-label">Ingresos</div>
    </div>
</div>

<!-- Recent Orders -->
<div class="data-table">
    <table>
        <thead>
            <tr>
                <th>Pedido</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $order): ?>
                <tr>
                    <td><a href="<?php echo AppConfig::url('/admin/pedidos/' . $order['id']); ?>" style="color: var(--primary); font-weight: 500;">#<?php echo $order['id']; ?></a></td>
                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                    <td><strong><?php echo number_format($order['total'], 2, ',', '.'); ?> €</strong></td>
                    <td>
                        <span class="badge badge-<?php echo $order['status'] == 'pending' ? 'warning' : ($order['status'] == 'delivered' ? 'success' : 'info'); ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($stats['pending_orders'] > 0): ?>
    <div style="margin-top: 20px; padding: 15px 20px; background: #FFF3E0; border-radius: var(--radius-sm); border-left: 4px solid var(--warning);">
        <strong>Tienes <?php echo $stats['pending_orders']; ?> pedido<?php echo $stats['pending_orders'] > 1 ? 's' : ''; ?> pendiente<?php echo $stats['pending_orders'] > 1 ? 's' : ''; ?> de procesar</strong>
        <a href="<?php echo AppConfig::url('/admin/pedidos?status=pending'); ?>" style="color: var(--warning); margin-left: 10px;">Ver pedidos →</a>
    </div>
<?php endif; ?>