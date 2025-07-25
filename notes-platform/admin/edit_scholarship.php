<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: login.php");

$db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;
$scholarship = null;

if ($id) {
    $stmt = $db->prepare("SELECT * FROM scholarships WHERE id = ?");
    $stmt->execute([$id]);
    $scholarship = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $application_link = trim($_POST['application_link']);
    $eligibility = trim($_POST['eligibility'] ?? '');
    $deadline = trim($_POST['deadline'] ?? null);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $current_image = $scholarship['featured_image'] ?? null;
    
    // Handle remove image checkbox
    if (isset($_POST['remove_image']) && $current_image) {
        if (file_exists($current_image)) {
            unlink($current_image);
        }
        $current_image = null;
    }

    // Handle file upload
    $uploaded_image = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/scholarships/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_ext, $allowed_extensions)) {
            $file_name = uniqid('scholarship_') . '.' . $file_ext;
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target_file)) {
                $uploaded_image = $target_file;
                // Delete old image if it exists
                if ($current_image && file_exists($current_image)) {
                    unlink($current_image);
                }
            }
        }
    }

    $featured_image = $uploaded_image ?? $current_image;

    if ($id) {
        $stmt = $db->prepare("UPDATE scholarships SET title=?, content=?, application_link=?, eligibility=?, deadline=?, featured_image=?, is_active=? WHERE id=?");
        $stmt->execute([$title, $content, $application_link, $eligibility, $deadline, $featured_image, $is_active, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO scholarships (title, content, application_link, eligibility, deadline, featured_image, is_active, posted_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$title, $content, $application_link, $eligibility, $deadline, $featured_image, $is_active]);
    }

    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?= $id ? 'Edit' : 'Add' ?> Scholarship</title>
    <link rel="stylesheet" href="css/admin_style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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
    <h1><?= $id ? 'Edit' : 'Add' ?> Scholarship</h1>

    <form method="POST" action="" enctype="multipart/form-data">
        <label>Scholarship Title:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($scholarship['title'] ?? '') ?>" required style="width:400px;"><br><br>

        <label>Content / Description:</label><br>
        <textarea name="content" rows="8" style="width:400px;"><?= htmlspecialchars($scholarship['content'] ?? '') ?></textarea><br><br>

        <label>Eligibility Criteria:</label><br>
        <textarea name="eligibility" rows="4" style="width:400px;"><?= htmlspecialchars($scholarship['eligibility'] ?? '') ?></textarea><br><br>

        <label>Application Deadline:</label><br>
        <input type="date" name="deadline" value="<?= htmlspecialchars($scholarship['deadline'] ?? '') ?>" style="width:400px;"><br><br>

        <label>Application Link:</label><br>
        <input type="url" name="application_link" value="<?= htmlspecialchars($scholarship['application_link'] ?? '') ?>" style="width:400px;"><br><br>

        <label>Featured Image:</label><br>
        <?php if (!empty($scholarship['featured_image']) && file_exists($scholarship['featured_image'])): ?>
            <img src="<?= htmlspecialchars($scholarship['featured_image']) ?>" style="max-width: 200px; display: block; margin-bottom: 10px;">
            <label><input type="checkbox" name="remove_image"> Remove current image</label><br>
        <?php endif; ?>
        <input type="file" name="featured_image" accept="image/*"><br>
        <small>Accepted formats: JPG, PNG, GIF. Max size: 2MB</small><br><br>

        <label>
            <input type="checkbox" name="is_active" <?= !isset($scholarship['is_active']) || $scholarship['is_active'] ? 'checked' : '' ?>> Active
        </label><br><br>

        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save</button>
        <a href="dashboard.php" class="btn-cancel"><i class="fas fa-times"></i> Cancel</a>
    </form>
</div>

<style>
    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    header {
        background: #2c3e50;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    nav a {
        color: white;
        text-decoration: none;
        margin-left: 15px;
    }
    
    input[type="text"], input[type="url"], input[type="date"], textarea {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    textarea {
        resize: vertical;
    }
    
    .btn-save {
        background: #27ae60;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .btn-cancel {
        background: #e74c3c;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        margin-left: 10px;
    }
    
    small {
        color: #7f8c8d;
        font-size: 0.8em;
    }
    
    img {
        max-height: 200px;
        object-fit: contain;
    }
</style>

</body>
</html>