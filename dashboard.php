<?php
/**
 * Buyer Dashboard Page
 * Farm-Direct Agricultural eCommerce Platform
 * 
 * Displays buyer's order history and account information.
 * Uses CRUD READ operations to fetch orders.
 */

require_once 'db.php';
require_once 'functions.php';

$page_title = 'My Dashboard';

// Require buyer role
require_role('buyer');

$user_id = $_SESSION['user_id'];
$order_success = isset($_GET['order_success']) ? true : false;

// READ: Fetch buyer's orders with order items
$orders_sql = "SELECT o.*, 
               (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
               FROM orders o 
               WHERE o.buyer_id = ? 
               ORDER BY o.created_at DESC";
$stmt = $conn->prepare($orders_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();

// READ: Get order statistics
$stats_sql = "SELECT 
              COUNT(*) as total_orders,
              SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
              SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
              COALESCE(SUM(total), 0) as total_spent
              FROM orders WHERE buyer_id = ?";
$stmt = $conn->prepare($stats_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
?>

<?php include 'includes/header.php'; ?>

<?php if ($order_success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <h5 class="alert-heading">Order Placed Successfully!</h5>
        <p>Thank you for your purchase. Your order will be delivered within 24 hours.</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold">My Dashboard</h2>
        <p class="text-muted">Welcome back, <?= clean_output($_SESSION['user_name']) ?>!</p>
    </div>
    <div class="col-auto">
        <a href="browse.php" class="btn btn-success">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4zM3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707zM2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10zm9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5zm.754-4.246a.389.389 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.389.389 0 0 0-.029-.518z"/>
                <path fill-rule="evenodd" d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A7.988 7.988 0 0 1 0 10zm8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.631A7 7 0 0 0 8 3z"/>
            </svg>
            Continue Shopping
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value"><?= $stats['total_orders'] ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-label">Pending</div>
            <div class="stat-value text-warning"><?= $stats['pending_orders'] ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-label">Delivered</div>
            <div class="stat-value text-success"><?= $stats['delivered_orders'] ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-label">Total Spent</div>
            <div class="stat-value"><?= format_price($stats['total_spent']) ?></div>
        </div>
    </div>
</div>

<!-- Orders List -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0">Order History</h4>
    </div>
    <div class="card-body">
        <?php if ($orders->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Delivery Location</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                <td><?= $order['item_count'] ?> item(s)</td>
                                <td class="fw-bold text-success"><?= format_price($order['total']) ?></td>
                                <td><?= clean_output($order['delivery_location']) ?></td>
                                <td>
                                    <span class="badge <?= get_status_badge_class($order['status']) ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="viewOrderDetails(<?= $order['id'] ?>)">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <h4 class="mt-3">No orders yet</h4>
                <p>Start shopping to see your orders here</p>
                <a href="browse.php" class="btn btn-success">Browse Products</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
// Function to view order details via AJAX
function viewOrderDetails(orderId) {
    $.ajax({
        url: 'order_details.php',
        type: 'GET',
        data: { order_id: orderId },
        success: function(response) {
            $('#orderDetailsContent').html(response);
            const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
            modal.show();
        },
        error: function() {
            showToast('Unable to load order details.', 'error');
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
