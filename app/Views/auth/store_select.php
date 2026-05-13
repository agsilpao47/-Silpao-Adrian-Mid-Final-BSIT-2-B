<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - Inventory System</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <div class="container login-container">
        <div class="form-container login-form">
            <h1>Select Store Type</h1>
            <p class="login-subtitle">Choose your store mode before proceeding.</p>

            <?php $errorMessage = session()->getFlashdata('error'); ?>
            <?php if ($errorMessage): ?>
                <div class="alert alert-error"><?= esc($errorMessage) ?></div>
            <?php endif; ?>

            <form action="<?= site_url('store/select') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="store_type">Store Type</label>
                    <select id="store_type" name="store_type" required>
                        <option value="lpg" selected>LPG Store</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Continue</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
