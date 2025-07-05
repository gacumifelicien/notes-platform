<?php
session_start();
$pageTitle = "Scholarships";
include 'header.php';

// Database connection
try {
    $db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get specific scholarship if ID provided
    $selectedScholarship = null;
    if (isset($_GET['scholarship_id']) && is_numeric($_GET['scholarship_id'])) {
        $stmt = $db->prepare("SELECT * FROM scholarships WHERE id = ?");
        $stmt->execute([$_GET['scholarship_id']]);
        $selectedScholarship = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($selectedScholarship) {
            $pageTitle = htmlspecialchars($selectedScholarship['title']) . " - Scholarships";
            
            // Proper image path handling for XAMPP
            $imageDir = 'admin/uploads/scholarships/';
            
            if (!empty($selectedScholarship['featured_image'])) {
                // Clean filename by removing any path segments
                $cleanFilename = basename($selectedScholarship['featured_image']);
                $selectedScholarship['image_url'] = $imageDir . $cleanFilename;
            }
        }
    }

    // Get all active scholarships for listing
    $scholarships = $db->query("
        SELECT id, title, featured_image, SUBSTRING(content, 1, 150) as excerpt, 
               posted_at, application_link 
        FROM scholarships 
        WHERE is_active = 1 
        ORDER BY posted_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #3498db;
            text-decoration: none;
            font-size: 16px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .scholarship-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;

            /* Added border and smooth transition */
            border: 2px solid transparent;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .scholarship-container:hover {
            border-color: #3498db;  /* blue border on hover */
            box-shadow: 0 4px 20px rgba(52, 152, 219, 0.4); /* stronger shadow */
        }
        
        .featured-image-container {
            width: 100%;
            max-height: 400px;
            overflow: hidden;
            border-radius: 8px;
            margin-bottom: 25px;
            background-color: #f5f5f5;
            text-align: center;
        }
        
        .featured-image {
            max-width: 100%;
            max-height: 400px;
            height: auto;
            display: inline-block;
        }
        
        .scholarship-title {
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 28px;
            transition: color 0.3s ease;
        }

        .scholarship-title:hover {
            color: #2980b9;
            text-decoration: underline;
        }
        
        .meta-info {
            color: #7f8c8d;
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        .deadline {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .scholarship-content {
            line-height: 1.8;
            margin-bottom: 25px;
            font-size: 16px;
        }
        
        .eligibility-section {
            margin-top: 30px;
        }
        
        .btn-apply {
            display: inline-block;
            padding: 12px 25px;
            background: #3498db;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
            transition: background 0.3s, box-shadow 0.3s;
            font-size: 16px;
        }
        
        .btn-apply:hover {
            background: #2980b9;
            box-shadow: 0 4px 8px rgba(41, 128, 185, 0.5);
        }
        
        .image-placeholder {
            padding: 40px;
            color: #999;
            font-size: 16px;
            text-align: center;
        }
        
        .image-placeholder i {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }
        
        /* Also add hover to links inside scholarship listing */
        .scholarship-container h2 a {
            color: #2c3e50;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .scholarship-container h2 a:hover {
            color: #2980b9;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($selectedScholarship): ?>
            <a href="scholarship.php" class="back-link">&larr; Back to all scholarships</a>
            
            <div class="scholarship-container">
                <div class="featured-image-container">
                    <?php if (!empty($selectedScholarship['image_url'])): ?>
                        <img src="<?= $selectedScholarship['image_url'] ?>" 
                             alt="<?= htmlspecialchars($selectedScholarship['title']) ?>" 
                             class="featured-image" />
                    <?php else: ?>
                        <div class="image-placeholder">
                            <i class="fas fa-image"></i>
                            <p>No featured image available</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <h1 class="scholarship-title"><?= htmlspecialchars($selectedScholarship['title']) ?></h1>
                
                <div class="meta-info">
                    <strong>Posted:</strong> <?= date('F j, Y', strtotime($selectedScholarship['posted_at'])) ?>
                    <?php if (!empty($selectedScholarship['deadline'])): ?>
                        | <span class="deadline"><strong>Deadline:</strong> <?= date('F j, Y', strtotime($selectedScholarship['deadline'])) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="scholarship-content">
                    <?= nl2br(htmlspecialchars($selectedScholarship['content'])) ?>
                </div>
                
                <?php if (!empty($selectedScholarship['eligibility'])): ?>
                    <div class="eligibility-section">
                        <h3>Eligibility Criteria</h3>
                        <div class="scholarship-content">
                            <?= nl2br(htmlspecialchars($selectedScholarship['eligibility'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($selectedScholarship['application_link'])): ?>
                    <a href="<?= htmlspecialchars($selectedScholarship['application_link']) ?>" 
                       target="_blank" 
                       class="btn-apply">
                        <i class="fas fa-external-link-alt"></i> Apply Now
                    </a>
                <?php endif; ?>
            </div>
            
        <?php else: ?>
            <h1>Available Scholarships</h1>
            
            <?php if (empty($scholarships)): ?>
                <div class="scholarship-container">
                    <p>No scholarships available at this time.</p>
                </div>
            <?php else: ?>
                <?php foreach ($scholarships as $scholarship): ?>
                    <div class="scholarship-container">
                        <h2>
                            <a href="scholarship.php?scholarship_id=<?= $scholarship['id'] ?>">
                                <?= htmlspecialchars($scholarship['title']) ?>
                            </a>
                        </h2>
                        <p class="meta-info">Posted: <?= date('F j, Y', strtotime($scholarship['posted_at'])) ?></p>
                        <p><?= htmlspecialchars($scholarship['excerpt']) ?>...</p>
                        <a href="scholarship.php?scholarship_id=<?= $scholarship['id'] ?>" class="btn-apply">
                            View Details
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
