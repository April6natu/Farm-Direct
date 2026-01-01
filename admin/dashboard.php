<?php
/**
 * Admin Dashboard
 * Farm-Direct Agricultural eCommerce Platform
 * 
 * Full CRUD operations for:
 * - Users (CREATE, READ, UPDATE, DELETE)
 * - Products (READ, UPDATE, DELETE)
 * - Orders (READ, UPDATE)
 * - System statistics
 */

require_once '../db.php';
require_once '../functions.php';

$page_title = 'Admin Dashboard';

// Require admin role
require_role('admin', '../browse.php');

$message = '';
$message_type = '';

// ===== USER CRUD OPERATIONS =====

// CREATE: Add new user
if (isset($_POST['add_user'])) {
    $name = escape_input($_POST['name']);
    $email = escape_input($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = escape_input($_POST['role']);
    
    // Check if email exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        // CREATE user
        $insert_sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        
        if ($stmt->execute()) {
            $message = 'User added successfully.';
            $message_type = 'success';
        } else {
            $message = 'Failed to add user.';
            $message_type = 'danger';
        }
    } else {
        $message = 'Email already exists.';
        $message_type = 'warning';
    }
}

// UPDATE: Update user role
if (isset($_POST['update_user_role'])) {
    $user_id = (int)$_POST['user_id'];
    $new_role = escape_input($_POST['role']);
    
    // UPDATE role
    $update_sql = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_role, $user_id);
    
    if ($stmt->execute()) {
        $message = 'User role updated successfully.';
        $message_type = 'success';
    }
}

// DELETE: Remove user
if (isset($_GET['delete_user'])) {
    $user_id = (int)$_GET['delete_user'];
    
    // Prevent deleting own account
    if ($user_id !== $_SESSION['user_id']) {
        // DELETE user (cascading will handle related records)
        $delete_sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $message = 'User deleted successfully.';
            $message_type = 'success';
        } else {
            $message = 'Failed to delete user.';
            $message_type = 'danger';
        }
    } else {
        $message = 'Cannot delete your own account.';
        $message_type = 'warning';
    }
}

// ===== PRODUCT CRUD OPERATIONS =====

// UPDATE: Update product status
if (isset($_POST['update_product_status'])) {
    $product_id = (int)$_POST['product_id'];
    $new_status = escape_input($_POST['status']);
    
    // UPDATE status
    $update_sql = "UPDATE products SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $product_id);
    
    if ($stmt->execute()) {
        $message = 'Product status updated.';
        $message_type = 'success';
    }
}

// DELETE: Remove product
if (isset($_GET['delete_product'])) {
    $product_id = (int)$_GET['delete_product'];
    
    // DELETE product
    $delete_sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        $message = 'Product deleted successfully.';
        $message_type = 'success';
    } else {
        $message = 'Failed to delete product.';
        $message_type = 'danger';
    }
}

// ===== ORDER CRUD OPERATIONS =====

// UPDATE: Update order status
if (isset($_POST['update_order_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = escape_input($_POST['status']);
    
    // UPDATE order status
    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        $message = 'Order status updated.';
        $message_type = 'success';
    }
}

// ===== READ OPERATIONS FOR DASHBOARD =====

// READ: System statistics
$stats_sql = "SELECT 
              (SELECT COUNT(*) FROM users WHERE role = 'buyer') as total_buyers,
              (SELECT COUNT(*) FROM users WHERE role = 'seller') as total_sellers,
              (SELECT COUNT(*) FROM products WHERE status = 'active') as total_products,
              (SELECT COUNT(*) FROM orders) as total_orders,
              (SELECT COALESCE(SUM(total), 0) FROM orders) as total_revenue";
$stats = $conn->query($stats_sql)->fetch_assoc();

// READ: Recent users
$users_sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT 10";
$users = $conn->query($users_sql);

// READ: Recent products
$products_sql = "SELECT p.*, u.name as seller_name 
                FROM products p 
                JOIN users u ON p.seller_id = u.id 
                ORDER BY p.created_at DESC 
                LIMIT 10";
$products = $conn->query($products_sql);

// READ: Recent orders
$orders_sql = "SELECT o.*, u.name as buyer_name 
               FROM orders o 
               JOIN users u ON o.buyer_id = u.id 
               ORDER BY o.created_at DESC 
               LIMIT 10";
$orders = $conn->query($orders_sql);

?>

<?php include '../includes/header.php'; ?>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show">
        <?= clean_output($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold">Admin Dashboard</h2>
        <p class="text-muted">Manage all aspects of Farm-Direct platform</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-2">
        <div class="stats-card">
            <div class="stat-label">Buyers</div>
            <div class="stat-value text-primary"><?= $stats['total_buyers'] ?></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stats-card">
            <div class="stat-label">Sellers</div>
            <div class="stat-value text-info"><?= $stats['total_sellers'] ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-label">Active Products</div>
            <div class="stat-value text-warning"><?= $stats['total_products'] ?></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stats-card">
            <div class="stat-label">Orders</div>
            <div class="stat-value"><?= $stats['total_orders'] ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-label">Revenue</div>
            <div class="stat-value text-success"><?= format_price($stats['total_revenue']) ?></div>
        </div>
    </div>
</div>

<!-- Tabs for different management sections -->
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#users">Users Management</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#products">Products Management</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#orders">Orders Management</a>
    </li>
</ul>

<div class="tab-content">
    <!-- USERS TAB -->
    <div class="tab-pane fade show active" id="users">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">User Directory</h5>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    + Add User
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td class="fw-bold"><?= clean_output($user['name']) ?></td>
                                    <td><?= clean_output($user['email']) ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="buyer" <?= $user['role'] === 'buyer' ? 'selected' : '' ?>>Buyer</option>
                                                <option value="seller" <?= $user['role'] === 'seller' ? 'selected' : '' ?>>Seller</option>
                                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                            <button type="submit" name="update_user_role" class="d-none"></button>
                                        </form>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                            <a href="?delete_user=<?= $user['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger confirm-delete">
                                                Delete
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- PRODUCTS TAB -->
    <div class="tab-pane fade" id="products">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Products Overview</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Seller</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = $products->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $product['id'] ?></td>
                                    <td class="fw-bold"><?= clean_output($product['name']) ?></td>
                                    <td><?= clean_output($product['seller_name']) ?></td>
                                    <td><?= clean_output($product['category']) ?></td>
                                    <td class="text-success"><?= format_price($product['price']) ?></td>
                                    <td><?= $product['stock'] ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                            </select>
                                            <button type="submit" name="update_product_status" class="d-none"></button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="?delete_product=<?= $product['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger confirm-delete">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ORDERS TAB -->
    <div class="tab-pane fade" id="orders">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Orders Management</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Buyer</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $orders->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-bold">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= clean_output($order['buyer_name']) ?></td>
                                    <td class="text-success fw-bold"><?= format_price($order['total']) ?></td>
                                    <td><?= ucwords(str_replace('_', ' ', $order['payment_method'])) ?></td>
                                    <td><?= clean_output($order['delivery_location']) ?></td>
                                    <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="paid" <?= $order['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                                                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" name="update_order_status" class="d-none"></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="buyer">Buyer</option>
                            <option value="seller">Seller</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
