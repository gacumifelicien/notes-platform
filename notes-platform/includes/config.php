<?php
/*--------------------------------------------------
| XAMPP NOTES PLATFORM CONFIGURATION
| Optimized for security and development
--------------------------------------------------*/

// ==================== 1. ENVIRONMENT SETUP ====================
define('ENV', 'development'); // Set to 'production' when deploying

define('BASE_DIR', dirname(__DIR__)); // Points to xampp/htdocs/notes-platform
define('BASE_URL', 'http://localhost/notes-platform');
define('ASSETS_URL', BASE_URL . '/public/assets');

// ==================== 2. SECURITY CONFIGURATION ====================
// Session security (XAMPP-compatible)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400, // 1 day
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
        'secure' => isset($_SERVER['HTTPS']), // Auto-detect if HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    session_name('NOTES_SESSION');
    session_start();
    
    // Regenerate session ID periodically
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
    
    // CSRF token generation
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// ==================== 3. DATABASE CONFIGURATION ====================
define('DB_HOST', 'localhost');
define('DB_NAME', 'notes_platform');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

// ==================== 4. FILE UPLOAD CONFIGURATION ====================
define('UPLOAD_DIR', BASE_DIR . '/protected_uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('MAX_FILES_PER_USER', 50);

// Whitelist of allowed file types with their extensions
define('ALLOWED_FILE_TYPES', [
    // Documents
    'application/pdf' => 'pdf',
    'application/msword' => 'doc',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
    'application/vnd.ms-powerpoint' => 'ppt',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
    'text/plain' => 'txt',
    
    // Images
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif'
]);

// ==================== 5. ERROR HANDLING ====================
if (ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
}

// Log errors to XAMPP's default error log
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');

// ==================== 6. SECURITY HEADERS ====================
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header_remove('X-Powered-By');

// Content Security Policy (adjust as needed)
// header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:");

// ==================== 7. UTILITY FUNCTIONS ====================
/**
 * Sanitize user input
 */
function sanitize_input($data, $strip_tags = true) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    
    $data = trim($data);
    if ($strip_tags) {
        $data = strip_tags($data);
    }
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Validate file uploads with enhanced security
 */
function validate_upload($file) {
    // Check basic upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server limit',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit',
            UPLOAD_ERR_PARTIAL => 'File upload incomplete',
            UPLOAD_ERR_NO_FILE => 'No file selected',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
            UPLOAD_ERR_EXTENSION => 'File upload blocked'
        ];
        return ['error' => $errors[$file['error']] ?? 'Unknown upload error'];
    }

    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['error' => 'File exceeds maximum allowed size'];
    }

    // Verify actual file type (not just extension)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    
    if (!isset(ALLOWED_FILE_TYPES[$mime])) {
        return ['error' => 'File type not allowed'];
    }

    // Additional security checks
    if (!is_uploaded_file($file['tmp_name'])) {
        return ['error' => 'Possible file upload attack'];
    }

    return [
        'extension' => ALLOWED_FILE_TYPES[$mime],
        'mime' => $mime,
        'size' => $file['size']
    ];
}

/**
 * Generate CSRF token field for forms
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/**
 * Verify CSRF token
 */
function verify_csrf($token) {
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die('CSRF validation failed');
    }
}

// ==================== 8. AUTOLOAD CLASSES ====================
spl_autoload_register(function ($class_name) {
    $file = BASE_DIR . '/classes/' . str_replace('\\', '/', $class_name) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Initialize essential components
require_once BASE_DIR . '/includes/functions.php';