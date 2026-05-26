<?php
/**
 * DASHBOARD.PHP - Main Task Management Page
 * Displays all tasks for the logged-in user
 * Provides buttons for Add, Edit, Delete, and Toggle Status
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

// GET USER INFORMATION: Retrieve from session
$user_id = $_SESSION["user_id"];      // Used to filter tasks
$username = $_SESSION["username"];    // Used for welcome message

// FETCH USER'S TASKS: Get all tasks belonging to logged-in user
// ORDER BY CreatedAt DESC: Shows newest tasks first
$sql = "SELECT * FROM Tasks WHERE UserID = $user_id ORDER BY CreatedAt DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Task Tracker</title>
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
        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
            transition: background 0.3s;
        }
        .navbar a:hover {
            background: rgba(255,255,255,0.3);
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header h2 {
            color: #333;
        }
        .btn {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5568d3;
        }
        .tasks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .task-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .task-card.complete {
            opacity: 0.7;
            background: #f0f0f0;
        }
        .task-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .task-card.complete .task-title {
            text-decoration: line-through;
        }
        .task-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        .task-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .task-status.pending {
            background: #fff3cd;
            color: #856404;
        }
        .task-status.complete {
            background: #d4edda;
            color: #155724;
        }
        .task-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .task-actions a,
        .task-actions button {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 12px;
            border: none;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        .task-actions a:hover,
        .task-actions button:hover {
            opacity: 0.8;
        }
        .btn-edit {
            background: #ffc107;
            color: #333;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn-complete {
            background: #28a745;
            color: white;
        }
        .btn-pending {
            background: #6c757d;
            color: white;
        }
        .no-tasks {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            color: #666;
        }
        .no-tasks h3 {
            margin-bottom: 10px;
        }
        .task-date {
            font-size: 12px;
            color: #999;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- NAVIGATION BAR: Shows system title, username, and logout button -->
    <div class="navbar">
        <h1>Task Tracker System</h1>
        <div class="user-info">
            <!-- Display logged-in username (htmlspecialchars prevents XSS) -->
            <span>Welcome, <?php echo htmlspecialchars($username); ?>!</span>
            <!-- Logout button -->
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <!-- HEADER: Page title and Add Task button -->
        <div class="header">
            <h2>My Tasks</h2>
            <a href="add_task.php" class="btn">+ Add New Task</a>
        </div>
        
        <!-- CHECK IF USER HAS TASKS: Display tasks or "no tasks" message -->
        <?php if (mysqli_num_rows($result) > 0): ?>
            <!-- TASKS GRID: Display all tasks in card layout -->
            <div class="tasks-grid">
                <!-- LOOP THROUGH TASKS: Create a card for each task -->
                <?php while ($task = mysqli_fetch_assoc($result)): ?>
                    <!-- TASK CARD: Add 'complete' class if task is complete -->
                    <div class="task-card <?php echo strtolower($task['TaskStatus']); ?>">
                        <!-- Task title (htmlspecialchars prevents XSS attacks) -->
                        <div class="task-title"><?php echo htmlspecialchars($task['TaskTitle']); ?></div>
                        <div class="task-description"><?php echo htmlspecialchars($task['TaskDescription']); ?></div>
                        <span class="task-status <?php echo strtolower($task['TaskStatus']); ?>">
                            <?php echo $task['TaskStatus']; ?>
                        </span>
                        
                        <!-- ACTION BUTTONS: Edit, Delete, Toggle Status -->
                        <div class="task-actions">
                            <!-- EDIT BUTTON: Links to edit page with task ID -->
                            <a href="edit_task.php?id=<?php echo $task['TaskID']; ?>" class="btn-edit">Edit</a>
                            
                            <!-- DELETE BUTTON: Includes JavaScript confirmation dialog -->
                            <a href="delete_task.php?id=<?php echo $task['TaskID']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this task?')">Delete</a>
                            
                            <!-- TOGGLE STATUS BUTTON: Changes based on current status -->
                            <?php if ($task['TaskStatus'] == 'Pending'): ?>
                                <!-- If Pending, show "Mark Complete" button -->
                                <a href="toggle_task.php?id=<?php echo $task['TaskID']; ?>&status=Complete" 
                                   class="btn-complete">Mark Complete</a>
                            <?php else: ?>
                                <!-- If Complete, show "Mark Pending" button -->
                                <a href="toggle_task.php?id=<?php echo $task['TaskID']; ?>&status=Pending" 
                                   class="btn-pending">Mark Pending</a>
                            <?php endif; ?>
                        </div>
                        
                        <!-- CREATION DATE: Format timestamp as readable date -->
                        <div class="task-date">Created: <?php echo date('M d, Y', strtotime($task['CreatedAt'])); ?></div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <!-- NO TASKS MESSAGE: Displayed when user has no tasks -->
            <div class="no-tasks">
                <h3>No tasks yet!</h3>
                <p>Click "Add New Task" to create your first task.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
