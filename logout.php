<?php
/**
 * LOGOUT.PHP - User Logout Handler
 * Destroys user session and redirects to login page
 * Logs user out of the system
 */

// Start or resume the existing session
// Required to access session data before destroying it
session_start();

// Clear all session variables
// Removes user_id, username, email from session
session_unset();

// Completely destroy the session
// User is now logged out
session_destroy();

// Redirect to login page
// User must login again to access the system
header("Location: login.php");

// Stop script execution
exit();
?>
