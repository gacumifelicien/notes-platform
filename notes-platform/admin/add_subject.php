<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: login.php");

$db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if ($name) {
        $stmt = $db->prepare("INSERT INTO subjects (name) VALUES (?)");
        $stmt->execute([$name]);
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Subject name cannot be empty.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Add Subject</title>
    <link rel="stylesheet" href="css/admin_style.css" />
</head>
<body>

<header>
    My Notes Platform - Admin Panel
    <nav>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a> | 
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</header>

<div class="container">
    <h1>Add Subject</h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Subject Name:</label><br>
        <input type="text" name="name" required style="width:300px;"><br><br>
        <button type="submit">Add Subject</button>
    </form>
</div>

</body>
</html>
