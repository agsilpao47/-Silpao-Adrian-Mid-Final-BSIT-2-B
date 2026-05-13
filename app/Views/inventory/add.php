<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Inventory Stock') ?></h1>
    <a href="<?= site_url('stock/inventory') ?>" class="btn btn-secondary">← Back to List</a>
</div>

<div class="form-container">
    <form action="<?= site_url('stock/inventory/add') ?>" method="post" class="product-form">
        <?= csrf_field() ?>
        
        <div class="form-row">
            <div class="form-group">
                <label for="product_code">Product Code *</label>
                <input type="text" name="product_code" id="product_code" value="<?= old('product_code') ?>" required>
                <?php if(isset($errors) && isset($errors['product_code'])): ?>
                    <span class="error"><?= esc($errors['product_code']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="product_name">Product Name *</label>
                <input type="text" name="product_name" id="product_name" value="<?= old('product_name') ?>" required>
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
                        <option value="<?= esc($cat->category_name) ?>" <?= old('category') == $cat->category_name ? 'selected' : '' ?>>
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
                    <option value="pcs" <?= old('unit') == 'pcs' ? 'selected' : '' ?>>Pieces (pcs)</option>
                    <option value="box" <?= old('unit') == 'box' ? 'selected' : '' ?>>Box</option>
                    <option value="ream" <?= old('unit') == 'ream' ? 'selected' : '' ?>>Ream</option>
                    <option value="kg" <?= old('unit') == 'kg' ? 'selected' : '' ?>>Kilogram (kg)</option>
                    <option value="ltr" <?= old('unit') == 'ltr' ? 'selected' : '' ?>>Liter (ltr)</option>
                    <option value="crates" <?= old('unit') == 'crates' ? 'selected' : '' ?>>Crates</option>
                </select>
                <?php if(isset($errors) && isset($errors['unit'])): ?>
                    <span class="error"><?= esc($errors['unit']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="cylinder_weight">LPG Cylinder Weight</label>
                <select name="cylinder_weight" id="cylinder_weight">
                    <option value="">Select Weight</option>
                    <option value="2.7kg" <?= old('cylinder_weight') == '2.7kg' ? 'selected' : '' ?>>2.7kg</option>
                    <option value="5kg" <?= old('cylinder_weight') == '5kg' ? 'selected' : '' ?>>5kg</option>
                    <option value="7kg" <?= old('cylinder_weight') == '7kg' ? 'selected' : '' ?>>7kg</option>
                    <option value="11kg" <?= old('cylinder_weight') == '11kg' ? 'selected' : '' ?>>11kg</option>
                    <option value="22kg" <?= old('cylinder_weight') == '22kg' ? 'selected' : '' ?>>22kg</option>
                    <option value="50kg" <?= old('cylinder_weight') == '50kg' ? 'selected' : '' ?>>50kg</option>
                </select>
                <?php if(isset($errors) && isset($errors['cylinder_weight'])): ?>
                    <span class="error"><?= esc($errors['cylinder_weight']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3"><?= old('description') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="quantity">Stock</label>
                <div class="number-input-wrapper">
                    <button type="button" class="number-btn decrement" onclick="adjustNumber('quantity', -1)">−</button>
                    <input type="number" name="quantity" id="quantity" value="<?= old('quantity') ?>" min="0" required class="number-input" placeholder="0">
                    <button type="button" class="number-btn increment" onclick="adjustNumber('quantity', 1)">+</button>
                </div>
                <?php if(isset($errors) && isset($errors['quantity'])): ?>
                    <span class="error"><?= esc($errors['quantity']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="supplier">Supplier</label>
                <input type="text" name="supplier" id="supplier" value="<?= old('supplier') ?>">
                <?php if(isset($errors) && isset($errors['supplier'])): ?>
                    <span class="error"><?= esc($errors['supplier']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-container section-spacing">
            <h3>Fixed Prices</h3>
            <p class="login-subtitle">Set product price once here. Future price changes should be done in Fixed Prices page.</p>
            <div class="form-row">
                <div class="form-group">
                    <label for="fixed_buying_price">Fixed Buying Price (<?= esc(currency_symbol()) ?>) *</label>
                    <div class="number-input-wrapper">
                        <button type="button" class="number-btn decrement" onclick="adjustPrice('fixed_buying_price', -1)">−</button>
                        <input type="number" name="fixed_buying_price" id="fixed_buying_price" value="<?= old('fixed_buying_price') ?>" step="0.01" min="0" required placeholder="0.00" class="number-input price-input">
                        <button type="button" class="number-btn increment" onclick="adjustPrice('fixed_buying_price', 1)">+</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="fixed_selling_price">Fixed Selling Price (<?= esc(currency_symbol()) ?>) *</label>
                    <div class="number-input-wrapper">
                        <button type="button" class="number-btn decrement" onclick="adjustPrice('fixed_selling_price', -1)">−</button>
                        <input type="number" name="fixed_selling_price" id="fixed_selling_price" value="<?= old('fixed_selling_price') ?>" step="0.01" min="0" required placeholder="0.00" class="number-input price-input">
                        <button type="button" class="number-btn increment" onclick="adjustPrice('fixed_selling_price', 1)">+</button>
                    </div>
                </div>
            </div>
            <?php if(isset($errors) && isset($errors['fixed_price'])): ?>
                <span class="error"><?= esc($errors['fixed_price']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Save Product</button>
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
        }

        categorySelect.addEventListener('change', updateUnitOptions);
        updateUnitOptions();
    })();

    // Number input adjustment functions
    function adjustNumber(inputId, change) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        const currentValue = parseInt(input.value) || 0;
        const newValue = Math.max(0, currentValue + change);
        input.value = newValue === 0 ? '' : newValue;
    }

    function adjustPrice(inputId, change) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        const currentValue = parseFloat(input.value) || 0;
        const newValue = Math.max(0, currentValue + change);
        input.value = newValue === 0 ? '' : newValue.toFixed(2);
    }

    // Allow keyboard shortcuts for number inputs
    document.addEventListener('keydown', function(e) {
        if (e.target.classList.contains('number-input')) {
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (e.target.classList.contains('price-input')) {
                    adjustPrice(e.target.id, 1);
                } else {
                    adjustNumber(e.target.id, 1);
                }
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (e.target.classList.contains('price-input')) {
                    adjustPrice(e.target.id, -1);
                } else {
                    adjustNumber(e.target.id, -1);
                }
            }
        }
    });
</script>