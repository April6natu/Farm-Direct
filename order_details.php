<?php
/**
 * Order Details AJAX Endpoint
 * Returns order items for display in modal
 * Uses CRUD READ operation
 */

require_once 'db.php';
require_once 'functions.php';

require_login();

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$user_id = $_SESSION['user_id'];

// READ: Verify order belongs to user
$verify_sql = "SELECT * FROM orders WHERE id = ? AND buyer_id = ?";
$stmt = $conn->prepare($verify_sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo '<p class="text-danger">Order not found.</p>';
    exit();
}

// READ: Get order items
$items_sql = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($items_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>

<div class="mb-3">
    <strong>Order ID:</strong> #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?><br>
    <strong>Date:</strong> <?= date('F d, Y \a\t h:i A', strtotime($order['created_at'])) ?><br>
    <strong>Status:</strong> <span class="badge <?= get_status_badge_class($order['status']) ?>"><?= ucfirst($order['status']) ?></span><br>
    <strong>Payment:</strong> <?= ucwords(str_replace('_', ' ', $order['payment_method'])) ?><br>
    <strong>Delivery:</strong> <?= clean_output($order['delivery_location']) ?>
</div>

<h6 class="fw-bold mb-3">Order Items</h6>
<table class="table table-sm">
    <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($item = $items->fetch_assoc()): ?>
            <tr>
                <td><?= clean_output($item['product_name']) ?></td>
                <td><?= format_price($item['price']) ?></td>
                <td><?= $item['quantity'] ?> <?= clean_output($item['unit']) ?></td>
                <td class="fw-bold"><?= format_price($item['price'] * $item['quantity']) ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr class="table-active">
            <td colspan="3" class="text-end fw-bold">Total:</td>
            <td class="fw-bold text-success"><?= format_price($order['total']) ?></td>
        </tr>
    </tfoot>
</table>
