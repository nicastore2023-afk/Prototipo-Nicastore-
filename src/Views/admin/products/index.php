<?php
/** @var array $data */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php $products = $data['products']; ?>
<?php $pagination = $data['pagination']; ?>

<div class="admin-header">
    <h1>Gestión de Productos</h1>
    <a href="<?php echo AppConfig::url('/admin/productos/nuevo'); ?>" class="btn btn-primary">+ Nuevo Producto</a>
</div>

<div class="data-table">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>#<?php echo $product['id']; ?></td>
                    <td>
                        <div style="width: 50px; height: 50px; border-radius: var(--radius-sm); background: linear-gradient(135deg, var(--purple-soft), var(--pink-soft)); display: flex; align-items: center; justify-content: center;">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo AppConfig::asset('images/products/' . basename($product['image'])); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-sm);">
                            <?php else: ?>
                                📦
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                        <div style="color: var(--text-secondary); font-size: 0.85rem;"><?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></div>
                    </td>
                    <td>
                        <strong><?php echo number_format($product['sale_price'] ?? $product['price'], 2, ',', '.'); ?> €</strong>
                        <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                            <div style="color: var(--error); text-decoration: line-through; font-size: 0.85rem;"><?php echo number_format($product['price'], 2, ',', '.'); ?> €</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-<?php echo $product['stock'] > 10 ? 'success' : ($product['stock'] > 0 ? 'warning' : 'danger'); ?>">
                            <?php echo $product['stock']; ?> unidades
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?php echo $product['is_active'] ? 'success' : 'danger'; ?>">
                            <?php echo $product['is_active'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="<?php echo AppConfig::url('/admin/productos/editar/' . $product['id']); ?>" class="btn btn-sm btn-primary">Editar</a>
                            <form method="POST" action="<?php echo AppConfig::url('/admin/productos/eliminar/' . $product['id']); ?>" style="display: inline;" onsubmit="return confirm('¿Eliminar este producto?')">
                                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        </div>
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
            <a href="<?php echo AppConfig::url('/admin/productos?page=' . ($pagination['current_page'] - 1)); ?>">← Anterior</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
            <?php if ($i == $pagination['current_page']): ?>
                <span class="active"><?php echo $i; ?></span>
            <?php else: ?>
                <a href="<?php echo AppConfig::url('/admin/productos?page=' . $i); ?>"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
            <a href="<?php echo AppConfig::url('/admin/productos?page=' . ($pagination['current_page'] + 1)); ?>">Siguiente →</a>
        <?php endif; ?>
    </div>
<?php endif; ?>