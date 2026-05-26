<?php
/**
 * DATABASE CONNECTION FILE
 * This file establishes connection to MySQL database
 * Included by all PHP files that need database access
 * 
 * RAILWAY DEPLOYMENT: Uses environment variables when available
 * Falls back to localhost configuration for local development (XAMPP)
 * 
 * AUTO-SETUP: Automatically creates tables if they don't exist
 */

// Database configuration with Railway environment variables support
// Railway provides these environment variables automatically when MySQL is added
$host = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? 'localhost';
$port = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? '3306';
$dbname = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? 'railway'; // Default to 'railway' for Railway
$user = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? 'root';
$pass = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? $_ENV['MYSQLROOT_PASSWORD'] ?? getenv('MYSQLROOT_PASSWORD') ?? '';

// Validate database name is not empty
if (empty($dbname)) {
    die("Database configuration error: MYSQLDATABASE is not set.<br>" .
        "Please check your Railway environment variables.<br>" .
        "Expected: MYSQLDATABASE = railway (or your database name)");
}

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
    
    // AUTO-SETUP: Check if tables exist, create them if they don't
    $tables_exist = true;
    
    // Check if Users table exists
    $result = @mysqli_query($conn, "SHOW TABLES LIKE 'Users'");
    if (!$result || mysqli_num_rows($result) == 0) {
        $tables_exist = false;
    }
    
    // Check if Tasks table exists
    $result = @mysqli_query($conn, "SHOW TABLES LIKE 'Tasks'");
    if (!$result || mysqli_num_rows($result) == 0) {
        $tables_exist = false;
    }
    
    // If tables don't exist, create them automatically
    if (!$tables_exist) {
        // Create Users table
        $sql = "CREATE TABLE IF NOT EXISTS Users (
            UserID INT AUTO_INCREMENT PRIMARY KEY,
            Username VARCHAR(50) UNIQUE NOT NULL,
            uPassword VARCHAR(255) NOT NULL,
            Email VARCHAR(100) UNIQUE NOT NULL,
            CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        @mysqli_query($conn, $sql);
        
        // Create Tasks table
        $sql = "CREATE TABLE IF NOT EXISTS Tasks (
            TaskID INT AUTO_INCREMENT PRIMARY KEY,
            UserID INT NOT NULL,
            TaskTitle VARCHAR(200) NOT NULL,
            TaskDescription TEXT,
            TaskStatus ENUM('Pending', 'Complete') DEFAULT 'Pending',
            CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE
        )";
        @mysqli_query($conn, $sql);
    }
    
} catch (Exception $e) {
    // Catch any connection errors and display helpful message
    die("Database connection error: " . htmlspecialchars($e->getMessage()) . 
        "<br>Please ensure MySQL service is running in Railway.");
}

// Connection successful - $conn variable now available for database queries
?>
