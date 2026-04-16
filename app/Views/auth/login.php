<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - Inventory System</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container login-container">
        <div class="form-container login-form">
            <h1>Inventory System Login</h1>
            <p class="login-subtitle">Please sign in to continue.</p>

            <?= session()->getFlashdata('success') ? '<div class="alert alert-success">' . session()->getFlashdata('success') . '</div>' : '' ?>
            <?= session()->getFlashdata('error') ? '<div class="alert alert-error">' . session()->getFlashdata('error') . '</div>' : '' ?>

            <form action="<?= site_url('login') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= old('username') ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
