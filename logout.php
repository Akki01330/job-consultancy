<?php
/**
 * Logout Handler
 * Logs out user from all modules
 */
require_once 'config.php';

// Destroy session
session_destroy();

// Redirect to home page
$_SESSION['message'] = 'You have been logged out successfully.';
$_SESSION['message_type'] = 'success';

redirect(APP_URL);
?>
