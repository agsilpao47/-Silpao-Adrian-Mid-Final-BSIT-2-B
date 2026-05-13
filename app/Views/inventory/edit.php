<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Edit Product') ?></h1>
    <a href="<?= site_url('stock/inventory') ?>" class="btn btn-secondary">← Back to List</a>
</div>

<div class="form-container">
    <form action="<?= site_url('stock/inventory/edit/' . $product->id) ?>" method="post" class="product-form">
        <?= csrf_field() ?>
        
        <div class="form-row">
            <div class="form-group">
                <label for="product_code">Product Code</label>
                <input type="text" name="product_code" id="product_code" value="<?= esc($product->product_code ?? '') ?>" readonly class="readonly">
            </div>
            
            <div class="form-group">
                <label for="product_name">Product Name *</label>
                <input type="text" name="product_name" id="product_name" value="<?= old('product_name', $product->product_name ?? '') ?>" required>
                <?php if(isset($errors) && isset($errors['product_name'])): ?>
                    <span class="error"><?= esc($errors['product_name']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category">
                    <option value="">Select Category</option>
                    <?php if(isset($categories)): foreach($categories as $cat): ?>
                        <option value="<?= esc($cat->category_name) ?>" <?= ($product->category ?? '') == $cat->category_name ? 'selected' : '' ?>>
                            <?= esc($cat->category_name) ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
                <?php if(isset($errors) && isset($errors['category'])): ?>
                    <span class="error"><?= esc($errors['category']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="unit">Unit</label>
                <select name="unit" id="unit">
                    <option value="pcs" <?= ($product->unit ?? '') == 'pcs' ? 'selected' : '' ?>>Pieces (pcs)</option>
                    <option value="box" <?= ($product->unit ?? '') == 'box' ? 'selected' : '' ?>>Box</option>
                    <option value="ream" <?= ($product->unit ?? '') == 'ream' ? 'selected' : '' ?>>Ream</option>
                    <option value="kg" <?= ($product->unit ?? '') == 'kg' ? 'selected' : '' ?>>Kilogram (kg)</option>
                    <option value="ltr" <?= ($product->unit ?? '') == 'ltr' ? 'selected' : '' ?>>Liter (ltr)</option>
                    <option value="crates" <?= ($product->unit ?? '') == 'crates' ? 'selected' : '' ?>>Crates</option>
                </select>
                <?php if(isset($errors) && isset($errors['unit'])): ?>
                    <span class="error"><?= esc($errors['unit']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="cylinder_weight">LPG Cylinder Weight</label>
                <select name="cylinder_weight" id="cylinder_weight">
                    <option value="">Select Weight</option>
                    <option value="2.7kg" <?= old('cylinder_weight', $product->cylinder_weight ?? '') == '2.7kg' ? 'selected' : '' ?>>2.7kg</option>
                    <option value="5kg" <?= old('cylinder_weight', $product->cylinder_weight ?? '') == '5kg' ? 'selected' : '' ?>>5kg</option>
                    <option value="7kg" <?= old('cylinder_weight', $product->cylinder_weight ?? '') == '7kg' ? 'selected' : '' ?>>7kg</option>
                    <option value="11kg" <?= old('cylinder_weight', $product->cylinder_weight ?? '') == '11kg' ? 'selected' : '' ?>>11kg</option>
                    <option value="22kg" <?= old('cylinder_weight', $product->cylinder_weight ?? '') == '22kg' ? 'selected' : '' ?>>22kg</option>
                    <option value="50kg" <?= old('cylinder_weight', $product->cylinder_weight ?? '') == '50kg' ? 'selected' : '' ?>>50kg</option>
                </select>
                <?php if(isset($errors) && isset($errors['cylinder_weight'])): ?>
                    <span class="error"><?= esc($errors['cylinder_weight']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3"><?= old('description', $product->description ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="quantity">Quantity *</label>
                <input type="number" name="quantity" id="quantity" value="<?= old('quantity', $product->quantity ?? 0) ?>" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="supplier">Supplier</label>
                <input type="text" name="supplier" id="supplier" value="<?= old('supplier', $product->supplier ?? '') ?>">
                <?php if(isset($errors) && isset($errors['supplier'])): ?>
                    <span class="error"><?= esc($errors['supplier']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-container section-spacing">
            <h3>Fixed Prices (Read-only)</h3>
            <p class="login-subtitle">Prices cannot be edited here. Update them from the Fixed Prices page.</p>
            <div class="form-row">
                <div class="form-group">
                    <label>Buying Price (<?= esc(currency_symbol()) ?>)</label>
                    <input type="text" class="readonly" readonly value="<?= esc(format_currency((float)($fixedPrice->buying_price ?? $product->buying_price ?? 0))) ?>">
                </div>
                <div class="form-group">
                    <label>Selling Price (<?= esc(currency_symbol()) ?>)</label>
                    <input type="text" class="readonly" readonly value="<?= esc(format_currency((float)($fixedPrice->selling_price ?? $product->selling_price ?? 0))) ?>">
                </div>
            </div>
            <a href="<?= site_url('stock/fixed-prices') ?>" class="btn btn-secondary btn-sm">Go to Fixed Prices</a>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-warning">🔄 Update Product</button>
            <a href="<?= site_url('stock/inventory') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?= $this->include('inventory/templates/footer') ?>

<script>
    (function () {
        const categorySelect = document.getElementById('category');
        const unitSelect = document.getElementById('unit');
        if (!categorySelect || !unitSelect) {
            return;
        }

        const originalOptions = unitSelect.innerHTML;
        const selectedUnit = "<?= esc(old('unit', $product->unit ?? '')) ?>";

        function updateUnitOptions() {
            const category = (categorySelect.value || '').toLowerCase();
            if (category === 'lpg') {
                unitSelect.innerHTML = '<option value="kg">Kilogram (kg)</option>';
                unitSelect.value = 'kg';
                return;
            }
            if (category === 'canister') {
                unitSelect.innerHTML = '<option value="crates">Crates</option><option value="pcs">Pieces (pcs)</option>';
                unitSelect.value = 'crates';
                return;
            }
            if (category === 'others') {
                unitSelect.innerHTML = '<option value="pcs">Pieces (pcs)</option>';
                unitSelect.value = 'pcs';
                return;
            }

            unitSelect.innerHTML = originalOptions;
            if (selectedUnit) {
                unitSelect.value = selectedUnit;
            }
        }

        categorySelect.addEventListener('change', updateUnitOptions);
        updateUnitOptions();
    })();
</script>