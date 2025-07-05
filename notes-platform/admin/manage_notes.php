<?php
session_start();
if(!isset($_SESSION['admin'])) header("Location: login.php");
$db = new PDO("mysql:host=localhost;dbname=notes_platform","root","");
$sid = intval($_GET['subject_id'] ?? 0);
$subject = $db->prepare("SELECT * FROM subjects WHERE id=?");
$subject->execute([$sid]);
$s = $subject->fetch(PDO::FETCH_ASSOC);
if(!$s) exit("Invalid subject.");
$notes = $db->prepare("SELECT * FROM notes WHERE subject_id=? ORDER BY uploaded_at DESC");
$notes->execute([$sid]);
$notes = $notes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage Notes - <?= htmlspecialchars($s['name']) ?></title>
  <link rel="stylesheet" href="css/admin_style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    /* Admin-specific styles */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f7fa;
      margin: 0;
      padding: 0;
      padding-bottom: 100px; /* Space for fixed footer */
    }

    header {
      background: #2F4F4F;
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    header nav a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
      font-weight: 500;
    }

    header nav a:hover {
      text-decoration: underline;
    }

    .container {
      max-width: 1000px;
      margin: 30px auto;
      padding: 0 20px;
    }

    form {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }

    input, textarea, button {
      display: block;
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-family: inherit;
    }

    textarea {
      min-height: 100px;
      resize: vertical;
    }

    button {
      background: #2F4F4F;
      color: white;
      border: none;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s;
    }

    button:hover {
      background: #1abc9c;
    }

    ul {
      list-style: none;
      padding: 0;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    ul li {
      padding: 15px 20px;
      border-bottom: 1px solid #eee;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    ul li:last-child {
      border-bottom: none;
    }

    ul li a {
      color: #e74c3c;
      text-decoration: none;
      font-weight: 500;
    }

    ul li a:hover {
      text-decoration: underline;
    }

    footer.admin-footer {
      background: #2c3e50;
      color: white;
      padding: 15px 20px;
      text-align: center;
      margin-top: 40px;
      font-size: 0.9em;
      position: fixed;
      bottom: 0;
      width: 100%;
      box-shadow: 0 -2px 5px rgba(0,0,0,0.2);
    }

    footer.admin-footer .social-icons {
      margin-top: 10px;
    }

    footer.admin-footer .social-icons a {
      color: white;
      margin: 0 8px;
      font-size: 1.2em;
      transition: color 0.3s;
    }

    footer.admin-footer .social-icons a:hover {
      color: #1abc9c;
    }
  </style>
</head>
<body>

<header>
  <span>My Notes Platform - Admin Panel</span>
  <nav>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </nav>
</header>

<div class="container">
  <h1>Manage Notes for <?= htmlspecialchars($s['name']) ?></h1>

  <h2>Add New Note</h2>
  <form action="upload_note.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="subject_id" value="<?= $sid ?>">
    <input name="title" placeholder="Note title" required>
    <textarea name="content" rows="4" placeholder="Or type note content here (optional)"></textarea>
    <input type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt">
    <button type="submit"><i class="fas fa-plus-circle"></i> Add Note</button>
  </form>

  <h2>Existing Notes</h2>
  <ul>
    <?php foreach ($notes as $n): ?>
      <li>
        <div>
          <strong><?= htmlspecialchars($n['title']) ?></strong>
          <div style="font-size: 0.9em; color: #777;">
            <?= date('M j, Y H:i', strtotime($n['uploaded_at'])) ?>
            <?php if ($n['filename']): ?>
              | <i class="fas fa-file"></i> <?= htmlspecialchars($n['filename']) ?>
            <?php endif; ?>
          </div>
        </div>
        <a href="delete_note.php?id=<?= $n['id'] ?>&subject_id=<?= $sid ?>" onclick="return confirm('Delete this note?')">
          <i class="fas fa-trash-alt"></i> Delete
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<footer class="admin-footer">
  <p>© <?= date('Y'); ?> My Notes Platform. All rights reserved.</p>
  <div class="social-icons">
    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer">
      <i class="fab fa-facebook-f"></i>
    </a>
    <a href="https://twitter.com" target="_blank" rel="noopener noreferrer">
      <i class="fab fa-twitter"></i>
    </a>
    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">
      <i class="fab fa-instagram"></i>
    </a>
  </div>
</footer>

</body>
</html>
