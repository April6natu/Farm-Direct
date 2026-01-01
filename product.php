<?php
/**
 * Product Details Page
 * Farm-Direct Agricultural eCommerce Platform
 * 
 * Displays detailed information about a single product.
 * Uses CRUD READ operation to fetch product data.
 */

require_once 'db.php';
require_once 'functions.php';

// READ: Get product ID from query string
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id === 0) {
    header("Location: browse.php");
    exit();
}

// READ: Fetch product details with seller information
$sql = "SELECT p.*, u.name as seller_name, u.email as seller_email 
        FROM products p 
        JOIN users u ON p.seller_id = u.id 
        WHERE p.id = ? AND p.status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: browse.php");
    exit();
}

$product = $result->fetch_assoc();
$page_title = $product['name'];

// Check if this is an AJAX request for quick view
$is_ajax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

if ($is_ajax) {
    // Return only product content for modal
    ?>
    <div class="row">
        <div class="col-md-5">
            <img src="<?= clean_output($product['image']) ?>" 
                 class="img-fluid rounded" 
                 alt="<?= clean_output($product['name']) ?>"
                 onerror="this.src='https://via.placeholder.com/400x300'">
        </div>
        <div class="col-md-7">
            <h3 class="fw-bold"><?= clean_output($product['name']) ?></h3>
            <span class="badge bg-success mb-3"><?= clean_output($product['category']) ?></span>
            
            <h4 class="text-success fw-bold"><?= format_price($product['price']) ?> 
                <small class="text-muted fs-6">per <?= clean_output($product['unit']) ?></small>
            </h4>
            
            <p class="text-muted"><?= nl2br(clean_output($product['description'])) ?></p>
            
            <div class="mb-3">
                <strong>Availability:</strong> 
                <span class="<?= $product['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                    <?= $product['stock'] ?> <?= clean_output($product['unit']) ?> in stock
                </span>
            </div>
            
            <div class="mb-3">
                <strong>Seller:</strong> <?= clean_output($product['seller_name']) ?>
            </div>
            
            <?php if (is_logged_in() && has_role('buyer') && $product['stock'] > 0): ?>
                <button onclick="addToCart(<?= $product['id'] ?>); $('.btn-close').click();" 
                        class="btn btn-success btn-lg w-100">
                    Add to Cart
                </button>
            <?php elseif (!is_logged_in()): ?>
                <a href="login.php" class="btn btn-success btn-lg w-100">Login to Purchase</a>
            <?php endif; ?>
        </div>
    </div>
    <?php
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="browse.php">Products</a></li>
        <li class="breadcrumb-item active"><?= clean_output($product['name']) ?></li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm">
            <img src="<?= clean_output($product['image']) ?>" 
                 class="card-img-top" 
                 alt="<?= clean_output($product['name']) ?>"
                 style="height: 400px; object-fit: cover;"
                 onerror="this.src='https://via.placeholder.com/400x400?text=No+Image'">
        </div>
    </div>
    
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <span class="badge bg-success mb-2"><?= clean_output($product['category']) ?></span>
                <h2 class="fw-bold mb-3"><?= clean_output($product['name']) ?></h2>
                
                <div class="d-flex align-items-baseline mb-4">
                    <h3 class="text-success fw-bold mb-0 me-2"><?= format_price($product['price']) ?></h3>
                    <span class="text-muted">per <?= clean_output($product['unit']) ?></span>
                </div>
                
                <div class="mb-4">
                    <h5 class="fw-bold">Description</h5>
                    <p class="text-muted"><?= nl2br(clean_output($product['description'])) ?></p>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="text-muted mb-1">Availability</h6>
                                <p class="mb-0 fw-bold <?= $product['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                                    <?php if ($product['stock'] > 0): ?>
                                        <?= $product['stock'] ?> <?= clean_output($product['unit']) ?> in stock
                                    <?php else: ?>
                                        Out of Stock
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="text-muted mb-1">Seller</h6>
                                <p class="mb-0 fw-bold"><?= clean_output($product['seller_name']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (is_logged_in() && has_role('buyer')): ?>
                    <?php if ($product['stock'] > 0): ?>
                        <div class="d-grid gap-2">
                            <button onclick="addToCart(<?= $product['id'] ?>)" 
                                    class="btn btn-success btn-lg">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                                    <path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z"/>
                                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg>
                                Add to Cart
                            </button>
                            <a href="cart.php" class="btn btn-outline-success">View Cart</a>
                        </div>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg w-100" disabled>Out of Stock</button>
                    <?php endif; ?>
                <?php elseif (!is_logged_in()): ?>
                    <div class="alert alert-info">
                        Please <a href="login.php" class="alert-link">login</a> to purchase this product.
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        Only buyers can purchase products.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Related Products Section -->
<div class="mt-5">
    <h4 class="fw-bold mb-4">More from <?= clean_output($product['category']) ?></h4>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
        <?php
        // READ: Fetch related products from same category
        $related_sql = "SELECT * FROM products 
                       WHERE category = ? AND id != ? AND status = 'active' 
                       LIMIT 4";
        $stmt = $conn->prepare($related_sql);
        $stmt->bind_param("si", $product['category'], $product_id);
        $stmt->execute();
        $related_products = $stmt->get_result();
        
        while ($related = $related_products->fetch_assoc()):
        ?>
            <div class="col">
                <div class="card product-card h-100">
                    <img src="<?= clean_output($related['image']) ?>" 
                         class="card-img-top" 
                         style="height: 150px; object-fit: cover;"
                         alt="<?= clean_output($related['name']) ?>">
                    <div class="card-body">
                        <h6 class="card-title"><?= clean_output($related['name']) ?></h6>
                        <p class="text-success fw-bold mb-2"><?= format_price($related['price']) ?></p>
                        <a href="product.php?id=<?= $related['id'] ?>" class="btn btn-sm btn-outline-success w-100">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
