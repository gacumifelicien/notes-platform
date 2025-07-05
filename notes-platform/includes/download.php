<?php
session_start();
require_once __DIR__ . '/config.php'; // Configuration na $pdo

// Security checks
if (!isset($_GET['id'])) {
    die("Invalid request");
}
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$fileId = (int)$_GET['id'];
$type = $_GET['type'] ?? ''; // 'note' or 'paper'

try {
    // Koresha $pdo iva muri config.php aho kurema connection nshya
    if ($type === 'note') {
        $stmt = $pdo->prepare("
            SELECT n.filepath, n.original_name, s.name as subject_name
            FROM notes n
            JOIN subjects s ON n.subject_id = s.id
            WHERE n.id = ? AND n.is_active = 1
        ");
    } elseif ($type === 'paper') {
        $stmt = $pdo->prepare("
            SELECT file_path as filepath, original_name, title
            FROM past_papers
            WHERE id = ? AND is_active = 1
        ");
    } else {
        die("Invalid file type");
    }
    
    $stmt->execute([$fileId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$file || empty($file['filepath'])) {
        die("File not found or unavailable");
    }
    
    // Koresha UPLOAD_DIR iva muri config.php
    $uploadDir = UPLOAD_DIR;
    $filePath = $uploadDir . $file['filepath'];
    
    // Security check: file igomba kuba iri muri uploadDir
    if (!file_exists($filePath)) {
        die("File not found on server");
    }
    if (strpos(realpath($filePath), realpath($uploadDir)) !== 0) {
        die("Invalid file path");
    }
    
    // Safely sanitize filename
    $cleanName = preg_replace('/[^\w\.\-]/', '_', $file['original_name'] ?? basename($file['filepath']));
    
    // Set headers
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $cleanName . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    
    // Clean output buffer if possible (suppress warnings)
    @ob_clean();
    @flush();
    
    readfile($filePath);
    exit;

} catch (PDOException $e) {
    error_log("Download error: " . $e->getMessage());
    die("An error occurred while processing your download");
}
