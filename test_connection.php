<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Database Connection</title>
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
        .success { color: green; margin: 10px 0; }
        .error { color: red; margin: 10px 0; }
        .info { color: blue; margin: 10px 0; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background: #667eea;
            color: white;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Database Connection Test</h2>
        
        <?php
        $db_server = "localhost";
        $db_user = "root";
        $db_pass = "";
        $db_name = "trackersystemdb";

        // Test 1: Connect to MySQL
        echo "<h3>Test 1: MySQL Connection</h3>";
        $conn = mysqli_connect($db_server, $db_user, $db_pass);
        if ($conn) {
            echo "<p class='success'>✓ Successfully connected to MySQL server</p>";
        } else {
            echo "<p class='error'>✗ Failed to connect to MySQL: " . mysqli_connect_error() . "</p>";
            echo "<p class='info'>Make sure XAMPP MySQL is running!</p>";
            exit();
        }

        // Test 2: Check if database exists
        echo "<h3>Test 2: Database Check</h3>";
        $db_check = mysqli_query($conn, "SHOW DATABASES LIKE '$db_name'");
        if (mysqli_num_rows($db_check) > 0) {
            echo "<p class='success'>✓ Database '$db_name' exists</p>";
        } else {
            echo "<p class='error'>✗ Database '$db_name' does not exist</p>";
            echo "<p class='info'>Please run <a href='setup_database.php'>setup_database.php</a> first!</p>";
            exit();
        }

        // Test 3: Select database
        echo "<h3>Test 3: Select Database</h3>";
        if (mysqli_select_db($conn, $db_name)) {
            echo "<p class='success'>✓ Successfully selected database '$db_name'</p>";
        } else {
            echo "<p class='error'>✗ Failed to select database: " . mysqli_error($conn) . "</p>";
            exit();
        }

        // Test 4: Check Users table
        echo "<h3>Test 4: Users Table Check</h3>";
        $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'Users'");
        if (mysqli_num_rows($table_check) > 0) {
            echo "<p class='success'>✓ Users table exists</p>";
            
            // Show table structure
            $structure = mysqli_query($conn, "DESCRIBE Users");
            echo "<table>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            while ($row = mysqli_fetch_assoc($structure)) {
                echo "<tr>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Count users
            $count = mysqli_query($conn, "SELECT COUNT(*) as total FROM Users");
            $total = mysqli_fetch_assoc($count)['total'];
            echo "<p class='info'>Total users in database: $total</p>";
            
        } else {
            echo "<p class='error'>✗ Users table does not exist</p>";
            echo "<p class='info'>Please run <a href='setup_database.php'>setup_database.php</a> first!</p>";
        }

        // Test 5: Check Tasks table
        echo "<h3>Test 5: Tasks Table Check</h3>";
        $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'Tasks'");
        if (mysqli_num_rows($table_check) > 0) {
            echo "<p class='success'>✓ Tasks table exists</p>";
            
            // Count tasks
            $count = mysqli_query($conn, "SELECT COUNT(*) as total FROM Tasks");
            $total = mysqli_fetch_assoc($count)['total'];
            echo "<p class='info'>Total tasks in database: $total</p>";
            
        } else {
            echo "<p class='error'>✗ Tasks table does not exist</p>";
            echo "<p class='info'>Please run <a href='setup_database.php'>setup_database.php</a> first!</p>";
        }

        mysqli_close($conn);
        ?>
        
        <hr>
        <h3>All Tests Complete!</h3>
        <p>
            <a href='setup_database.php' class='btn'>Run Setup</a>
            <a href='register.php' class='btn'>Go to Register</a>
            <a href='login.php' class='btn'>Go to Login</a>
        </p>
    </div>
</body>
</html>
