<?php
/**
 * Database Connection Wrapper
 * Uses configuration from config.php
 */
require_once __DIR__ . '/../config.php';

// Config.php already initializes $conn variable
// So nothing more needed here

// Ensure connection is established
if (!$conn) {
    die("Database connection not established");
}
?>