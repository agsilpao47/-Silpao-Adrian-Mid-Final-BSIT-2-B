<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Customer Sales Overview') ?></h1>
    <a href="<?= site_url('customer') ?>" class="btn btn-secondary">← Back to Customer Records</a>
</div>

<div class="customer-overview">
    <div class="customer-info-card">
        <h2>Customer: <?= esc($customerName) ?></h2>
        <div class="customer-stats">
            <div class="stat-item">
                <span class="stat-label">Total Customer Records:</span>
                <span class="stat-value"><?= count($customerRecords) ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Total Sales:</span>
                <span class="stat-value"><?= count($sales) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Customer Records Section -->
<div class="dashboard-card section-spacing">
    <div class="card-header">
        <h2>Customer Purchase Records</h2>
        <div class="card-icon">📋</div>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product Bought</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Purchase Date</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($customerRecords)): ?>
                    <?php foreach ($customerRecords as $record): ?>
                        <tr>
                            <td><?= esc($record->product_bought) ?></td>
                            <td>
                                <span class="category-badge <?= esc(strtolower($record->product_category)) ?>">
                                    <?= esc($record->product_category) ?>
                                </span>
                            </td>
                            <td><?= (int) $record->quantity ?></td>
                            <td><?= esc(format_currency((float) $record->price)) ?></td>
                            <td><?= esc(format_currency((float) $record->price * (int) $record->quantity)) ?></td>
                            <td><?= esc(date('Y-m-d H:i', strtotime($record->purchase_date))) ?></td>
                            <td><?= esc($record->notes ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No customer records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Sales Records Section -->
<div class="dashboard-card section-spacing">
    <div class="card-header">
        <h2>Sales Records</h2>
        <div class="card-icon">💰</div>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Sale Date</th>
                    <th>Total Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($sales)): ?>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><strong><?= esc($sale->invoice_no) ?></strong></td>
                            <td><?= esc(date('Y-m-d H:i', strtotime($sale->sale_date))) ?></td>
                            <td><?= esc(format_currency((float) $sale->total_amount)) ?></td>
                            <td class="actions">
                                <a href="<?= site_url('sales/print/' . $sale->id) ?>" class="btn btn-sm btn-secondary" target="_blank">Print</a>
                                <a href="<?= site_url('sales/pdf/' . $sale->id) ?>" class="btn btn-sm btn-primary">PDF</a>
                            </td>
                        </tr>
                        <?php if (isset($saleDetails[$sale->id])): ?>
                            <tr class="sale-items-row">
                                <td colspan="4">
                                    <div class="sale-items">
                                        <strong>Items:</strong>
                                        <ul>
                                            <?php foreach ($saleDetails[$sale->id] as $item): ?>
                                                <li>
                                                    <?= esc($item->product_name) ?> - 
                                                    <?= (int) $item->quantity ?> x 
                                                    <?= esc(format_currency((float) $item->unit_price)) ?> = 
                                                    <?= esc(format_currency((float) $item->subtotal)) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No sales records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('inventory/templates/footer') ?>
