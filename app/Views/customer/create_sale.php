<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Create Sale from Customer Records') ?></h1>
    <a href="<?= site_url('customer') ?>" class="btn btn-secondary">← Back to Customer Records</a>
</div>



<!-- Customer Records Display -->
<div class="form-container section-spacing">
    <h3>All Customer Records and Purchase History</h3>
    <div class="customer-records-list">
        <?php if (!empty($customers)): ?>
            <?php foreach ($customers as $customer): ?>
                <div class="customer-record">
                    <div class="customer-header">
                        <div class="customer-info">
                            <strong><?= esc($customer->customer_name) ?></strong>
                            <small>
                                <?= (int) $customer->total_purchases ?> purchases, 
                                <?= esc(format_currency((float) $customer->total_spent)) ?> total
                            </small>
                        </div>
                    </div>
                    <div class="purchase-history">
                        <h5>Purchase History:</h5>
                        <?php 
                        // Get all purchase records for this customer
                        $purchases = $customerPurchases[$customer->id] ?? [];
                        if (!empty($purchases)): 
                        ?>
                            <table class="mini-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($purchases as $purchase): ?>
                                        <tr>
                                            <td><?= esc($purchase->product_bought) ?></td>
                                            <td><?= esc($purchase->product_category) ?></td>
                                            <td><?= (int) $purchase->quantity ?></td>
                                            <td><?= esc(format_currency((float) $purchase->price)) ?></td>
                                            <td><?= esc(date('M j, Y', strtotime($purchase->purchase_date))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="no-purchases">No purchase records found</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No customer records found. <a href="<?= site_url('customer/create') ?>">Add a customer record first</a>.</p>
        <?php endif; ?>
    </div>
    <!-- Direct Sale Processing Form -->
    <div class="form-container section-spacing">
        <h3>Process Sale Directly</h3>
        <form action="<?= site_url('customer/process-all-to-sales') ?>" method="post" class="product-form">
            <?= csrf_field('csrf_token') ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="sale_date">Sale Date *</label>
                    <input type="datetime-local" id="sale_date" name="sale_date" value="<?= date('Y-m-d\TH:i') ?>" required>
                </div>
                <div class="form-group">
                    <label for="sale_notes">Notes</label>
                    <textarea id="sale_notes" name="notes" rows="2" placeholder="Optional notes for this sale..."></textarea>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>⚠️ Important:</strong><br>
                • All <?= count($customers ?? []) ?> customer records will be processed into one sale<br>
                • Stock quantities will be automatically reduced based on customer purchases<br>
                • All customer records will be cleared after successful processing<br>
                • An invoice will be generated with all customer purchases
            </div>

            <div class="form-actions">
                <button type="submit" id="process_sale_btn" name="submit" class="btn btn-success btn-large">📦 Process Sale & Clear Records</button>
                <a href="<?= site_url('customer') ?>" class="btn btn-secondary btn-large">Cancel</a>
            </div>
        </form>
    </div>

    <div class="form-actions">
        <a href="<?= site_url('customer/select-products') ?>" class="btn btn-primary">⚙️ Advanced: Select Products Manually</a>
    </div>
</div>



<style>
.customer-records-list {
    max-height: 600px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.customer-record {
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
}

.customer-record:hover {
    background-color: #f8f9fa;
}

.customer-record:last-child {
    border-bottom: none;
}

.customer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background-color: #f9f9f9;
}

.customer-info strong {
    display: block;
    margin-bottom: 5px;
    font-size: 16px;
}

.customer-info small {
    color: #666;
    font-size: 14px;
}

.customer-radio {
    margin-left: 15px;
}

.customer-radio input[type="radio"] {
    transform: scale(1.2);
}

.purchase-history {
    padding: 0 15px 15px 15px;
    background-color: #fff;
}

.purchase-history h5 {
    margin: 10px 0;
    color: #333;
    font-size: 14px;
    font-weight: 600;
}

.mini-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    margin-top: 5px;
}

.mini-table th {
    background-color: #f1f1f1;
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
    font-weight: 600;
}

.mini-table td {
    padding: 6px 8px;
    border: 1px solid #ddd;
}

.no-purchases {
    color: #999;
    font-style: italic;
    margin: 10px 0;
    font-size: 13px;
}
</style>

<?= $this->include('inventory/templates/footer') ?>
