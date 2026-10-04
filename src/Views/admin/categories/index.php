<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php $categories = $data['categories']; ?>

<div class="admin-header">
    <h1>Gestión de Categorías</h1>
</div>

<div class="data-table">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Slug</th>
                <th>Descripción</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td>#<?php echo $category['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($category['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($category['slug']); ?></td>
                    <td><?php echo htmlspecialchars($category['description'] ?? ''); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $category['is_active'] ? 'success' : 'danger'; ?>">
                            <?php echo $category['is_active'] ? 'Activa' : 'Inactiva'; ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Category Form -->
<div class="data-table" style="padding: 25px; margin-top: 30px;">
    <h3 style="margin-bottom: 20px;">Añadir Categoría</h3>
    <form method="POST" action="<?php echo AppConfig::url('/admin/categorias/guardar'); ?>">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 15px; align-items: end;">
            <div class="form-group" style="margin: 0;">
                <label class="form-label">Nombre</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group" style="margin: 0;">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Añadir</button>
        </div>
    </form>
</div>