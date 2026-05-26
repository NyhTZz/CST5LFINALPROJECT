<?php
/**
 * ADD_TASK.PHP - New Task Creation Page
 * Allows logged-in users to create new tasks
 * Tasks are automatically linked to the logged-in user
 */

// Start session to access user information
session_start();

// Include database connection
include("database.php");

// AUTHENTICATION CHECK: Verify user is logged in
if (!isset($_SESSION["user_id"])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Initialize message variables
$error = "";
$success = "";

// FORM SUBMISSION HANDLER: Process new task when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get logged-in user's ID from session
    // This links the task to the current user
    $user_id = $_SESSION["user_id"];
    
    // SANITIZE INPUT: Clean task data to prevent SQL injection
    $task_title = mysqli_real_escape_string($conn, trim($_POST["task_title"]));
    $task_description = mysqli_real_escape_string($conn, trim($_POST["task_description"]));
    
    // VALIDATION: Ensure task title is not empty
    // Description is optional
    if (empty($task_title)) {
        $error = "Task title is required!";
    } else {
        // INSERT TASK: Add new task to database
        // UserID links task to current user
        // TaskStatus defaults to 'Pending'
        // TaskID, CreatedAt, UpdatedAt are auto-generated
        $sql = "INSERT INTO Tasks (UserID, TaskTitle, TaskDescription, TaskStatus) 
                VALUES ($user_id, '$task_title', '$task_description', 'Pending')";
        
        // Execute insert query
        if (mysqli_query($conn, $sql)) {
            // Success: Redirect to dashboard to see new task
            header("Location: dashboard.php");
            exit();
        } else {
            // Error: Display error message
            $error = "Error adding task: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task - Task Tracker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        .navbar {
            background: #667eea;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .navbar h1 {
            font-size: 24px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
        }
        .navbar a:hover {
            background: rgba(255,255,255,0.3);
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 0 20px;
        }
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
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
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }
        textarea {
            min-height: 150px;
            resize: vertical;
        }
        input:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            padding: 12px 30px;
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
        .btn-secondary {
            background: #6c757d;
            margin-left: 10px;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Task Tracker System</h1>
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
    
    <div class="container">
        <div class="form-container">
            <h2>Add New Task</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="task_title">Task Title:</label>
                    <input type="text" id="task_title" name="task_title" required>
                </div>
                
                <div class="form-group">
                    <label for="task_description">Task Description:</label>
                    <textarea id="task_description" name="task_description" placeholder="Enter task details..."></textarea>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn">Add Task</button>
                    <a href="dashboard.php" class="btn btn-secondary" style="text-decoration: none; display: inline-block; text-align: center; line-height: 1;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
