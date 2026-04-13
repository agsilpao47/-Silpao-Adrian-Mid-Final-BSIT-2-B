<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1>Product Details</h1>
    <a href="<?= site_url('/') ?>" class="btn btn-secondary">← Back to List</a>
</div>

<div class="product-detail-card">
    <div class="detail-header">
        <h2><?= esc($product->product_name) ?></h2>
        <span class="product-code"><?= esc($product->product_code) ?></span>
    </div>
    
    <div class="detail-body">
        <div class="detail-row">
            <div class="detail-item">
                <label>Category:</label>
                <span><?= esc($product->category ?? 'N/A') ?></span>
            </div>
            
            <div class="detail-item">
                <label>Supplier:</label>
                <span><?= esc($product->supplier ?? 'N/A') ?></span>
            </div>
        </div>
        
        <div class="detail-row">
            <div class="detail-item">
                <label>Quantity:</label>
                <span class="<?= $product->quantity < 10 ? 'low-stock' : '' ?>">
                    <?= $product->quantity ?> <?= esc($product->unit ?? '') ?>
                </span>
            </div>
            
            <div class="detail-item">
                <label>Unit:</label>
                <span><?= esc($product->unit ?? 'N/A') ?></span>
            </div>
        </div>
        
        <div class="detail-row">
            <div class="detail-item">
                <label>Buying Price:</label>
                <span class="price">$<?= number_format($product->buying_price, 2) ?></span>
            </div>
            
            <div class="detail-item">
                <label>Selling Price:</label>
                <span class="price selling">$<?= number_format($product->selling_price, 2) ?></span>
            </div>
        </div>
        
        <div class="detail-row">
            <div class="detail-item">
                <label>Profit Margin:</label>
                <span class="<?= $profit > 0 ? 'profit' : 'loss' ?>">
                    $<?= number_format($profit, 2) ?> (<?= number_format($margin, 1) ?>%)
                </span>
            </div>
        </div>
        
        <div class="detail-description">
            <label>Description:</label>
            <p><?= esc($product->description ?? 'No description available.') ?></p>
        </div>
        
        <div class="detail-timestamps">
            <p><small>Created: <?= date('M d, Y H:i', strtotime($product->created_at)) ?></small></p>
            <p><small>Last Updated: <?= date('M d, Y H:i', strtotime($product->updated_at)) ?></small></p>
        </div>
    </div>
    
    <div class="detail-actions">
        <a href="<?= site_url('inventory/edit/' . $product->id) ?>" class="btn btn-warning">✏️ Edit Product</a>
        <a href="<?= site_url('inventory/delete/' . $product->id) ?>" class="btn btn-danger" onclick="return confirmDelete();">🗑️ Delete Product</a>
    </div>
</div>

<?= $this->include('inventory/templates/footer') ?>