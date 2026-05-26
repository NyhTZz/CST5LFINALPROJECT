<?php
/**
 * REGISTER.PHP - User Registration Page
 * Creates new user accounts with validation
 * Hashes passwords securely before storing in database
 */

// Start session for potential future use
session_start();

// Include database connection
include("database.php");

// Initialize message variables
$error = "";    // error messages stored
$success = "";  // success maessages stored

// FORM SUBMISSION HANDLER - Processes registration when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Cleaning Inputs to prevent SQL injection
    $username = mysqli_real_escape_string($conn, trim($_POST["username"]));
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
    
    // VALIDATION BLOCK: Check requirements before creating account
    
    // Check if all required fields are filled
    if (empty($username) || empty($password) || empty($email)) {
        $error = "All fields are required!";
        
    // Check if passwords match
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
        
    // Check password length (minimum 6 characters)
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
        
    } else {
        // DUPLICATE CHECK: Verify username and email are unique
        $check_sql = "SELECT * FROM Users WHERE Username = '$username' OR Email = '$email'";
        $result = mysqli_query($conn, $check_sql);
        
        // If any rows returned, username or email already exists
        if (mysqli_num_rows($result) > 0) {
            $error = "Username or Email already exists!";
        } else {
            // PASSWORD HASHING: Create secure hash using bcrypt
            // Never store plain text passwords!
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // INSERT USER: Add new user to database
            $insert_sql = "INSERT INTO Users (Username, uPassword, Email) 
                          VALUES ('$username', '$hashed_password', '$email')";
            
            // Execute insert query
            if (mysqli_query($conn, $insert_sql)) {
                // Registration successful
                $success = "Registration successful! You can now login.";
            } else {
                // Registration failed (database error)
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Task Tracker</title>
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
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
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
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
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
        <h2>Register</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn">Register</button>
        </form>
        
        <div class="link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>
