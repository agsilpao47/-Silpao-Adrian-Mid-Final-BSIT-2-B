<?= $this->include('inventory/templates/inventory_header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Inventory Management') ?></h1>
    <a href="<?= site_url('stock/inventory/add') ?>" class="btn btn-primary">+ Add Product</a>
</div>

<!-- Search and Filter Section -->
<div class="inventory-controls">
    <div class="search-container">
        <form method="get" class="search-form">
            <input type="text" name="search" placeholder="Search products..." class="search-input" value="<?= esc($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn btn-secondary">Search</button>
        </form>
    </div>
    <div class="quick-filters">
        <select name="category_filter" onchange="this.form.submit()" class="filter-select">
            <option value="">All Categories</option>
            <option value="LPG" <?= ($_GET['category_filter'] ?? '') === 'LPG' ? 'selected' : '' ?>>LPG</option>
            <option value="Canister" <?= ($_GET['category_filter'] ?? '') === 'Canister' ? 'selected' : '' ?>>Canister</option>
            <option value="Others" <?= ($_GET['category_filter'] ?? '') === 'Others' ? 'selected' : '' ?>>Others</option>
        </select>
    </div>
</div>

<!-- Products Grid -->
<div class="inventory-grid">
    <?php if(!empty($products)): ?>
        <?php foreach($products as $product): ?>
            <div class="inventory-card">
                <div class="card-header">
                    <div class="product-info">
                        <div class="product-code"><?= esc($product->product_code) ?></div>
                        <div class="product-name"><?= esc($product->product_name) ?></div>
                    </div>
                    <div class="stock-indicator <?= $product->quantity < 10 ? 'low-stock' : 'good-stock' ?>">
                        <span class="stock-number"><?= $product->quantity ?></span>
                        <span class="stock-label">in stock</span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="product-details">
                        <div class="detail-row">
                            <div class="detail-label">Category</div>
                            <div class="detail-value">
                                <span class="category-badge <?= esc(strtolower($product->category ?? '')) ?>"><?= esc($product->category ?? 'N/A') ?></span>
                            </div>
                        </div>
                        
                        <?php if (!empty($product->cylinder_weight)): ?>
                        <div class="detail-row">
                            <div class="detail-label">LPG Weight</div>
                            <div class="detail-value weight"><?= esc($product->cylinder_weight) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="detail-row">
                            <div class="detail-label">Supplier</div>
                            <div class="detail-value"><?= esc($product->supplier ?? 'N/A') ?></div>
                        </div>
                    </div>
                    
                    <div class="pricing-section">
                        <div class="price-row">
                            <div class="price-box buying">
                                <div class="price-label">Buying</div>
                                <div class="price-amount"><?= esc(format_currency($product->buying_price)) ?></div>
                            </div>
                            <div class="price-box selling">
                                <div class="price-label">Selling</div>
                                <div class="price-amount"><?= esc(format_currency($product->selling_price)) ?></div>
                            </div>
                        </div>
                        
                        <div class="profit-section">
                            <div class="profit-amount">
                                <?= esc(format_currency((float)($product->selling_price - $product->buying_price))) ?>
                            </div>
                            <div class="profit-margin <?= ((float)($product->selling_price - $product->buying_price) / (float)$product->buying_price * 100) > 20 ? 'high-margin' : (((float)($product->selling_price - $product->buying_price) / (float)$product->buying_price * 100) > 10 ? 'medium-margin' : 'normal-margin') ?>">
                                <?= number_format((float)($product->selling_price - $product->buying_price) / (float)$product->buying_price * 100, 1) ?>% margin
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-actions">
                    <a href="<?= site_url('stock/inventory/view/' . $product->id) ?>" class="btn btn-sm btn-info">View</a>
                    <a href="<?= site_url('stock/inventory/edit/' . $product->id) ?>" class="btn btn-sm btn-secondary">Edit</a>
                    <form method="post" action="<?= site_url('stock/inventory/delete/' . $product->id) ?>" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this product? This cannot be undone.');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-products-state">
            <div class="no-products-icon">📦</div>
            <h3>No products found</h3>
            <p>Try adjusting your search or filters, or add your first product.</p>
            <a href="<?= site_url('stock/inventory/add') ?>" class="btn btn-primary">Add First Product</a>
        </div>
    <?php endif; ?>
</div>

<?= $this->include('inventory/templates/footer') ?>