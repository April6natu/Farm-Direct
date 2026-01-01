<?php
/**
 * Helper Functions for Farm-Direct Platform
 * 
 * This file contains utility functions used throughout the application
 * for authentication, authorization, and common operations.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user has a specific role
 * 
 * @param string $role Role to check (admin, seller, buyer)
 * @return bool True if user has the role, false otherwise
 */
function has_role($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Redirect if not logged in
 * 
 * @param string $redirect_to URL to redirect to (default: login.php)
 */
function require_login($redirect_to = 'login.php') {
    if (!is_logged_in()) {
        header("Location: $redirect_to");
        exit();
    }
}

/**
 * Redirect if user doesn't have required role
 * 
 * @param string $required_role Required role
 * @param string $redirect_to URL to redirect to (default: index.php)
 */
function require_role($required_role, $redirect_to = 'index.php') {
    require_login();
    if (!has_role($required_role)) {
        header("Location: $redirect_to");
        exit();
    }
}

/**
 * Get current logged-in user data
 * 
 * @return array|null User data or null if not logged in
 */
function get_current_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['role']
    ];
}

/**
 * Format price with currency symbol
 * 
 * @param float $price Price value
 * @return string Formatted price
 */
function format_price($price) {
    return '$' . number_format($price, 2);
}

/**
 * Get time ago format for timestamps
 * 
 * @param string $datetime MySQL datetime string
 * @return string Human-readable time ago
 */
function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M d, Y', $timestamp);
    }
}

/**
 * Generate a random string for file uploads
 * 
 * @param int $length Length of random string
 * @return string Random string
 */
function generate_random_string($length = 10) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Validate email format
 * 
 * @param string $email Email address
 * @return bool True if valid, false otherwise
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Get supported delivery areas
 * 
 * @return array List of delivery areas
 */
function get_delivery_areas() {
    return [
        'Central Business District',
        'North Industrial Park',
        'Riverside Estates',
        'Lakeside Heights',
        'Market Square Area',
        'Green Valley Farm Zone'
    ];
}

/**
 * Get product categories
 * 
 * @return array List of product categories
 */
function get_product_categories() {
    return [
        'Tubers',
        'Grains',
        'Legumes',
        'Vegetables',
        'Fruits/Staples',
        'Other'
    ];
}

/**
 * Get badge color class based on order status
 * 
 * @param string $status Order status
 * @return string CSS class for badge
 */
function get_status_badge_class($status) {
    $classes = [
        'pending' => 'badge-warning',
        'paid' => 'badge-info',
        'delivered' => 'badge-success',
        'cancelled' => 'badge-danger'
    ];
    return $classes[$status] ?? 'badge-secondary';
}

/**
 * Create notification for user
 * 
 * @param int $user_id User ID
 * @param string $message Notification message
 * @return bool Success status
 */
function create_notification($user_id, $message) {
    global $conn;
    $user_id = (int)$user_id;
    $message = escape_input($message);
    
    $sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $message);
    
    return $stmt->execute();
}

/**
 * Get unread notification count for user
 * 
 * @param int $user_id User ID
 * @return int Unread notification count
 */
function get_unread_notification_count($user_id) {
    global $conn;
    $user_id = (int)$user_id;
    
    $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return (int)$row['count'];
}
?>
