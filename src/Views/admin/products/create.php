<?php
/** @var array $data */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php $categories = $data['categories']; ?>

<div class="admin-header">
    <h1>Nuevo Producto</h1>
    <a href="<?php echo AppConfig::url('/admin/productos'); ?>" class="btn btn-secondary">← Volver</a>
</div>

<div class="data-table" style="padding: 30px;">
    <form method="POST" action="<?php echo AppConfig::url('/admin/productos/guardar'); ?>" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-group">
            <label class="form-label">Nombre *</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Slug (URL amigable) *</label>
            <input type="text" name="slug" class="form-control" required placeholder="ej: camiseta-morada">
        </div>
        
        <div class="form-group">
            <label class="form-label">Categoría *</label>
            <select name="category_id" class="form-control" required>
                <option value="">Seleccionar categoría</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Descripción corta</label>
            <input type="text" name="short_description" class="form-control">
        </div>
        
        <div class="form-group">
            <label class="form-label">Descripción completa</label>
            <textarea name="description" class="form-control" rows="5"></textarea>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Precio *</label>
                <input type="number" name="price" class="form-control" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Precio Oferta</label>
                <input type="number" name="sale_price" class="form-control" step="0.01">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">SKU</label>
                <input type="text" name="sku" class="form-control">
            </div>
            
            <div class="form-group">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" value="0">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Imagen</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        
        <div class="form-group" style="display: flex; gap: 20px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_active" checked>
                <span>Activo</span>
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_featured">
                <span>Destacado</span>
            </label>
        </div>
        
        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Guardar Producto</button>
            <a href="<?php echo AppConfig::url('/admin/productos'); ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>