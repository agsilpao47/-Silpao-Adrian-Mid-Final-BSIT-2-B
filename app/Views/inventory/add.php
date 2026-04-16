<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= $title ?></h1>
    <a href="<?= site_url('inventory') ?>" class="btn btn-secondary">← Back to List</a>
</div>

<div class="form-container">
    <form action="<?= site_url('inventory/add') ?>" method="post" class="product-form">
        
        <div class="form-row">
            <div class="form-group">
                <label for="product_code">Product Code *</label>
                <input type="text" name="product_code" id="product_code" value="<?= old('product_code') ?>" required>
                <?php if(isset($errors['product_code'])): ?>
                    <span class="error"><?= esc($errors['product_code']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="product_name">Product Name *</label>
                <input type="text" name="product_name" id="product_name" value="<?= old('product_name') ?>" required>
                <?php if(isset($errors['product_name'])): ?>
                    <span class="error"><?= esc($errors['product_name']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category">
                    <option value="">Select Category</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= esc($cat->category_name) ?>" <?= old('category') == $cat->category_name ? 'selected' : '' ?>>
                            <?= esc($cat->category_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="unit">Unit</label>
                <select name="unit" id="unit">
                    <option value="pcs" <?= old('unit') == 'pcs' ? 'selected' : '' ?>>Pieces (pcs)</option>
                    <option value="box" <?= old('unit') == 'box' ? 'selected' : '' ?>>Box</option>
                    <option value="ream" <?= old('unit') == 'ream' ? 'selected' : '' ?>>Ream</option>
                    <option value="kg" <?= old('unit') == 'kg' ? 'selected' : '' ?>>Kilogram (kg)</option>
                    <option value="ltr" <?= old('unit') == 'ltr' ? 'selected' : '' ?>>Liter (ltr)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3"><?= old('description') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="quantity">Quantity *</label>
                <input type="number" name="quantity" id="quantity" value="<?= old('quantity', 0) ?>" min="0" required>
                <?php if(isset($errors['quantity'])): ?>
                    <span class="error"><?= esc($errors['quantity']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="supplier">Supplier</label>
                <input type="text" name="supplier" id="supplier" value="<?= old('supplier') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="buying_price">Buying Price ($) *</label>
                <input type="number" name="buying_price" id="buying_price" value="<?= old('buying_price', '0.00') ?>" step="0.01" min="0" required>
                <?php if(isset($errors['buying_price'])): ?>
                    <span class="error"><?= esc($errors['buying_price']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="selling_price">Selling Price ($) *</label>
                <input type="number" name="selling_price" id="selling_price" value="<?= old('selling_price', '0.00') ?>" step="0.01" min="0" required>
                <?php if(isset($errors['selling_price'])): ?>
                    <span class="error"><?= esc($errors['selling_price']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Save Product</button>
            <a href="<?= site_url('inventory') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?= $this->include('inventory/templates/footer') ?>