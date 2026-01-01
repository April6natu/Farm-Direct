<?php
/**
 * Header Include File
 * Farm-Direct Agricultural eCommerce Platform
 * 
 * This file is included at the top of every page to provide
 * consistent navigation and branding across the platform.
 */

require_once __DIR__ . '/../functions.php';

$current_user = get_current_user();
$cart_count = 0;

// Get cart count for logged-in buyers
if ($current_user && $current_user['role'] === 'buyer') {
    $cart_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param("i", $current_user['id']);
    $stmt->execute();
    $cart_result = $stmt->get_result();
    $cart_row = $cart_result->fetch_assoc();
    $cart_count = $cart_row['total'] ?? 0;
}

// Get notification count for sellers
$notification_count = 0;
if ($current_user && $current_user['role'] === 'seller') {
    $notification_count = get_unread_notification_count($current_user['id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? clean_output($page_title) . ' - ' : '' ?>Farm-Direct</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 2) ?>assets/css/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= $current_user ? ($current_user['role'] === 'admin' ? '/admin/dashboard.php' : ($current_user['role'] === 'seller' ? '/seller/dashboard.php' : '/browse.php')) : '/login.php' ?>">
                <svg class="me-2" width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <span>Farm-Direct</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if ($current_user): ?>
                        <?php if ($current_user['role'] === 'buyer'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/browse.php">Browse Products</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/dashboard.php">My Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link position-relative" href="/cart.php">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <?php if ($cart_count > 0): ?>
                                        <span class="cart-badge"><?= $cart_count ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php elseif ($current_user['role'] === 'seller'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/seller/dashboard.php">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/seller/products.php">My Products</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link position-relative" href="/seller/dashboard.php#notifications">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <?php if ($notification_count > 0): ?>
                                        <span class="cart-badge"><?= $notification_count ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php elseif ($current_user['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/dashboard.php">Dashboard</a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="nav-item dropdown ms-2">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <?= clean_output($current_user['name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text small text-muted"><?= clean_output($current_user['email']) ?></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light btn-sm ms-2" href="/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="container my-4">
