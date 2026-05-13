<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Customer Records') ?></h1>
    <div class="header-actions">
        <a href="<?= site_url('customer/create') ?>" class="btn btn-primary">+ Add Customer</a>
        <a href="<?= site_url('customer/create-sale') ?>" class="btn btn-success">+ New Sale</a>
    </div>
</div>

<!-- Search Form -->
<div class="form-container section-spacing">
    <form action="<?= site_url('customer') ?>" method="get" class="product-form">
        <div class="form-row">
            <div class="form-group">
                <label for="search">Search Customers</label>
                <input type="text" id="search" name="search" value="<?= esc($search ?? '') ?>" placeholder="Search by name, product, or category...">
            </div>
            <div class="form-group form-actions-inline">
                <button type="submit" id="search_btn" name="submit" class="btn btn-secondary">Search</button>
                <a href="<?= site_url('customer') ?>" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>
</div>

<!-- Customer Summary Cards -->
<div class="dashboard-grid">
    <!-- Customer Summary -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2>Top Customers</h2>
            <div class="card-icon">👥</div>
        </div>
        <div class="customer-summary">
            <?php if (!empty($customerSummary)): ?>
                <?php foreach (array_slice($customerSummary, 0, 5) as $summary): ?>
                    <div class="summary-item">
                        <div class="summary-name"><?= esc($summary->customer_name) ?></div>
                        <div class="summary-details">
                            <span class="detail-item"><?= (int) $summary->total_purchases ?> purchases</span>
                            <span class="detail-item"><?= esc(format_currency((float) $summary->total_spent)) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data-state">
                    <p class="no-data">No customer data available</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Category Summary -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2>Popular Categories</h2>
            <div class="card-icon">📊</div>
        </div>
        <div class="category-summary">
            <?php if (!empty($categorySummary)): ?>
                <?php foreach (array_slice($categorySummary, 0, 5) as $summary): ?>
                    <div class="summary-item">
                        <div class="summary-name"><?= esc($summary->product_category) ?></div>
                        <div class="summary-details">
                            <span class="detail-item"><?= (int) $summary->total_quantity ?> items</span>
                            <span class="detail-item"><?= esc(format_currency((float) $summary->total_revenue)) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data-state">
                    <p class="no-data">No category data available</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Customer Records Table -->
<div class="table-container section-spacing">
    <table class="data-table">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Product Bought</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Purchase Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($customers)): ?>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><strong><?= esc($customer->customer_name) ?></strong></td>
                        <td><?= esc($customer->product_bought) ?></td>
                        <td>
                            <span class="category-badge <?= esc(strtolower($customer->product_category)) ?>">
                                <?= esc($customer->product_category) ?>
                            </span>
                        </td>
                        <td><?= (int) $customer->quantity ?></td>
                        <td><?= esc(format_currency((float) $customer->price)) ?></td>
                        <td><?= esc(format_currency((float) $customer->price * (int) $customer->quantity)) ?></td>
                        <td><?= esc(date('Y-m-d H:i', strtotime($customer->purchase_date))) ?></td>
                        <td class="actions">
                            <a href="<?= site_url('customer/view-sales/' . urlencode($customer->customer_name)) ?>" class="btn btn-sm btn-info">View Sales</a>
                            <a href="<?= site_url('customer/edit/' . $customer->id) ?>" class="btn btn-sm btn-secondary">Edit</a>
                            <form method="post" action="<?= site_url('customer/delete/' . $customer->id) ?>" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this customer record?')">
                                <?= csrf_field() ?>
                                <button type="submit" id="delete_btn_<?= $customer->id ?>" name="delete" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No customer records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->include('inventory/templates/footer') ?>
