<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Sales Records') ?></h1>
</div>

<div class="form-container section-spacing">
    <form action="<?= site_url('sales') ?>" method="get" class="product-form">
        <div class="form-row compact-row">
            <div class="form-group quick-filter-group">
                <a href="<?= site_url('sales?preset=today') ?>" class="btn btn-sm btn-secondary">Today</a>
                <a href="<?= site_url('sales?preset=week') ?>" class="btn btn-sm btn-secondary">This Week</a>
                <a href="<?= site_url('sales?preset=month') ?>" class="btn btn-sm btn-secondary">This Month</a>
                <a href="<?= site_url('sales?preset=year') ?>" class="btn btn-sm btn-secondary">This Year</a>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="date_from">From Date</label>
                <input type="date" id="date_from" name="date_from" value="<?= esc($dateFrom ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="date_to">To Date</label>
                <input type="date" id="date_to" name="date_to" value="<?= esc($dateTo ?? '') ?>">
            </div>
            <div class="form-group form-actions-inline">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="<?= site_url('sales') ?>" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>
</div>

<div class="form-container section-spacing">
    <div class="form-row">
        <div class="form-group">
            <label>Total Sales (Filtered)</label>
            <input type="text" value="<?= esc(format_currency((float) ($totalSalesAmount ?? 0))) ?>" readonly class="readonly">
        </div>
        <div class="form-group form-actions-inline">
            <a href="<?= site_url('sales/print-period?preset=' . urlencode((string) ($preset ?? '')) . '&date_from=' . urlencode((string) ($dateFrom ?? '')) . '&date_to=' . urlencode((string) ($dateTo ?? ''))) ?>" target="_blank" class="btn btn-secondary">Print Period</a>
            <a href="<?= site_url('sales/pdf-period?preset=' . urlencode((string) ($preset ?? '')) . '&date_from=' . urlencode((string) ($dateFrom ?? '')) . '&date_to=' . urlencode((string) ($dateTo ?? ''))) ?>" class="btn btn-primary">PDF Period</a>
        </div>
    </div>
</div>

<div class="table-container section-spacing">
    <table class="data-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Transactions</th>
                <th>Total Amount</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($customerSummary)): ?>
                <?php foreach ($customerSummary as $row): ?>
                    <?php
                        $customerName = (string) ($row->customer_name ?? 'Walk-in Customer');
                        $customerParam = strtolower($customerName) === 'walk-in customer' ? '__walkin__' : $customerName;
                    ?>
                    <tr>
                        <td><?= esc($customerName) ?></td>
                        <td><?= (int) ($row->total_transactions ?? 0) ?></td>
                        <td><?= esc(format_currency((float) ($row->total_amount ?? 0))) ?></td>
                        <td>
                            <a href="<?= site_url('sales/customer?customer=' . urlencode($customerParam) . '&preset=' . urlencode((string) ($preset ?? '')) . '&date_from=' . urlencode((string) ($dateFrom ?? '')) . '&date_to=' . urlencode((string) ($dateTo ?? ''))) ?>" class="btn btn-sm btn-info">View Purchases</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No customer sales found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Sale Date</th>
                <th>Customer</th>
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
                        <td><?= esc($sale->customer_name ?: 'Walk-in Customer') ?></td>
                        <td><?= esc(format_currency((float) $sale->total_amount)) ?></td>
                        <td class="actions">
                            <a href="<?= site_url('sales/view/' . $sale->id) ?>" class="btn btn-sm btn-info">View</a>
                            <a href="<?= site_url('sales/print/' . $sale->id) ?>" class="btn btn-sm btn-secondary" target="_blank">Print</a>
                            <a href="<?= site_url('sales/pdf/' . $sale->id) ?>" class="btn btn-sm btn-primary">PDF</a>
                            <form method="post" action="<?= site_url('sales/delete/' . $sale->id) ?>" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this sale record? This will restore the stock quantities.')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No sales records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->include('inventory/templates/footer') ?>
