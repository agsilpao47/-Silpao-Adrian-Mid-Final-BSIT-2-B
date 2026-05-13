<?= $this->include('inventory/templates/header') ?>

<div class="page-header">
    <h1><?= esc($title ?? 'Edit Customer Record') ?></h1>
    <a href="<?= site_url('customer') ?>" class="btn btn-secondary">← Back to Customer Records</a>
</div>

<div class="form-container">
    <form action="<?= site_url('customer/edit/' . $customer->id) ?>" method="post" class="product-form">
        <?= csrf_field('csrf_token') ?>
        
        <div class="form-row">
            <div class="form-group">
                <label for="customer_name">Customer Name *</label>
                <input type="text" id="customer_name" name="customer_name" value="<?= old('customer_name', $customer->customer_name) ?>" required class="<?= session('error.customer_name') ? 'is-invalid' : '' ?>">
                <?php if (session('error.customer_name')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.customer_name')) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="purchase_date">Purchase Date *</label>
                <input type="datetime-local" id="purchase_date" name="purchase_date" value="<?= old('purchase_date', date('Y-m-d\TH:i', strtotime($customer->purchase_date))) ?>" required class="<?= session('error.purchase_date') ? 'is-invalid' : '' ?>">
                <?php if (session('error.purchase_date')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.purchase_date')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="product_bought">Product Bought *</label>
                <input type="text" id="product_bought" name="product_bought" value="<?= old('product_bought', $customer->product_bought) ?>" required class="<?= session('error.product_bought') ? 'is-invalid' : '' ?>">
                <?php if (session('error.product_bought')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.product_bought')) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="product_category">Product Category *</label>
                <select id="product_category" name="product_category" required class="<?= session('error.product_category') ? 'is-invalid' : '' ?>">
                    <option value="">Select Category</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= esc($category->category) ?>" <?= (old('product_category', $customer->product_category) == $category->category) ? 'selected' : '' ?>>
                                <?= esc($category->category) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if (session('error.product_category')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.product_category')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="quantity">Quantity *</label>
                <input type="number" id="quantity" name="quantity" value="<?= old('quantity', $customer->quantity) ?>" min="1" required class="<?= session('error.quantity') ? 'is-invalid' : '' ?>">
                <?php if (session('error.quantity')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.quantity')) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="price">Price per Unit *</label>
                <input type="number" id="price" name="price" value="<?= old('price', $customer->price) ?>" step="0.01" min="0" required class="<?= session('error.price') ? 'is-invalid' : '' ?>">
                <?php if (session('error.price')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.price')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group full-width">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="<?= session('error.notes') ? 'is-invalid' : '' ?>"><?= esc(old('notes', $customer->notes ?? '')) ?></textarea>
                <?php if (session('error.notes')): ?>
                    <div class="invalid-feedback"><?= esc(session('error.notes')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary">Update Customer Record</button>
            <a href="<?= site_url('customer') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?= $this->include('inventory/templates/footer') ?>
