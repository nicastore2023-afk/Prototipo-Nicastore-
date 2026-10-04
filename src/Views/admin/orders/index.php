<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php $orders = $data['orders']; ?>
<?php $pagination = $data['pagination']; ?>

<div class="admin-header">
    <h1>Gestión de Pedidos</h1>
</div>

<div class="data-table">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><strong>#<?php echo $order['id']; ?></strong></td>
                    <td>
                        <div><?php echo htmlspecialchars($order['user_name']); ?></div>
                        <div style="color: var(--text-secondary); font-size: 0.85rem;"><?php echo htmlspecialchars($order['user_email']); ?></div>
                    </td>
                    <td><strong><?php echo number_format($order['total'], 2, ',', '.'); ?> €</strong></td>
                    <td>
                        <span class="badge badge-<?php echo $order['status'] == 'pending' ? 'warning' : ($order['status'] == 'delivered' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'info')); ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                    <td>
                        <a href="<?php echo AppConfig::url('/admin/pedidos/' . $order['id']); ?>" class="btn btn-sm btn-primary">Ver</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if ($pagination['last_page'] > 1): ?>
    <div class="pagination">
        <?php if ($pagination['current_page'] > 1): ?>
            <a href="<?php echo AppConfig::url('/admin/pedidos?page=' . ($pagination['current_page'] - 1)); ?>">← Anterior</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
            <?php if ($i == $pagination['current_page']): ?>
                <span class="active"><?php echo $i; ?></span>
            <?php else: ?>
                <a href="<?php echo AppConfig::url('/admin/pedidos?page=' . $i); ?>"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
            <a href="<?php echo AppConfig::url('/admin/pedidos?page=' . ($pagination['current_page'] + 1)); ?>">Siguiente →</a>
        <?php endif; ?>
    </div>
<?php endif; ?>