<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

userOnly();

// Get subject by slug
$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';
$subject = getSubjectBySlug($slug);

if (!$subject) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

$pageTitle = htmlspecialchars($subject['name']) . " - Digital Notes Platform";
include 'header.php';

// Get notes for this subject
$notes = getNotesBySubject($subject['id']);
?>

<div class="container">
  <h1 class="subject-title"><?php echo htmlspecialchars($subject['name']); ?></h1>
  <p class="subject-description"><?php echo htmlspecialchars($subject['description']); ?></p>

  <div class="notes-list">
      <?php if (empty($notes)): ?>
          <p>No notes available for this subject yet.</p>
      <?php else: ?>
          <?php foreach ($notes as $note): ?>
              <div class="note-card">
                  <h3><?php echo htmlspecialchars($note['title']); ?></h3>
                  <?php if ($note['content']): ?>
                      <div class="note-content"><?php echo nl2br(htmlspecialchars($note['content'])); ?></div>
                  <?php endif; ?>
                  
                  <?php if ($note['file_path']): ?>
                      <div class="note-file">
                          <i class="fas fa-file-alt"></i>
                          <a href="<?php echo BASE_URL . '/notes/' . rawurlencode(basename($note['file_path'])); ?>" download>
                              Download File (<?php echo strtoupper(htmlspecialchars($note['file_type'])); ?>, <?php echo formatFileSize($note['file_size']); ?>)
                          </a>
                      </div>
                  <?php endif; ?>
              </div>
          <?php endforeach; ?>
      <?php endif; ?>
  </div>
</div>

<?php include 'footer.php'; ?>