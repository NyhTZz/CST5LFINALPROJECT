<?php
/**
 * LOGIN.PHP - User Login Page
 * Authenticates users and creates session
 * Verifies username and password against database
 */

// Start session to store user login information
session_start();

// Include database connection file
include("database.php");

// Initialize error message variable
$error = "";

// CHECK IF ALREADY LOGGED IN: Redirect to dashboard if user already has active session
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}

// FORM SUBMISSION HANDLER: Process login when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // SANITIZE INPUT: Clean username to prevent SQL injection
    // trim() removes extra spaces, mysqli_real_escape_string() escapes special characters
    $username = mysqli_real_escape_string($conn, trim($_POST["username"]));
    
    // Get password (not escaped because it will be verified with hash)
    $password = trim($_POST["password"]);
    
    // VALIDATION: Check if both fields are filled
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password!";
    } else {
        // QUERY DATABASE: Search for user by username
        $sql = "SELECT * FROM Users WHERE Username = '$username'";
        $result = mysqli_query($conn, $sql);
        
        // CHECK IF USER EXISTS: Verify exactly one user found
        if (mysqli_num_rows($result) == 1) {
            // Fetch user data as associative array
            $user = mysqli_fetch_assoc($result);
            
            // VERIFY PASSWORD: Compare entered password with hashed password in database
            // password_verify() handles bcrypt comparison
            if (password_verify($password, $user["uPassword"])) {
                
                // LOGIN SUCCESSFUL: Create session variables
                $_SESSION["user_id"] = $user["UserID"];       // Store user ID
                $_SESSION["username"] = $user["Username"];     // Store username
                $_SESSION["email"] = $user["Email"];           // Store email
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
                
            } else {
                // Password incorrect
                $error = "Invalid username or password!";
            }
        } else {
            // User not found
            $error = "Invalid username or password!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Task Tracker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5568d3;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .link a {
            color: #667eea;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        
        <!-- Display error message if login fails -->
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Form submits to same page via POST method -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>
