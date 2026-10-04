<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php
    $order = $data['order'];
    $items = $data['items'];
?>

<div class="admin-header">
    <h1>Pedido #<?php echo $order['id']; ?></h1>
    <a href="<?php echo AppConfig::url('/admin/pedidos'); ?>" class="btn btn-secondary">← Volver</a>
</div>

<div class="checkout-grid">
    <div>
        <!-- Order Info -->
        <div class="data-table" style="padding: 25px; margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px; color: var(--primary);">Información del Pedido</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <strong>Cliente:</strong> <?php echo htmlspecialchars($order['user_name']); ?><br>
                    <strong>Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?><br>
                    <strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                </div>
                <div>
                    <strong>Total:</strong> <?php echo number_format($order['total'], 2, ',', '.'); ?> €<br>
                    <strong>Subtotal:</strong> <?php echo number_format($order['subtotal'], 2, ',', '.'); ?> €<br>
                    <strong>Estado:</strong>
                    <span class="badge badge-<?php echo $order['status'] == 'pending' ? 'warning' : ($order['status'] == 'delivered' ? 'success' : 'info'); ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Shipping -->
        <div class="data-table" style="padding: 25px;">
            <h3 style="margin-bottom: 20px; color: var(--primary);">Dirección de Envío</h3>
            <p><?php echo htmlspecialchars($order['shipping_name']); ?></p>
            <p><?php echo htmlspecialchars($order['shipping_address']); ?></p>
            <p><?php echo htmlspecialchars($order['shipping_city'] ?? ''); ?>, <?php echo htmlspecialchars($order['shipping_state'] ?? ''); ?></p>
            <p><?php echo htmlspecialchars($order['shipping_zip'] ?? ''); ?> - <?php echo htmlspecialchars($order['shipping_country'] ?? 'España'); ?></p>
            <p style="color: var(--text-secondary);">Email: <?php echo htmlspecialchars($order['shipping_email']); ?></p>
            <?php if ($order['shipping_phone']): ?>
                <p style="color: var(--text-secondary);">Tel: <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
            <?php endif; ?>
            <?php if ($order['notes']): ?>
                <div style="margin-top: 15px; padding: 10px; background: var(--bg-light); border-radius: var(--radius-sm);">
                    <strong>Notas:</strong> <?php echo htmlspecialchars($order['notes']); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Items & Actions -->
    <div>
        <!-- Items -->
        <div class="data-table" style="padding: 25px; margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px; color: var(--primary);">Productos</h3>
            
            <?php foreach ($items as $item): ?>
                <div style="display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border);">
                    <div style="width: 50px; height: 50px; border-radius: var(--radius-sm); background: linear-gradient(135deg, var(--purple-soft), var(--pink-soft)); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <?php if ($item['image']): ?>
                            <img src="<?php echo AppConfig::asset('images/products/' . basename($item['image'])); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-sm);">
                        <?php else: ?>
                            📦
                        <?php endif; ?>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500;"><?php echo htmlspecialchars($item['name']); ?></div>
                        <div style="color: var(--text-secondary); font-size: 0.85rem;">Cant: <?php echo $item['quantity']; ?> x <?php echo number_format($item['price'], 2, ',', '.'); ?> €</div>
                    </div>
                    <div style="font-weight: 600;"><?php echo number_format($item['total'], 2, ',', '.'); ?> €</div>
                </div>
            <?php endforeach; ?>
            
            <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid var(--border);">
                <div style="display: flex; justify-content: space-between; padding: 5px 0;">
                    <span>Subtotal</span>
                    <span><?php echo number_format($order['subtotal'], 2, ',', '.'); ?> €</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 5px 0;">
                    <span>Envío</span>
                    <span><?php echo number_format($order['shipping'], 2, ',', '.'); ?> €</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 5px 0;">
                    <span>Impuestos</span>
                    <span><?php echo number_format($order['tax'], 2, ',', '.'); ?> €</span>
                </div>
                <?php if ($order['discount'] > 0): ?>
                    <div style="display: flex; justify-content: space-between; padding: 5px 0; color: var(--success);">
                        <span>Descuento</span>
                        <span>-<?php echo number_format($order['discount'], 2, ',', '.'); ?> €</span>
                    </div>
                <?php endif; ?>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; font-size: 1.1rem; font-weight: 700; color: var(--secondary);">
                    <span>Total</span>
                    <span><?php echo number_format($order['total'], 2, ',', '.'); ?> €</span>
                </div>
            </div>
        </div>
        
        <!-- Update Status -->
        <div class="data-table" style="padding: 25px;">
            <h3 style="margin-bottom: 20px; color: var(--primary);">Cambiar Estado</h3>
            <form method="POST" action="<?php echo AppConfig::url('/admin/pedidos/' . $order['id'] . '/estado'); ?>">
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <div class="form-group">
                    <select name="status" class="form-control">
                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Procesando</option>
                        <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Enviado</option>
                        <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Entregado</option>
                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Actualizar Estado</button>
            </form>
        </div>
    </div>
</div>