<?php
$pageTitle = "Job Opportunities - Digital Notes Platform";
include 'header.php';

$db = new PDO("mysql:host=localhost;dbname=notes_platform","root","");
$jobs = $db->query("SELECT * FROM jobs WHERE is_active = TRUE ORDER BY posted_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Job Opportunities</h1>
    <p>Browse through available job opportunities below:</p>
    
    <?php if(empty($jobs)): ?>
        <div class="empty-state">
            <i class="fas fa-briefcase fa-3x"></i>
            <h3>No job opportunities available at the moment</h3>
            <p>Please check back later for new job postings.</p>
        </div>
    <?php else: ?>
        <div class="job-list">
            <?php foreach($jobs as $job): ?>
                <div class="job-card">
                    <div class="job-header">
                        <h3><?= htmlspecialchars($job['title']) ?></h3>
                        <span class="date"><i class="fas fa-calendar-alt"></i> Posted: <?= date('M j, Y', strtotime($job['posted_at'])) ?></span>
                    </div>
                    
                    <div class="job-content">
                        <?= nl2br(htmlspecialchars($job['content'])) ?>
                    </div>
                    
                    <?php if($job['application_link']): ?>
                        <div class="job-actions">
                            <a href="<?= htmlspecialchars($job['application_link']) ?>" target="_blank" class="btn btn-apply">
                                <i class="fas fa-external-link-alt"></i> Apply Now
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>