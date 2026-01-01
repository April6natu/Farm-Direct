<?php
/**
 * Seller Actions Handler
 * Handles AJAX requests for seller operations
 * Uses CRUD UPDATE for marking notifications as read
 */

require_once '../db.php';
require_once '../functions.php';

// Require seller role
if (!is_logged_in() || !has_role('seller')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'mark_notification_read':
            // UPDATE: Mark notification as read
            $notification_id = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : 0;
            
            if ($notification_id > 0) {
                // Verify notification belongs to seller
                $update_sql = "UPDATE notifications SET is_read = 1 
                              WHERE id = ? AND user_id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("ii", $notification_id, $user_id);
                
                if ($stmt->execute()) {
                    $response = ['success' => true];
                }
            }
            break;
            
        case 'mark_all_read':
            // UPDATE: Mark all notifications as read
            $update_sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'All notifications marked as read'];
            }
            break;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
