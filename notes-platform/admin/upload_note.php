<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = intval($_POST['subject_id']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content'] ?? '');
    
    // Validate inputs
    if(empty($title) || empty($subject_id)) {
        $_SESSION['error'] = "Title and subject are required";
        header("Location: manage_notes.php?subject_id=$subject_id");
        exit();
    }
    
    // Handle file upload
    $filename = null;
    if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Validate file
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'text/plain'];
        $file_type = $_FILES['file']['type'];
        
        if(!in_array($file_type, $allowed_types)) {
            $_SESSION['error'] = "Only PDF, DOC, DOCX, PPT, PPTX, and TXT files are allowed";
            header("Location: manage_notes.php?subject_id=$subject_id");
            exit();
        }
        
        // Create uploads/notes directory if it doesn't exist
        $upload_dir = 'uploads/notes/';
        if(!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('note_', true) . '.' . $extension;
        $destination = $upload_dir . $filename;
        
        if(!move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
            $_SESSION['error'] = "Failed to upload file";
            header("Location: manage_notes.php?subject_id=$subject_id");
            exit();
        }
    }
    
    // Insert into database
    $stmt = $db->prepare("INSERT INTO notes (subject_id, title, content, filename, uploaded_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$subject_id, $title, $content, $filename]);
    
    $_SESSION['success'] = "Note added successfully";
    header("Location: manage_notes.php?subject_id=$subject_id");
    exit();
}

// If not a POST request, redirect
header("Location: dashboard.php");
exit();
?>