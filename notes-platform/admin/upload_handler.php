<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin'])) {
    die("Unauthorized access");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method");
}

// Common upload settings
$uploadDir = __DIR__ . '/../protected_uploads/';
$allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'jpg', 'png'];
$maxSize = 10 * 1024 * 1024; // 10MB

try {
    $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $fileType = $_POST['file_type'] ?? ''; // 'note' or 'paper'
    $subjectId = isset($_POST['subject_id']) ? (int)$_POST['subject_id'] : null;
    
    // Validate file
    if (!isset($_FILES['file']) {
        throw new Exception("No file uploaded");
    }
    
    $file = $_FILES['file'];
    $originalName = basename($file['name']);
    $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExt, $allowedTypes)) {
        throw new Exception("File type not allowed");
    }
    
    if ($file['size'] > $maxSize) {
        throw new Exception("File too large. Max size: " . ($maxSize/1024/1024) . "MB");
    }
    
    // Generate unique filename
    $newFilename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExt;
    $uploadPath = $uploadDir . $newFilename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception("Failed to move uploaded file");
    }
    
    // Insert into database
    if ($fileType === 'note') {
        $stmt = $db->prepare("
            INSERT INTO notes 
            (subject_id, title, content, filename, original_name, filepath, uploaded_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $subjectId,
            $_POST['title'],
            $_POST['content'] ?? null,
            $originalName,
            $originalName,
            $newFilename
        ]);
    } elseif ($fileType === 'paper') {
        $stmt = $db->prepare("
            INSERT INTO past_papers 
            (subject_id, title, year, paper_type, description, file_path, original_name, posted_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $subjectId,
            $_POST['title'],
            $_POST['year'],
            $_POST['paper_type'],
            $_POST['description'] ?? null,
            $newFilename,
            $originalName
        ]);
    } else {
        throw new Exception("Invalid file type");
    }
    
    $_SESSION['success'] = "File uploaded successfully";
    header("Location: " . ($fileType === 'note' ? 'notes.php' : 'past_papers.php'));
    exit;
    
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'dashboard.php'));
    exit;
}