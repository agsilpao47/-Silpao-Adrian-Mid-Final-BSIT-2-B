<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Select Products for Sale') ?></h1>
    <a href="<?= site_url('customer/create-sale') ?>" class="btn btn-secondary">← Back to Customer Selection</a>
</div>

<!-- Customer Info Display -->
<div class="form-container section-spacing">
    <div class="customer-info-display">
        <h4>All Customer Records to be Processed</h4>
        <div class="customer-details">
            <div class="detail-item">
                <span class="detail-label">Total Customers:</span>
                <span class="detail-value"><?= count($customers) ?> customers</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span class="detail-value">All customer records will be processed and cleared after sale</span>
            </div>
        </div>
    </div>
</div>

<!-- Product Selection -->
<div class="form-container section-spacing">
    <h3>Select Products for Sale</h3>
    <form id="saleForm" action="<?= site_url('customer/preview-sale') ?>" method="post" class="product-form">
        <?= csrf_field('csrf_token') ?>
        <input type="hidden" id="process_all_customers" name="process_all_customers" value="1">
        
        <div class="form-row">
            <div class="form-group">
                <label for="product_search">Search Products</label>
                <input type="text" id="product_search" placeholder="Search products by name or code..." onkeyup="searchProducts()">
                <div id="product_results" class="product-search-results"></div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="sale_date">Sale Date *</label>
                <input type="datetime-local" id="sale_date" name="sale_date" value="<?= date('Y-m-d\TH:i') ?>" required>
            </div>
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="2"></textarea>
            </div>
        </div>

        <div class="selected-products">
            <h4>Selected Products</h4>
            <table id="productsTable" class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Available Stock</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Products will be added here dynamically -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right"><strong>Total:</strong></td>
                        <td id="grandTotal">0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="form-actions">
            <button type="button" id="preview_btn" name="preview" class="btn btn-info" onclick="previewSale()">Preview Sale</button>
            <button type="submit" id="process_btn" name="submit" class="btn btn-success">Process Sale</button>
            <a href="<?= site_url('customer/create-sale') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Sale Preview</h3>
            <span class="close" onclick="closePreview()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="confirm_btn" name="confirm" class="btn btn-success" onclick="confirmSale()">Confirm Sale</button>
            <button type="button" id="preview_cancel_btn" name="preview_cancel" class="btn btn-secondary" onclick="closePreview()">Cancel</button>
        </div>
    </div>
</div>

<script>
let selectedProducts = [];
let productIdCounter = 0;

function searchProducts() {
    const searchTerm = document.getElementById('product_search').value;
    if (searchTerm.length < 2) {
        document.getElementById('product_results').innerHTML = '';
        return;
    }
    
    fetch(`<?= site_url('customer/search-products') ?>?term=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            data.forEach(product => {
                html += `
                    <div class="product-item" onclick="addProduct(${product.id}, '${product.product_name}', '${product.category}', ${product.quantity}, ${product.selling_price})">
                        <strong>${product.product_name}</strong><br>
                        <small>Category: ${product.category} | Stock: ${product.quantity} | Price: ₱${product.selling_price}</small>
                    </div>
                `;
            });
            document.getElementById('product_results').innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}

function addProduct(id, name, category, stock, price) {
    if (stock <= 0) {
        alert('This product is out of stock!');
        return;
    }
    
    const productId = `product_${productIdCounter++}`;
    selectedProducts.push({
        id: productId,
        product_id: id,
        name: name,
        category: category,
        stock: stock,
        price: price,
        quantity: 1
    });
    
    renderProducts();
    document.getElementById('product_search').value = '';
    document.getElementById('product_results').innerHTML = '';
}

function renderProducts() {
    const tbody = document.querySelector('#productsTable tbody');
    tbody.innerHTML = '';
    
    let total = 0;
    selectedProducts.forEach(product => {
        const subtotal = product.price * product.quantity;
        total += subtotal;
        
        tbody.innerHTML += `
            <tr id="${product.id}">
                <td>${product.name}</td>
                <td>${product.category}</td>
                <td>${product.stock}</td>
                <td>
                    <input type="number" name="quantity_${product.id}" value="${product.quantity}" min="1" max="${product.stock}" onchange="updateQuantity('${product.id}', this.value)">
                </td>
                <td>₱${product.price.toFixed(2)}</td>
                <td>₱${subtotal.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeProduct('${product.id}')">Remove</button>
                </td>
            </tr>
        `;
    });
    
    document.getElementById('grandTotal').textContent = `₱${total.toFixed(2)}`;
}

function updateQuantity(productId, newQuantity) {
    const product = selectedProducts.find(p => p.id === productId);
    if (product) {
        const maxQuantity = parseInt(product.stock);
        const quantity = Math.min(Math.max(1, parseInt(newQuantity)), maxQuantity);
        product.quantity = quantity;
        renderProducts();
    }
}

function removeProduct(productId) {
    selectedProducts = selectedProducts.filter(p => p.id !== productId);
    renderProducts();
}

function previewSale() {
    if (selectedProducts.length === 0) {
        alert('Please add at least one product to the sale!');
        return;
    }
    
    let html = `
        <h4>All Customer Records Will Be Processed</h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    let total = 0;
    selectedProducts.forEach(product => {
        const subtotal = product.price * product.quantity;
        total += subtotal;
        html += `
            <tr>
                <td>${product.name}</td>
                <td>${product.quantity}</td>
                <td>₱${product.price.toFixed(2)}</td>
                <td>₱${subtotal.toFixed(2)}</td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total:</th>
                    <th>₱${total.toFixed(2)}</th>
                </tr>
            </tfoot>
        </table>
        
        <p><strong>Stock will be automatically reduced after confirmation and all customer records will be cleared.</strong></p>
    `;
    
    document.getElementById('previewContent').innerHTML = html;
    document.getElementById('previewModal').style.display = 'block';
}

function closePreview() {
    document.getElementById('previewModal').style.display = 'none';
}

function confirmSale() {
    // Add hidden inputs for selected products before submitting
    const form = document.getElementById('saleForm');
    
    // Remove any existing product inputs
    const existingInputs = form.querySelectorAll('input[name^="product_id_"], input[name^="quantity_"]');
    existingInputs.forEach(input => input.remove());
    
    // Add hidden inputs for each selected product
    selectedProducts.forEach((product, index) => {
        const productIdInput = document.createElement('input');
        productIdInput.type = 'hidden';
        productIdInput.name = 'product_id[]';
        productIdInput.value = product.product_id;
        form.appendChild(productIdInput);
        
        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = 'quantity[]';
        quantityInput.value = product.quantity;
        form.appendChild(quantityInput);
    });
    
    form.submit();
}
</script>

<style>
.product-search-results {
    border: 1px solid #ddd;
    max-height: 200px;
    overflow-y: auto;
    margin-top: 5px;
}

.product-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}

.product-item:hover {
    background-color: #f5f5f5;
}

.customer-info-display {
    background-color: #e8f4f8;
    border: 1px solid #d1e7dd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.customer-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background-color: white;
    border-radius: 5px;
    border: 1px solid #e0e0e0;
}

.detail-label {
    font-weight: 600;
    color: #374151;
}

.detail-value {
    font-weight: 500;
    color: #1f2937;
}

.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}

.modal-footer {
    margin-top: 20px;
    text-align: right;
}
</style>

<?= $this->include('inventory/templates/footer') ?>
