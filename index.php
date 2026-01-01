<?php
/**
 * Farm-Direct Landing Page
 * Redirects users based on their login status and role
 */

require_once 'functions.php';

// If user is logged in, redirect to appropriate dashboard
if (is_logged_in()) {
    $role = $_SESSION['role'];
    
    switch ($role) {
        case 'admin':
            header("Location: admin/dashboard.php");
            break;
        case 'seller':
            header("Location: seller/dashboard.php");
            break;
        case 'buyer':
            header("Location: browse.php");
            break;
        default:
            header("Location: browse.php");
    }
    exit();
}

// If not logged in, show welcome page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Farm-Direct</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Hero Section -->
    <section class="min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-white mb-5 mb-lg-0">
                    <div class="mb-4">
                        <svg width="64" height="64" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </div>
                    <h1 class="display-3 fw-bold mb-4">Farm-Direct</h1>
                    <p class="lead mb-4">
                        Connecting farmers directly to your table. Fresh agricultural products delivered right to your doorstep.
                    </p>
                    <div class="d-flex gap-3 mb-4">
                        <a href="register.php" class="btn btn-light btn-lg px-5">Get Started</a>
                        <a href="login.php" class="btn btn-outline-light btn-lg px-5">Login</a>
                    </div>
                    <div class="mt-5">
                        <p class="mb-2 fw-bold">Why Choose Farm-Direct?</p>
                        <ul class="list-unstyled">
                            <li class="mb-2">✓ Fresh produce directly from farmers</li>
                            <li class="mb-2">✓ Competitive prices with no middlemen</li>
                            <li class="mb-2">✓ Fast delivery within 24 hours</li>
                            <li class="mb-2">✓ Support local agriculture</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-5">
                            <h3 class="fw-bold mb-4 text-center">Quick Access</h3>
                            
                            <a href="browse.php" class="btn btn-outline-success w-100 mb-3 py-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                    </svg>
                                    <span class="fw-bold">Browse Products</span>
                                </div>
                            </a>
                            
                            <div class="text-center my-4">
                                <span class="text-muted">or</span>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-6">
                                    <a href="register.php?role=buyer" class="btn btn-success w-100 py-3">
                                        <div class="fw-bold">Buy Products</div>
                                        <small class="d-block">As a Buyer</small>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="register.php?role=seller" class="btn btn-success w-100 py-3">
                                        <div class="fw-bold">Sell Products</div>
                                        <small class="d-block">As a Seller</small>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <p class="small text-muted mb-0">
                                    Already have an account? 
                                    <a href="login.php" class="text-success fw-bold">Login here</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">How It Works</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 text-center p-4">
                        <div class="mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3">
                                <svg width="32" height="32" fill="#16a34a" viewBox="0 0 16 16">
                                    <path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1V2z"/>
                                </svg>
                            </div>
                        </div>
                        <h5 class="fw-bold">For Buyers</h5>
                        <p class="text-muted">Browse fresh products, add to cart, and checkout with multiple payment options.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 text-center p-4">
                        <div class="mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3">
                                <svg width="32" height="32" fill="#16a34a" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                </svg>
                            </div>
                        </div>
                        <h5 class="fw-bold">For Sellers</h5>
                        <p class="text-muted">List your agricultural products, manage inventory, and receive instant notifications.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 text-center p-4">
                        <div class="mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3">
                                <svg width="32" height="32" fill="#16a34a" viewBox="0 0 16 16">
                                    <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                                </svg>
                            </div>
                        </div>
                        <h5 class="fw-bold">Fast Delivery</h5>
                        <p class="text-muted">Get your orders delivered to your location within 24 hours of purchase.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Farm-Direct. All rights reserved.</p>
            <p class="small text-muted">Connecting farmers to your table.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
