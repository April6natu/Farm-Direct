<?php
/**
 * Seller Products Management Page
 * Farm-Direct Agricultural eCommerce Platform
 * 
 * Full CRUD operations for seller's products:
 * - CREATE: Add new product (redirects to add_product.php)
 * - READ: View all seller's products
 * - UPDATE: Edit product details
 * - DELETE: Remove product
 */

require_once '../db.php';
require_once '../functions.php';

$page_title = 'My Products';

// Require seller role
require_role('seller', '../browse.php');

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// DELETE: Handle product deletion
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    
    // Verify product belongs to seller
    $verify_sql = "SELECT id FROM products WHERE id = ? AND seller_id = ?";
    $stmt = $conn->prepare($verify_sql);
    $stmt->bind_param("ii", $product_id, $user_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
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
}

// UPDATE: Handle inline status toggle
if (isset($_POST['toggle_status'])) {
    $product_id = (int)$_POST['product_id'];
    $new_status = $_POST['status'] === 'active' ? 'inactive' : 'active';
    
    // Verify and UPDATE
    $update_sql = "UPDATE products SET status = ? WHERE id = ? AND seller_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sii", $new_status, $product_id, $user_id);
    
    if ($stmt->execute()) {
        $message = "Product " . ($new_status === 'active' ? 'activated' : 'deactivated') . " successfully.";
        $message_type = 'success';
    }
}

// UPDATE: Handle quick stock update
if (isset($_POST['update_stock'])) {
    $product_id = (int)$_POST['product_id'];
    $new_stock = (int)$_POST['stock'];
    
    if ($new_stock >= 0) {
        // Verify and UPDATE
        $update_sql = "UPDATE products SET stock = ? WHERE id = ? AND seller_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("iii", $new_stock, $product_id, $user_id);
        
        if ($stmt->execute()) {
            $message = 'Stock updated successfully.';
            $message_type = 'success';
        }
    }
}

// READ: Get all seller's products
$products_sql = "SELECT * FROM products 
                 WHERE seller_id = ? 
                 ORDER BY created_at DESC";
$stmt = $conn->prepare($products_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$products = $stmt->get_result();
?>

<?php include '../includes/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold">My Products</h2>
        <p class="text-muted">Manage your product inventory</p>
    </div>
    <div class="col-auto">
        <a href="dashboard.php" class="btn btn-outline-secondary me-2">
            ← Back to Dashboard
        </a>
        <a href="add_product.php" class="btn btn-success">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Add New Product
        </a>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show">
        <?= clean_output($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if ($products->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = $products->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= clean_output($product['image']) ?>" 
                                             alt="<?= clean_output($product['name']) ?>"
                                             class="rounded me-2"
                                             style="width: 50px; height: 50px; object-fit: cover;"
                                             onerror="this.src='https://via.placeholder.com/50'">
                                        <div>
                                            <div class="fw-bold"><?= clean_output($product['name']) ?></div>
                                            <small class="text-muted"><?= clean_output($product['unit']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= clean_output($product['category']) ?></td>
                                <td class="fw-bold text-success"><?= format_price($product['price']) ?></td>
                                <td>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Update stock?');">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <input type="number" name="stock" value="<?= $product['stock'] ?>" 
                                               class="form-control form-control-sm d-inline-block" 
                                               style="width: 80px;" min="0">
                                        <button type="submit" name="update_stock" class="btn btn-sm btn-outline-primary">
                                            Update
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <input type="hidden" name="status" value="<?= $product['status'] ?>">
                                        <button type="submit" name="toggle_status" 
                                                class="badge <?= $product['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?> border-0">
                                            <?= ucfirst($product['status']) ?>
                                        </button>
                                    </form>
                                </td>
                                <td><?= date('M d, Y', strtotime($product['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="add_product.php?edit=<?= $product['id'] ?>" 
                                           class="btn btn-outline-primary">
                                            Edit
                                        </a>
                                        <a href="?delete=1&id=<?= $product['id'] ?>" 
                                           class="btn btn-outline-danger confirm-delete">
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h4 class="mt-3">No products yet</h4>
                <p>Add your first product to start selling</p>
                <a href="add_product.php" class="btn btn-success">Add Product</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
