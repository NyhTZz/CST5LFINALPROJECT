<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Task Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .success {
            color: green;
            margin: 10px 0;
        }
        .error {
            color: red;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 10px 0 0;
        }
        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Database Setup</h2>
        <p>Starting database setup...</p>
        
        <?php
        /**
         * SETUP_DATABASE.PHP - Database Installation Script
         * Creates database and tables for the Task Tracker System
         * Run this file ONCE during initial installation
         * 
         * RAILWAY DEPLOYMENT: Uses environment variables when available
         */
        
        // Database configuration with Railway environment variables support
        $db_server = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? 'localhost';
        $db_port = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? '3306';
        $db_name = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? 'trackersystemdb';
        $db_user = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? 'root';
        $db_pass = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? $_ENV['MYSQLROOT_PASSWORD'] ?? getenv('MYSQLROOT_PASSWORD') ?? '';

        // STEP 1: CONNECT TO MYSQL SERVER (without selecting a database)
        // Database doesn't exist yet, so we connect to MySQL server first
        $conn = mysqli_connect($db_server, $db_user, $db_pass, null, $db_port);

        // Check if connection successful
        if (!$conn) {
            echo "<p class='error'>✗ Connection failed: " . mysqli_connect_error() . "</p>";
            echo "<p>Make sure XAMPP MySQL is running (local) or Railway MySQL is configured!</p>";
            exit();
        }

        echo "<p class='success'>✓ Connected to MySQL server</p>";

        // STEP 2: CREATE DATABASE (only for local, Railway database already exists)
        // IF NOT EXISTS prevents error if database already created
        $sql = "CREATE DATABASE IF NOT EXISTS `$db_name`";
        if (mysqli_query($conn, $sql)) {
            echo "<p class='success'>✓ Database '$db_name' created successfully or already exists</p>";
        } else {
            echo "<p class='error'>✗ Error creating database: " . mysqli_error($conn) . "</p>";
        }

        // STEP 3: SELECT DATABASE
        // Switch to the newly created database
        mysqli_select_db($conn, $db_name);

        // STEP 4: CREATE USERS TABLE
        // Stores user account information
        $sql = "CREATE TABLE IF NOT EXISTS Users (
            UserID INT AUTO_INCREMENT PRIMARY KEY,
            Username VARCHAR(50) UNIQUE NOT NULL,
            uPassword VARCHAR(255) NOT NULL,
            Email VARCHAR(100) UNIQUE NOT NULL,
            CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        if (mysqli_query($conn, $sql)) {
            echo "<p class='success'>✓ Users table created successfully</p>";
        } else {
            echo "<p class='error'>✗ Error creating Users table: " . mysqli_error($conn) . "</p>";
        }

        // STEP 5: CREATE TASKS TABLE
        // Stores task information linked to users
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

        if (mysqli_query($conn, $sql)) {
            echo "<p class='success'>✓ Tasks table created successfully</p>";
        } else {
            echo "<p class='error'>✗ Error creating Tasks table: " . mysqli_error($conn) . "</p>";
        }

        // STEP 6: SETUP COMPLETE
        echo "<hr>";
        echo "<h3 style='color:green;'>✓ Database setup complete!</h3>";
        
        // Close database connection
        mysqli_close($conn);
        ?>
        
        <p>
            <a href='register.php' class='btn'>Go to Register</a>
            <a href='login.php' class='btn'>Go to Login</a>
        </p>
    </div>
</body>
</html>
