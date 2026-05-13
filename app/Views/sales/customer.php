<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Customer Purchase Details') ?></h1>
    <a href="<?= site_url('sales?preset=' . urlencode((string) ($preset ?? '')) . '&date_from=' . urlencode((string) ($dateFrom ?? '')) . '&date_to=' . urlencode((string) ($dateTo ?? ''))) ?>" class="btn btn-secondary">← Back to Sales</a>
</div>

<div class="form-container section-spacing">
    <div class="form-row">
        <div class="form-group">
            <label>Customer</label>
            <input type="text" value="<?= esc($customer ?? 'Walk-in Customer') ?>" class="readonly" readonly>
        </div>
        <div class="form-group">
            <label>Date Range</label>
            <input type="text" value="<?= esc(($dateFrom ?: 'Start') . ' to ' . ($dateTo ?: 'End')) ?>" class="readonly" readonly>
        </div>
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Sale Date</th>
                <th>Items Bought</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($sales)): ?>
                <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><strong><?= esc($sale->invoice_no) ?></strong></td>
                        <td><?= esc(date('Y-m-d H:i', strtotime($sale->sale_date))) ?></td>
                        <td>
                            <?php $items = $saleDetails[(int) $sale->id] ?? []; ?>
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $item): ?>
                                    <div>
                                        <?= esc($item->product_name) ?>
                                        (<?= (int) $item->quantity ?> x <?= esc(format_currency((float) $item->unit_price)) ?>)
                                        = <?= esc(format_currency((float) $item->subtotal)) ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-center">No item details</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc(format_currency((float) $sale->total_amount)) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No sales found for this customer.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->include('inventory/templates/footer') ?>
