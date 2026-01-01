<?php
/**
 * User Logout Script
 * Farm-Direct Agricultural eCommerce Platform
 * 
 * Destroys user session and redirects to login page.
 */

session_start();

// Unset all session variables
$_SESSION = array();

// Destroy session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
