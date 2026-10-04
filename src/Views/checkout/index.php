<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php
    $items = $data['items'];
    $subtotal = $data['subtotal'];
    $shipping = $data['shipping'];
    $tax = $data['tax'];
    $discount = $data['discount'];
    $total = $data['total'];
    $user = $data['user'];
?>

<div class="container" style="padding: 40px 20px;">
    <div class="breadcrumb">
        <a href="<?php echo AppConfig::url('/'); ?>">Inicio</a>
        <span>/</span>
        <a href="<?php echo AppConfig::url('/carrito'); ?>">Carrito</a>
        <span>/</span>
        <span>Finalizar Compra</span>
    </div>
    
    <h1 class="section-title" style="text-align: left; margin: 20px 0;">Finalizar Compra</h1>
    
    <form method="POST" action="<?php echo AppConfig::url('/checkout/procesar'); ?>">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="checkout-grid">
            <div>
                <!-- Shipping Info -->
                <div class="checkout-form" style="margin-bottom: 30px;">
                    <h3>📦 Información de Envío</h3>
                    
                    <div class="form-group">
                        <label class="form-label">Nombre completo *</label>
                        <input type="text" name="shipping_name" class="form-control" required value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="shipping_email" class="form-control" required value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" name="shipping_phone" class="form-control" placeholder="+34 123 456 789">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Dirección *</label>
                        <input type="text" name="shipping_address" class="form-control" required placeholder="Calle, número, piso">
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label class="form-label">Ciudad *</label>
                            <input type="text" name="shipping_city" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Provincia</label>
                            <input type="text" name="shipping_state" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Código Postal *</label>
                            <input type="text" name="shipping_zip" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">País</label>
                        <input type="text" name="shipping_country" class="form-control" value="España">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Notas del pedido</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Instrucciones especiales de entrega..."></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Summary -->
            <div>
                <div class="checkout-summary">
                    <h3 style="margin-bottom: 20px;">Resumen del Pedido</h3>
                    
                    <?php foreach ($items as $item): ?>
                        <?php
                            $itemPrice = $item['on_sale'] ? $item['sale_price'] : $item['price'];
                            $itemTotal = $itemPrice * $item['quantity'];
                        ?>
                        <div style="display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border);">
                            <div style="width: 50px; height: 50px; border-radius: var(--radius-sm); background: linear-gradient(135deg, var(--purple-soft), var(--pink-soft)); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <?php if ($item['image']): ?>
                                    <img src="<?php echo AppConfig::asset('images/products/' . basename($item['image'])); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-sm);">
                                <?php else: ?>
                                    📦
                                <?php endif; ?>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 500; font-size: 0.9rem;"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div style="color: var(--text-secondary); font-size: 0.85rem;">Cant: <?php echo $item['quantity']; ?></div>
                            </div>
                            <div style="font-weight: 600;"><?php echo number_format($itemTotal, 2, ',', '.'); ?> €</div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="margin-top: 20px;">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span><?php echo number_format($subtotal, 2, ',', '.'); ?> €</span>
                        </div>
                        <div class="summary-row">
                            <span>Envío</span>
                            <span><?php echo $shipping == 0 ? 'Gratis' : number_format($shipping, 2, ',', '.') . ' €'; ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Impuestos (21%)</span>
                            <span><?php echo number_format($tax, 2, ',', '.'); ?> €</span>
                        </div>
                        <?php if ($discount > 0): ?>
                            <div class="summary-row" style="color: var(--success);">
                                <span>Descuento</span>
                                <span>-<?php echo number_format($discount, 2, ',', '.'); ?> €</span>
                            </div>
                        <?php endif; ?>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span><?php echo number_format($total, 2, ',', '.'); ?> €</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                        Realizar Pedido
                    </button>
                    
                    <a href="<?php echo AppConfig::url('/carrito'); ?>" style="display: block; text-align: center; margin-top: 15px; color: var(--text-secondary);">
                        ← Volver al carrito
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>