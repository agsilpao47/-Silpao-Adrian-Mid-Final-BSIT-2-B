<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= $title ?></h1>
    <a href="<?= site_url('/') ?>" class="btn btn-secondary">← Back to List</a>
</div>

<div class="form-container">
    <form action="<?= site_url('inventory/edit/' . $product->id) ?>" method="post" class="product-form">
        
        <div class="form-row">
            <div class="form-group">
                <label for="product_code">Product Code</label>
                <input type="text" name="product_code" id="product_code" value="<?= esc($product->product_code) ?>" readonly class="readonly">
            </div>
            
            <div class="form-group">
                <label for="product_name">Product Name *</label>
                <input type="text" name="product_name" id="product_name" value="<?= old('product_name', $product->product_name) ?>" required>
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
                        <option value="<?= esc($cat->category_name) ?>" <?= ($product->category == $cat->category_name) ? 'selected' : '' ?>>
                            <?= esc($cat->category_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="unit">Unit</label>
                <select name="unit" id="unit">
                    <option value="pcs" <?= ($product->unit == 'pcs') ? 'selected' : '' ?>>Pieces (pcs)</option>
                    <option value="box" <?= ($product->unit == 'box') ? 'selected' : '' ?>>Box</option>
                    <option value="ream" <?= ($product->unit == 'ream') ? 'selected' : '' ?>>Ream</option>
                    <option value="kg" <?= ($product->unit == 'kg') ? 'selected' : '' ?>>Kilogram (kg)</option>
                    <option value="ltr" <?= ($product->unit == 'ltr') ? 'selected' : '' ?>>Liter (ltr)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3"><?= old('description', $product->description) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="quantity">Quantity *</label>
                <input type="number" name="quantity" id="quantity" value="<?= old('quantity', $product->quantity) ?>" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="supplier">Supplier</label>
                <input type="text" name="supplier" id="supplier" value="<?= old('supplier', $product->supplier) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="buying_price">Buying Price ($) *</label>
                <input type="number" name="buying_price" id="buying_price" value="<?= old('buying_price', $product->buying_price) ?>" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="selling_price">Selling Price ($) *</label>
                <input type="number" name="selling_price" id="selling_price" value="<?= old('selling_price', $product->selling_price) ?>" step="0.01" min="0" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-warning">🔄 Update Product</button>
            <a href="<?= site_url('/') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?= $this->include('inventory/templates/footer') ?>