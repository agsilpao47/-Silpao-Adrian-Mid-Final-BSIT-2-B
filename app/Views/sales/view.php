<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'View Sale Details') ?></h1>
    <a href="<?= site_url('sales') ?>" class="btn btn-secondary">← Back to Sales Records</a>
</div>

<div class="form-container section-spacing">
    <div class="form-row">
        <div class="form-group">
            <label>Invoice Number</label>
            <input type="text" value="<?= esc($sale->invoice_no) ?>" readonly class="readonly">
        </div>
        <div class="form-group">
            <label>Sale Date</label>
            <input type="text" value="<?= esc(date('Y-m-d H:i:s', strtotime($sale->sale_date))) ?>" readonly class="readonly">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Customer Name</label>
            <input type="text" value="<?= esc($sale->customer_name ?: 'Walk-in Customer') ?>" readonly class="readonly">
        </div>
        <div class="form-group">
            <label>Total Amount</label>
            <input type="text" value="<?= esc(format_currency((float) $sale->total_amount)) ?>" readonly class="readonly">
        </div>
    </div>
    <?php if (!empty($sale->notes)): ?>
    <div class="form-row">
        <div class="form-group">
            <label>Notes</label>
            <textarea readonly class="readonly" rows="3"><?= esc($sale->notes) ?></textarea>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="table-container section-spacing">
    <h3>Sale Items</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Weight</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= esc($item->product_name) ?></td>
                        <td><?= esc($item->cylinder_weight ?: 'N/A') ?></td>
                        <td><?= (int) $item->quantity ?></td>
                        <td><?= esc(format_currency((float) $item->unit_price)) ?></td>
                        <td><?= esc(format_currency((float) $item->subtotal)) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No items found for this sale.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <?php if (!empty($items)): ?>
        <tfoot>
            <tr>
                <th colspan="4">Total</th>
                <th><?= esc(format_currency((float) $sale->total_amount)) ?></th>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div>

<div class="form-actions section-spacing">
    <a href="<?= site_url('sales/print/' . $sale->id) ?>" class="btn btn-secondary" target="_blank">🖨️ Print Invoice</a>
    <a href="<?= site_url('sales/pdf/' . $sale->id) ?>" class="btn btn-primary">📄 Download PDF</a>
    <a href="<?= site_url('sales') ?>" class="btn btn-secondary">← Back to Sales</a>
</div>

<?= $this->include('inventory/templates/footer') ?>
