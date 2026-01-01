<?php
/**
 * Browse Products Page
 * Farm-Direct Agricultural eCommerce Platform
 * 
 * Displays all available agricultural products with category filtering.
 * Accessible to all users (logged in or not), but only buyers can add to cart.
 */

require_once 'db.php';
require_once 'functions.php';

$page_title = 'Browse Products';

// Get filter parameters
$selected_category = isset($_GET['category']) ? escape_input($_GET['category']) : 'All';
$search_query = isset($_GET['search']) ? escape_input($_GET['search']) : '';

// Build SQL query based on filters
$sql = "SELECT p.*, u.name as seller_name 
        FROM products p 
        JOIN users u ON p.seller_id = u.id 
        WHERE p.status = 'active'";

$params = [];
$types = '';

if ($selected_category !== 'All') {
    $sql .= " AND p.category = ?";
    $params[] = $selected_category;
    $types .= 's';
}

if (!empty($search_query)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $search_param = '%' . $search_query . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

$categories = get_product_categories();
?>

<?php include 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-dark">Fresh from the Farm</h2>
        <p class="text-muted">Discover quality agricultural products at the best prices</p>
    </div>
    <div class="col-md-4">
        <!-- Search Form -->
        <form method="GET" action="" class="d-flex">
            <input type="text" name="search" class="form-control" placeholder="Search products..." 
                   value="<?= clean_output($search_query) ?>">
            <button type="submit" class="btn btn-success ms-2">Search</button>
        </form>
    </div>
</div>

<!-- Category Filter Pills -->
<div class="mb-4 d-flex flex-wrap gap-2">
    <a href="?category=All<?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" 
       class="category-pill <?= $selected_category === 'All' ? 'active' : '' ?>">
        All
    </a>
    <?php foreach ($categories as $category): ?>
        <a href="?category=<?= urlencode($category) ?><?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" 
           class="category-pill <?= $selected_category === $category ? 'active' : '' ?>">
            <?= clean_output($category) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Products Grid -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php if ($products->num_rows > 0): ?>
        <?php while ($product = $products->fetch_assoc()): ?>
            <div class="col">
                <div class="card product-card h-100 shadow-sm">
                    <!-- Product Image -->
                    <div class="position-relative overflow-hidden" style="height: 200px;">
                        <img src="<?= clean_output($product['image']) ?>" 
                             class="card-img-top w-100 h-100 object-fit-cover" 
                             alt="<?= clean_output($product['name']) ?>"
                             onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                        <span class="position-absolute top-0 end-0 m-2 badge bg-success">
                            <?= clean_output($product['category']) ?>
                        </span>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-truncate">
                            <?= clean_output($product['name']) ?>
                        </h5>
                        <p class="card-text text-muted small flex-grow-1" style="min-height: 40px;">
                            <?= clean_output(substr($product['description'], 0, 80)) . '...' ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="h5 text-success mb-0 fw-bold">
                                <?= format_price($product['price']) ?>
                            </span>
                            <span class="text-muted small">per <?= clean_output($product['unit']) ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <span class="<?= $product['stock'] < 10 ? 'text-danger' : '' ?>">
                                    <?= $product['stock'] ?> in stock
                                </span>
                            </small>
                            <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-success">
                                View Details
                            </a>
                        </div>
                        
                        <?php if (is_logged_in() && has_role('buyer')): ?>
                            <button onclick="addToCart(<?= $product['id'] ?>)" 
                                    class="btn btn-success mt-2 w-100">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z"/>
                                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg>
                                Add to Cart
                            </button>
                        <?php elseif (!is_logged_in()): ?>
                            <a href="login.php" class="btn btn-outline-success mt-2 w-100">
                                Login to Purchase
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="empty-state py-5">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h4 class="mt-3">No products found</h4>
                <p>Try adjusting your search or filter to find what you're looking for.</p>
                <a href="browse.php" class="btn btn-success mt-2">View All Products</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
