<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Product Prices') ?></h1>
    <a href="<?= site_url('stock/inventory') ?>" class="btn btn-secondary">← Back to Inventory</a>
</div>

<div class="form-container section-spacing">
    <div class="price-header">
        <h2>Product Price Management</h2>
        <p class="price-description">Update buying and selling prices for all your products in one place. This is the only page where product prices can be modified.</p>
    </div>
    <form action="<?= site_url('stock/fixed-prices') ?>" method="post">
        <?= csrf_field() ?>
        <div class="price-table-container">
            <table class="price-table">
                <thead>
                    <tr>
                        <th class="product-col">Product Details</th>
                        <th class="price-col">Buying Price</th>
                        <th class="price-col">Selling Price</th>
                        <th class="margin-col">Profit Margin</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($fixedPrices)): ?>
                    <?php foreach ($fixedPrices as $row): ?>
                        <tr>
                            <td class="product-info">
                                <div class="product-name">
                                    <strong><?= esc($row->product_name) ?></strong>
                                    <?php if (!empty($row->cylinder_weight)): ?>
                                        <span class="cylinder-weight">(<?= esc($row->cylinder_weight) ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-meta">
                                    <span class="product-code"><?= esc($row->product_code) ?></span>
                                    <span class="product-category"><?= esc($row->category ?? 'N/A') ?></span>
                                </div>
                            </td>
                            <td class="price-input-cell">
                                <div class="price-input-wrapper">
                                    <span class="currency-symbol"><?= esc(currency_symbol()) ?></span>
                                    <input type="hidden" name="product_id[]" value="<?= (int) $row->product_id ?>">
                                    <input type="number" name="buying_price[]" value="<?= esc((string) $row->buying_price) ?>" min="0" step="0.01" required class="price-input-field buying-price" data-product-id="<?= (int) $row->product_id ?>">
                                </div>
                            </td>
                            <td class="price-input-cell">
                                <div class="price-input-wrapper">
                                    <span class="currency-symbol"><?= esc(currency_symbol()) ?></span>
                                    <input type="number" name="selling_price[]" value="<?= esc((string) $row->selling_price) ?>" min="0" step="0.01" required class="price-input-field selling-price" data-product-id="<?= (int) $row->product_id ?>">
                                </div>
                            </td>
                            <td class="margin-cell">
                                <span class="profit-margin" data-product-id="<?= (int) $row->product_id ?>">0%</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center no-products">No products available.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-large">💾 Save Product Prices</button>
        </div>
    </form>
</div>

<?= $this->include('inventory/templates/footer') ?>

<script>
    // Calculate profit margins in real-time
    function calculateProfitMargin(productId) {
        const buyingInput = document.querySelector(`input[data-product-id="${productId}"].buying-price`);
        const sellingInput = document.querySelector(`input[data-product-id="${productId}"].selling-price`);
        const marginDisplay = document.querySelector(`.profit-margin[data-product-id="${productId}"]`);
        
        if (!buyingInput || !sellingInput || !marginDisplay) return;
        
        const buyingPrice = parseFloat(buyingInput.value) || 0;
        const sellingPrice = parseFloat(sellingInput.value) || 0;
        
        if (buyingPrice > 0) {
            const margin = ((sellingPrice - buyingPrice) / buyingPrice) * 100;
            marginDisplay.textContent = margin.toFixed(1) + '%';
            marginDisplay.className = margin >= 0 ? 'profit-margin' : 'profit-margin negative';
        } else {
            marginDisplay.textContent = '0%';
            marginDisplay.className = 'profit-margin';
        }
    }
    
    // Initialize calculations on page load
    document.addEventListener('DOMContentLoaded', function() {
        const priceInputs = document.querySelectorAll('.price-input-field');
        priceInputs.forEach(input => {
            const productId = input.dataset.productId;
            calculateProfitMargin(productId);
            
            // Add event listeners for real-time updates
            input.addEventListener('input', function() {
                calculateProfitMargin(productId);
            });
        });
    });
</script>
