= $this->include('templates/header') ?>

<div class="page-header">
    <h1>= $title ?></h1>
    <a href="= site_url('inventory/add') ?>" class="btn btn-primary">+ Add New Product</a>
</div>

<!-- Statistics Cards -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-info">
            <h3>= $totalProducts ?></h3>
            <p>Total Products</p>
        </div>
    </div>
    <div class="stat-card stat-warning">
        <div class="stat-icon">⚠️</div>
        <div class="stat-info">
            <h3>= count($lowStock) ?></h3>
            <p>Low Stock Items</p>
        </div>
    </div>
    <div class="stat-card stat-info">
        <div class="stat-icon">📊</div>
        <div class="stat-info">
            <h3>= count($products) ?></h3>
            <p>Showing</p>
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="search-container">
    <form action="= site_url('inventory/search') ?>" method="get" class="search-form">
        <input type="text" name="keyword" placeholder="Search products..." class="search-input" value="= isset($keyword) ? esc($keyword) : '' ?>">
        <button type="submit" class="btn btn-search">🔍 Search</button>
    </form>
</div>

<!-- Products Table -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Buying Price</th>
                <th>Selling Price</th>
                <th>Supplier</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($products)): ?>
                <?php foreach($products as $product): ?>
                <tr>
                    <td><strong>= esc($product->product_code) ?></strong></td>
                    <td>= esc($product->product_name) ?></td>
                    <td><span class="badge">= esc($product->category ?? 'N/A') ?></span></td>
                    <td class="= $product->quantity < 10 ? 'low-stock' : '' ?>">
                        = $product->quantity ?> = esc($product->unit ?? '')
                    </td>
                    <td>$= number_format($product->buying_price, 2) ?></td>
                    <td>$= number_format($product->selling_price, 2) ?></td>
                    <td>= esc($product->supplier ?? 'N/A') ?></td>
                    <td class="actions">
                        <a href="= site_url('inventory/view/' . $product->id) ?>" class="btn btn-sm btn-info">View</a>
                        <a href="= site_url('inventory/edit/' . $product->id) ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="= site_url('inventory/delete/' . $product->id) ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete();">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

= $this->include('templates/footer') ?>