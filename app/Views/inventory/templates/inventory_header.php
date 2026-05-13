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
            <li><a href="<?= site_url('stock/inventory') ?>" class="nav-active">Inventory</a></li>
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
