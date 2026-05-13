<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Create Sales Record') ?></h1>
    <a href="<?= site_url('sales') ?>" class="btn btn-secondary">← Back to Sales</a>
</div>

<div class="form-container">
    <form action="<?= site_url('sales/create') ?>" method="post" class="product-form">
        <?= csrf_field() ?>

        <div class="form-row">
            <div class="form-group">
                <label>Invoice No</label>
                <input type="text" value="<?= esc($invoiceNo) ?>" readonly class="readonly">
            </div>
            <div class="form-group">
                <label for="sale_date">Sale Date *</label>
                <input
                    type="datetime-local"
                    name="sale_date"
                    id="sale_date"
                    value="<?= old('sale_date', date('Y-m-d\TH:i')) ?>"
                    required
                >
            </div>
        </div>

        <div class="form-row">
            <div class="form-group full-width">
                <label>Sale Items *</label>
                <div id="sale-items">
                    <div class="form-row sale-item-row">
                        <div class="form-group">
                            <select name="product_id[]" required>
                                <option value="">Select Product</option>
                                <?php if(isset($products)): foreach ($products as $product): ?>
                                    <option value="<?= (int) $product->id ?>">
                                        <?= esc($product->product_name) ?>
                                        <?php if (!empty($product->cylinder_weight)): ?> - <?= esc($product->cylinder_weight) ?><?php endif; ?>
                                        (Stock: <?= (int) $product->quantity ?>)
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="number" name="quantity[]" value="1" min="1" required>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeSaleItem(this)">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addSaleItem()">+ Add Item</button>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="customer_name">Customer Name</label>
                <input type="text" name="customer_name" id="customer_name" value="<?= old('customer_name') ?>" placeholder="Optional">
            </div>
            <div class="form-group">
                <label for="notes">Notes</label>
                <input type="text" name="notes" id="notes" value="<?= old('notes') ?>" placeholder="Optional">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Sale</button>
            <a href="<?= site_url('sales') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    const productOptionsHtml = `<?php if(isset($products)): foreach ($products as $product): ?><option value="<?= (int) $product->id ?>"><?= esc($product->product_name) ?><?php if (!empty($product->cylinder_weight)): ?> - <?= esc($product->cylinder_weight) ?><?php endif; ?> (Stock: <?= (int) $product->quantity ?>)</option><?php endforeach; endif; ?>`;

    function addSaleItem() {
        const container = document.getElementById('sale-items');
        const row = document.createElement('div');
        row.className = 'form-row sale-item-row';
        row.innerHTML = `
            <div class="form-group">
                <select name="product_id[]" required>
                    <option value="">Select Product</option>
                    ${productOptionsHtml}
                </select>
            </div>
            <div class="form-group">
                <input type="number" name="quantity[]" value="1" min="1" required>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeSaleItem(this)">Remove</button>
            </div>
        `;
        container.appendChild(row);
    }

    function removeSaleItem(button) {
        const container = document.getElementById('sale-items');
        if (container.querySelectorAll('.sale-item-row').length === 1) {
            return;
        }
        button.closest('.sale-item-row').remove();
    }
</script>

<?= $this->include('inventory/templates/footer') ?>
