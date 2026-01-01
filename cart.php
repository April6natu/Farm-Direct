<?php
/**
 * Shopping Cart Page
 * Farm-Direct Agricultural eCommerce Platform
 * 
 * Displays buyer's cart items with checkout functionality.
 * Uses CRUD operations for cart management.
 */

require_once 'db.php';
require_once 'functions.php';

$page_title = 'Shopping Cart';

// Require buyer role
require_role('buyer');

$user_id = $_SESSION['user_id'];
$message = '';

// Handle checkout (CREATE order)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $delivery_location = escape_input($_POST['delivery_location']);
    $payment_method = escape_input($_POST['payment_method']);
    
    // READ: Get cart items
    $cart_sql = "SELECT c.*, p.name, p.price, p.unit, p.stock, p.seller_id 
                 FROM cart c 
                 JOIN products p ON c.product_id = p.id 
                 WHERE c.user_id = ?";
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_items = $stmt->get_result();
    
    if ($cart_items->num_rows > 0) {
        // Calculate total
        $total = 0;
        $items = [];
        $stock_error = false;
        
        while ($item = $cart_items->fetch_assoc()) {
            if ($item['quantity'] > $item['stock']) {
                $stock_error = true;
                $message = 'Some items in your cart are out of stock.';
                break;
            }
            $total += $item['price'] * $item['quantity'];
            $items[] = $item;
        }
        
        if (!$stock_error && count($items) > 0) {
            // BEGIN TRANSACTION
            $conn->begin_transaction();
            
            try {
                // CREATE: Insert order
                $order_sql = "INSERT INTO orders (buyer_id, total, delivery_location, payment_method, status) 
                             VALUES (?, ?, ?, ?, 'pending')";
                $stmt = $conn->prepare($order_sql);
                $stmt->bind_param("idss", $user_id, $total, $delivery_location, $payment_method);
                $stmt->execute();
                $order_id = $conn->insert_id;
                
                // CREATE: Insert order items and UPDATE product stock
                foreach ($items as $item) {
                    // CREATE order item
                    $item_sql = "INSERT INTO order_items (order_id, product_id, seller_id, product_name, price, quantity, unit) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($item_sql);
                    $stmt->bind_param("iiisdis", $order_id, $item['product_id'], $item['seller_id'], 
                                     $item['name'], $item['price'], $item['quantity'], $item['unit']);
                    $stmt->execute();
                    
                    // UPDATE: Reduce product stock
                    $new_stock = $item['stock'] - $item['quantity'];
                    $stock_sql = "UPDATE products SET stock = ? WHERE id = ?";
                    $stmt = $conn->prepare($stock_sql);
                    $stmt->bind_param("ii", $new_stock, $item['product_id']);
                    $stmt->execute();
                    
                    // CREATE: Notification for seller
                    $notification_msg = "New sale! {$item['quantity']} {$item['unit']} of {$item['name']} purchased.";
                    create_notification($item['seller_id'], $notification_msg);
                }
                
                // DELETE: Clear cart
                $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
                $stmt = $conn->prepare($clear_cart_sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                
                // COMMIT TRANSACTION
                $conn->commit();
                
                // Redirect to success page
                header("Location: dashboard.php?order_success=1");
                exit();
                
            } catch (Exception $e) {
                // ROLLBACK on error
                $conn->rollback();
                $message = 'Checkout failed. Please try again.';
            }
        }
    } else {
        $message = 'Your cart is empty.';
    }
}

// READ: Get cart items with product details
$cart_sql = "SELECT c.*, p.name, p.price, p.unit, p.stock, p.image, p.category 
             FROM cart c 
             JOIN products p ON c.product_id = p.id 
             WHERE c.user_id = ? AND p.status = 'active'
             ORDER BY c.created_at DESC";
$stmt = $conn->prepare($cart_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();

// Calculate cart total
$cart_total = 0;
$cart_data = [];
while ($item = $cart_items->fetch_assoc()) {
    $cart_total += $item['price'] * $item['quantity'];
    $cart_data[] = $item;
}

$delivery_areas = get_delivery_areas();
?>

<?php include 'includes/header.php'; ?>

<?php if ($message): ?>
    <div class="alert alert-warning alert-dismissible fade show">
        <?= clean_output($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    Your Cart (<?= count($cart_data) ?> items)
                </h4>
            </div>
            <div class="card-body">
                <?php if (count($cart_data) > 0): ?>
                    <?php foreach ($cart_data as $item): ?>
                        <div class="cart-item row align-items-center py-3 border-bottom" 
                             id="cart-item-<?= $item['product_id'] ?>"
                             data-price="<?= $item['price'] ?>">
                            <div class="col-md-2">
                                <img src="<?= clean_output($item['image']) ?>" 
                                     class="img-fluid rounded" 
                                     alt="<?= clean_output($item['name']) ?>"
                                     onerror="this.src='https://via.placeholder.com/100'">
                            </div>
                            <div class="col-md-4">
                                <h6 class="mb-1 fw-bold"><?= clean_output($item['name']) ?></h6>
                                <small class="text-muted"><?= clean_output($item['category']) ?></small>
                            </div>
                            <div class="col-md-2">
                                <span class="text-success fw-bold"><?= format_price($item['price']) ?></span>
                                <small class="d-block text-muted">per <?= clean_output($item['unit']) ?></small>
                            </div>
                            <div class="col-md-2">
                                <input type="number" 
                                       class="form-control quantity-input" 
                                       value="<?= $item['quantity'] ?>"
                                       min="1" 
                                       max="<?= $item['stock'] ?>"
                                       data-product-id="<?= $item['product_id'] ?>">
                                <small class="text-muted"><?= $item['stock'] ?> available</small>
                            </div>
                            <div class="col-md-2 text-end">
                                <div class="fw-bold text-success" id="item-total-<?= $item['product_id'] ?>">
                                    <?= format_price($item['price'] * $item['quantity']) ?>
                                </div>
                                <button class="btn btn-sm btn-outline-danger mt-2" 
                                        onclick="removeFromCart(<?= $item['product_id'] ?>)">
                                    Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <h4 class="mt-3">Your cart is empty</h4>
                        <p>Start shopping to add items to your cart</p>
                        <a href="browse.php" class="btn btn-success">Browse Products</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <?php if (count($cart_data) > 0): ?>
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal:</span>
                        <span class="fw-bold" id="cart-total"><?= format_price($cart_total) ?></span>
                    </div>
                    <hr>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Delivery Location</label>
                            <select name="delivery_location" class="form-select" required>
                                <option value="">Select location</option>
                                <?php foreach ($delivery_areas as $area): ?>
                                    <option value="<?= clean_output($area) ?>"><?= clean_output($area) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Delivery within 24 hours</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Payment Method</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                       value="mobile_money" id="mobile" checked>
                                <label class="form-check-label" for="mobile">
                                    Mobile Money
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                       value="credit_card" id="card">
                                <label class="form-check-label" for="card">
                                    Credit Card
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" name="checkout" class="btn btn-success w-100 btn-lg">
                            Complete Purchase
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
