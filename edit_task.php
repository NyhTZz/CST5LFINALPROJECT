<?php
/**
 * EDIT_TASK.PHP - Task Editing Page
 * Allows users to modify existing tasks
 * Includes security check to ensure user owns the task
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

// Initialize error message variable
$error = "";

// GET TASK ID: Retrieve task ID from URL parameter
// Example: edit_task.php?id=5
// intval() converts to integer for security
$task_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Get logged-in user's ID from session
$user_id = $_SESSION["user_id"];

// FETCH TASK DATA: Get task details from database
// WHERE clause ensures: 1) Correct task, 2) User owns the task
$sql = "SELECT * FROM Tasks WHERE TaskID = $task_id AND UserID = $user_id";
$result = mysqli_query($conn, $sql);

// OWNERSHIP VERIFICATION: Check if task exists and belongs to user
if (mysqli_num_rows($result) == 0) {
    // Task not found or doesn't belong to user
    // Redirect to dashboard (security measure)
    header("Location: dashboard.php");
    exit();
}

// Fetch task data as associative array
// Used to pre-fill form fields
$task = mysqli_fetch_assoc($result);

// FORM SUBMISSION HANDLER: Process updates when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // SANITIZE INPUT: Clean updated task data
    $task_title = mysqli_real_escape_string($conn, trim($_POST["task_title"]));
    $task_description = mysqli_real_escape_string($conn, trim($_POST["task_description"]));
    
    // VALIDATION: Ensure title is not empty
    if (empty($task_title)) {
        $error = "Task title is required!";
    } else {
        // UPDATE TASK: Modify task in database
        // WHERE clause ensures correct task and user ownership
        // UpdatedAt timestamp automatically updated by database
        $update_sql = "UPDATE Tasks 
                       SET TaskTitle = '$task_title', TaskDescription = '$task_description' 
                       WHERE TaskID = $task_id AND UserID = $user_id";
        
        // Execute update query
        if (mysqli_query($conn, $update_sql)) {
            // Success: Redirect to dashboard to see updated task
            header("Location: dashboard.php");
            exit();
        } else {
            // Error: Display error message
            $error = "Error updating task: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - Task Tracker</title>
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
            <h2>Edit Task</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="task_title">Task Title:</label>
                    <input type="text" id="task_title" name="task_title" 
                           value="<?php echo htmlspecialchars($task['TaskTitle']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="task_description">Task Description:</label>
                    <textarea id="task_description" name="task_description"><?php echo htmlspecialchars($task['TaskDescription']); ?></textarea>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn">Update Task</button>
                    <a href="dashboard.php" class="btn btn-secondary" style="text-decoration: none; display: inline-block; text-align: center; line-height: 1;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
