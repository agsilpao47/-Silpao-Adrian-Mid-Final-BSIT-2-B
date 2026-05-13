<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Add Customer Record') ?></h1>
    <a href="<?= site_url('customer') ?>" class="btn btn-secondary">← Back to Customer Records</a>
</div>

<?php if (session('error')): ?>
    <div class="alert alert-danger">
        <strong>Error:</strong> <?= esc(session('error')) ?>
        <br><small>Debug: Check browser console (F12) for detailed validation information.</small>
    </div>
<?php endif; ?>

<?php if (session('validation')): ?>
    <div class="alert alert-danger">
        <strong>Validation Errors:</strong>
        <ul>
            <?php foreach (session('validation') as $field => $errors): ?>
                <li><?= esc($field) ?>: <?= is_array($errors) ? implode(', ', $errors) : $errors ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="form-container">
    <form id="customer-form" action="<?= site_url('customer/create') ?>" method="post" class="product-form">
        <?= csrf_field('csrf_token') ?>
        
        <div class="form-row">
            <div class="form-group">
                <label for="customer_name">Customer Name *</label>
                <input type="text" id="customer_name" name="customer_name" value="<?= old('customer_name') ?>" required class="<?= session('error.customer_name') ? 'is-invalid' : '' ?>" autocomplete="name">
                <?php if (session('error.customer_name')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.customer_name')) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="purchase_date">Purchase Date *</label>
                <input type="datetime-local" id="purchase_date" name="purchase_date" value="<?= old('purchase_date', date('Y-m-d\TH:i')) ?>" required class="<?= session('error.purchase_date') ? 'is-invalid' : '' ?>" autocomplete="off">
                <?php if (session('error.purchase_date')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.purchase_date')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="product_bought">Product Bought *</label>
                <select id="product_bought" name="product_bought" required class="<?= session('error.product_bought') ? 'is-invalid' : '' ?>" autocomplete="off">
                    <option value="">Select Product</option>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= esc($product->product_name) ?>" 
                                    data-category="<?= esc($product->category) ?>"
                                    data-price="<?= esc($product->selling_price) ?>"
                                    <?= old('product_bought') == $product->product_name ? 'selected' : '' ?>>
                                <?= esc($product->product_name) ?> (<?= esc($product->cylinder_weight) ?>) - <?= esc(format_currency((float) $product->selling_price)) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if (session('error.product_bought')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.product_bought')) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="product_category">Product Category *</label>
                <select id="product_category" name="product_category" required class="<?= session('error.product_category') ? 'is-invalid' : '' ?>" autocomplete="off">
                    <option value="">Select Category</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= esc($category->category) ?>" <?= old('product_category') == $category->category ? 'selected' : '' ?>>
                                <?= esc($category->category) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if (session('error.product_category')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.product_category')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="quantity">Quantity *</label>
                <input type="number" id="quantity" name="quantity" value="<?= old('quantity', 1) ?>" min="1" required class="<?= session('error.quantity') ? 'is-invalid' : '' ?>" autocomplete="off">
                <?php if (session('error.quantity')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.quantity')) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="price">Price per Unit *</label>
                <input type="number" id="price" name="price" value="<?= old('price') ?>" step="0.01" min="0" required class="<?= session('error.price') ? 'is-invalid' : '' ?>" autocomplete="off">
                <input type="hidden" id="price_hidden" name="price_hidden" value="<?= old('price') ?>">
                <?php if (session('error.price')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.price')) ?></div>
                <?php endif; ?>
                <small class="form-text text-muted">Price automatically set from selected product</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group full-width">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="<?= session('error.notes') ? 'is-invalid' : '' ?>" autocomplete="off"><?= esc(old('notes')) ?></textarea>
                <?php if (session('error.notes')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.notes')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary">Save Customer Record</button>
            <a href="<?= site_url('customer') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.readonly-field {
    background-color: #f8f9fa;
    cursor: not-allowed;
    border-color: #ced4da;
}
.readonly-field:focus {
    background-color: #f8f9fa;
    border-color: #ced4da;
    box-shadow: 0 0 0 0 0.2rem rgba(206, 212, 218, 0.25);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_bought');
    const categorySelect = document.getElementById('product_category');
    const priceInput = document.getElementById('price');
    
    // Store all products
    const allProducts = [];
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            allProducts.push({
                name: <?= json_encode($product->product_name) ?>,
                category: <?= json_encode($product->category) ?>,
                price: <?= (float) $product->selling_price ?>
            });
        <?php endforeach; ?>
    <?php endif; ?>
    
    // Function to filter products by category
    function filterProductsByCategory(category) {
        // Clear current options
        productSelect.innerHTML = '<option value="">Select Product</option>';
        
        // Add filtered products
        allProducts.forEach(function(product) {
            if (category === '' || product.category === category) {
                const option = document.createElement('option');
                option.value = product.name;
                option.textContent = product.name + ' - ' + formatCurrency(product.price);
                option.dataset.price = product.price;
                productSelect.appendChild(option);
            }
        });
    }
    
    // Function to update price when product is selected
    function updatePrice() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.price) {
            priceInput.value = selectedOption.dataset.price;
            // Also update category field
            const selectedProduct = allProducts.find(p => p.name === selectedOption.value);
            if (selectedProduct) {
                categorySelect.value = selectedProduct.category;
            }
        } else {
            // Clear price if no valid selection
            priceInput.value = '';
        }
    }
    
    // Format currency function
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).format(amount);
    }
    
    // Event listeners
    categorySelect.addEventListener('change', function() {
        filterProductsByCategory(this.value);
        priceInput.value = ''; // Clear price when category changes
    });
    
    productSelect.addEventListener('change', updatePrice);
    
    // Initial filter
    filterProductsByCategory(categorySelect.value);
    
    // Comprehensive form submission handler with debugging
    document.getElementById('customer-form').addEventListener('submit', function(e) {
        // Make sure price has a value before submission
        if (!priceInput.value) {
            e.preventDefault();
            alert('Please select a product to set the price automatically.');
            return false;
        }
        
        // Remove hidden price field to avoid conflicts
        const priceHidden = document.getElementById('price_hidden');
        if (priceHidden) {
            priceHidden.remove();
        }
        
        // Debug logging
        console.log('=== FORM SUBMISSION DEBUG ===');
        console.log('Customer Name:', document.getElementById('customer_name').value);
        console.log('Product Bought:', document.getElementById('product_bought').value);
        console.log('Product Category:', document.getElementById('product_category').value);
        console.log('Quantity:', document.getElementById('quantity').value);
        console.log('Price:', priceInput.value);
        console.log('Purchase Date:', document.getElementById('purchase_date').value);
        console.log('Notes:', document.getElementById('notes').value);
        
        // Client-side validation
        const customerName = document.getElementById('customer_name').value.trim();
        const productBought = document.getElementById('product_bought').value.trim();
        const productCategory = document.getElementById('product_category').value.trim();
        const quantity = document.getElementById('quantity').value.trim();
        const price = priceInput.value.trim();
        const purchaseDate = document.getElementById('purchase_date').value.trim();
        
        const validationErrors = [];
        
        if (customerName.length < 2) validationErrors.push('Customer name must be at least 2 characters');
        if (productBought.length < 2) validationErrors.push('Please select a product');
        if (productCategory.length < 1) validationErrors.push('Product category is required');
        if (!quantity || isNaN(quantity) || parseInt(quantity) <= 0) validationErrors.push('Quantity must be a positive number');
        if (!price || isNaN(price) || parseFloat(price) < 0) validationErrors.push('Price must be a positive number');
        if (!purchaseDate) validationErrors.push('Purchase date is required');
        
        if (validationErrors.length > 0) {
            e.preventDefault();
            alert('Please correct the following errors:\n• ' + validationErrors.join('\n• '));
            console.log('Client-side validation errors:', validationErrors);
            return false;
        }
        
        // If validation passes, submit normally
        console.log('Validation passed - submitting form...');
        
        // DON'T call this.submit() - let the form submit naturally
        // This prevents double submission issues
    });
});
</script>

<?= $this->include('inventory/templates/footer') ?>
