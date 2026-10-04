<?php
/** @var array $data */
/** @var string $app_name */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php $title = $data['title']; ?>

<div class="container" style="padding: 40px 20px;">
    <div class="breadcrumb">
        <a href="<?php echo AppConfig::url('/'); ?>">Inicio</a>
        <span>/</span>
        <span>Productos</span>
    </div>
    
    <h1 class="section-title" style="text-align: left; margin: 20px 0 10px;"><?php echo htmlspecialchars($title); ?></h1>
    
    <!-- Search -->
    <form method="GET" action="<?php echo AppConfig::url('/buscar'); ?>" class="search-bar">
        <input type="text" name="q" class="form-control" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
    
    <!-- Categories Filter -->
    <div class="category-grid" style="margin-bottom: 40px; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));">
        <?php foreach ($data['categories'] as $category): ?>
            <a href="<?php echo AppConfig::url('/categoria/' . $category['slug']); ?>" class="category-card">
                <div class="category-icon">📦</div>
                <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
            </a>
        <?php endforeach; ?>
    </div>
    
    <!-- Products -->
    <?php if (empty($data['products'])): ?>
        <div class="empty-state">
            <div class="empty-state-icon">📦</div>
            <h3>No hay productos disponibles</h3>
            <p>Vuelve pronto para ver nuestras novedades</p>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($data['products'] as $product): ?>
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
                        <?php if ($originalPrice): ?>
                            <span class="product-badge sale-badge">Oferta</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Producto</div>
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
        
        <!-- Pagination -->
        <?php if (isset($data['pagination']) && $data['pagination']['last_page'] > 1): ?>
            <?php
                $currentPage = $data['pagination']['current_page'];
                $lastPage = $data['pagination']['last_page'];
            ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="<?php echo AppConfig::url('/productos?page=' . ($currentPage - 1)); ?>">← Anterior</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $lastPage; $i++): ?>
                    <?php if ($i == $currentPage): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo AppConfig::url('/productos?page=' . $i); ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($currentPage < $lastPage): ?>
                    <a href="<?php echo AppConfig::url('/productos?page=' . ($currentPage + 1)); ?>">Siguiente →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>