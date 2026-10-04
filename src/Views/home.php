<?php
/** @var array $data */
/** @var string $app_name */
/** @var string $csrf_token */
/** @var \Nicastore\Config\AppConfig $appConfig */
use Nicastore\Config\AppConfig;
?>

<?php $title = $data['title']; ?>

<!-- Hero -->
<section class="hero">
    <div class="container fade-in">
        <h1>Bienvenido a <?php echo htmlspecialchars($app_name); ?></h1>
        <p>Descubre productos únicos con estilo minimalista</p>
        <a href="<?php echo AppConfig::url('/productos'); ?>" class="btn btn-primary">Ver Productos</a>
    </div>
</section>

<!-- Categories -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Categorías</h2>
        <p class="section-subtitle">Explora nuestra selección de productos</p>
        
        <div class="category-grid">
            <?php foreach ($data['categories'] as $category): ?>
                <a href="<?php echo AppConfig::url('/categoria/' . $category['slug']); ?>" class="category-card fade-in">
                    <div class="category-icon">📦</div>
                    <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
                    <div class="category-count"><?php echo $category['product_count']; ?> productos</div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section" style="background: var(--bg-white);">
    <div class="container">
        <h2 class="section-title">Productos Destacados</h2>
        <p class="section-subtitle">Los más populares de nuestra tienda</p>
        
        <div class="product-grid">
            <?php foreach ($data['featuredProducts'] as $product): ?>
                <?php 
                    $price = $product['sale_price'] ?? $product['price'];
                    $originalPrice = $product['sale_price'] && $product['sale_price'] < $product['price'] ? $product['price'] : null;
                ?>
                <div class="product-card fade-in">
                    <div class="product-image">
                        <?php if ($product['image']): ?>
                            <img src="<?php echo AppConfig::asset('images/products/' . basename($product['image'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            📦
                        <?php endif; ?>
                        <?php if ($product['is_featured']): ?>
                            <span class="product-badge">Destacado</span>
                        <?php endif; ?>
                        <?php if ($originalPrice): ?>
                            <span class="product-badge sale-badge">Oferta</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></div>
                        <h3 class="product-title">
                            <a href="<?php echo AppConfig::url('/producto/' . $product['slug']); ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        <div class="product-price">
                            <span class="current-price"><?php echo number_format($price, 2, ',', '.'); ?> €</span>
                            <?php if ($originalPrice): ?>
                                <span class="original-price"><?php echo number_format($originalPrice, 2, ',', '.'); ?> €</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <form class="add-to-cart-form" action="<?php echo AppConfig::url('/carrito/añadir'); ?>" method="POST">
                                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary btn-sm">Añadir</button>
                            </form>
                            <a href="<?php echo AppConfig::url('/producto/' . $product['slug']); ?>" class="btn btn-secondary btn-sm">Ver más</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- New Products -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Novedades</h2>
        <p class="section-subtitle">Recién llegados a nuestra tienda</p>
        
        <div class="product-grid">
            <?php foreach ($data['newProducts'] as $product): ?>
                <?php 
                    $price = $product['sale_price'] ?? $product['price'];
                    $originalPrice = $product['sale_price'] && $product['sale_price'] < $product['price'] ? $product['price'] : null;
                ?>
                <div class="product-card fade-in">
                    <div class="product-image">
                        <?php if ($product['image']): ?>
                            <img src="<?php echo AppConfig::asset('images/products/' . basename($product['image'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            📦
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Nuevo</div>
                        <h3 class="product-title">
                            <a href="<?php echo AppConfig::url('/producto/' . $product['slug']); ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        <div class="product-price">
                            <span class="current-price"><?php echo number_format($price, 2, ',', '.'); ?> €</span>
                            <?php if ($originalPrice): ?>
                                <span class="original-price"><?php echo number_format($originalPrice, 2, ',', '.'); ?> €</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <form class="add-to-cart-form" action="<?php echo AppConfig::url('/carrito/añadir'); ?>" method="POST">
                                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary btn-sm">Añadir</button>
                            </form>
                            <a href="<?php echo AppConfig::url('/producto/' . $product['slug']); ?>" class="btn btn-secondary btn-sm">Ver más</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>