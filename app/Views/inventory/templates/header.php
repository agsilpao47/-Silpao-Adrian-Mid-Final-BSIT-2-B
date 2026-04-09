<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>= $title ?> - Inventory System</title>
    <link rel="stylesheet" href="= base_url('assets/css/style.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <h2>📦 Inventory System</h2>
        </div>
        <ul class="nav-menu">
            <li><a href="= site_url('/') ?>">Dashboard</a></li>
            <li><a href="= site_url('inventory/add') ?>">Add Product</a></li>
        </ul>
    </nav>
    
    <div class="container">
        = session()->getFlashdata('success') ? '<div class="alert alert-success">' . session()->getFlashdata('success') . '</div>' : '' ?>
        = session()->getFlashdata('error') ? '<div class="alert alert-error">' . session()->getFlashdata('error') . '</div>' : '' ?>