<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Sale Preview') ?></h1>
</div>

<div class="form-container">
    <div class="sale-preview">
        <div class="customer-info">
            <h3>Customer Information</h3>
            <p><strong>Customer Name:</strong> <?= esc($customer_name) ?></p>
            <p><strong>Sale Date:</strong> <?= esc(date('Y-m-d H:i', strtotime($sale_date))) ?></p>
            <?php if (!empty($notes)): ?>
                <p><strong>Notes:</strong> <?= esc($notes) ?></p>
            <?php endif; ?>
        </div>

        <div class="sale-items">
            <h3>Order Details</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($line_items as $item): ?>
                        <tr>
                            <td><?= esc($item['product']->product_name) ?></td>
                            <td>
                                <span class="category-badge <?= esc(strtolower($item['product']->category)) ?>">
                                    <?= esc($item['product']->category) ?>
                                </span>
                            </td>
                            <td><?= (int) $item['product']->quantity ?></td>
                            <td><?= (int) $item['quantity'] ?></td>
                            <td><?= esc(format_currency((float) $item['unit_price'])) ?></td>
                            <td><?= esc(format_currency((float) $item['subtotal'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right"><strong>Total Amount:</strong></td>
                        <td><strong><?= esc(format_currency((float) $total_amount)) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="stock-warning">
            <div class="alert alert-info">
                <strong>⚠️ Stock Update Warning:</strong><br>
                After confirming this sale, the following stock quantities will be automatically reduced:
                <ul>
                    <?php foreach ($line_items as $item): ?>
                        <li>
                            <?= esc($item['product']->product_name) ?>: 
                            <?= (int) $item['product']->quantity ?> → 
                            <?= ((int) $item['product']->quantity - (int) $item['quantity']) ?> 
                            (reducing by <?= (int) $item['quantity'] ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="customer-record-info">
            <div class="alert alert-success">
                <strong>✅ Customer Record:</strong><br>
                A customer record will be automatically created for each product in this sale, 
                linking the purchase to the customer's history.
            </div>
        </div>
    </div>

    <div class="form-actions">
        <form action="<?= site_url('customer/confirm-sale') ?>" method="post">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-success btn-large">✓ Confirm & Process Sale</button>
            <a href="<?= site_url('customer/create-sale') ?>" class="btn btn-secondary btn-large">← Back to Edit</a>
            <a href="<?= site_url('customer') ?>" class="btn btn-secondary btn-large">Cancel</a>
        </form>
    </div>
</div>

<style>
.sale-preview {
    max-width: 800px;
    margin: 0 auto;
}

.customer-info, .sale-items, .stock-warning, .customer-record-info {
    margin-bottom: 30px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.customer-info h3, .sale-items h3 {
    margin-top: 0;
    color: #333;
}

.customer-info p {
    margin: 10px 0;
}

.stock-warning ul {
    margin: 10px 0;
    padding-left: 20px;
}

.btn-large {
    padding: 12px 24px;
    font-size: 16px;
    margin: 0 10px;
}

.form-actions {
    text-align: center;
    margin-top: 30px;
}

.alert-info {
    background-color: #e3f2fd;
    border: 1px solid #bbdefb;
    color: #0d47a1;
}

.alert-success {
    background-color: #e8f5e8;
    border: 1px solid #c8e6c9;
    color: #2e7d32;
}
</style>

<?= $this->include('inventory/templates/footer') ?>
