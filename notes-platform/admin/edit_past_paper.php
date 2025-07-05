<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: login.php");

$db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");

$subjects = $db->query("SELECT id, name FROM subjects ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$paper = null;
if (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM past_papers WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $paper = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $subject_id = $_POST['subject_id'];
    $year = $_POST['year'];
    $paper_type = $_POST['paper_type'];
    $description = $_POST['description'];
    
    // File upload handling
    $file_path = $paper['file_path'] ?? ''; // Keep existing if editing
    if (isset($_FILES['paper_file']) && $_FILES['paper_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/past_papers/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = basename($_FILES['paper_file']['name']);
        $file_path = $upload_dir . uniqid() . '_' . $file_name;
        
        if (move_uploaded_file($_FILES['paper_file']['tmp_name'], $file_path)) {
            // Delete old file if editing
            if ($paper && file_exists($paper['file_path'])) {
                unlink($paper['file_path']);
            }
        } else {
            $_SESSION['error'] = "File upload failed";
            header("Location: edit_past_paper.php?id=" . ($_GET['id'] ?? ''));
            exit;
        }
    }
    
    if ($paper) {
        // Update existing paper
        $stmt = $db->prepare("
            UPDATE past_papers 
            SET title = ?, subject_id = ?, year = ?, paper_type = ?, 
                description = ?, file_path = ?
            WHERE id = ?
        ");
        $stmt->execute([$title, $subject_id, $year, $paper_type, $description, $file_path, $paper['id']]);
        $_SESSION['success'] = "Past paper updated successfully";
    } else {
        // Insert new paper
        $stmt = $db->prepare("
            INSERT INTO past_papers 
            (title, subject_id, year, paper_type, description, file_path)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $subject_id, $year, $paper_type, $description, $file_path]);
        $_SESSION['success'] = "Past paper added successfully";
    }
    
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title><?= $paper ? 'Edit' : 'Add' ?> Past Paper</title>
  <link rel="stylesheet" href="css/admin_style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .form-container {
      max-width: 800px;
      margin: 20px auto;
      padding: 20px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
      color: #2c3e50;
    }
    
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
    }
    
    .form-group textarea {
      min-height: 100px;
    }
    
    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 30px;
    }
    
    .btn {
      padding: 10px 20px;
      border-radius: 4px;
      text-decoration: none;
      font-size: 16px;
      cursor: pointer;
    }
    
    .btn-primary {
      background: #3498db;
      color: white;
      border: none;
    }
    
    .btn-secondary {
      background: #95a5a6;
      color: white;
      border: none;
    }
    
    .file-info {
      margin-top: 10px;
      font-size: 14px;
      color: #7f8c8d;
    }
    
    .current-file {
      margin-top: 5px;
      font-weight: bold;
    }
  </style>
</head>
<body>

<header>
  My Notes Platform - Admin Panel
  <nav>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a> | 
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </nav>
</header>

<div class="form-container">
  <h1><i class="fas fa-file-alt"></i> <?= $paper ? 'Edit' : 'Add' ?> Past Paper</h1>
  
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
      <?= $_SESSION['error'] ?>
      <?php unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>
  
  <form method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label for="title">Title</label>
      <input type="text" id="title" name="title" value="<?= $paper['title'] ?? '' ?>" required>
    </div>
    
    <div class="form-group">
      <label for="subject_id">Subject</label>
      <select id="subject_id" name="subject_id" required>
        <option value="">Select Subject</option>
        <?php foreach ($subjects as $subject): ?>
          <option value="<?= $subject['id'] ?>" <?= ($paper['subject_id'] ?? '') == $subject['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($subject['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    
    <div class="form-group">
      <label for="year">Year</label>
      <input type="number" id="year" name="year" min="2000" max="<?= date('Y') ?>" 
             value="<?= $paper['year'] ?? date('Y') ?>" required>
    </div>
    
    <div class="form-group">
      <label for="paper_type">Paper Type</label>
      <select id="paper_type" name="paper_type" required>
        <option value="Main" <?= ($paper['paper_type'] ?? '') == 'Main' ? 'selected' : '' ?>>Main Exam</option>
        <option value="Supplementary" <?= ($paper['paper_type'] ?? '') == 'Supplementary' ? 'selected' : '' ?>>Supplementary Exam</option>
        <option value="Mock" <?= ($paper['paper_type'] ?? '') == 'Mock' ? 'selected' : '' ?>>Mock Exam</option>
      </select>
    </div>
    
    <div class="form-group">
      <label for="description">Description (Optional)</label>
      <textarea id="description" name="description"><?= $paper['description'] ?? '' ?></textarea>
    </div>
    
    <div class="form-group">
      <label for="paper_file">Paper File</label>
      <input type="file" id="paper_file" name="paper_file" <?= !$paper ? 'required' : '' ?>>
      <div class="file-info">
        <?php if ($paper): ?>
          <div class="current-file">
            Current file: <?= basename($paper['file_path']) ?>
          </div>
          <small>Upload a new file only if you want to replace the existing one.</small>
        <?php else: ?>
          <small>Upload PDF or document file (max 10MB)</small>
        <?php endif; ?>
      </div>
    </div>
    
    <div class="form-actions">
      <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Save Past Paper</button>
    </div>
  </form>
</div>

</body>
</html>