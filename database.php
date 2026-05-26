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

// Enable detailed error reporting for debugging
// Shows SQL errors and connection issues during development
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Try to establish database connection
try {
    // Connect to MySQL database using provided credentials
    // Include port parameter for Railway compatibility
    // Returns connection object stored in $conn variable
    $conn = mysqli_connect($host, $user, $pass, $dbname, $port);
    
    // Check if connection was successful
    if (!$conn) {
        // If connection fails, stop execution and show error
        die("Connection failed: " . mysqli_connect_error() . "<br>Please run <a href='setup_database.php'>setup_database.php</a> first!");
    }
    
    // Set character encoding to UTF-8 for proper handling of special characters
    mysqli_set_charset($conn, "utf8");
    
} catch (mysqli_sql_exception $e) {
    // Catch any connection errors and display helpful message
    die("Database connection error: " . $e->getMessage() . "<br>Please run <a href='setup_database.php'>setup_database.php</a> first!");
}

// Connection successful - $conn variable now available for database queries
?>
