<?php
/**
 * TOGGLE_TASK.PHP - Task Status Toggle Handler
 * Changes task status between Pending and Complete
 * No UI - just processing logic, then redirects to dashboard
 */

// Start session to access logged-in user information
session_start();

// Include database connection
include("database.php");

// AUTHENTICATION CHECK: Verify user is logged in
if (!isset($_SESSION["user_id"])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Get task ID from URL parameter, convert to integer for security
// If no ID provided, default to 0
$task_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Get new status from URL parameter
// If not provided, default to "Pending"
$status = isset($_GET["status"]) ? $_GET["status"] : "Pending";

// Get logged-in user's ID from session
$user_id = $_SESSION["user_id"];

// VALIDATION: Ensure status is valid (only Pending or Complete allowed)
if ($status != "Pending" && $status != "Complete") {
    // If invalid status provided, default to Pending
    $status = "Pending";
}

// Update task status in database
// WHERE clause ensures: 1) Correct task is updated, 2) User owns the task
$sql = "UPDATE Tasks SET TaskStatus = '$status' WHERE TaskID = $task_id AND UserID = $user_id";

// Execute the update query
if (mysqli_query($conn, $sql)) {
    // Success: Redirect back to dashboard to see updated task
    header("Location: dashboard.php");
    exit();
} else {
    // Error: Display error message (rarely happens)
    echo "Error updating task: " . mysqli_error($conn);
}
?>
