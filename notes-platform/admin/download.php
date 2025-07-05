<?php
session_start();

try {
    $db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = (int)$_GET['id'];
    $type = $_GET['type'];

    if ($type === 'note') {
        $stmt = $db->prepare("SELECT filename FROM notes WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($file && !empty($file['filename'])) {
            $filepath = 'uploads/notes/' . $file['filename'];
            $original_name = $file['filename'];
        }
    } elseif ($type === 'paper') {
        $stmt = $db->prepare("SELECT file_path, original_name FROM past_papers WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($file && !empty($file['file_path'])) {
            $filepath = $file['file_path'];
            $original_name = $file['original_name'] ?? basename($file['file_path']);
        }
    }

    if (isset($filepath) && file_exists($filepath)) {
        // Log download (optional)
        $stmt = $db->prepare("INSERT INTO download_logs (file_id, file_type, downloaded_at) VALUES (?, ?, NOW())");
        $stmt->execute([$id, $type]);
        
        // Send file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $original_name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        die('File not found');
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}