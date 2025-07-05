<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: login.php");

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");

// Get paper info to delete the file
$stmt = $db->prepare("SELECT file_path FROM past_papers WHERE id = ?");
$stmt->execute([$_GET['id']]);
$paper = $stmt->fetch(PDO::FETCH_ASSOC);

// Delete the file if exists
if ($paper && file_exists($paper['file_path'])) {
    unlink($paper['file_path']);
}

// Delete from database
$stmt = $db->prepare("DELETE FROM past_papers WHERE id = ?");
$stmt->execute([$_GET['id']]);

$_SESSION['success'] = "Past paper deleted successfully";
header("Location: dashboard.php");
exit;
?>