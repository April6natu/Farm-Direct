<?php
/**
 * Seller Dashboard
 * Farm-Direct Agricultural eCommerce Platform
 * 
 * Main dashboard for sellers to view sales, notifications, and statistics.
 * Uses CRUD READ operations for fetching data.
 */

require_once '../db.php';
require_once '../functions.php';

$page_title = 'Seller Dashboard';

// Require seller role
require_role('seller', '../browse.php');

$user_id = $_SESSION['user_id'];

// READ: Get seller statistics
$stats_sql = "SELECT 
              COUNT(DISTINCT p.id) as total_products,
              COALESCE(SUM(p.stock), 0) as total_stock,
              COUNT(DISTINCT oi.order_id) as total_sales,
              COALESCE(SUM(oi.price * oi.quantity), 0) as total_revenue
              FROM products p
              LEFT JOIN order_items oi ON p.id = oi.product_id
              WHERE p.seller_id = ?";
$stmt = $conn->prepare($stats_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// READ: Get recent sales (last 10)
$sales_sql = "SELECT oi.*, o.created_at, o.status, o.delivery_location,
              u.name as buyer_name
              FROM order_items oi
              JOIN orders o ON oi.order_id = o.id
              JOIN users u ON o.buyer_id = u.id
              WHERE oi.seller_id = ?
              ORDER BY o.created_at DESC
              LIMIT 10";
$stmt = $conn->prepare($sales_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_sales = $stmt->get_result();

// READ: Get notifications (unread first)
$notif_sql = "SELECT * FROM notifications 
              WHERE user_id = ? 
              ORDER BY is_read ASC, created_at DESC 
              LIMIT 15";
$stmt = $conn->prepare($notif_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();

// READ: Get low stock products (stock < 10)
$low_stock_sql = "SELECT * FROM products 
                  WHERE seller_id = ? AND stock < 10 AND status = 'active'
                  ORDER BY stock ASC
                  LIMIT 5";
$stmt = $conn->prepare($low_stock_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$low_stock = $stmt->get_result();
?>

<?php include '../includes/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold">Seller Dashboard</h2>
        <p class="text-muted">Manage your products and track your sales</p>
    </div>
    <div class="col-auto">
        <a href="add_product.php" class="btn btn-success">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Add New Product
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-label">Total Products</div>
            <div class="stat-value"><?= $stats['total_products'] ?></div>
            <a href="products.php" class="small text-success">Manage Products →</a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-label">Total Stock</div>
            <div class="stat-value"><?= number_format($stats['total_stock']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-label">Total Sales</div>
            <div class="stat-value"><?= $stats['total_sales'] ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-label">Revenue</div>
            <div class="stat-value text-success"><?= format_price($stats['total_revenue']) ?></div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Sales -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Recent Sales</h5>
            </div>
            <div class="card-body">
                <?php if ($recent_sales->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Buyer</th>
                                    <th>Qty</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($sale = $recent_sales->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold"><?= clean_output($sale['product_name']) ?></td>
                                        <td><?= clean_output($sale['buyer_name']) ?></td>
                                        <td><?= $sale['quantity'] ?> <?= clean_output($sale['unit']) ?></td>
                                        <td class="text-success fw-bold"><?= format_price($sale['price'] * $sale['quantity']) ?></td>
                                        <td><?= time_ago($sale['created_at']) ?></td>
                                        <td>
                                            <span class="badge <?= get_status_badge_class($sale['status']) ?>">
                                                <?= ucfirst($sale['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p class="text-muted">No sales yet. Add products to start selling!</p>
                        <a href="add_product.php" class="btn btn-success">Add Product</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Low Stock Alert -->
        <?php if ($low_stock->num_rows > 0): ?>
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                        Low Stock Alert
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">These products are running low on stock:</p>
                    <div class="list-group">
                        <?php while ($product = $low_stock->fetch_assoc()): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><?= clean_output($product['name']) ?></span>
                                <span class="badge bg-danger"><?= $product['stock'] ?> left</span>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <a href="products.php" class="btn btn-sm btn-warning mt-3">Update Stock</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Notifications -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center" id="notifications">
                <h5 class="mb-0">Notifications</h5>
                <span class="badge bg-success"><?= get_unread_notification_count($user_id) ?></span>
            </div>
            <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                <?php if ($notifications->num_rows > 0): ?>
                    <?php while ($notif = $notifications->fetch_assoc()): ?>
                        <div class="notification-item <?= !$notif['is_read'] ? 'unread' : '' ?>" 
                             id="notification-<?= $notif['id'] ?>"
                             onclick="markNotificationRead(<?= $notif['id'] ?>)">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="mb-1"><?= clean_output($notif['message']) ?></p>
                                    <small class="text-muted"><?= time_ago($notif['created_at']) ?></small>
                                </div>
                                <?php if (!$notif['is_read']): ?>
                                    <div class="ms-2">
                                        <span class="badge bg-success rounded-circle" style="width: 10px; height: 10px; padding: 0;"></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <svg width="48" height="48" fill="currentColor" viewBox="0 0 16 16" class="mb-2 opacity-25">
                            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                        </svg>
                        <p>No notifications yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
