<?php
/**
 * DATABASE CONNECTION FILE
 * This file establishes connection to MySQL database
 * Included by all PHP files that need database access
 * 
 * RAILWAY DEPLOYMENT: Uses environment variables when available
 * Falls back to localhost configuration for local development (XAMPP)
 */

// Database configuration with Railway environment variables support
// Railway provides these environment variables automatically when MySQL is added
$host = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? 'localhost';
$port = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? '3306';
$dbname = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? 'trackersystemdb';
$user = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? 'root';
$pass = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? $_ENV['MYSQLROOT_PASSWORD'] ?? getenv('MYSQLROOT_PASSWORD') ?? '';

// Check if mysqli extension is loaded
if (!extension_loaded('mysqli')) {
    die("Error: mysqli extension is not loaded. Please enable it in your PHP configuration.");
}

// Enable detailed error reporting for debugging (only if mysqli_report exists)
if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

// Try to establish database connection
try {
    // For Railway: Use TCP connection with explicit port
    // For XAMPP: Use standard connection
    
    // Initialize connection variable
    $conn = false;
    
    // Attempt connection with error suppression to handle it gracefully
    $conn = @mysqli_connect($host, $user, $pass, $dbname, (int)$port);
    
    // Check if connection was successful
    if (!$conn) {
        $error_msg = mysqli_connect_error();
        $error_no = mysqli_connect_errno();
        
        // Provide helpful error message
        die("Database connection failed!<br>" . 
            "Error: " . htmlspecialchars($error_msg) . "<br>" . 
            "Error Code: " . $error_no . "<br>" .
            "Host: " . htmlspecialchars($host) . "<br>" .
            "Port: " . htmlspecialchars($port) . "<br>" .
            "Database: " . htmlspecialchars($dbname) . "<br>" .
            "<br>Please ensure MySQL service is running in Railway and environment variables are set correctly.");
    }
    
    // Set character encoding to UTF-8 for proper handling of special characters
    if (!mysqli_set_charset($conn, "utf8")) {
        die("Error setting charset: " . mysqli_error($conn));
    }
    
} catch (Exception $e) {
    // Catch any connection errors and display helpful message
    die("Database connection error: " . htmlspecialchars($e->getMessage()) . 
        "<br>Please ensure MySQL service is running in Railway.");
}

// Connection successful - $conn variable now available for database queries
?>
