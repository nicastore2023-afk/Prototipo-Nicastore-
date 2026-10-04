<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php
    $product = $data['product'];
    $price = $product['sale_price'] ?? $product['price'];
    $originalPrice = $product['sale_price'] && $product['sale_price'] < $product['price'] ? $product['price'] : null;
?>

<div class="container" style="padding: 40px 20px;">
    <div class="breadcrumb">
        <a href="<?php echo AppConfig::url('/'); ?>">Inicio</a>
        <span>/</span>
        <a href="<?php echo AppConfig::url('/productos'); ?>">Productos</a>
        <span>/</span>
        <span><?php echo htmlspecialchars($product['name']); ?></span>
    </div>
    
    <div class="product-detail">
        <!-- Gallery -->
        <div class="product-gallery">
            <div class="main-image">
                <?php if ($product['image']): ?>
                    <img src="<?php echo AppConfig::asset('images/products/' . basename($product['image'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    📦
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Info -->
        <div class="product-info-detail">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="product-meta">
                <span>SKU: <?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></span>
                <span>Categoría: <?php echo htmlspecialchars($product['category_id']); ?></span>
            </div>
            
            <div class="product-price-detail">
                <span class="current"><?php echo number_format($price, 2, ',', '.'); ?> €</span>
                <?php if ($originalPrice): ?>
                    <span class="original"><?php echo number_format($originalPrice, 2, ',', '.'); ?> €</span>
                <?php endif; ?>
            </div>
            
            <div class="stock-info <?php echo $product['stock'] > 10 ? 'stock-in' : ($product['stock'] > 0 ? 'stock-low' : 'stock-out'); ?>">
                <?php if ($product['stock'] > 10): ?>
                    ✓ En stock
                <?php elseif ($product['stock'] > 0): ?>
                    ⚠ Solo quedan <?php echo $product['stock']; ?> unidades
                <?php else: ?>
                    ✗ Agotado
                <?php endif; ?>
            </div>
            
            <p class="product-description"><?php echo nl2br(htmlspecialchars($product['description'] ?? $product['short_description'] ?? '')); ?></p>
            
            <?php if ($product['stock'] > 0): ?>
                <form class="add-to-cart-form" action="<?php echo AppConfig::url('/carrito/añadir'); ?>" method="POST">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="form-group">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary" <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>>
                        Añadir al Carrito
                    </button>
                </form>
            <?php else: ?>
                <button class="btn btn-primary" disabled>Producto Agotado</button>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($data['relatedProducts'])): ?>
        <section class="section">
            <h2 class="section-title">Productos Relacionados</h2>
            <div class="product-grid">
                <?php foreach ($data['relatedProducts'] as $related): ?>
                    <?php 
                        $rPrice = $related['sale_price'] ?? $related['price'];
                        $rOriginalPrice = $related['sale_price'] && $related['sale_price'] < $related['price'] ? $related['price'] : null;
                    ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($related['image']): ?>
                                <img src="<?php echo AppConfig::asset('images/products/' . basename($related['image'])); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                            <?php else: ?>
                                📦
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">
                                <a href="<?php echo AppConfig::url('/producto/' . $related['slug']); ?>">
                                    <?php echo htmlspecialchars($related['name']); ?>
                                </a>
                            </h3>
                            <div class="product-price">
                                <span class="current-price"><?php echo number_format($rPrice, 2, ',', '.'); ?> €</span>
                                <?php if ($rOriginalPrice): ?>
                                    <span class="original-price"><?php echo number_format($rOriginalPrice, 2, ',', '.'); ?> €</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>