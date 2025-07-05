<?php
session_start();
$pageTitle = "Job Opportunities - Digital Notes Platform";
include 'header.php';

// Database connection with error handling
try {
    $db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get specific job if ID provided
    $selectedJob = null;
    if (isset($_GET['job_id']) && is_numeric($_GET['job_id'])) {
        $stmt = $db->prepare("SELECT * FROM jobs WHERE id = ?");
        $stmt->execute([$_GET['job_id']]);
        $selectedJob = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($selectedJob) {
            $pageTitle = htmlspecialchars($selectedJob['title']) . " - Job Opportunity";
            
            // Proper image path handling for XAMPP
            $imageDir = 'admin/uploads/jobs/';
            
            if (!empty($selectedJob['featured_image'])) {
                $cleanFilename = basename($selectedJob['featured_image']);
                $selectedJob['image_url'] = $imageDir . $cleanFilename;
            }
        }
    }

    // Get all active jobs for listing - using 'content' instead of 'description'
    $jobs = $db->query("SELECT id, title, featured_image, SUBSTRING(content, 1, 150) as excerpt, posted_at, application_link FROM jobs ORDER BY posted_at DESC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .job-container, .job-listing {
            border: 2px solid #28a745;
            border-radius: 8px;
            background-color: #e6ffe6;
            padding: 15px;
            margin-bottom: 20px;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .job-container:hover, .job-listing:hover {
            background-color: #ffe6e6;
            border-color: #cc0000;
        }

        .btn-apply {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .btn-apply:hover {
            background-color: #0056b3;
        }

        .image-placeholder {
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .featured-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($selectedJob): ?>
            <a href="job.php" class="back-link">&larr; Back to all jobs</a>
            
            <div class="job-container">
                <div class="featured-image-container">
                    <?php if (!empty($selectedJob['image_url'])): ?>
                        <img src="<?= $selectedJob['image_url'] ?>" alt="<?= htmlspecialchars($selectedJob['title']) ?>" class="featured-image">
                    <?php else: ?>
                        <div class="image-placeholder">
                            <i class="fas fa-briefcase"></i>
                            <p>No featured image available</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <h1 class="job-title"><?= htmlspecialchars($selectedJob['title']) ?></h1>
                
                <div class="meta-info">
                    <strong>Posted:</strong> <?= date('F j, Y', strtotime($selectedJob['posted_at'])) ?>
                    <?php if (!empty($selectedJob['deadline'])): ?>
                        | <span class="deadline"><strong>Deadline:</strong> <?= date('F j, Y', strtotime($selectedJob['deadline'])) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="job-content">
                    <?= nl2br(htmlspecialchars($selectedJob['content'])) ?>
                </div>
                
                <?php if (!empty($selectedJob['requirements'])): ?>
                    <div class="requirements-section">
                        <h3>Requirements</h3>
                        <div class="job-content">
                            <?= nl2br(htmlspecialchars($selectedJob['requirements'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($selectedJob['application_link'])): ?>
                    <a href="<?= htmlspecialchars($selectedJob['application_link']) ?>" target="_blank" class="btn-apply">
                        <i class="fas fa-external-link-alt"></i> Apply Now
                    </a>
                <?php endif; ?>
            </div>
            
        <?php else: ?>
            <h1>Available Job Opportunities</h1>
            
            <?php if (empty($jobs)): ?>
                <div class="job-container">
                    <div class="image-placeholder">
                        <i class="fas fa-briefcase fa-3x"></i>
                        <h3>No job opportunities available at the moment</h3>
                        <p>Please check back later for new job postings.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="job-listings">
                    <?php foreach ($jobs as $job): ?>
                        <div class="job-listing">
                            <h2 class="job-listing-title">
                                <a href="job.php?job_id=<?= $job['id'] ?>"><?= htmlspecialchars($job['title']) ?></a>
                            </h2>
                            <div class="meta-info">
                                Posted: <?= date('F j, Y', strtotime($job['posted_at'])) ?>
                            </div>
                            <p class="job-listing-excerpt">
                                <?= htmlspecialchars($job['excerpt']) ?>...
                            </p>
                            <a href="job.php?job_id=<?= $job['id'] ?>" class="btn-apply">View Details</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>