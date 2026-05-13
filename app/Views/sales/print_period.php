<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - <?= esc(strtoupper($preset ?: 'custom')) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #222; }
        .top { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .total { font-weight: 700; }
        .sale-items { margin: 6px 0 0 0; font-size: 12px; }
        .actions { margin-top: 20px; }
        @media print { .actions { display: none; } }
    </style>
</head>
<body>
    <div class="top">
        <h2>Sales Report (<?= esc(strtoupper($preset ?: 'CUSTOM')) ?>)</h2>
        <p><strong>Date Range:</strong> <?= esc(($dateFrom ?: 'Start') . ' to ' . ($dateTo ?: 'End')) ?></p>
        <p><strong>Printed By:</strong> <?= esc($printedBy) ?></p>
    </div>

    <?php if ($detailed): ?>
        <table>
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?= esc($sale->invoice_no) ?></td>
                    <td><?= esc(date('Y-m-d H:i', strtotime($sale->sale_date))) ?></td>
                    <td><?= esc($sale->customer_name ?: 'Walk-in Customer') ?></td>
                    <td>
                        <?php foreach (($saleItemsMap[(int) $sale->id] ?? []) as $item): ?>
                            <div class="sale-items">
                                <?= esc($item->product_name) ?> (<?= (int) $item->quantity ?> x <?= esc(format_currency((float) $item->unit_price)) ?>)
                            </div>
                        <?php endforeach; ?>
                    </td>
                    <td class="text-right"><?= esc(format_currency((float) $sale->total_amount)) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" class="text-right total">Total Sales</td>
                <td class="text-right total"><?= esc(format_currency((float) $totalSalesAmount)) ?></td>
            </tr>
            </tbody>
        </table>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Period</th>
                    <th class="text-right">Total Sales</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= esc(ucfirst($preset ?: 'Custom')) ?> Summary</td>
                    <td class="text-right"><?= esc(format_currency((float) $totalSalesAmount)) ?></td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="actions">
        <button onclick="window.print()">Print Report</button>
        <button onclick="window.close()">Close</button>
    </div>
</body>
</html>
