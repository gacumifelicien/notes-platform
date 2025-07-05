<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$pageTitle = "Study Notes - Digital Notes Platform";
include 'header.php';

// Database connection
try {
    $db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all subjects with their notes count
    $subjects = $db->query("        
        SELECT s.*, COUNT(n.id) as notes_count 
        FROM subjects s 
        LEFT JOIN notes n ON s.id = n.subject_id 
        GROUP BY s.id 
        ORDER BY s.name
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Get notes for a specific subject if requested
    $selectedSubject = null;
    $subjectNotes = [];
    if (isset($_GET['subject_id'])) {
        $subjectId = (int)$_GET['subject_id'];

        // Get subject details
        $stmt = $db->prepare("SELECT * FROM subjects WHERE id = ?");
        $stmt->execute([$subjectId]);
        $selectedSubject = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($selectedSubject) {
            $pageTitle = htmlspecialchars($selectedSubject['name']) . " - Study Notes";

            // Get all notes for this subject
            $stmt = $db->prepare("
                SELECT *, CONCAT('admin/uploads/notes/', filename) as file_path 
                FROM notes 
                WHERE subject_id = ? 
                ORDER BY uploaded_at DESC
            ");
            $stmt->execute([$subjectId]);
            $subjectNotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Process file information
            foreach ($subjectNotes as &$note) {
                if (!empty($note['file_path'])) {
                    $fileAbsolutePath = $_SERVER['DOCUMENT_ROOT'] . '/notes-platform/' . $note['file_path'];
                    $note['file_exists'] = file_exists($fileAbsolutePath);
                    $fileExtension = strtolower(pathinfo($note['file_path'], PATHINFO_EXTENSION));
                    $note['is_viewable'] = in_array($fileExtension, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt']);
                }
            }
        }
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: notes.php");
    exit;
}

function formatFileSize($bytes) {
    if ($bytes == 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
?>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    padding-top: 60px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.85);
}
.modal-content {
    margin: auto;
    background: #fff;
    padding: 20px;
    width: 90%;
    height: 90%;
    max-width: 1000px;
    position: relative;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
}
.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    position: absolute;
    top: 10px;
    right: 20px;
    cursor: pointer;
}
.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
}
.file-viewer-container {
    flex-grow: 1;
    margin-top: 15px;
    overflow-y: auto;
}
.file-viewer {
    width: 100%;
    height: 100%;
    border: none;
}
.modal-actions {
    margin-top: 10px;
    text-align: right;
}
.note-preview-scroll {
    max-height: 150px;
    overflow-y: auto;
    white-space: pre-wrap;
    background: #f8f8f8;
    padding: 10px;
    border-radius: 6px;
    font-size: 14px;
    margin-bottom: 10px;
}
.note-card {
    transition: background 0.3s;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #007BFF;
    margin-bottom: 20px;
    background: #E6F0FF;
}
.note-card:hover {
    background: #ffe6cc;
    border-color: #ff6600;
}
</style>

<div class="container">
    <?php if ($selectedSubject): ?>
        <h2><?= htmlspecialchars($selectedSubject['name']) ?> Notes</h2>
        <?php if (empty($subjectNotes)): ?>
            <p>No notes found for this subject.</p>
        <?php else: ?>
            <div class="notes-grid">
                <?php foreach ($subjectNotes as $note): ?>
                    <div class="note-card">
                        <h4><?= htmlspecialchars($note['title']) ?></h4>
                        <?php if (!empty($note['content'])): ?>
                            <div class="note-preview-scroll">
                                <?= nl2br(htmlspecialchars($note['content'])) ?>
                            </div>
                            <?php if (strlen($note['content']) > 200): ?>
                                <a href="#" onclick="openNoteViewer(`<?= htmlspecialchars($note['title']) ?>`, `<?= nl2br(htmlspecialchars($note['content'])) ?>`); return false;" class="btn-view">🔍 View Fullscreen</a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class="note-meta">
                            <span><?= date('M j, Y', strtotime($note['uploaded_at'])) ?></span>
                            <?php if (!empty($note['file_path']) && $note['file_exists']): ?>
                                <div>
                                    <a href="download.php?id=<?= $note['id'] ?>" class="file-badge">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <?php if ($note['is_viewable']): ?>
                                        <a href="#" class="view-file-btn" onclick="openFileViewer('<?= htmlspecialchars($note['file_path']) ?>', '<?= htmlspecialchars($note['title']) ?>')">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <h2>Select a Subject</h2>
        <?php if (empty($subjects)): ?>
            <p>No subjects available.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($subjects as $subject): ?>
                    <li><a href="?subject_id=<?= $subject['id'] ?>"><?= htmlspecialchars($subject['name']) ?> (<?= $subject['notes_count'] ?>)</a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- File Viewer Modal -->
<div id="fileViewerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeFileViewer()">&times;</span>
        <h2 id="fileViewerTitle"></h2>
        <div class="file-viewer-container">
            <iframe id="fileViewerFrame" class="file-viewer" frameborder="0"></iframe>
        </div>
        <div class="modal-actions">
            <a href="#" id="fileDownloadLink" class="file-badge">
                <i class="fas fa-download"></i> Download File
            </a>
        </div>
    </div>
</div>

<!-- Note Content Fullscreen Modal -->
<div id="noteViewerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeNoteViewer()">&times;</span>
        <h2 id="noteViewerTitle"></h2>
        <div class="file-viewer-container">
            <div id="noteViewerContent" style="white-space: pre-wrap; font-size: 16px; padding: 10px;"></div>
        </div>
    </div>
</div>

<script>
function openFileViewer(filePath, title) {
    document.getElementById('fileViewerTitle').textContent = title;
    document.getElementById('fileViewerFrame').src = filePath;
    document.getElementById('fileDownloadLink').href = filePath;
    document.getElementById('fileViewerModal').style.display = "block";
    document.body.style.overflow = 'hidden';
}

function closeFileViewer() {
    document.getElementById('fileViewerModal').style.display = "none";
    document.getElementById('fileViewerFrame').src = "";
    document.body.style.overflow = 'auto';
}

function openNoteViewer(title, content) {
    document.getElementById('noteViewerTitle').textContent = title;
    document.getElementById('noteViewerContent').innerHTML = content;
    document.getElementById('noteViewerModal').style.display = "block";
    document.body.style.overflow = 'hidden';
}

function closeNoteViewer() {
    document.getElementById('noteViewerModal').style.display = "none";
    document.body.style.overflow = 'auto';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeNoteViewer();
        closeFileViewer();
    }
});
</script>

<?php include 'footer.php'; ?>
