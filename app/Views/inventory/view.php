<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1>Product Details</h1>
    <a href="<?= site_url('stock/inventory') ?>" class="btn btn-secondary">← Back to List</a>
</div>

<?php if(isset($product)): ?>
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
                <label>Stock:</label>
                <span class="<?= $product->quantity < 10 ? 'low-stock' : '' ?>">
                    <?= $product->quantity ?>
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
                <span class="price"><?= esc(format_currency($product->buying_price)) ?></span>
            </div>
            
            <div class="detail-item">
                <label>Selling Price:</label>
                <span class="price selling"><?= esc(format_currency($product->selling_price)) ?></span>
            </div>
        </div>
        
        <div class="detail-row">
            <div class="detail-item">
                <label>Profit Margin:</label>
                <?php 
                $profit = $product->selling_price - $product->buying_price;
                $margin = $product->buying_price > 0 ? ($profit / $product->buying_price) * 100 : 0;
                ?>
                <span class="<?= $profit > 0 ? 'profit' : 'loss' ?>">
                    <?= esc(format_currency($profit)) ?> (<?= number_format($margin, 1) ?>%)
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
        <a href="<?= site_url('stock/inventory/edit/' . $product->id) ?>" class="btn btn-warning">✏️ Edit Product</a>
        <form method="post" action="<?= site_url('stock/inventory/delete/' . $product->id) ?>" class="inline-form" onsubmit="return confirmDelete();">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-danger">🗑️ Delete Product</button>
        </form>
    </div>
</div>
<?php else: ?>
    <div class="alert alert-error">Product not found.</div>
<?php endif; ?>

<?= $this->include('inventory/templates/footer') ?>
