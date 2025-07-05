<?php
$pageTitle = "Digital Notes Platform - Home";
include 'header.php';

$db = new PDO("mysql:host=localhost;dbname=notes_platform", "root", "");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle search
$search = trim($_GET['search'] ?? '');

// Get featured content
$featuredSubjects = $db->query("SELECT * FROM subjects ORDER BY RAND() LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
$recentJobs = $db->query("SELECT * FROM jobs ORDER BY posted_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
$recentScholarships = $db->query("SELECT * FROM scholarships ORDER BY posted_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
$recentPapers = $db->query("SELECT pp.*, s.name as subject_name FROM past_papers pp JOIN subjects s ON pp.subject_id = s.id ORDER BY posted_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);

// Get counts for Quick Figures section
$subjectCount = $db->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
$notesCount = $db->query("SELECT COUNT(*) FROM notes")->fetchColumn();
$jobsCount = $db->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
$scholarshipsCount = $db->query("SELECT COUNT(*) FROM scholarships")->fetchColumn();
$papersCount = $db->query("SELECT COUNT(*) FROM past_papers")->fetchColumn();
?>

<style>
    .hero {
        background: linear-gradient(135deg, #3498db, #2c3e50);
        color: white;
        padding: 4rem 2rem;
        text-align: center;
        margin-bottom: 2rem;
        border-radius: 8px;
    }
    
    .hero h1 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .hero p {
        font-size: 1.2rem;
        max-width: 800px;
        margin: 0 auto 2rem;
    }
    
    .search-form {
        max-width: 600px;
        margin: 0 auto 3rem;
        display: flex;
    }
    
    .search-form input {
        flex: 1;
        padding: 12px 15px;
        border: 2px solid #3498db;
        border-radius: 4px 0 0 4px;
        font-size: 1rem;
    }
    
    .search-form button {
        padding: 0 20px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 0 4px 4px 0;
        cursor: pointer;
        font-size: 1rem;
    }
    
    .section-title {
        color: #2c3e50;
        margin: 3rem 0 1.5rem;
        padding-bottom: 10px;
        border-bottom: 2px solid #eee;
    }
    
    .content-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }
    
    .card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .card-image {
        height: 180px;
        background: #ecf0f1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .card-image i {
        font-size: 4rem;
        color: #bdc3c7;
    }
    
    .subject-card .card-image i { color: #3498db; }
    .job-card .card-image i { color: #e67e22; }
    .scholarship-card .card-image i { color: #27ae60; }
    .paper-card .card-image i { color: #9b59b6; }
    
    .card-content {
        padding: 1.5rem;
    }
    
    .card h3 {
        margin: 0 0 0.5rem;
        color: #2c3e50;
    }
    
    .card-meta {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    
    .card-meta i {
        margin-right: 5px;
    }
    
    .btn {
        display: inline-block;
        padding: 8px 16px;
        background: #3498db;
        color: white;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        transition: background 0.3s ease;
    }
    
    .btn:hover {
        background: #2980b9;
    }
    
    .view-all {
        display: block;
        text-align: center;
        margin: 2rem 0;
        font-size: 1.1rem;
    }
    
    /* Quick Figures Styles */
    .quick-figures {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin: 3rem 0;
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 8px;
    }
    
    .figure-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    
    .figure-card:hover {
        transform: translateY(-5px);
    }
    
    .figure-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .figure-count {
        font-size: 2rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .figure-label {
        color: #7f8c8d;
        font-size: 1rem;
    }
    
    .subjects-icon { color: #3498db; }
    .notes-icon { color: #e74c3c; }
    .jobs-icon { color: #e67e22; }
    .scholarships-icon { color: #27ae60; }
    .papers-icon { color: #9b59b6; }
    
    /* Why Choose Us Section */
    .why-choose-us {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        padding: 3rem 2rem;
        border-radius: 8px;
        margin: 3rem 0;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .feature-card {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        text-align: center;
        transition: transform 0.3s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
    }
    
    .feature-icon {
        font-size: 2.5rem;
        color: #3498db;
        margin-bottom: 1rem;
    }
    
    .feature-title {
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .feature-desc {
        color: #7f8c8d;
        font-size: 0.95rem;
    }
    
    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .hero {
            padding: 2rem 1rem;
        }
        
        .quick-figures {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 480px) {
        .quick-figures {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container">
    <div class="hero">
        <h1>Welcome to Digital Notes Platform</h1>
        <p>Access study materials, past papers, job opportunities, and scholarship programs all in one place.</p>
        
        <div class="search-form">
            <form method="GET" action="index.php">
                <input type="text" name="search" placeholder="Search subjects, past papers, jobs, scholarships..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>
    </div>

    <h2 class="section-title"><i class="fas fa-book"></i> Featured Subjects</h2>
    <div class="content-grid">
        <?php if (empty($featuredSubjects)): ?>
            <p>No subjects available yet.</p>
        <?php else: ?>
            <?php foreach ($featuredSubjects as $subject): ?>
                <a href="notes.php?subject_id=<?= $subject['id'] ?>" class="card subject-card">
                    <div class="card-image">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($subject['name']) ?></h3>
                        <p>Explore study materials for this subject</p>
                        <span class="btn">View Notes</span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <a href="notes.php" class="view-all">View all subjects →</a>

    <h2 class="section-title"><i class="fas fa-file-alt"></i> Recent Past Papers</h2>
    <div class="content-grid">
        <?php if (empty($recentPapers)): ?>
            <p>No past papers available at the moment.</p>
        <?php else: ?>
            <?php foreach ($recentPapers as $paper): ?>
                <a href="past_papers.php?paper_id=<?= $paper['id'] ?>" class="card paper-card">
                    <div class="card-image">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($paper['subject_name']) ?> - <?= htmlspecialchars($paper['title']) ?></h3>
                        <div class="card-meta">
                            <div><i class="fas fa-calendar-alt"></i> Year: <?= $paper['year'] ?></div>
                            <div><i class="fas fa-file-alt"></i> Type: <?= $paper['paper_type'] ?></div>
                            <div><i class="fas fa-clock"></i> Posted: <?= date('M d, Y', strtotime($paper['posted_at'])) ?></div>
                        </div>
                        <span class="btn">View Details</span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <a href="past_papers.php" class="view-all">View all past papers →</a>

    <h2 class="section-title"><i class="fas fa-briefcase"></i> Recent Job Opportunities</h2>
    <div class="content-grid">
        <?php if (empty($recentJobs)): ?>
            <p>No job opportunities available at the moment.</p>
        <?php else: ?>
            <?php foreach ($recentJobs as $job): ?>
                <a href="job.php?id=<?= $job['id'] ?>" class="card job-card">
                    <div class="card-image">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($job['title']) ?></h3>
                        <div class="card-meta">
                            <i class="fas fa-clock"></i> Posted: <?= date('M d, Y', strtotime($job['posted_at'])) ?>
                        </div>
                        <p><?= substr(htmlspecialchars($job['description']), 0, 100) ?>...</p>
                        <span class="btn">View Details</span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <a href="jobs.php" class="view-all">View all job opportunities →</a>

    <h2 class="section-title"><i class="fas fa-graduation-cap"></i> Recent Scholarships</h2>
    <div class="content-grid">
        <?php if (empty($recentScholarships)): ?>
            <p>No scholarship opportunities available at the moment.</p>
        <?php else: ?>
            <?php foreach ($recentScholarships as $scholarship): ?>
                <a href="scholarship.php?id=<?= $scholarship['id'] ?>" class="card scholarship-card">
                    <div class="card-image">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($scholarship['title']) ?></h3>
                        <div class="card-meta">
                            <i class="fas fa-clock"></i> Posted: <?= date('M d, Y', strtotime($scholarship['posted_at'])) ?>
                        </div>
                        <p><?= substr(htmlspecialchars($scholarship['description']), 0, 100) ?>...</p>
                        <span class="btn">View Details</span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <a href="scholarships.php" class="view-all">View all scholarship opportunities →</a>

    <!-- Quick Figures Section -->
    <h2 class="section-title"><i class="fas fa-chart-bar"></i> Platform Statistics</h2>
    <div class="quick-figures">
        <div class="figure-card">
            <div class="figure-icon subjects-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="figure-count"><?= $subjectCount ?></div>
            <div class="figure-label">Subjects</div>
        </div>
        
        <div class="figure-card">
            <div class="figure-icon notes-icon">
                <i class="fas fa-notes"></i>
            </div>
            <div class="figure-count"><?= $notesCount ?></div>
            <div class="figure-label">Study Notes</div>
        </div>
        
        <div class="figure-card">
            <div class="figure-icon papers-icon">
                <i class="fas fa-file-pdf"></i>
            </div>
            <div class="figure-count"><?= $papersCount ?></div>
            <div class="figure-label">Past Papers</div>
        </div>
        
        <div class="figure-card">
            <div class="figure-icon jobs-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="figure-count"><?= $jobsCount ?></div>
            <div class="figure-label">Job Opportunities</div>
        </div>
        
        <div class="figure-card">
            <div class="figure-icon scholarships-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="figure-count"><?= $scholarshipsCount ?></div>
            <div class="figure-label">Scholarships</div>
        </div>
    </div>

    <!-- Why Choose Our Platform Section -->
    <div class="why-choose-us">
        <h2 class="section-title"><i class="fas fa-star"></i> Why Choose Our Platform</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-book"></i>
                </div>
                <h3 class="feature-title">Comprehensive Resources</h3>
                <p class="feature-desc">Access a vast collection of study materials, past papers, and educational resources all in one place.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h3 class="feature-title">Career Opportunities</h3>
                <p class="feature-desc">Discover job openings and internship opportunities tailored for students and graduates.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="feature-title">Scholarship Programs</h3>
                <p class="feature-desc">Find and apply for scholarships to support your educational journey.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="feature-title">Community Support</h3>
                <p class="feature-desc">Connect with fellow students and educators in our growing learning community.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>