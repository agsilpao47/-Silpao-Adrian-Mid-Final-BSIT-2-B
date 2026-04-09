<div class="page-header">
    <h1><?php echo $title; ?></h1>
    <a href="<?php echo site_url('inventory/add'); ?>" class="btn btn-primary">+ Add New Product</a>
</div>

<!-- Statistics Cards -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-info">
            <h3><?php echo $total_products; ?></h3>
            <p>Total Products</p>
        </div>
    </div>
    <div class="stat-card stat-warning">
        <div class="stat-icon">⚠️</div>
        <div class="stat-info">
            <h3><?php echo count($low_stock); ?></h3>
            <p>Low Stock Items</p>
        </div>
    </div>
    <div class="stat-card stat-info">
        <div class="stat-icon">📊</div>
        <div class="stat-info">
            <h3><?php echo count($products); ?></h3>
            <p>Showing</p>
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="search-container">
    <form action="<?php echo site_url('inventory/search'); ?>" method="get" class="search-form">
        <input type="text" name="keyword" placeholder="Search products..." class="search-input">
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
                    <td><strong><?php echo $product->product_code; ?></strong></td>
                    <td><?php echo $product->product_name; ?></td>
                    <td><span class="badge"><?php echo $product->category ?: 'N/A'; ?></span></td>
                    <td class="<?php echo $product->quantity < 10 ? 'low-stock' : ''; ?>">
                        <?php echo $product->quantity; ?> <?php echo $product->unit; ?>
                    </td>
                    <td>$<?php echo number_format($product->buying_price, 2); ?></td>
                    <td>$<?php echo number_format($product->selling_price, 2); ?></td>
                    <td><?php echo $product->supplier ?: 'N/A'; ?></td>
                    <td class="actions">
                        <a href="<?php echo site_url('inventory/view/'.$product->id); ?>" class="btn btn-sm btn-info">View</a>
                        <a href="<?php echo site_url('inventory/edit/'.$product->id); ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="<?php echo site_url('inventory/delete/'.$product->id); ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete();">Delete</a>
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
</div><div class="page-header">
    <h1><?php echo $title; ?></h1>
    <a href="<?php echo site_url('inventory/add'); ?>" class="btn btn-primary">+ Add New Product</a>
</div>

<!-- Statistics Cards -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-info">
            <h3><?php echo $total_products; ?></h3>
            <p>Total Products</p>
        </div>
    </div>
    <div class="stat-card stat-warning">
        <div class="stat-icon">⚠️</div>
        <div class="stat-info">
            <h3><?php echo count($low_stock); ?></h3>
            <p>Low Stock Items</p>
        </div>
    </div>
    <div class="stat-card stat-info">
        <div class="stat-icon">📊</div>
        <div class="stat-info">
            <h3><?php echo count($products); ?></h3>
            <p>Showing</p>
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="search-container">
    <form action="<?php echo site_url('inventory/search'); ?>" method="get" class="search-form">
        <input type="text" name="keyword" placeholder="Search products..." class="search-input">
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
                    <td><strong><?php echo $product->product_code; ?></strong></td>
                    <td><?php echo $product->product_name; ?></td>
                    <td><span class="badge"><?php echo $product->category ?: 'N/A'; ?></span></td>
                    <td class="<?php echo $product->quantity < 10 ? 'low-stock' : ''; ?>">
                        <?php echo $product->quantity; ?> <?php echo $product->unit; ?>
                    </td>
                    <td>$<?php echo number_format($product->buying_price, 2); ?></td>
                    <td>$<?php echo number_format($product->selling_price, 2); ?></td>
                    <td><?php echo $product->supplier ?: 'N/A'; ?></td>
                    <td class="actions">
                        <a href="<?php echo site_url('inventory/view/'.$product->id); ?>" class="btn btn-sm btn-info">View</a>
                        <a href="<?php echo site_url('inventory/edit/'.$product->id); ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="<?php echo site_url('inventory/delete/'.$product->id); ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete();">Delete</a>
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