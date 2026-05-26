<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Railway Connection Test</title>
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
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Railway MySQL Connection Test</h2>
        
        <?php
        echo "<h3>Environment Variables:</h3>";
        echo "<pre>";
        echo "MYSQLHOST: " . ($_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? 'NOT SET') . "\n";
        echo "MYSQLPORT: " . ($_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? 'NOT SET') . "\n";
        echo "MYSQLDATABASE: " . ($_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? 'NOT SET') . "\n";
        echo "MYSQLUSER: " . ($_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? 'NOT SET') . "\n";
        echo "MYSQLPASSWORD: " . (($_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD')) ? '***SET***' : 'NOT SET') . "\n";
        echo "</pre>";
        
        $host = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? 'localhost';
        $port = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? '3306';
        $dbname = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? 'trackersystemdb';
        $user = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? 'root';
        $pass = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? '';
        
        echo "<h3>Connection Attempt:</h3>";
        echo "<p class='info'>Attempting to connect to: $host:$port</p>";
        
        // Test connection
        $conn = @mysqli_connect($host, $user, $pass, $dbname, (int)$port);
        
        if ($conn) {
            echo "<p class='success'>✓ Successfully connected to MySQL!</p>";
            echo "<p class='success'>✓ Database: $dbname</p>";
            
            // Test query
            $result = mysqli_query($conn, "SELECT VERSION() as version");
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                echo "<p class='success'>✓ MySQL Version: " . $row['version'] . "</p>";
            }
            
            // Check tables
            $result = mysqli_query($conn, "SHOW TABLES");
            if ($result) {
                $tables = [];
                while ($row = mysqli_fetch_array($result)) {
                    $tables[] = $row[0];
                }
                
                if (count($tables) > 0) {
                    echo "<p class='success'>✓ Tables found: " . implode(', ', $tables) . "</p>";
                } else {
                    echo "<p class='error'>✗ No tables found. Please run setup_database.php</p>";
                }
            }
            
            mysqli_close($conn);
            
            echo "<hr>";
            echo "<p><a href='setup_database.php' style='padding:10px 20px; background:#667eea; color:white; text-decoration:none; border-radius:5px;'>Setup Database Tables</a></p>";
            echo "<p><a href='index.php' style='padding:10px 20px; background:#28a745; color:white; text-decoration:none; border-radius:5px; margin-top:10px; display:inline-block;'>Go to App</a></p>";
            
        } else {
            echo "<p class='error'>✗ Connection failed!</p>";
            echo "<p class='error'>Error: " . mysqli_connect_error() . "</p>";
            echo "<p class='error'>Error Code: " . mysqli_connect_errno() . "</p>";
            
            echo "<h3>Troubleshooting:</h3>";
            echo "<ul>";
            echo "<li>Verify MySQL service is running in Railway</li>";
            echo "<li>Check that environment variables are set correctly</li>";
            echo "<li>Ensure MySQL service is in the same Railway project</li>";
            echo "<li>Try restarting the MySQL service in Railway</li>";
            echo "</ul>";
        }
        ?>
    </div>
</body>
</html>
