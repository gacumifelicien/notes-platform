<?php
// Start output buffering to prevent header errors
ob_start();

// Database connection and file download logic
try {
    $db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $noteId = intval($_GET['id'] ?? 0);
    $stmt = $db->prepare("SELECT filename, CONCAT('uploads/notes/', filename) as filepath FROM notes WHERE id = ?");
    $stmt->execute([$noteId]);
    $note = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($note && file_exists($note['filepath'])) {
        // Clear any previous output
        ob_end_clean();
        
        // Set headers for file download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($note['filename']).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($note['filepath']));
        flush();
        readfile($note['filepath']);
        exit;
    } else {
        // If file doesn't exist, redirect with error
        header("Location: notes.php?error=note_not_found");
        exit;
    }
} catch (PDOException $e) {
    // If there's a database error, redirect with error
    header("Location: notes.php?error=db_error");
    exit;
}
?>