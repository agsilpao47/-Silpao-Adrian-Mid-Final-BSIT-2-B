<div class="page-header">
    <h1><?php echo $title; ?></h1>
    <a href="<?php echo site_url('inventory'); ?>" class="btn btn-secondary">← Back to List</a>
</div>

<div class="form-container">
    <form action="<?php echo site_url('inventory/add'); ?>" method="post" class="product-form">
        
        <div class="form-row">
            <div class="form-group">
                <label for="product_code">Product Code *</label>
                <input type="text" name="product_code" id="product_code" value="<?php echo set_value('product_code'); ?>" required>
                <?php echo form_error('product_code', '<span class="error">', '</span>'); ?>
            </div>
            
            <div class="form-group">
                <label for="product_name">Product Name *</label>
                <input type="text" name="product_name" id="product_name" value="<?php echo set_value('product_name'); ?>" required>
                <?php echo form_error('product_name', '<span class="error">', '</span>'); ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category">
                    <option value="">Select Category</option>
                    <?php if(!empty($categories)): ?>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat->category_name; ?>" <?php echo set_select('category', $cat->category_name); ?>>
                                <?php echo $cat->category_name; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="unit">Unit</label>
                <select name="unit" id="unit">
                    <option value="pcs" <?php echo set_select('unit', 'pcs'); ?>>Pieces (pcs)</option>
                    <option value="box" <?php echo set_select('unit', 'box'); ?>>Box</option>
                    <option value="ream" <?php echo set_select('unit', 'ream'); ?>>Ream</option>
                    <option value="kg" <?php echo set_select('unit', 'kg'); ?>>Kilogram (kg)</option>
                    <option value="ltr" <?php echo set_select('unit', 'ltr'); ?>>Liter (ltr)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3"><?php echo set_value('description'); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="quantity">Quantity *</label>
                <input type="number" name="quantity" id="quantity" value="<?php echo set_value('quantity', 0); ?>" min="0" required>
                <?php echo form_error('quantity', '<span class="error">', '</span>'); ?>
            </div>
            
            <div class="form-group">
                <label for="supplier">Supplier</label>
                <input type="text" name="supplier" id="supplier" value="<?php echo set_value('supplier'); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="buying_price">Buying Price ($) *</label>
                <input type="number" name="buying_price" id="buying_price" value="<?php echo set_value('buying_price', '0.00'); ?>" step="0.01" min="0" required>
                <?php echo form_error('buying_price', '<span class="error">', '</span>'); ?>
            </div>
            
            <div class="form-group">
                <label for="selling_price">Selling Price ($) *</label>
                <input type="number" name="selling_price" id="selling_price" value="<?php echo set_value('selling_price', '0.00'); ?>" step="0.01" min="0" required>
                <?php echo form_error('selling_price', '<span class="error">', '</span>'); ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Save Product</button>
            <a href="<?php echo site_url('inventory'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>