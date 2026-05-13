<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= esc($sale->invoice_no) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #222; }
        .top { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .title { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .total { font-weight: 700; }
        .actions { margin-top: 20px; }
        @media print { .actions { display: none; } }
    </style>
</head>
<body>
    <div class="top">
        <div>
            <h2 class="title">Sales Invoice</h2>
            <p><strong>Invoice No:</strong> <?= esc($sale->invoice_no) ?></p>
            <p><strong>Date:</strong> <?= esc(date('Y-m-d H:i', strtotime($sale->sale_date))) ?></p>
        </div>
        <div>
            <p><strong>Customer:</strong> <?= esc($sale->customer_name ?: 'Walk-in Customer') ?></p>
            <p><strong>Printed By:</strong> <?= esc($printedBy) ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Weight</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= esc($item->product_name) ?></td>
                    <td><?= esc($item->cylinder_weight ?? '-') ?></td>
                    <td><?= (int) $item->quantity ?></td>
                    <td class="text-right"><?= esc(format_currency((float) $item->unit_price)) ?></td>
                    <td class="text-right"><?= esc(format_currency((float) $item->subtotal)) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" class="text-right total">Total</td>
                <td class="text-right total"><?= esc(format_currency((float) $sale->total_amount)) ?></td>
            </tr>
        </tbody>
    </table>

    <?php if (!empty($sale->notes)): ?>
        <p><strong>Notes:</strong> <?= esc($sale->notes) ?></p>
    <?php endif; ?>

    <div class="actions">
        <button onclick="window.print()">Print Invoice</button>
        <button onclick="window.close()">Close</button>
    </div>
</body>
</html>
