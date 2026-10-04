<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php
    $order = $data['order'];
    $items = $data['items'];
?>

<div class="container" style="padding: 40px 20px; max-width: 800px;">
    <div class="auth-card" style="max-width: 100%; text-align: center;">
        <div style="font-size: 4rem; margin-bottom: 20px;">✅</div>
        <h2>¡Pedido Confirmado!</h2>
        <p style="color: var(--text-secondary); margin-bottom: 30px;">
            Gracias por tu compra. Tu pedido #<?php echo $order['id']; ?> ha sido procesado correctamente.
        </p>
        
        <div class="coupon-display" style="text-align: left; margin-bottom: 30px;">
            <div>
                <h4>Número de Pedido: #<?php echo $order['id']; ?></h4>
                <p>Fecha: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
            </div>
            <div style="text-align: right;">
                <h4>Total: <?php echo number_format($order['total'], 2, ',', '.'); ?> €</h4>
                <p>Estado: <?php echo ucfirst($order['status']); ?></p>
            </div>
        </div>
        
        <div style="text-align: left; background: var(--bg-light); padding: 25px; border-radius: var(--radius); margin-bottom: 30px;">
            <h3 style="margin-bottom: 15px;">Productos</h3>
            <?php foreach ($items as $item): ?>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border);">
                    <div>
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                        <span style="color: var(--text-secondary);"> x<?php echo $item['quantity']; ?></span>
                    </div>
                    <span><?php echo number_format($item['total'], 2, ',', '.'); ?> €</span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: left; background: var(--bg-light); padding: 25px; border-radius: var(--radius); margin-bottom: 30px;">
            <h3 style="margin-bottom: 15px;">Dirección de Envío</h3>
            <p><?php echo htmlspecialchars($order['shipping_name']); ?></p>
            <p><?php echo htmlspecialchars($order['shipping_address']); ?></p>
            <p><?php echo htmlspecialchars($order['shipping_city'] ?? ''); ?>, <?php echo htmlspecialchars($order['shipping_state'] ?? ''); ?></p>
            <p><?php echo htmlspecialchars($order['shipping_zip'] ?? ''); ?> - <?php echo htmlspecialchars($order['shipping_country'] ?? 'España'); ?></p>
            <p style="color: var(--text-secondary);">Email: <?php echo htmlspecialchars($order['shipping_email']); ?></p>
        </div>
        
        <div style="display: flex; gap: 15px; justify-content: center;">
            <a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>" class="btn btn-primary">Ver Mis Pedidos</a>
            <a href="<?php echo AppConfig::url('/productos'); ?>" class="btn btn-secondary">Seguir Comprando</a>
        </div>
    </div>
</div>