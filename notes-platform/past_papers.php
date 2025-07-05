<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$pageTitle = "Past Papers - Digital Notes Platform";
include 'header.php';

$fileDir = 'admin/uploads/past_papers/';

try {
    $db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $selectedPaper = null;
    if (isset($_GET['paper_id']) && is_numeric($_GET['paper_id'])) {
        $stmt = $db->prepare("SELECT pp.*, s.name as subject_name FROM past_papers pp JOIN subjects s ON pp.subject_id = s.id WHERE pp.id = ?");
        $stmt->execute([$_GET['paper_id']]);
        $selectedPaper = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($selectedPaper && !empty($selectedPaper['file_path'])) {
            $cleanFilename = basename($selectedPaper['file_path']);
            $selectedPaper['file_url'] = $fileDir . $cleanFilename;
            $selectedPaper['file_exists'] = file_exists($_SERVER['DOCUMENT_ROOT'] . '/notes-platform/' . $selectedPaper['file_url']);
            $ext = strtolower(pathinfo($cleanFilename, PATHINFO_EXTENSION));
            $selectedPaper['is_viewable'] = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif']);
        }
    }

    $papers = $db->query("SELECT pp.id, pp.title, pp.year, pp.paper_type, s.name as subject_name, pp.posted_at FROM past_papers pp JOIN subjects s ON pp.subject_id = s.id WHERE pp.is_active = 1 ORDER BY posted_at DESC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<style>
.paper-container {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid #28a745;
    background-color: #e6ffe6;
    transition: background 0.3s;
}

.paper-container:hover {
    background-color: #ffcccc;
    border-color: #cc0000;
}

.viewer-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.9);
    z-index: 10000;
    justify-content: center;
    align-items: center;
}

.viewer-container {
    width: 95vw;
    height: 95vh;
    background: #fff;
    border-radius: 8px;
    overflow: auto;
}

.close-viewer {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 32px;
    color: #fff;
    background: transparent;
    border: none;
    cursor: pointer;
}
</style>

<div class="container">
    <?php if ($selectedPaper): ?>
        <a href="past_papers.php" class="back-link">&larr; Back to all past papers</a>

        <div class="paper-container">
            <h1 class="paper-title"><?= htmlspecialchars($selectedPaper['title']) ?></h1>
            <div class="meta-info">
                <span><i class="fas fa-book"></i> Subject: <?= htmlspecialchars($selectedPaper['subject_name']) ?></span>
                <span><i class="fas fa-calendar"></i> Posted: <?= date('F j, Y', strtotime($selectedPaper['posted_at'])) ?></span>
                <span><i class="fas fa-calendar-alt"></i> Year: <?= $selectedPaper['year'] ?></span>
                <span><i class="fas fa-file-alt"></i> Type: <?= $selectedPaper['paper_type'] ?></span>
            </div>

            <?php if (!empty($selectedPaper['description'])): ?>
                <p><?= nl2br(htmlspecialchars($selectedPaper['description'])) ?></p>
            <?php endif; ?>

            <?php if (!empty($selectedPaper['file_url']) && $selectedPaper['file_exists']): ?>
                <?php if ($selectedPaper['is_viewable']): ?>
                    <div class="file-preview-container" style="max-width: 100%; height: 600px; margin: 20px 0;">
                        <?php if (strtolower(pathinfo($selectedPaper['file_url'], PATHINFO_EXTENSION)) === 'pdf'): ?>
                            <iframe src="<?= htmlspecialchars($selectedPaper['file_url']) ?>" class="pdf-preview" style="width:100%; height:100%; border:none;"></iframe>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($selectedPaper['file_url']) ?>" alt="Preview" class="file-preview" style="max-width:100%; height:auto;">
                        <?php endif; ?>
                    </div>
                    <div class="action-buttons">
                        <a href="<?= htmlspecialchars($selectedPaper['file_url']) ?>" class="btn-download" download>
                            <i class="fas fa-download"></i> Download
                        </a>
                        <a href="#" class="btn-view" onclick="openFullscreen('<?= htmlspecialchars($selectedPaper['file_url']) ?>'); return false;">
                            <i class="fas fa-expand"></i> View Fullscreen
                        </a>
                    </div>
                <?php else: ?>
                    <p>This file type cannot be previewed. <a href="<?= htmlspecialchars($selectedPaper['file_url']) ?>" download>Download here</a>.</p>
                <?php endif; ?>
            <?php else: ?>
                <p>No file found.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <h1>Available Past Papers</h1>
        <?php foreach ($papers as $paper): ?>
            <div class="paper-container">
                <h2><a href="past_papers.php?paper_id=<?= $paper['id'] ?>"><?= htmlspecialchars($paper['subject_name']) ?> - <?= htmlspecialchars($paper['title']) ?></a></h2>
                <div class="meta-info">
                    <span><i class="fas fa-calendar-alt"></i> Year: <?= $paper['year'] ?></span>
                    <span><i class="fas fa-file-alt"></i> Type: <?= $paper['paper_type'] ?></span>
                    <span><i class="fas fa-calendar"></i> Posted: <?= date('F j, Y', strtotime($paper['posted_at'])) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="viewer-overlay" id="viewerOverlay">
    <button class="close-viewer" onclick="closeFullscreen()">&times;</button>
    <div class="viewer-container">
        <iframe id="fullscreenViewer" style="width:100%; height:100%; border:none;"></iframe>
    </div>
</div>

<script>
function openFullscreen(filePath) {
    const viewer = document.getElementById('fullscreenViewer');
    const overlay = document.getElementById('viewerOverlay');
    viewer.src = filePath;
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeFullscreen() {
    const viewer = document.getElementById('fullscreenViewer');
    const overlay = document.getElementById('viewerOverlay');
    overlay.style.display = 'none';
    viewer.src = '';
    document.body.style.overflow = 'auto';
}
</script>

<?php include 'footer.php'; ?>
