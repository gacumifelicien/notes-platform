<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Database connection with error handling
try {
    $db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['error'] = "Database connection failed: " . $e->getMessage();
    header("Location: manage_notes.php");
    exit();
}

// Validate and sanitize input
$note_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$subject_id = filter_input(INPUT_GET, 'subject_id', FILTER_VALIDATE_INT);

if ($note_id > 0) {
    try {
        // First get the filename if it exists
        $stmt = $db->prepare("SELECT filename FROM notes WHERE id = ?");
        $stmt->execute([$note_id]);
        $note = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($note) {
            // Delete the file if it exists
            if (!empty($note['filename'])) {
                $file_path = 'uploads/notes/' . $note['filename'];
                
                // Validate file path to prevent directory traversal
                $base_dir = realpath('uploads/notes/');
                $absolute_path = realpath($file_path);
                
                if ($absolute_path && strpos($absolute_path, $base_dir) === 0 && is_file($absolute_path)) {
                    if (!unlink($absolute_path)) {
                        throw new Exception("Failed to delete file");
                    }
                }
            }
            
            // Delete the database record
            $stmt = $db->prepare("DELETE FROM notes WHERE id = ?");
            $stmt->execute([$note_id]);
            
            $_SESSION['success'] = "Note deleted successfully";
        } else {
            $_SESSION['error'] = "Note not found";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting note: " . $e->getMessage();
    }
}

// Redirect back with appropriate subject_id
$redirect_url = "manage_notes.php" . ($subject_id ? "?subject_id=$subject_id" : "");
header("Location: $redirect_url");
exit();
?>