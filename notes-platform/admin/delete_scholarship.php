<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: login.php");

if (isset($_GET['id'])) {
    $db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");
    $stmt = $db->prepare("DELETE FROM scholarships WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: dashboard.php");
exit;
?>
