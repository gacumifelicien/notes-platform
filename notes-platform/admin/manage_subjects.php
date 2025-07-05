<?php
require_once __DIR__ . '/../includes/config_file.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_subject'])) {
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        
        if (!empty($name)) {
            $stmt = $pdo->prepare("INSERT INTO subjects (name, description) VALUES (?, ?)");
            if ($stmt->execute([$name, $description])) {
                $message = 'Subject added successfully!';
            } else {
                $message = 'Error adding subject.';
            }
        } else {
            $message = 'Subject name is required.';
        }
    }
    
    if (isset($_POST['edit_subject'])) {
        $id = (int)$_POST['id'];
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        
        if (!empty($name)) {
            $stmt = $pdo->prepare("UPDATE subjects SET name = ?, description = ? WHERE id = ?");
            if ($stmt->execute([$name, $description, $id])) {
                $message = 'Subject updated successfully!';
            } else {
                $message = 'Error updating subject.';
            }
        } else {
            $message = 'Subject name is required.';
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = 'Subject deleted successfully!';
    } else {
        $message = 'Error deleting subject.';
    }
}

// Get all subjects
$stmt = $pdo->query("SELECT s.*, COUNT(n.id) as note_count FROM subjects s LEFT JOIN notes n ON s.id = n.subject_id GROUP BY s.id ORDER BY s.name");
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get subject for editing
$edit_subject = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_subject = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h1><i class="fas fa-folder"></i> Manage Subjects</h1>
            <nav class="admin-nav">
                <a href="admin_dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="manage_subjects.php" class="nav-link active">
                    <i class="fas fa-folder"></i> Subjects
                </a>
                <a href="manage_notes.php" class="nav-link">
                    <i class="fas fa-sticky-note"></i> Notes
                </a>
                <a href="../index.php" class="nav-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Site
                </a>
                <a href="logout.php" class="nav-link logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>
    </header>

    <main class="admin-main">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="admin-content">
                <div class="form-section">
                    <h2>
                        <i class="fas fa-plus"></i>
                        <?php echo $edit_subject ? 'Edit Subject' : 'Add New Subject'; ?>
                    </h2>
                    
                    <form method="POST" action="" class="admin-form">
                        <?php if ($edit_subject): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_subject['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="name">Subject Name *</label>
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo $edit_subject ? htmlspecialchars($edit_subject['name']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3"><?php echo $edit_subject ? htmlspecialchars($edit_subject['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <?php if ($edit_subject): ?>
                                <button type="submit" name="edit_subject" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Subject
                                </button>
                                <a href="manage_subjects.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            <?php else: ?>
                                <button type="submit" name="add_subject" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Subject
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="table-section">
                    <h2><i class="fas fa-list"></i> All Subjects</h2>
                    
                    <?php if (empty($subjects)): ?>
                        <p class="no-data">No subjects available.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Notes Count</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subjects as $subject): ?>
                                        <tr>
                                            <td><?php echo $subject['id']; ?></td>
                                            <td><?php echo htmlspecialchars($subject['name']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($subject['description'], 0, 50)) . (strlen($subject['description']) > 50 ? '...' : ''); ?></td>
                                            <td>
                                                <span class="badge"><?php echo $subject['note_count']; ?></span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($subject['created_at'])); ?></td>
                                            <td class="actions">
                                                <a href="?edit=<?php echo $subject['id']; ?>" class="btn btn-sm btn-edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="manage_notes.php?subject=<?php echo $subject['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="?delete=<?php echo $subject['id']; ?>" 
                                                   class="btn btn-sm btn-delete" 
                                                   onclick="return confirm('Are you sure? This will delete all notes in this subject!')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="admin-footer">
        <div class="container">
            <p>Digital Notes Platform &copy; <?php echo date('Y'); ?></p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Help Center</a>
            </div>
        </div>
    </footer>

    <script src="../assets/js/admin_script.js"></script>
</body>
</html>