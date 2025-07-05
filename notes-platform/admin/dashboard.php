<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: login.php");

$db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");
$jobs = $db->query("SELECT * FROM jobs ORDER BY posted_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
$scholarships = $db->query("SELECT * FROM scholarships ORDER BY posted_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
$subjects = $db->query("SELECT * FROM subjects ORDER BY name LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
$past_papers = $db->query("SELECT pp.*, s.name as subject_name FROM past_papers pp JOIN subjects s ON pp.subject_id = s.id ORDER BY posted_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="css/admin_style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    
    .admin-links {
      display: flex;
      gap: 15px;
      margin-bottom: 30px;
      flex-wrap: wrap;
    }
    
    .admin-links a {
      display: flex;
      align-items: center;
      gap: 5px;
      padding: 10px 15px;
      background: #3498db;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      transition: background 0.3s;
    }
    
    .admin-links a:hover {
      background: #2980b9;
    }
    
    .dashboard-section {
      background: white;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .dashboard-section h1 {
      color: #2c3e50;
      margin-top: 0;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .post-card {
      padding: 15px;
      border: 1px solid #eee;
      border-radius: 4px;
      margin-bottom: 15px;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .post-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .post-card h3 {
      margin: 0 0 10px 0;
      color: #2c3e50;
    }
    
    .post-meta {
      color: #7f8c8d;
      font-size: 0.9em;
      margin-bottom: 10px;
    }
    
    .post-actions {
      display: flex;
      gap: 10px;
    }
    
    .edit-btn, .delete-btn {
      padding: 5px 10px;
      border-radius: 4px;
      text-decoration: none;
      font-size: 0.9em;
      transition: opacity 0.3s;
    }
    
    .edit-btn {
      background: #27ae60;
      color: white;
    }
    
    .edit-btn:hover {
      opacity: 0.9;
    }
    
    .delete-btn {
      background: #e74c3c;
      color: white;
    }
    
    .delete-btn:hover {
      opacity: 0.9;
    }
    
    .view-all {
      display: inline-block;
      margin-top: 10px;
      color: #3498db;
      text-decoration: none;
      font-weight: 500;
    }
    
    .view-all:hover {
      text-decoration: underline;
    }
    
    .admin-footer {
      text-align: center;
      padding: 20px;
      background: #2c3e50;
      color: white;
      margin-top: 30px;
      border-radius: 0 0 8px 8px;
    }
    
    .social-icons {
      margin-top: 10px;
    }
    
    .social-icons a {
      color: white;
      margin: 0 10px;
      font-size: 1.2em;
      transition: color 0.3s;
    }
    
    .social-icons a:hover {
      color: #3498db;
    }
    
    .no-items {
      padding: 15px;
      background: #f8f9fa;
      border-radius: 4px;
      color: #6c757d;
      text-align: center;
    }
  </style>
</head>
<body>

<header>
  <div class="header-content">
    <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
    <nav>
      <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
  </div>
</header>

<div class="container">
  <div class="admin-links">
    <a href="edit_scholarship.php"><i class="fas fa-plus"></i> Add Scholarship</a>
    <a href="edit_job.php"><i class="fas fa-plus"></i> Add Job</a>
    <a href="add_subject.php"><i class="fas fa-plus"></i> Add Subject</a>
    <a href="edit_past_paper.php"><i class="fas fa-plus"></i> Add Past Paper</a>
  </div>

  <div class="dashboard-section">
    <h1><i class="fas fa-briefcase"></i> Recent Job Postings</h1>
    
    <?php if(empty($jobs)): ?>
      <div class="no-items">
        <p>No job postings available. <a href="edit_job.php">Add a job posting</a></p>
      </div>
    <?php else: ?>
      <?php foreach($jobs as $job): ?>
        <div class="post-card">
          <h3><?= htmlspecialchars($job['title']) ?></h3>
          <div class="post-meta">
            Posted: <?= date('M d, Y', strtotime($job['posted_at'])) ?>
          </div>
          <div class="post-actions">
            <a href="edit_job.php?id=<?= $job['id'] ?>" class="edit-btn"><i class="fas fa-edit"></i> Edit</a>
            <a href="delete_job.php?id=<?= $job['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this job?')"><i class="fas fa-trash-alt"></i> Delete</a>
          </div>
        </div>
      <?php endforeach; ?>
      <a href="job.php" class="view-all">View all jobs →</a>
    <?php endif; ?>
  </div>

  <div class="dashboard-section">
    <h1><i class="fas fa-graduation-cap"></i> Recent Scholarship Opportunities</h1>
    
    <?php if(empty($scholarships)): ?>
      <div class="no-items">
        <p>No scholarships available. <a href="edit_scholarship.php">Add a scholarship</a></p>
      </div>
    <?php else: ?>
      <?php foreach($scholarships as $scholarship): ?>
        <div class="post-card">
          <h3><?= htmlspecialchars($scholarship['title']) ?></h3>
          <div class="post-meta">
            Posted: <?= date('M d, Y', strtotime($scholarship['posted_at'])) ?>
            <?php if (!empty($scholarship['deadline'])): ?>
              | Deadline: <?= date('M d, Y', strtotime($scholarship['deadline'])) ?>
            <?php endif; ?>
          </div>
          <div class="post-actions">
            <a href="edit_scholarship.php?id=<?= $scholarship['id'] ?>" class="edit-btn"><i class="fas fa-edit"></i> Edit</a>
            <a href="delete_scholarship.php?id=<?= $scholarship['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this scholarship?')"><i class="fas fa-trash-alt"></i> Delete</a>
          </div>
        </div>
      <?php endforeach; ?>
      <a href="scholarship.php" class="view-all">View all scholarships →</a>
    <?php endif; ?>
  </div>

  <div class="dashboard-section">
    <h1><i class="fas fa-book"></i> Recent Subjects</h1>
    
    <?php if(empty($subjects)): ?>
      <div class="no-items">
        <p>No subjects available. <a href="add_subject.php">Add a subject</a></p>
      </div>
    <?php else: ?>
      <?php foreach($subjects as $subject): ?>
        <div class="post-card">
          <h3><?= htmlspecialchars($subject['name']) ?></h3>
          <div class="post-actions">
            <a href="manage_notes.php?subject_id=<?= $subject['id'] ?>" class="edit-btn"><i class="fas fa-edit"></i> Manage Notes</a>
            <a href="delete_subject.php?id=<?= $subject['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this subject?')"><i class="fas fa-trash-alt"></i> Delete</a>
          </div>
        </div>
      <?php endforeach; ?>
      <a href="notes.php" class="view-all">View all subjects →</a>
    <?php endif; ?>
  </div>

  <div class="dashboard-section">
    <h1><i class="fas fa-file-alt"></i> Recent Past Papers</h1>
    
    <?php if(empty($past_papers)): ?>
      <div class="no-items">
        <p>No past papers available. <a href="edit_past_paper.php">Add a past paper</a></p>
      </div>
    <?php else: ?>
      <?php foreach($past_papers as $paper): ?>
        <div class="post-card">
          <h3><?= htmlspecialchars($paper['subject_name']) ?> - <?= htmlspecialchars($paper['title']) ?></h3>
          <div class="post-meta">
            Year: <?= $paper['year'] ?> | 
            Type: <?= $paper['paper_type'] ?> | 
            Posted: <?= date('M d, Y', strtotime($paper['posted_at'])) ?>
          </div>
          <div class="post-actions">
            <a href="edit_past_paper.php?id=<?= $paper['id'] ?>" class="edit-btn"><i class="fas fa-edit"></i> Edit</a>
            <a href="delete_past_paper.php?id=<?= $paper['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this past paper?')"><i class="fas fa-trash-alt"></i> Delete</a>
          </div>
        </div>
      <?php endforeach; ?>
      <a href="past_papers.php" class="view-all">View all past papers →</a>
    <?php endif; ?>
  </div>
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