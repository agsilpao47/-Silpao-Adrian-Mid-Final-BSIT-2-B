<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Inventory System</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <div class="profile-placeholder" aria-hidden="true"></div>
            <div class="brand-text">
                <h2>Inventory System</h2>
                <p class="nav-subtitle">Stock and Sales Management</p>
            </div>
        </div>
        <ul class="nav-menu">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
            <li><a href="<?= site_url('stock/inventory') ?>">Stock/Inventory</a></li>
            <li><a href="<?= site_url('stock/fixed-prices') ?>">Fixed Prices</a></li>
            <li><a href="<?= site_url('stock/inventory/add') ?>">Add Product</a></li>
            <li><a href="<?= site_url('sales') ?>">Sales</a></li>
            <li><a href="<?= site_url('customer') ?>">Customer Records</a></li>
            <?php if (session()->get('isLoggedIn')): ?>
                <li>
                    <form action="<?= site_url('store/currency') ?>" method="post" class="inline-form currency-form">
                        <?= csrf_field() ?>
                        <select name="currency" id="currency_selector" onchange="this.form.submit()">
                            <option value="php" <?= get_currency_code() === 'php' ? 'selected' : '' ?>>Peso (PHP)</option>
                            <option value="usd" <?= get_currency_code() === 'usd' ? 'selected' : '' ?>>Dollar (USD)</option>
                        </select>
                    </form>
                </li>
            <?php endif; ?>
            <?php if (session()->get('isLoggedIn')): ?>
                <li><a href="<?= site_url('logout') ?>">Logout (<?= esc(session()->get('username')) ?>)</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <div class="container">
        <?php $successMessage = session()->getFlashdata('success'); ?>
        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?= esc($successMessage) ?></div>
        <?php endif; ?>
        <?php $errorMessage = session()->getFlashdata('error'); ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert-error"><?= esc($errorMessage) ?></div>
        <?php endif; ?>

        <div class="page-header">
            <h1><?= esc($title) ?></h1>
        </div>

        <div class="dashboard-grid">
            <!-- Income Summary -->
            <div class="dashboard-card income-card">
                <div class="card-header">
                    <h2>Income Summary</h2>
                    <div class="card-icon">💰</div>
                </div>
                <div class="income-filters">
                    <form method="get" class="filter-form">
                        <div class="preset-buttons">
                            <button type="submit" name="preset" value="today" class="preset-btn <?= $preset === 'today' ? 'active' : '' ?>">Today</button>
                            <button type="submit" name="preset" value="week" class="preset-btn <?= $preset === 'week' ? 'active' : '' ?>">Week</button>
                            <button type="submit" name="preset" value="month" class="preset-btn <?= $preset === 'month' ? 'active' : '' ?>">Month</button>
                            <button type="submit" name="preset" value="year" class="preset-btn <?= $preset === 'year' ? 'active' : '' ?>">Year</button>
                            <a href="<?= site_url('dashboard') ?>" class="btn btn-outline">Clear</a>
                        </div>
                    </form>
                </div>
                <div class="income-display">
                    <div class="income-amount">
                        <span class="income-label">Total Income:</span>
                        <span class="income-value"><?= esc(format_currency($totalIncome)) ?></span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-content">
                        <div class="stat-value"><?= !empty($inventorySummary) ? array_sum(array_column($inventorySummary, 'count')) : 0 ?></div>
                        <div class="stat-label">Total Products</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <div class="stat-value"><?= !empty($inventorySummary) ? count($inventorySummary) : 0 ?></div>
                        <div class="stat-label">Categories</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Summary -->
        <div class="dashboard-card inventory-card">
            <div class="card-header">
                <h2>Inventory Summary</h2>
                <div class="card-icon">📋</div>
            </div>
            <div class="inventory-summary">
                <?php if (!empty($inventorySummary)): ?>
                    <?php foreach ($inventorySummary as $summary): ?>
                        <div class="summary-item enhanced">
                            <div class="summary-category">
                                <span class="category-badge <?= esc(strtolower((string) ($summary->category ?? ''))) ?>"><?= esc((string) ($summary->category ?? '')) ?></span>
                            </div>
                            <div class="summary-details">
                                <div class="detail-item">
                                    <span class="detail-label">Items:</span>
                                    <span class="detail-value"><?= (int) $summary->count ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Quantity:</span>
                                    <span class="detail-value"><?= (int) $summary->total_qty ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data-state">
                        <div class="no-data-icon">📦</div>
                        <p class="no-data">No inventory data available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Category Pricing Overview -->
        <div class="dashboard-card pricing-card">
            <div class="card-header">
                <h2>Category Pricing Overview</h2>
                <div class="card-icon">💵</div>
            </div>
            <div class="pricing-overview">
                <?php if (!empty($categoryPricing)): ?>
                    <div class="category-cards-grid">
                        <?php foreach ($categoryPricing as $category): ?>
                            <div class="category-pricing-card">
                                <div class="category-card-header">
                                    <span class="category-badge large"><?= esc($category->category ?? 'N/A') ?></span>
                                    <div class="product-count">
                                        <span class="count-number"><?= (int) $category->products_count ?></span>
                                        <span class="count-label">Products</span>
                                    </div>
                                </div>
                                <div class="pricing-details">
                                    <div class="price-item">
                                        <div class="price-label">Avg Buying</div>
                                        <div class="price-value buying"><?= esc(format_currency((float) $category->avg_buying_price)) ?></div>
                                    </div>
                                    <div class="price-item">
                                        <div class="price-label">Avg Selling</div>
                                        <div class="price-value selling"><?= esc(format_currency((float) $category->avg_selling_price)) ?></div>
                                    </div>
                                    <div class="margin-display">
                                        <div class="margin-label">Profit Margin</div>
                                        <div class="margin-value <?= ((float)($category->avg_selling_price - $category->avg_buying_price) / (float)$category->avg_buying_price * 100) > 20 ? 'high-margin' : (((float)($category->avg_selling_price - $category->avg_buying_price) / (float)$category->avg_buying_price * 100) > 10 ? 'medium-margin' : 'normal-margin') ?>">
                                            <?= number_format((float)($category->avg_selling_price - $category->avg_buying_price) / (float)$category->avg_buying_price * 100, 1) ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-data-state">
                        <div class="no-data-icon">💵</div>
                        <p class="no-data">No pricing data available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="dashboard-card products-card">
            <div class="card-header">
                <h2>Product Pricing Overview</h2>
                <div class="card-icon">🏷️</div>
            </div>
            <div class="product-pricing">
                <?php if (!empty($productPricing)): ?>
                    <div class="products-grid">
                        <?php foreach ($productPricing as $product): ?>
                            <div class="product-pricing-card">
                                <div class="product-card-header">
                                    <div class="product-info">
                                        <div class="product-code-badge"><?= esc($product->product_code) ?></div>
                                        <div class="product-name"><?= esc($product->product_name) ?></div>
                                    </div>
                                </div>
                                <div class="product-pricing-details">
                                    <div class="price-row">
                                        <div class="price-box buying">
                                            <div class="price-box-label">Buying</div>
                                            <div class="price-box-amount"><?= esc(format_currency((float) $product->buying_price)) ?></div>
                                        </div>
                                        <div class="price-box selling">
                                            <div class="price-box-label">Selling</div>
                                            <div class="price-box-amount"><?= esc(format_currency((float) $product->selling_price)) ?></div>
                                        </div>
                                    </div>
                                    <div class="profit-row">
                                        <div class="profit-info">
                                            <div class="profit-amount">
                                                <?= esc(format_currency((float)($product->selling_price - $product->buying_price))) ?>
                                            </div>
                                            <div class="profit-margin <?= ((float)($product->selling_price - $product->buying_price) / (float)$product->buying_price * 100) > 20 ? 'high-margin' : (((float)($product->selling_price - $product->buying_price) / (float)$product->buying_price * 100) > 10 ? 'medium-margin' : 'normal-margin') ?>">
                                                <?= number_format((float)($product->selling_price - $product->buying_price) / (float)$product->buying_price * 100, 1) ?>% margin
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-data-state">
                        <div class="no-data-icon">🏷️</div>
                        <p class="no-data">No products found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?= date('Y') ?> Inventory System - Built with CodeIgniter 4</p>
        </div>
    </footer>
    
    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this product?');
        }
    </script>
</body>
</html>
