<?php
$db = new PDO("mysql:host=localhost;dbname=notes_platform","root","");

$sid = intval($_GET['subject_id'] ?? 0);

$subjectStmt = $db->prepare("SELECT * FROM subjects WHERE id = ?");
$subjectStmt->execute([$sid]);
$subject = $subjectStmt->fetch(PDO::FETCH_ASSOC);

if (!$subject) {
    die("Subject not found.");
}

$notesStmt = $db->prepare("SELECT * FROM notes WHERE subject_id = ? ORDER BY uploaded_at DESC");
$notesStmt->execute([$sid]);
$notes = $notesStmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = htmlspecialchars($subject['name']) . " - Digital Notes Platform";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
</head>
<body>

<header class="site-header">
    <div class="header-container">
        <div class="logo">
            <a href="index.php">
                <img src="icons/logo.png" alt="Digital Notes Logo">
                <span>Digital Notes Platform</span>
            </a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                <li><a href="donate.php"><i class="fas fa-heart"></i> Donate</a></li>
            </ul>
        </nav>
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>

<main class="main-content">
    <div class="container">
        <div class="subject-header">
            <h1 class="subject-title"><?= htmlspecialchars($subject['name']) ?></h1>
            
            <div class="subject-meta">
                <span class="notes-count">
                    <i class="fas fa-book"></i> <?= count($notes) ?> notes available
                </span>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="upload.php?subject_id=<?= $sid ?>" class="btn btn-secondary">
                        <i class="fas fa-plus"></i> Add Notes
                    </a>
                <?php else: ?>
                    <a href="login.php?redirect=view.php?subject_id=<?= $sid ?>" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Login to contribute
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="notes-list">
            <?php if (empty($notes)): ?>
                <div class="empty-state">
                    <i class="fas fa-book-open fa-3x"></i>
                    <h3>No notes available yet</h3>
                    <p>Be the first to contribute notes for this subject!</p>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="upload.php?subject_id=<?= $sid ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Upload Notes
                        </a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Register to contribute
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($notes as $note): ?>
                    <div class="note-card">
                        <div class="note-header">
                            <h3 class="note-title"><?= htmlspecialchars($note['title']) ?></h3>
                            <div class="note-meta">
                                <span class="note-date">
                                    <i class="fas fa-calendar-alt"></i> 
                                    <?= date('M j, Y', strtotime($note['uploaded_at'])) ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if ($note['content']): ?>
                            <div class="note-content">
                                <?= nl2br(htmlspecialchars($note['content'])) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($note['filename']): ?>
                            <div class="note-file">
                                <div class="file-info">
                                    <i class="fas fa-file-alt"></i>
                                    <div class="file-details">
                                        <span class="file-name">
                                            <?= htmlspecialchars($note['filename']) ?>
                                        </span>
                                        <?php if (file_exists('uploads/' . $note['filename'])): ?>
                                            <span class="file-meta">
                                                <?= strtoupper(pathinfo($note['filename'], PATHINFO_EXTENSION)) ?> • 
                                                <?= formatFileSize(filesize('uploads/' . $note['filename'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <a href="uploads/<?= rawurlencode($note['filename']) ?>" download class="btn btn-primary download-btn">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="privacy.php">Privacy Policy</a></li>
                <li><a href="terms.php">Terms of Service</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Resources</h3>
            <ul>
                <li><a href="blog.php">Blog</a></li>
                <li><a href="tutorials.php">Tutorials</a></li>
                <li><a href="faq.php">FAQ</a></li>
                <li><a href="contribute.php">Contribute</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Connect With Us</h3>
            <div class="social-links">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
            <div class="newsletter">
                <p>Subscribe to our newsletter</p>
                <form action="subscribe.php" method="POST">
                    <input type="email" name="email" placeholder="Your email address" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Digital Notes Platform. All rights reserved.</p>
        <p>Made with <i class="fas fa-heart" style="color: #e74c3c;"></i> for students worldwide</p>
    </div>
</footer>

<script src="js/main.js"></script>
</body>
</html>

<?php
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return '1 byte';
    } else {
        return '0 bytes';
    }
}
?>