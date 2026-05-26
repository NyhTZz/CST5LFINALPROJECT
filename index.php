<?php
/**
 * INDEX.PHP - Entry Point
 * This is the first page users see when visiting the application
 * Automatically redirects to login page
 */

// Redirect browser to login page
header("Location: login.php");

// Stop script execution after redirect
// Ensures no code runs after the redirect
exit();
?>
