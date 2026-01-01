<?php
/**
 * User Login Page
 * Farm-Direct Agricultural eCommerce Platform
 * 
 * Handles authentication for all user roles (Admin, Seller, Buyer)
 * and redirects to appropriate dashboard based on role.
 */

require_once 'db.php';
require_once 'functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    $role = $_SESSION['role'];
    header("Location: " . ($role === 'admin' ? 'admin/' : ($role === 'seller' ? 'seller/' : '')) . "dashboard.php");
    exit();
}

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = escape_input($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Query user from database
        $sql = "SELECT id, name, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on role
                switch ($user['role']) {
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
                        header("Location: index.php");
                }
                exit();
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Farm-Direct</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <!-- Logo and Title -->
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <svg class="text-success" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </div>
                            <h2 class="fw-bold text-dark">Farm-Direct</h2>
                            <p class="text-muted">Login to Your Account</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= clean_output($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" required
                                       value="<?= isset($_POST['email']) ? clean_output($_POST['email']) : '' ?>"
                                       placeholder="your@email.com">
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" required
                                       placeholder="Enter your password">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">Login</button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                Don't have an account? 
                                <a href="register.php" class="text-success fw-bold text-decoration-none">Register</a>
                            </p>
                        </div>

                        <!-- Demo Credentials Info -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <p class="small text-muted mb-2 fw-bold">Demo Accounts:</p>
                            <p class="small text-muted mb-1"><strong>Admin:</strong> admin@farm-direct.com / admin123</p>
                            <p class="small text-muted mb-1"><strong>Seller:</strong> john@farm-direct.com / seller123</p>
                            <p class="small text-muted mb-0"><strong>Buyer:</strong> buyer@test.com / buyer123</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
