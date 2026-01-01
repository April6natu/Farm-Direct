<?php
/**
 * Add/Edit Product Page
 * Farm-Direct Agricultural eCommerce Platform
 * 
 * CRUD Operations:
 * - CREATE: Add new product with image upload
 * - READ: Load existing product data for editing
 * - UPDATE: Modify product details
 */

require_once '../db.php';
require_once '../functions.php';

$page_title = 'Add Product';

// Require seller role
require_role('seller', '../browse.php');

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';
$is_edit = false;
$product_data = [];

// READ: Check if editing existing product
if (isset($_GET['edit'])) {
    $product_id = (int)$_GET['edit'];
    $is_edit = true;
    $page_title = 'Edit Product';
    
    // READ product data
    $read_sql = "SELECT * FROM products WHERE id = ? AND seller_id = ?";
    $stmt = $conn->prepare($read_sql);
    $stmt->bind_param("ii", $product_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product_data = $result->fetch_assoc();
    } else {
        header("Location: products.php");
        exit();
    }
}

// Handle form submission (CREATE or UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = escape_input($_POST['name']);
    $category = escape_input($_POST['category']);
    $price = (float)$_POST['price'];
    $unit = escape_input($_POST['unit']);
    $description = escape_input($_POST['description']);
    $stock = (int)$_POST['stock'];
    $image_path = $is_edit ? $product_data['image'] : 'https://via.placeholder.com/400x300?text=Product+Image';
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            // Validate file size (max 5MB)
            if ($_FILES['image']['size'] <= 5 * 1024 * 1024) {
                $new_filename = 'product_' . generate_random_string(16) . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_path = 'uploads/' . $new_filename;
                    
                    // Delete old image if editing
                    if ($is_edit && !empty($product_data['image']) && strpos($product_data['image'], 'uploads/') === 0) {
                        $old_image = '../' . $product_data['image'];
                        if (file_exists($old_image)) {
                            unlink($old_image);
                        }
                    }
                } else {
                    $message = 'Failed to upload image.';
                    $message_type = 'danger';
                }
            } else {
                $message = 'Image size must be less than 5MB.';
                $message_type = 'danger';
            }
        } else {
            $message = 'Invalid image format. Allowed: JPG, PNG, GIF, WebP.';
            $message_type = 'danger';
        }
    }
    
    // Validate required fields
    if (empty($message) && !empty($name) && !empty($category) && $price > 0) {
        if ($is_edit) {
            // UPDATE existing product
            $update_sql = "UPDATE products 
                          SET name = ?, category = ?, price = ?, unit = ?, description = ?, stock = ?, image = ?
                          WHERE id = ? AND seller_id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssdssisii", $name, $category, $price, $unit, $description, $stock, $image_path, $product_id, $user_id);
            
            if ($stmt->execute()) {
                $message = 'Product updated successfully!';
                $message_type = 'success';
                
                // Reload product data
                $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $product_data = $stmt->get_result()->fetch_assoc();
            } else {
                $message = 'Failed to update product.';
                $message_type = 'danger';
            }
        } else {
            // CREATE new product
            $create_sql = "INSERT INTO products (seller_id, name, category, price, unit, description, stock, image, status) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')";
            $stmt = $conn->prepare($create_sql);
            $stmt->bind_param("issdsis", $user_id, $name, $category, $price, $unit, $description, $stock, $image_path);
            
            if ($stmt->execute()) {
                $message = 'Product added successfully!';
                $message_type = 'success';
                
                // Redirect to products page after short delay
                header("refresh:2;url=products.php");
            } else {
                $message = 'Failed to add product.';
                $message_type = 'danger';
            }
        }
    } elseif (empty($message)) {
        $message = 'Please fill in all required fields correctly.';
        $message_type = 'warning';
    }
}

$categories = get_product_categories();
?>

<?php include '../includes/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold"><?= $is_edit ? 'Edit' : 'Add New' ?> Product</h2>
        <p class="text-muted">Fill in the details below to <?= $is_edit ? 'update' : 'add' ?> your product</p>
    </div>
    <div class="col-auto">
        <a href="products.php" class="btn btn-outline-secondary">
            ← Back to Products
        </a>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show">
        <?= clean_output($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   value="<?= $is_edit ? clean_output($product_data['name']) : '' ?>"
                                   placeholder="e.g., Fresh Cassava Tubers">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat ?>" 
                                            <?= ($is_edit && $product_data['category'] === $cat) ? 'selected' : '' ?>>
                                        <?= $cat ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label fw-bold">Price ($) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0.01" required
                                   value="<?= $is_edit ? $product_data['price'] : '' ?>"
                                   placeholder="0.00">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="unit" class="form-label fw-bold">Unit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unit" name="unit" required
                                   value="<?= $is_edit ? clean_output($product_data['unit']) : '' ?>"
                                   placeholder="e.g., kg, Bunch, Bag">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="stock" class="form-label fw-bold">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stock" name="stock" min="0" required
                                   value="<?= $is_edit ? $product_data['stock'] : 10 ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                                  placeholder="Describe your product, its quality, origin, and benefits..."><?= $is_edit ? clean_output($product_data['description']) : '' ?></textarea>
                        <small class="text-muted">Provide detailed information to help buyers make informed decisions.</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="image" class="form-label fw-bold">Product Image</label>
                        <?php if ($is_edit && !empty($product_data['image'])): ?>
                            <div class="mb-2">
                                <img src="../<?= clean_output($product_data['image']) ?>" 
                                     alt="Current image" 
                                     class="img-thumbnail" 
                                     style="max-width: 200px;">
                                <p class="small text-muted mt-1">Current image. Upload a new one to replace it.</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Supported formats: JPG, PNG, GIF, WebP. Max size: 5MB.</small>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <?= $is_edit ? 'Update Product' : 'Add Product' ?>
                        </button>
                        <a href="products.php" class="btn btn-outline-secondary btn-lg px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow-sm bg-light">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Tips for Better Listings</h5>
                <ul class="small text-muted">
                    <li class="mb-2">Use clear, descriptive product names</li>
                    <li class="mb-2">Upload high-quality images showing the product clearly</li>
                    <li class="mb-2">Provide detailed descriptions highlighting quality and freshness</li>
                    <li class="mb-2">Set competitive prices based on market rates</li>
                    <li class="mb-2">Keep stock levels updated to avoid overselling</li>
                    <li class="mb-2">Choose the correct category for better discoverability</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
