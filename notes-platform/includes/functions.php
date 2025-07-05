<?php
// Ensure config is loaded
require_once __DIR__ . '/config.php';

/**
 * Get all active subjects
 */
function getAllSubjects() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT id, name, slug, description 
            FROM subjects 
            WHERE is_active = 1 
            ORDER BY name ASC
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error in getAllSubjects: " . $e->getMessage());
        return [];
    }
}

/**
 * Authenticate admin user
 */
function authenticateAdmin($username, $password) {
    global $pdo;
    
    try {
        // Get admin with lock status check
        $stmt = $pdo->prepare("
            SELECT id, username, password_hash 
            FROM admins 
            WHERE username = ? AND is_locked = 0
        ");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Update last login and reset attempts
            $pdo->prepare("
                UPDATE admins 
                SET last_login = NOW(), failed_attempts = 0 
                WHERE id = ?
            ")->execute([$admin['id']]);
            
            return $admin;
        } else {
            // Record failed attempt
            $pdo->prepare("
                UPDATE admins 
                SET failed_attempts = failed_attempts + 1 
                WHERE username = ?
            ")->execute([$username]);
            
            // Lock account if too many attempts
            $pdo->prepare("
                UPDATE admins 
                SET is_locked = 1 
                WHERE username = ? AND failed_attempts >= 5
            ")->execute([$username]);
            
            return false;
        }
    } catch (PDOException $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_logged_in']);
}

/**
 * Get notes for a subject
 */
function getSubjectNotes($subjectId, $publishedOnly = true) {
    global $pdo;
    
    try {
        $sql = "
            SELECT n.*, s.name AS subject_name 
            FROM notes n
            JOIN subjects s ON n.subject_id = s.id
            WHERE n.subject_id = ?
        ";
        
        if ($publishedOnly) {
            $sql .= " AND n.is_published = 1";
        }
        
        $sql .= " ORDER BY n.created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$subjectId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error getting notes: " . $e->getMessage());
        return [];
    }
}

/**
 * Sanitize input data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate and handle file upload
 */
function handleFileUpload($file, $uploadPath) {
    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error: " . $file['error']);
    }
    
    // Verify size
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception("File exceeds maximum size limit");
    }
    
    // Verify type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    
    if (!array_key_exists($mime, ALLOWED_FILE_TYPES)) {
        throw new Exception("Invalid file type");
    }
    
    // Generate safe filename
    $extension = ALLOWED_FILE_TYPES[$mime];
    $basename = bin2hex(random_bytes(8));
    $filename = sprintf('%s.%s', $basename, $extension);
    
    // Move file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {
        throw new Exception("Failed to move uploaded file");
    }
    
    return [
        'name' => $file['name'],
        'path' => $filename,
        'type' => $extension,
        'size' => $file['size']
    ];
}

/**
 * Log system events
 */
function logEvent($message, $type = 'info') {
    $logFile = BASE_DIR . '/logs/app_' . date('Y-m-d') . '.log';
    $message = sprintf(
        "[%s] [%s] %s\n",
        date('Y-m-d H:i:s'),
        strtoupper($type),
        $message
    );
    file_put_contents($logFile, $message, FILE_APPEND);
}