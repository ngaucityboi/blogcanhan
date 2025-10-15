<?php
/**
 * Main Functions File
 * Include tất cả các function files từ folder functions
 */

// Đường dẫn đến folder functions
$functions_dir = __DIR__ . '/functions/';

// Kiểm tra folder functions có tồn tại không
if (is_dir($functions_dir)) {
    // Lấy tất cả file .php trong folder functions
    $function_files = glob($functions_dir . '*.php');
    
    // Include từng file
    foreach ($function_files as $file) {
        if (is_file($file)) {
            require_once $file;
        }
    }
} else {
    // Tạo folder functions nếu chưa có
    mkdir($functions_dir, 0755, true);
}

/**
 * Utility Functions - Các function tiện ích chung
 */

/**
 * Sanitize input data
 * @param string $data
 * @return string
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Format date to Vietnamese format
 * @param string $date
 * @return string
 */
function formatDateVN($date) {
    return date('d/m/Y H:i', strtotime($date));
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect function
 * @param string $url
 * @param int $statusCode
 */
function redirect($url, $statusCode = 302) {
    header("Location: $url", true, $statusCode);
    exit;
}

/**
 * Check if request is POST
 * @return bool
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request is GET
 * @return bool
 */
function isGet() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Get POST data safely
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function getPost($key, $default = '') {
    return $_POST[$key] ?? $default;
}

/**
 * Get GET data safely
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function getGet($key, $default = '') {
    return $_GET[$key] ?? $default;
}
