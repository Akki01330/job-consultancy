<?php
/**
 * ============================================
 * Job Consultancy Web Application
 * Configuration File
 * ============================================
 * Database connection and application settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'job_consultancy');

// Application Settings
define('APP_NAME', 'ProHire Consultancy');
define('APP_URL', 'http://localhost/job-consultancy');
define('APP_TIMEZONE', 'UTC');

// Security Settings
define('SECURE_PASSWORD_MIN_LENGTH', 8);
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCK_TIME', 900); // 15 minutes in seconds

// File Upload Settings
define('UPLOAD_DIR', 'uploads/');
define('ALLOWED_RESUME_TYPES', ['pdf', 'doc', 'docx']);
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('MAX_FILE_SIZE', 5242880); // 5 MB

// Pagination Settings
define('ITEMS_PER_PAGE', 10);

// Email Configuration (for future use)
define('MAIL_FROM', 'noreply@jobconsultancy.com');
define('MAIL_HOST', 'smtp.mailtrap.io');
define('MAIL_PORT', '2525');
define('MAIL_USERNAME', 'your_username');
define('MAIL_PASSWORD', 'your_password');

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Database Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Error: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Helper function to escape user input
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Helper function to redirect
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Helper function to check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

/**
 * Helper function to check user role
 */
function hasRole($role) {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === $role;
}

/**
 * Helper function to get current logged-in user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Helper function to get current user type
 */
function getCurrentUserType() {
    return $_SESSION['user_type'] ?? null;
}

/**
 * Function to validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Function to validate password strength
 */
function isValidPassword($password) {
    return strlen($password) >= SECURE_PASSWORD_MIN_LENGTH;
}

/**
 * Function to hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Function to verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Function to validate file upload
 */
function isValidFileUpload($file, $allowedTypes) {
    if (!isset($file['tmp_name']) || !isset($file['size']) || !isset($file['error'])) {
        return false;
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    // Simple extension check
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    return in_array($fileExt, $allowedTypes);
}

/**
 * Function to generate unique filename
 */
function generateUniqueFilename($originalName) {
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    return time() . '_' . md5(uniqid()) . '.' . $ext;
}

/**
 * Function to calculate pagination
 */
function getPaginationData($totalRecords, $itemsPerPage = ITEMS_PER_PAGE) {
    $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $totalPages = ceil($totalRecords / $itemsPerPage);
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'items_per_page' => $itemsPerPage
    ];
}

/**
 * Function for AJAX response
 */
function sendJsonResponse($success, $message = '', $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Helper to format dates for display
 */
function formatDate($dateString, $format = 'F j, Y') {
    if (empty($dateString) || $dateString === '0000-00-00' || $dateString === '0000-00-00 00:00:00') {
        return '';
    }

    try {
        $dt = new DateTime($dateString);
        return $dt->format($format);
    } catch (Exception $e) {
        return $dateString;
    }
}
?>
