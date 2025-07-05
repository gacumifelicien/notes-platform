<?php
// download_note.php
require_once 'header.php';

try {
    $db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $noteId = intval($_GET['id'] ?? 0);
    $stmt = $db->prepare("SELECT filename, filepath FROM notes WHERE id = ?");
    $stmt->execute([$noteId]);
    $note = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($note && file_exists($note['filepath'])) {
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
        header("Location: notes.php?error=note_not_found");
    }
} catch (PDOException $e) {
    header("Location: notes.php?error=db_error");
}
?>