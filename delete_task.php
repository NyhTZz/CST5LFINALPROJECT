<?php
/**
 * DELETE_TASK.PHP - Task Deletion Handler
 * Permanently removes a task from the database
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
// Example: delete_task.php?id=5
$task_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Get logged-in user's ID from session
$user_id = $_SESSION["user_id"];

// Delete task from database
// WHERE clause ensures: 1) Correct task is deleted, 2) User owns the task
// This prevents users from deleting other users' tasks
$sql = "DELETE FROM Tasks WHERE TaskID = $task_id AND UserID = $user_id";

// Execute the delete query
if (mysqli_query($conn, $sql)) {
    // Success: Redirect back to dashboard
    // Task will no longer appear in the list
    header("Location: dashboard.php");
    exit();
} else {
    // Error: Display error message (rarely happens)
    echo "Error deleting task: " . mysqli_error($conn);
}
?>
