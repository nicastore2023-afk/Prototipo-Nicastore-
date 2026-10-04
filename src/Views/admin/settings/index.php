<?php
/** @var array $data */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php $settings = $data['settings']; ?>

<div class="admin-header">
    <h1>Configuración</h1>
</div>

<div class="data-table" style="padding: 30px;">
    <form method="POST" action="<?php echo AppConfig::url('/admin/configuracion/guardar'); ?>">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <h3 style="margin-bottom: 20px; color: var(--primary);">Información de la Tienda</h3>
        
        <div class="form-group">
            <label class="form-label">Nombre de la Tienda</label>
            <input type="text" name="store_name" class="form-control" value="<?php echo htmlspecialchars($settings['store_name'] ?? 'Nicastore'); ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Email de Contacto</label>
            <input type="email" name="store_email" class="form-control" value="<?php echo htmlspecialchars($settings['store_email'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Teléfono</label>
            <input type="text" name="store_phone" class="form-control" value="<?php echo htmlspecialchars($settings['store_phone'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Dirección</label>
            <textarea name="store_address" class="form-control" rows="3"><?php echo htmlspecialchars($settings['store_address'] ?? ''); ?></textarea>
        </div>
        
        <h3 style="margin: 30px 0 20px; color: var(--primary);">Configuración de Envíos e Impuestos</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Moneda</label>
                <select name="currency" class="form-control">
                    <option value="EUR" <?php echo ($settings['currency'] ?? 'EUR') == 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                    <option value="USD" <?php echo ($settings['currency'] ?? '') == 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                    <option value="GBP" <?php echo ($settings['currency'] ?? '') == 'GBP' ? 'selected' : ''; ?>>GBP (£)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Impuesto (%)</label>
                <input type="number" name="tax_rate" class="form-control" value="<?php echo htmlspecialchars($settings['tax_rate'] ?? '21'); ?>">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Coste de Envío (€)</label>
                <input type="number" name="shipping_cost" class="form-control" step="0.01" value="<?php echo htmlspecialchars($settings['shipping_cost'] ?? '5.99'); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Envío Gratis a partir de (€)</label>
                <input type="number" name="free_shipping_threshold" class="form-control" step="0.01" value="<?php echo htmlspecialchars($settings['free_shipping_threshold'] ?? '50'); ?>">
            </div>
        </div>
        
        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Guardar Configuración</button>
        </div>
    </form>
</div>