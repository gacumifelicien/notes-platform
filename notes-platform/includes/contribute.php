<?php
$pageTitle = "Contribute - Digital Notes Platform";
include 'header.php';
?>

<div class="container">
    <h1>Contribute to Our Platform</h1>
    
    <p>We welcome contributions from educators, students, and knowledge enthusiasts. Here's how you can help:</p>
    
    <div class="contribute-options">
        <div class="contribute-card">
            <i class="fas fa-upload fa-3x"></i>
            <h2>Upload Notes</h2>
            <p>Share your study materials with students worldwide. Your notes could help someone ace their exams!</p>
            <a href="upload.php" class="btn">Start Uploading</a>
        </div>
        
        <div class="contribute-card">
            <i class="fas fa-edit fa-3x"></i>
            <h2>Improve Content</h2>
            <p>Help us improve existing notes by suggesting corrections or adding more details.</p>
            <a href="contact.php" class="btn">Suggest Edits</a>
        </div>
        
        <div class="contribute-card">
            <i class="fas fa-language fa-3x"></i>
            <h2>Translate</h2>
            <p>Help translate notes into different languages to make them accessible to more students.</p>
            <a href="contact.php" class="btn">Volunteer to Translate</a>
        </div>
    </div>
    
    <div class="contribute-guidelines">
        <h2>Contribution Guidelines</h2>
        <ul>
            <li>Only upload content you have the rights to share</li>
            <li>Ensure your notes are accurate and well-organized</li>
            <li>Use clear file names and include relevant descriptions</li>
            <li>No spam or promotional content</li>
            <li>Respect copyright laws</li>
        </ul>
        
        <p>By contributing, you agree to our <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a>.</p>
    </div>
</div>

<?php include 'footer.php'; ?>