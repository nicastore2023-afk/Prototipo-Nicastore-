<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php
    $items = $data['items'];
    $subtotal = $data['subtotal'];
    $shipping = $data['shipping'];
    $tax = $data['tax'];
    $total = $data['total'];
?>

<div class="container" style="padding: 40px 20px;">
    <div class="breadcrumb">
        <a href="<?php echo AppConfig::url('/'); ?>">Inicio</a>
        <span>/</span>
        <span>Carrito de Compras</span>
    </div>
    
    <h1 class="section-title" style="text-align: left; margin: 20px 0;">Carrito de Compras (<?php echo $data['count']; ?>)</h1>
    
    <?php if (empty($items)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🛒</div>
            <h3>Tu carrito está vacío</h3>
            <p>Descubre nuestros productos y añade los que más te gusten</p>
            <a href="<?php echo AppConfig::url('/productos'); ?>" class="btn btn-primary" style="margin-top: 20px;">Ver Productos</a>
        </div>
    <?php else: ?>
        <div class="checkout-grid">
            <!-- Cart Items -->
            <div>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php
                                $itemPrice = $item['on_sale'] ? $item['sale_price'] : $item['price'];
                                $itemTotal = $itemPrice * $item['quantity'];
                            ?>
                            <tr>
                                <td data-label="Producto">
                                    <div class="cart-item">
                                        <div class="cart-item-image">
                                            <?php if ($item['image']): ?>
                                                <img src="<?php echo AppConfig::asset('images/products/' . basename($item['image'])); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                            <?php else: ?>
                                                📦
                                            <?php endif; ?>
                                        </div>
                                        <div class="cart-item-info">
                                            <h3>
                                                <a href="<?php echo AppConfig::url('/producto/' . $item['slug']); ?>">
                                                    <?php echo htmlspecialchars($item['name']); ?>
                                                </a>
                                            </h3>
                                            <span class="cart-item-price"><?php echo number_format($itemPrice, 2, ',', '.'); ?> €</span>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Precio"><?php echo number_format($itemPrice, 2, ',', '.'); ?> €</td>
                                <td data-label="Cantidad">
                                    <form class="update-cart-form" data-item-id="<?php echo $item['id']; ?>" style="display: inline;">
                                        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <div class="quantity-controls">
                                            <button type="button" class="qty-btn" onclick="this.nextElementSibling.stepDown()">−</button>
                                            <input type="number" name="quantity" class="qty-input" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>">
                                            <button type="button" class="qty-btn" onclick="this.previousElementSibling.stepUp()">+</button>
                                        </div>
                                    </form>
                                </td>
                                <td data-label="Total"><strong><?php echo number_format($itemTotal, 2, ',', '.'); ?> €</strong></td>
                                <td data-label="">
                                    <form class="update-cart-form" data-item-id="<?php echo $item['id']; ?>" style="display: inline;">
                                        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <button type="button" class="remove-item btn btn-danger btn-sm" style="background: none; color: var(--error); padding: 5px;">🗑</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Summary -->
            <div class="cart-summary">
                <h3 style="margin-bottom: 20px;">Resumen</h3>
                
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="cart-subtotal"><?php echo number_format($subtotal, 2, ',', '.'); ?> €</span>
                </div>
                
                <div class="summary-row">
                    <span>Envío</span>
                    <span id="cart-shipping"><?php echo $shipping == 0 ? 'Gratis' : number_format($shipping, 2, ',', '.') . ' €'; ?></span>
                </div>
                
                <div class="summary-row">
                    <span>Impuestos (21%)</span>
                    <span id="cart-tax"><?php echo number_format($tax, 2, ',', '.'); ?> €</span>
                </div>
                
                <?php if (Session::has('applied_coupon')): ?>
                    <?php $coupon = Session::get('applied_coupon'); ?>
                    <div class="summary-row" style="color: var(--success);">
                        <span>Cupón (<?php echo htmlspecialchars($coupon['code']); ?>)</span>
                        <span>-<?php echo number_format($coupon['discount'], 2, ',', '.'); ?> €</span>
                    </div>
                <?php endif; ?>
                
                <div class="summary-row total">
                    <span>Total</span>
                    <span id="cart-total"><?php echo number_format($total, 2, ',', '.'); ?> €</span>
                </div>
                
                <a href="<?php echo AppConfig::url('/checkout'); ?>" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                    Finalizar Compra
                </a>
                
                <!-- Coupon -->
                <form id="coupon-form" action="<?php echo AppConfig::url('/carrito/cupon'); ?>" method="POST" class="coupon-form">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <input type="text" name="coupon_code" class="form-control" placeholder="Código de cupón">
                    <button type="submit" class="btn btn-secondary btn-sm">Aplicar</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('change', function() {
        const form = this.closest('.update-cart-form');
        const itemId = form.dataset.itemId;
        const quantity = this.value;
        
        updateCartItem(itemId, quantity);
    });
});

document.querySelectorAll('.remove-item').forEach(button => {
    button.addEventListener('click', function() {
        const form = this.closest('.update-cart-form');
        const itemId = form.dataset.itemId;
        removeCartItem(itemId);
    });
});
</script>