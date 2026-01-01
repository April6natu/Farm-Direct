<?php
/**
 * AJAX Cart Actions Handler
 * Farm-Direct Agricultural eCommerce Platform
 * 
 * This file handles all AJAX requests for cart operations:
 * - Add items to cart
 * - Remove items from cart
 * - Update item quantities
 */

require_once 'db.php';
require_once 'functions.php';

// Require user to be logged in as buyer
if (!is_logged_in() || !has_role('buyer')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$response = ['success' => false, 'message' => 'Invalid action'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    switch ($action) {
        case 'add':
            // Add product to cart or increase quantity
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            if ($product_id > 0 && $quantity > 0) {
                // Check if product exists and is active
                $product_sql = "SELECT id, name, stock FROM products WHERE id = ? AND status = 'active'";
                $stmt = $conn->prepare($product_sql);
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $product_result = $stmt->get_result();
                
                if ($product_result->num_rows > 0) {
                    $product = $product_result->fetch_assoc();
                    
                    // Check if already in cart
                    $check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
                    $stmt = $conn->prepare($check_sql);
                    $stmt->bind_param("ii", $user_id, $product_id);
                    $stmt->execute();
                    $cart_result = $stmt->get_result();
                    
                    if ($cart_result->num_rows > 0) {
                        // Update quantity
                        $cart_item = $cart_result->fetch_assoc();
                        $new_quantity = $cart_item['quantity'] + $quantity;
                        
                        // Check stock availability
                        if ($new_quantity <= $product['stock']) {
                            $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
                            $stmt = $conn->prepare($update_sql);
                            $stmt->bind_param("ii", $new_quantity, $cart_item['id']);
                            
                            if ($stmt->execute()) {
                                $response = ['success' => true, 'message' => 'Cart updated successfully'];
                            } else {
                                $response = ['success' => false, 'message' => 'Failed to update cart'];
                            }
                        } else {
                            $response = ['success' => false, 'message' => 'Not enough stock available'];
                        }
                    } else {
                        // Check stock availability
                        if ($quantity <= $product['stock']) {
                            // Insert new cart item
                            $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                            $stmt = $conn->prepare($insert_sql);
                            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
                            
                            if ($stmt->execute()) {
                                $response = ['success' => true, 'message' => 'Added to cart successfully'];
                            } else {
                                $response = ['success' => false, 'message' => 'Failed to add to cart'];
                            }
                        } else {
                            $response = ['success' => false, 'message' => 'Not enough stock available'];
                        }
                    }
                } else {
                    $response = ['success' => false, 'message' => 'Product not found'];
                }
            }
            break;
            
        case 'remove':
            // Remove product from cart
            if ($product_id > 0) {
                $delete_sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
                $stmt = $conn->prepare($delete_sql);
                $stmt->bind_param("ii", $user_id, $product_id);
                
                if ($stmt->execute()) {
                    $response = ['success' => true, 'message' => 'Item removed from cart'];
                } else {
                    $response = ['success' => false, 'message' => 'Failed to remove item'];
                }
            }
            break;
            
        case 'update':
            // Update cart item quantity
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
            
            if ($product_id > 0 && $quantity > 0) {
                // Check stock availability
                $product_sql = "SELECT stock FROM products WHERE id = ?";
                $stmt = $conn->prepare($product_sql);
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $product_result = $stmt->get_result();
                
                if ($product_result->num_rows > 0) {
                    $product = $product_result->fetch_assoc();
                    
                    if ($quantity <= $product['stock']) {
                        $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
                        $stmt = $conn->prepare($update_sql);
                        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
                        
                        if ($stmt->execute()) {
                            $response = ['success' => true, 'message' => 'Quantity updated'];
                        } else {
                            $response = ['success' => false, 'message' => 'Failed to update quantity'];
                        }
                    } else {
                        $response = ['success' => false, 'message' => 'Not enough stock available'];
                    }
                }
            }
            break;
    }
    
    // Get updated cart count
    $count_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($count_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $count_result = $stmt->get_result();
    $count_row = $count_result->fetch_assoc();
    $response['cart_count'] = (int)($count_row['total'] ?? 0);
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
