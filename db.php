<?php
/**
 * Database Connection Configuration
 * Farm-Direct Agricultural eCommerce Platform
 * 
 * This file establishes the connection to the MySQL database
 * and provides error handling for connection failures.
 */

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'agriecom');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection and handle errors
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set charset to UTF-8 for proper character encoding
$conn->set_charset("utf8mb4");

/**
 * Function to escape user input and prevent SQL injection
 * 
 * @param string $data User input data
 * @return string Sanitized data
 */
function escape_input($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

/**
 * Function to sanitize output and prevent XSS attacks
 * 
 * @param string $data Output data
 * @return string Sanitized data
 */
function clean_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>
